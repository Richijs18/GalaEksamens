<?php
//noformē pasūtījumu
if(isset($_POST['takeOrder'])){
	$cart=json_decode($_COOKIE['cart'],true);
	
	$to      = 'ricis1818@gmail.com';
	$subject = 'Jauns pasūtījums';
	$message = "Vārds, uzvārds: ".$_POST['name']."\r\nE-pasts: ".$_POST['email']."\r\nTālrunis: ".$_POST['phone']."\r\n\r\nPasūtītās preces:\r\n";
	
	$total_price=0;
	foreach($cart as $c){
		$product=db::query_row("SELECT * FROM products WHERE id=?",array(array('i',$c)));
		$message .=$product['title'].", €".$product['price']."\r\n";
		
	}
	$message .="Kopā €".$total_price."\r\n";
	
	$message = wordwrap($message, 70, "\r\n");
	$headers = array(
		'From' => 'ricis1818@gmail.com',
		'Reply-To' => 'ricis1818@gmail.com',
		'X-Mailer' => 'PHP/' . phpversion()
	);
	
	//Epasts adminam
	@mail($to, $subject, $message, $headers);
	//Epasts pasūtītājam
	@mail($_POST['email'], $subject, $message, $headers);
	//iztukšo grozu
	setcookie('cart','',time()-60*60*24*7,'/');
	
	header("Location: ".WEB_URL.'grozs/?done');
}

//iztukšo grozu
if(isset($_POST['clearCart'])){
	setcookie('cart','',time()-60*60*24*7,'/');
}


if(isset($_COOKIE['cart'])){
	$cart=json_decode($_COOKIE['cart'],true);		
}

if(isset($cart) && !empty($cart)){
	echo'
	<br />
	<h1 class="text-center">Preces Jūsu grozā</h1>
	<br />
	<table class="table products">
		<thead>
			<tr>
				<th>Prece</th>
				<th>Cena</th>
			</tr>
		</thead>
		<tbody>';
			$total_price=0;
			foreach($cart as $c){
				$product=db::query_row("SELECT * FROM products WHERE id=?",array(array('i',$c)));
				echo'
				<tr>
					<td><img src="'.WEB_URL.'img/products/'.$product['id'].'.jpg" /> '.$product['title'].'</td>
					<td>€'.$product['price'].'</td>
				</tr>
				';
				$total_price+=$product['price'];
			}
			echo'
			<tr>
				<td class="text-end"><b>Kopā:</b></td>
				<td class="text-center"><b>€'.$total_price.'</b></td>
			</tr>';
		
		echo'
		</tbody>
	</table>';
	
	if($total_price>0){
		echo'<h2 class="text-center">Pasūtījuma noformēšana</h2>
		<form method="post" action="">
			<div class="mb-3">
				<label for="InputName" class="form-label">Vārds, uzvārds</label>
				<input type="text" class="form-control" id="InputName" name="name">    
			</div>
			<div class="mb-3">
				<label for="InputEmail" class="form-label">E-pasts</label>
				<input type="text" class="form-control" id="InputEmail" name="email">    
			</div>
			<div class="mb-3">
				<label for="InputPhone" class="form-label">Tālrunis</label>
				<input type="text" class="form-control" id="InputPhone" name="phone">    
			</div>
			<div class="mb-3">
				<button name="takeOrder">Pasūtīt</button> 
				<button name="clearCart">Iztukšot grozu</button>
			</div>
		</form>
		';
	}
	
}
else{	
	if(isset($_GET['done']))
		echo'<br><p class="text-center">Pasūtījums veiksmīgi noformēts</p>';
	else
		echo'<br><p class="text-center">Grozs tukšs!</p>';
}


?>