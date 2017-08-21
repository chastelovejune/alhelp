<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class ServiceOrderController extends HomeBaseController {
	public function show($id){
		$order = M("service_order")->find($id);
		$this->showTrueJson($order);
	}
	public function ok($id){
		$o = D("ServiceOrder")->relation(true)->find($id);
		$this->o = $o;
		$this->display();
	}
}