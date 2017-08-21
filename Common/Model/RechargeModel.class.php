<?php
namespace Common\Model;
use Think\Model\RelationModel;
class RechargeModel extends RelationModel {
	protected $_link = array(
			"Cash" => self::BELONGS_TO,
		);
}