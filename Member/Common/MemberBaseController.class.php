<?php
namespace Member\Common;
use Common\Controller\BaseController;

require_once "../lib/curd.trail.php";
class MemberBaseController extends BaseController {
	public function _initialize(){
		parent::_initialize();
		if (!session("id")) {
			$url = U('/home/member/login');
			redirect($url."?url=".$_SERVER['REQUEST_URI']);
		}else{
			$this->assign("m", M("member")->find(session("id")));
            $this->mid = session("id");
		}
		$is_student = false;
        if(CONTROLLER_NAME == "Bid" && ACTION_NAME=='index'){
            $is_student = true;
        }else if(CONTROLLER_NAME == "Audition" && ACTION_NAME=='index'){
            $is_student = true;
        }else if(CONTROLLER_NAME == "ServiceOrder" && ACTION_NAME=='index'){
            $is_student = true;
        }else if(CONTROLLER_NAME == "Index" && ACTION_NAME=='studentCenter'){
            $is_student = true;
        }else if(CONTROLLER_NAME == "Demand"){
            $is_student = true;
        }else if(CONTROLLER_NAME == "MemberPost" && cookie('role') == 0){
            $is_student = true;
        }else if(CONTROLLER_NAME == "Order" && cookie('role') == 0){
            $is_student = true;
        }
		else if(CONTROLLER_NAME == "bid" && ACTION_NAME=='bidDesc'){
            $is_student = true;
        }
		//dump($is_student);
        $this->is_student = $is_student;
        $this->is_teacher = !$is_student;
	}
	use \curd;
}
