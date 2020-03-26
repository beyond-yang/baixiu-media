<?php 
	require_once '../functions.php';
	if (empty($_GET['id'])) {
		exit();
	}
	$id=$_GET['id'];
	xiu_delete_or_insert("delete from posts where id in(".$id.")");
	$origin=isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'posts.php';
	header('Location: '.$origin);
 ?>