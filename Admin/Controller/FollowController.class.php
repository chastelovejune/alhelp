<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class FollowController extends AdminBaseController{
  protected $table_name = "follow";
  public function index(){
    $list = M("follow")->select();
    $this->assign_list($list); 
    $this->display();
  }	
}