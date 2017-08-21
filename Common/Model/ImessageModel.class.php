<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ImessageModel extends RelationModel {
	function defaultScope(){
		$ims = $this
		->join("LEFT JOIN mb_member as from_member ON from_member.id=from_member_id")
		->join("LEFT JOIN mb_member as to_member ON to_member.id=to_member_id")
		->field("imessage.*,from_member.avatar as from_member_avatar,from_member.nickname as from_member_nickname,to_member.avatar as to_member_avatar,to_member.nickname as to_member_nickname");
		return $ims;
	}
	function ims($object_id,$object_type,$mid){
		$ims = $this->defaultScope()
		->where(['object_type'=>$object_type,'object_id'=>$object_id,'_complex'=>['_logic'=>'or',"from_member_id = $_SESSION[id] AND to_member_id=$mid","to_member_id = $_SESSION[id] AND from_member_id=$mid"]])->select();
		return $ims;
	}
}
