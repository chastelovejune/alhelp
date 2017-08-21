<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class TeachController extends ApiBaseController {
    
	//申请试听
	public function applylis(){

    if (M("audition")->where(['service_id'=>I("request.service_id"),'member_id'=>I("request.id")])->find()) {
		  $msg = array(
			 'status' => '你已经申请过试听了'   
		   );
				$this->success($msg);
		}else{

		$data['member_id'] = I("request.id");
		$data['service_id'] = I("request.service_id");
		$data['add_time'] = I("request.time");
		$data['question'] = I("request.content");

		$res = M("audition")->add($data);
		if($res){
			$service = M("service")->find(I("request.service_id"));
			$m = M("mb_member")->find(I("request.id"));
			$c1 = "你已对{$service['member_name']}的【{$service['description']}】申请了试听";
			M("message")->add(['type'=>1,'to_member_id'=>I("request.id"),'object_id'=>$res,'content'=>$c1,'role'=>0]);
			M("message")->add(['type'=>1,'to_member_id'=>$service['member_id'],'object_id'=>$res,'content'=>"{$m['nickname']}已对你的【{$service['description']}】申请了试听",'role'=>1]);

			//发送短信给老师
			$ms = M("mb_member")->field("phone")->find($service['member_id']);
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好！'.$m['nickname'].'对您的【'.$service['description'].'】申请试听！登录www.alhelp.net查看详情。');
		     }

			M()->commit();
		  $msg = array(
			 'status' => '1' 
		  );
		  		$this->success($msg);	
		}else{
		   $msg = array(
			  'status' => '申请失败，请重试' 
		   );
		   		$this->success($msg);	
		}
	
		}
		
		}

		public function getlistenstatus($id,$uid){
		    if(M("audition")->where(['member_id'=>$uid,'service_id'=>$id])->find()){
	          $msg = array(
				   'status'=> '1',
				   
			   );
		   $this->success($msg);
	   }else{
	         $msg = array(
				   'status'=> '0',
				   
			   );
		  $this->success($msg);
	   }
		}
}