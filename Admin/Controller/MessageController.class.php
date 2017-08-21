<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class MessageController extends AdminBaseController{
  protected $table_name = "message";
  /*public function _initialize(){
    parent::_initialize();
    $members = M("mb_member")->select();
    $this->assign("members",$members);
  }*/
  //分页好像不对，待优化
  public function index(){
    $list = M("message")->page($_GET['page']?:1,20)->order('add_time desc')->select();
    $count=M("message")->count();
    foreach ($list as $i => $v) {
      $v["member_name"] = M("mb_member")->find($list["to_member_id"])["nickname"];
      if($v['type']==0){
        $v['type_name']="系统消息";
        $v['url']="/member_service.html";
      }
      if($v['type']==1){
        $v['type_name']="试听";
        $v['url']=U('member/OpenClass/Index',['id'=>$v['object_id']]);
      }
      if($v['type']==2){
        $v['type_name']="协议";
        $v['url']=U('member/contract/show',['id'=>$v['object_id']]);
      }
      if($v['type']==3){
        $v['type_name']="学习管理";
        $v['url']=U('member/serviceOrder/show',['id'=>$v['object_id']]);
      }
      if($v['type']==4){
        $v['type_name']="评价";
        $v['url']="";
      }
      if($v['type']==5){
        if($v['role']==1) {
          $v['type_name'] = "老师投标";
          $v['url'] = U('member/bid/teacher',['id'=>$v['id']]);
        }else{
          $v['type_name'] = "学生投标";
          $v['url'] = U('member/bid/index',['id'=>$v['id']]);
        }
      }
      if($v['type']==6){
        $v['type_name'] = "支付";
        $v['url'] = "";

      }
      if($v['type']==7){
        $v['type_name']="仲裁";
        $v['url']="";
      }
      if($v['type']==8){
        $v['type_name']="网站广播";
        $v['url']="";
      }
      if($v['type']==9){
        $v['type_name']="留言新助邦";
        $v['url']="";
      }
      if($v['type']==10){
        if($v['role']==1) {
          $v['type_name'] = "匹配专业推荐";
          $v['url'] = U('home/demand/detail',['id'=>$v['object_id']]);
        }else{
          $v['type_name'] = "匹配专业推荐";
          $v['url'] = U('home/service/detail',['id'=>$v['object_id']]);
        }
      }
      $list[$i] = $v;
    }
    $this->assign("cou1",$count);
    $this->assign_list($list);
    $this->display();
  }	
}
