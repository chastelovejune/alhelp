<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class CircleController extends MemberBaseController {

	public function index(){
	  $circles = M("circle")->join("LEFT JOIN school ON object_id=school.id")->join("LEFT JOIN circle_member ON circle_member.circle_id=circle.id")->where(["circle_member.member_id"=>session('id')])->field("logo,circle.*,title")->select();
	  $this->circles = $circles;
	  $followCs = M("circle")->join("LEFT JOIN school ON object_id=school.id")->join("LEFT JOIN member_follow_circle ON member_follow_circle.circle_id=circle.id")->where(["member_follow_circle.member_id"=>session('id')])->limit(10)->field("logo,circle.*,title")->select();
	  $this->followcs = $followCs;
	  $this->display();
	}

	public function del(){
	   M("member_follow_circle")->where(['member_id'=>session('id'),'circle_id'=>I("request.circle_id")])->delete();
	   $m = M("mb_member")->field('chat_id')->find(session('id'));
		$im = new \Org\Util\Im();
		$g = M("group_manage")->where(['circle_id'=>I("request.circle_id")])->find();	 
		$im->group_remove($m['chat_id'],$g['group_id']);
	   $this->showTrueJson();
	}

	public function add(){
		$circle = M("circle")->where(['object_id'=>I("request.school_id")])->find();
		$data["member_id"] = session("id");
		$data["circle_id"] = $circle['id'];
		M("member_follow_circle")->add($data);
			//加入校友圈群聊
		$m = M("mb_member")->find(session('id'));
		$im = new \Org\Util\Im();	
		if(M("group_manage")->where(['circle_id'=>$circle['id']])->find()){
			$g = M("group_manage")->where(['circle_id'=>$circle['id']])->find();	      
	        $im->group_join($m['chat_id'],$g['group_id']);
		}else{
			$gid = $im->group_create('19999',$circle['circle_name']);
			$g['group_id'] = $gid;
			$g['is_circle'] = 1;
			$g['circle_id'] = $circle['id'];
			M("group_manage")->add($g);
            $im->group_join($m['chat_id'],$gid);
		}
		$this->showTrueJson();
	}
}