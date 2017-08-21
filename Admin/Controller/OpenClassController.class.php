<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class OpenClassController extends AdminBaseController{
  protected $table_name = "open_class";
  public function index(){
    $open_class = M("open_class");
    $count=$open_class->count();
    $Page=new \Think\Page($count,20);
    $show=$Page->show();
    $list=$open_class->field("open_class.*,IF(school_id > 0,0,IF(unified_id>0,1,2)) as type")->limit($Page->firstRow.','.$Page->listRows)->select();
    $this->assign('page',$show);// 赋值分页输出
    $this->assign("count",$count);
    $this->assign_list($list);
    $this->display();
  }
  public function hold($openclass_id,$reason)
  {//禁用
    $re=M("open_class")->where("$openclass_id=$openclass_id")->find();
    if($re['status']==4){
      $this->showFalseJson("已经禁用了");
    }
    $doc['status']=4;
    $documents= M("open_class")->where("open_class_id=$openclass_id")->save($doc);
    //发送系统消息
    $data['member_id']=$re['member_id'];
    $data['content']="你发布的直播课[".$re['description']."]不符合规范被管理员禁用了,原因:".$reason."!";
    $data['time_create']=time();
    M("system_message")->add($data);
    $this->showTrueJson("禁用成功");
  }
  public function open($openclass_id)
  {//取消禁用
    $re=M("open_class")->where("$openclass_id=$openclass_id")->find();
    if($re['status']==0){
      $this->showFalseJson("已经启用了");
    }
    $doc['status']=0;
    $documents= M("open_class")->where("open_class_id=$openclass_id")->save($doc);
    //发送系统消息
    $data['member_id']=$re['member_id'];
    $data['content']="你发布的直播课[".$re['description']."]被管理员启用了";
    $data['time_create']=time();
    M("system_message")->add($data);
    $this->showTrueJson("启用成功");
  }
  public function edit($openclass_id){
    $re=M("open_class")->where("open_class_id=$openclass_id")->find();
    $re=parse_class($re);
   // dump($re);
    $this->assign('re',$re);
    if(IS_POST){
      //dump($_POST);
      if($_POST['unified_id']!=0){
        $d['public_subject_id']=0;
        $d['school_id']=0;
        $d['unified_id']=$_POST['unified_id'];
      }
      if($_POST['school_id']!=0){
        $d['public_subject_id']=0;
        $d['school_id']=$_POST['school_id'];
        $d['unified_id']=0;
      }
      if($_POST['public_subject_id']!=0){
        $d['public_subject_id']=$_POST['public_subject_id'];
        $d['school_id']=0;
        $d['unified_id']=0;
      }
      $image = upload("image");
      if($image!=""){
        $d["image"] = $image;
      }

      $d['description']=$_POST['description'];
      $d['price']=$_POST['price'];
      $d['remarks']=$_POST['remarks'];
      $d['content_reference']=$_POST['content_reference'];
      $d['open_class_time']=$_POST['open_class_time'];
      $d['classroom']=$_POST['classroom'];
      $d['teacher']=$_POST['teacher'];
   // dump($image);
      $re=M('open_class')->where("open_class_id=".$openclass_id)->save($d);
      if($re){
        $this->success("保存成功");
      }else{
        $this->error("保存失败，你没任何修改");
      }
    }
    //dump($re);
    $this->display('edit');
  }
}