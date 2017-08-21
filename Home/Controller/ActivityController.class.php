<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
require "../lib/curd.trail.php";
class ActivityController extends HomeBaseController {
	use \curd;

	public function index(){
		$hot = M("activity")->order("id desc")->find();
		if (session("id")) {
			$hot['activity_member_id'] = M("activity_member")->getFieldByActivityId($hot['id'],'id');
		}
		$this->hot = $hot;
		$this->display();
	}
	public function add(){
		if (IS_POST) {
			$a = array_merge($_POST,['member_id'=>session("id")]);
			$image = upload("image");
			if ($image) {
				$a['image'] = $image;
			}
			$id = M("activity")->add($a);
			$this->showTrueJson(U('/home/activity/show',['id'=>$id]));
		}
		$this->display();
	}
	public function show($id){
		$a = M("activity")->join("LEFT JOIN mb_member ON mb_member.id=activity.member_id")->field("activity.*,nickname,avatar")->where(["activity.id"=>$id])->find();

		if (session("id")) {
			$a['activity_member_id'] = M("activity_member")->getFieldByActivityId($a['id'],'id');
		}
		$this->assign("a",$a);
		$this->display();
	}
	public function join(){
		$id = M("activity_member")->add(["member_id"=>session("id"),"activity_id"=>I("request.id")]);
		$this->showTrueJson($id);
	}
}