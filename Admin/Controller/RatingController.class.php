<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class RatingController extends AdminBaseController{
  protected $table_name = "rating";
  public function index(){
    $list = M("rating")->select();
    $this->assign_list($list); 
    $this->display();
  }	
}
