<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class ContractController extends MemberBaseController {
    public function add($id) {
        $re=M("service_order_view")->field("package_num")->where("id=".$id)->find();
        if($re['teacher_id']!=session("id")){
            $this->showFalseJson("只有老师才能拟定协议");
            exit;
        }
    	if (IS_POST) {
    		M()->startTrans();
            $n=0;
    		$contract = $_POST;
    		$contract['order_id'] = $id;
    		$contract_id = M("contract")->add($contract);
    		foreach (I("request.periods") as $value) {
    			$value['contract_id'] = $contract_id;
    			M("learning_periods")->add($value);
    		}
            //判断次数必须小于优惠包的次数
           foreach($contract['periods'] as $k =>$v){
               $n=$n+$v['num'];//累加次数
           }
            //dump($n);
           // dump((int)$re['package_num']);
            if((int)$re['package_num']!=$n){
                $this->showFalseJson("你的学习阶段的总次数必须等于优惠包的次数!");
                exit;
            }
            $member_id = M("service_order")->getFieldById($id,'member_id');

            M("message")->add(['type'=>2,'role'=>1,'to_member_id'=>session("id"),'object_id'=>$contract_id,'content'=>"你已为".id2name($member_id)."拟定协议"]);
            M("message")->add(['type'=>2,'role'=>0,'to_member_id'=>$member_id,'object_id'=>$contract_id,'content'=>id2name(session("id"))."老师已给你拟定协议, 请尽快确认"]);
            $member=M('mb_member')->find($member_id);
            if(!empty($member['phone'])){
                require "../lib/Sms.class.php";
                $sms = new \Sms();
                $sms->sendToServer($member['phone'],"你好".id2name(session("id"))."老师已给你拟定协议, 请尽快确认,登录www.alhelp.net查看详情");}
    		M()->commit();
    		$this->showTrueJson();
    	}

     //   dump($re);
        $this->assign("re", $re['package_num']);
    	$this->display();
    }
    public function confirm($id){
        $this->c = M("contract")->find($id);
        $this->periods = M("learning_periods")->where(['contract_id'=>$id])->select();
        $this->display();
    }
    public function update($id){
        M()->startTrans();
        M("contract")->where(['id'=>$id])->save($_POST);
        if ($_POST['status'] == 1) {//同意合同
            $contract = M("contract")->find($id);
            $service_order = M("service_order")->find($contract['order_id']);
            $service = M("service")->find($service_order['service_id']);
            M("message")->add(['type'=>2,'role'=>0,'to_member_id'=>session("id"),'object_id'=>$id,'content'=>"你已同意".$service['member_name']."的协议,请尽快付款"]);
            M("message")->add(['type'=>2,'role'=>1,'to_member_id'=>$service['member_id'],'object_id'=>$id,'content'=>$this->m['nickname']."已同意合同！请尽快到教学中心拟定学习规划，对学员进行辅导。
"]);
            $member= M('mb_member')->find($service['member_id']);
            if(!empty($member['phone'])){
                require "../lib/Sms.class.php";
                $sms = new \Sms();
                $sms->sendToServer($member['phone'],"您好!".$this->m['nickname']."已同意合同！请尽快到教学中心拟定学习规划，对学员进行辅导,登录www.alhelp.net查看详情。");}
        }
        M()->commit();
        $this->showTrueJson();
    }
    public function show($id){
        $this->c = M("contract")->find($id);
        $this->periods = M("learning_periods")->where(['contract_id'=>$id])->select();
        $this->display();   
    }
}