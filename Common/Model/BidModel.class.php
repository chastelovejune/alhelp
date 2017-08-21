<?php
namespace Common\Model;
use Think\Model\RelationModel;
class BidModel extends RelationModel {
	 protected $_link = array(
	 	'Demand'=>self::BELONGS_TO,
	 	'Service'=>self::BELONGS_TO,
	 	'MbMember'=>['mapping_type'  => self::BELONGS_TO,'foreign_key'=>'member_id'],
	 	);
}