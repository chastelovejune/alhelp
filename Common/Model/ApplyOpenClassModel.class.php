<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ApplyOpenClassModel extends RelationModel {
	 protected $_link = array(
	 	'OpenClass'=>['mapping_type'=>self::BELONGS_TO,'foreign_key'=>'open_class_id'],
	 	"MbMember"=>['mapping_type'=>self::BELONGS_TO,'foreign_key'=>'member_id']
	 	);
}