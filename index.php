<?php

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','kursadarbs');
define('DEBUG',true);
define('WEB_URL','http://127.0.0.1/kursadarbs/');

require 'class.db.php';

session_start();

//kāda sadaļa atvērta?
$menu=null;
if(isset($_GET['menu']) && $_GET['menu']!=''){
	$menu=db::query_row("SELECT * FROM menu WHERE url=?",array(array('s',$_GET['menu'])));	
}
if($menu==NULL){
	$menu=db::query_row("SELECT * FROM menu ORDER BY id ASC LIMIT 1");		
}

echo'
<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="'.WEB_URL.'css/style.css" />
		<title>'.$menu['title'].'</title>
	</head>
	<body>
		<header>
		
			<nav>';
				$menus=db::query("SELECT * FROM menu ORDER BY weight ASC");
				if($menus!=NULL){
					echo'<ul>';
						foreach($menus as $m){
							echo'
							<li>
								<a'.($m['url']==$menu['url'] ? ' class="active"' : '').' href="'.WEB_URL.$m['url'].'/">'.$m['title'].'</a>
							</li>';
						}
					echo'</ul>';
				}
			echo'
			</nav>
		</header>
		
		<main>
			<div class="container">
				'.$menu['text'].'
			</div>
		</main>
		
		<footer>
		2023
		</footer>';



exit;

echo'<p>';
foreach($sadalas as $sadala){
	//divi nederīgi gadījumi, kad mēs sadaļu neizvadām.
	if(($sadala['redzams']=='autorizetiem' && AUTORIZEJIES==0) || ($sadala['redzams']=='neautorizetiem' && AUTORIZEJIES==1)){
		continue;
	}
	else{
		echo'<a href="?sadala='.$sadala['adrese'].'">'.$sadala['nosaukums'].''.$sadala['informacija'].'</a> ';
	}
}
echo'</p>';

if(isset($_GET['sadala'])){
	$atrasta_sadala=0;
	foreach($sadalas as $sadala){
		if($_GET['sadala']==$sadala['adrese']){
			if(($sadala['redzams']=='autorizetiem' && AUTORIZEJIES==0) || ($sadala['redzams']=='neautorizetiem' && AUTORIZEJIES==1)){
				header("Location: test5.php");
			}
			else{
				if($sadala['tips']=='fails'){
					include 'lib/'.$_GET['sadala'].'.php';
				}
				else{
					echo nl2br($sadala['saturs']);
				}
				$atrasta_sadala=1;
				break;
			}
		}
	}
	if($atrasta_sadala==0){
		include 'lib/sakums.php';
	}
}
else{
	include 'lib/sakums.php';
}

	echo'
	</body>
</html>
';



/* paziņojuma funkcija */
function pazinojums($pazinojums){
	if($pazinojums!=''){
		echo'<p>'.$pazinojums.'</p>';
	}
}



?>