<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class ServiceQuestionController extends AdminBaseController{

  protected $table_name = "service";
  protected function convert($origin)
  {
	  $dest=$origin;
	  switch($origin['status'])
	  {
	  case 0:
		  $dest['status']="上架";
		  break;
	  case 1:
		  $dest['status']="未审核";
		  break;
	  case 2:
		  $dest['status']="删除";
		  break;
	  case 3:
		  $dest['status']="下架";
	     break;
	case 4:
		  $dest['status']="禁用";
		  break;
	  }
	  return $dest;
  }
  public function _initialize()
  {
    parent::_initialize();
    $documents= M("service")->where("service_type=1")->order("add_time desc")->select();
    $this->assign("documents",$document);
  }
  public function index()
  {
      $documents= M("service");
    $count      = $documents->where("service_type=1")->count();
    $Page       = new \Think\Page($count,20);
    $show       = $Page->show();// 分页显示输出
    $result = $documents->where("service_type=1")->order("add_time desc")->limit($Page->firstRow.','.$Page->listRows)->select();
 
	foreach($result as $doc)
	{
		$item=$this->convert($doc);
    $re=parse_class($item);
		$list[]=$re;
	}
    $this->assign("count",$count);
    $this->assign('page',$show);// 赋值分页输出
    $this->assign_list($list);
    $this->display();
  }
  public function search($keyword)
  {
	  $map['service_type']=array('eq',1);
	  $map['detail|member_name|service_id']=array('like',"%$keyword%","OR");
	  $documents= M("service")->where($map)->select();
	foreach($documents as $doc)
	{
		$item=$this->convert($doc);
		$list[]=parse_class($item);
	}
    $this->assign_list($list);
    $this->display('index');
  }
  public function hold($service_id,$reason)
  {//禁用
	$doc['status']=4;
    $documents= M("service")->where("service_type=1 and service_id=$service_id")->save($doc);
    //发送系统消息
	    $desc=M("service")->field("description")->where("service_type=1 and service_id=$service_id")->find();
	    $re=M("service")->field("member_id")->where("service_type=1 and service_id=$service_id")->find();
	    $data['to_member_id']=$re['member_id'];
	    $data['content']="你发布的服务[".$desc['description']."]不符合规范被管理员禁用了,原因:".$reason."!";
	    $data['time_create']=date('Y-m-d H:i:s',time());
		$data['type']=0;
		$data['readed']=0;
   		 M("message")->add($data);
	$this->success("禁用成功",U("index"));
  }
   public function open($service_id)
  {//取消禁用
	$doc['status']=0;
    $documents= M("service")->where("service_type=1 and service_id=$service_id")->save($doc);
     //发送系统消息
	    $desc=M("service")->field("description")->where("service_type=1 and service_id=$service_id")->find();
	    $re=M("service")->field("member_id")->where("service_type=1 and service_id=$service_id")->find();
	    $data['to_member_id']=$re['member_id'];
	    $data['content']="你发布的服务[".$desc['description']."]取消禁用又上架了!";
	    $data['time_create']=date('Y-m-d H:i:s',time());
		$data['type']=0;
		$data['readed']=0;
   		 M("message")->add($data);
	$this->success("启用成功",U("index"));
  }
  public function top($service_id)
  {
  	$data['set_time']=date('Y-m-d H:i:s',time());

  	M("service")->where("service_type=1 and service_id=$service_id")->save($data);
  	$this->success("置顶成功",U("index"));
  }
  public function edit($service_id){
  	$re=M("service")->where("service_type=1 and service_id=$service_id")->find();
	  $re=parse_class($re);
  	$this->assign('re',$re);
  	if(IS_POST){
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
  		$d['cost']=$_POST['cost'];
  		//$d['qq']=$_POST['qq'];
  	//	$d['wechat']=$_POST['wechat'];
  		$d['mobile']=$_POST['mobile'];
  		$d['detail']=$_POST['detail'];
  		$re=M('service')->where("service_id=".$service_id)->save($d);
		if($re){
			$this->success("保存成功");
		}else{
			$this->error("保存失败，你没任何修改");
		}
  	}
   // dump($re);
  	$this->display('edit');
  }
}
