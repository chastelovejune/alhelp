<?php
namespace Home\Controller;                                                                                                                                                      
use Home\Common\HomeBaseController;
use Home\Common\Alhelp_Constant;

class SchoolController extends HomeBaseController 
{
	public function index(){
		$pid = I("request.pid") ?: 0;
		$field = I("request.field") ?: "id,title";
		$schools = M("school")->where(['pid'=>$pid])->getField($field);
		$this->showTrueJson($schools);
	}

	public function index1(){
		$pid = I("request.pid") ?: 0;
		while (true) {
			$a = M("school")->where(['pid'=>$pid])->getField("id,title",true);
			if (count($a) == 0) {
				break;
			}else{
				$pid = array_keys($a)[0];
				$schools[] = $a;
			}
		}
		$this->showTrueJson($schools);
	}

	public function id2circle($id){
		$circle = M("zysc_view")->where(['yuan_id'=>$id,'school_id'=>$id,'_logic'=>'OR'])->find();
		if ($circle) {
			$this->success($circle,U('/home/circle/show',['id'=>$circle['circle_id']]));
		}else{
			$this->error();
		}
	}
}
