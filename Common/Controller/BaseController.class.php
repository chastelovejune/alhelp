<?php
namespace Common\Controller;
use Think\Controller;

class BaseController extends Controller {
	public function _initialize(){
		if (!$this->table_name) {
			$name = CONTROLLER_NAME;
			preg_match_all("/[A-Z][a-z]*/", $name, $matches);
			$table_name = implode('_', $matches[0]);
			$table_name = strtolower($table_name);
			$this->table_name = $table_name;
		}
		
		if (is_android()) {
			$userid = $_REQUEST["userId"];
			if ($userid > 0) {
				session("id",$userid);
			}
			go_log($_POST);
		}
		
	}
	public function showTrueJson($message=""){
    	$this->ajaxReturn(array("suc"=>true,"message"=>$message));
	}
	public function showFalseJson($message=""){
    	$this->ajaxReturn(array("suc"=>false,"message"=>$message));
	}
	
	//awen add
	public function url(){
		$domain = $_SERVER['HTTP_HOST'];
		return (is_ssl()?'https://':'http://').$domain.'/';
	}
	
	//awen add 
	public function hashPwd($password){
		return md5($password);
	}
}