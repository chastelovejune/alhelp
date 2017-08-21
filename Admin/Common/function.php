<?php
function menus(){
  $menu15 = [
  "管理员管理" => array("管理员信息" =>"","权限管理"=>"" ),
  "会员组管理" => array("会员组列表" =>""),
  "会员等级管理" => ["会员等级管理"=>""],
  "系统设置" => ["菜单管理"=>"","平台设置"=>"","APP设置"=>"","缓存管理"=>"","积分设置管理"=>"","金币设置管理
"=>"","推广奖励管理"=>"","协议合同范文"=>U('/admin/essay/index'),"运费模版"=>"","消息模版"=>""],
	"地区学校专业管理"=>["地区管理"=>U('/admin/area/index'),"学校学院专业管理"=>U('/admin/school/index')],
      "学科管理" => array(
      "统考专业课分类" => U('/admin/UnifiedClassify/index'),
      "统考专业" => U('/admin/unified/index'),
      "非统考学校专业课" => U('/admin/school/index'),
      "学科门类" => U('/admin/major/index'),
      "公共课类型管理" => U('/admin/PublicSubject/index')
    ),
	 " 圈子管理"=>["圈子"=>U('/admin/circle/index')],
  ];
  $menu1 = array(
    "服务管理" => ["资料" =>U('/admin/ServiceDocument/index'), "答疑"=>U('/admin/ServiceQuestion/index'),"授课"=>U('/admin/ServiceTeaching/index')],
    "需求管理" => ["资料" => U('/admin/DemandDocument/index'),"答疑"=>U('/admin/DemandQuestion/index'),"授课"=>U('/admin/DemandTeaching/index')],
    "直播课" =>["直播课列表"=>U('/admin/openClass/index')],
    "订单合同管理" => ["资料" => U('/admin/order/index')," 答疑和个性化授课" => ''],
    "仲裁管理" => ["答疑授课仲裁"=>"","图书资料仲裁"=>"",],
    "投标试听管理" => ["投标"=>U('/admin/bid/index'),"服务试听"=>U('/admin/audition/index'),'直播课试听'=>U('/admin/ApplyOpenClass/index')],
  );
  $menu11 = array(
    "用户管理" => ["会员列表" => U('/admin/member/index'),"地址管理"=>U('/admin/address/index'),"实名认证"=>U('/admin/member/realname'),"身份认证"=>U('/admin/member/identify')],
    "关注管理" => ["关注列表" => U("/admin/follow/index")],
    "会员积分管理" => ["会员金币奖励管理" => "","兑换结算记录"=>""],
    "优惠卷管理" => ["导入优惠券" => "","优惠券兑换"=>""]
  );
  $menu8 = array(
    "系统账户管理" => ["银行卡管理" => U('/admin/bankcard/index'),"提现记录"=>U('/admin/acount/index'),"充值记录"=>U('/admin/recharge/index'),"收支记录"=>U('/admin/cash/index')],
    "交易记录管理" => ["交易记录"=>U('/admin/record/index')],
  );
  $menu4 = array(
    "系统消息" => ["系统消息" => U('/admin/message/index')],
    "说说消息" => ["说说消息" => U('/admin/MemberPost/index')],
    "悄悄话管理" => ["悄悄话管理" => ""],
    "评论管理" => ["评论管理" => U('/admin/comment/index')],
    "评价管理" => ["评价管理" => U('/admin/rating/index')],
    "反馈管理" => ["反馈管理" => U('/admin/feedback/index')],
    "网站广播" => ["广播" => U('/admin/SystemMessage/index')],
    "IM管理" => ["消息列表" => U('/admin/imessage/index')],
  );
  $menu5 = array(
  	"活动管理" => ["活动管理"=>U('/admin/activity/index')],
  );
  $menu6 = array(
	  "下载" => ["分类"=>U('/admin/InformationType/index',['type'=>0]),"内容"=>U("/admin/Information/index",['type'=>0])],
	  "资讯" => ["分类"=>U('/admin/InformationType/index',['type'=>1]),"内容"=>U("/admin/Information/index",['type'=>1])],
	  "分享" => ["分享管理" => U("/admin/share/index")],
	  "公告" => ["公告分类"=>U('/admin/InformationType/index',['type'=>3]), "公告内容" =>U("/admin/Information/index",['type'=>3])],
    "我们" => ["关于我们" =>U("/admin/Information/index",['type'=>4])],
    "院校动态" => ["院校动态" =>U("/admin/Information/index",['type'=>5])],
	  "你所知道的新助邦" => ["分类"=>U('/admin/InformationType/index',['type'=>6]),"内容"=>U("/admin/Information/index",['type'=>6])],
	 // "夏季荣老师下载专区" => ["分类"=>U('/admin/InformationType/index',['type'=>2]), "公告内容" =>U("/admin/Information/index",['type'=>2])],
    "幻灯片管理" => ["轮播图" => U('/admin/lunbotu/index'),
		"广告位" => U('/admin/ad/index')],
  );
  //menu标号是为了和文档一样, 方便对着
  $menus = array(
    ["name" => "学习服务管理", "menus"=>$menu1,"url" => U('admin/ServiceDocument/index')],//
    ["name" => "系统消息管理" ,"menus" => $menu4, "url" => U('/admin/message/index')],//
    ["name"=>"活动管理","menus"=>$menu5,"url"=>U('/admin/activity/index')],//
    ["name"=>"下载资讯公告管理","menus"=>$menu6 ,"url"=> U('/admin/Information/index')],//
	["name" => "财务管理","menus"=>$menu8, "url" => U('/admin/bankcard/index')],//
    ["name" => "用户管理", "menus"=>$menu11, "url" => U('/admin/member/index')],//
  	["name" => "系统管理","url" => U("/admin/unified/index"), "menus" => $menu15],//
    );
  return $menus;
}

//去除img
function remove_img($str){
	$ex="/<img.*?>/si";
	return preg_replace($ex,'', $str); //去除img标签
}
//图片压缩 $img 图片，$type:宽->高->质量(0-100); 获得任意大小图像，不足地方拉伸，不产生变形，不留下空白
function img_resize($img,$type,$isthis=true){
	$imgip=$isthis?$_SERVER['HTTP_HOST']:"www.kankanguoye.com";
	if(!strpos($img,"ueditor/"))$rootimg="/Uploads/";
	return "http://".$_SERVER['HTTP_HOST']."/Public/php/img_cut.php?s=".$type."&p=http://".$imgip.$rootimg.$img;
}

//获取第一张图片
function oneimg($text){
	preg_match ("<img.*src=[\"](.*?)[\"].*?>",$text,$match);
  return $match[1];
}

function  returnPageSize(){
	return  10;
}

function  returnImagePageSize(){
	return  12;
}

/*时间转换函数*/
function transTime($ustime) {
	$ustime=strtotime($ustime);
	$ytime = date ( "Y-m-d H:i", $ustime );
	$rtime = date ( "Y年j月n日 H:i", $ustime );
	$htime = date ( "H:i", $ustime );
	$time = time () - $ustime;
	$todaytime = strtotime ( "today" );
	$time1 = time () - $todaytime;
	if ($time < 60) {
		$str = '刚刚';
	} else if ($time < 60 * 60) {
		$min = floor ( $time / 60 );
		$str = $min . '分钟前';
	} else if ($time < $time1) {
		$str = '今天 ' . $htime;
	} else {
		$str = $rtime;
	}
	return $str;
	}
//图片拆分
function img_array($arrayImg,$i=0){
	$img=explode(',',$arrayImg);
	if(!$img)return ;
	return "Shop/".$img[$i];
}

//图片压缩
function img_240($p,$type){
	if(!strpos($p,"ueditor/"))$rootimg="/Uploads/";
	switch($type){
		case "1":
			$s="193-193-100";//列表页图片尺寸
		break;	
		case "2":
			$s="384-246-100";//详细页大图尺寸
		break;	
		case "3":
			$s="87-90-100";//详细页推荐图尺寸
		break;	
		case "3":
			$s="362-261-100";//首页推荐图尺寸
		break;	
	}
	return "http://".$_SERVER['HTTP_HOST']."/Public/pic/index.php?s=".$s."&p=http://".$_SERVER['HTTP_HOST'].$rootimg.$p;
}
//更新字段
function upc($table ,$obj){
	$zd=M($table);
	
	$data['c']=$obj[0]['c']+1;
	if($obj[0]['ID'])$ID["ID"]=$obj[0]['ID'];
	if($obj[0]['id'])$ID["id"]=$obj[0]['id'];
	$c=$zd->where($ID)->save($data);
	return $c;
}
//判断是否是手机访问
function is_Mobile() {
	$mobile = array();
	static $mobilebrowser_list ='Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
	//note 获取手机浏览器
	if(preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
		return true;
	}else{
		if(preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}else{
			if($_GET['mobile'] === 'yes') {
				return true;
			}else{
				return false;
			}
		}
	}
}

/**
 * 文章截取 
 * @param unknown $str 字符串
 * @param number $start 开始截取位置
 * @param unknown $length 截取长度
 * @param string $charset 字符串编码
 * @param string $suffix 是否用...结尾
 * @return void|string|unknown
 */
function jq($str, $start=0, $length, $charset="utf-8", $suffix=true){
	$str=strip_tags($str);//标签剔除
	if(mb_strlen($str,'utf-8')-$start<$length|| strlen($str)-$start<$length)$suffix=false;
	if(mb_strlen($str,'utf-8')-$start<0|| strlen($str)-$start<0)return ;	
	if(function_exists("mb_substr")){
		if($suffix)
			return mb_substr($str, $start, $length, $charset)."...";
		else
			return mb_substr($str, $start, $length, $charset);
	}elseif(function_exists('iconv_substr')) {
		if($suffix)
			return iconv_substr($str,$start,$length,$charset)."...";
		else
			return iconv_substr($str,$start,$length,$charset);
	}
	$re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
	$re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
	$re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
	$re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	if($suffix) return $slice."...";
	return $slice;
}
	/**
	 *
	 * 获取主键
	 */
	 function getid()
	{
		$id =  date('YmdHis',time()). mt_rand(1,1000000);
		return($id);
	}

	function GetSize($sizeb) {
		$sizekb = $sizeb / 1024;
		$sizemb = $sizekb / 1024;
		$sizegb = $sizemb / 1024;
		$sizetb = $sizegb / 1024;
		$sizepb = $sizetb / 1024;
		if ($sizeb > 1) {$size = round($sizeb,2) . "B";}
		if ($sizekb > 1) {$size = round($sizekb,2) . "K";}
		if ($sizemb > 1) {$size = round($sizemb,2) . "M";}
		if ($sizegb > 1) {$size = round($sizegb,2) . "G";}
		if ($sizetb > 1) {$size = round($sizetb,2) . "T";}
		if ($sizepb > 1) {$size = round($sizepb,2) . "P";}
		return $size;
	}

	/**
	 *
	 * 获取数据根据表名
	 */
	function getRole(){
		$role = M("role");
		return  $role->select();
	}
	
	function getVcode($size=6){
		$code = '';
		for($i=1;$i<=$size;$i++){
			$code .= chr(rand(97,122));
		}
		return $code;
	}
	
	
	
	/**
	 *
	 * 获取数据根据表名
	 */
	function getDic($table){
		$data = M($table);
		return  $data->select();
	}

 	function getPageHtml($total=0,$pageCount,$pageNo,$pageSize){
 			$html  ="<ul class=\"pagination pull-right\">";
 			$start = 0;
 			$end   = 0;
 			if ($pageNo < $pageSize)
 			{
 				$start = 1;
 				$end = $pageSize;
 			}
 			else
 			{
 				$start = $pageNo - 2;
 				$end   = $pageNo + 2;
 			}
 			if ($end > $pageCount)
 			{
 				$end = $pageCount;
 			}
 			if ($pageNo > 1)
 			{
 				$html= $html."<li><a href=\"javascript:page(".($pageNo - 1).")\"  aria-label=\"Previous\"><span aria-hidden=\"true\">上一页</span></a></li>";
 			}
 			if ($start > 1)
 			{
 				$html = $html."<li class=\"active\"><a href=\"javascript:page(1)\">1<span class=\"sr-only\">(current)</span></a></li>";
 				$html = $html."<li class='disabled'><a href='javascript:;'>...</a></li>";
 			}
 		
 			for ($i = $start; $i <= $end; $i++)
 			{
 				if($i == $pageNo)
 				{
 					$html = $html."<li class=\"active\"><a href=\"#\">".$i."<span class=\"sr-only\">(current)</span></a></li>";
 				}
 				else{
 					$html = $html."<li><a href=\"javascript:page(".$i.")\">".$i."</a></li>";
 				}
 			}
 		
 			if($end <$pageCount){
 				if($end != $pageCount - 1){
 					$html = $html."<li class='disabled'><a href='javascript:;'>...</a></li>";
 					$html = $html."<li><a href=\"javascript:page(".$pageCount.")\">".$pageCount."</a></li>";
 				}
 				else	$html = $html."<li><a href=\"javascript:page(".$pageCount.")\">".$pageCount."</a></li>";
 				 
 			}
 			if ($pageNo < $pageCount){
 				$html = $html."<li><a href=\"javascript:page(".($pageNo+1).")\"  aria-label=\"Next\"><span aria-hidden=\"true\">下一页</span></a></li>";
 			}
	      	$html = $html."<li class=\"disabled\"><a>一共".$total."条数据</a></li></ul>";
	      return   ($html);
	}


	//模糊条件装配
	function mhcx($str,$title,$length=2){
		for($i=0;$i<mb_strlen($str,'UTF-8');$i++){
			
			$m[]='%'.jq($str,$i, $length,'UTF-8',false).'%';
			
		}
		$m[]='%'.$str.'%';
		$xg[$title]=array('like',$m,'OR');
		return $xg;
	}
	
	/*
	 * 登陆成功返回true 失败返回false
	 */
	function checkSession(){
		if(!session('id')){
			return false;
		}
		else{
			//return true;
			return session('id');
		}
	}
	
	
	
	function returnUploadHtml(){
		return "<form id=\"form1\" action=\"\" method=\"post\" enctype=\"multipart/form-data\">"
				."<div class=\"fieldset flash\" id=\"fsUploadProgress\">"
				."<span class=\"legend\">图片上传队列</span>"
				."</div>"
				."<div id=\"divStatus\" style=\"margin-bottom:5px;\">0 文件上传</div>"
				."<div>"
				."<span id=\"spanButtonPlaceHolder\"></span>"
				."<input id=\"btnCancel\" type=\"button\" value=\"取消所有文件上传\" onClick=\"swfu.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 29px;\" />"
				."</div>"
				."</form>";
	}
	
	
/**
 * select返回的数组进行整数映射转换
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @return array
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}


/**
 * 智能匹配模版接口发短信
 * apikey 为云片分配的apikey
 * text 为短信内容
 * mobile 为接受短信的手机号
 */
function send_sms($apikey, $text, $mobile){
	$url          ="http://yunpian.com/v1/sms/send.json";
	$encoded_text = urlencode("$text");
	$mobile       = urlencode("$mobile");
	$post_string  = "apikey=$apikey&text=$encoded_text&mobile=$mobile";
	return sock_post($url, $post_string);
}

function  isAllowip($ip){
	$sms_info_Model        =   M("sms_info");
	$total = $sms_info_Model->query("select count(*) as num from t_sms_info where add_time BETWEEN (UNIX_TIMESTAMP(NOW())-86440) AND NOW() and client_ip='".$ip."'");
	return $total[0]["num"];
}


function  isAllowCode($tel,$code){
	$sms_info_Model        =   M("sms_info");
	$total 				   =   $sms_info_Model->query("select count(*) as num from t_sms_info where add_time BETWEEN (UNIX_TIMESTAMP(NOW())-300) AND NOW() and code='".$code."' and tel='".$tel."'");
	return $total[0]["num"];
}


/**
 * url 为服务的url地址
 * query 为请求串
 */
function sock_post($url,$query){

	$data = "";
	$info = parse_url($url);
	$fp   = fsockopen($info["host"],80,$errno,$errstr,30);

	if(!$fp){
		return $data;
	}

	$head ="POST ".$info['path']." HTTP/1.0\r\n";
	$head.="Host: ".$info['host']."\r\n";
	$head.="Referer: http://".$info['host'].$info['path']."\r\n";
	$head.="Content-type: application/x-www-form-urlencoded\r\n";
	$head.="Content-Length: ".strlen(trim($query))."\r\n";
	$head.="\r\n";
	$head.=trim($query);

	$write  =	fputs($fp,$head);
	$header = 	"";

	while ($str = trim(fgets($fp,4096))) {
		$header.=$str;
	}
	while (!feof($fp)) {
		$data .= fgets($fp,4096);
	}
	return $data;
}

	/**
	 * 通过正则表达式获得编辑器文本
	 */
	function getImgURLFromHtml($str){
		$pattern="<[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg|.bmp|.png|.tiff|.jpeg|.GIF|.JPG|.BMP|.PNG|.TIFF|.JPEG]))['|\"].*?[/]?>";
		preg_match_all($pattern,$str,$match);
		
		return $match[1];
	}

	/**
	 * 读取远程图片并用浏览器下载(单张)
	 */
	function downloadRemoteImg($imgURL){
		/**
		 * todo
		 *curl无法初始化，待以后处理
		$ch = curl_init();
		$timeout = 30;
		$url = 'http://img.test.927tour.com/20160530/201605301159045455_1000_812.jpg';
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		//在需要用户检测的网页里需要增加下面两行
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, US_NAME.”:”.US_PWD);
		$contents = curl_exec($ch);
		curl_close($ch);
		echo $contents;
		 */
		//处理文件名
		//$imgURL = 'http://img.test.927tour.com/20160530/201605301159045455_1000_812.jpg';
		$file_name=basename($imgURL);
        //$file_type=explode('.',$imgURL);
        //$file_type=$file_type[count($file_type)-1];
		
		$contents = file_get_contents($imgURL);
		
		//如果出现中文乱码使用下面代码
		//$getcontent = iconv(”gb2312″, “utf-8″,file_get_contents($url));
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length:".$contents.count());
		Header("Content-Disposition: attachment; filename=\"" .$file_name."\"");

		echo $contents;

		fclose($contents);

	}
	
	function SysHttpRequest($url, $params, $method = 'GET', $header = array(), $multi = false){
		$opts = array(
				CURLOPT_TIMEOUT        => 30,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HTTPHEADER     => $header
		);
		/* 根据请求类型设置特定参数 */
		switch(strtoupper($method)){
			case 'GET':
				$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
				$opts[CURLOPT_USERAGENT] = '927tour.com';
				break;
			case 'POST':
				//判断是否传输文件
				$params = $multi ? $params : http_build_query($params);
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $params;
				$opts[CURLOPT_USERAGENT] = '927tour.com';
				break;
			default:
				throw new Exception('不支持的请求方式！');
		}
		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data  = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if($error) throw new Exception('请求发生错误：' . $error);
		return  $data;
	}



