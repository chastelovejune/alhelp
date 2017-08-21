<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class ClassPlanController extends MemberBaseController {

   public function add($id,$rid){
	   //创建学习规划  rid就是订单order ID
	   $student_id=M('service_order_view')->field('student_id')->find($rid);
	   //查出第几个阶段
	   $period=M('learning_periods')->field('title')->find($id);
	   if(IS_POST){
		   $data=$_POST;
		   $data['update']=date('Y-m-d h:i:s',time());
		   $re=M('learning_periods')->where("id=".$id)->save($data);
		   //老师拟定学习规划，向学生发送消息
		   $data['type']=3;
		   $data['to_member_id']=$student_id['student_id'];
		   $data['object_id']=$rid;//订单的ID，这里存的对象ID是否需靠考虑，信息的跳转 待考虑
		   $data['add_time']=date('Y-m-d H:i:s',time());
		   $data['content']="老师已经创建".$period['title']."教学方案";
		   $data['role']=0;
		   $data['periods']=$id;
		   M('message')->add($data);

		   if($re){
			   header("Content-type: text/html; charset=utf-8");
			   $this->redirect('member/serviceOrder/show',array('id'=>$rid),1,"提交成功..");
		   }
	   }
	   if(session("id") !=$student_id['student_id']){
		   $role=1;
	   }else{
		   $role=0;
	   }
	  	$re=M('learning_periods')->find($id);
	   $this->assign('role',$role);
	   $this->assign('re',$re);
	   $this->display("add");
   }

   public function yue($id,$per=0){//传入教学阶段
	   $o = M("service_order_view")->find($id);
	   $this->o = $o;
	   //选判断身份
	   if (session("id") != $o['student_id'] && session("id") != $o['teacher_id']) {
		   $this->error('你不是老师也不是学生',U('/member/index/studentCenter'));
	   }
	 if($o['num']>0){//如果大于0是单品
		$period=0;
	 }
	   if($o['num']==0){//套餐
		   $period=$per;
	   }
	   if (IS_POST) {
			$map['order_id']=$id;
		   $map['learning_periods_id']=$period;
		   $n=M("learning_teaching_plan")->where($map)->count();//查出第几次约课
			$plan = $_POST;
			$plan['member_id']= session("id");
			$plan['order_id'] = $id;
			$plan['learning_periods_id'] = $period;
			$plan['add_time'] = add_time();
		   	$plan['last_edit_member_id']=session('id');
			//M()->startTrans();

			$learn = M("learning_teaching_plan")->add($plan);
		   //发送信息给老师和学生
		   $student=$o['student_id'];
		   $teacher=$o['teacher_id'];
		   if(session("id")==$student){
			   //如果是学生 自己是学生
			   $m_id=session("id");
			   $re=M('mb_member')->find($m_id);
			   $data['to_member_id']=$o['teacher_id'];//发送的对象总是相反的
			   $data['type']=3;
			   $data['role']=1;//对方角色是老师
			   $data['object_id']=$id;
			   $data['periods']=$period;
			   $data['add_time']=date("Y-m-d h:i:s",time());
			   $data['content']=$re['nickname']."学生发起了第".($n+1)."次约课。";
			   M('message')->add($data);
		   }
		   if(session("id")== $teacher ){
			   //如果是老师 自己是老师
			   $m_id=session("id");
			   $re=M('mb_member')->find($m_id);
			   $data['to_member_id']=$o['student_id'];//发送对象是学生
			   $data['type']=3;
			   $data['role']=0;//学生角色
			   $data['object_id']=$id;
			   $data['periods']=$period;
			   $data['add_time']=date("Y-m-d h:i:s",time());
			   $data['content']=$re['nickname']."老师发起了第".($n+1)."次约课。";
			   M('message')->add($data);
		   }

		   if($learn){
			   header( 'Content-Type:text/html;charset=utf-8 ');
			 // dump($learn);
			   $this->redirect('member/serviceOrder/show', array('id' => $id), 1, '页面跳转中...');
		   }
		}

	   $this->display();
   }

public  function  edit($id,$per){
	$r=M('learning_teaching_plan')->find($id);
	$o = M("service_order_view")->find($r['order_id']);
	$this->o = $o;
	//选判断身份
	if (session("id") != $o['student_id'] && session("id") != $o['teacher_id']) {
		$this->error('你不是老师也不是学生',U('/member/index/studentCenter'));
	}
	if($o['num']>0){//如果大于0是单品
		$period=0;
	}
	if($o['num']==0){//套餐
		$period=$per;
	}
	//设置为已读
	$re=M('learning_teaching_plan')->find($id);
	$map['is_read']=1;
	M('learning_teaching_plan')->where("id=".$id)->save($map);
	$re=M('learning_teaching_plan')->where("id=".$id)->find();
		if($re['last_edit_member_id']==session('id')){
			//此人是发起者，发起者不具有确认签约功能，由此判断
			$is_start=1;
			$this->assign("is_start",$is_start);
		}
	if (IS_POST) {
		$plan = $_POST;
		$plan['status']=3;//如果修改过修改状态，修改中
		$plan['is_read']=0;
		$plan['last_edit_member_id']=session('id');
		$learn = M("learning_teaching_plan")->where("id=".$id)->save($plan);
		//发送消息
		$student=$o['student_id'];
		$teacher=$o['teacher_id'];
		if(session("id")==$student){
			//如果是学生  自己的身份
			$m_id=session("id");
			$re=M('mb_member')->find($m_id);
			$data['to_member_id']=$o['teacher_id'];//发送的对象总是相反的
			$data['type']=3;
			$data['role']=1;
			$data['object_id']=$r['order_id'];
			$data['periods']=$period;
			$data['add_time']=date("Y-m-d h:i:s",time());
			$data['content']=$re['nickname']."学生修改了约课计划";
			M('message')->add($data);
		}
		if(session("id")== $teacher ){
			//如果是老师   自己的身份
			$m_id=session("id");
			$re=M('mb_member')->find($m_id);
			$data['to_member_id']=$o['student_id'];
			$data['type']=3;
			$data['role']=0;//要向对方发送
			$data['object_id']=$r['order_id'];
			$data['periods']=$period;
			$data['add_time']=date("Y-m-d h:i:s",time());
			$data['content']=$re['nickname']."老师修改了约课计划";
			M('message')->add($data);
		}
		if ($learn) {
			header( 'Content-Type:text/html;charset=utf-8 ');
			$this->redirect('member/serviceOrder/show', array('id' => $r['order_id']), 1, '页面跳转中...');
		}
	}
	$this->assign('per',$period);//传入阶段
	$this->assign('re',$re);
	$this->display();
}
   public function set(){
     $data['name']='�ֹ�˾';
	  $data['set_time']='1899-12-31 04:20';
	   $data['target']='�ķ��ͺ�';
	    $data['listen_type']='�ǵķ��';
		 $data['member_id']='17';
		  $data['order_id']='28';
		   $data['learning_periods_id']='0';
		    $data['add_time']='2016-09-19 17:42:05';
			$res = M("learning_teaching_plan")->add($data);
   
   }
}