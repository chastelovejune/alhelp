<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class UnifiedClassifyController extends HomeBaseController {
	public function index(){
		$cs = M("unified_classify")->getField("id,title");
		$this->showTrueJson($cs);
	}
}