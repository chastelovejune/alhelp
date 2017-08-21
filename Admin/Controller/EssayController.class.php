<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class EssayController extends AdminBaseController{
  protected $table_name = "essay";
  public function index(){
    $list = M("essay")->select();
    $this->assign_list($list); 
    $this->display();
  }
  public function edit($id){
    $re=M('essay')->find($id);
    $this->assign("re",$re);
    if(IS_POST){
      $d=$_POST;
      if(M('essay')->where('id='.$id)->save($d)){
        $this->success("保存成功");
      }
    }
    $this->display();
  }
}
