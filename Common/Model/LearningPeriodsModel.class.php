<?php
namespace Common\Model;

class LearningPeriodsModel extends \Think\Model {
	public function fee($id){
		$fee = M("learning_periods")->join("left join contract on contract.id=contract_id")->join("left join service_order on service_order.id=order_id ")->join("left join service_package on service_package.id=package_id")->join("left join service on service.service_id=service_package.service_id")->where(["learning_periods.id"=>$id])->getField("learning_periods.num*discount*cost/10 as fee");
		return round($fee,2);
	}
}