<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class CashController extends HomeBaseController {
	public function show($id){
		$this->showTrueJson(M("cash")->find($id));
	}
}