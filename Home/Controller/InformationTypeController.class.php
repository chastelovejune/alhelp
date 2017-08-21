<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class InformationTypeController extends HomeBaseController {
	public function index(){
		$tops = M("information_type")->where(['pid'=>0])->select();
		foreach($tops as $i=>$t){
			$t['types'] = M("information_type")->where(['pid'=>$t[id]])->select(); 
			$tops[$i]=$t;
		}
		$this->showTrueJson($tops);
	}
}