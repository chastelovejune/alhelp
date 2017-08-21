<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class PublicSubjectController extends AdminBaseController{
  protected $table_name = "public_subject";
  public function index(){
    $list = M("public_subject")->select();
    $this->assign_list($list); 
    $this->display();
  }
  public function show(){
  	
  }	
}
