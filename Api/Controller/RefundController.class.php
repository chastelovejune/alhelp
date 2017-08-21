<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class RefundController extends ApiBaseController {

    //学生申请退货
	public function goodsreturn($id){
		if(IS_POST){
		$order['refund_reason'] = I("request.reason");
		$order['refund_description'] = I("request.content");
		$order['status'] = 7;
		$res = M("order")->where(['id'=>$id])->save($order);
		if($res){
			$data = array(
				'status' => 1
			);
		}else{
			$data = array(
				'status' => 0
			);
		}
		$this->success($data);
		}
	}

   //学生寄出货
   public function devgoods(){
             M("transport")->add($_POST);
			M("order")->where(["id"=>I("request.order_id")])->save(["status"=>9]);
			M()->commit();
			$data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //获取学生退货信息
   public function reason($id){
	   $reson = M("order")->where(['id'=>$id])->field("refund_reason,refund_description")->find();
	   $this->success($reson);
   }

   //老师同意退货
   public function agree($id){
	   $order['status'] = 8;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //获取学生退货物流
   public function trans($id){
	   $trans = M("transport")->where(['order_id'=>$id,'type = 1'])->find();
	   $this->success($trans);
   }

   //老师确认收货,退钱给学生
   public function confirm($id){
	        $data['status'] = 10;
	        M("order")->where(['id'=>$id])->save($data); //修改订单状态

	        $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //生成流水
			
			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);

			$data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //老师拒绝退货
   public function refuse($id){
	   $order['status'] = 11;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //学生取消退货
   public function cel($id){
       $order['status'] = 3;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //学生申请退款
   public function money($id){
	   $order_time = M("order")->where(["id"=>$id])->getField("pay_time");
		$day = (time() - strtotime($order_time)) / (3600*24);
		if($day < 5){
	    $order['status'] = 5;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
		}else{
		    $order['status'] = 6; //修改订单状态
	       M("order")->where(['id'=>$id])->save($order);

          $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //生成流水

			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);	
        
          $data = array(
				'status' => 1
			);
		}
    	$this->success($data);
   }
      //老师同意退款
   public function moneyagree($id){
	    $order['status'] = 6; //修改订单状态
	   M("order")->where(['id'=>$id])->save($order);

        $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //生成流水

			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);
        
       $data = array(
				'status' => 1
			);
    	$this->success($data);
   }

}