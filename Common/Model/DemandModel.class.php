<?php
namespace Common\Model;
use Think\Model\RelationModel;
class DemandModel extends RelationModel {
	 protected $_link = array(
	 	"Member"=>self::BELONGS_TO,
	 	'Remark' => ['mapping_type'  => self::HAS_MANY,'foreign_key'=>'object_id','condition'=>'is_demand=1'],
	 );
}