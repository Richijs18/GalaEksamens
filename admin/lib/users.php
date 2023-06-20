<?php
if (!defined('CHK')) exit;

//pārbaude vai dzēš Lietotāju
if(isset($_GET['delete']) && (int)$_GET['delete']>0 && $_GET['delete']!=$_SESSION['id']){
	$param=array(
		array('i',$_GET['delete'])	
	);	
	db::query("DELETE FROM user WHERE id=? LIMIT 1",$param);
}

//pārbaude vai pievieno Lietotāju
if(isset($_POST['username'],$_POST['password'],$_POST['add']) && !empty($_POST['username']) && !empty($_POST['password'])){
	$param=array(
		array('s',$_POST['username']),
		array('s',md5('f^89#hJ!'.md5($_POST['password'])))
	);
	
	db::query("INSERT INTO user (username, password) VALUES(?,?)",$param);
}

//pārbaude vai labo Lietotāju
if(isset($_POST['username'],$_POST['password'],$_POST['edit'],$_POST['id']) && !empty($_POST['username']) && (int)$_POST['id']>0){
	//vai maina paroli?
	if($_POST['password']!=''){
		$param=array(
			array('s',$_POST['username']),
			array('s',md5('f^89#hJ!'.md5($_POST['password']))),
			array('i',$_POST['id'])
		);
		
		db::query("UPDATE user SET username=?, password=? WHERE id=? LIMIT 1",$param);
	}
	else{
		$param=array(
			array('s',$_POST['username']),
			array('i',$_POST['id'])
		);
		
		db::query("UPDATE user SET username=? WHERE id=? LIMIT 1",$param);
	}
	header("Location: ".WEB_URL."users/");	
}

//pārbaude vai dzēš
if(isset($_GET['delete'])){
	$param=array(
		array('i',(int)$_GET['delete'])
	);		
	db::query("DELETE FROM user WHERE id=? LIMIT 1",$param);
	header("Location: ".WEB_URL."users/");	
}

//labošanas skata izvade
if(isset($_GET['edit'])){
	$param=array(
		array('i',$_GET['edit'])
	);
	$user=db::query_row("SELECT * FROM user WHERE id=?",$param);
	if($user!=NULL){
		echo'
		<form method="post" action="">
			<div class="mb-3">
				<label for="InputUsername" class="form-label">Lietotājvārds</label>
				<input value="'.$user['username'].'" type="text" class="form-control" id="InputUsername" name="username">    
			</div>
			<div class="mb-3">
				<label for="InputPassword" class="form-label">Parole</label>
				<input type="password" class="form-control" id="InputPassword" name="password">
			</div>
			<input type="hidden" name="id" value="'.$user['id'].'" />
			<button type="submit" name="edit" class="btn btn-primary">Saglabāt</button>
		</form>';
	}
	else{
		echo'
		<div class="alert alert-danger" role="alert">
			Lietotājs neeksistē!
		</div>';
	}
}
else{	
	echo'
	<div class="modal fade" tabindex="-1" id="addUser">
		<div class="modal-dialog">
			<form method="post" action="">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Pievienot lietotāju</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Aizvērt"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="exampleInputUsername" class="form-label">Lietotājvārds</label>
						<input type="text" class="form-control" id="exampleInputUsername" name="username">    
					</div>
					<div class="mb-3">
						<label for="exampleInputPassword" class="form-label">Parole</label>
						<input type="password" class="form-control" id="exampleInputPassword" name="password">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aizvērt</button>
					<button type="submit" name="add" class="btn btn-primary">Saglabāt</button>
				</div>
			</div>
			</form>
		</div>
	</div>
	
	<p class="text-end"><button data-bs-toggle="modal" data-bs-target="#addUser" type="button" class="btn btn-primary">Pievienot</button></p>
	';
	
	
	
	//Saraksta skata izvade
	$users=db::query("SELECT * FROM user");
	if($users!=NULL){
		echo'
		<table class="table">
			<thead>
				<tr>
					<th style="width:40px;" class="text-center">ID</th>
					<th>Lietotajvārds</th>
					<th style="width:120px;" class="text-center">Darbības</th>
				</tr>
			</thead>
			<tbody>';
			
				foreach($users as $user){
					echo'
					<tr>
						<td class="text-center">'.$user['id'].'</td>
						<td>'.$user['username'].'</td>';
						if($user['id']==$_SESSION['id']){
							echo'
							<td class="text-center">
								<a data-bs-toggle="tooltip" data-bs-title="Labot" class="btn btn-primary" href="'.WEB_URL.'users/?edit='.$user['id'].'">
									<i class="bi bi-pencil"></i>
								</a> 
							</td>';
						}
						else{
							echo'
							<td class="text-center">
								<a data-bs-toggle="tooltip" data-bs-title="Labot" class="btn btn-primary" href="'.WEB_URL.'users/?edit='.$user['id'].'">
									<i class="bi bi-pencil"></i>
								</a>  
								<a data-bs-toggle="tooltip" data-bs-title="Dzēst" class="btn btn-primary" href="'.WEB_URL.'users/?delete='.$user['id'].'"><i class="bi bi-trash"></i></a>
							</td>';
						}
					echo'
					</tr>';
				}
			
			echo'
			</tbody>
		</table>';	
	}
	else{
		echo'
		<div class="alert alert-danger" role="alert">
			Lietotāju nav!
		</div>';
	}
}


?>