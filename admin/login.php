<?php
  //引入配置文件
  require_once '../config.php'; 
  
  //服务端对客户端提交的数据进行处理
  //1.接收数据并校验
  //2.持久化
  //3.响应
  function xiu_login () {
    if (empty($_POST['email'])||empty($_POST['password'])) {
      $GLOBALS['error_message'] = '请输入完整的用户名和密码';
      return;
    }
    //接收用户提交的用户名和密码存入变量中
    $email=$_POST['email'];
    $password=$_POST['password'];

    //连接数据库
    $conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if (!$conn) {
      exit('数据库连接失败');
    }
    //建立查询
    $query=mysqli_query($conn,"select * from users where email='{$email}';");
    if (!$query) {
      $GLOBALS['error_message'] = '查询失败';
      return;
    }
    $user=mysqli_fetch_assoc($query);
   
    if (!$user) {
      $GLOBALS['error_message'] = '该用户名不存在';
      return;
    }
    if ($user['password']!==$password) {
      $GLOBALS['error_message'] = '输入的密码和用户名不匹配';
      return;
    }
    //访问控制
    session_start();
    //把当前的登陆者的信息存到session中
    $_SESSION['current_login_user']=$user;
    //管理员登录成功后，自动跳转到index页面
    header('Location: /admin/index.php');
    



    // if ($_POST['email']!=='admin@zce.me') {
    //   // exit('用户名不存在');
    //   $GLOBALS['error_message'] = '用户名不存在';
    //   return;
    // }
    // if ($_POST['password']!=='123') {
    //   $GLOBALS['error_message'] = '输入的密码和用户名不匹配';
    //   return;
    // }

  }
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    xiu_login();
  }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $error_message; ?>
        </div>
      <?php endif ?>
      
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" type="email" name="email" value="<?php echo $_POST['email'] ?>" class="form-control" placeholder="邮箱" autofocus autocomplete="off">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" name="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $('#email').on('change',function () {
      var reg=/^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
      var value=$(this).val();
      if (!reg.test(value)) return;
      $.get('/admin/api/avatar.php',{email: value},function (res) {
        $('.avatar').fadeOut(function () {
          $(this).on('load',function () {
            $(this).fadeIn();
          }).attr('src',res);
        })
      });
    });
  </script>
</body>
</html>
