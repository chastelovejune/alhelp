<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class UnifiedController extends AdminBaseController{
  protected $table_name = "unified";
  public function _initialize(){
    parent::_initialize();
    $cs = M("unified_classify")->select();
    $this->assign("cs",$cs);
    $majors = M("mj_major")->where("mj_type=0")->select();
    $this->assign("majors",$majors);
  }
  public function index(){
    $list = M("unified")->select();
    foreach ($list as $index => $u) {
      $u["classify"] = M("unified_classify")->where(["id"=>$u["cid"]])->find();
      $u["major"] = M("mj_major")->where(["id"=>$u["major_id"]])->find();
      $list[$index] = $u;
    }
    $this->assign_list($list);
    $this->display();
  }
  //一级学科
  public function majors(){
    $majors = M("mj_major")->where(["pid"=>$_GET["pid"]])->select();
    $this->ajaxReturn($majors);
  }

  public function edit(){
    parent::edit($this->table_name,function() {
      $unified = $this->unified;
      $unified["major"] = M("mj_major")->where(["id"=>$unified["major_id"]])->find();
      $this->assign("unified",$unified);
    });
  }
}
