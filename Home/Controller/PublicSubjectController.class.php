<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class PublicSubjectController extends HomeBaseController {
	public function index(){
		$ps = M("public_subject")->getField("id,name");
		$this->showTrueJson($ps);
	}
}