<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class MajorController extends HomeBaseController {
	public function tree(){
		$pid = I("request.pid") ?: 0;
		if ($pid > 13) {//是一级
			$schools = M("school")->where(["major_id"=>$pid])->getField("id,title");
			$this->showTrueJson($schools);
		}else{
			$majors = M("mj_major")->where(["pid"=>$pid])->getField("id,mj_name");
			$this->showTrueJson($majors);
		}
	}
}