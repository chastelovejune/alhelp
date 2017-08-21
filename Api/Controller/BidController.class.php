<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class BidController extends ApiBaseController {

    //投标
	public function add(){
		if (M("bid")->where(['demand_id'=>I("request.demand_id"),'member_id'=>I("request.id")])->find()) {
			     $msg = array(
			 'status' => '你已经投过标了'   
		   );
				$this->success($msg);
			}else{
	    $data['demand_id'] = I("request.demand_id");
		$data['member_id'] = I("request.id");
		$data['service_id'] = I("request.service_id");
		$data['qq'] = I("request.qq");
		$data['phone'] = I("request.phone");
		$data['bid_price'] = I("request.price");
		$data['description'] = I("request.content");
		$res = M("bid")->add($data);
		if($res){
			$demand = M("demand")->find(I("request.demand_id"));
			$m = M("mb_member")->find(I("request.id"));
			M("message")->add(['type'=>5,'to_member_id'=>I("request.id"),'object_id'=>$res,'content'=>'你对'.$demand['member_name'].'的【'.$demand['description'].'】发送了投标申请','role'=>1]);
			M("message")->add(['type'=>5,'to_member_id'=>$demand['member_id'],'object_id'=>$res,'content'=>$m['nickname'].'对你的【'.$demand['description'].'】发送了投标申请','role'=>0]);
			M()->commit();
			//发送短信给学生
				$ms = M("mb_member")->field("phone")->find($demand['member_id']);
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好！'.$m['nickname'].'对您的【'.$demand['description'].'】发送了投标申请！登录www.alhelp.net查看详情。');
		     }
			$msg = array(
			 'status' => '1'   
		   );
		   $this->success($msg);
		}else{
		 $msg = array(
			 'status' => '投标失败，请重试'   
		   );
		   $this->success($msg);
		}	
			}
	}
}