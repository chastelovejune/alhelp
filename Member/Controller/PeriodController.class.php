<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class PeriodController extends MemberBaseController {
	var $table_name = "learning_periods";
	public function end($order_id){
		M()->startTrans();
		$contract_id = M("contract")->where(['order_id'=>$order_id,'status'=>1])->getField("id");
		M("learning_periods")->where(['contract_id' => $contract_id])->save(['status'=>2]);

		$order = M('service_order')->find($order_id);
		$service = M('service')->find($order['service_id']);
		$m = $this->m;
		M("message")->add(['type'=>3,'to_member_id'=>$m['id'],'object_id'=>$contract_id,'content'=>'教学已经终止']);
		M("message")->add(['type'=>3,'to_member_id'=>$service['member_id'],'object_id'=>$contract_id,'content'=>'教学已经终止']);
		M()->commit();
		$this->showTrueJson();
	}
}