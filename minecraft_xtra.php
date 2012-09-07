<?php
/*
Plugin Name: Minecraft-Xtra
Plugin URI: http://bukkit.fr/index.php?threads/plugin-wordpress-minecraft-xtra.3780/
Description: Le plugin wordpress iConomique pour Bukkit
Version: 0.1
Author: johnrazeur
Author URI: https://twitter.com/johnrazeur
*/

class minecraft_xtra {
    
protected $tb_config,$tb_prachat,$tb_blocks,$tb_users,$tb_allopass;
protected $dir,$url;
protected $dons,$code,$iddcode,$idpcode,$monnaie,$portjson,$mdpjson,$saltjson,$userjson,$pseudo,$token;

    function __construct() { 
    	global $wpdb;
    	$this->dir = plugin_dir_path( __FILE__ );
    	$this->url = plugins_url('',__FILE__);
	$this->tb_config = $wpdb->prefix . 'xtra_config';
	$this->tb_prachat = $wpdb->prefix . 'xtra_prachat';
	$this->tb_blocks = $wpdb->prefix . 'xtra_blocks';
	$this->tb_users = $wpdb->prefix . 'users';
        $this->tb_allopass = $wpdb->prefix . 'xtra_allopass';
	add_action('init', array(&$this,'hydrate')); //empèche affichage innatendu
	add_action('admin_menu', array(&$this, 'xtra_menu') );
	add_action('admin_bar_menu',array(&$this,'admin_bar'));
  
        }

    public function hydrate() {
	global $wpdb;
	global $current_user;
        $this->pseudo = $current_user->user_login;
	$sql = $wpdb->get_results ('SELECT * FROM '.$this->tb_config.'');
	foreach ($sql as $value) {
            $nom = $value->nom;
            $this->$nom  = $value->valeur;
		}
	$token = $wpdb->get_row('SELECT * FROM '.$this->tb_users.' WHERE user_login = "'.$this->pseudo.'"');
        $this->token = $token->tokens;

	}


   public function installDB() {  //Installation de la bdd
   global $wpdb;
   $config= $wpdb->prefix . 'xtra_config';
   $prachat= $wpdb->prefix . 'xtra_prachat';
 
	$crea_config = 'CREATE TABLE IF NOT EXISTS `'. $this->tb_config .'` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `valeur` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;';

	$ins_config = 'INSERT INTO `'.$this->tb_config.'` (`id`, `nom`, `valeur`) VALUES
(1, \'dons\', \'75\'),
(2, \'monnaie\', \'token\'),
(3, \'code\', \'\'),
(4, \'iddcode\', \'\'),
(5, \'idpcode\', \'\'),
(6, \'userjson\', \'\'),
(7, \'mdpjson\', \'\'),
(8, \'portjson\', \'\'),
(9, \'saltjson\', \'\');';


	$crea_prachat = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_prachat.'` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permissions` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;';

   $ins_prachat = 'INSERT INTO `'.$this->tb_prachat.'` (`id`, `permissions`, `prix`) VALUES
(1, \'falsebook.blocks.bridge\', 35),
(2, \'falsebook.ic.mc1110\', 30),
(3, \'falsebook.ic.mc0111\', 30),
(4, \'falsebook.blocks.hiddenswitch.create\', 25),
(5, \'falsebook.blocks.gate\', 40);';

	$crea_blocks = 'CREATE TABLE IF NOT EXISTS `'.$this->tb_blocks.'` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8 NOT NULL,
  `prix` decimal(11,2) NOT NULL,
  `vente` int(11) NOT NULL DEFAULT \'0\',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

	$ins_blocks = 'INSERT INTO `'.$this->tb_blocks.'` (`id`, `nom`, `prix`, `vente`, `actif`) VALUES
(1, \'stone\', \'1.00\', 1),
(2, \'Grass\', \'3.00\', 1),
(3, \'dirt\', \'4.00\', 0),
(4, \'cobble\', \'5.00\', 0),
(5, \'wooden_plank\', \'10.00\', 0),
(6, \'sapling\',\'2.00\', 0),
(7, \'bedrock\', \'30.00\', 0),
(12,\'sand\', \'0.00\', 0),
(13, \'gravel\', \'0.00\', 0),
(17, \'wood\', \'0.00\', 0);';
        

	$modif_wp_users = 'ALTER TABLE `'.$this->tb_users.'` ADD `tokens` INT NOT NULL DEFAULT 0';

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   global $wpdb;
  dbDelta($crea_config);
  dbDelta($ins_config);
  dbDelta($crea_prachat);
  dbDelta($ins_prachat);
  dbDelta($crea_blocks);
  dbDelta($ins_blocks);
  $wpdb->query($modif_wp_users);
   add_option('minecraft_xtra_db_version', '0.1') ;

	} 

   public function uninstallDB() { //Désinstallation de la bdd
   global $wpdb;
   $wpdb->query('DROP TABLE`' .$this->tb_config. '`');
   $wpdb->query('DROP TABLE`' .$this->tb_prachat. '`');
   $wpdb->query('DROP TABLE`' .$this->tb_blocks. '`');
   $wpdb->query('ALTER TABLE `'.$this->tb_users.'` DROP `tokens`');

   delete_option('minecraft_xtra_db_version'); 
	} 

   public function xtra_menu() {
     add_menu_page('Boutique', 'Boutique', 'read',''.$this->dir.'/boutique', '');
     add_submenu_page(''.$this->dir.'/boutique', 'Achat Block', 'Achat block', 'read',''.$this->dir.'/boutique', array($this, 'block'));
     add_submenu_page(''.$this->dir.'/boutique', 'Achat '.$this->monnaie.'s', 'Achat '.$this->monnaie.'s', 'read',''.$this->dir.'/achattoken', array($this, 'achattoken'));
     //add_submenu_page($this->dir.'/boutique.php', 'Achat Permissions', 'Achat Permissions', 'read','achatpermissions', array(&$boutique, 'permissions'));
     add_menu_page('Minecraft-Xtra', 'Minecraft-Xtra', 'edit_users',''.$this->dir.'/minecraft-xtra', '');
     add_submenu_page(''.$this->dir.'/minecraft-xtra', 'Paramètres', 'Paramètres', 'edit_users',''.$this->dir.'/minecraft-xtra', array(&$this, 'parametres'));
     add_submenu_page(''.$this->dir.'/minecraft-xtra', 'Gestion boutique', 'Gestion boutique', 'edit_users',''.$this->dir.'/gestionboutique', array(&$this, 'boutique'));

    }

    function admin_bar () {
    	global $wp_admin_bar;
	$url = site_url('/wp-admin/', 'http');
	$wp_admin_bar->add_menu( array(
		'id'        => 'Minecraft-Xtra',
		'parent'    => 'top-secondary',
		'title'     => 'Minecraft-Xtra'
	) );

	$wp_admin_bar->add_menu( array(
	'id'        => 'Token',
	'parent'    => 'Minecraft-Xtra',
	'title'     => ''.$this->monnaie.' : '.$this->token.''
	) );

	$wp_admin_bar->add_menu( array(
	'id'        => 'Achat',
	'parent'    => 'Minecraft-Xtra',
	'title'     => 'Acheter des '.$this->monnaie.'s !',
        'href'      => ''.$url.'admin.php?page=minecraft_xtra/achattoken'
	) );
    } 

	public function parametres() { //Contenu paramÃ¨tres
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div><h2>Paramètres</h2>

		<?php
		if (isset($_POST['monnaie'])) //Traitement du formulaire
		{
			global $wpdb;
			foreach ($_POST as $key => $value) {
                            $value = stripslashes($value);//Enlève les magic quotes
				$wpdb->query($wpdb->prepare('UPDATE '.$this->tb_config.' SET valeur = %s WHERE nom = %s',array($value,$key))); 
			}
			echo 'Les modifications ont été changées avec succès';
		} else { 
                    $this->code =htmlspecialchars($this->code)
//Affichage du formulaire?>
		<h3>Général</h3>
		<form method="post" action="" > 

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="monnaie">Nom de la monnaie : </label></th>
					<td><input type="text" value="<?php echo $this->monnaie ?>" name="monnaie"></td>
				</tr>
				<tr>
					<th><label for="dons">Token donné pour un don :</label></th>
					<td><input type="text" value="<?php echo $this->dons ?>" name="dons"></td>
				</tr>
				<tr>    
					<th><label for="allopass">Code paiement :</label></th>
					<td><input type="text" value="<?php echo $this->code ?>" name="code"></td>
				</tr>
				<tr>
					<th><label for="iddcode">Idd code :</label></th>
					<td><input type="text" value="<?php echo $this->iddcode ?>" name="iddcode"></td>
				</tr>
				<tr>
					<th><label for="iddcode">Idp code :</label></th>
					<td><input type="text" value="<?php echo $this->idpcode ?>" name="idpcode"></td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<h3>Jsonapi</h3>
				<tr>
					<th><label for="userjson">Userjson :</label></th>
					<td><input type="text" value="<?php echo $this->userjson ?>" name="userjson"></td>
				</tr>
				<tr>
					<th><label for="userjson">Mot de passe Json :</label></th>
					<td><input type="password" value="<?php echo $this->mdpjson ?>" name="mdpjson"></td>
				</tr>
				<tr>
					<th><label for="portjson">Port Json :</label></th>
					<td><input type="text" value="<?php echo $this->portjson ?>" name="portjson"></td>
				</tr>
				<tr>
					<th><label for="userjson">Salt Json :</label></th>
					<td><input type="text" value="<?php echo $this->saltjson ?>" name="saltjson"></td>
				</tr>

				<tr><td><input type="submit" class="button-primary" value="Changer"></td></tr>
			</tbody>
		</table>

		</form>

	</div>

		<?php
	 } 

	 } 


	 public function boutique() { 
         global $wpdb;      
             ?>
<div class="wrap">
  <div id="icon-themes" class="icon32"></div><h2>Gestion boutique</h2>
  <?php
     if (isset($_GET['action']) && $_GET['action'] == 'modify') {
         foreach ($_POST as $key => $value) {
             
         $wpdb->show_errors(); 
         $clé = explode('-', $key);
         $name = $clé[0];
         $id = $clé[1];
      if($name != 'supprimer') {
        if ($name != 'id') {
              if ($name == 'vente') {
                $vente = ($value == 'stack') ? 1:0;                  
                $wpdb->query($wpdb->prepare('UPDATE '.$this->tb_blocks.' SET '.$name.' = %s WHERE id = %s', array($vente,$id)));
              }
              else {
                $wpdb->query($wpdb->prepare('UPDATE '.$this->tb_blocks.' SET '.$name.' = %s WHERE id = %s', array($value,$id)));
               } 
        
       }     
      } else {
          if ($value == 'on') {
          $wpdb->query($wpdb->prepare('DELETE FROM '.$this->tb_blocks.' WHERE id = %s',$id));
          }
      }
         }   echo 'Changements effectués !';
} //FIN ACTION MODIFY

    if (isset($_GET['action']) && $_GET['action'] == 'ajouter') {
            $vente = ($_POST['vente'] == 'stack') ? 1:0;
            $wpdb->query($wpdb->prepare('INSERT INTO '.$this->tb_blocks.' VALUES(%s,%s,%s,%s)', array($_POST['id'],$_POST['nom'],$_POST['prix'],$vente)));
            echo 'Block ajouté.';
    }
  
  
           $req = $wpdb->get_results('SELECT * FROM '.$this->tb_blocks.'');
  ?>
  <form action="?page=minecraft_xtra/gestionboutique&action=modify" method="POST">
<table class="widefat" style="width: 80%;margin-bottom:20px;margin-top:20px;">
<thead>
    <tr>
        <th>Id</th>
        <th>Nom</th>       
        <th>Vente par</th>
        <th>Prix</th>
        <th>Supprimer</th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th>Id</th>
        <th>Nom</th>       
        <th>Vente par</th>
        <th>Prix</th>
        <th>Supprimer</th>
    </tr>
</tfoot>
<tbody>
    <?php  foreach ($req as $value) {
    echo '<tr>';
    $id = $value->id;
        echo '<th><input type="hidden" name="id-'.$id.'" value="'.$id.'" />'.$id.'</th>';
        echo '<th>'.$value->nom.'</th>';
        $vente = ($value->vente == 1) ? 'selected':'null'; //si 1 alors vente par stack
        echo '<th><select name="vente-'.$id.'"><option value="unité">Unité</option><option value="stack" '.$vente.'>Stack</option></select></th>';
        echo '<th><input type="number" value="'.$value->prix.'" name="prix-'.$id.'"/></th>';
        $actif = ($value->actif == 1) ? 'checked' : null; //Si 1, alors on coche la checkbox
        echo '<th><input type="checkbox" name="supprimer-'.$id.'"/></th>';
    echo '</tr>';
    } ?>
</tbody>
</table>
    <input type='submit' value='Modifié' class='button-secondary' />
  </form>
  <form action ="?page=minecraft_xtra/gestionboutique&action=ajouter"  method="POST">
<table class="widefat" style="width: 50%;margin-top: 50px;margin-bottom:20px;">
<thead>
    <tr>
        <th>Id</th>
        <th>Nom</th>       
        <th>Vente par</th>
        <th>Prix</th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th>Id</th>
        <th>Nom</th>       
        <th>Vente par</th>
        <th>Prix</th>
    </tr>
</tfoot>
<tbody> 
    <tr>
        <th><input name="id" type="number"/></th>
        <th><input name="nom" type="text" /></th>
        <th><select name="vente"> <option value="unite">Unité</option><option value="stack">Stack</option></select></th>
        <th><input name="prix" type="number"/></th>
   </tr>
</tbody>
</table>
      <input type='submit' value='Ajouter' class='button-primary' />
  </form>
</div>
    <?php
	} 
        
         public function block () {       
        
global $wpdb;      
include_once('JSONAPI.php');
$api = new JSONAPI('localhost', ''.$this->portjson.'', ''.$this->userjson.'', ''.$this->mdpjson.'', ''.$this->saltjson.'');
wp_register_style('style.css',''.$this->url.'/style.css','','1.1');
wp_enqueue_style('style.css');

$total = 0;
$nombrestack = 0;

if (isset($_POST) && !empty($_POST))
{

foreach ($_POST as $value) {  //Calcul du prix
	$value = htmlspecialchars($value);
	$blockachat = explode('-', $value);
	$stack = $blockachat[0];
	$nombre = $blockachat[2];
	$block = $blockachat[1];
	$req = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$this->tb_blocks.' WHERE nom = %s',$block));

		if($stack <2) { //si qu'un stack
			if($req[0]->vente == 0) { //si l'achat a l'unitÃ© est activÃ©
			 $prixtotal = $nombre * $req[0]->prix;
			 $total = $total + $prixtotal;
			}
			else {
			 $prixtotal = $stack * $req[0]->prix;
			 $total = $total + $prixtotal;
			}
		$nombrestack = $nombrestack+$stack;
		}
		else { //si > 2 stacks
		$nombrestack = $nombrestack + $stack;
		$stack--;
		$prixstack = 64 * $stack;
		$nombretotal = $prixstack + $nombre;
		$prixtotal = $nombretotal*$req[0]->prix;
		$total = $total + $prixtotal;
		}
}
echo '<p>Le prix total est '.$total.'<br></p></br>';
  

$nombrevide = 0;
$player=$api->call('getPlayerNames');
if (!empty($player['success'])) {
if (in_array($this->pseudo,$player['success'])) //Si le membre est connecté
{
$player = $api->call('getPlayer',array($this->pseudo));
//var_dump($player['success']['inventory']['inventory']);
foreach ($player['success']['inventory']['inventory'] as $key =>$value) { 
    
    if (is_null($value)) { //Si une place est vide, on incrÃ©mente
       $nombrevide ++;
    }
} 
$quantite = 0;
if($nombrevide >= $nombrestack){//Si il y a assez de place
        $argent = intval($this->token);
		if ($total <= $argent) {

	
	foreach ($_POST as $value) {
	$blockachat = explode('-', $value);
	$stack = $blockachat[0];
	$nombre = $blockachat[2];
	$block = $blockachat[1];
			$req = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$this->tb_blocks.' WHERE nom = %s',$block));
		if($stack <2) { //si qu'un stack
			$api->call('runConsoleCommand',array('give '.$this->pseudo.' '.$req[0]->id.' '.$nombre.''));
		}
		else { //si > 2 stacks
		$stack--;
		$nombrestack = 64 * $stack;
		$total = $nombrestack + $nombre;
		while($total >64){
			$total = $total-64;
			$api->call('runConsoleCommand',array('give '.$this->pseudo.' '.$req[0]->id.' 64'));
		}
			$api->call('runConsoleCommand',array('give '.$this->pseudo.' '.$req[0]->id.' '.$total.''));
		}
}
		//Achat
		$total = htmlspecialchars($total);
			$argentfinale = $argent - $total;
			$req = $wpdb->query($wpdb->prepare('UPDATE '.$this->tb_users.' SET tokens = %s WHERE user_login = %s',array($argentfinale,$this->pseudo)));


}
	else { echo 'Vous n\'avez pas assez de '.$this->monnaie.'s.';}
	}

else {
	$besoin = $nombrestack - $nombrevide;
	echo 'Vous avez encore besoin de '.$besoin.' places en plus dans votre inventaire';
}

}

}
else
{
echo 'Vous devez être connecté au serveur';
}
}
?> 
<div class="wrap">
<div id="icon-themes" class="icon32"></div><h2 style="margin-bottom:20px">Achats de blocks</h2>
<div id="list">
<?php

$q = $wpdb->get_results('SELECT * FROM '.$this->tb_blocks.'');

foreach ($q as $value) {
	if($value->vente == 0)
	 {
	 echo '<div class="blockvente"><span class="info"><img id="'.$value->nom.'" src="'.$this->url.'/images/blocks/'.$value->nom.'.png" alt="'.$value->nom.'"><span>Nom : '.$value->nom.'<br>Prix : '.$value->prix.' '.$this->monnaie.'<br>Vente : unité</span></span><a href="#" style="position:relative;left:-45px;text-decoration:none;color:black;">[Stack]</a></div>'; //Si stack
	 }
	else {
	 echo '<div class="blockvente"><span class="info"><img src="'.$this->url.'/images/blocks/'.$value->nom.'.png" alt="'.$value->nom.'"><span>Nom : '.$value->nom.'<br>Prix : '.$value->prix.' '.$this->monnaie.'<br>Vente : stack</span></span><a href="#" style="position:relative;left:-45px;text-decoration:none;color:black;">[Stack]</a></div>';
	}

}

?>

</div>

<form method="post">
<div id="achat">
<!--
####################################
Les nouveaux blocks ce placeront ici
####################################
-->

</div>
<br><input type="submit" value="Acheter" style="margin-top:20px;">
</form>
</div>
<?php include($this->dir.'/js.php');

} 

    public function achattoken() {
        


if ($_GET['type'] == 'starpass') {
/* 
###################################
DEBUT CODE STARPASS
###################################
*/

// Déclaration des variables
$ident=$idp=$ids=$idd=$codes=$code1=$code2=$code3=$code4=$code5=$datas='';
$idp = $this->idpcode;
// $ids n'est plus utilisé, mais il faut conserver la variable pour une question de compatibilité
$idd = $this->iddcode;
$ident=$idp.";".$ids.";".$idd;
// On récupère le(s) code(s) sous la forme 'xxxxxxxx;xxxxxxxx'
if(isset($_POST['code1'])) $code1 = $_POST['code1'];
if(isset($_POST['code2'])) $code2 = ";".$_POST['code2'];
if(isset($_POST['code3'])) $code3 = ";".$_POST['code3'];
if(isset($_POST['code4'])) $code4 = ";".$_POST['code4'];
if(isset($_POST['code5'])) $code5 = ";".$_POST['code5'];
$codes=$code1.$code2.$code3.$code4.$code5;
// On récupère le champ DATAS
if(isset($_POST['DATAS'])) $datas = $_POST['DATAS'];
// On encode les trois chaines en URL
$ident=urlencode($ident);
$codes=urlencode($codes);
$datas=urlencode($datas);

/* Envoi de la requête vers le serveur StarPass
Dans la variable tab[0] on récupère la réponse du serveur
Dans la variable tab[1] on récupère l'URL d'accès ou d'erreur suivant la réponse du serveur */
$get_f=@file("http://script.starpass.fr/check_php.php?ident=$ident&codes=$codes&DATAS=$datas");
if(!$get_f)
{
exit("Votre serveur n'a pas accès au serveur de Starpass, merci de contacter votre hébergeur.");
}
$tab = explode("|",$get_f[0]);

if(!$tab[1]) $url = "http://script.starpass.fr/erreur.php";
else $url = $tab[1];

// dans $pays on a le pays de l'offre. exemple "fr"
$pays = $tab[2];
// dans $palier on a le palier de l'offre. exemple "Plus A"
$palier = urldecode($tab[3]);
// dans $id_palier on a l'identifiant de l'offre
$id_palier = urldecode($tab[4]);
// dans $type on a le type de l'offre. exemple "sms", "audiotel, "cb", etc.
$type = urldecode($tab[5]);
// vous pouvez à tout moment consulter la liste des paliers à l'adresse : http://script.starpass.fr/palier.php

// Si $tab[0] ne répond pas "OUI" l'accès est refusé
// On redirige sur l'URL d'erreur
if(substr($tab[0],0,3) != "OUI")
{
      header("Location: $url");
      exit;
}
else
{
      /* Le serveur a répondu "OUI"
      Code a executé car tout est bon */
        global $wpdb;
	$reqa = $wpdb->query($wpdb->prepare('UPDATE '.$this->tb_users.' SET tokens=tokens+%s WHERE user_login = %s',$this->dons,$this->pseudo));
        echo '<div class="wrap">';
	echo 'Vous avez bien effectué le don';

	
}
/*
###################################
FIN CODE STARPASS
###################################
*/
}
echo '<div id="icon-users" class="icon32"></div><h2>Achat '.$this->monnaie.'s </h2>';
    
echo $this->code ;

echo '<p> Recevez '.$this->dons.' '.$this->monnaie.' pour tous achat !!! </p>';



echo '</div>';
    }
}
if (class_exists('minecraft_xtra')) {
$minecraft_xtra = new minecraft_xtra();
}

register_activation_hook(__FILE__, array($minecraft_xtra, 'installDB')); //Installation bdd
register_deactivation_hook(__FILE__, array($minecraft_xtra, 'uninstallDB')); // Désinstallation bdd