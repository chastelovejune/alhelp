<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class MemberPostController extends AdminBaseController{
  protected $table_name = "member_post";
  public function index(){
    $list = M("member_post")->order('id desc')->where(['status = 1']);
		{
		//·ÖÒ³
		$tmp = clone $list;
		$this->assign_page($tmp->count());
		unset($tmp);
	}
	$list = $list->page(I("request.page",1),20)->select();
    $this->assign_list($list); 
    $this->display();
  }	
}
