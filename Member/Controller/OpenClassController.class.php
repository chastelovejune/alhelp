<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class OpenClassController extends MemberBaseController {
    public function index() {
		//$openClass3 = $openClass3->where(['member_id'=>session("id")]);
    		//$openClass3 = $openClass3->where("open_class_id in (select open_class_id FROM apply_open_class where member_id=$_SESSION[id])");
	    $keywords = I("request.keywords"); 
		if(strlen($keywords)>0){
			$re = M("open_class")->where(['_string'=>"description LIKE '%$keywords%' OR content_course LIKE '%$keywords%' OR content_reference LIKE '%$keywords%' OR remarks LIKE '%$keywords%'"])->page($_GET['page'] ?: 1, 20)->order("open_class_time desc")->select();
			$cou= M("open_class")->where(['_string'=>"description LIKE '%$keywords%' OR content_course LIKE '%$keywords%' OR content_reference LIKE '%$keywords%' OR remarks LIKE '%$keywords%'"])->count();
			$this->assign('cou', $cou);
			$this->openClass3 = $re;
		}else {
			$re = M("open_class")->where(['member_id' => session("id")])->page($_GET['page'] ?: 1, 20)->order("open_class_time desc")->select();
			$cou =M("open_class")->where(['member_id' => session("id")])->count();
			$this->assign('cou', $cou);
			$this->openClass3 = $re;
		}
    	$this->display();
    }
	public  function up()
	{
		$id=I('id');
		$re=M('open_class')->where("open_class_id=".$id)->setField('status',0);
		if ($re) {
			$this->showTrueJson("上架成功");
		}
	}
		public  function down(){
			$id=I('id');
			$re=M('open_class')->where("open_class_id=".$id)->setField("status",3);
			if($re){
				$this->showTrueJson("下架成功");
			}
	}
}