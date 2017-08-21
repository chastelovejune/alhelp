<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class RechargeController extends HomeBaseController {
	public function show($id){
		$c = M("recharge")->find($id);
		$this->showTrueJson($c);
	}
	public function ok($id){
		$r = D("Recharge")->relation(true)->find($id);
		if ($r['cash_id'] == 0) {
			E("成功成功怎么可能没有cash,脏数据");
		}
		$this->r  =$r;
		$this->display();
	}
}