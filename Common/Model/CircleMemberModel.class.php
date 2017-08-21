<?php
namespace Common\Model;

class CircleMemberModel extends \Think\Model {
	public function addBySchoolId($school_id){
		$circle = M('circle')->where(['circle_type'=>0,'object_id'=>$school_id])->find();
		if (!$circle) {
			E("脏数据, 没有这个circle");
		}
		$cm = M("circle_member")->where(['member_id'=>session("id"),'circle_id'=>$circle['id']])->find();
		if ($cm) {
			go_log('已经加入了圈子'.print_r($cm,true));
			return;
		}
		return M("circle_member")->add(['member_id'=>session("id"),'circle_id'=>$circle['id'],'member_type'=>1]);
	}
}
