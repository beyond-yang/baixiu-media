<?php 
	require_once '../../functions.php';
	header('Content-Type: application/json');
	$id=empty($_GET['id'])?'':$_GET['id'];
	$rows=xiu_delete_or_insert("delete from comments where id in(".$id.")");
	echo json_encode($rows>0);
	
 ?>