<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class ActivityController extends AdminBaseController{
  protected $table_name = "activity";
  public function index(){
    $list = M("activity")->select();
    $this->assign_list($list); 
    $this->display();
  }	
}