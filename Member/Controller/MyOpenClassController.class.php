<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class MyOpenClassController extends MemberBaseController {
    public function index() {
        //$map['apply_open_class.member_id'] = session("id");
        //试听课部分
      $keywords = I("request.keywords");
		if(strlen($keywords)>0){
            $re = M("open_class")->where(['_string'=>"description LIKE '%$keywords%' OR content_course LIKE '%$keywords%' OR content_reference LIKE '%$keywords%' OR remarks LIKE '%$keywords%'"])->page($_GET['page'] ?: 1, 20)->order("open_class_time desc")->select();
            $cou= M("open_class")->where(['_string'=>"description LIKE '%$keywords%' OR content_course LIKE '%$keywords%' OR content_reference LIKE '%$keywords%' OR remarks LIKE '%$keywords%'"])->count();
            $this->assign('cou', $cou);
            $this->apply = $re;
        }else {
            $re = M("open_class")->where("open_class_id in (select open_class_id FROM apply_open_class where member_id=$_SESSION[id])")->page($_GET['page'] ?: 1, 20)->order("open_class_time desc")->select();
            $cou =M("open_class")->where("open_class_id in (select open_class_id FROM apply_open_class where member_id=$_SESSION[id])")->count();
            $this->assign('cou', $cou);
            $this->apply = $re;
        }

        $this->is_student = true;
    	$this->display();
    }
   
}