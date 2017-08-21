<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class BlbController extends HomeBaseController {
	public function index(){
		$schools = M("school")->where(['type'=>2])->join('LEFt JOIN circle ON object_id=school.id')->field('school.*,circle_name,circle.id as circle_id');
		{
			$_c = clone $schools;
			$this->count = $_c->count();
		}
		$schools  = $schools->page(I('request.page',1),20)->select();
		$this->schools = $schools;
		$this->display();
	}
	public function zhuanye($id){
		$school = M("school")->join("LEFT JOIN school as ps ON ps.id=school.pid")->join('left join circle on circle.object_id=school.id')->field('school.title as title,ps.title as ptitle,school.id,circle.id as circle_id')->where(['school.id'=>$id])->find();
		$this->school = $school;
		$zys = M("school")->where(['pid'=>$id]);
		{
			$_c = clone $zys;
			$this->count = $_c->count();
		}
		$zys  = $zys->page(I('request.page',1),10)->select();
		$this->zys = array_chunk($zys,3);
		$this->display();
	}
	public function show($id){
		$this->display();
	}
}