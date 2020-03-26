<?php
  require_once '../functions.php';
  xiu_get_current_user();

  //分页加载文章数据
  //获取用户传入的当前页为第几页
  $page=empty($_GET['page'])||$_GET['page']<=1?1:(int)$_GET['page'];//获取当前页
  $size=10;//一页要显示几条数据
  //$total_pages为数据的总页数
  $count=xiu_fetch_one('select count(1) as count from posts')['count'];
  echo $count;
  $total_pages=(int)ceil(xiu_fetch_one('select count(1) as count from posts')['count']/$size);
  if ($page>$total_pages) {
    $page=$total_pages;
  }
  $offset=($page-1)*$size;


  //处理页面中的页码部分
  $visible_pages=5;
  $region=($visible_pages-1)/2;
  $begin=$page-$region;
  $end=$begin+$visible_pages;

  if ($begin<1) {
    $begin=1;
    $end=$begin+$visible_pages;
  }
  
  if ($end>$total_pages) {
    $end=$total_pages+1;
    $begin=$end-$visible_pages;
  }

  //分类表中的所有分类
  $categories=xiu_fetch_all("select * from categories;");
  //选定文章类别和状态来筛选文章  
  // $category=empty($_GET['category'])?'':$_GET['category'];
  // $status=empty($_GET['status'])?'':$_GET['status'];
  $where="1=1";
  $search=' ';
  if (isset($_GET['category'])&&$_GET['category']!=='all') {
    $where.=" and categories.id=".$_GET['category'];
    $search.="&category=".$_GET['category'];
  }
  if (isset($_GET['status'])&&$_GET['status']!=='all') {
    $where.=" and posts.status='{$_GET['status']}'";
    $search.="&status=".$_GET['status'];
  }


  $posts=xiu_fetch_all(
    "select
      posts.id,
      posts.title,
      users.nickname as user_name,
      categories.name as category_name,
      posts.created,
      posts.status
    from posts 
    inner join users on posts.user_id=users.id
    inner join categories on posts.category_id=categories.id
    where {$where}
    order by posts.created desc
    limit ".$offset.",".$size.";");
  //数据过滤输出的处理状态函数
  function convert_status ($status) {
    $status_arr = array(
    'drafted' => '草稿', 
    'published' => '已发布',
    'trashed' => '回收站');
    return isset($status_arr[$status])?$status_arr[$status]:'';
  }
  //数据过滤输出的处理时间格式的函数
  function convert_time ($time) {
    $time=strtotime($time);
    $date=date('Y年m月d日<b\r/>H:m:s',$time);
    return $date;
  }


  

  
  
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="/admin/post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a id="delete_many" class="btn btn-danger btn-sm" href="/admin/post-delete.php" style="display: none;width: 70;height: 30">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'] ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id'] ?>" 
              <?php echo isset($_GET['category'])&&$_GET['category']===$item['id']?'selected':''; ?>><?php echo $item['name'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all" <?php echo isset($_GET['status'])&&$_GET['status']==='all'?'selected':''; ?>>所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']==='drafted'?'selected':''; ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']==='published'?'selected':''; ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']==='trashed'?'selected':''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <?php for ($i=$begin; $i < $end; $i++) : ?>
            <li <?php echo $i==$page?'class="active"':''; ?>><a href="?page=<?php echo $i.$search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <li><a href="#">下一页</a></li>
        </ul>
        
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
              <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
              <td><?php echo $item['title']; ?></td>
              <td><?php echo $item['user_name']; ?></td>
              <td><?php echo $item['category_name']; ?></td>
              <td class="text-center"><?php echo convert_time($item['created']); ?></td>
              <td class="text-center"><?php echo convert_status($item['status']); ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="/admin/post-delete.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 载入外部的公共的侧边栏文件 -->
  <?php $current_page='posts'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    var tbody_input=$('tbody input');
    var thead_input=$('thead input');
    var delete_many=$('#delete_many');
    var selected=[];
    tbody_input.on('change',function () {
      var id=$(this).data('id');
      if ($(this).prop('checked')) {
        //check为选中状态时，把选中的那行id追加到selected数组中
        selected.includes(id)||selected.push(id);
      } else {
        //check为取消选中状态后，删除selected数组中存入 的这条的id
        selected.splice(selected.indexOf(id),1);
      }
      selected.length?delete_many.fadeIn():delete_many.fadeOut();
      delete_many.prop('search','?id='+selected);
    });
    thead_input.on('change',function () {
      var checked=$(this).prop('checked');
      tbody_input.prop('checked',checked).trigger('change');
    });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
