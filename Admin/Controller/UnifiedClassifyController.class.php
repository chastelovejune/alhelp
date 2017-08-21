<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class UnifiedClassifyController extends AdminBaseController{
  protected $table_name = "unified_classify";
  public function index(){
    $list = M("unified_classify")->select();
    foreach ($list as $index => $c) {
      $list[$index] = $c;
    }
    $this->assign_list($list);
    $this->display();
  }
}
