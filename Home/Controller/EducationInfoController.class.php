<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class EducationInfoController extends HomeBaseController {
	public function edit(){
		$info = $_POST;
		$info['member_id'] = session("id");
	
		
		if ($info['id'] > 0) {
			M("mb_education_info")->save($info);		
		}else{
			M("mb_education_info")->add($info);
			$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'], 'school_id');
			D("CircleMember")->addBySchoolId($school_id);
			$m = M("mb_member")->find(session('id'));
			$c = M("circle")->where(['object_id'=>$school_id])->find();
			$im = new \Org\Util\Im();	
			if(M("group_manage")->where(['circle_id'=>$c['id']])->find()){
				$g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	      
	        	$im->group_join($m['chat_id'],$g['group_id']);
		    }else{
				  $gid = $im->group_create('19999',$c['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $c['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
			}
			
		}

		$this->showTrueJson();
	}
	public function delete($id){
		$ec = M("mb_education_info")->find($id);
		M("mb_education_info")->delete($id);
		$school_id = M("zysc_view")->getFieldByZhuanyeId($ec['school_id'], 'school_id');
		$c = M("circle")->where(['object_id'=>$school_id])->find();
		$m = M("mb_member")->field('chat_id')->find(session('id'));
		$im = new \Org\Util\Im();
		$g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	 
		$im->group_remove($m['chat_id'],$g['group_id']);
		M("circle_member")->where(['member_id'=>session('id'),'circle_id'=>$c['id']])->delete();
		$this->showTrueJson();
	}
}