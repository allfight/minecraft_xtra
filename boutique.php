<?php 

//if( !defined( 'minecraft_xtra' ) )
//{
 // define( 'minecraft_xtra', 1 );
class boutique extends minecraft_xtra {
    
    protected $pseudo;
     
    public function block () {
        
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
        
        
        
$minecraft_xtra = new minecraft_xtra();
include_once('JSONAPI.php');
$api = new JSONAPI('localhost', ''.$minecraft_xtra->portjson.'', ''.$minecraft_xtra->userjson.'', ''.$minecraft_xtra->mdpjson.'', ''.$minecraft_xtra->saltjson.'');
global $current_user;
global $wpdb;
wp_register_style('style.css',''.$minecraft_xtra->url.'/style.css','','1.1');
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
	$req = $wpdb->query($wpdb->prepare('SELECT * FROM '.$minecraft_xtra->tb_blocks.' WHERE nom = %s AND actif = 1',array($block)));

		if($stack <2) { //si qu'un stack
			if($req->vente == 0) { //si l'achat a l'unité est activé
			 $prixtotal = $nombre * $req->prix;
			 $total = $total + $prixtotal;
			}
			else {
			 $prixtotal = $stack * $req->prix;
			 $total = $total + $prixtotal;
			}
		$nombrestack = $nombrestack+$stack;
		}
		else { //si > 2 stacks
		$nombrestack = $nombrestack + $stack;
		$stack--;
		$prixstack = 64 * $stack;
		$nombretotal = $prixstack + $nombre;
		$prixtotal = $nombretotal*$req->prix;
		$total = $total + $prixtotal;
		}
}
echo '<p>Le prix total est '.$total.'<br></p></br>'.$req->vente.'';

$nombrevide = 0;
$player=$api->call('getPlayerNames');
if (in_array($minecraft_xtra->pseudo,$player['success'])) //Si le membre est connecté
{
$player = $api->call('getPlayer',array($minecraft_xtra->pseudo));
//print_r($johnrazeur['success']['inventory']['inventory']);
foreach ($player['success']['inventory']['inventory'] as $key => $value) { 
	if (!array_key_exists('amount',$value)){ //si amount existe alors case non prise
		$nombrevide ++;
	}
}
$quantite = 0;
if($nombrevide >= $nombrestack){//Si il y a assez de place
		 $argent = $membre->getTokens();
		if ($total < $argent) {

	
	foreach ($_POST as $value) {
	$blockachat = explode('-', $value);
	$stack = $blockachat[0];
	$nombre = $blockachat[2];
	$block = $blockachat[1];
			$req = $bdd ->prepare('SELECT * FROM blocks WHERE nom = :nom AND actif = 1');
			$req->execute(array(
			'nom' => $block
			));
			$donnees=$req->fetch();

		if($stack <2) { //si qu'un stack
			$api->call('runConsoleCommand',array('give '.$current_user->user_login.' '.$donnees['id'].' '.$nombre.''));
		}
		else { //si > 2 stacks
		$stack--;
		$nombrestack = 64 * $stack;
		$total = $nombrestack + $nombre;
		while($total >64){
			$total = $total-64;
			$api->call('runConsoleCommand',array('give '.$current_user->user_login.' '.$donnees['id'].' 64'));
		}
			$api->call('runConsoleCommand',array('give '.$current_user->user_login.' '.$donnees['id'].' '.$total.''));
		}
}
		//Achat
		$total = htmlspecialchars($total);
			$argentfinale = $this->tokens - $somme;
			$req = $bdd->prepare('UPDATE users SET tokens = :tokens WHERE pseudo = :pseudo');
			$req->execute(array(
				'tokens' => $argentfinale,
				'pseudo' => $this->pseudo
				));


}
	else { echo 'Vous n\'avez pas assez de '.$typemonnaie.'';}
	}

else {
	$besoin = $nombrestack - $nombrevide;
	echo 'Vous avez encore besoin de '.$besoin.' places en plus dans votre inventaire';
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
global $wpdb;

$q = $wpdb->get_results('SELECT * FROM '.$minecraft_xtra->tb_blocks.' WHERE actif = 1');

foreach ($q as $value) {
	if($value->vente == 0)
	 {
	 echo '<div class="blockvente"><span class="info"><img id="'.$value->nom.'" src="'.$minecraft_xtra->url.'/images/blocks/'.$value->nom.'.png" alt="'.$value->nom.'"><span>Nom : '.$value->nom.'<br>Prix : '.$value->prix.' '.$minecraft_xtra->monnaie.'<br>Vente : unité</span></span><a href="#" style="position:relative;left:-45px;text-decoration:none;color:black;">[Stack]</a></div>'; //Si stack
	 }
	else {
	 echo '<div class="blockvente"><span class="info"><img src="'.$minecraft_xtra->url.'/images/blocks/'.$value->nom.'.png" alt="'.$value->nom.'"><span>Nom : '.$value->nom.'<br>Prix : '.$value->prix.' '.$minecraft_xtra->monnaie.'<br>Vente : stack</span></span><a href="#" style="position:relative;left:-45px;text-decoration:none;color:black;">[Stack]</a></div>';
	}

}
echo $minecraft_xtra->pseudo;
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
<?php include($minecraft_xtra->dir.'/js.php');

} 


	public function permissions () {
            $minecraft_xtra = new minecraft_xtra();
            global $wpdb;
            $mylink = $wpdb->get_row('SELECT * FROM '.$minecraft_xtra->tb_blocks.' WHERE id = 3');
            echo $mylink->prix;
            include_once('JSONAPI.php');
            $api = new JSONAPI('localhost', ''.$minecraft_xtra->portjson.'', ''.$minecraft_xtra->userjson.'', ''.$minecraft_xtra->mdpjson.'', ''.$minecraft_xtra->saltjson.'');
            $player=$api->call('getPlayerNames');
            echo $minecraft_xtra->pseudo;
	}

} 

//add_action('wp_enqueue_scripts', array('boutique','loaddesign')); 
//}
?>