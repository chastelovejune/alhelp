<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class MajorController extends AdminBaseController{
  public function index(){
    $page = I("get.page") ?: 1;
    $list = M("mj_major")->page("$page,20")->select();
    foreach ($list as $index => $major) {
      $major["mj_type"] = C("mj_major.mj_type")[$major["mj_type"]];
      $list[$index] = $major;
    }
    $this->assign_page(M("mj_major")->count(),$page);
    $this->assign_list($list);
    $this->display();
  }
}
