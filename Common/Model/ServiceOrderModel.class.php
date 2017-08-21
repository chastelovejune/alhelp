<?php
namespace Common\Model;

class ServiceOrderModel extends \Think\Model\RelationModel {
	protected $_link = array(
			"Cash" => self::BELONGS_TO,
		);

	public function fee($id){
		$order =M("service_order")->join("LEFT JOIN service ON service.service_id=service_order.service_id")->join("LEFT JOIN service_package ON service_package.id = service_order.package_id")->field("service_order.*,service.cost,service_package.num as package_num,discount")->where(['service_order.id'=>$id])->find();
		if ($order['num'] > 0) {
			return $order['num'] * $order['cost'];
		}else{
			return $order['package_num'] * $order['discount'] / 10.0 *$order['cost'];
		}
	}


}