<?php
namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;


class LookMemberController extends ApiBaseController {
    
	//»ñÈ¡Ð£ÓÑ
	public function getmembers($sid,$uid,$page){
		if($sid == 0){
	       $members = M("mb_member")->page($page,10)->field("avatar,nickname,id")->select();
		   $count = M("mb_member")->count();
		    $count = $count - ($page-1) * 10;
		}else{
			$zyid = M("zysc_view")->field("zhuanye_id")->where(['school_id'=>$sid])->select();
			
				 foreach($zyid as $key => $value){
		      $sids[$key] = $value['zhuanye_id'];
				 }
		  $sid['school_id'] = array('in',$sids);
		   $members = M("mb_education_info")->table("mb_member m,mb_education_info me")->page($page,10)->where(['school_id'=>['in',$sids],'me.member_id = m.id'])->field('m.avatar,m.nickname,me.member_id as id')->group("member_id")->select();
		   $count = M("mb_education_info")->where(['school_id'=>['in',$sids]])->count('id');
		    $count = $count - ($page-1) * 10;
		}

		foreach($members as $i => $c){
		   if(M("follow")->where(['follower'=>$uid,'befollowed'=>$c['member_id']])->find()&&M("follow")->where(['follower'=>$c['member_id'],'befollowed'=>$uid])->find()){
		       $members[$i]['is_mutual'] = '1';
		   }else{
		       $members[$i]['is_mutual'] = '0';
		   }
		}

		$data=array(
			'list' => $members,
			'count' => $count
		);
		$this->success($data);
	}
}