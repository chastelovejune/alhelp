<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class CircleController extends ApiBaseController { 
    
	//搜索圈子-专业
	public function searchschool(){
		if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$community = $post_data['community']; 
			$term = $community['term'];
			$province = 'provinces';
			$school = 'schools';
			$colloge = 'colleges';
			$major = 'majors';
		if($term===$province){
	  	    $pid = I("request.pid") ?: 0;
		    $schools = M("school")->where(['pid'=>$pid])->field("id,title")->select();
		}else if($term===$school){
			$pid = $community['province_id'];
            $schools = M("school")->where(['pid'=>$pid])->select();
			 
		}else if($term===$colloge){
		   $school_id=$community['school_id'];
			$schools=M('school')->where("status=0 and pid=$school_id")->select();
		}else if($term===$major){
		   $college_id = $community['college_id'];
           $schools=M('school')->where("status=0 and pid=$college_id")->select();
		}
		    $this->success($schools);
		
		}
	}

   //搜索统考专业类别
	public function classifies(){
	     $class = M("unified_classify")->select();
		 $this->success($class);
	}

    //获取专业类别下的统考科目
	public function classifiesid($id){
	     $class = M("unified")->where("cid = $id")->select();
		 $this->success($class);
	}

 //加入圈子
   public function add(){
     if(IS_POST){
	   
		$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
            $circle = $post_data['circle'];
			$cir = M("circle")->where(['object_id'=>$circle['circle_id']])->field("id,circle_name")->find();

            $data['circle_id'] = $cir['id'];
		    $data['member_id'] = $circle['id'];
		    $cirs = M('member_follow_circle');
			$cirs -> create();
			$result=$cirs->add($data);

			//加入校友圈群聊
				  $m = M("mb_member")->find($circle['id']);
			      $im = new \Org\Util\Im();	
				if(M("group_manage")->where(['circle_id'=>$cir['id']])->find()){
				  $g = M("group_manage")->where(['circle_id'=>$cir['id']])->find();	      
	        	  $im->group_join($m['chat_id'],$g['group_id']);
				}else{
				  $gid = $im->group_create('19999',$cir['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $cir['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
				}
            
			if($result){
			   $msg = array(
				   'status'=> '1',				   
			   );
			   $this->success($msg);
			}else{
			    $msg = array(
				   'status'=> '0',
				   
			   );
			   $this->success($msg);
			}
	 }
   }

   //获取我所在的圈子
   public function mycircle($id){
    
	  $circle0 = M("circle_member")->where(['member_id'=>$id])->field("circle_id,'1' as type")->select();
	  $circle1 = M("member_follow_circle")->where(['member_id'=>$id])->field("circle_id,'0' as type")->select();
	  $circle_id = array_merge($circle0,$circle1);

	  foreach($circle_id as $i => $c){
	     $cir =  M("circle")->find($c['circle_id']);
		 $circle_id[$i]['id'] = $cir['id'];
         $circle_id[$i]['circle_name'] = $cir['circle_name'];
		  $circle_id[$i]['object_id'] = $cir['object_id'];
	  }
	  $this->success($circle_id);  
   }
 
   //退出圈子
   public function exitcircle(){
	   $m = M("mb_member")->field('chat_id')->find(I("request.id"));
		$im = new \Org\Util\Im();
		$g = M("group_manage")->where(['circle_id'=>I("request.circle_id")])->find();	 
		$im->group_remove($m['chat_id'],$g['group_id']);
		$result = M("member_follow_circle")->where(['circle_id'=>I("request.circle_id"),'member_id'=>I("request.id")])->delete();

        if($result){
			   $msg = array(
				   'status'=> '1',
				   'position' =>I("request.position")				   
			   );
			   $this->success($msg);
			}else{
			    $msg = array(
				   'status'=> '0',
				   
			   );
			   $this->success($msg);
			}
	 
   }

}