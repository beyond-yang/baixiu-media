<?php
  require_once '../functions.php';
  xiu_get_current_user(); 
  $count=xiu_fetch_one("select 
                        count(1) as count
                  from comments 
                  inner join posts on comments.post_id=posts.id")['count'];
  $page_size=5;
  $total_pages=ceil($count/$page_size);
  echo $total_pages;
  
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch btn-all" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right" id="pagination-demo">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 载入外部的公共的侧边栏文件 -->
  <?php $current_page='comments'; ?>
  <?php include 'inc/sidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script type="text/x-jsrender" id="comments-tmpl">
    {{for comments}}
       <tr {{if status=='held'}} class="warning" {{else status=='rejected'}} class='danger' {{/if}} data-id={{:id}}>
          <td class="text-center"><input type="checkbox" id='checkbox'></td>
          <td>{{:author}}</td>
          <td>{{:content}}</td>
          <td>《{{:title}}》</td>
          <td>{{:created}}</td>
          <td>{{:status}}</td>
          <td class="text-center">
            {{if status=='held'}}
              <a href="/admin/post-add.php" class="btn btn-info btn-xs">批准</a>
              <a href="/admin/post-add.php" class="btn btn-warning btn-xs">待审</a>
            {{/if}}
            <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
          </td>
        </tr>
    {{/for}}
  </script>
  <script>
    var $tbody=$('tbody');
    var current_page=1;
    function load_page(page) {
        $.get('/admin/api/comments.php',{page: page},function (res) {
        if (page>res.total_pages) {
          load_page(res.total_pages);
          return;
        }
        $('#pagination-demo').twbsPagination('destroy');
        $('#pagination-demo').twbsPagination({
          startPage: page,
          totalPages: res.total_pages,
          visiblePages: res.page_size,
          first:'首页',
          last:'末页',
          prev: '上一页',
          next: '下一页',
          initiateStartPageClick: false,
          onPageClick: function (event,page) {
            load_page(page);
            current_page=page;
          }
      });
        var html=$('#comments-tmpl').render({comments: res.comments});
        $tbody.html(html);
      });
    }
    load_page(current_page);
    
    $tbody.on('click','.btn-delete',function () {
      $id=$(this).parent().parent().data('id');
      $.get('/admin/api/comment-delete.php',{id:$id},function (res) {
          if (!res) {
            return;
          }
          load_page(current_page);
      });
    });

    //批量操作
    var id_arr=[];
    var $btn_all=$('.btn-all');
    $tbody.on('change','#checkbox',function () {
      $id=$(this).parent().parent().data('id');
      if ($(this).prop('checked')) {
        //把id存入到数组中
        id_arr.includes($id)||id_arr.push($id);
      } else {
        id_arr.splice(id_arr.indexOf($id),1);
      }
      id_arr.length?$btn_all.fadeIn():$btn_all.fadeOut()
    });

    //点击thead中的checked进行全选全不选操作
    //
    //怎么动态的获取通过异步方式生成的html元素
    // var $thead_input=$('thead input');
    // $thead_input.on('change',function () {
    //   var checked=$(this).prop('checked');
    //   $(tbody_input).prop('checked',checked).trigger('change');
    // });

    
  </script>

  <script>NProgress.done()</script>
</body>
</html>
