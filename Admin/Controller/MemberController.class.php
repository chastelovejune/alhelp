<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class MemberController extends AdminBaseController{
  protected $table_name = "mb_member";
  public function index(){
    if($_REQUEST['key']){
      //这里查询是否需要分页，需要考虑下,没有分页的功能
      $key= $_REQUEST['key'];
      $map['nickname']=array('like',"%$key%");
      $list = D("MbMember")->where($map)->order('reg_time desc')->select();
     // $cou= D("MbMember")->where($map)->count();
    }else{
      $list = D("MbMember")->page(I('request.page'),20)->order('reg_time desc')->select();
      $cou= D("MbMember")->count();
    }
    $this->assign_list($list);
    $this->assign_page($cou);
    $this->display();
  }	
  public function addCircle($id){
    if (IS_POST) {
      $id = M("circle_member")->add(['member_id'=>$id,'circle_id'=>I("request.circle_id"),'member_type'=>0],[],true);
      $this->showTrueJson($id);
    }else{
      $m = D("MbMember")->find($id);
      $masters = M("circle_member")->join("circle on circle.id=circle_id")->where(['member_id'=>$id,'member_type'=>0])->getField("circle_name",true);
      $m['masters']=$masters;
      $circles = M("circle")->where("id in (select circle_id from circle_member where member_id=$id and member_type!=0) or id in (select circle_id from member_follow_circle where member_id=$id)")->getField('id,circle_name');
      $m['circles']=$circles;
      $this->showTrueJson($m);
    }
  }
  public function update($info){
    M("mb_member")->save($info);
    $this->showTrueJson();
  }
  public function realname(){
    $profiles = M("mb_profile")->join("LEFT JOIN mb_member ON mb_member.id=mb_profile.member_id")->where(["profile_type"=>0])->order("profile_id DESC")->getField("mb_profile.*,mb_member.nickname,mb_member.avatar");
    $this->assign("profiles",$profiles);
    $this->display();
  }
    public function identify(){
    $profiles = M("mb_profile")->join("LEFT JOIN mb_member ON mb_member.id=mb_profile.member_id")->where(["profile_type"=>1])->order("profile_id DESC")->getField("mb_profile.*,mb_member.nickname,mb_member.avatar");
    $this->assign("profiles",$profiles);
    $this->display();
  }


  public function reject($profile_id,$text=null){
    //拒绝实名认证
    $re=M("mb_profile")->where("profile_id=".$profile_id)->save(["status"=>2]);
    $p = M("mb_profile")->find($profile_id);
    //发送消息
    $data['type']=0;
    $data['to_member_id']=$p["member_id"];
    $data['content']="你的实名认证被管理员拒绝了，原因:".$text;
    $data['object_id']=$profile_id;
    $data['add_time']=date("Y-m-d h:i:s",time());
    M('message')->add($data);
    $this->showTrueJson("保存成功");
  }
  public function ok(){
    $id=$_POST['profile_id'];
    $re=M("mb_profile")->where("profile_id=".$id)->save(["status"=>1]);
    $p = M("mb_profile")->find($id);
    $data['is_realname']=1;
    $data['realname']=$p["realname"];
    $re1=M("mb_member")->where(["id"=>$p["member_id"]])->save($data);
   if($re&&$re1) {
     $this->success("操作成功");
   }
  }

  public function identifyReject(){
    M("mb_profile")->where($_POST)->save(["status"=>2]);
    $p = M("mb_profile")->find($_POST);
    $this->success("操作成功");
  }
  
  public function identifyOk(){
    M("mb_profile")->where($_POST)->save(["status"=>1]);
    $p = M("mb_profile")->find($_POST);
    M("mb_member")->where(["id"=>$p["member_id"]])->save(["is_identify"=>1]);
    $this->success("操作成功"); 
  }
  
  public function add()
  {
	  $m = M("mb_member")->find(I("request.id"));
	  $this->assign("m",$m);
	  $this->display();
  }
  
  public function edit()
  {
    if(IS_POST) {
      $info = $_POST;
      $info['password']=$this->hashPwd($info['pwd']);
      M("mb_member")->where('id='.$info['id'])->save($info);
      $this->success("操作成功",'admin_member_index');
    }
    $m = M("mb_member")->find(I("request.id"));
    $mb_group=M('member_group')->select();
    $this->assign("mb_group",$mb_group);
    $this->assign("m",$m);
    $this->display();
  }
  public function stop(){
    $id=I('id');
    if(M('mb_member')->where('id='.$id)->setField("status",2)){
      $this->success("禁用成功",'admin_member_index');
    }
  }
  public function start(){
    $id=I('id');
    if(M('mb_member')->where('id='.$id)->setField("status",0)){
      $this->success("启用成功",'admin_member_index');
    }
  }
  
}
