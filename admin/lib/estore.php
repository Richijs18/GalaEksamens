<?php
if (!defined('CHK')) exit;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'image.php';

//pārbaude vai pievieno preci
if(isset($_POST['url'],$_POST['price'],$_POST['title'],$_POST['text'],$_POST['add']) && !empty($_POST['url']) && !empty($_POST['title'])){

	$param=array(
		array('s',$_POST['url']),
		array('s',$_POST['title']),
		array('d',$_POST['price']),
		array('s',$_POST['text'])
	);	
	db::query("INSERT INTO products (url, title,price,text,menu_id) VALUES(?,?,?,?,12)",$param);
	$id=db::insert_id();
	if(isset($_FILES['image']['tmp_name'])){
		$filename = stripslashes($_FILES['image']['name']);
		$filename = str_replace(array("(",")"),array("",""),$filename);
		$ext=explode(".",$filename);
		$extension = mb_strtolower(end($ext));
		if(in_array($extension,array("jpg","jpeg","png","gif"))){
			$image=new image($_FILES['image']['tmp_name']);
			$image->resize(800,600,2);			
			$image->save(ABS_PATH_PUBLIC.'\img\products\\'.$id.'.jpg');			
		}
	}
}

//pārbaude vai labo preci
if(isset($_POST['url'],$_POST['title'],$_POST['price'],$_POST['text'],$_POST['edit'],$_POST['id']) && !empty($_POST['url']) && !empty($_POST['title'])){
	$data=db::query_row("SELECT * FROM products WHERE id=?",array(array('i',$_POST['id'])));
	if($data!=NULL){
		$param=array(
			array('s',$_POST['url']),
			array('s',$_POST['title']),
			array('d',$_POST['price']),
			array('s',$_POST['text']),
			array('i',$_POST['id'])
		);
			
		db::query("UPDATE products SET url=?, title=?,price=?,text=? WHERE id=? LIMIT 1",$param);
		
		if(isset($_FILES['image']['tmp_name'])){
			if(file_exists(ABS_PATH_PUBLIC.'\img\products\\'.$data['id'].'.jpg')){
				unlink(ABS_PATH_PUBLIC.'\img\products\\'.$data['id'].'.jpg');
			}
			
			$filename = stripslashes($_FILES['image']['name']);
			$filename = str_replace(array("(",")"),array("",""),$filename);
			$ext=explode(".",$filename);
			$extension = mb_strtolower(end($ext));
			if(in_array($extension,array("jpg","jpeg","png","gif"))){
				$image=new image($_FILES['image']['tmp_name']);
				$image->resize(800,600,2);			
				$image->save(ABS_PATH_PUBLIC.'\img\products\\'.$data['id'].'.jpg');			
			}
		}
	}
	header("Location: ".WEB_URL."estore/");	
}

//pārbaude vai dzēš
if(isset($_GET['delete'])){
	$param=array(
		array('i',(int)$_GET['delete'])
	);		
	db::query("DELETE FROM products WHERE id=? LIMIT 1",$param);
	header("Location: ".WEB_URL."estore/");	
}
?>
<script>
	
	$(document).ready(function() {		
		$('.summernote').summernote({
			height: 300,
			lang: 'lv-LV',
			callbacks: {
				onInit: function() {
					$("button[data-toggle='dropdown']").each(function (index) { 
						$(this).removeAttr("data-toggle").attr("data-bs-toggle", "dropdown"); 
						console.log('change');
					});
				}
			}
		});
	
		$('.summernote').on('summernote.init', function() {
			$("button[data-toggle='dropdown']").each(function (index) { 
				$(this).removeAttr("data-toggle").attr("data-bs-toggle", "dropdown"); 
				console.log('change');
			});
		});
		
		tinymce.init({
			selector: 'textarea#tiny',
			file_picker_callback: (cb, value, meta) => {
				const input = document.createElement('input');
				input.setAttribute('type', 'file');
				input.setAttribute('accept', 'image/*');

				input.addEventListener('change', (e) => {
				  const file = e.target.files[0];

				  const reader = new FileReader();
				  reader.addEventListener('load', () => {
					/*
					  Note: Now we need to register the blob in TinyMCEs image blob
					  registry. In the next release this part hopefully won't be
					  necessary, as we are looking to handle it internally.
					*/
					const id = 'blobid' + (new Date()).getTime();
					const blobCache =  tinymce.activeEditor.editorUpload.blobCache;
					const base64 = reader.result.split(',')[1];
					const blobInfo = blobCache.create(id, file, base64);
					blobCache.add(blobInfo);

					/* call the callback and populate the Title field with the file name */
					cb(blobInfo.blobUri(), { title: file.name });
				  });
				  reader.readAsDataURL(file);
				});

				input.click();
			  },
			file_picker_types: 'file image media',
			plugins: 'image,media,table,link',
			language: 'lv',
			toolbar: [
			
    { name: 'history', items: [ 'undo', 'redo' ] },
    { name: 'styles', items: [ 'styles' ] },
    { name: 'formatting', items: [ 'bold', 'italic' ] },
    { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ] },
    { name: 'indentation', items: [ 'outdent', 'indent' ] }
  ]
		});
	});
</script>
<?php
//labošanas skata izvade
if(isset($_GET['edit'])){
	$param=array(
		array('i',$_GET['edit'])
	);
	$product=db::query_row("SELECT * FROM products WHERE id=?",$param);
	if($product!=NULL){		
		echo'		
		<form method="post" action="" enctype="multipart/form-data">
			<div class="mb-3">
				<label for="InputUrl" class="form-label">Adrese</label>
				<input value="'.$product['url'].'" type="text" class="form-control" id="InputUrl" name="url">    
			</div>
			<div class="mb-3">
				<label for="InputTitle" class="form-label">Nosaukums</label>
				<input value="'.$product['title'].'" type="text" class="form-control" id="InputTitle" name="title">    
			</div>
			<div class="mb-3">
				<label for="InputPrice" class="form-label">Cena</label>
				<input value="'.$product['price'].'" type="text" class="form-control" id="InputPrice" name="price">    
			</div>
			<div class="mb-3">
				<p class="text-center">
					<img style="max-width:100%;" src="'.WEB_URL_PUBLIC.'img/products/'.$product['id'].'.jpg" />								
				</p>
				<label for="InputImage" class="form-label">Attēls</label>
				<input type="file" class="form-control" id="InputImage" name="image">    
			</div>
			<div class="mb-3">
				<label for="InputText" class="form-label">Saturs</label>
				<textarea id="tiny" name="text" class="4summernote">'.$product['text'].'</textarea>    
			</div>
			<input type="hidden" name="id" value="'.$product['id'].'" />
			<button type="submit" name="edit" class="btn btn-primary">Saglabāt</button>			
		</form>';
	}
	else{
		echo'
		<div class="alert alert-danger" role="alert">
			Prece neeksistē!
		</div>';
	}
}
else{
	echo'
	<div class="modal fade modal-lg" tabindex="-1" id="addProduct">
		<div class="modal-dialog">
			<form method="post" action="" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Pievienot preci</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Aizvērt"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="exampleInputUrl" class="form-label">Adrese</label>
						<input type="text" class="form-control" id="exampleInputUrl" name="url">    
					</div>
					<div class="mb-3">
						<label for="exampleInputTitle" class="form-label">Nosaukums</label>
						<input type="text" class="form-control" id="exampleInputTitle" name="title">
					</div>
					<div class="mb-3">
						<label for="exampleInputPrice" class="form-label">Cena</label>
						<input type="text" class="form-control" id="exampleInputPrice" name="price">
					</div>
					<div class="mb-3">
						<label for="exampleInputImage" class="form-label">Attēls</label>
						<input type="file" class="form-control" id="exampleInputImage" name="image">
					</div>
					<div class="mb-3">
						<label for="exampleInputText" class="form-label">Apraksts</label>
						<textarea name="text" class="summernote"></textarea>   
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
	
	<p class="text-end"><button data-bs-toggle="modal" data-bs-target="#addProduct" type="button" class="btn btn-primary">Pievienot</button></p>';
	
	//Saraksta skata izvade
	$products=db::query("SELECT * FROM products");
	if($products!=NULL){
		echo'
		<table class="table">
			<thead>
				<tr>
					<th style="width:40px;" class="text-center">ID</th>
					<th>Nosaukums</th>
					<th>Adrese</th>
					<th>Cena</th>
					<th style="width:120px;" class="text-center">Darbības</th>
				</tr>
			</thead>
			<tbody>';			
				foreach($products as $p){
					echo'
					<tr>
						<td>'.$p['id'].'</td>
						<td>'.htmlspecialchars($p['title']).'</td>	
						<td>'.htmlspecialchars($p['url']).'</td>
						<td>'.htmlspecialchars($p['price']).'</td>						
						<td class="text-center">
							<a data-bs-toggle="tooltip" data-bs-title="Labot" class="btn btn-primary" href="'.WEB_URL.'estore/?edit='.$p['id'].'">
								<i class="bi bi-pencil"></i>
							</a>  
							<a data-bs-toggle="tooltip" data-bs-title="Dzēst" class="btn btn-primary" href="'.WEB_URL.'estore/?delete='.$p['id'].'"><i class="bi bi-trash"></i></a>
						</td>						
					</tr>';
				}			
			echo'
			</tbody>
		</table>';	
	}
	else{
		echo'
		<div class="alert alert-danger" role="alert">
			Preču nav!
		</div>';
	}
}

?>