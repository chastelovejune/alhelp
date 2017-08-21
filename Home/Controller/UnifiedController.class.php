<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class UnifiedController extends HomeBaseController {
	public function index(){
		$cid = I("get.cid") ?: 1;
		$us = M("unified")->where(["cid"=>$cid])->getField("id,cname");
		$this->showTrueJson($us);
	}
}