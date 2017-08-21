<?php
namespace Common\Model;
use Think\Model\RelationModel;
class AuditionModel extends RelationModel {
	 protected $_link = array(
	 	'Service'=>self::BELONGS_TO,
	 	"MbMember"=>['mapping_type'=>self::BELONGS_TO,'foreign_key'=>'member_id']
	 	);
}