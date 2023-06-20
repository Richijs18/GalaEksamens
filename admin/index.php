<?php
define('CHK',true);
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','kursadarbs');
define('DEBUG',true);
define('WEB_URL','http://127.0.0.1/kursadarbs/admin/');
define('WEB_URL_PUBLIC','http://127.0.0.1/kursadarbs/');
if ( !defined('ABS_PATH') )
    define('ABS_PATH', dirname(__FILE__) . '\\');

define('ABS_PATH_PUBLIC', str_replace('\admin','',ABS_PATH));

require '../class.db.php';

session_start();

$message='';

/* Parbaude vai megina autorizeties */
if(isset($_POST['username'],$_POST['password'],$_POST['login'])){
	$param=array(
		array('s',$_POST['username']),
		array('s',md5('f^89#hJ!'.md5($_POST['password'])))
	);
	$user=db::query_row("SELECT * FROM user WHERE username=? AND password=?",$param);
	
	if($user!=NULL){
		$_SESSION['username']=$user['username'];			
		$_SESSION['id']=$user['id'];			
		header("Location: ".WEB_URL);
	}
	else
		$message='Lietotājvārds un/vai parole nav pareiza.';
}

/* parbauda vai meģina iziet */
if(isset($_GET['menu']) && $_GET['menu']=='logout'){
	session_destroy();
	header("Location: ".WEB_URL);
}

//parbaude vai ir autorizejies
if(isset($_SESSION['id']) && $_SESSION['id']>0){
	define('AUTORIZEJIES',1);	
}
else{
	define('AUTORIZEJIES',0);	
}

echo'
<!DOCTYPE HTML>
<html>
	<head>
		<title>Admin</title>
			
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" integrity="sha512-ngQ4IGzHQ3s/Hh8kMyG4FC74wzitukRMIcTOoKT3EyzFZCILOPF0twiXOQn75eDINUfKBYmzYn2AA8DkAk8veQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
		
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha3/css/bootstrap.min.css" integrity="sha512-iGjGmwIm1UHNaSuwiNFfB3+HpzT/YLJMiYPKzlQEVpT6FWi5rfpbyrBuTPseScOCWBkRtsrRIbrTzJpQ02IaLA==" crossorigin="anonymous" referrerpolicy="no-referrer" />	
		
		<link rel="stylesheet" type="text/css" href="'.WEB_URL.'css/style.css" />
		
		
		
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha3/js/bootstrap.min.js" integrity="sha512-wOLiP6uL5tNrV1FiutKtAyQGGJ1CWAsqQ6Kp2XZ12/CvZxw8MvNJfdhh0yTwjPIir4SWag2/MHrseR7PRmNtvA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
				
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
						
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js" integrity="sha512-6F1RVfnxCprKJmfulcxxym1Dar5FsT/V2jiEUvABiaEiFWoQ8yHvqRM/Slf0qJKiwin6IDQucjXuolCfCKnaJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/lang/summernote-lt-LV.min.js" integrity="sha512-1PGKmSEGusrS5X3MlojPSZe34nkdrq1ASg/8eiQCZJ+93QFQXN6c+TsjuT1kc0NQxSIlV2DcTAeS+CvwAYGcrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		
		<script src="'.WEB_URL.'/lib/tinymce/tinymce.min.js"></script>
		<script>	
		$(document).ready(function() {
			const tooltipTriggerList = document.querySelectorAll(\'[data-bs-toggle="tooltip"]\')
			const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
		});
		</script>
	</head>
	<body>';


		//ja nav autorizejies, tad izvada autorizēšanās formu
		if(!AUTORIZEJIES){
			echo'
			<div class="container">
				<div class="row">
					<div class="col-md-6 offset-md-3">
						<h2 class="text-center text-dark mt-5">Autorizēties</h2>
						<div class="card my-5">
							<form method="post" action="" class="card-body p-lg-5 m-0">
								'.($message!='' ? '<div class="alert alert-danger" role="alert">'.$message.'</div>' : '').'
								<div class="mb-3">
									<input type="text" class="form-control" name="username"  placeholder="Lietotājvārds">
								</div>
								<div class="mb-3">
									<input type="password" class="form-control" name="password" placeholder="Parole">
								</div>
								<div class="text-center">
									<button name="login" type="submit" class="btn btn-primary px-5 w-100">Autorizēties</button>
								</div>								
							</form>
						</div>
					</div>
				</div>
			</div>';
		}
		else{
			//kāda sadaļa atvērta?
			$menu='menu';
			if(isset($_GET['menu']) && $_GET['menu']!='' && file_exists(ABS_PATH.'/lib/'.htmlspecialchars($_GET['menu']).'.php')){
				$menu=$_GET['menu'];
			}
			
			
			
			echo'
			<div class="container">
				<header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
					<a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">						
						
					</a>

					<ul class="nav nav-pills">
						<li class="nav-item"><a href="'.WEB_URL.'menu/" class="nav-link'.($menu=='menu' ? ' active' : '').'" aria-current="page">Sadaļas</a></li>
						<li class="nav-item"><a href="'.WEB_URL.'estore/" class="nav-link'.($menu=='estore' ? ' active' : '').'" aria-current="page">Preces</a></li>
						<li class="nav-item"><a href="'.WEB_URL.'users/" class="nav-link'.($menu=='users' ? ' active' : '').'">Lietotāji</a></li>
						<li class="nav-item"><a href="'.WEB_URL.'logout/" class="nav-link">Iziet</a></li>
					</ul>
				</header>
				
				<main>';
					include ABS_PATH.'lib/'.$menu.'.php';
				echo'
				</main>
			
				
			</div>
			<footer class="text-center">
				<div class="container">
					<hr />
					2023
				</div>
			</footer>';
		}
		
		echo'		
	</body>
</html>';

?>