<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class ApplyOpenClassController extends AdminBaseController{
	function index(){
		$as = D("ApplyOpenClass")->relation(true)->select();
		$this->as = $as;
		$this->display();
	}
}