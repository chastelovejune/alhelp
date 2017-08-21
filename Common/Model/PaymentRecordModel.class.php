<?php
namespace Common\Model;

class PaymentRecordModel extends \Think\Model {
	public function id2name($id){
		if ($id == 0) {
			return "新助邦";
		}else{
			return M("mb_member")->getFieldById($id,'nickname');
		}
	}
	//关联太多模块, 统一入口处理
	public function goAdd($obj){
		$type = 0;
		$income_type = 0;
		$object_id=$obj['id'];
		$from_member_id = 0;
		$to_member_id = 0;
		$balance = 0;
		if (CONTROLLER_NAME == "Order" && ACTION_NAME=="receive") {
			$type = 2;
			$to_member_id = $obj['from_member_id'];
			$balance = $obj['total'];
		}

		$record = ['type'=>$type,'income_type'=>$income_type,'object_id'=>$object_id,'from_member_id'=>$from_member_id,'to_member_id'=>$to_member_id,'balance'=>$balance];
		return M("payment_record")->add($record);
	}
}