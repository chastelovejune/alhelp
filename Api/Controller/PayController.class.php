<?php
namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;


class PayController extends ApiBaseController {

    //支付宝
	public function alipay(){
     
	  // $order_number = I("request.order_num");
	    $total = I("request.total");
	  /* $order = I("request.order");
	   $total = 0;
	   foreach($order as $i => $c){
	      $cost = M("order")->where(['id'=>$c])->field("total")->find();
		  $total = $total + $cost['total'];
	   }*/
        
		$config1 = array(

			'service' => 'mobile.securitypay.pay', //½Ó¿ÚÃû³Æ£¬¹Ì¶¨Öµ¡£

			'partner' => '2088411764673830', //ºÏ×÷ÕßÉí·ÝID

			'_input_charset' => trim(strtolower($alipay_config['input_charset'])), //²ÎÊý±àÂë×Ö·û¼¯

			//'sign_type' => $this->alipay_config['sign_type'], //Ç©Ãû·½Ê½

			'notify_url' => $alipay_config['notify_url'], //·þÎñÆ÷Òì²½Í¨ÖªÒ³ÃæÂ·¾¶

			'out_trade_no' => time(), //ÉÌ»§ÍøÕ¾Î¨Ò»¶©µ¥ºÅ

			'subject' => 'testname', //ÉÌÆ·Ãû³Æ

			'payment_type' => 1, //Ö§¸¶ÀàÐÍ

			'body' => 'describe', //ÉÌÆ·ÏêÇé

			'seller_id' => '2088411764673830', //Âô¼ÒÖ§¸¶±¦ÕËºÅ

			'total_fee' => $total, //×Ü½ð¶î		

		);

		$this->success($config1);
	}

    //微信
	public function wx(){

       //$order_number = I("request.order_num");
	   $total = I("request.total") * 100;	
	  /* foreach($order as $i => $c){
	      $cost = M("order")->where(['id'=>$c])->field("total")->find();
		  $total = $total + $cost['total'];
	   }  */

		$appid = 'wx8d1fd459ddd5f49b';$mch_id = '1284712301';$key = '6d24dfe8ec7b061c00de0c7d64aeb129';
		$sign_f = function ($values)use ($key){
			//签名步骤一：按字典序排序参数
			ksort($values);
			$string = http_build_query($values);
			$string = urldecode($string);
			//签名步骤二：在string后加入KEY
			$string = $string . "&key=".$key;
			//签名步骤三：MD5加密
			$string = md5($string);
			//签名步骤四：所有字符转为大写
			$sign = strtoupper($string);
			return $sign;
		};
		$notify = "http://".$_SERVER["HTTP_HOST"].U('/home/common/wxNotify');
		$values = ['appid'=>$appid,'attach'=>'支付测试','body'=>'body','mch_id'=>$mch_id,'nonce_str'=>time(),'notify_url'=>$notify,'out_trade_no'=>time(),'spbill_create_ip'=>'127.0.0.1','total_fee'=>$total,'trade_type'=>"APP"];
//print_r($values);
//die;
		$sign = $sign_f($values);

		$values['sign'] = $sign;
		$xml = "<xml>";
		foreach ($values as $key => $value) {
			$xml .= "<$key>$value</$key>";
		}
		$xml .= '</xml>';
  	 	
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
	
		curl_setopt($ch,CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
			E("curl出错，错误码:$error");
		}
		$obj = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		//参与签名的字段名为appId，partnerId，prepayId，nonceStr，timeStamp，package。注意：package的值格式为Sign=WXPay 
		if ($obj['return_code']=='FAIL') {
			E(print_r($obj,true));
		}
		go_log('微信app下单返回'.print_r($obj,true));
		$result = ['appid'=>$appid,'partnerid'=>$mch_id,'package'=>'Sign=WXPay','noncestr'=>time(),'timestamp'=>time(),'prepayid'=>$obj['prepay_id']];
		$sign = $sign_f($result);
		$result['sign'] = $sign;

		go_log("weipay".$result);
	   $this->success($result);
	}

	public function banlanceadd(){
		$data['balance'] = 100;
	   M("mb_member")->where(['id = 25'])->save($data);
	}

    //余额支付
	public function banlancepay(){
	   $total = I("request.total");
	   $id = I("request.id");
	   $banlance = M("mb_member")->where(['id'=>$id])->field("balance")->find();
	   if($total>$banlance['balance']){
	       $msg = array(
			   'msg' => '1', //余额不足
		   );
		   $this->success($msg);
	   
	}else{
	   $banlance['balance'] = $banlance['balance'] - $total;
       $res = M("mb_member")->where(['id'=>$id])->save($banlance);
	  
		   $msg = array(
			   'msg' => '0', //支付成功
		   );
		   $this->success($msg);
	   
	}
	}
}