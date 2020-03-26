<?php 
	require_once '../functions.php';
	if (empty($_GET['id'])) {
		exit('请传入必要参数');
	}
	$id=$_GET['id'];
	xiu_delete_or_insert("delete from categories where id in($id)");
	header('Location: /admin/categories.php');
 ?>