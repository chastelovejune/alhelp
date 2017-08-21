<?php
namespace Admin\Common;
use Think\Controller;

class AdminBaseController extends Controller {
	
	public function _initialize(){
    $menus= menus();
    $this->assign("menus",$menus);
    //高亮当前菜单用js去做简单些
    $thisMenuIndex = 0;
    foreach ($menus as $index => $tmp) {
      $menu1 = $tmp["menus"];
      foreach ($menu1 as $menu2) {
        foreach ($menu2 as $url) {
          if (strpos($url, "_".strtolower(CONTROLLER_NAME)."_") || strpos($url, "_".CONTROLLER_NAME."_") ) {
            $thisMenuIndex = $index;
          }
        }    
      }
    }
    $this->assign("thisMenuIndex",$thisMenuIndex);//高亮
    $this->assign("thisMenu",$menus[$thisMenuIndex]["menus"]);
    if ( CONTROLLER_NAME == "Index" && ACTION_NAME=="login" ) {
    }else if(!checkSession()){
      $this->redirect('Index/login'); 
    }
	}
  public function update(){
    M($this->table_name)->save($_POST);
    $this->showTrueJson();
  }
  public function assign_page($total){
    if (!$total) {
      $total  = $this->table_name;
    }
    if ((int)$total == 0) {
      $total = M($total)->count();
    }
    $current = I("request.page") ?: 1;
    $xianshiCount = $total > 120 ? 6 : $total/20+1;
    $html = getPageHtml($total,20,$current,$xianshiCount);  
    $this->assign("pageHtml", $html);
  }
  public function assign_list($list){
    $this->assign("list",$list);
    $this->assign("columns",array_keys($list[0]));
  }

  public function index(){
    $list = M($this->table_name)->select();
    $this->assign_list($list); 
    $this->display();
  }	
  public function add($table = ""){
    $table = $table ?: $this->table_name;
    if ( IS_POST ) {
      M($table)->add($_POST);       
      $this->success("添加成功",U('index'));
    }else
      $this->display();
  }

  public function delete($table = ""){
    $table = $table ?: $this->table_name;
    M($table)->where($_GET)->delete();
    $this->success("删除成功",U("index"),1);

  }

  public function edit($table = "",$callback=null){
    $table = $table ?: $this->table_name;
    if ( IS_POST ) {
      M($table)->where($_GET)->save($_POST);       
      $this->success("修改成功",U('index'));
    }else{
      $obj = M($table)->where($_GET)->find();
      $this->assign($table,$obj);
      if ( $callback ) {
        $callback();
      }
      $this->display("add");
    }
  }
  public function showTrueJson($message=""){
      $this->ajaxReturn(array("suc"=>true,"message"=>$message));
  }
  public function showFalseJson($message=""){
      $this->ajaxReturn(array("suc"=>false,"message"=>$message));
  }
  //定义一个方法，对给定的数组，递归形成树
  public function tree($arr,$pid = 0,$level = 0) {
    static $tree = array();
    foreach ($arr as $v) {
      if ($v['pid'] == $pid) {
        //说明找到，保存
        $v['level'] = $level; //保存当前分类的所在层级
        if($v['pid']!=0) {
          $tree[] = $v;
        }
        //继续找
        $this->tree($arr,$v['id'],$level + 1);
      }
    }
    return $tree;
  }
  

  //awen add
  public function hashPwd($password){
  	return md5($password);
  }
}
