<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class AddressController extends AdminBaseController{
  protected $table_name = "address";
  public function index(){
    $list = M("address")->page(I('request.page'),20)->order("add_time desc")->select();
    $cou=M("address")->count();
    $this->assign_page($cou);
    $this->assign_list($list); 
    $this->display();
  }
  public function edit($id){
    $aid=$id;
    $re=M('area')->select();
    $re= $this->tree($re);
    if(IS_POST){
      $d=$_POST;
     if( M('address')->where("id=".$d['id'])->save($d)){
       $this->success("保存成功",U('index'));
     }
    }
    $list=M("address")->find($aid);
    $this->assign("re",$re);
    $this->assign('l',$list);
    $this->display();
  }
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
}