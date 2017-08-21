<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class BankcardController extends AdminBaseController{
  protected $table_name = "bankcard";
  public function index(){
    $list = M("bankcard")->table("mb_member m,bankcard b")->where(['b.member_id = m.id'])->order("id desc")->page(I('request.page'),20)->field("b.*,m.nickname")->select();
    $this->assign_list($list); 
	$this->assign_page("bankcard"); 
    $this->display();
  }
  
  public function updatestatus(){
    $b['status'] = I("request.status");
	M("bankcard")->where(['id'=>I("request.id")])->save($b);
	$this->showTrueJson();
  }
}
