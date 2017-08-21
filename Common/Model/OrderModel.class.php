<?php
namespace Common\Model;

class OrderModel extends \Think\Model\RelationModel {
	protected $_link = array(
			"Cash" => self::BELONGS_TO,
			"Teacher" => ['mapping_type'=>self::BELONGS_TO,'foreign_key'=>'from_member_id','class_name'=>'MbMember'],
			"Student" => ['mapping_type'=>self::BELONGS_TO,'foreign_key'=>'to_member_id','class_name'=>'MbMember'],
			'Service' => self::BELONGS_TO,
		);
}