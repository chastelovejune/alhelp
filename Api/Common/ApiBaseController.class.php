<?php
namespace Api\Common;
use \Common\Controller\BaseController;

class ApiBaseController extends BaseController {
   
     public function success($data = ""){
	     $this->ajaxReturn(array("suc"=>true,"message"=>$data));
	 }	
	 
	 //awen add
	 public function hashPwd($password){
	 	return md5($password);
	 }
}
