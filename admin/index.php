<?php 
  //载入配置文件
  require_once '../config.php';
  require_once '../functions.php';
  xiu_get_current_user();

  
  $post_count=xiu_fetch_one('select count(1) as num from posts;')['num'];
  $draft_count=xiu_fetch_one("select count(1) as num from posts where status='drafted';")['num'];
  $category_count=xiu_fetch_one('select count(1) as num from categories;')['num'];
  $comments_count=xiu_fetch_one('select count(1) as num from comments;')['num'];
  $held_count=xiu_fetch_one("select count(1) as num from comments where status='held';")['num'];

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    
    <!-- 载入外部的公共的导航栏文件 -->
    <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="/admin/post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $post_count; ?></strong>篇文章（<strong><?php echo $draft_count; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $category_count ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count ?></strong>条评论（<strong><?php echo $held_count ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page='index'; ?>
  <!-- 载入外部的公共的侧边栏文件 -->
  <?php include 'inc/sidebar.php' ?>



  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
