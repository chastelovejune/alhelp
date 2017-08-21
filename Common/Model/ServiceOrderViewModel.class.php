<?php
namespace Common\Model;
use Think\Model\ViewModel;
class ServiceOrderViewModel extends ViewModel {
	public $viewFields = array(
		'ServiceOrder'=>['*'],
		"Service" => ['description','image','member_name','public_subject_id','cost','unified_id','school_id','_on'=>'ServiceOrder.service_id=Service.service_id','_type'=>'LEFT'],
		"ServicePackage" => ['num'=>'package_num','discount','_on'=>'ServicePackage.id=ServiceOrder.package_id','_type'=>'LEFT']
		);
}