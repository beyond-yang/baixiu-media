<?php
  require_once '../functions.php';
  xiu_get_current_user(); 
  
  //添加新分类目录
  //服务端 1.接收数据并校验 2.持久化 3.响应
  function xiu_add_category () {
    if (empty($_POST['slug'])||empty($_POST['name'])) {
      $GLOBALS['message'] = '请完整填写数据后添加';
      $GLOBALS['success'] = false;
      return;
    }
    $slug=$_POST['slug'];
    $name=$_POST['name'];
    $rows=xiu_delete_or_insert("insert into categories values(null,'{$slug}','{$name}')");
    $GLOBALS['success'] = $rows>0;
    $GLOBALS['message']=$rows<=0?'添加失败':'添加成功';

  }
  //点击编辑按钮时，服务端给客户端返回一个带有数据的表单
  function xiu_save_category () {
    global $current_edit;
    $id=$current_edit['id'];
    $name=empty($_POST['name'])?$current_edit['name']:$_POST['name'];
    $current_edit['name']=$name;
    $slug=empty($_POST['slug'])?$current_edit['slug']:$_POST['slug'];
    $current_edit['slug']=$slug;
    //更新
    $rows=xiu_delete_or_insert("update categories set slug='{$slug}', name='{$name}' where id=".$id);
    $GLOBALS['success'] = $rows>=0;
    $GLOBALS['message'] = $rows<0?'更新失败':'更新成功';
  }
  
 

  //编辑
  //由于我们把客户端对编辑，添加，保存，和分类目录操作的服务端都放在了
  //这个页面，所以对于几个请求要分类操作，一共分为四类
  //1.点编辑按钮时，get请求+有id传参
  //2.点击添加时，post请求+没有id传参
  //3.点击保存时，post请求+有id传参
  //4.点击侧边栏的分类目录时，get请求+无id传参
  //所以可以通过看请求方式和有无id传参，来判断要具体怎么操作
  

  if($_SERVER['REQUEST_METHOD']==='POST') {
    if (!empty($_GET['id'])) {
      //以下是点击保存时进行的操作
      $current_edit=xiu_fetch_one("select * from categories where id=".$_GET['id']);
      xiu_save_category();
      
    } else {
      //以下是点击添加时进行的操作
      xiu_add_category();
    }

  } else if ($_SERVER['REQUEST_METHOD']==='GET') {
    if (!empty($_GET['id'])) {
      //以下是点编辑按钮时进行的操作
      $current_edit=xiu_fetch_one("select * from categories where id=".$_GET['id']);
    } else {
      //以下是点击侧边栏分类目录时进行的操作
    }
  }

   $categories=xiu_fetch_all("select * from categories;");
  // if (empty($_GET['id'])) {
    
  // }
  // $id=$_GET['id'];
  
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <?php if ($success): ?>
          <div class="alert alert-success">
            <strong>成功！</strong> <?php echo $message; ?>
          </div>
          <?php else: ?>
          <div class="alert alert-danger">
            <strong>错误！</strong> <?php echo $message; ?>
          </div>
        <?php endif ?>
      <?php endif ?>
      
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']?>?id=<?php echo $current_edit['id'] ?>" method="post">
              <h2>添加新分类目录</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" value=" <?php echo $current_edit['name'] ?> " type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" value=" <?php echo $current_edit['slug'] ?> " type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存</button>
              </div>
            </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
              <h2>添加新分类目录</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
          <?php endif ?>
          
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn-delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
                  <td><?php echo $item['name']; ?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td class="text-center">
                    <a href="/admin/categories.php?id=<?php echo $item['id'] ?>" class="btn btn-info btn-xs">编辑</a>
                    <a href="/admin/category-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- 载入外部的公共的侧边栏文件 -->
  <?php $current_page='categories' ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //显示隐藏批量删除按钮
    var tbody_checkbox=$('tbody input');
    var thead_checkbox=$('thead input');
    var btn_delete=$('#btn-delete');
    var id_arr=[];
    tbody_checkbox.on('change',function () {
      if ($(this).prop('checked')) {
        var id=$(this).data('id');
        id_arr.includes(id)||id_arr.push(id);
      } else {
        id_arr.splice(id_arr.indexOf(id),1);
      }
      id_arr.length?btn_delete.fadeIn():btn_delete.fadeOut();
      btn_delete.prop('search','?id='+id_arr);
    });
    //全选全不选操作
    thead_checkbox.on('change',function () {
      var checked=$(this).prop('checked');
      tbody_checkbox.prop('checked',checked).trigger('change');
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
