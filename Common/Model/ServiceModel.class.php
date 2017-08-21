<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ServiceModel extends RelationModel {
	 protected $_link = array(
	 	"Member"=>self::BELONGS_TO,
	 	"Attachment"=>[
		    'mapping_type'  => self::HAS_MANY,
		    'foreign_key' => 'object_id',
		    'condition' => 'type = 2',
	 	],
	 	'Remark' => ['mapping_type'  => self::HAS_MANY,'foreign_key'=>'object_id','condition'=>'is_demand=0'],
	 	"ServicePackage" => self::HAS_MANY,
	 );
}