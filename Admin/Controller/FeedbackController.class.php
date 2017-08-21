<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class FeedbackController extends AdminBaseController{
  protected $table_name = "feedback";
  public function index(){
    $list = M("feedback")->select();
    $this->assign_list($list); 
    $this->display();
  }	
  public function adminfeedback(){
  		$id=I('id');
  		$data['admin_feedback']=I('feedback');
  		$re=M('feedback')->where('id='.$id)->save($data);
        $mb=M('feedback')->field("member_id,member_nickname,content")->find($id);
      //发送系统消息
            $d['type']=0;
            $d['to_member_id']=$mb['member_id'];
            $d['object_id']=$id;
            $d['content']="你好，你反馈的消息[".$mb['content']."]管理员给出反馈:".$data['admin_feedback'];
            M('message')->add($d);
  		if($re){
  			$this->ajaxReturn($re);
  		}
  }
}
