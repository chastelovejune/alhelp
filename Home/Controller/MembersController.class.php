<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class MembersController extends HomeBaseController {
	
	public function index(){

		if(I("request.school_id")<2){
		    $mes= M("mb_member");
			$mes->order("id desc");
		}else{
			$sid = M("circle")->where(['object_id'=>I("request.school_id")])->find();
			if($sid){
			   $mes= M("circle_member")->table("circle_member cm,mb_member m")->where(['cm.circle_id'=>$sid['id'],'cm.member_id = m.id'])->field('m.id,m.avatar,m.nickname,m.recommend_as_teacher');
			}else{
			   $sid = M("zysc_view")->where(['zhuanye_id'=>I("request.school_id")])->find();
			   $mes= M("circle_member")->table("circle_member cm,mb_member m")->where(['cm.circle_id'=>$sid['school_id'],'cm.member_id = m.id'])->field('m.id,m.avatar,m.nickname,m.recommend_as_teacher');
			}	
			
			$mes->order("m.id desc");
		}
				
		if(I("request.type")==0){
		
		}else{
		   $mes  =$mes->where(['recommend_as_teacher'=>1]);
		}
		
		$copy = clone $mes;
		$this->count = $copy->count();
		$mes = $mes->page($_GET['page']?:1,20);
		$mes = $mes->select();	
		$this->mes = $mes;

		$medus = M("mb_education_info")->where(['member_id'=>session('id')])->select();
		$this->medus = $medus;

		$this->display();
	}

}