<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class CircleController extends AdminBaseController{
  public function index(){
    $page = I("get.page") ?: 1;
    $list = M("circle")->page($page,20)->select();
    $this->assign_page(M("circle")->count());
    $this->assign_list($list);
    $this->display();
  }
  public function stop($id)
  {
    if (M("circle")->where("id=" . $id)->setField("status", 1)) {
      $this->success("禁用成功", U('admin/circle/index'));
    }
  }
    public function start($id){
      if(M("circle")->where("id=".$id)->setField("status",0)){
        $this->success("启用成功",U('admin/circle/index'));
      }
  }
  public function delete($id){
    $this->error("禁止删除",U('admin/circle/index'));
    /*if(M("circle")->->delete($id)){
      $this->success("启用成功",U('admin/circle/index'));
    }*/
  }
  public function edit($id)
  {
    $circle=M('circle')->find($id);
    $this->assign("circle",$circle);
    $c_list=M('circle')->select();
    $this->assign("c_list",$c_list);
    $school=M('school')->where('type=2')->select();
    $this->assign("school", $school);
    if(IS_POST){
      $d=$_POST;
      $logo=upload();
      if($logo){
        $d['logo']=$logo['logo'];
      }
      if(M('circle')->where('id='.$id)->save($d)){
        $this->success("保存成功",U('admin/circle/index'));
      }else{
        $this->error("保存失败",U('admin/circle/index'));
      }
    }
    $this->display();
  }
  public function add(){
    $c_list=M('circle')->select();
    $this->assign("c_list",$c_list);
    $school=M('school')->where('type=2')->select();
    $this->assign("school", $school);
    if(IS_POST){
      $d=$_POST;
      $logo=upload();
      if($logo){
        $d['logo']=$logo['logo'];
      }
      $d['add_time']=date('Y-m-d h:i:s',time());
      if(M('circle')->add($d)){
        $this->success("添加成功",U('admin/circle/index'));
      }else{
        $this->error("添加失败",U('admin/circle/index'));
      }
    }
    $this->display();
  }
}
