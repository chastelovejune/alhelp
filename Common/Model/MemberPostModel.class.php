<?php
namespace Common\Model;
use Think\Model\RelationModel;
class MemberPostModel extends RelationModel {
	public function mps(){
		$mid = session("id");
		$field = "member_post.*,mb_member.nickname,mb_member.avatar,circle.circle_name";
		if ($mid) {
			$field .= ",(select count(*) from circle_member where circle_id=member_post.circle_id and member_id = $mid and member_type=0) as is_master";
		}
		$mps = $this
		->join("LEFT JOIN mb_member ON mb_member.id=member_id")
		->join("LEFT JOIN circle ON circle_id=circle.id")
		->field($field);
		$type = I("request.type") ?: 0;
		switch ($type) {
			case 0:
				if ($mid) {
					$where = "CASE view_permission
						WHEN 0 THEN true
						WHEN 1 THEN member_id=$mid
						WHEN 2 THEN member_id IN (SELECT befollowed FROM follow WHERE follower = $mid union select $mid)
						WHEN 3 THEN  member_id IN (SELECT follower FROM follow WHERE befollowed = $mid union select $mid)
					END";
					$mps = $mps->where($where);	
				}
				break;
			case 1://公告
				$mps = $mps->where(['is_announcement'=>1]);
				break;
			case 2://热门
				$mps = $mps->where(['is_hot'=>1]);
				break;
			case 3://名师
				$mps = $mps->where("member_id IN (SELECT id FROM mb_member where is_v=1)");
				break;
			case 4://粉丝可见
				$mps = $mps->where(["view_permission" => 2]);
				if ($mid) {
					$mps = $mps->where("member_id IN (SELECT befollowed FROM follow WHERE follower = ".session("id")." )");
				}
				break;
			case 5://关注着可见
				$mps = $mps->where(["view_permission" => 3]);
				if ($mid) {
					$mps = $mps->where("member_id IN (SELECT follower FROM follow WHERE befollowed = ".session("id")." )");
				}
				break;
			case 6://自己可见
				$mps = $mps->where(["view_permission" => 1]);
				if ($mid) {
					$mps = $mps->where(['member_id'=>$mid]);
				}
				break;
			default:
				break;
		}
		$mps = $mps->order('is_top desc,member_post.id desc');
		return $mps;
	}

public function cmps(){
		$mid = session("id");
		$field = "member_post.*,mb_member.nickname,mb_member.avatar,circle.circle_name";
		if ($mid) {
			$field .= ",(select count(*) from circle_member where circle_id=member_post.circle_id and member_id = $mid and member_type=0) as is_master";
		}
		$mps = $this
		->join("LEFT JOIN mb_member ON mb_member.id=member_id")
		->join("LEFT JOIN circle ON circle_id=circle.id")
		->field($field);
		$type = I("request.type") ?: 0;
		switch ($type) {
			case 0:
				if ($mid) {
					$where = "CASE view_permission
						WHEN 0 THEN true
						WHEN 1 THEN member_id=$mid
						WHEN 2 THEN member_id IN (SELECT befollowed FROM follow WHERE follower = $mid union select $mid)
						WHEN 3 THEN  member_id IN (SELECT follower FROM follow WHERE befollowed = $mid union select $mid)
					END";
					$mps = $mps->where($where);	
				}
				break;
			case 1://公告
				$mps = $mps->where(['circle_announcement'=>1]);
				break;
			case 2://热门
				$mps = $mps->where(['circle_hot'=>1]);
				break;
			case 3://名师
				$mps = $mps->where("member_id IN (SELECT id FROM mb_member where is_v=1)");
				break;
			case 4://粉丝可见
				$mps = $mps->where(["view_permission" => 2]);
				if ($mid) {
					$mps = $mps->where("member_id IN (SELECT befollowed FROM follow WHERE follower = ".session("id")." )");
				}
				break;
			case 5://关注着可见
				$mps = $mps->where(["view_permission" => 3]);
				if ($mid) {
					$mps = $mps->where("member_id IN (SELECT follower FROM follow WHERE befollowed = ".session("id")." )");
				}
				break;
			case 6://自己可见
				$mps = $mps->where(["view_permission" => 1]);
				if ($mid) {
					$mps = $mps->where(['member_id'=>$mid]);
				}
				break;
			default:
				break;
		}
		$mps = $mps->order('is_top desc,member_post.id desc');
		return $mps;
	}

}