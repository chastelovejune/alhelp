<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
require '../lib/curd.trail.php';
class ServiceOrderController extends MemberBaseController {
    use \curd;
    public function index() {
		$order= M("service_order_view")->where(['member_id'=>session("id"),'package_id'=>['gt',0]])->order('id desc')->select();
		//dump($order);
		$this->orders=$order;
    	$this->display();
    }
    public function teacher(){

		$services = M("service")->join("INNER JOIN service_order ON service_order.service_id=service.service_id")->where(['service.member_id'=>session('id')])->field("service.*")->group("service.service_id")->page($_GET['page']?:1,20)->select();
		$cou=count($services);
		$this->assign("coun",$cou);
		$this->assign("services",$services);
    	$this->display();
    }

    public function show($id){
		//把此条记录设置为已读
		/*$re=M('service_order')->field("service_id")->find($id);
		$data['is_read']=1;*/
		$res=M('service_order')->where(['service_id'=>$re['service_id']])->save($data);
    	$o = M("service_order_view")->find($id);
        if (session("id") != $o['student_id'] && session("id") != $o['teacher_id']) {
            $this->error('你不是老师也不是学生',U('/member/index/studentCenter'));
        }
    	$this->o = $o;
        $this->is_stu = $o['student_id'] == session('id');
    	$s = M("service")->join("LEFT JOIN mb_member ON member_id=mb_member.id")->field("service.*,nickname,avatar,sign")->find($o['service_id']);
    	$s = parse_class($s);
	//取出基础信息
    	$this->s = $s;
    	if ($o['num'] > 0) {//是单品那种
	    	$plans = M("learning_teaching_plan")->where(['order_id'=>$id])->select();
	    	$this->plans = $plans;
			$this->assign("per",0);
    		if ($o['member_id'] == session("id")) {
				$role=0;//设置角色 0是学生 1是老师
				$this->assign("role",$role);
    			$this->display("single_student");
    		}else{
				$role=1;
				$this->assign("role",$role);
    			$this->display("single_teacher");
    		}
    	}else{

			$step= I("request.step") ?: 0;
			$this->step =$step;
			//查询初始的ID
				$sub = M("contract")->where(['order_id'=>$id,'status'=>1])->field('id')->buildSql();
				$per = M("learning_periods")->where("contract_id = {$sub}")->select();
			$this->per = $per[$step]['id'];
			//判断阶段是否结束
			/*
			$peri= I("request.per") ?:$per[$step]['id'];
			if($peri!=null) {
				echo $peri;
				$last_plan_end = M("learning_teaching_plan")->where(['learning_periods_id' => $peri])->
				order('id desc')->limit("0,1")->select();
				if (($last_plan_end[0]['set_time']) < (date('Y-m-d h:i:s', time()))) {
					//已经结束
					dump($last_plan_end[0]['set_time']);
					die;
					$this->error("上一个阶段还未结束");
				}
			}*/
            $sub = M("contract")->where(['order_id'=>$id,'status'=>1])->field('id')->buildSql();
            $periods = M("learning_periods")->where("contract_id = {$sub}")->select();
            foreach ($periods as $i => $p) {
                $p['plans'] = M("learning_teaching_plan")->where(['learning_periods_id'=>$p['id']])->select();
				//查找阶段的最后一条，看阶段是否结束
				$last_plan=M("learning_teaching_plan")->where(['learning_periods_id'=>$p['id']])->
					order('id desc')->limit("0,1")->select();
				if(($last_plan[0]['set_time'])<(date('Y-m-d h:i:s',time()))){
					//已经结束
					$p['end']=1;
				}
                $p['is_over'] = count($p['plans']) == $p['num'];
                $periods[$i] = $p;

            }

			$this->id=$id;
            $this->periods = $periods;
			//dump($periods[$this->step]);
            $this->period = $periods[$this->step];
    		if ($o['member_id'] == session("id")) {
				$role=0;//设置角色 0是学生 1是老师
				$this->assign("role",$role);
    			$this->display("package_student");
    		}else{
				$role=1;//设置角色 0是学生 1是老师
				$this->assign("role",$role);
    			$this->display("package_teacher");
    		}
    	}
    }
}