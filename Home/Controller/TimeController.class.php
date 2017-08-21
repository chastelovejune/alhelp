<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class TimeController extends HomeBaseController {

    public function classpay(){
	    $classes = M("learning_teaching_plan")->select();
		foreach($classes as $i => $c){
		   $time_now = date('Y-m-d H:i:s');
		   //$time = floor((strtotime($time_now)-strtotime($c['set_time']))/86400);
		   if(strtotime($time_now) > strtotime($c['set_time']) && $c['status']<3){
			   $c['status'] = 3;
			   M("learning_teaching_plan")->where(['id'=>$c['id']])->save($c);
			   $o = M("service_order_view")->where(['id'=>$c['order_id']])->find();
			   M("mb_member")->where(['id'=>$o['teacher_id']])->setInc("balance",$o['fee']*0.9);
			   $record = ['type'=>6,'income_type'=>0,'object_id'=>$o['id'],'from_member_id'=>$o['student_id'],'to_member_id'=>$o['teacher_id'],'balance'=>$o['fee']];
				M("payment_record")->add($record);
			   $record = ['type'=>6,'income_type'=>0,'object_id'=>$o['id'],'from_member_id'=>0,'to_member_id'=>$o['teacher_id'],'balance'=>$o['fee']];
				M("payment_record")->add($record);
				 $record = ['type'=>10,'income_type'=>0,'object_id'=>$o['id'],'from_member_id'=>$o['from_member_id'],'to_member_id'=>0,'balance'=>$o['fee']*0.1];
				M("payment_record")->add($record);
		   }
		}
	}

	public function datapay(){
	     $orders = M("order")->select();
		 foreach($orders as $i => $c){
		     $time_now = date('Y-m-d H:i:s');
			 if(strtotime($time_now) > strtotime($c['pay_time']) && $c['status']==2){
			  $c['status'] = 3;
			   M("order")->where(['id'=>$c['id']])->save($c);
			   $fee = $o['total'] * 0.9;
			   M("mb_member")->where(['id'=>$c['from_member_id']])->setInc("balance",$fee);
			   $record = ['type'=>5,'income_type'=>0,'object_id'=>$c['id'],'from_member_id'=>0,'to_member_id'=>$o['from_member_id'],'balance'=>$c['total']];
				M("payment_record")->add($record);
			  $record = ['type'=>10,'income_type'=>0,'object_id'=>$c['id'],'from_member_id'=>$o['from_member_id'],'to_member_id'=>0,'balance'=>$c['total']*0.1];
				M("payment_record")->add($record);
			 }
		 }
	}
}