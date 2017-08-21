<?php
namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;


class ImmessageController extends ApiBaseController {

	//获取未读消息
	public function messnoread($id){
		$message = M("immessage")->where(['to_id'=>$id,'is_read = 0'])->select();
	}

	//获取最近联系人
	public function messrecent($id){
       
	   $where = "select * from imessage where to_member_id = $id or from_member_id=$id group by from_member_id,to_member_id order by id desc";

$sql = "SELECT *,
			IF(to_member_id=$id,
				(select avatar from mb_member where mb_member.id=from_member_id) ,
				(select avatar from mb_member where mb_member.id=to_member_id) 
			)  as avatar
			FROM (
					select * from 
						(select * from imessage where from_member_id =$id 
						 
						) as me 
					where not exists 
						(select * from imessage where object_id=me.object_id and object_type=me.object_type and to_member_id=$id and id < me.id)
						
					union
					select * from 
						(select * from imessage where to_member_id =$id 
							 
						) as me 
					where not exists 
						(select * from imessage where object_id=me.object_id and object_type=me.object_type and from_member_id=$id and id < me.id) 
				) as t group by to_member_id+from_member_id order by id desc
					";

	  $message = M()->query($sql);	
	  foreach($message as $i => $c){
		  if($c['to_member_id']==$id){

			  $name = M("mb_member")->field("nickname")->find($c['from_member_id']);
		  
		  }else{
			  $name = M("mb_member")->field("nickname")->find($c['to_member_id']);
		  }

		  $message[$i]['nickname']=$name['nickname'];
	  }
		$data = array(
		   'list' => $message	
		);
		$this->success($data);
	}

    //获取好友
	public function friend($id){
		$follow = M("follow")->where(['follower'=>$id])->field("follow.befollowed")->select();	
		
			if(is_array($follow)&&count($follow)){
				 foreach($follow as $key => $value){
		      $followid[$key] = $value['befollowed'];
		  }
			}
		  
		  $befollowed['id'] = array('in',$followid);
		  $friend = M("mb_member")->where($befollowed)->field("id,avatar,nickname")->select();

		  $data = array(
		      'list' => $friend	  
		  );

		$this->success($data);
	}

	//群
	public function groups(){
	   $data = array(
		  'list' =>'1'   
	   );
	   $this->success($data);
	}

	//获取跟某人的聊天消息
	public function messagehistory($id,$uid){
			$where = " select * from (select * from imessage where to_member_id = $id and from_member_id = $uid   union select * from imessage where from_member_id = $id and to_member_id = $uid) as ms ORDER BY id ASC";


			$message = M()->query($where);
			
     		foreach($message as $i => $c){
				$avatar = M("mb_member")->field("avatar")->find();
	    		$message[$i]['avatar'] = $avatar['avatar'];
			}

		$data = array(
		   'list' => $message	
		);
		$this->success($data);
	}

	//发消息
	public function send(){
	    $res = M("imessage")->add($_POST);
		$msg = M("imessage")->table("imessage im,mb_member mb")->where(['from_member_id'=>I("request.from_member_id"),'to_member_id'=>I("request.to_member_id"),'im.from_member_id = mb.id'])->order('id desc')->field("im.*,mb.avatar")->find();
		$this->success($msg);
	}

}