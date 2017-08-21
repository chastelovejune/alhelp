<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class SystemMessageController extends AdminBaseController{
  protected $table_name = "system_message";
  public function index(){
    $list = M("system_message")->page(I('request.page'),20)->order('time_create desc')->select();
    $cou= M("system_message")->select();
    $this->assign('cou',$cou);
    $this->assign_list($list); 
    $this->display();
  }
  public function add(){
    if(IS_POST) {
      $d = $_POST;
      $d['time_create']=time();
      if (M('system_message')->add($d)) {
        $this->success("添加成功");
      } else {
        $this->error("添加失败");
      }
    }
    $this->display();
  }
}
