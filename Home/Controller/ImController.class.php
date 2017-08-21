<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class ImController extends HomeBaseController{
	public function _initialize(){
		parent::_initialize();
		$this->mustLogin();
		
	}
	public function _ims($id,$type){

	}
	public function _common($id,$type){
		$follows = M("follow")->join("LEFT JOIN mb_member ON mb_member.id=befollowed")->where(['follower'=>session("id")])->getField("mb_member.id,avatar,nickname");
		$fans = M("follow")->join("LEFT JOIN mb_member ON mb_member.id=follower")->where(['befollowed'=>session("id")])->getField("mb_member.id,avatar,nickname");
		$friends = array_intersect_assoc($follows,$fans);
		$this->follows = $follows;
		$this->fans = $fans;
		$this->friends = $friends;

		$this->me = $this->m;

		if ($type == 'service') {
			$service = M("service")->find($id);
			$service = parse_class($service);
			$this->service = $service;
			$this->obj = $service;
		}else if($type == 'demand'){
			$demand = M("demand")->find($id);
			$demand = parse_class($demand);
			$this->demand = $demand;
			$this->obj = $demand;
		}else{
			$open_class = M("open_class")->find($id);
			$open_class = parse_class($open_class);
			$this->open_class = $open_class;
			$this->obj = $open_class;
		}
		{
			//左边的联系人
						//我主动找别人的
			// select * from (select * from imessage where from_member_id =4 group by object_type,object_id) as me where not exists (select * from imessage where object_id=me.object_id and object_type=me.object_type and to_member_id=4 and id < me.id)
			//别人找我的 

			//select * from (select * from imessage where to_member_id =4 group by object_type,object_id) as me where not exists (select * from imessage where object_id=me.object_id and object_type=me.object_type and from_member_id=4 and id < me.id);

			$myid = session("id");
			// if (ACTION_NAME == 'index') {
			// 	M("imessage")->where(['object_id'=>$id,'object_type'=>$type])
			// }
//新版	, 我主动找别人的, union别人主动找我的
			$sql = "SELECT *,
			IF(to_member_id=$myid,
				(select avatar from mb_member where mb_member.id=from_member_id) ,
				(select avatar from mb_member where mb_member.id=to_member_id) 
			)  as avatar
			FROM (
					select * from 
						(select * from imessage where from_member_id =$myid 
							group by object_type,object_id
						) as me 
					where not exists 
						(select * from imessage where object_id=me.object_id and object_type=me.object_type and to_member_id=$myid and id < me.id)
					union
					select * from 
						(select * from imessage where to_member_id =$myid 
							group by object_type,object_id
						) as me 
					where not exists 
						(select * from imessage where object_id=me.object_id and object_type=me.object_type and from_member_id=$myid and id < me.id)
				) as t ORDER BY object_type='$type' DESC,object_id=$id DESC 
					";
			$contracts = M()->query($sql);
// print_r($contracts);die;
// 			$c1 = M("imessage")->group("object_type,from_member_id,object_id")->field("*,count(*) as num,IF(to_member_id=$mid,(select avatar from mb_member where mb_member.id=from_member_id) ,(select avatar from mb_member where mb_member.id=to_member_id) )  as avatar");
// 			$c2 = clone $c1;
// 			$sub1 = $c1->where(['to_member_id'=>session("id")])->buildSql();
// 			$sub2 = $c2->where(['from_member_id'=>session("id")])->buildSql();
// 			$contracts = M()->query("$sub1 union $sub2");
			foreach ($contracts as $key => $c) {
				if ($c['object_type'] == 'service') {
					$service = M("service")->find($c['object_id']);
					$c['title'] = $service['description'];
					$c['nickname'] = $service['member_name'];
				}else if($c['object_type'] == 'demand'){
					$demand = M("demand")->find($c['object_id']);
					$c['title'] = $demand['description'];
					$c['nickname'] = $demand['member_name'];
				}else{
					$open_class = M("open_class")->find($c['object_id']);
					$c['title'] = $open_class['description'];
					$c['nickname'] = $open_class['member_name'];
				}
				$contracts[$key] = $c;
			}
			
			$this->contracts = $contracts;
		}
	}

	public function index(){
		$im = new \Org\Util\Im();
		$id = I("request.id");
		$dlgid = I("request.dlgid");

		//获取当前登录用户
		$me = M("mb_member")->find(session('id'));

		//获取聊天记录，当前只有最后一条记录，应该改成所有未读消息
		$ims = $im->read1($me['chat_id'],$dlgid);

		foreach($ims as $i => $c){
		      $ims[$i]['content']=html_entity_decode($c['msg'], ENT_QUOTES, 'UTF-8');
			   $ims[$i]['add_time']=$c['time'];
			   $m = M("mb_member")->where(['chat_id'=>$c['sender']['id']])->field('avatar,nickname')->find();
			   $ims[$i]['avatar'] = $m['avatar'];
			   $ims[$i]['nickname'] = $m['nickname'];
			   $ims[$i]['obj'] = $c['sender']['id'];
		}
		$this->ims = $ims;

		if (IS_POST) {						
			$from = $me['chat_id'];
			$to = $id;
			$con = I("request.content");
			$dlgid = $im->send($from,$to,$con,"1");				
			$this->showTrueJson($dlgid);
		}
		
		//最近聊天
		$msg = $im->get_list($me['chat_id']);
		$unreadcount = 0;
		foreach($msg as $i => $dlga)
       {	      
	    	$dlgid=$dlga['dlgid'];
			$dialog=$im->read($mid,$dlgid);
			$msg[$i]['content']=$dialog['msg'];
			$msg[$i]['add_time']=$dialog['time'];
			if(is_numeric($dlga['obj'])){
			  $m = M("mb_member")->field('avatar,nickname,hobby')->where(['chat_id'=>$dlga['obj']])->find();
			}else{
			  $m = M("group_manage")->table("group_manage gm,circle c")->where(['group_id'=>$dlga['obj'],'gm.circle_id = c.id'])->field('c.circle_name as nickname,c.logo as avatar')->find();
			}
			$msg[$i]['avatar'] = $m['avatar'];
			$msg[$i]['nickname'] = $m['nickname'];
			$msg[$i]['hobby'] = $m['hobby'];
			
			if($dlga['obj'] != $me['chat_id']){
			   $unreadcount = $unreadcount + $dlga['unread'];
			}
       }
	   $this->unreadcount = $unreadcount;
	   $this->remgs = $msg;

	   //我的关注、我的粉丝、我的好友
	   	$follows = M("follow")->join("LEFT JOIN mb_member ON mb_member.id=befollowed")->where(['follower'=>session("id")])->getField("mb_member.id,chat_id,avatar,nickname");
		$fans = M("follow")->join("LEFT JOIN mb_member ON mb_member.id=follower")->where(['befollowed'=>session("id")])->getField("mb_member.id,chat_id,avatar,nickname");
		$friends = array_intersect_assoc($follows,$fans);
		$this->follows = $follows;
		$this->fans = $fans;
		$this->friends = $friends;

		//获取我的校友群
		$im = new \Org\Util\Im();
		$c = $im->groups($me['chat_id']);
        foreach($c as $i => $v){
		   if(M("group_manage")->where(['group_id'=>$v['id']])->find()){
		     $g = M("group_manage")->where(['group_id'=>$v['id']])->find();
			 $gm = M("circle")->where(['id'=>$g['circle_id']])->field('logo')->find();
			 $v['logo'] = $gm['logo'];
			 $cgs[] = $v;
		   }
		}
		$this->cgroup = $cgs;

		$sers = M("service")->where(['member_id'=>session('id')])->select();
		foreach ($sers as $k => $v) {
     		//$pros[]=parse_class($v);
     	}
		$this->pros = $pros;
		$medu = M("mb_education_info")->where(['member_id'=>$id])->find();
	//	$medu = parse_class($medu);
		$this->medu = $medu;
		if(is_numeric($id)){
		  $this->you = M("mb_member")->where(['chat_id'=>$id])->find();
		}else{
		   $g = M("group_manage")->table("group_manage gm,circle c")->where(['group_id'=>$id,'gm.circle_id = c.id'])->field('c.circle_name as nickname,c.logo as avatar')->find();
		   $this->you = $g;
		}	
		
		$this->crm=$me;

		$this->display();
	}

	public function tome($id,$type,$mid){
		if (IS_POST) {
			$im = ['from_member_id'=>session("id"),'object_id'=>$id,'object_type'=>$type,'content'=>I('request.content'),'to_member_id'=>$mid];
			$id = M("imessage")->add($im);
			$this->showTrueJson($id);
		}
		$this->_common($id,$type,$mid);
		$this->you = M("mb_member")->find($mid);
		$this->ims = D("Imessage")->ims($id,$type,$mid);
		$this->display();
	}

	public function js(){
		$this->display("../Application/Home/View/Im/js.js");
	}

	public function one($id){
		$im = new \Org\Util\Im();
	   $msg = $im->get_new_list(session('id'));	
	   foreach($msg as $i => $dlga)
       {	if($dlga['obj']){
	    	$dlgid=$dlga['dlgid'];
			$dialog=$im->read($mid,$dlgid);
			$msg[$i]['content']=$dialog['msg'];
			$msg[$i]['add_time']=$dialog['time'];
			$m = M("mb_member")->field('avatar')->find($dialog['sender']['id']);
			$msg[$i]['avatar'] = $m['avatar'];			
	      }
       }
       $this->im = $msg;
		$this->display("chat_li");
	}
	public function pull($id,$dlgid){
		$im = new \Org\Util\Im();
		$me = M("mb_member")->find(session('id'));
	    $msg = $im->get_new_list($me['chat_id']);
		dump($msg);exit;
	   $msgs = array();
	   foreach($msg as $i => $dlga)
       {	if($dlga['obj'] == $id){
	    	$did=$dlga['dlgid'];
			 if($did == $dlgid){
			  $dialog=$im->read($me['chat_id'],$did);
			if($dialog['sender']['id']==$me['chat_id']){
			
			}else{
			   $msg[$i]['content']=html_entity_decode($dialog['msg'], ENT_QUOTES, 'UTF-8');
			   $msg[$i]['add_time']=$dialog['time'];
			   $m = M("mb_member")->field('avatar')->where(['chat_id'=>$dialog['sender']['id']])->find();
			   $msg[$i]['nickname'] = $dlga['name'];
			   $msg[$i]['avatar'] = $m['avatar'];	
			   $msgs[] = $msg[$i];
			}			
		   }
	      }
       }
	  if(count($msgs)>0){
       $this->ims = $msgs;
	    $this->display();	
	  }	  	
	}
	
	//检查我有没有新的聊天信息过来
	public function checkme(){
		if(!is_numeric(session('id'))) exit;
		$im = new \Org\Util\Im();
		$me_chat_id = 20000+session('id');
		$msg = $im->get_new_list($me_chat_id);
		if( $msg[1] && $msg[1]['obj']>0 && $msg[1]['obj']!=$me_chat_id ){
			echo count($msg)-1;
		}
	}

    //会话id
	public function getdlgid($mid){
		 $im = new \Org\Util\Im();
		 $me = M("mb_member")->find(session('id'));
		 $dlgid = $im->get_dialog_id($me['chat_id'],$mid);
		 $this->showTrueJson($dlgid);
	}

	public function gm($gid){
	    $im = new \Org\Util\Im();
		$members = $im->group_member($gid);
		$num = count($members);
		foreach($members as $i => $c){
		   if($c['id'] == $gid){
		      $ms[] = $c;
			  break;
		   }
		}
		$this->showTrueJson($members);
	}

	public function g($mid){
	  $im = new \Org\Util\Im();
	  $g = $im ->groups($mid);
	  $this->showTrueJson($g);
	}

	public function deleteg(){
	    $im = new \Org\Util\Im();
		$g = $im->delgroup("1");
		
	}

	public function createm($page){
	    $ms = M("mb_member")->page($page,1000)->select();
		foreach($ms as $i => $c){
		   $ms[$i]['chat_id'] = 20000+$c['id'];
		   M("mb_member")->where(['id'=>$c['id']])->save($ms[$i]);
		}
		$this->showTrueJson();
	}

	public function test(){
	$this->showTrueJson();
	}

	public function inputms($page){
	    $ms = M("mb_member")->page($page,300)->select();
		$im = new \Org\Util\Im();
		foreach($ms as $i => $c){
		  $im->member_create($c['chat_id'],$c['nickname']);
		}
		$this->showTrueJson();
	}

	public function getm($mid){
	   
		   $im = new \Org\Util\Im();
	     $m = $im->member_load($mid);
		 $this->showTrueJson($m);
	}

}