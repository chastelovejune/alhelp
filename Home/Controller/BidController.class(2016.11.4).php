<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class BidController extends HomeBaseController {
	public function add($id){
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
			//发给老师
			M("message")->add(['type'=>1,'to_member_id'=>session("id"),'object_id'=>$bid_id,'content'=>'你对'.$demand['member_name'].'的'.$demand['description'].'发送了投标申请']);
			M("message")->add(['type'=>1,'to_member_id'=>$demand['member_id'],'object_id'=>$bid_id,'content'=>$m['nickname'].'对你的'.$demand['description'].'发送了投标申请']);
			M()->commit();
			$this->showTrueJson($bid_id);
		}
		$d = M("demand")->find(I("request.id"));
		$d = parse_class($d);
		$this->d = $d;
		$ss = M("service")->where(['member_id'=>$this->m['id']])->select();
		$this->ss = $ss;
		$this->display();
	}
	
	
	public function buy($id)
	{
		

//		$this->display();
	}
	
}