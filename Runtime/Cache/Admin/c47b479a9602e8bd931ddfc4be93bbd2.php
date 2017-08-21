<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="renderer" content="webkit">
    <title>登录 <?php echo (C("waketitle")); ?>平台</title>
    <link href="__CSS__/bootstrap.min.css?v=3.4.0" rel="stylesheet">
    <link href="http://http://120.24.14.210/font-awesome/css/font-awesome.css?v=4.3.0" rel="stylesheet">
    <link href="__CSS__/animate.css" rel="stylesheet">
    <link href="__CSS__/style.css?v=2.2.0" rel="stylesheet">
    <style type="text/css">
		.header {
		  height:100px;
		}
		 
    </style>
</head>

<body class="gray-bg">
 	
    <div class="middle-box text-center loginscreen  animated fadeInDown">
        <div>
        	<h3>欢迎使用<?php echo (C("waketitle")); ?>后台管理系统</h3>
            <div>
              	<img src="__IMG__/logo.png" class="img-responsive" alt="Responsive image" /> 
            </div>
            <form class="m-t" role="form" action="<?php echo U('Index/login');?>" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="用户名" required="">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password"  placeholder="密码" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b">登 录</button>
                <p class="text-muted text-center">
                	<a href="login.html#">忘记密码了？</a>|<a href="tel:13988811222">联系技术中心</a>
                </p>
            </form>
        </div>
    </div>
    <script src="__JS__/jquery.js"></script>
    <script src="__JS__/bootstrap.min.js?v=3.4.0"></script>
</body>
</html>