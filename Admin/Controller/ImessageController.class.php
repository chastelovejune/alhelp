<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class ImessageController extends AdminBaseController{
  protected $table_name = "imessage";
  public function index(){
    $list = M("imessage")->order("add_time desc")->page(I('request.page'),20)->select();
    //dump($list);
    $cou=M("imessage")->count();
    $this->assign_page($cou);
    $this->assign_list($list);
    $this->display();
  }	
}
