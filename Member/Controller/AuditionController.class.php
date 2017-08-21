<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class AuditionController extends MemberBaseController {
    public function index() {
        $auditions = M("audition")->join("LEFT JOIN service ON service.service_id=audition.service_id")->join("LEFT JOIN member ON member.id=service.member_id")->where(['audition.member_id'=>$this->m['id']])
            ->field("audition.*,service_type,service.description as service_description,service.school_id,service.unified_id,cost,service.public_subject_id,member.nickname,member.phone,member.qq,member.avatar")->order('audition.update_time desc,audition.id desc')->order("add_time desc")->select();
        $this->auditions = $auditions;
       // dump($auditions);
    	$this->display();
    }
    public function update($id){
        //这里把服务里的试听加入了直播课里
        M()->startTrans();
    	M("audition")->where(['id'=>$id])->save($_POST);
        if (I("request.status")==1) {//同意试听
            $audition = M("audition")->find($id);
            $m = $this->m;
            $member = M("mb_member")->find($audition['member_id']);
          //  M("message")->add(['type'=>1,'role'=>1,'to_member_id'=>session("id"),'object_id'=>$id,'content'=>"你已同意{$member['nickname']}的试听"]);
            M("message")->add(['type'=>1,'role'=>0,'to_member_id'=>$audition['member_id'],'object_id'=>$id,'content'=>"'{$m['nickname']}'已同意你的试听"]);
            //对学生发送短信
            if(!empty($member['phone'])){
            require "../lib/Sms.class.php";
            $sms = new \Sms();
            $sms->sendToServer($member['phone'],"({$m['nickname']})已同意你的试听,登录www.alhelp.net查看详情");}
            if (I("request.is_open")==1) {
                $service = M("service")->find($audition['service_id']);
                $class = ['school_id'=>$service['school_id'],'unified_id'=>$service['unified_id'],'public_subject_id'=>$service['public_subject_id'],'member_id'=>$service['member_id'],'member_name'=>$service['member_name'],'description'=>$audition['title'],'content_course'=>$audition['content'],'content_reference'=>$audition['content_reference'],'classroom'=>$audition['method'],'open_class_time'=>$audition['accept_time'],'image'=>$service['image'],'remarks'=>"同意试听:".$audition['remark'],'teacher'=>$service['member_name']];
                M("open_class")->add($class);
            }
        }
        M()->commit();
    	$this->showTrueJson();
    }
    public function teacher(){
    	$this->display();
    }
    public function ok($id){
        $audition = M("audition")->find($id);
        go_assert($audition,'没有这个试听');
        $this->audition = $audition;
        $service = M("service")->find($audition['service_id']);
        $service = parse_class($service);
        $this->service = $service;
        $this->display();
    }

}