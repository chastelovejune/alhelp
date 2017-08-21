<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class AuditionController extends HomeBaseController {
	public function add($service_id){
		$this->mustLogin();
		//if (IS_POST) {
			//$audition = $_POST;
			$audition['member_id']= session("id");
			$audition['service_id'] = $service_id;
			$re=M("audition")->where($audition)->find();
		if($re){
			$this->showFalseJson("你已经试听过了");
			exit;
		}
			$audition['add_time'] = add_time();
			M()->startTrans();
			$audition_id = M("audition")->add($audition);

			$service = M("service")->find($service_id);
			$m = $this->m;
			//$c1 = "你已对{$service['member_name']}的{$service['description']}申请了试听";
		//	M("message")->add(['type'=>1,'role'=>0,'to_member_id'=>session("id"),'object_id'=>$audition_id,'content'=>$c1]);
			M("message")->add(['type'=>1,'role'=>1,'to_member_id'=>$service['member_id'],'object_id'=>$service_id,'content'=>"'{$m['nickname']}'已对你的【{$service['description']}】申请了试听"]);
			//对老师发送短信
		if(!empty($mb['phone'])){
		$mb=M('mb_member')->find($service['member_id']);
		require "../lib/Sms.class.php";
		$sms = new \Sms();
		$sms->sendToServer($mb['phone'],"'{$m['nickname']}'已对你的【{$service['description']}申请了试听,登录www.alhelp.net查看详情");}
			M()->commit();
			$this->showTrueJson($audition_id);
	//	}
		//$service = M("service")->find($id);
		//$service = parse_class($service);
		//$this->service = $service;
		//$this->display();
	}
}