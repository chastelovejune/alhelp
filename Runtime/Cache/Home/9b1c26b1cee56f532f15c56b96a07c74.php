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
<link rel="stylesheet" type="text/css" href="__CSS__/index.css" />
<script src="__SCRIPT__/index.js"></script>
<!--banner go-->
<div id="main_banner" class="f_clearfix">
    <div class="slideBox">
        <div class="bd">
            <ul>
                <?php if(is_array($lunbotu)): foreach($lunbotu as $key=>$s): ?><li>
                        <a class="pic" href="http://<?php echo ($s['link']); ?>" target="_blank"><div class="sd_img" title="<?php echo ($s['title']); ?>" style="background-image:url(<?php echo ($s["img"]); ?>);"></div></a>
                    </li><?php endforeach; endif; ?>
            </ul>
        </div>
        <div class="hd">
            <ul>
            </ul>
        </div>
    </div>
	<!--轮播图结束-->
    <div class="index_release" id="navindex">
        <a href="<?php echo U('/home/demand/add');?>" class="u_btn_sc">发布需求</a>
        <a href="<?php echo U('/home/service/add');?>" class="u_btn_sc">发布服务</a>
        <ul class="u_tab1 f_clearfix">
            <li id="ids1" onmouseover="setTab('ids', 'u_btn_sl', 1, 2)" class="u_btn_sl" style="background:none;">地区</li>
            <li id="ids2" onmouseover="setTab('ids', 'u_btn_sl', 2, 2)"  class="u_btn_sl" style="background:none;">学科门类</li>
        </ul>
        <div class="index_reclass">
            <div id="ids1box">
                <form class="school" method="post" action="<?php echo U('home/index/search');?>">
                    <div>
                        <select name="Select1" class="u_slt school1" data-href="<?php echo U('/home/school/index');?>">
                            <option>选择省份</option>
                        </select>
                    </div>
                    <div>
                        <select name="Select1_school" class="u_slt school2">
                            <option>选择学校</option>
                        </select>
                    </div>
                    <div class="serch">
                        <input type="submit" class="u_btn_sl" value="搜索" style="padding:10px 30px; font-size:18px;">
                    </div>
                </form>
            </div>
            <div id="ids2box" style="display:none;" data-href="<?php echo U('/home/major/tree');?>">
                <div class="index_xl_tab_div">
                    <select name="Select1" class="u_slt major0" style="width:48%; float:left;">
                        <option>选择学科</option>
                    </select>
                </div>
                <div class="index_xl_tab_div ">
                    <select name="Select1" class="u_slt major1" style="width:48%; float:right;">
                        <option>选择学科</option>
                    </select>
                </div>
                <div class="index_xl_tab_div ">
                    <select name="Select1" class="u_slt major2">
                        <option>选择学科</option>
                    </select>
                </div>
                <div class="serch">
                    <input type="button" class="u_btn_sl" value="搜索" style="padding:10px 30px; font-size:18px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!--banner end & index1-->
<div class="index1 pt_20">
    <section class="g_container">
        <header class="u_tab1">
            <ul class="f_clearfix">
                <li id="idc1_1" onmouseover="setTab('idc1_', 'u_btn_sl', 1, 3)" class="u_btn_sl hover">最新需求<p class="u_btn_z"></p></li>
                <li id="idc1_2" onmouseover="setTab('idc1_', 'u_btn_sl', 2, 3)" class="u_btn_sl">最新服务<p class="u_btn_z"></p></li>
                <li id="idc1_3" onmouseover="setTab('idc1_', 'u_btn_sl', 3, 3)" class="u_btn_sl">直播课<p class="u_btn_z"></p></li>
                <li class="u_btn_bl"><a class="more3">more</a></li>
            </ul>
        </header>
        <ul id="idc1_1box" class="m_boxlist">
            <?php if(is_array($latest_demands)): $i = 0; $__LIST__ = $latest_demands;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="m_item g_width_4">
                    <div class="m_imgbox">
                    <a href="<?php echo id2path($vo[member_id]);?>">
                        <!--头像显示表示-->
                        <?php $mb= M('mb_member')->field('is_v,is_realname,is_identify')->where('id='.$vo['member_id'])->find(); ?>

                        <?php if($vo['is_realname'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 105px; margin-top:2px;"src="images/icons/ico_ming.png"><?php endif; ?>
                        <?php if($vo['is_v'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 85px;  margin-top:2px;"src="/images/icons/ico_v.png"><?php endif; ?>
                        <?php if($vo['is_identify'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 65px;margin-top:2px;"src="/images/icons/ico_id.png"><?php endif; ?>
                        <img src="<?php echo ($vo["avatar"]); ?>">
                    </a>
                        <!--头像显示标识END-->


                    <i class="u_ico_hot"></i>
                    </div>
                    <div class="idc1_txt">
                        <h3>【资料】<a href="<?php echo U('demand/detail');?>?id=<?php echo ($vo["demand_id"]); ?>"><?php echo ($vo["description"]); ?></a></h3>
                        <?php $type_obj = $vo; ?>
                     <p class="idc1_line1">   <?php if(is_array($type_obj[types])): foreach($type_obj[types] as $key=>$t): if(($type_obj["school_id"]) > "0"): ?><a href="<?php echo U('/home/circle/show',['id'=>$type_obj['circle_id']]);?>"><?php echo ($t); ?></a>
    <?php else: ?>
    <a><?php echo ($t); ?></a><?php endif; endforeach; endif; ?></p>
                        <div class="idc1_line2"><a href="<?php echo U('demand/add');?>">类似需求</a><a href="<?php echo U('home/bid/add',['id'=>$vo['demand_id']]);?>" target="_blank">提供帮助</a></div>
                        <div class="idc1_line3">
                        <small><?php echo ($vo["add_time"]); ?></small>
                        <strong>&yen;<?php echo ($vo["cost"]); ?></strong>
                        <?php if(($vo["advance_payment"]) > "0"): ?><span style="font-size: 16px; color:#ff7101;">【已托付】</span><?php endif; ?>
                        </div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <ul id="idc1_2box" class="m_boxlist" style="display:none;">
                  <?php if(is_array($latest_services)): $i = 0; $__LIST__ = $latest_services;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="m_item g_width_4">
                    <div class="m_imgbox">
                    <a href="<?php echo id2path($vo[member_id]);?>">
                    <?php if($vo['image']): ?><img src="<?php echo ($vo["image"]); ?>"><?php else: ?>
                        <!--头像显示表示-->
                        <?php $mb= M('mb_member')->field('is_v,is_realname,is_identify')->where('id='.$vo['member_id'])->find(); ?>

                        <?php if($vo['is_realname'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 105px; margin-top:2px;"src="images/icons/ico_ming.png"><?php endif; ?>
                        <?php if($vo['is_v'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 85px;  margin-top:2px;"src="/images/icons/ico_v.png"><?php endif; ?>
                        <?php if($vo['is_identify'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-left: 65px;margin-top:2px;"src="/images/icons/ico_id.png"><?php endif; ?>
                        <img src="/Ucenter/images/noavatar_big.gif"/>
                    </a>
                        <!--头像显示标识END--><?php endif; ?>
                    </a>
                    <i class="u_ico_hot"></i></div>
                    <div class="idc1_txt">
                        <h3>【答疑】<a href="<?php echo U('service/detail');?>?id=<?php echo ($vo["service_id"]); ?>"><?php echo ($vo["description"]); ?></a></h3>
                   <p class="idc1_line1">     <?php if(is_array($vo["types"])): foreach($vo["types"] as $key=>$type): ?><a href=""><?php echo ($type); ?></a><?php endforeach; endif; ?> </p>
                        <div class="idc1_line2"><a href="<?php echo U('demand/service');?>">类似服务</a></div>
                        <div class="idc1_line3"><small><?php echo ($vo["add_time"]); ?></small><strong>&yen;<?php echo ($vo["cost"]); ?></strong></div>
                    </div>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <ul id="idc1_3box" class="m_boxlist" style="display:none;">
            <?php if(is_array($classes)): foreach($classes as $key=>$c): ?><li class="m_item g_width_4">
                    <div class="m_imgbox"><img src="<?php echo ($c["image"]); ?>" onerror="this.src='/Ucenter/images/noavatar_big.gif'"></div>
                    <div class="idc1_txt">
                        <h3><i class="ico"></i>&nbsp;&nbsp;<a href="<?php echo U('/home/OpenClass/show',["id"=>$c['open_class_id']]);?>"><?php echo ($c["description"]); ?></a></h3>
                        <p class="idc1_line1">
                            <?php if(is_array($c['types'])): foreach($c['types'] as $key=>$type): ?><a href=""><?php echo ($type); ?></a><?php endforeach; endif; ?>
                        </p>
                        <div class="idc1_line2"><a href="">类似直播课</a></div>
                        <div class="idc1_line3">
                            <small><?php echo substr($c["open_class_time"],0,10);?></small>
                            <strong> <?php if($c['price'] == 0): ?>免费<?php else: ?> &yen;<?php echo ($c["price"]); endif; ?> </strong>
                        </div>
                    </div>
                </li><?php endforeach; endif; ?>
        </ul>
    </section>
</div>

<!--index1 end & index2-->
<div class="index2 pt_20 pb_20">
    <section class="g_container">
        <header class="u_tab1">
            <ul class="f_clearfix">
                <li id="idc2_1" onmouseover="setTab('idc2_', 'u_btn_sl', 1, 2)" class="u_btn_sl hover">热门圈子<p class="u_btn_z"></p></li>
                <li id="idc2_2" onmouseover="setTab('idc2_', 'u_btn_sl', 2, 2)" class="u_btn_sl">收藏圈子<p class="u_btn_z"></p></li>
                <li class="u_btn_bl"><a href="<?php echo U('/home/circle');?>">more</a></li>
            </ul>
        </header>
        <?php $mps = M("member_post") ->join("LEFT JOIN circle ON circle.id=member_post.circle_id") ->join("LEFT JOIN mb_member ON mb_member.id=member_post.member_id") ->order("member_post.id DESC")->field("member_post.*,circle_name,mb_member.nickname,mb_member.avatar")->limit(3)->select(); ?>
        <ul id="idc2_1box" class="idc2_list f_clearfix">
        <?php if(is_array($mps)): foreach($mps as $key=>$mp): ?><li>
                <span class="idc2_date"><?php echo substr($mp[add_time],11,5);?></span><i class="ico"></i>
                <a class="idc2_tit cr" href="<?php echo U('/home/circle/memberPost',['id'=>$mp[circle_id]]);?>"><?php echo ($mp[circle_name]); ?></a><img class="idc2_img" src="<?php echo ($mp[avatar]); ?>" onerror="this.src='/Ucenter/images/noavatar_big.gif'">
                <p class="idc2_txt"><a href="<?php echo U('/home/member/memberPost',['id'=>$mp[member_id]]);?>"><?php echo ($mp[nickname]); ?>：</a><?php echo ($mp[content]); ?></p>
            </li><?php endforeach; endif; ?>
        </ul>
        <?php if(session('id')): $mps = M("member_post") ->join("LEFT JOIN circle ON circle.id=member_post.circle_id") ->join("LEFT JOIN mb_member ON mb_member.id=member_post.member_id") ->join("LEFT JOIN member_follow_circle on member_follow_circle.circle_id=circle.id") ->order("member_post.id DESC") ->where(['member_follow_circle.member_id'=>session('id')]) ->field("member_post.*,circle_name,mb_member.nickname,mb_member.avatar")->limit(3)->select(); ?>
        <ul id="idc2_2box" class="idc2_list f_clearfix" style="display:none;">
            <?php if(is_array($mps)): foreach($mps as $key=>$mp): ?><li>
                <span class="idc2_date"><?php echo substr($mp[add_time],11,5);?></span><i class="ico"></i>
                <a class="idc2_tit" href="<?php echo U('/home/circle/memberPost',['id'=>$mp[circle_id]]);?>"><?php echo ($mp[circle_name]); ?></a><img class="idc2_img" src="<?php echo ($mp[avatar]); ?>" onerror="this.src='/Ucenter/images/noavatar_big.gif'">
                <p class="idc2_txt"><a href="<?php echo U('/home/member/memberPost',['id'=>$mp[member_id]]);?>"><?php echo ($mp[nickname]); ?>：</a><?php echo ($mp[content]); ?></p>
            </li><?php endforeach; endif; ?>
        </ul><?php endif; ?>
    </section>
</div>

<!--index2 end & index3-->
<div class="index3 m_box">
    <section class="g_container">
        <header class="u_tab1">
            <ul class="f_clearfix">
                <li id="idc3_1" onmouseover="setTab('idc3_', 'u_btn_sl', 1, 2)" class="u_btn_sl hover">推荐老师<p class="u_btn_z"></p></li>
                <li id="idc3_2" onmouseover="setTab('idc3_', 'u_btn_sl', 2, 2)" class="u_btn_sl">报考学生<p class="u_btn_z"></p></li>
                <li class="u_btn_bl"><a href="<?php echo U('/home/Recommend/teacher');?>" class="more_recommend">more</a></li>
            </ul>
        </header>

        <ul id="idc3_1box" class="m_imglist f_clearfix">
            <?php if(is_array($teachers)): foreach($teachers as $key=>$t): ?><li class="m_item" style="margin-left:10px;">
                    <div class="m_imgbox pointeri">
                        <!--头像显示表示-->
                        <?php if($t['is_realname'] == 1): ?><img class="fx_ming" style="width:18px; height:18px; margin-top:2px;"src="images/icons/ico_ming.png"><?php endif; ?>
                        <?php if($t['is_v'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;  margin-top:2px;"src="/images/icons/ico_v.png"><?php endif; ?>
                        <?php if($t['is_identify'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-top:2px;"src="/images/icons/ico_id.png"><?php endif; ?>
                        <!--头像显示标识END-->
                        <img src="<?php echo ($t['avatar']); ?>" onerror="this.src='/Ucenter/images/noavatar_big.gif'">
                        </a>
                        <div class="hover_item">            
                        </div>
						<?php echo ($t['isfed']); ?>
                        <?php if($t['isfed']): ?><i class="u_btn_sl gz unfollow"  f-href="<?php echo U("/home/member/follow");?>" u-href="<?php echo U("/home/member/unfollow");?>" data-id="<?php echo ($t["id"]); ?>" style = "font-size:14px; right:12%;  ">取消关注</i>
                            <?php else: ?>
                            <i class="u_btn_sl gz follow" f-href="<?php echo U("/home/member/follow");?>" u-href="<?php echo U("/home/member/unfollow");?>" data-id="<?php echo ($t["id"]); ?>">关&nbsp;&nbsp;注</i><?php endif; ?>
                    </div>
                    <h4><a href="<?php echo U('/home/member/profile',['id'=>$t[id]]);?>"><?php echo ($t["nickname"]); ?></a></h4>          
                </li><?php endforeach; endif; ?> 
        </ul>

        <ul id="idc3_2box" class="m_imglist f_clearfix" style="display:none;">
            <?php if(is_array($students)): foreach($students as $key=>$s): ?><li class="m_item" style="margin-left:10px;">
                    <div class="m_imgbox pointeri">
                        <!--头像显示表示-->
                        <?php if($s['is_realname'] == 1): ?><img class="fx_ming" style="width:18px; height:18px; margin-top:2px;"src="images/icons/ico_ming.png"><?php endif; ?>
                        <?php if($s['is_v'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;  margin-top:2px;"src="/images/icons/ico_v.png"><?php endif; ?>
                        <?php if($s['is_identify'] == 1): ?><img class="fx_ming" style="width:18px; height:18px;margin-top:2px;"src="/images/icons/ico_id.png"><?php endif; ?>
                        <!--头像显示标识END-->
                        <img src="<?php echo ($s['avatar']); ?>" onerror="this.src='/Ucenter/images/noavatar_big.gif'">
                        </a>
                                                
                        <div class="hover_item">            
                        </div>
                        <?php if($s['isfed']): ?><i class="u_btn_sl gz unfollow" f-href="<?php echo U("/home/member/follow");?>" u-href="<?php echo U("/home/member/unfollow");?>" data-id="<?php echo ($s["id"]); ?>" style = "font-size:14px; right:12%;  ">取消关注</i>
                            <?php else: ?>
                            <i class="u_btn_sl gz follow" f-href="<?php echo U("/home/member/follow");?>" u-href="<?php echo U("/home/member/unfollow");?>" data-id="<?php echo ($s["id"]); ?>">关&nbsp;&nbsp;注</i><?php endif; ?>
                    </div>
                   
                    <h4><a href="<?php echo U('/home/member/profile',['id'=>$s[id]]);?>"><?php echo ($s["nickname"]); ?></a></h4>          
                </li><?php endforeach; endif; ?> 
        </ul>
    </section>
</div>

<!--index3 end & index4~6-->
<div class="g_container mt_30">
    <div class="g_row">
        <div class="g_cell_9 g_cell_max">
            <section class="indx4 m_box mb_20">
                <header class="u_tab2">
                    <ul>
                        <li id="idc4_1" onmouseover="setTab('idc4_', 'u_tab2_item', 1, 3)" class="u_tab2_item hover">下载专区</li>
                      <!-- <li id="idc4_2" onmouseover="setTab('idc4_', 'u_tab2_item', 2, 3)" class="u_tab2_item">资讯</li>
                        <li id="idc4_3" onmouseover="setTab('idc4_', 'u_tab2_item', 3, 3)" class="u_tab2_item">我们</li>-->
                        <a href="<?php echo U('/home/information');?>"> <li class="u_btn_bl">more</li></a>
                    </ul>
                </header>
                <?php $info1 = M("information")->where(['type'=>0])->order("add_time desc")->limit(3)->select(); ?>
                <div id="idc4_1box" class="m_newslist">
                    <?php if(is_array($info1)): foreach($info1 as $key=>$vo): ?><h3><a href="<?php echo U('/home/information/show',['id'=>$vo[id]]);?>"><?php echo ($vo[title]); ?></a>
                    <span>发布时间：<?php echo substr($vo[add_time],0,16);?></span></h3>
                  <i style="color:grey;font-style:normal;margin-left:15px"> <?php echo substr(strip_tags($vo[content]),0,500);?></i>
                        </br></br></br><?php endforeach; endif; ?>
                </div>
              <!--  <div id="idc4_2box" class="m_newslist" style="display:none;">
                    <h3><a href="<?php echo U('/home/information/show',['id'=>$info2[id]]);?>"><?php echo ($info2[title]); ?></a>
                    <span>发布时间：<?php echo substr($info2[add_time],0,16);?></span></h3>
                    <?php echo ($info2[content]); ?>
                </div>
                <div id="idc4_3box" class="m_newslist" style="display:none;">
                    <h3><a href="<?php echo U('/home/information/show',['id'=>$info3[id]]);?>"><?php echo ($info3[title]); ?></a>
                    <span>发布时间：<?php echo substr($info3[add_time],0,16);?></span></h3>
                    <?php echo ($info3[content]); ?>
                </div>-->
            </section>
            <section class="indx5 m_box">
                <header class="u_tab2">
                    <ul>
                        <li id="idc5_1" onmouseover="setTab('idc5_', 'u_tab2_item', 1, 3)" class="u_tab2_item hover">资 料</li>
                        <li id="idc5_2" onmouseover="setTab('idc5_', 'u_tab2_item', 2, 3)" class="u_tab2_item">视 频</li>
                        <li id="idc5_3" onmouseover="setTab('idc5_', 'u_tab2_item', 3, 3)" class="u_tab2_item">心 得</li>
                        <a href="<?php echo U('/home/information/index2');?>"><li class="u_btn_bl">more</li></a>
                    </ul>
                </header>
                <?php $tmp = M("share")->join("LEFT JOIN mb_member ON mb_member.id=share.member_id")->field("share.*,avatar")->order('add_time desc')->limit(3)->select(); foreach($tmp as $s){ $shares[$s[type]][] = $s; } ?>
                <ul id="idc5_1box" class="m_newslist">
                    <?php if(is_array($shares[0])): foreach($shares[0] as $key=>$s): ?><li class="m_newslist_item">
                        <div class="m_imgbox"><img src="<?php echo ($s[avatar]); ?>"><div class="u_ico_vcmz"><i class="u_ico_V"></i></div></div>
                        <div class="m_txtbox">
                            <h3>
                            <a href="<?php echo U('/home/share/show',['id'=>$s[id]]);?>"><?php echo ($s[title]); ?></a>
                            <span><?php echo substr($s[add_time],0,16);?></span></h3>
                            <i style="color:grey;font-style:normal;margin-left:15px">   <?php echo substr(strip_tags($s[content]),0,500);?></i>
                        </div>
                    </li><?php endforeach; endif; ?>
                </ul>
                <ul id="idc5_2box" class="m_newslist" style="display:none;">
                    <?php if(is_array($shares[1])): foreach($shares[1] as $key=>$s): ?><li class="vedio_item">               
    <img src="<?php echo ($s[avatar]); ?>"> 
    <div class="vedio_hover"></div>
    <div class="vedio_con">
    <h3><?php echo ($s[title]); ?></h3>
    <h4><?php echo ($s[content]); ?></h4>
    <a href="<?php echo U('/home/share/show',['id'=>$s[id]]);?>" class="u_btn_sl">播放</a>
    </div>                               
</li><?php endforeach; endif; ?>
                </ul>
                <ul id="idc5_3box" class="m_newslist" style="display:none;">
                    <?php if(is_array($shares[2])): foreach($shares[2] as $key=>$s): ?><li class="m_newslist_item">
                        <div class="m_imgbox"><img src="<?php echo ($s[avatar]); ?>"><div class="u_ico_vcmz"><i class="u_ico_V"></i></div></div>
                        <div class="m_txtbox">
                            <h3>
                            <a href="<?php echo U('/home/share/show',['id'=>$s[id]]);?>"><?php echo ($s[title]); ?></a>
                            <span><?php echo substr($s[add_time],0,16);?></span></h3>
                            <i style="color:grey;font-style:normal;margin-left:15px"> <?php echo substr(strip_tags($s[content]),0,500);?></i>
                        </div>
                    </li><?php endforeach; endif; ?>
                </ul>
            </section>
        </div>
        <?php $ggs = M('information')->where("type=3")->limit(7)->order('add_time desc')->select(); ?>
        <div class="g_cell_3">
            <section class="indx6 m_box mb_20">
                <h3 class="m_title">公告<a href="<?php echo U('/home/information/index3');?>" class="u_btn_bl">more</a></h3>
                <ul class="m_newsTlist f_clearfix">
                    <?php if(is_array($ggs)): foreach($ggs as $key=>$g): ?><li class="m_item"><a href="<?php echo U('/home/information/show',['id'=>$g[id]]);?>"><?php echo ($g[title]); ?></a></li><?php endforeach; endif; ?>
                </ul>
            </section>
            <?php $infos = M("information")->where("type=1")->order("id DESC")->limit(7)->order('add_time desc')->getField("id,title"); ?>
            <section class="indx6 m_box">
                <h3 class="m_title">资讯<a href="<?php echo U('/home/information/index1');?>" class="u_btn_bl">more</a></h3>
                <ul class="m_newsTlist f_clearfix">
                    <?php if(is_array($infos)): foreach($infos as $k=>$info): ?><li class="m_item"><a href="<?php echo U('/home/information/show',['id'=>$k]);?>"><?php echo ($info); ?></a></li><?php endforeach; endif; ?>
                </ul>
            </section>
        </div>
    </div>
</div>

<!--index4~6 end & index7-->
<div class="indx7 mt_30">
    <foreach class="g_container">
        <?php if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="idx7_item">
            <a href="" class="idx7tit">
                <div class="m_imgbox"><img src="<?php echo ($vo["image"]); ?>" style="width: 56px;height: 56px"></div>
                <h4><?php echo ($vo["name"]); ?></h4>
            </a>
            <p>
                <?php if(is_array($vo['info'])): $i = 0; $__LIST__ = $vo['info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$k): $mod = ($i % 2 );++$i;?><a href="<?php echo U('home/information/show',['id'=>$k['id']]);?>"><?php echo ($k["title"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
            </p>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<script type="text/javascript">
    //首页
    var mb_memeber="<?php echo session('id');?>";
    $(".follow").click(function(){
        if(mb_memeber==""){
            layer.msg("登录才能关注");
            exit;
        }
        $(this).toggleClass("follow");
        $(this).toggleClass("unfollow");
        $(this).html("取消关注");
        $.post($(this).attr("f-href"),{"member_id":$(this).attr("data-id")},function(){
        });
    });
    $(".unfollow").click(function(){
        if(mb_memeber==""){
            layer.msg("登录才能关注");
            exit;
        }
        $(this).toggleClass("follow");
        $(this).toggleClass("unfollow");
        $(this).html("关注");
        $.post($(this).attr("u-href"),{"member_id":$(this).attr("data-id")},function(){
        });
    });
    $.get($("form.school .school1").attr("data-href"),{},function(data){
        var html = "<option>选择省份</option>";
        for(var id in data.message){
            html = html + '<option style="color:white;background:#7C7D7C" value='+id+' >'+data.message[id]+'</option>';
        }
        $("form.school .school1").html(html);
    }); 
    $("form.school .school1").change(function(){
        var pid = $(this).val();
        $.get($("form.school .school1").attr("data-href"),{"pid":pid},function(data){
            var html = "";
            for(var id in data.message){
                html = html + '<option style="color:white;background:#7C7D7C" value='+id+' >'+data.message[id]+'</option>';
            }
            $("form.school .school2").html(html);
        })
    })
    $.get($("#ids2box").attr("data-href"),{},function(data){
        var html = " <option>选择学科</option>";
        for(var id in data.message){
            html = html + '<option style="color:white;background:#7C7D7C" value='+id+' >'+data.message[id]+'</option>';
        }
        $("#ids2box .major0").html(html);
    });
    $("#ids2box .major0").change(function(){
        $.get($("#ids2box").attr("data-href"),{pid:$(this).val()},function(data){
            var html = "<option>选择学科</option>";
            for(var id in data.message){
                html = html + '<option style="color:white;background:#7C7D7C" value='+id+' >'+data.message[id]+'</option>';
            }
            $("#ids2box .major1").html(html);
        });
    })
    $("#ids2box .major1").change(function(){
        $.get($("#ids2box").attr("data-href"),{pid:$(this).val()},function(data){
            var html = "";
            for(var id in data.message){
                html = html + '<option style="color:white;background:#7C7D7C" value='+id+' >'+data.message[id]+'</option>';
            }
            $("#ids2box .major2").html(html);
        });
    })
    $(".more3").click(function(){
        if(!$("#idc1_1box").is(":hidden")){
            window.location.href = "<?php echo U('/home/demand/lists');?>";
        }
        if(!$("#idc1_2box").is(":hidden")){
            window.location.href = "<?php echo U('/home/service/lists');?>";
        }
        if(!$("#idc1_3box").is(":hidden")){
            window.location.href = "<?php echo U('/home/openClass/index');?>";
        }
    })

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