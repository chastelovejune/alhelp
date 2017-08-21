<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class BidController extends HomeBaseController {
	public function add($id){
			$this->mustLogin();
		if (IS_POST) {
			if (M("bid")->where(['demand_id'=>$id,'member_id'=>session("id")])->find()) {
				$this->showFalseJson("你已经投过标了");
			}
			if (M("demand")->getFieldBydemand_id($id,"member_id") == session("id")) {
				$this->showFalseJson("不能自己投自己");
			}
			if (I("request.service_id",0) == 0) {
				$this->showFalseJson("没有选择服务");
			}
			M()->startTrans();
			$bid = $_POST;
			$bid['demand_id'] = $id;
			$bid['member_id'] = session("id");
			$bid_id=M("bid")->add($bid);

			$demand = M('demand')->find($id);
			$service = M("service")->find($_POST['service_id']);
			$m = $this->m;
			//发送信息给老师(自己)
		//	M("message")->add(['type'=>5,'role'=>1,'to_member_id'=>session("id"),'object_id'=>$bid_id,'content'=>'你对'.$demand['member_name'].'的'.$demand['description'].'发送了投标申请']);
			//发送信息给学生
			M("message")->add(['type'=>5,'role'=>0,'to_member_id'=>$demand['member_id'],'object_id'=>$demand['demand_id'],'content'=>'"'.$m['nickname'].'"老师对您的【'.$demand['description'].'】发送了投标申请，快去看看吧']);
			$mb=M('mb_member')->find($demand['member_id']);
			//对学生发送短信
			if(!empty($mb['phone'])){
			require "../lib/Sms.class.php";
			$sms = new \Sms();
			$sms->sendToServer($mb['phone'],'"'.$m['nickname'].'"对您的【'.$demand['description'].'】发送了投标申请，登录www.alhelp.net查看详情');}
			M()->commit();
			$this->showTrueJson($bid_id);
		}
		$d = M("demand")->find(I("request.id"));
		$d = parse_class($d);
		$this->d = $d;
		//数据分页
			$service=M("service");
            {
                $copy = clone $service;
                $this->count = $copy->where(['member_id'=>$this->m['id']])->count();
                unset($copy);
            }
             $ss=$service->where(['member_id'=>$this->m['id']])->page($_GET['page']?:1,5)->select();
           // $ss=$service->select();
		if($ss==null){
			$service_empty=1;
			$this->assign('service_empty',$service_empty);
		}
      //  dump($ss);
		//$service_empty=1;
		//$this->assign('service_empty',$service_empty);
		$this->assign('list',$ss);// 赋值数据集
		//$this->assign('page',$show);// 赋值分页输出
		//$this->assign('count',$count);
		$this->ss = $ss;
		$this->display();
	}
	
	
	public function buy($id)
	{
		

//		$this->display();
	}
	
}