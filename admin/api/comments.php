<?php 
	require_once '../../functions.php';
	header('Content-Type: application/json');
	$page=empty($_GET['page'])?1:intval($_GET['page']);
	$page_size=10;
	$offset=($page-1)*$page_size;
	$comments=xiu_fetch_all("select comments.*,
									posts.title
						from comments 
						inner join posts on comments.post_id=posts.id
						limit ".$offset.",".$page_size);
	$count=xiu_fetch_one("select 
								count(1) as count
					from comments 
					inner join posts on comments.post_id=posts.id")['count'];
	$total_pages=(int)ceil($count/$page_size);
	
	$json = json_encode(array(
		'comments' => $comments, 
		'total_pages' => $total_pages,
		'visible_pages' => $page_size
		));
	echo $json;
	
 ?>