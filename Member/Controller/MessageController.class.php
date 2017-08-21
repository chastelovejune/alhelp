<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class MessageController extends MemberBaseController {
	var $table_name="message";
    public function index() {
        //$role=cookie("role")?:0;//设置默认角色为学生
        $type = I("type",0);
        $this->type = $type;
        $map['type']=$type;
     //   $map['role']=$role;  <!--系统消息暂时不分角色>
        $map['to_member_id']=session("id");
        $ms = M("message")->where($map)->order('id desc')->page($_GET['page']?:1,20)->select();
        $cou_m=M("message")->where($map)->count();
        $this->ms = $ms;;
        $this->assign("cou_m",$cou_m);
        $this->is_student = intval(cookie('role'));
        $this->is_teacher = !intval(cookie('role'));
        $this->display();
    }
    public function noRead() {
        $role=(int)I("role")?:0;//设置默认角色为学生
        //取出系统消息并计算
        if($role==3) {
            $map['type'] = 0;
            $map['to_member_id'] = session("id");
            $map['readed'] = 0;
            $no_read_sys = M("message")->where($map)->count();
            $ms = M("message")->where($map)->select();
            foreach ($ms as $k => $v) {
                    $v['type_name'] = "系统消息";
                    $v['url'] = "#";
                $msm[]=$v;
            }
            //end
            $this->assign("no_read_sys", $no_read_sys);
            $this->assign("msm", $msm);
        }else {
            $map['role'] = $role;
            $map['to_member_id'] = session("id");
            $map['readed'] = 0;
            $map['type'] = array('gt', 0);
            $ms = M("message")->where($map)->order(array('readed' => asc, 'add_time' => desc))->page($_GET['page'] ?: 1, 20)->select();
            //组合跳转URL和类型
            foreach ($ms as $k => $v) {
                if ($v['type'] == 0) {
                    $v['type_name'] = "系统消息";
                    $v['url'] = "/member_service.html";
                }
                if ($v['type'] == 1) {
                    $v['type_name'] = "试听";
                    if ($v['role'] == 0) {
                        $v['url'] = U('member/audition/index');//学生
                    } else {
                        $v['url'] = U('member/service/audition', ['id' => $v['object_id']]);//老师
                    }
                }
                if ($v['type'] == 2) {
                    $v['type_name'] = "协议";
                    if ($v['role'] == 1) {
                        $v['url'] = U('member/contract/add', ['id' => $v['object_id']]);
                    } else {
                        $v['url'] = U('member/contract/confirm', ['id' => $v['object_id']]);
                    }
                }
                if ($v['type'] == 3) {
                    $v['type_name'] = "学习管理";
                    $v['url'] = U('member/serviceOrder/show', ['id' => $v['object_id']]);
                }
                if ($v['type'] == 4) {
                    $v['type_name'] = "评价";
                    $v['url'] = "";
                }
                if ($v['type'] == 5) {
                    if ($v['role'] == 1) {
                        $v['type_name'] = "老师投标";
                        $v['url'] = U('member/bid/teacher', ['id' => $v['object_id']]);
                    } else {
                        $v['type_name'] = "学生投标";
                        $v['url'] = U('member/bid/bidDesc', ['id' => $v['object_id']]);
                    }
                }
                if ($v['type'] == 6) {
                    $v['type_name'] = "支付";
                   
					if(strpos($v['content'], "辅导")){
					   if(strpos($v['content'], "签约")){
					    $v['url'] = U('member_ServiceOrder_teacher');
					   }else{
						   $v['url'] = U('member_ServiceOrder_show',['id'=>$v['object_id']]);
					   }
					}else if(strpos($v['content'], "提现")){
					    $v['url'] = U('member_index_minxi');
					}else if(strpos($v['content'], "充值")){
					   $v['url'] = U('member_index_minxi');
					}else{				
						$v['url'] = U('member_order');
					}					
                }
                if ($v['type'] == 7) {
                    $v['type_name'] = "仲裁";
                    $v['url'] = "";
                }
                if ($v['type'] == 8) {
                    $v['type_name'] = "网站广播";
                    $v['url'] = "member_message_index_type_8.html";
                }
                if ($v['type'] == 9) {
                    $v['type_name'] = "直播课";
                    $v['url'] = U('home/OpenClass/show', ['id' => $v['object_id']]);
                }
                if ($v['type'] == 10) {
                    if ($v['role'] == 1) {
                        $v['type_name'] = "匹配专业推荐";
                        $v['url'] = U('home/service/detail', ['id' => $v['object_id']]);
                    } else {
                        $v['type_name'] = "匹配专业推荐";
                        $v['url'] = U('home/demand/detail', ['id' => $v['object_id']]);
                    }
                }
                $msm[] = $v;
            }
            // dump($msm);
        }
        $cou_m=M("message")->where($map)->count();//计算总信息
        //计算未读信息-学生
        unset($map);
        $map['role']=0;
        $map['to_member_id']=session("id");
        $map['readed']=0;
        $no_read=M("message")->where($map)->count();
        //计算老师 未读信息
        unset($map);
        $map['role']=1;
        $map['to_member_id']=session("id");
        $map['readed']=0;
        $no_read_te=M("message")->where($map)->count();
        $this->no_read_te=$no_read_te;
    	$this->msm = $msm;
        $this->assign("no_read",$no_read);
        $this->assign("no_read_te",$no_read_te);
        $this->assign("cou_m",$cou_m);
        $this->assign("role", $role);
        $this->is_student = intval(cookie('role'));
        $this->is_teacher = !intval(cookie('role'));
    	$this->display();
    }
    //设置为已读
    public function read($id){
       $re= M('message')->where("id=".$id)->setField("readed",1);
        $this->showTrueJson($re);

    }
}