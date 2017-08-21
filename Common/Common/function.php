<?php
function array_count($a){
    if (is_array($a)) {
        return count($a);
    }
    return 0;
}
function merge_url($key,$value){
    $url = http_build_query(array_merge($_GET,[$key=>$value]));
    return "?".$url; 
}
function to_date($str){
    return substr($str,0,10);
}
function to_minute($str){
    return substr($str,0,16);
}
function parse_emotion($content){
    $pattern = '(\[em_(\d+)\])';
    $result = preg_replace_callback($pattern, function($matchs){
      return "<img src='". "/arclist/".$matchs[1].".gif"."'/>";
    }, $content);
    return $result;
}
 
function send_mail($to = '', $subject = '', $body = '', $attachment = null){
    $re=import('Org.PHPMailer.phpmailer');//从PHPMailer目录导入phpmailer.class.php类文件
    $mail = new PHPMailer(); //实例化PHPMailer
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    //$mail->SMTPDebug = true ; // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能

    $mail->SMTPSecure = ''; // 使用安全协议
//     $mail->Host = 'smtp.163.com';//C('MAIL_SMTP_HOST'); // SMTP 服务器
//     $mail->Port = '25';//C('MAIL_SMTP_PORT'); // SMTP服务器的端口号
//     $mail->Username = 'xinzhubang@vip.163.com';//C('MAIL_SMTP_USER'); // SMTP服务器用户名
//     $mail->Password = '15001276577';//C('MAIL_SMTP_PASS'); // SMTP服务器密码
    
    $mail->Host = 'smtp.126.com';//C('MAIL_SMTP_HOST'); // SMTP 服务器
    $mail->Port = '25';//C('MAIL_SMTP_PORT'); // SMTP服务器的端口号
    $mail->Username = 'ldh8210@126.com';//C('MAIL_SMTP_USER'); // SMTP服务器用户名
    $mail->Password = '111111';//C('MAIL_SMTP_PASS'); // SMTP服务器密码

    $mail->SetFrom($mail->Username, C('waketitle'));

    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body); //解析
    $mail->AddAddress($to);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    return $mail->Send() ? true : $mail->ErrorInfo; //返回错误信息
}


function upload($name = null,$suc = null){
	if (!file_exists('../Public/Uploads/')) {
		mkdir('../Public/Uploads/');
	}
	$upload = new \Think\Upload();// 实例化上传类
    $upload->rootPath  =     '../Public/Uploads/'; // 设置附件上传根目录
    // 上传文件 
    $info   =   $upload->upload();
    if(!$info) {// 上传错误提示错误信息
    	\Think\Log::write("上传图片报错了".$upload->getError());
    }else{// 上传成功
		if ($name) {
	    	return "/Uploads/".$info[$name]["savepath"].$info[$name]["savename"];
		}else{
            foreach ($info as $k => $v) {
                $info[$k] = "/Uploads/".$info[$k]["savepath"].$info[$k]["savename"];
            }
			return $info;
		}
    }
}
function parse_class($c){
	if($c['school_id']==0 && $c["unified_id"]==0 && $c["public_subject_id"]==0){
		$c["types"][0]="发布需求或者服务时候没有选择相关信息";
	}else{
    $data = M()->query("call sup(".$c['school_id'].",".$c["unified_id"].",".$c["public_subject_id"].")");
    $types = array_values($data[0]);
    $c["types"] = $types;
    if ($c['school_id'] > 0) {
        $c['circle_id'] = M("zysc_view")->getFieldByZhuanyeId($c['school_id'],'circle_id');
    }
	}
    return $c;
}


function _subComments($c,&$all){
    $cs = M()->query("CALL get_sub_comments(".$c["id"].",".$c['member_id'].");");
    if (count($cs) > 0) {
        $all = array_merge($all,$cs);
    }
    foreach ($cs as $i => $subC) {
        _subComments($subC,$all);
    }
}

function get_comments($type,$id){
    $cs = M()->query("call get_top_comments(".$type.",".$id.");");
    foreach ($cs as $index => $c) {
        $all = [];
        _subComments($c,$all);
        $c["subCs"] = $all; 
        $cs[$index] = $c;
    }
    return $cs;
}
function add_time(){
    return date("Y-m-d H:i:s");
}
function pageHtml($all,$per=10){
    $a = intval($all/$per);
    if ($all%$per > 0) {
        $a++;
    }
    
    $urlPage = $_GET['page'] ?: 1;
    $html = "<a href='".merge_url("page",1)."'>第一页</a>";
   // $html = "<a>{$all} 条记录 {$_GET["page"]}/$a 页</a>"."<a href='".merge_url("page",1)."'>第一页</a>";
    for ($i=1; $i <= $a; $i++) {
        if ($i < $urlPage-2) {
            continue;
        }
        if ($i > $urlPage + 2) {
            continue;
        }
        if ($i == $urlPage) {
            $html .= "<a class='current'>{$i}</a>";
        }else{
            $url = merge_url("page",$i);
            $html .= "<a href='{$url}'>{$i}</a>";
        }
    }
    $html .= "<a href='".merge_url("page",$a)."'>最后一页</a>";
    return $html;
}
//最新公告
function topGongGao(){
    $ggs = M("information")->where(['type'=>2,'type2'=>0])->select();
    return $ggs;
}
function go_alipay($returnUrl,$out_trade_no,$subject,$fee){
    if ($_SERVER["HTTP_HOST"] != 'www.alhelp.net') {
        E("必须绑定域名,否则被挂");
    }
    require_once(APP_PATH."/Common/Conf/alipay.config.php");
    require_once("../lib/alipay/lib/alipay_submit.class.php");
    $parameter = array(
        "service"       => $alipay_config['service'],
        "partner"       => $alipay_config['partner'],
        "seller_id"  => $alipay_config['seller_id'],
        "payment_type"  => $alipay_config['payment_type'],
        // "notify_url" => "http://".$_SERVER["HTTP_HOST"].U('/member/index/alipayNotify'),
        "return_url"    => "http://".$_SERVER["HTTP_HOST"].$returnUrl,
        "anti_phishing_key"=>$alipay_config['anti_phishing_key'],
        "exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
        "out_trade_no"  => $out_trade_no,
        "subject"   => $subject,
        "total_fee" => $fee/100,
        "body"  => "本次充值金额为".I("request.balance"),
        "_input_charset"    => trim(strtolower($alipay_config['input_charset']))    
    );  
    $alipaySubmit = new \AlipaySubmit($alipay_config);
    $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
    return $html_text;
}
function validate_alipay(){
    require_once(APP_PATH."/Common/Conf/alipay.config.php");
    require_once("../lib/alipay/lib/alipay_notify.class.php");
    $alipayNotify = new \AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();
    return $verify_result;
}
function go_wxPay($trade_no,$fee,$attach){
    go_log($trade_no);
    require_once "../lib/wx/lib/WxPay.Api.php";
    $input = new \WxPayUnifiedOrder();
    $input->SetBody("我是body");
    $input->SetAttach($attach);
    $input->SetOut_trade_no($trade_no);
    $input->SetTotal_fee($fee);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("我是goods_tag");
    $notify = U('/home/common/wxNotify');
    $input->SetNotify_url("http://".$_SERVER["HTTP_HOST"].$notify);
    $input->SetTrade_type("NATIVE");
    $input->SetProduct_id("product_id");
    $result = \WxPayApi::unifiedOrder($input);
    $code_url = $result["code_url"];
    go_log($result);
    if ($result['return_code'] == 'FAIL' || $result['result_code']=='FAIL') {
        E(print_r($result,true));
    }
    return $code_url;
}
function get_pay_id($out_trade_no){
    $id = explode('_', $out_trade_no)[1];
    return $id;
}
//支付宝要求每次out_trade_no不一样
function pay_id($id){
    return time()."_".$id;
}
function go_log($data){
    if (is_array($data)) {
        \Think\Log::write(print_r($data,true));
    }else{
        \Think\Log::write($data);
    }
}
function is_android(){
    return strpos($_SERVER['HTTP_USER_AGENT'],"Android") !== false;
}
function id2name($id){
    return M("mb_member")->getFieldById($id,'nickname');
}
function id2path($id){
    return U('/home/member/profile',['id'=>$id]);
}
function xieyi(){
    return U('/home/information/show',['id'=>38]);
}
function go_assert($condition,$message){
    if (!$condition) {
        E($message ?: "断言失败");
    }
}
//消息统一函数
/*type:0系统通知','1试听','2协议','3学习管理','4评价','5投标','6支付','7仲裁','8网站广播','9留言新助邦','10匹配专业推荐
 member_id:发给谁
object_id:对应表中对象ID
content:内容
* */
function message($type,$member_id,$object_id){

}