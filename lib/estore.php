<?php
$alert='';

//ieliek grozā
if(isset($_POST['addToCart']) && (int)$_POST['addToCart']>0){
	$cart=array();
	if(isset($_COOKIE['cart'])){
		$cart=json_decode($_COOKIE['cart'],true);		
	}
	$cart[]=$_POST['addToCart'];
	setcookie('cart',json_encode($cart),time()+60*60*24*7,'/');
	$alert='<p class="text-center"><br /><b>Prece pievienota grozam</b></p>';
}

//izvada viena produkta skatu
if(isset($_GET['product'])){
	$product=db::query_row("SELECT * FROM products WHERE url=?",array(array('s',$_GET['product'])));
	if($product!=NULL){
		echo'<div class="row">
		<div class="col-lg-12 prod">'.($alert=='' ? '' : $alert).'
			<form method="post" action="">
				<h1>'.$product['title'].'</h1>
				<p>
					<img src="'.WEB_URL.'img/products/'.$product['id'].'.jpg" />								
				</p>
				'.$product['text'].'
				<p><b>Cena: €'.$product['price'].'</b><br><br>
				<button name="addToCart"  value="'.$product['id'].'">Pievienot grozam</button>
				</p>
			</form>
		</div>
		</div>';
	}
	
	
}
//izvada saraksta skatu
else{
	$products=db::query("SELECT * FROM products WHERE menu_id=?",array(array('i',$menu['id'])));
	if($products!=NULL){
		echo'<div class="row">';
		foreach($products as $p){
			echo'
			<div class="product col-lg-3 col-md-4 col-sm-6">
				<h3><a href="'.WEB_URL.$menu['url'].'/'.$p['url'].'/">'.$p['title'].'</a></h3>
				<a href="'.WEB_URL.$menu['url'].'/'.$p['url'].'/">
					<img src="'.WEB_URL.'img/products/'.$p['id'].'.jpg" />
				</a>
			</div>
			';
		}
		echo'</div>';
	}
}
?>