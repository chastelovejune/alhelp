<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class RefundController extends ApiBaseController {

    //ѧ�������˻�
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

   //ѧ���ĳ���
   public function devgoods(){
             M("transport")->add($_POST);
			M("order")->where(["id"=>I("request.order_id")])->save(["status"=>9]);
			M()->commit();
			$data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //��ȡѧ���˻���Ϣ
   public function reason($id){
	   $reson = M("order")->where(['id'=>$id])->field("refund_reason,refund_description")->find();
	   $this->success($reson);
   }

   //��ʦͬ���˻�
   public function agree($id){
	   $order['status'] = 8;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //��ȡѧ���˻�����
   public function trans($id){
	   $trans = M("transport")->where(['order_id'=>$id,'type = 1'])->find();
	   $this->success($trans);
   }

   //��ʦȷ���ջ�,��Ǯ��ѧ��
   public function confirm($id){
	        $data['status'] = 10;
	        M("order")->where(['id'=>$id])->save($data); //�޸Ķ���״̬

	        $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //������ˮ
			
			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);

			$data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //��ʦ�ܾ��˻�
   public function refuse($id){
	   $order['status'] = 11;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //ѧ��ȡ���˻�
   public function cel($id){
       $order['status'] = 3;
	   M("order")->where(['id'=>$id])->save($order);
       $data = array(
				'status' => 1
			);
			$this->success($data);
   }

   //ѧ�������˿�
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
		    $order['status'] = 6; //�޸Ķ���״̬
	       M("order")->where(['id'=>$id])->save($order);

          $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //������ˮ

			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);	
        
          $data = array(
				'status' => 1
			);
		}
    	$this->success($data);
   }
      //��ʦͬ���˿�
   public function moneyagree($id){
	    $order['status'] = 6; //�޸Ķ���״̬
	   M("order")->where(['id'=>$id])->save($order);

        $order = M("order")->find($id);
			$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record); //������ˮ

			M("mb_member")->where(['id' => $order['to_member_id']])->setInc("balance",$order['total']);
        
       $data = array(
				'status' => 1
			);
    	$this->success($data);
   }

}