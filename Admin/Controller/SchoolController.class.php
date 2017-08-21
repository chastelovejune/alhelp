<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class SchoolController extends AdminBaseController{
  public function index(){
    $page = I("get.page") ?: 1;
    if(IS_POST){
    	$key=$_POST['key'];
    	$map['title']=array('like',"%$key%");
    	$map['type']=$_POST['type'];
    	 $list = M("school")->where($map)->page($page,20)->select();
    }else{
    $list = M("school")->page($page,20)->select();
}
    foreach ($list as $index => $school) {
      $school["type"] = C("school.type")[$school["type"]-1]; 
      $list[$index] = $school;
    }
    $this->assign_page(M("school")->count());
    $this->assign_list($list);
    $this->display();
  }
  public function stop($id)
  {
    if (M("school")->where("id=" . $id)->setField("status", 1)) {
      $this->success("禁用成功", U('admin/school/index'));
    }
  }
    public function start($id){
      if(M("school")->where("id=".$id)->setField("status",0)){
        $this->success("启用成功",U('admin/school/index'));
      }
  }
  public function delete($id){
    $this->error("禁止删除",U('admin/school/index'));
    /*if(M("school")->->delete($id)){
      $this->success("启用成功",U('admin/school/index'));
    }*/
  }
  public function edit($id)
  {
    $school=M('school')->find($id);
    if($school['pid']!=0){
    	$pid_school=M('school')->where("id=".$school['pid'])->select();
    }
     $this->assign("pid_school",$pid_school);
    $this->assign("school",$school);
    if(IS_POST){
      $d=$_POST;
      if(M('school')->where('id='.$id)->save($d)){
        $this->success("保存成功",U('admin/school/index'));
      }else{
        $this->error("保存失败",U('admin/school/index'));
      }
    }
    $this->display();
  }
  public function add(){
  	  if($school['pid']!=0){
    	$pid_school=M('school')->where("id=".$school['pid'])->select();
    }
     $this->assign("pid_school",$pid_school);
    if(IS_POST){
      $d=$_POST;
      $d['add_time']=date('Y-m-d h:i:s',time());
      if(M('school')->add($d)){
        $this->success("添加成功",U('admin/school/index'));
      }else{
        $this->error("添加失败",U('admin/school/index'));
      }
    }
    $this->display();
  }
}
