<?php
namespace Home\Common;
use \Common\Controller\BaseController;

class HomeBaseController extends BaseController {
	public function _initialize(){
		parent::_initialize();
		if (session("id")) {
			$this->assign("m", M("mb_member")->find(session("id")));
			$this->mid = session("id");
		}
	}

	public function mustLogin(){
		if (!session("id")) {
			$url = U('/home/member/login');
			if(IS_AJAX){
				$this->showFalseJson("请先登录吧..");
			}
			//header( 'Content-Type:text/html;charset=utf-8 ');
			header("Content-type: text/html; charset=utf-8");
			redirect($url."?url=".$_SERVER['REQUEST_URI'],1,"请先登录吧..");
		}
	}
	protected function sort($data,$column=""){
		$sort = $_GET[sort] ?: 0;
		$this->sort = $sort; 
		if ($sort == 0) {
			$key = "id";
			$order =  "DESC";
     	}elseif($sort == 1){
     		$key = "id";
     		$order = "ASC";
     	}elseif($sort == 2){
     		$key = "views";
     		$order = "ASC";
     	}else{
     		$key = "views";
     		$order = "DESC";
     	}
     	if (($sort == 0 || $sort == 1) && $column) {
     		$key = $column;
     	}
     	if ($data) {
	     	$data = $data->order($key . " ".$order);
			return $data;
     	}else{
     		return $key . " ".$order;
     	}
	}
	protected function where3type(){
		$school_id = I("request.school_id"); 
		if ($school_id) {
			$w = ["zysc_view.area_id"=>$school_id,"zysc_view.school_id"=>$school_id,"zysc_view.yuan_id"=>$school_id,"zysc_view.zhuanye_id"=>$school_id,"_logic"=>"or"];
			return ['_complex' => $w];
		}else if (strlen(I("request.unified_id")) > 0) {
			return ['unified_id' => I("request.unified_id")];
		}else if (strlen(I("request.public_subject_id")) > 0) {
			return ['public_subject_id' => I("request.public_subject_id")];
		}
		return [];
	}
}
