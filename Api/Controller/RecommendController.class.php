<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class RecommendController extends ApiBaseController {

	//系统推荐-服务
	public function recommendservice(){
	   
	   if(I("request.school_id")){
	      $where = ['school_id'=>I("request.school_id")];
	   }else if(I("request.unified_id")){
	       $where = ['unified_id'=>I("request.unified_id")];
	   }else{
	      $where = ['public_subject_id'=>I("request.public_subject_id")];
	   }

	  // $service = M("service")->limit(3)->where("service.school_id = '{$demand['school_id']}' OR service.unified_id = '{$demand['unified_id']}' OR service.public_subject_id = '{$demand['public_subject_id']}'")->select();

	 $service = M("service")->table("service service,mb_member mb_member")->limit(3)->where([$where,'service.member_id = mb_member.id','service.status = 0'])->field("service.*,mb_member.avatar")->select();

      foreach($service as $index => $d){
           $d = parse_class($d);
           $service[$index]=$d;
         }

	 $data = array(
	    'list' => $service,	 
	 );
	   $this->success($data);
	}

    //系统推荐-需求
	public function recommenddemand(){
	  if(I("request.school_id")){
	      $where = ['school_id'=>I("request.school_id")];
	   }else if(I("request.unified_id")){
	       $where = ['unified_id'=>I("request.unified_id")];
	   }else{
	      $where = ['public_subject_id'=>I("request.public_subject_id")];
	   }

	  // $service = M("service")->limit(3)->where("service.school_id = '{$demand['school_id']}' OR service.unified_id = '{$demand['unified_id']}' OR service.public_subject_id = '{$demand['public_subject_id']}'")->select();

	 $demand = M("demand")->table("demand demand,mb_member mb_member")->limit(3)->where([$where,'demand.member_id = mb_member.id','demand.status = 0'])->field("demand.*,mb_member.avatar")->select();

     foreach($demand as $index => $d){
           $d = parse_class($d);
           $demand[$index]=$d;
         }
	
	 $data = array(
	    'list' => $demand,	 
	 );
	   $this->success($data);
	}

	//类似公开课
	public function openclasssimilar($id){
		$school = M("open_class")->where(['open_class_id'=>$id])->find();
		$classid['open_class_id'] = array('neq',$id);
		$class = M("open_class")->table("open_class open_class,mb_member mb_member")->limit(3)->where(['school_id'=>$school['school_id'],$classid])->field("open_class.*,mb_member.avatar")->select();

    foreach($class as $index => $d){
           $d = parse_class($d);
           $class[$index]=$d;
         }
	
	 $data = array(
	    'list' => $class,	 
	 );
	   $this->success($data);
	}

}