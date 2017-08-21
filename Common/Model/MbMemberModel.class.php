<?php
namespace Common\Model;

class MbMemberModel extends \Think\Model\RelationModel {

    protected $_link = array(
    		"Circle"=>array(
				    'mapping_type'      =>  self::MANY_TO_MANY,
    	    		'foreign_key'   => 'member_id',
	    		    'relation_foreign_key'  =>  'circle_id',
	    		    'relation_table'    =>  'circle_member'
    			),
    	);

	public function validate($data,$id = 0){
		if ($data['nickname'] && M("mb_member")->where(['nickname'=>$data['nickname'],'id' => ['neq',$id]])->find()) {
			return "用户名已存在";
		}else if ($data['phone'] && M("mb_member")->where(['phone'=>$data['phone'],'id' => ['neq',$id]])->find()) {
			return "手机号已存在";
		}else if ($data['email'] && M("mb_member")->where(['email'=>$data['email'],'id' => ['neq',$id]])->find()) {
			return "邮箱已存在";
		}
		return true;
	}
	public function save($id,$data){
		$result = $this->validate($data,$id) ;
		if ($result === true) {
			M()->startTrans();
			M("mb_member")->where(['id'=>$id])->save($data);
			//更新冗余
			$nickname = $data['nickname'];
			if ($nickname) {
				M("member_post")->where(['member_id'=>$id])->save(['member_nickname'=>$nickname]);
				M("open_class")->where(['member_id'=>$id])->save(['member_name'=>$nickname]);
				M("demand")->where(['member_id'=>$id])->save(['member_name'=>$nickname]);
				M("service")->where(['member_id'=>$id])->save(['member_name'=>$nickname]);
			}
			M()->commit();
			return true;
		}else{
			return $result;
		}
	}
}