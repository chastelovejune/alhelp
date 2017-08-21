<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class EntrustController extends ApiBaseController {

		//�и�����
	public function add(){
		if(IS_POST){
		//����и�����
		$tuofee['advance_payment'] = I("request.total");
		$tuofee['is_payed'] = 1;
		M("demand")->where(['demand_id'=>I("request.id")])->save($tuofee);

		//�������
		$m = M('mb_member')->where(['id'=>I("request.member_id")])->field('balance_frozen')->find();
		$m['balance_frozen'] = $m['balance_frozen'] + I("request.total");
		M('mb_member')->where(['id'=>I("request.member_id")])->save($m);

		//���֧��
		if(I("request.payMode")==3){
			  $record = array('type'=>3,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => I("request.total"),'object_id'=>I("request.id"),
	        );
	            M("payment_record")->add($record);
		}else{ //΢�š�֧����֧��
		    $m = M("mb_member")->find(I("request.member_id"));  //����cash
			$cash['member_id'] = I("request.member_id");
			$cash['balance'] = I("request.total");
			$cash['balance_now'] = $m['balance'];
			$cash['pay_id'] = I("request.pay_id"); 
			if(I("request.payMode") == '1'){
		      $cash['is_alipay'] = '1';
		    }else{
			  $cash['is_alipay'] = '0';
		    }
		   M("cash")->add($cash);

		    $record = array('type'=>3,'income_type'=>1,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => I("request.total"),'object_id'=>I("request.id"),
	        );
	            M("payment_record")->add($record);
			  $record = array('type'=>3,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => I("request.total"),'object_id'=>I("request.id"),
	        );
	            M("payment_record")->add($record);
		}

		   $msg = array(
				'status'=> '1'
			);
		   $this->success($msg);
		}
	}

	//��ȡ�и����ö���
	public function enlist($id,$page){
		$tuo = M("demand")->table('demand demand,mb_member mb_member')->where(['member_id'=>$id,'demand.member_id = mb_member.id'])->order('demand_id desc')->page($page,10)->field("mb_member.nickname,demand.*")->select();
		$count = M("demand")->where(['member_id'=>$id])->count();
		$count = $count - ($page-1) * 10;

		foreach($tuo as $index => $d){
           $d = parse_class($d);
           $tuo[$index]=$d;
         }

		$data = array(
			'list' => $tuo,
			'count' => $count
		);
		$this->success($data);
	}

	//�ⶳ�и�����
	public function unfreezed(){
		if(IS_POST){
			//���ٶ�����������
			$m = M("mb_member")->where(['id'=>I("request.id")])->field("balance,balance_frozen")->find();
			$m['balance_frozen'] = $m['balance_frozen'] - I("request.total");
			$m['balance'] = $m['balance'] + I("request.total");
			M("mb_member")->where(['id'=>I("request.id")])->save($m);

			//�и�����Ϊ0
			$demand['advance_payment'] = 0;
			$demand['is_payed']=0;
			M("demand")->where(['id'=>I("request.demand_id")])->save($demand);
           $msg = array(
				'status'=> '1'
			);
		   $this->success($msg);
		}
	}
}