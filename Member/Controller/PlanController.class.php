<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class PlanController extends MemberBaseController {
	var $table_name = "learning_teaching_plan";

	public function add($id){
		if(IS_POST){
			$plan = $_POST;
			$plan['member_id']= session("id");
			$plan['order_id'] = $id;
			$plan['add_time'] = add_time();
			M()->startTrans();
			$audition_id = M("learning_teaching_plan")->add($plan);
		}
		}

	public function update()
	{
			$id=I('id');
			$data['status']=I('status');
			$periods=I('period');
		//修改状态  1生效  2是取消
			$re=M('learning_teaching_plan')->where("id=".$id)->save($data);
		//获取身份
			$r=M('learning_teaching_plan')->find($id);
			$o = M("service_order_view")->find($r['order_id']);
			$student=$o['student_id'];
			$teacher=$o['teacher_id'];
		//发送信息
		if($data['status']==1) {
			//如果是生效
			if (session("id") == $student) {
				//如果是学生 自己身份
				$m_id = session("id");
				$re = M('mb_member')->find($m_id);
				$data['to_member_id'] = $o['teacher_id'];//发送的对象总是相反的
				$data['type'] = 3;
				$data['role'] = 1;//对方身份是老师
				$data['object_id'] = $r['order_id'];
				$data['periods'] = $periods;
				$data['add_time'] = date("Y-m-d h:i:s", time());
				$data['content'] = $re['nickname'] . "学生已同意约课";
				M('message')->add($data);
				//把约课放在直播课里
				if($r['is_open']==1){
					$data_open['unified_id']=$o['unified_id'];
					$data_open['school_id']=$o['school_id'];
					$data_open['public_subject_id']=$o['public_subject_id'];
					$data_open['member_id']=$o['teacher_id'];
					$data_open['member_name']=$o['teacher_nickname'];
					$data_open['description']=$r['name'];
					$data_open['content_course']=$r['content'];
					$data_open['content_reference']=$r['content_reference'];
					$data_open['remarks']="";
					$data_open['add_time']=date("Y-m-d h:i:s",time());
					$data_open['open_class_time']=$r['set_time'];
					$data_open['image']=$o['image'];
					$data_open['classroom']=$r['listen_type'];
					$data_open['teacher']=$o['teacher_nickname'];
					M('open_class')->add($data_open);
				}
			}
			if (session("id") == $teacher) {
				//如果是老师
				$m_id = session("id");
				$re = M('mb_member')->find($m_id);
				$data['to_member_id'] = $o['student_id'];
				$data['type'] = 3;
				$data['role'] = 0;
				$data['object_id'] = $r['order_id'];
				$data['periods'] = $periods;
				$data['add_time'] = date("Y-m-d h:i:s", time());
				$data['content'] = $re['nickname'] . "老师已同意约课";
				M('message')->add($data);
				//把约课放在直播课里
				if($r['is_open']==1){
					$data_open['unified_id']=$o['unified_id'];
					$data_open['school_id']=$o['school_id'];
					$data_open['public_subject_id']=$o['public_subject_id'];
					$data_open['member_id']=$o['teacher_id'];
					$data_open['member_name']=$o['teacher_nickname'];
					$data_open['description']=$r['name'];
					$data_open['content_course']=$r['content'];
					$data_open['content_reference']=$r['content_reference'];
					$data_open['remarks']="";
					$data_open['add_time']=date("Y-m-d h:i:s",time());
					$data_open['open_class_time']=$r['set_time'];
					$data_open['image']=$o['image'];
					$data_open['classroom']=$r['listen_type'];
					$data_open['teacher']=$o['teacher_nickname'];
					M('open_class')->add($data_open);
				}
			}
		}else{
			//如果是取消
			if (session("id") == $student) {
				//如果是学生
				$m_id = session("id");
				$re = M('mb_member')->find($m_id);
				$data['to_member_id'] = $o['teacher_id'];//发送的对象总是相反的
				$data['type'] = 3;
				$data['role'] = 1;
				$data['object_id'] = $r['order_id'];
				$data['periods'] = $periods;
				$data['add_time'] = date("Y-m-d h:i:s", time());
				$data['content'] = $re['nickname'] . "学生已取消约课";
				M('message')->add($data);
			}
			if (session("id") == $teacher) {
				//如果是老师
				$m_id = session("id");
				$re = M('mb_member')->find($m_id);
				$data['to_member_id'] = $o['student_id'];
				$data['type'] = 3;
				$data['role'] = 0;
				$data['object_id'] = $r['order_id'];
				$data['periods'] = $periods;
				$data['add_time'] = date("Y-m-d h:i:s", time());
				$data['content'] = $re['nickname'] . "老师已取消约课";
				M('message')->add($data);
			}

		}
		if($re){
			$this->showTrueJson();
		}

	}
}