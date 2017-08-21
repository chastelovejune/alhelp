<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class ImController extends ApiBaseController {

	/*
	用于把之前的老用户导入进去
	从数据库取出会员数据
	将会员创建
	*/
/*	public function createmember(){
		$m = M("mb_member")->field("id,nickname")->select();
		$im = new \Org\Util\Im();
		foreach($m as $c){
			$im->member_create($c['id'],$c['nickname']);
		}
	}*/

	//创建im会员
    //@chat_id:用户聊天id
	//@nickname：用户昵称
	public function createmember($chat_id,$nickname){
	   $im = new \Org\Util\Im();
	   $im->member_create($chat_id,$nickname);
	}

	//发送消息
	//POST
	//@from:发送方
	//@to:接受方
	//@con：内容
	//@type:消息类型 1：im消息
	public function send(){
        $im = new \Org\Util\Im();
		if(IS_POST){
			$from = I("request.from");
			$to = I("request.to");
			$con = I("request.content");
			$dlgid = $im->send($from,$to,$con,"1");
			$m = M("mb_member")->where(['chat_id' => $from])->field("avatar,nickname")->find();			
			 $ms = array(
				 'obj' => $from,
				 'add_time' => date("Y-m-d H:i:s"),
				 'content' => $con,
				 'avatar' => $m['avatar'],
				 'dlgid' => $dlgid
			 );
			$this->success($ms);
		}
	}

	//获取会话id
	//@sid:发送方
	//@rid:接收方
	public function dlgid($sid,$rid){
		 $im = new \Org\Util\Im();
		 $dlgid = $im->get_dialog_id($sid,$rid);
		 $data = array(
			 'dlgid' => $dlgid
		 );
		 $this->success($data);
	}

	public function add($from,$to,$con){
        $im = new \Org\Util\Im();
        $im->send($from,$to,$con,"1");
		$this->success('ok');
	}

	//获取两人对话
	//@mid：用户id
	//@id：会话id
	public function msread($mid,$id){
		$im = new \Org\Util\Im();
		$ms = $im->read($mid,$id);
		$ms['content']=$ms['msg'];
		$ms['add_time']=$ms['time'];
		$m = M("mb_member")->field('avatar')->find($dlga['obj']);
		$ms['avatar'] = $m['avatar'];
	   $data = array(
		   'list' => $ms
	   );
		$this->success($data);
	}

	//获取最近联系人
	//@mid：用户id
	public function msrecent($mid){
		$im = new \Org\Util\Im();
		$msg = $im->get_list($mid);
		$remsg = array();
		foreach($msg as $i => $dlga)
       {	      
	    	$dlgid=$dlga['dlgid'];
			$dialog=$im->read($mid,$dlgid);
			$msg[$i]['content']=html_entity_decode($dialog['msg'], ENT_QUOTES, 'UTF-8');
			$msg[$i]['add_time']=$dialog['time'];
			if(is_numeric($dlga['obj'])){
			   $m = M("mb_member")->field('avatar')->where(['chat_id'=>$dlga['obj']])->find();
			   $msg[$i]['avatar'] = $m['avatar'];	
			   $remsg[] = $msg[$i];
			}else{
			  if($res = $im->in_grouped($mid,$dlga['obj']) == 1){
			    $c = M("group_manage")->field("circle_id")->where(['group_id'=>$dlga['obj']])->find();
			    $m = M("circle")->field("logo as avatar")->find($c['circle_id']);
			    $msg[$i]['avatar'] = $m['avatar'];	
			    $remsg[] = $msg[$i];
			  }
			}
				
       }
	   $data = array(
		   'list' => $remsg
	   );
		$this->success($data);
	}

	/*
	获取未读消息
	@mid：用户id
	*/
	public function msnew($mid){
       $im = new \Org\Util\Im();
	   $msg = $im->get_new_list($mid);	
	   foreach($msg as $i => $dlga)
       {	if($dlga['obj']){
	    	$dlgid=$dlga['dlgid'];
			$dialog=$im->read($mid,$dlgid);
			$msg[$i]['content']=html_entity_decode($dialog['msg'], ENT_QUOTES, 'UTF-8');
			$msg[$i]['add_time']=$dialog['time'];
			$m = M("mb_member")->where(['chat_id'=>$dialog['sender']['id']])->field('avatar')->find();
			$msg[$i]['avatar'] = $m['avatar'];			
	      }
       }
	   $data = array(
		   'list' => $msg
	   );
		$this->success($data);
	}

	/*
	创建群
	@mid:创建人id
	@gname:群名称
	*/
	public function groupcreate(){
		$im = new \Org\Util\Im();
		if(IS_POST){
		$gid = $im->group_create(I("request.mid"),I("request.name"));
		$msg = array(
			'group_id'=>$gid
		);
		$this->success($msg);
		}
	}

	/*是否在群里*/
	public function beingroup($mid,$gid){
	   $im = new \Org\Util\Im();
	   $res = $im->in_grouped($mid,$gid);
	   $this->success($res);
	}

	/*
	添加群成员
	@mid:成员id
	@gid:群id
	*/
	public function groupjoin($gid){
		$im = new \Org\Util\Im();
		$members = I("request.member_ids");
		foreach($members as $m){
		  $im->group_join($m,$gid);
		}
		$msg = array(
			'status'=>1
		);
		$this->success($msg);
	}

	/*
	移出群成员
	@mid:成员id
	@gid:群id
	*/
	public function groupremove($mid,$gid){
		$im = new \Org\Util\Im();
		$im->group_remove($mid,$gid);
		$msg = array(
			'status'=>1
		);
		$this->success($msg);
	}

	/*
	加载用户的群
	@mid:会员id
	*/
	public function groups($mid){
		$im = new \Org\Util\Im();
		$group = $im->groups($mid);
		$cgroup = array();
		$mgroup = array();
		foreach($group as $i => $c){
			$cir = M("group_manage")->table("group_manage gm,circle c")->where(['group_id'=>$c['id'],'gm.circle_id = c.id'])->field("gm.*,c.logo")->find();
			if($cir['is_circle'] == 1  && $cir['status'] == 1){
				$group[$i]['avatar']=$cir['logo'];
				$cgroup[] = $group[$i];
			}else if($cir['is_circle'] == 2 && $cir['status'] == 1){
				$m = M("mb_member")->where(['chat_id'=>$c['creator']['id']])->field('avatar')->find();
				$group[$i]['avatar']=$m['logo'];
                $mgroup[] = $group[$i];
			}

		}
		$data = array(
			'offical' => $cgroup,
			'private' => $mgroup
		);
		$this->success($data);		
	}



	//上传音频文件
	//@ruturn 文件地址
	public function upload(){
		$upload = new \Think\Upload();
		$upload->maxSize = 3145728;
		$upload->exts = array('amr');
	    $upload->rootPath = './';
		$upload->savePath = '/Uploads/';
		$upload->saveName = array('uniqid','');
		//$upload->autoSub = true;
		//$upload->subName = array('date','Y-m-d');
		$infos = $upload->upload();

		if(!$infos) {// 上传错误提示错误信息
        $this->error($upload->getError());
        }else{// 上传成功
			foreach ( $infos as $info ) {
				 $path[]['url'] = $info ['savepath'] . $info ['savename'];
			}
		
        $this->success($path);
        }
	}

	//获取我的好友
	public function friends(){
	    if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId']; 
			$follow = M("follow")->where(['follower'=>$user['id']])->field("follow.befollowed")->select();	
					
			if(is_array($follow)&&count($follow)){
				 foreach($follow as $key => $value){
		      $followid[$key] = $value['befollowed'];
		  } 	   
		  $befollowed['id'] = array('in',$followid);
		  $followers = M("mb_member")->where($befollowed)->field("id,avatar,nickname")->select();

		foreach ($follow as $i => $c) {
        $res = M("follow")->where(['follower'=>$c['befollowed'],'befollowed'=>$user['id']])->find();
         if($res){
		   $mul[$i] = '1';
		}else{
		    $mul[$i] = '0';
		}
      }

	   foreach ($followers as $i => $c) {
		   $followers[$i]['is_mutual'] = $mul[$i];		    
      }
			}else{
				$followers = array();
				}	

           
				$data = array(
				   'list' => $followers,
				);

		  $this->success($data);

		}
	}

	//获取群成员
	//@gid 群id
	public function groupmember($gid){
	    $im = new \Org\Util\Im();
		$members = $im -> group_member($gid);
		foreach($members as $i => $c){
			$m = M("mb_member")->where(['chat_id'=>$c['id']])->field("avatar,nickname")->find();
			$members[$i]['avatar'] = $m['avatar'];
            $members[$i]['nickname'] = $m['nickname'];
		}
		$data = array(
			'list' => $members
		);
		$this->success($data);
	}

	//获取群信息
	//@gid 群id
	public function groupcreator($gid){
       $im = new \Org\Util\Im();
	   $group = $im->group($gid);
	   
	   $this->success($group['creator']);
	}

	//解散群
	//@gid 群id
	public function groupdel($gid){
       $im = new \Org\Util\Im();
	   $res = $im->delgroup($gid);
	   $msg = array(
		   'status' => 1
	   );
	   $this->success($msg);
	}

	//退出群
	//@mid 用户id
	//@gid 群id
	public function groupexit($mid,$gid){
		$im = new \Org\Util\Im();
		go_log($mid);
		$res = $im->group_remove($mid,$gid);
        $msg = array(
		   'status' => 1
	    );
	   $this->success($msg);
	}
}