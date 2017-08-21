<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class DemandDocumentController extends AdminBaseController{

  protected $table_name = "demand";
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
    $documents= M("demand")->where("demand_type=0")->select();

    $this->assign("documents",$document);
  }
  public function index()
  {
    $documents= M("demand");
    $count      = $documents->where("demand_type=0")->count();
    $Page       = new \Think\Page($count,20);
    $show       = $Page->show();// 分页显示输出
    $result = $documents->where("demand_type=0")->order("add_time desc")->limit($Page->firstRow.','.$Page->listRows)->select();
 
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
	  $map['demand_type']=array('eq',0);
	  $map['detail|member_name|demand_id|demand_year']=array('like',"%$keyword%","OR");
	  $documents= M("demand")->where($map)->select();
	foreach($documents as $doc)
	{
		$item=$this->convert($doc);
		$list[]=parse_class($item);
	}
    $this->assign_list($list);
    $this->display('index');
  }
  public function hold($demand_id,$reason)
  {//禁用
    
	$doc['status']=4;
    $documents= M("demand")->where("demand_type=0 and demand_id=$demand_id")->save($doc);
    //发送系统消息
	    $desc=M("demand")->field("description")->where("demand_type=0 and demand_id=$demand_id")->find();
	    $re=M("demand")->field("member_id")->where("demand_type=0 and demand_id=$demand_id")->find();
	    $data['member_id']=$re['member_id'];
	    $data['content']="你发布的服务[".$desc['description']."]不符合规范被管理员禁用了,原因:".$reason."!";
	    $data['time_create']=time();
   		 M("system_message")->add($data);
	$this->success("禁用成功",U("index"));
  }
   public function open($demand_id)
  {//取消禁用
	$doc['status']=0;
    $documents= M("demand")->where("demand_type=0 and demand_id=$demand_id")->save($doc);
     //发送系统消息
	    $desc=M("demand")->field("description")->where("demand_type=0 and demand_id=$demand_id")->find();
	    $re=M("demand")->field("member_id")->where("demand_type=0 and demand_id=$demand_id")->find();
	    $data['member_id']=$re['member_id'];
	    $data['content']="你发布的服务[".$desc['description']."]取消禁用又上架了!";
	    $data['time_create']=time();
   		 M("system_message")->add($data);
	$this->success("取消用成功",U("index"));
  }
  public function top($demand_id)
  {
  	$data['set_time']=date('Y-m-d H:i:s',time());

  	M("demand")->where("demand_type=0 and demand_id=$demand_id")->save($data);
  	$this->success("置顶成功",U("index"));
  }
  public function edit($demand_id){
  	$re=M("demand")->where("demand_type=0 and demand_id=$demand_id")->find();
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
  		$d['description']=$_POST['description'];
  		$d['cost']=$_POST['cost'];
  		//$d['qq']=$_POST['qq'];
  		//$d['wechat']=$_POST['wechat'];
  		$d['mobile']=$_POST['mobile'];
  		$d['detail']=$_POST['detail'];
		$d['demand_year']=$_POST['demand_year'];
  		$re=M('demand')->where("demand_id=".$demand_id)->save($d);
		if($re){
			$this->success("保存成功");
		}else{
			$this->error("保存失败，你没任何修改");
		}
  	}
  	$this->display('edit');
  }
}
