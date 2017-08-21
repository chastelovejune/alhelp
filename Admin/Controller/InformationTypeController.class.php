<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class InformationTypeController extends AdminBaseController{
  protected $table_name = "information_type";
  public function index($type=0){
    $list = M("information_type")->where("type=".$type)->select();
    $list=$this->tree($list);
    $this->assign_list($list);
    $type_name=["下载","资讯","分享","公告","我们","院校动态","你该知道的新助邦"];
    $this->assign('type_name',$type_name);
    $this->assign('type',$type);
    $this->display();
  }
  //定义一个方法，对给定的数组，递归形成树
  public function tree($arr,$pid = 0,$level = 0) {
    static $tree = array();
    foreach ($arr as $v) {
      if ($v['pid'] == $pid) {
        //说明找到，保存
        $v['level'] = $level; //保存当前分类的所在层级
        $tree[] = $v;
        //继续找
        $this->tree($arr,$v['id'],$level + 1);
      }
    }
    return $tree;
  }
  public function add($type){
    $tops = M("information_type")->where('type='.$type)->select();
    $tops=$this->tree($tops);
    if(IS_POST){
     $d['name']=$_POST['name'];
      $d['pid']=$_POST['pid'];
      if(empty($_POST['pid'])){
        $d['pid']=0;
      }
      $d['type']=$_POST['type'];
      $upload = upload();
      $image = $upload['image'];
      if ($image) {
        $d['image']  =$image;
      }
      $re=M('information_type')->add($d);
      if($re){
        $this->success("添加成功",U('admin/informationType/index',['type'=>$type]));
      }
      else{
        $this->error("添加失败");
      }
    }
    $this->assign('type',$type);
    $this->assign('tops',$tops);
    $this->display();
  }
  public function edit($id,$type){
    $type2 = M("information_type")->where('type='.$type)->find($id);
    $tops = M("information_type")->where('type='.$type)->select();
    $tops=$this->tree($tops);
    if(IS_POST){
      $d['name']=$_POST['name'];
      $d['pid']=$_POST['pid'];
      $d['type']=$_POST['type'];
      $re=M('information_type')->add($d);
      if($re){
        $this->success("修改成功",U('admin/informationType/index',['type'=>$type]));
      }
      else{
        $this->error("修改失败");
      }
    }
    $this->assign('type',$type);
    $this->assign('type2',$type2);
    $this->assign('tops',$tops);
    $this->display();
  }
}