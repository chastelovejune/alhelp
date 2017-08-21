<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en" id="body">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="renderer" content="webkit">
<title>新助邦考研平台</title>
<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/base.css" />
<link rel="stylesheet" type="text/css" href="css/common.css" />
<link rel="stylesheet" type="text/css" href="css/subpage.css" />
<link rel="stylesheet" type="text/css" href="css/alhelp.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.min.css" />
<script src="script/jquery.min.js"></script>
<script src="script/jquery.form.min.js"></script>
<script src="script/common.js"></script>
<script src="script/TouchSlide.js"></script>
<script src="script/jquery.qqFace.js"></script>
<script type="text/javascript" src="script/jquery.cookie.js"></script>
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/localization/messages_zh.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="js/datepicker/bootstrap-datetimepicker.min.js"></script>
<script src="js/datepicker/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="<?php echo U('/home/common/js',[],'js');?>"></script>
<script src="script/layer/layer.js"></script>
</head>
<body>

<header class="main_top">
    <div class="g_container">
        <button value="close" class="main_navbtn" onclick="top_onoff('main_nav','main_nav')"><i class="u_ico u_ico_bar"></i><i class="u_ico u_ico_bar"></i><i class="u_ico u_ico_bar"></i></button>
        <h1 class="logo" title="新助邦考研平台"><a href="index.html"><span class="f_dn">新助邦考研平台</span></a></h1>
        <div id="main_login" class="main_login">
          <button onclick="top_onoff('main_loginbox','main_loginbox')" value="close" class="m_imgbox"><img src="<?php echo ($m['avatar']); ?>"></button>
          <div id="main_loginbox" class="main_loginbox" data-href="<?php echo U('/home/member/isLogined');?>">
            <!--未登录时显示P标签，登录时显示ul标签，用类名f_dn隐藏-->
                 <?php if(!isset($_SESSION["id"])): ?><p class="main_loginlink ">
                  <a href="<?php echo U('/home/member/login');?>?url=<?php echo ($_SERVER[REQUEST_URI]); ?>">登录</a>|<a href="<?php echo U('/home/member/register');?>">注册</a>
              </p>
              <?php else: ?>
              <ul class="main_nav_personal">
                <li class="item"><a href="#"><i class="ico ico0" title="进入锁定的圈子" style="background-color:#13bc98"></i><span>进入锁定的圈子</span></a></li>
                <li class="item"><a href="<?php echo U('/member/message/noRead');?>"><i class="ico ico1" title="我的消息" style="background-color:#13bc98"></i><span>我的消息</span>
                <?php $unMessages = M("message")->where(['to_member_id'=>session("id"),'readed'=>0])->count(); $map['status']=0; $map['member']=session('id'); $member_post_re= M('member_post')->where($map)->count(); $mp = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select(); foreach($mp as $i => $c){ $mpids[] = $c['id']; } if(count($mpids)){ $count0 =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count(); $count1 =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count(); $mpcounts = $count0 + $count1; } $sum= (int)$message_re+ (int)$mpcounts; ?>
                <?php if(($unMessages) > "0"): ?><b class="u_tsh"><?php echo ($unMessages); ?></b><?php endif; ?>
                </a>
              <!--  <ul class="msg">
               <li><a href="<?php echo U('/member/message/noRead');?>"><img src="images/icons/ico_sys.png" style="margin-right:10px;">未读消息<?php if($unMessages): ?><i class="u_tsd" style="text-align: center"><?php echo ($unMessages); ?></i><?php endif; ?></a></li>
              <li><a href="<?php echo U('/member/message');?>"><img src="images/icons/ico_mss.png" style="margin-right:10px;">悄悄话<i class="u_tsd">0</i></a></li>

              <li><a href="<?php echo U('/member/memberPost');?>"><img src="images/icons/ico_ms.png" style="margin-right:10px;">说说消息<?php if($mpcounts): ?><i class="u_tsd"><?php echo ($mpcounts); ?></i><?php endif; ?></a></li>
              <li><a href="<?php echo U('/member/message');?>"><img src="images/icons/ico_brocast.png" style="margin-right:10px;">新助邦广播<i class="u_tsd">0</i></a></li>
                    <li><a href="<?php echo U('/member/message');?>"><img src="images/icons/ico_love.png" style="margin-right:10px;">爱慕者消息<i class="u_tsd">0</i></a></li>
					  
            </ul>-->
                </li>
                
                <li class="item"><a href="<?php echo U('/home/cart/index');?>">
                <i class="ico ico2" title="我的购物车" style="background-color:#13bc98"></i>
                <span>我的购物车</span>
                <?php $cartnum = M("cart")->where(['member_id'=>session('id')])->sum('count'); ?>
                <i id="cartnum" style="<?php if(($cartnum) > "0"): ?>display:block;<?php endif; ?>" class="u_cart"><?php echo ($cartnum); ?></i>
                </a>			
				</li>
				
             <li style="float:left; position:absolute;"><i id="end"></i></li>
                <li class="item">
				  <?php $m = M("mb_member")->where(['id'=>session('id')])->find(); ?>
                  <a href="<?php echo U('/member/center');?>">
                      <img src="<?php echo ($m['avatar']); ?>" alt="" title="个人中心" style="width:30px; height:30px; border-radius:50%;"><span>个人中心</span></a>
                  <ul>

                      <li>
                          <a href="<?php echo U('/member/center');?>">
                              <img src="/images/icons/ico_home.png" style=" margin-right:12px; margin-top:-5px;">
                              个人中心
                          </a>
                      </li>
                    <li>
                        <a href="<?php echo U('/home/member/setting');?>">
                            <img src="/images/icons/ico_ifo.png" style=" margin-right:12px; margin-top:-5px;">
                            基本设置
                        </a>
                    </li>  
                    <li id="logout"><a data-href="<?php echo U('/home/member/logout');?>" class="logout"><img src="/images/icons/ico_exit.png" style=" margin-right:12px; margin-top:-5px;">退出登录</a></li>
                </ul><?php endif; ?>
            </div>
        </div>
        <nav id="main_nav" class="main_nav">
            <ul class="main_nav_list f_clearfix">
                <li class="item mdown">
                  <a href="<?php echo U('/home/demand/lists');?>"  class="<?php if((CONTROLLER_NAME) == "Demand"): ?>hover<?php endif; ?>">找需求<i class="u_ico u_ico_cdown"></i></a>
                  <ul class="menu">
                    <li><a href="<?php echo U('/home/demand/lists?de');?>?demand_type=0" >资料</a></li>
                    <li><a href="<?php echo U('/home/demand/lists');?>?demand_type=1" >答疑</a></li>
                    <li><a href="<?php echo U('/home/demand/lists');?>?demand_type=2" >授课</a></li>
                  </ul>
                </li>
                <li class="item mdown">
                  <a href="<?php echo U('/home/service/lists');?>"   class="<?php if((CONTROLLER_NAME) == "Service"): ?>hover<?php endif; ?>">找服务<i class="u_ico u_ico_cdown"></i></a>
          <ul class="menu">
                    <li><a href="<?php echo U('/home/service/lists');?>?service_type=0" >资料</a></li>
                    <li><a href="<?php echo U('/home/service/lists');?>?service_type=1" >答疑</a></li>
                    <li><a href="<?php echo U('/home/service/lists');?>?service_type=2" >授课</a></li>
          </ul>
                </li>
                <li class="item mdown"><a href="<?php echo U("/home/OpenClass/index");?>"  class="<?php if((CONTROLLER_NAME) == "OpenClass"): ?>hover<?php endif; ?>">慕课<i class="u_ico u_ico_cdown"></i></a>
          <ul class="menu">
                    <li><a href="<?php echo U("/home/OpenClass/index");?>" >直播课</a></li>
                    <li><a href="http://xjr.alhelp.net/" >网校</a></li>
          </ul>
                </li>
                <li class="item"><a href="<?php echo U('/home/MemberPost');?>"  class="<?php if((CONTROLLER_NAME) == "MemberPost"): ?>hover<?php endif; ?>">提问交流</a></li>
                <li class="item mdown"><a href="<?php echo U('/home/circle/index');?>"   class="<?php if((CONTROLLER_NAME) == "Circle"): ?>hover<?php endif; ?>">社区<i class="u_ico u_ico_cdown"></i></a>
          <ul class="menu">
                    <li><a href="<?php echo U('/home/circle/index');?>" >圈子</a></li>
                    <li><a href="<?php echo U('/home/members/index');?>" >找人</a></li>
                 <!--   <li><a href="<?php echo U('/home/activity/index');?>" >活动</a></li>-->
                   <!-- <li><a href="<?php echo U('/home/MemberPost');?>" >提问交流</a></li>-->
          </ul>
                </li>
                <li class="item mdown"><a href="<?php echo U('/home/information/index');?>"  class="<?php if((CONTROLLER_NAME) == "Information"): ?>hover<?php endif; ?>">资讯<i class="u_ico u_ico_cdown"></i></a>
          <ul class="menu">
                    <li><a href="<?php echo U('/home/information/index');?>">下载</a></li>
                    <li><a href="<?php echo U('/home/information/index1');?>">资讯</a></li>
                    <li><a href="<?php echo U('/home/information/index2');?>">分享</a></li>
                    <li><a href="<?php echo U('/home/information/index3');?>">公告</a></li>
                    <li><a href="<?php echo U('/home/information/index4');?>">院校动态</a></li>
          </ul>
                </li>
                <li class="item"><a href="<?php echo U('/home/common/download');?>"  class="<?php if((CONTROLLER_NAME) == "Common"): ?>hover<?php endif; ?>">APP下载</a></li>
               <!-- <li class="item"><a href="<?php echo U('/home/price');?>"  class="<?php if((CONTROLLER_NAME) == "Price"): ?>hover<?php endif; ?>">奖品兑换</a></li>-->
            </ul>
        </nav>
    </div>
	<script>
	$('#logout').click(function(){
	$.post("<?php echo U('/home/member/logout');?>", function(data){
   if(data.suc){
		window.location=window.location;
   }
	});
 });
	</script>
</header>
<!--以上头部-->
<script src="script/layer/layer.js"></script>
<div class="u_bannerbar">
  <div class="g_container">
    <div class="u_fss">
    <a class="u_btn_sl" id="fullbtn" href="<?php echo U('/home/OpenClass/add');?>" style="padding-top:8px; padding-bottom:8px;">发直播课</a>   	
    </div>
  	<div class="u_ss">
  		<form>
  		<input type="text" class="u_ss_txn" name="keywords" value="<?php echo ($_GET["keywords"]); ?>">
  		<input type="submit" value="" class="u_ss_btn" name="">
  		</form>
  	</div>
  </div>
</div>

<div class="g_container mt_20">
	<div class="g_row">
	<?php $_cs = $cs; ?>
	 <!--<div class="g_cell_12 mb_20 m_box act_hot">
			<h3 class="m_title">名师课堂</h3>
			<?php $vcs = M("open_class")->join("LEFT JOIN mb_member ON member_id=mb_member.id")->join("LEFT JOIN zysc_view ON open_class.school_id = zhuanye_id"); if(CONTROLLER_NAME == 'Circle'){ $vcs =$vcs->where(['circle_id'=>I('request.id')]); } if(session("id")){ $vcs = $vcs->field("open_class.*,(SELECT apply_open_class.id FROM apply_open_class WHERE apply_open_class.open_class_id=open_class.open_class_id AND apply_open_class.member_id=".session("id").") as apply_id"); }else{ $vcs = $vcs->field("open_class.*"); } $vcs = $vcs->where(['is_v'=>1,'open_class.status'=>0])->order('open_class_id desc')->select(); ?>
			<div class="f_clearfix pd_30" id="fa_teacher">
	            <div class="slideBox">
		            <div class="bd">
			            <ul>
				            <?php if(is_array($vcs)): $i = 0; $__LIST__ = array_slice($vcs,0,4,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?><li>
				            		<a href="<?php echo U('/home/openClass/show',['id'=>$c[open_class_id]]);?>" target="_blank">
						            	<div class="act_imgbox">
						            		<?php if($c[image] != ''): ?><img src="<?php echo ($c[image]); ?>"><?php endif; ?>
						            	</div>
			            			</a>
									<div class="act_txtbox">
									  <h3><a href="<?php echo U('/home/openClass/show',['id'=>$c[open_class_id]]);?>"><?php echo ($c["description"]); ?></a></h3>
					                  <div class="act_price"><?php if($c['price'] == 0): ?>免费<?php else: ?>&yen;<?php echo ($c["price"]); endif; ?></div>
									  <ul class="clear">
									  	<li><span>时间：</span><?php echo (to_date($c["open_class_time"])); ?></li>
									  	<li><span>地点：</span><?php echo ($c["classroom"]); ?></li>
									  	<li><span>主办：</span><?php echo ($c['teacher']); ?></li>
									  </ul>
									  <div class="act_btnbox"><a href="<?php echo U('/home/OpenClass/show',["id"=>$c['open_class_id']]);?>" class="u_btn_sl">查看详情</a>
									  <?php if(isset($_SESSION["id"])): if(($c["member_id"]) != $m["id"]): if($c[apply_id]): ?><a class="u_btn_sc ml_20" style="background-color: lightgray; cursor: default;">已报名</a>
									  <?php else: ?>
									  <a data-href="<?php echo U('/home/OpenClass/apply');?>" data-id=<?php echo ($c['open_class_id']); ?> class="u_btn_sc ml_20 apply2">报名课程</a><?php endif; endif; endif; ?>
									  </div>
									</div>
					            </li><?php endforeach; endif; else: echo "" ;endif; ?>
			            </ul>
			        </div>
		            <div class="hd">
						<ul>
			            
						</ul>
					</div>
	            </div>
			</div>
			<div id="picScroll" class="curpicScroll">
				<div class="hd">
						<span class="next"><</span>
						<span class="prev">></span>
				</div>
				<div class="bd">
				<?php $_chunked_cs = array_chunk($vcs, 4); ?>
				<?php if(is_array($_chunked_cs)): foreach($_chunked_cs as $key=>$cs1): ?><ul class="m_curlist">
		  			<?php if(is_array($cs1)): foreach($cs1 as $key=>$c): ?><li class="m_cur_item">
                      <img src="<?php echo ($c[image]); ?>">
                      <div class="txt_cur">
				  	  <h4><a style="color:white;" href="<?php echo U('/home/openClass/show',['id'=>$c[open_class_id]]);?>"><?php echo ($c["description"]); ?></a></h4>
				  	  <p>地点: <?php echo ($c['classroom']); ?><br>课程时间：<?php echo substr($c['open_class_time'],0,10);?></p>
				  	  <span>
				  	  	<?php if(isset($_SESSION["id"])): if(($c["member_id"]) != $m["id"]): if($c['apply_id'] > 0): ?><span class="applyed">已报名</span>
						<?php else: ?>
					  	  <a class="u_btn_bk apply" data-href="<?php echo U('/home/OpenClass/apply');?>" data-id=<?php echo ($c['open_class_id']); ?>>报&nbsp;名</a><?php endif; endif; endif; ?>
				  	  </span>
                      </div>
					  </li><?php endforeach; endif; ?>
				</ul><?php endforeach; endif; ?>
				</div>
			</div>
	  </div>
<script type="text/javascript">
	TouchSlide({ 
		slideCell:"#picScroll",
		pnLoop:"false", // 前后按钮不循环
		effect:"left"
	});
	$(function(){
		$("a.apply2").click(function(){
			var t = $(this);
	     $.post($(this).attr("data-href"),{"open_class_id":$(this).attr("data-id")},function(d){
	     	alert("已报名成功");
	     	t.css({'background-color': 'lightgray', 'cursor': 'default'});
	     	t.html("已报名");
	     })
	     return false;
	   });
	});
</script>
<style type="text/css">
	.applyed{
		 font-size: 16px;
    height: 35px;
    margin-left: 10%;
    padding-top: 5px;
    width: 80%;
	background-color: lightgray;display: inline-block;color: white;line-height: 26px;text-align: center;
	}
</style>-->
		<?php $school_id = I("request.school_id",0); if($school_id > 0){ $f_s = M("school")->find($school_id); if($f_s['type'] == 3){ $zys = M("zysc_view")->getByYuanId($school_id); $f_area = ['id'=>$zys[area_id],'title'=>$zys[area_title]]; $f_school = ['id'=>$zys[school_id],'title'=>$zys[school_title]]; $f_yuan = ['id'=>$zys[yuan_id],'title'=>$zys[yuan_title]]; $f_ss = M("school")->where(['pid'=>$f_s[pid]])->getField('id,title,first_letter'); }elseif($f_s['type'] == 2){ $zys = M("zysc_view")->getBySchoolId($school_id); $f_area = ['id'=>$zys[area_id],'title'=>$zys[area_title]]; $f_school = ['id'=>$zys[school_id],'title'=>$zys[school_title]]; $f_ss = M("school")->where(['pid'=>$school_id])->getField('id,title,first_letter'); }else{ $zys = M("zysc_view")->getByAreaId($school_id); $f_area = ['id'=>$zys[area_id],'title'=>$zys[area_title]]; $f_ss = M("school")->where(['pid'=>$school_id])->getField('id,title,first_letter'); } }else{ $f_ss = M("school")->where(['pid'=>0])->getField('id,title,first_letter'); } $major_id = I("request.major_id",0); if($major_id > 0){ $mj = M("mj_major")->find($major_id); if($mj['mj_type'] == 0){ $mjs = M("mj_major")->where(['pid'=>$major_id])->select(); $f_area = ['id'=>$mj[id],'title'=>$mj[mj_name]]; }else{ $mjs = M("mj_major")->where(['pid'=>$mj['pid']])->select(); $f_school = ['id'=>$mj[id],'title'=>$mj[mj_name]]; $fmj = M("mj_major")->where(['id'=>$mj['pid']])->find(); $f_area = ['id'=>$fmj[id],'title'=>$fmj[mj_name]]; } }else{ $mjs = M("mj_major")->where(['pid'=>0])->select(); } $unified_id = I("request.unified_id",0); $utype = I("request.utype",0); if($unified_id > 0){ if($utype == 0){ $u = M("unified_classify")->find($unified_id); $us = M("unified")->where(['cid'=>$unified_id])->getField("id,cname as name,type"); $f_area = ['id'=>$u[id],'title'=>$u[title]]; }else{ $u = M("unified")->find($unified_id); $us = M("unified")->where(['cid'=>$u['cid']])->getField("id,cname as name,type"); $fu = M("unified_classify")->where(['id'=>$u['cid']])->find(); $f_area = ['id'=>$fu[id],'title'=>$fu[title]]; $f_school = ['id'=>$u[id],'title'=>$u[cname]]; } }else{ $us = M("unified_classify")->getField("id,title as name,type"); } ?>
<section class="g_cell_3 class_seat m_box">
	<header class="cls_header">
	  <?php if(isset($f_school)): ?><input type="button" class="active" onclick="list_filter_go_circle();"><?php else: endif; ?>
	  <div class="cls_tit on">
	  <a href="javascript:;" class="xq-nav" onclick="list_filter_changeArea(this)"><?php if(isset($f_area)): echo ($f_area["title"]); else: ?>下方选择地区<?php endif; ?></a>
	  </div>
	  <div class="cls_tit ">
	  <a href="javascript:;" class="xq-nav" onclick="list_filter_changeSchool(this)" data-id="<?php echo ($f_area["id"]); ?>"><?php if(isset($f_school)): echo ($f_school["title"]); else: ?>选择学校<?php endif; ?></a>
	  </div>
	  <div class="cls_tit ">
	  <a href="javascript:;" class="xq-nav" onclick="list_filter_changeYuan(this)" data-id="<?php echo ($f_school["id"]); ?>"><?php if(isset($f_yuan)): echo ($f_yuan["title"]); else: ?>选择院系<?php endif; ?></a>
	  </div>
	</header>
	<ul class="u_tab2"> 
	  <li id="clsTab_1" onclick="change_type('0','0')" class="u_tab2_item <?php if(($_GET[stype]) == "0"): ?>hover<?php endif; ?>">大学</li>
	  <li id="clsTab_2" onclick="change_type('0','2')" class="u_tab2_item <?php if(($_GET[stype]) == "2"): ?>hover<?php endif; ?>">学科</li>
	  <li id="clsTab_3" onclick="change_type('0','3')" class="u_tab2_item <?php if(($_GET[stype]) == "3"): ?>hover<?php endif; ?>">统考</li>
	  <li id="clsTab_4" onclick="change_type('0','4')" class="u_tab2_item <?php if(($_GET[stype]) == "4"): ?>hover<?php endif; ?>">公共课</li>
	</ul>
	<?php if(strlen($_GET[stype]) == 0 OR $_GET[stype] == 0): ?><div id="clsTab_1box" class="cls_mainct">
	  <ul class="u_tab1">
	  <?php $letters = ['ABCD','EFGH','IJKL','MNOP','QRST','UVW','XYZ']; ?>
	  	<?php if(is_array($letters)): foreach($letters as $key=>$letter): ?><li class="u_btn_sl letter"><?php echo ($letter); ?></li><?php endforeach; endif; ?>
	  </ul>
	  <div class="m_titlist">
			<div class="cls_slt cls_slt1 school">
			<?php if(is_array($f_ss)): foreach($f_ss as $key=>$s): ?><button onclick='change_school(this)' data-letter='<?php echo ($s["first_letter"]); ?>'  data-id="<?php echo ($s["id"]); ?>"><?php echo ($s["title"]); ?></button><?php endforeach; endif; ?>
			</div>
		</div>
	</div><?php endif; ?>

	<?php if($_GET[stype] == 2): ?><div id="clsTab_2box" class="cls_mainct">
		<?php  ?>
		<div class="m_titlist">
		<?php if(is_array($mjs)): foreach($mjs as $id=>$name): ?><button  data-id="<?php echo ($name["id"]); ?>"><?php echo ($name['mj_name']); ?></button><?php endforeach; endif; ?>
		</div>
	</div><?php endif; ?>

	<?php if($_GET[stype] == 3): ?><div id="clsTab_3box" class="cls_mainct">
	 	<?php  ?>
		<div class="m_titlist">
		<?php if(is_array($us)): foreach($us as $id=>$name): ?><button onclick="change_unified('<?php echo ($name['id']); ?>','<?php echo ($name['type']); ?>')"><?php echo ($name["name"]); ?></button><?php endforeach; endif; ?>
		</div>
	</div><?php endif; ?>

	<?php if($_GET[stype] == 4): ?><div id="clsTab_4box" class="cls_mainct">
		<div class="m_titlist">
		<?php $ps = M("public_subject")->getField("id,name"); ?>
		<?php if(is_array($ps)): foreach($ps as $id=>$name): ?><button data-id=<?php echo ($id); ?>><?php echo ($name); ?></button><?php endforeach; endif; ?>
		</div>
	</div><?php endif; ?>

	<form id="list_filter_form">
		<?php if(is_array($_GET)): foreach($_GET as $k=>$v): ?><input type="hidden" name="<?php echo ($k); ?>" value="<?php echo ($v); ?>"><?php endforeach; endif; ?>
		<?php if(!isset($_GET[school_id])): ?><input type="hidden" name="school_id"><?php endif; ?>
		<?php if(!isset($_GET[major_id])): ?><input type="hidden" name="major_id"><?php endif; ?>
		<?php if(!isset($_GET[public_subject_id])): ?><input type="hidden" name="public_subject_id"><?php endif; ?>
		<?php if(!isset($_GET[unified_id])): ?><input type="hidden" name="unified_id"><?php endif; ?>
		<?php if(!isset($_GET[stype])): ?><input type="hidden" name="stype"><?php endif; ?>
		<?php if(!isset($_GET[utype])): ?><input type="hidden" name="utype"><?php endif; ?>
		
	</form>
	<form id="list_major_form">
	<?php if(is_array($_GET)): foreach($_GET as $k=>$v): ?><input type="hidden" name="<?php echo ($k); ?>" value="<?php echo ($v); ?>"><?php endforeach; endif; ?>
		<?php if(!isset($_GET[major_id])): ?><input type="hidden" name="major_id"><?php endif; ?>
	</form>
</section>
<script type="text/javascript">


	function change_school(t){
		var t = $(t);
		var id = t.attr("data-id");
		$("#list_filter_form input[name=unified_id]").val("");
		$("#list_filter_form input[name=public_subject_id]").val("");
		$("#list_filter_form input[name=major_id]").val("");
		$("#list_filter_form input[name=school_id]").val(id);
        $("#list_filter_form input[name=stype]").val("0");
		$("#list_filter_form").submit();
	}

	$("li.letter").click(function(){
		$("div.school button").hide();
		var li = $(this);
		var letters = li.html();
		for (var i = 0; i < letters.length; i++) {
			var letter = letters[i].toLocaleLowerCase();
			$("div.school button[data-letter="+letter+"]").show();
		}
	})

	function change_type(id,type){
	   if(type == 0){
	     $("#list_filter_form input[name=unified_id]").val("");
		 $("#list_filter_form input[name=public_subject_id]").val("");
		 $("#list_filter_form input[name=major_id]").val("");
		 $("#list_filter_form input[name=school_id]").val(id);
		 $("#list_filter_form input[name=stype]").val(type);
		 $("#list_filter_form").submit();
	   }
	    if(type == 2){
	     $("#list_filter_form input[name=unified_id]").val("");
		 $("#list_filter_form input[name=public_subject_id]").val("");
		 $("#list_filter_form input[name=major_id]").val(id);
		 $("#list_filter_form input[name=school_id]").val("");
		 $("#list_filter_form input[name=stype]").val(type);
		 $("#list_filter_form").submit();
	   }
	    if(type == 3){
	     $("#list_filter_form input[name=unified_id]").val(id);
		 $("#list_filter_form input[name=public_subject_id]").val("");
		 $("#list_filter_form input[name=major_id]").val("");
		 $("#list_filter_form input[name=school_id]").val("");
		 $("#list_filter_form input[name=stype]").val(type);
		  $("#list_filter_form input[name=utype]").val(0);
		 $("#list_filter_form").submit();
	   }
	    if(type == 4){
	     $("#list_filter_form input[name=unified_id]").val("");
		 $("#list_filter_form input[name=public_subject_id]").val(id);
		 $("#list_filter_form input[name=major_id]").val("");
		 $("#list_filter_form input[name=school_id]").val("");
		 $("#list_filter_form input[name=stype]").val(type);
		 $("#list_filter_form").submit();
	   }
	}

	//学科
	$("#clsTab_2box button").click(function(){
		$("#list_filter_form input[name=unified_id]").val("");
		$("#list_filter_form input[name=school_id]").val("");
		$("#list_filter_form input[name=public_subject_id]").val("");
		var id = $(this).attr("data-id");
		$("#list_filter_form input[name=major_id]").val(id);
		$("#list_filter_form").submit();
	})

	//公共课
	$("#clsTab_4box button").click(function(){
		$("#list_filter_form input[name=unified_id]").val("");
		$("#list_filter_form input[name=school_id]").val("");
		$("#list_filter_form input[name=major_id]").val("");
		var id = $(this).attr("data-id");
		$("#list_filter_form input[name=public_subject_id]").val(id);
		$("#list_filter_form").submit();
	})

	//统考
	/*$("#clsTab_3box button").click(function(){
		$("#list_filter_form input[name=public_subject_id]").val("");
		$("#list_filter_form input[name=school_id]").val("");
		$("#list_filter_form input[name=major_id]").val("");
		var id = $(this).attr("data-id");
		$("#list_filter_form input[name=unified_id]").val(id);
		 $("#list_filter_form input[name=utype]").val(1);
		$("#list_filter_form").submit();
	})*/

	function change_unified(t,type){
		$("#list_filter_form input[name=public_subject_id]").val("");
		$("#list_filter_form input[name=school_id]").val("");
		$("#list_filter_form input[name=major_id]").val("");
		$("#list_filter_form input[name=unified_id]").val(t);
		 $("#list_filter_form input[name=utype]").val(type);
		$("#list_filter_form").submit();
	}

	$(document).ready(function(){
		var action= "<?php echo ACTION_NAME;?>";
		if(action=="majorCircle"){
			$("#clsTab_2box").show();
			$(".school").hide();
			$("#clsTab_1box").hide();
		}
	})
</script>
		<section class="g_cell_9 m_box itembor">
  <header class="u_tab2">
		<ul>
			<li id="spgc_1" onclick="setTab('spgc_','u_tab2_item',1,1)" class="u_tab2_item hover"><i class="ico"></i>直播课</li>
			<li class="u_tab2_item"><i class="ico"></i><a href="http://xjr.alhelp.net/" target="_blank">网校</a></li>
		</ul>
	</header>
	<ul id="spgc_1box" class="m_boxlist2 f_clearfix pd_10 mt_10 op">
	<?php if(is_array($_cs)): foreach($_cs as $key=>$c): ?><li class="m_item op_item">
			<div class="m_imgbox">
				<a href="<?php echo U('/home/openClass/show',['id'=>$c['open_class_id']]);?>"><img src="<?php echo ($c["image"]); ?>" /></a>
			</div>
			<div class="m_text">
				<h3><a href="<?php echo U('/home/openClass/show',['id'=>$c[open_class_id]]);?>">
				<i style="font-size: 15px;font-style:normal" ><?php if(mb_strlen($c['description']) > 7){ echo mb_substr($c['description'],0,6)."..."; }else{ echo $c['description']; } ?></i>
				</a></h3>
				<h4>主讲人：<?php echo substr($c['teacher'],0,9);?></h4>
				<p><?php echo substr($c['open_class_time'],0,10);?><br><?php echo ($c['classroom']); ?></p>
				<div class="f_clearfix btnlis">
				<?php if(($c["member_id"]) != $m["id"]): if($c['open_class_time'] < date('Y-m-d h:i:s',time())): ?><span class="u_btn_bk" style="background-color:grey;color:white;border: 0px">已开课</span>
						<?php else: ?>
				<?php if($c['apply_id'] > 0): ?><span class="u_btn_bk" style="background-color:green;color:white;border: 0px">已报名</span>
				<?php else: ?>
					<a class="u_btn_bk apply" data-href="<?php echo U('/home/OpenClass/apply');?>" data-id="<?php echo ($c['open_class_id']); ?>">报&nbsp;名</a><?php endif; endif; endif; ?>
				</div>
			</div>
		</li><?php endforeach; endif; ?>
	</ul>
<div class="u_fy">
               <?php echo pageHtml($count); ?>
            </div>
</section>
	</div>
</div>


<div class="hots">
<div class="g_container">
<div class="g_row">
 <section class="g_cell_12 hot_ds mb_20">
      <header class="u_tab2">
        <ul class="f_clearfix">
          <li id="dshot1" onmouseover="setTab('dshot','u_tab2_item',1,2)" class="u_tab2_item hover" style="background:none;">名师推荐</li>
        </ul>
      </header>
      <ul id="dshot1box" class="m_hotliao m_imglistx">
      <?php if(session("id")){ $ts = M()->query("SELECT id,nickname,sign,avatar,(SELECT count(*) FROM follow WHERE follow.befollowed = mb_member.id AND follower=".session("id").") as is_followd FROM mb_member WHERE recommend_as_teacher = 1"); }else{ $ts = M("mb_member")->where(["recommend_as_teacher"=>1])->field("id,nickname,sign,avatar")->select(); } ?>
      	<?php if(is_array($ts)): $i = 0; $__LIST__ = $ts;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?><li class="m_item">
			  	<div class="m_imgbox pointer"><img src="<?php echo ($t['avatar']); ?>"></div>
			  	<h4><?php echo ($t['nickname']); ?><span><?php echo ($t['sign']); ?></span>
			  	<?php if($t['is_followd'] == 0): ?><a href="javascript:follow(<?php echo ($t['id']); ?>)" class="u_btn_sl f<?php echo ($t['id']); ?>">+关注</a>
			  	<?php else: ?>
			  	<a href="javascript:unfollow(<?php echo ($t['id']); ?>)" class="u_btn_sl f<?php echo ($t['id']); ?>">-取消关注</a><?php endif; ?>
			  	</h4>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
    </section>
</div>
</div>
</div>

<script type="text/javascript">
	function follow(id){
		var a = $('a.f'+id);
		a.html('-取消关注');
		a.attr("href","javascript:unfollow("+id+")");
		$.post("<?php echo U('/home/member/follow');?>",{member_id:id},function(d){

		})
	}
	function unfollow(id){
		var a = $('a.f'+id);
		a.html('+关注');
		a.attr("href","javascript:follow("+id+")");
		$.post("<?php echo U('/home/member/unfollow');?>",{member_id:id},function(d){
		})
	}
</script>



<footer class="main_footer">
    <div class="g_container" style="padding-top:35px; padding-bottom:35px;">
        <div class="g_row">
        
            <div class="footer1">
            <img src="images/icons/logo.png" style=" margin-right:10px;">
          
            </div>
            <div class="footer1">
            <div class="main_ftnav">
            <a href="">关于我们</a>|<a href="">加入我们</a>|<a href="">意见反馈</a>|<a href="">APP下载</a>
            
            </div>
            <div class="main_fttxt" style="margin-left:10px;">
             All Rights Reserved 滇ICP备15001166号
            </div>
            </div>
            <div class="footer1 main_ftnav">
            <span>关注我们：</span><i class="sina"></i><i class="weixin"><img src="" alt="二维码"></i
            ></div>
        </div>
    </div>
    </div>
    
    <div class="line"></div>
    
    <div class="g_container" style="padding-top:20px;">
        <div class="g_row">
    
    <div class="main_fttxt">
      友情链接：<a href="">&nbsp;&nbsp;&nbsp;&nbsp;百度&nbsp;&nbsp;&nbsp;&nbsp;</a>|<a href="">&nbsp;&nbsp;&nbsp;&nbsp;百度&nbsp;&nbsp;&nbsp;&nbsp;</a>|<a href="">&nbsp;&nbsp;&nbsp;&nbsp;百度&nbsp;&nbsp;&nbsp;&nbsp;</a>
    </div>  
       
        </div>
    </div>
</footer>

<div class="float_container">
  <div class="fl_ftop">
  <!-- gt name='mid' value='0' -->
  <?php  $m = M("mb_member")->find(session('id')); $t = new \Org\Util\Im(); $msg = $t->get_list($m['chat_id']); if(count($msg)>0){ $ims = $msg[0]; }else{ $ims = array( 'obj' => ' ', 'dlgid' => ' ' ); } ?>
   <!-- <a target="_blank" href="<?php if(($im["from_member_id"]) == $m["id"]): echo U('/home/im/index',['id'=>$im[object_id],'mid'=>$im[to_member_id],'type'=>$im[object_type]]); else: echo U('/home/im/tome',['id'=>$im[object_id],'mid'=>$im[from_member_id],'type'=>$im[object_type]]); endif; ?>">-->
   <a id="rightWeiLiaoBtn" target="_blank" href="<?php echo U('/home/im/index',['id'=>$ims[obj],'dlgid'=>$ims[dlgid]]);?>">
    <span>微聊</span>
    </a>
    <!-- /gt -->
		<?php $mp = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select(); foreach($mp as $i => $c){ $mpids[] = $c['id']; } if(count($mpids)){ $count0 =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count(); $count1 =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count(); $mpcounts = $count0 + $count1; } $mp1 = M("member_post")->where(['member_id'=>session('id'),'reward > 0','status = 1'])->field("id")->select(); foreach($mp1 as $k => $v){ $mpids1[] = $v['id']; } if(count($mpids1)){ $answercount =M("answer")->where(['object_id' => ['in', $mpids1],'readed = 0'])->count(); $benefitcount =M("benefit")->where(['member_post_id' => ['in', $mpids1],'readed = 0'])->count(); $acceptcount = M("message")->where(['object_id'=>['in', $mpids1],'type = 11','readed = 0'])->count(); $quizcount = $answercount + $benefitcount + $acceptcount; } $counts = $mpcounts + $quizcount; ?>
	<?php if(($mpcounts != 0 AND $quizcount != 0)): ?><a href="<?php echo U('/member/MemberPost');?>"><?php if(($counts) > "0"): ?><i class="u_tsd"><?php echo ($counts); ?></i><?php endif; ?><span>提问交流</span></a>
	<?php else: ?>
	   <?php if($mpcounts != 0): ?><a href="<?php echo U('/member/MemberPost');?>"><?php if(($counts) > "0"): ?><i class="u_tsd"><?php echo ($counts); ?></i><?php endif; ?><span>提问交流</span></a>
	   <?php else: ?>
	   <a href="<?php echo U('/member/Quiz');?>"><?php if(($counts) > "0"): ?><i class="u_tsd"><?php echo ($counts); ?></i><?php endif; ?><span>提问交流</span></a><?php endif; endif; ?>

    <a href="#"><i class="u_ico_flW"></i></a>
    <a href="#"><i class="u_ico_flT"></i></a>
    <a href="javascript:void(0);"><i class="u_ico_fl2"></i><img src="images/test/90.jpg" alt=""></a>

  </div>
</div>

<div class="mtipsbg disn" id="mtip">
   <p>完善教育信息，可以快速进入校园圈找到匹配的同学啦！</p>
   <div class="mtipbtn"><a  href="<?php echo U('/home/member/setting');?>" class="u_btn_bl">现在就去</a><input type="button" class="u_btn_bl" value="以后再说"  onClick="gbBzBox('mtip')"></div>
</div>
<script language="javascript">
	//检查有没有我的最新消息
	$(function(){
		if($('#rightWeiLiaoBtn').length > 0){
			$.get('<?php echo U("home/im/checkme");?>', {}, function(data){
				if(data!='' && !isNaN(data)){
					$('#rightWeiLiaoBtn').append('<i style="position: absolute;top: -15px;right: -10px;width: 25px;height: 25px;background: #D84C29;border-radius: 50%;font-size: 10px;font-style: normal;line-height: 26px;">'+data+'</i>');
				}else{
					$('#rightWeiLiaoBtn i').remove();	
				}
			});	
		}
	});
</script>
</body>
</html>