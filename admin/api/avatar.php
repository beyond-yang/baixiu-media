<?php 
	require_once '../../functions.php';
	$email=$_GET['email'];
	$result=xiu_fetch_one("select avatar from users where email='{$email}';");
	echo $result['avatar'];
 ?>