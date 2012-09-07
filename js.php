<script>
var list = document.getElementById('list');
var achat = document.getElementById('achat');
var test = document.getElementById('shopdirt');
list.addEventListener('click',function(e) {

var blocks = document.getElementById('shop'+e.target.id);

if (e.target.href) //Si on clique sur stack
{
	var idblock = e.target.previousSibling.firstChild.alt; //Permet de blocker les unité si besoin
	var existblock = document.getElementById('shop'+idblock);

	if (!existblock)
	{
    var newblock = document.createElement('newblock');

	newblock.setAttribute('id','shop'+idblock);
	newblock.setAttribute('nombre',64);
	newblock.setAttribute('stack',1);
	newblock.setAttribute('onclick','this.parentNode.removeChild(this);')
	newblock.innerHTML ='<div class="blockachat"><img src="<?php echo $this->url ?>/images/blocks/'+idblock+'.png"><span id="stack-'+idblock+'" style="position: relative;left:-40px;top:-35px;color:yellow;">1</span><span id="quantite-'+idblock+'" style="position: relative;left:-20px;bottom:5px;color:blue;">64</span><input type="hidden" name="'+idblock+'" value="1-'+idblock+'-64"></div>'; //par defaut 1
	achat.appendChild(newblock);
	}
	else {
	var blockpanier = document.getElementsByName(idblock)[0];
	var nombrestack = document.getElementById('stack-'+idblock);
	var quantite = existblock.getAttribute('nombre');
	var stack = existblock.getAttribute('stack');
	stack = parseInt(stack);
	stack++;
	existblock.setAttribute('stack',stack)
	nombrestack.innerHTML = stack;
	blockpanier.value = ''+stack+'-'+idblock+'-'+quantite;

	}
}

if (e.target.id == 'list' || e.target.href ||!e.target.id) { //Si on clique sur le block on fait rien
} else {
	if (!blocks) { //Si le block n'existe pas
	var newblock = document.createElement('newblock');

	newblock.setAttribute('id','shop'+e.target.id);
	newblock.setAttribute('nombre',1);
	newblock.setAttribute('stack',1);
	newblock.setAttribute('onclick','this.parentNode.removeChild(this);')
	newblock.innerHTML ='<div class="blockachat"><img src="<?php echo $this->url ?>/images/blocks/'+e.target.id+'.png"><span id="stack-'+e.target.id+'" style="position: relative;left:-40px;top:-35px;color:yellow;">1</span><span id="quantite-'+e.target.id+'" style="position: relative;left:-20px;bottom:5px;color:blue;">1</span><input type="hidden" name="'+e.target.id+'" value="1-'+e.target.id+'-1"></div>'; //par defaut 1

achat.appendChild(newblock);
}
	else { //si le block existe déjà
	var blockpanier = document.getElementsByName(e.target.id)[0];
	var nombreblock = document.getElementById('quantite-'+e.target.id);
	var nombrestack = document.getElementById('stack-'+e.target.id);
	var quantite = blocks.getAttribute('nombre'); //On récupère le nombre actuelle
	var stack = blocks.getAttribute('stack');
	quantite = parseInt(quantite); // Conversion en nombre
	stack = parseInt(stack);
	quantite++;
	if (quantite > 64) { //création d'un nouveau block
	stack++;
	blocks.setAttribute('nombre',1);
	blocks.setAttribute('stack',stack)
	nombreblock.innerHTML = 1;
	nombrestack.innerHTML = stack;
	blockpanier.value = ''+stack+'-'+e.target.id+'-1';
	}
	else {
	blocks.setAttribute('nombre',quantite);
	nombreblock.innerHTML = quantite;
	blockpanier.value = ''+stack+'-'+e.target.id+'-'+quantite;
	}
	}
	}
//var achat = document.getElementById('achat');
//achat.appendChild(newblock);

},false);
</script>
