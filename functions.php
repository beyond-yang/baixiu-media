<?php 
	require_once 'config.php';
	session_start();
	function xiu_get_current_user () {
		if (empty($_SESSION['current_login_user'])) {
		    header('Location: /admin/login.php');
		    exit();
	  	} else {
	  		return $_SESSION['current_login_user'];
	  	}
	}
	//创建数据库连接对象函数
	function xiu_connect () {
		$conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
		if (!$conn) {
			exit('数据库连接失败');
		}
		return $conn;
	}
	//封装获取多条记录的数据库连接查询
	function xiu_fetch_all ($sql) {
		$conn=xiu_connect();
		$query=mysqli_query($conn,$sql);
		if (!$query) {
			return false;
		}
		$res_arr=array();
		while ($result=mysqli_fetch_assoc($query)) {
			array_push($res_arr, $result);
		}
		mysqli_free_result($result);
		mysqli_close($conn);
		return $res_arr;
	}
	//封装获取一条记录的数据库连接查询
	function xiu_fetch_one ($sql) {
		$all=xiu_fetch_all($sql);
		return isset($all[0])?$all[0]:null;
	 //    $conn=xiu_connect();
	 //    $query=mysqli_query($conn,$sql);
	 //    if (!$query) {
	 //      return false;
	 //    }
	 //    $result=mysqli_fetch_assoc($query);
	 //    mysqli_free_result($result);
		// mysqli_close($conn);
	 //    return $result;
	}

	//封装删除/新增函数/更新
	function xiu_delete_or_insert ($sql) {
		$conn=xiu_connect();
		$query=mysqli_query($conn,$sql);
		if (!$query) {
			return false;
		}
		$rows=mysqli_affected_rows($conn);
		mysqli_close($conn);
		return $rows;

	}

	
 ?>