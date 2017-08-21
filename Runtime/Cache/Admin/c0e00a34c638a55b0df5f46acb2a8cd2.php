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

<!-- Include Font Awesome. -->
<link href="//cdn.bootcss.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!-- Include Editor style. -->
<link href='//cdn.bootcss.com/froala-editor/2.4.0/css/froala_editor.min.css' rel='stylesheet' type='text/css' />
<link href='//cdn.bootcss.com/froala-editor/2.4.0/css/froala_style.min.css' rel='stylesheet' type='text/css' />
<!-- Include Editor Plugins style. -->
  <link rel="stylesheet" href="//cdn.bootcss.com/froala-editor/2.4.0/css/plugins/image.css">
  <link rel="stylesheet" href="//cdn.bootcss.com/froala-editor/2.4.0/css/plugins/table.css">
<!-- Include JS file. -->
<script type='text/javascript' src='//cdn.bootcss.com/froala-editor/2.4.0/js/froala_editor.min.js'></script>

<!-- Include Plugins. -->
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/align.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/image.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/link.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/lists.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/paragraph_format.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/paragraph_style.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/quote.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/table.min.js"></script>
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0/js/plugins/url.min.js"></script>

  <!-- Include Language file if we want to use it. -->
  <script type="text/javascript" src="//cdn.bootcss.com/froala-editor/2.4.0-rc.1/js/languages/zh_cn.js"></script>
<style type="text/css">
    p.comment{width: 500px;}
    /**隐藏editor版权声明**/
    .fr-wrapper + div{display:none}
    .fr-popup{margin-left:20px; z-index:5px;}
    .fr-arrow{display:block}
	.fr-box{padding:0 10px 10px 0;}
</style>
<form  method="post" enctype="multipart/form-data">
	标题：
	<input type="text" name="title" value="<?php echo ($info['title']); ?>">
	</br></br>
	发布人:
	<input type="text" name="name" value="<?php echo ($info['nickname']); ?>">
	</br></br>
	选择分类：
<select name="type2">
	<?php if(is_array($type2)): foreach($type2 as $key=>$i): ?><option value="<?php echo ($i[id]); ?>"  <?php if($info['type'] == $i[id]): ?>selected<?php else: endif; ?>><?php echo (str_repeat("&nbsp;&nbsp;&nbsp;",$i["level"])); echo ($i["name"]); ?></option><?php endforeach; endif; ?>
</select>
	</br></br>
	<?php if($type == 0): ?>专业:&nbsp;<?php echo ($info['types'][0]); ?>&nbsp;<?php echo ($info['types'][1]); ?>&nbsp;<?php echo ($info['types'][2]); ?>&nbsp;<?php echo ($info['types'][3]); ?> &nbsp;<input type="button" value="编辑专业" id="edit1" class="btn btn-info"/>
	<ul class="m_form f_clearfix pd_20">
		<div id="major_edit" style="display: none">
	<li class="m_item"><div class="m_formtit">专业类型：</div>
		<select class="u_slt add_type">
			<?php if(is_array(C("3types"))): foreach(C("3types") as $k=>$v): ?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; endif; ?>
		</select>
	</li>

	<li class="m_item school"><div class="m_formtit">地区：</div>
		<select class="u_slt school0">
		</select> </li>
	<li class="m_item school"><div class="m_formtit">大学：</div>

		<select class="u_slt school1"  name="diqu">
		</select>
	</li>
	<li class="m_item school">
		<div class="m_formtit">学院:</div>
		<select class="u_slt school2"  name="daxue">
		</select>
	</li>
	<li class="m_item school"><div class="m_formtit">专业类型：</div>
		<select class="u_slt school3" name="school_id">
		</select>
	</li>

	<li class="m_item unified f_dn">
		<div class="m_formtit">专业分类：</div>
		<select class="u_slt unified0">
		</select>
		专业:
		<select class="u_slt unified1" name="unified_id">
		</select>
	</li>
	<li class="m_item public_subject">
		<div class="m_formtit">专业：</div>
		<select class="u_slt public_subject0" name="public_subject_id">
		</select>
	</li></div>

	<script src="script/layer/layer.js"></script>
	<script type="text/javascript">
		$('#edit1').click(function(){
			$("#major_edit").toggle(1000);
		})

		//获取地区 学校 专业
		$("select.add_type").change(function(){
			var t = $(this).val();
			if (t == 0) {
				$(".school").show();
				$(".unified").hide();
				$(".public_subject").hide();
				$.get("<?php echo U('/home/school/index');?>",function(d){
					var html = build_options(d.message);
					$(".school0").html(html);
					$(".school0").change();
				})
			}else if (t == 1) {
				//统考
				$(".school").hide();
				$(".public_subject").hide();
				$(".unified").show();
				$.get("<?php echo U('/home/unifiedClassify/index');?>",function(d){
					var html = build_options(d.message);
					$(".unified0").html(html);
					$(".unified0").change();
				})
			}else{
				$(".school").hide();
				$(".public_subject").show();
				$(".unified").hide();
				$.get("<?php echo U('/home/publicSubject/index');?>",function(d){
					var html = build_options(d.message);
					$(".public_subject0").html(html);
				})
			}

		})
		$("select.add_type").change();
		$(".unified0").change(function(){
			$.get("<?php echo U('/home/unified/index');?>",{cid:$(this).val()},function(d){
				var html = build_options(d.message);
				$(".unified1").html(html);
			})
		})
		function remove_hide_types(){
			var val = $("select.add_type").val();
			if (val == 0) {
				$(".unified").remove();
				$(".public_subject").remove();
			}else if (val == 1) {
				//统考
				$(".school").remove();
				$(".public_subject").remove();
			}else{
				$(".school").remove();
				$(".unified").remove();
			}
		}
		$(".school0").change(function(){
			$.get("<?php echo U('/home/school/index1');?>",{pid:$(this).val()},function(d){
				var html = build_options(d.message[0]);
				$(".school1").html(html);
				html = build_options(d.message[1]);
				$(".school2").html(html);
				html = build_options(d.message[2]);
				$(".school3").html(html);
			})
		})
		$(".school1").change(function(){
			$.get("<?php echo U('/home/school/index1');?>",{pid:$(this).val()},function(d){
				var html = build_options(d.message[0]);
				$(".school2").html(html);
				html = build_options(d.message[1]);
				$(".school3").html(html);
			})
		})
		$(".school2").change(function(){
			$.get("<?php echo U('/home/school/index');?>",{pid:$(this).val()},function(d){
				var html = build_options(d.message);
				$(".school3").html(html);
			})
		})
	</script>
		<?php elseif($type == 5): ?>动态显示显示学校<?php else: endif; ?>
	<!--选择专业end-->
		</br>
	  <textarea id='edit' name="content" style="margin-top: 30px;margin-left:150px;width:400px;height:200px" >
                <?php echo ($info['content']); ?> </textarea>

	<!-- <textarea name="detail" class="u_inp_t" placeholder="输入你想说的话"></textarea>-->
	<script type="text/javascript">
		$('#edit').froalaEditor({
			height: 300,
			language: "zh_cn",
			toolbarButtons: ["bold","italic","underline","strikeThrough","subscript","superscript","|","paragraphFormat","align","formatOL","formatUL","outdent","indent","quote","insertHR","|","insertLink","insertImage","insertTable","undo","redo","clearFormatting"],

			imageUploadURL: "<?php echo U('home/common/editorImage');?>",//上传到本地服务器
			imageUploadParam:'file', //文件名称 <input type=file name=file>
			//imageUploadParams: {control: "headline",action:"uploads",key:$('input[name="key"]').val()},  //修改图片的参数

			toolbarButtonsXS: ['bold','align','insertImage','undo','redo'], //,'undo','redo'
			imageInsertButtons: ['imageUpload'],
			imageEditButtons: ['imageReplace', 'imageRemove'], //, 'imageAlign'
		}).on('froalaEditor.image.beforeUpload', function (e, editor, images) {
			$('.fr-image-progress-bar-layer').addClass('fr-active');
		}).on('froalaEditor.image.inserted', function (e, editor, $img, response) {
			if( $('.fr-message').text()=='Loading image' ){
				$('.fr-image-progress-bar-layer').removeClass('fr-active');
				$img.froalaEditor('events.focus');
			}
		});
	</script>
链接地址：
<input type="text" name="url" style="margin-top: 5px" value="<?php echo ($info['url']); ?>">
	<div style="margin-top: 20px">
		<p>上传图片：</br></br><input type="file" name="image"/></p><img src="<?php echo ($info['image']); ?>" height="100" width="100">
		<?php if($type == 0): ?></br>
			</br>
			<p>附件:<?php echo ($info['attachment_name']); ?>
				</br>
				附件名字:<input type="text" name="attachment_name" value="<?php echo ($info['attachment_name']); ?>"/>
				</br></br>
				重新上传:<input type="file" name="attachment"/></p>  (提示:请上传压缩包文件)
			</br>
			<input type="radio" name="attachment_type" id="free" value="0" <?php if(($info['attachment_type']) == "0"): ?>checked<?php endif; ?>>免费
		<input type="radio" name="attachment_type" id="score" value="1" <?php if(($info['attachment_type']) == "1"): ?>checked<?php endif; ?>>需要积分
			<input type="radio" name="attachment_type" id="cost" value="2" <?php if(($info['attachment_type']) == "2"): ?>checked<?php endif; ?>>需要收费
			<input type="radio" name="attachment_type" id="coin" value="3" <?php if(($info['attachment_type']) == "3"): ?>checked<?php endif; ?>>需要金币
			</br></br>
			<div id="score_input" style="display: none">
				附件下载所需积分:
				<input type="text" name="attachment_score" placeholder="请填写数字" value="<?php echo ($info['attachment_score']); ?>">
				</br></br>
			</div>
			<div id="cost_input" style="display: none">
				附件下载所需费用:
				<input type="text" name="attachment_cost" placeholder="请填写数字" value="<?php echo ($info['attachment_cost']); ?>">&nbsp;元
				</br></br>
				</div>
				<div id="coin_input" style="display: none">
					附件下载所需金币:
					<input type="text" name="attachment_coin" placeholder="请填写数字" value="<?php echo ($info['attachment_coin']); ?>" >&nbsp;个
				</div>
			</div>
			<?php elseif($type == 5): else: endif; ?>
		<input type="hidden" name="type" value="<?php echo ($type); ?>">
		<input type="submit" value="保存" class="btn btn-info">
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		var atta_type="<?php echo ($info['attachment_type']); ?>";
		if(atta_type==1){
			$('#score_input').show();
		}
		if(atta_type==2){
			$('#cost_input').show();
		}
		if(atta_type==3){
			$('#coin_input').show();
		}
	})
	$('#score').click(function(){
		$('#score_input').toggle(1000);
		$('#cost_input').hide(1000);
		$('#coin_input').hide(1000);
	})
	$('#cost').click(function(){
		$('#cost_input').toggle(1000);
		$('#score_input').hide(1000);
		$('#coin_input').hide(1000);
	})
	$('#free').click(function(){
		$('#cost_input').hide(1000);
		$('#score_input').hide(1000);
		$('#coin_input').hide(1000);
	})
	$('#coin').click(function(){
		$('#coin_input').toggle(1000);
		$('#score_input').hide(1000);
		$('#cost_input').hide(1000);
	})
</script>
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