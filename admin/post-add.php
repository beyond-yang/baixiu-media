<?php
  require_once '../functions.php';
  $current_user=xiu_get_current_user(); 
  //对于客户端提交的表单的三步操作
  //1.接收数据并校验
  //2.持久化
  //3.响应
  
  function xiu_add_artical() {
    if (empty($_POST['title'])
      ||empty($_POST['content'])
      ||empty($_POST['slug'])
      ||empty($_POST['category'])
      ||empty($_POST['created'])
      ||empty($_POST['status'])) {
      $GLOBALS['error_message'] = '请完整填写所有内容';
    } else if (xiu_fetch_one("select count(1) as count from posts where slug='{$_POST['slug']}';")['count']>=1) {
      $GLOBALS['error_message'] = 'slug别名已经存在,请修改别名';
    } else {
      
      if (empty($_FILES['feature'])) {
        $GLOBALS['error_message'] = '必须上传文件';
        return;
      }
      $feature=$_FILES['feature'];
      if ($feature['error']!==UPLOAD_ERR_OK) {
        $$GLOBALS['error_message'] = '文件上传失败';
        return;
      }
      //验证图片类型
      // $img_type=array('jpg','png','jpeg');
      // if (!$img_type.indexOf($feature['type'])) {
      //   $GLOBALS['error_message'] = '不被允许的文件类型';
      //   return;
      // }
      //验证图片大小
      $feature=$_FILES['feature'];
      if ($feature['size']<1*1024) {
        $$GLOBALS['error_message'] = '上传的图片过小';
        return;
      }
      if ($feature['size']>100*1024) {
        $$GLOBALS['error_message'] = '上传的图片过小';
        return;
      }
      
      //把上传的文件从默认路径移动到指定路径
      // $ext=pathinfo($feature['name'],PATHINFO_EXTENSION);
      // $target='../../static/uploads/img-'.uniqid().'.'.$ext;
      // if (!move_uploaded_file($feature['tmp_name'], $target)) {
      //   $GLOBALS['error_message'] = '上传失败';
      //   return;
      // }
      $ext=pathinfo($feature['name'],PATHINFO_EXTENSION);
      $target='../static/uploads/img-'.uniqid().'.'.$ext;
      if (!move_uploaded_file($feature['tmp_name'], $target)) {
       exit('上传失败');
      }
      // var_dump($_FILES['feature']);
      // echo '啦啦啦';
      $path=substr($target, 5);

       // var_dump($_POST['title'])
      
      //数据提交的格式正确
      $title=$_POST['title'];
      $content=$_POST['content'];
      $slug=$_POST['slug'];
      $feature_path=$path;
      $created=$_POST['created'];
      $status=$_POST['status'];
      $user_id=$current_user['id'];
      $category_id=$_POST['category'];
      $sql=sprintf(
        "insert into posts values(null,'%s','%s','%s','%s','%s',0,0,'%s',%d,%d)",
        $slug,
        $title,
        $feature_path,
        $created,
        $content,
        $status,
        $user_id,
        $category_id);
      $rows=xiu_delete_or_insert($sql);
      if ($rows>0) {
        $GLOBALS['success_message'] = '保存成功';
        header('Location: /admin/posts.php');
      } else {
        $$GLOBALS['error_message'] = '保存失败';
      }

    }

  }

  //判断客户端的请求是否是post请求，如果时post请求则接受校验持久化响应
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    xiu_add_artical();
  }

  //加载分类列表
  $categories=xiu_fetch_all("select * from categories;");

  // http://dummyimage.com/800x600/f2797d&text=zce.me
  // /static/uploads/img-5dc37bb02bc9f.jpg

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $error_message; ?>
        </div>
      <?php endif ?>
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
          <strong><?php echo $success_message; ?></strong>
        </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" value="<?php echo isset($_POST['title'])?$_POST['title']:''; ?> " type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" value="<?php echo isset($_POST['slug'])?$_POST['slug']:''; ?>"  type="text" placeholder="slug">
            <p class="help-block">https://baixiu.io/admin/posts/<strong>slug</strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none; ">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category" >
              <?php foreach ($categories as $item): ?>
                <option value="<?php echo $item['id'] ?>"
                <?php echo isset($_POST['category'])&&$_POST['category']===$item['id']?'selected':''; ?>><?php echo $item['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" value="<?php echo isset($_POST['created'])?$_POST['created']:''; ?>"  type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted" <?php echo isset($_POST['status'])&&$_POST['status']==='drafted'?'selected':''; ?>>草稿</option>
              <option value="published" <?php echo isset($_POST['status'])&&$_POST['status']==='published'?'selected':''; ?>>已发布</option>
              <option value="trashed" <?php echo isset($_POST['status'])&&$_POST['status']==='trashed'?'selected':''; ?>>回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- 载入外部的公共的侧边栏文件 -->
  <?php $current_page='post-add'; ?>
  <?php include 'inc/sidebar.php' ?>
  
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //图片文件上传预览
    $('#feature').on('change',function () {
      var file=$(this).prop('files')[0];
      //为这个文件对象创建一个Object URL
      var url=URL.createObjectURL(file);
      $(this).siblings('.thumbnail').attr('src',url).fadeIn();
    })
    //slug预览
    $('#slug').on('change',function () {
      $(this).next().children().text($(this).val());
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
