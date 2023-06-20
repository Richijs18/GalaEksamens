<?php
if (!defined('CHK')) exit;

//pārbaude vai pievieno sadaļu
if(isset($_POST['url'],$_POST['title'],$_POST['text'],$_POST['add']) && !empty($_POST['url']) && !empty($_POST['title'])){
	$weight=(int)db::query_value("SELECt MAX(weight) FROM menu")+1;
	$param=array(
		array('s',$_POST['url']),
		array('s',$_POST['title']),
		array('s',$_POST['text']),
		array('i',$weight)
	);	
	db::query("INSERT INTO menu (url, title,text,weight) VALUES(?,?,?,?)",$param);
}

//pārbaude vai labo sadaļu
if(isset($_POST['url'],$_POST['title'],$_POST['text'],$_POST['edit'],$_POST['id']) && !empty($_POST['url']) && !empty($_POST['title'])){
	$param=array(
		array('s',$_POST['url']),
		array('s',$_POST['title']),
		array('s',$_POST['text']),
		array('i',$_POST['id'])
	);
		
	db::query("UPDATE menu SET url=?, title=?,text=? WHERE id=? LIMIT 1",$param);
	header("Location: ".WEB_URL."menu/");	
}

//pārbaude vai dzēš
if(isset($_GET['delete'])){
	$param=array(
		array('i',(int)$_GET['delete'])
	);		
	db::query("DELETE FROM menu WHERE id=? LIMIT 1",$param);
	header("Location: ".WEB_URL."menu/");	
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
	$menu=db::query_row("SELECT * FROM menu WHERE id=?",$param);
	if($menu!=NULL){		
		echo'		
		<form method="post" action="">
			<div class="mb-3">
				<label for="InputUrl" class="form-label">Adrese</label>
				<input value="'.$menu['url'].'" type="text" class="form-control" id="InputUrl" name="url">    
			</div>
			<div class="mb-3">
				<label for="InputTitle" class="form-label">Nosaukums</label>
				<input value="'.$menu['title'].'" type="text" class="form-control" id="InputTitle" name="title">    
			</div>
			<div class="mb-3">
				<label for="InputText" class="form-label">Saturs</label>
				<textarea id="tiny" name="text" class="4summernote">'.$menu['text'].'</textarea>    
			</div>
			<input type="hidden" name="id" value="'.$menu['id'].'" />
			<button type="submit" name="edit" class="btn btn-primary">Saglabāt</button>			
		</form>';
	}
	else{
		echo'
		<div class="alert alert-danger" role="alert">
			Sadaļa neeksistē!
		</div>';
	}
}
else{
	echo'
	<div class="modal fade modal-lg" tabindex="-1" id="addMenu">
		<div class="modal-dialog">
			<form method="post" action="">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Pievienot sadaļu</h5>
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
						<label for="exampleInputText" class="form-label">Saturs</label>
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
	
	<p class="text-end"><button data-bs-toggle="modal" data-bs-target="#addMenu" type="button" class="btn btn-primary">Pievienot</button></p>';
	
	//Saraksta skata izvade
	$menus=db::query("SELECT * FROM menu WHERE type='page'");
	if($menus!=NULL){
		echo'
		<table class="table">
			<thead>
				<tr>
					<th style="width:40px;" class="text-center">ID</th>
					<th>Adrese</th>
					<th>Nosaukums</th>
					<th style="width:120px;" class="text-center">Darbības</th>
				</tr>
			</thead>
			<tbody>';			
				foreach($menus as $m){
					echo'
					<tr>
						<td>'.$m['id'].'</td>
						<td>'.htmlspecialchars($m['url']).'</td>
						<td>'.htmlspecialchars($m['title']).'</td>						
						<td class="text-center">
							<a data-bs-toggle="tooltip" data-bs-title="Labot" class="btn btn-primary" href="'.WEB_URL.'menu/?edit='.$m['id'].'">
								<i class="bi bi-pencil"></i>
							</a>  
							<a data-bs-toggle="tooltip" data-bs-title="Dzēst" class="btn btn-primary" href="'.WEB_URL.'menu/?delete='.$m['id'].'"><i class="bi bi-trash"></i></a>
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
			Sadaļu nav!
		</div>';
	}
}

?>