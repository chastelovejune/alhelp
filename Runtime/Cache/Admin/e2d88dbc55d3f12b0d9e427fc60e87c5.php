<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>后台管理 <?php echo (C("waketitle")); ?></title>
    <link href="__CSS__/bootstrap.min.css?v=3.4.0" rel="stylesheet" />
    <link href="http://http://120.24.14.210/font-awesome/css/font-awesome.css?v=4.3.0" rel="stylesheet" />
    <link href="__CSS__/plugins/morris/morris-0.4.3.min.css" rel="stylesheet" />    
    <link href="__CSS__/animate.css" rel="stylesheet">
    <link href="__CSS__/style.css?v=2.2.0" rel="stylesheet">
    <link href="__JS__/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="__JS__/plugins/layer/skin/layer.css" rel="stylesheet">
  <link href="script/froala/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link href="script/froala/css/froala_editor.min.css" rel="stylesheet" type="text/css">
  <!-- The fav icon -->
  <link rel="shortcut icon" href="__IMG__/favicon.ico">
    <style type="text/css">
        table img{width:100px;}
        p.comment{width: 500px;}
        /**隐藏editor版权声明**/
        .fr-wrapper + div{display:none}
        .fr-popup{margin-left:20px; z-index:5px;}
        .fr-arrow{display:block}
    </style>
    <script src="__JS__/jquery-2.1.1.min.js"></script>
    <script src="__JS__/bootstrap.min.js?v=3.4.0"></script>
    <script src="__JS__/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="__JS__/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="__JS__/hplus.js?v=2.2.0"></script>
    <script src="__JS__/plugins/pace/pace.min.js"></script>
    <script src="__JS__/plugins/layer-v2.1/layer/layer.js"></script>
    <script src="__JS__/plugins/layer-v2.1/layer/extend/layer.ext.js"></script>
    <script src="__JS__/plugins/layer/laydate/laydate.js"></script>
    <script src="__JS__/plugins/toastr/toastr.min.js"></script>
    <script src="http://http://120.24.14.210/plugins/zeroclipboard/ZeroClipboard.js"></script>
    <script src="/script/jquery.form.min.js"></script>
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
    <script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/localization/messages_zh.js"></script>
    <script src="/script/jquery.qqFace.js"></script>
    <script src="/js/datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="/js/datepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
    <script src="<?php echo U('/home/common/js',[],'js');?>"></script>

</head>
<body>
	<div id="wrapper">
        <nav class="navbar-default navbar-static-side" id="side_navbar" role="navigation" style="overflow: auto; height: 100%;">
          <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
              <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                    <img alt="image" class="img-circle" src="/img/profile_small.jpg" />
                  </span>
                  <a data-toggle="dropdown" class="dropdown-toggle" href="index.html#">
                    <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Beaut-zihan</strong>
                    </span> <span class="text-muted text-xs block">超级管理员 <b class="caret"></b></span> </span>
                  </a>
                  <ul class="dropdown-menu animated fadeInRight m-t-xs">
                    <li><a href="form_avatar.html">修改头像</a>
                    </li>
                    <li><a href="profile.html">个人资料</a>
                    </li>
                    <li><a href="contacts.html">联系我们</a>
                    </li>
                    <li><a href="mailbox.html">信箱</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="login.html">安全退出</a>
                    </li>
                  </ul>
                </div>
                <div class="logo-element">
                  H+
                </div>
              </li>

              <?php if(is_array($thisMenu)): foreach($thisMenu as $name1=>$sub_menus): ?><li >
                <a href="index.html"><i class="fa fa-th-large"></i> <span class="nav-label"><?php echo ($name1); ?></span> <span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">
                <?php if(is_array($sub_menus)): foreach($sub_menus as $name2=>$url): ?><li><a href="<?php echo ($url); ?>"><?php echo ($name2); ?></a></li><?php endforeach; endif; ?>
                  </ul>
                </li><?php endforeach; endif; ?>
            </ul>
          </div>
        </nav>
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
              <nav class="navbar-default navbar-static-top" role="navigation" id="user_navbar" style="margin-bottom: 0px;position:fixed; ">
                <ul class="nav navbar-nav ">
      <?php if(is_array($menus)): foreach($menus as $index=>$tmp): ?><li><a <?php if($thisMenuIndex == $index): ?>class="btn-info"<?php endif; ?> href="<?php echo ($tmp['url']); ?>"><?php echo ($tmp['name']); ?></a></li><?php endforeach; endif; ?>
                  <li>
                    <a href="<?php echo U('/admin/index/logout');?>">
                      <i class="fa fa-sign-out" ></i> 退出
                    </a>
                  </li>
                </ul>
              </nav>
              <div style="height:60px;"></div>
            </div>
            <div class="row  border-bottom white-bg dashboard-header" style="width:100%;overflow:auto;">

<center><h2><<?php echo ($type_name[$type]); ?>></h2></center>
<a class="btn btn-outline btn-success btn-sm" href="<?php echo U('/admin/Information/add',['type'=>$type]);?>">添加</a>
<table class="table table-striped">
  <thead>
    <tr>

    <td>日期</td>
      <td>编号</td>
      <td>栏目</td>
      <td>分类</td>
      <td>发布人</td>
      <td>文章标题</td>
      <?php if($type == 0): ?><td>需要</td>
        <?php else: endif; ?>
     <!-- <td>需要金币</td>-->
      <td>浏览数</td>
      <?php if($type == 0): ?><td>下载数</td>
        <?php else: endif; ?>
      <!--<td>排序</td>-->
      <td>状态</td>
      <td>操作</td>

    </tr>
  </thead>
    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
      <td><?php echo substr($v['add_time'],0,10);?></td>
      <td><?php echo ($v['id']); ?></td>
      <td><?php echo ($type_name[$type]); ?></td>
      <td><?php echo ($v['name']); ?></td>
      <td><?php echo ($v['nickname']); ?></td>
      <td><?php echo ($v['title']); ?></td>
      <?php if($type == 0): ?><td>
        <?php if($v['attachment_type'] == 0): ?>免费
          <?php elseif($v['attachment_type'] == 1): ?>
        <span style="color:greenyellow">积分:<?php echo ($v['attachment_score']); ?>个<span>
          <?php elseif($v['attachment_type'] == 2): ?>
        <span style="color:cornflowerblue">RMB:<?php echo ($v['attachment_cost']); ?>元<span>
          <?php else: ?>
          <span style="color:gold">金币:<?php echo ($v['attachment_coin']); ?>个<span><?php endif; ?>
      </td>
        <?php else: endif; ?>
      <td><?php echo ($v['views']); ?></td>
      <?php if($type == 0): ?><td><?php echo ($v['download_num']); ?></td>
        <?php else: endif; ?>
      <td><?php echo ($v['status_name']); ?></td>
      <td>
        <?php if($v['status'] == 0): ?><a class="btn-default btn btn-sm" href="<?php echo U('admin/Information/stop',['id'=>$v['id']]);?>">禁用</a>
          <?php else: ?>
          <a class="btn-default btn btn-sm" href="<?php echo U('admin/Information/start',['id'=>$v['id']]);?>" style="background-color: green;border:0px">启用</a><?php endif; ?>
        <a class="btn-info btn btn-sm" href="<?php echo U('admin/Information/edit',['id'=>$v['id'],'type'=>$type]);?>">编辑</a>
        <a class="btn-danger btn btn-sm" href="<?php echo U('delete',['id'=>$v['id']]);?>">删除</a></td>


    </tr><?php endforeach; endif; ?>
</table>
<div class="u_fy">
  <?php echo pageHtml($count); ?>
</div>
            </div>
        </div>
    </div>
  
<script>
//导航高亮
$(function(){
  var controller_name = location.pathname.match(/_([\w]*)_/)[1];
  $("#side-menu").find("a").each(function(){
  	var href = $(this).attr("href");
  	if (href.indexOf(controller_name)!=-1) {
	  $(this).parent().addClass("active");
	  $(this).parent().parent().addClass("in");
	  $(this).parent().parent().parent().addClass("active")
	  return false;
  	}
  });
});

function page(page){
  var search = location.search;
  if(search){
    var url = search.replace(/page=?[\d]*/,"page="+page);
    window.location = url;
  }else{
    window.location = "?page="+page;  
  }
}

$(window).resize(function(){
	//调整顶部用户菜单的宽度
	$("#user_navbar").width($(document).width()-$("#side_navbar").width());
	drowPageContent();
});
</script>
</body>
</html>