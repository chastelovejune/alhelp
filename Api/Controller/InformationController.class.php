<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class InformationController extends ApiBaseController { 
      
	  //资讯首页
	  public function information(){

		  $group = M("information")->table('information information,mb_member mb_member')->order('id desc')->where(['type = 3','information.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,information.*")->select();

		  $information = M("information")->table('information information,mb_member mb_member')->order('id desc')->where(['type = 1','information.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,information.*")->select();

		  $notice = M("information")->table('information information,mb_member mb_member')->order('id desc')->where(['type = 2','information.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,information.*")->select();

		  $other = M("information")->table('information information,mb_member mb_member')->order('id desc')->where(['type = 1','information.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,information.*")->select();

		  $daily = M("information")->table('information information,mb_member mb_member')->order('id desc')->where(['type = 3','information.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,information.*")->select();

		  $yylive = M("open_class")->table('open_class open_class,mb_member mb_member')->order('open_class_id desc')->where(['open_class.member_id = mb_member.id'])->limit(3)->field("mb_member.avatar,open_class.open_class_id as id,open_class.description as title,open_class.add_time,open_class.school_id")->select();
		 
		  $data = array(
			  'daily'=>$daily,
			  'group' => $group,
			  'other'=>$other,
			  'information'=>$information,
			  'notice'=>$notice,
			  'yylive'=>$yylive

		  );
		  $this->success($data);

	  }

	  //资讯更多
	  public function informationall($type,$page){
		  if($type == 5){

		      $information = M("open_class")->table('open_class open_class,mb_member mb_member')->order('open_class_id desc')->where(['open_class.member_id = mb_member.id'])->field("mb_member.avatar,open_class.open_class_id as id,open_class.description as title,open_class.add_time")->select();
              $count =M("open_class")->count();
	          $count = $count - ($page-1) * 10;
		  }else{

			  $information = M("information")->table('information information,mb_member mb_member')->page($page,10)->where(['type'=>$type,'information.member_id = mb_member.id'])->field("mb_member.avatar,information.*")->select();
	          $count =M("information")->where(['type'=>$type])->count();
	          $count = $count - ($page-1) * 10;
		  }
	    
		if($count < 0){
		  $count = 0;
		}
		$data = array(
		   'list'=>$information,
		   'count'=>$count
		);
		$this -> success($data);
	  }
   
	  //资讯详情
	  public function informdetail($type,$id){
		  if($type == 5){
		      $information = M("open_class")->table('open_class open_class,mb_member mb_member')->where(['open_class.member_id = mb_member.id','open_class_id'=>$id])->field("mb_member.avatar,mb_member.nickname,open_class.open_class_id as id,open_class.description as title,open_class.add_time,open_class.praise_num,open_class.classroom as content")->find();
			  $cls = M("open_class")->where(['open_class_id'=>$id])->find();
			 
			  $information['views']= '';
			  $information['attachment_name'] = '';
		  }else{
		    $information = M("information")->where(['id'=>$id])->find();
		  }
	  
		$this -> success($information);
	  }

      //资讯点赞
	  public function infopraise($id){
		  $res = M("information")->where(['id'=>$id])->setInc('praise_num');
		  if($res){
				 $msg = array(
				   'status'=> '1',				   
			     );
	       }else{
		       $msg = array(
				   'status'=> '0',				   
			     ); 
		   }

		   $this->success($msg);

	  }

	  //考研天数
	  public function day(){
		  $date = date("Y-m-d H:i:s");
		  $startdate=strtotime($date);
         $enddate=strtotime('2017-10-21 14:00:00'); 
		 $days['day']=round(($enddate-$startdate)/3600/24) ;
		$this->success($days);
	  }

}