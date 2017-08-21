<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class AreaController extends HomeBaseController{
	public function index(){
		$pid = I("request.pid") ?: 0;
		$area = M("area")->where(["pid" => $pid])->select();
		$this->showTrueJson($area);
	}

	public function index1(){
		$pid = I("request.pid") ?: 0;
		while (true) {
			$a = M("area")->where(['pid'=>$pid])->getField("id,title",true);
			if (count($a) == 0) {
				break;
			}else{
				$pid = array_keys($a)[0];
				$areas[] = $a;
			}
		}

		$this->showTrueJson($areas);
	}

	public function pca($pid,$cid){
		$ps = M("area")->where(['pid'=>0])->getField("id,title",true);
		$cs = M("area")->where(['pid'=>$pid])->getField("id,title",true);
		$as = M("area")->where(['pid'=>$cid])->getField("id,title",true);
		$this->showTrueJson([$ps,$cs,$as]);
	}
}