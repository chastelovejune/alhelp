<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class AccountController extends ApiBaseController { 
     
	 //获取银行卡列表
	public function banklist(){
	    $bank = C('bankcard_types');
		$this->success($bank);
	}

	//设置银行卡
	public function setbank(){
	    if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$bank = $post_data['bank'];
			$data['card_type'] = $bank['card_type'];
			$data['card_num'] = $bank['card_num'];
			$data['card_name'] = $bank['card_name'];
			$data['member_id'] = $bank['id'];
			$res = M("bankcard")->add($data);
			if($res){
			     $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}
		}
	}
  
   //提现账户列表
   public function withdrawacount($id){
      $bank = M("account")->where(['member_id'=>$id])->select();
	  $this->success($bank);
   }

   //账户余额
   public function balance($id){
      $balance = M("cash")->where(['member_id'=>$id])->field("balance_now")->find();	 
	  $this->success($balance);
   }

   public function ba($id){
    $ban = M("cash")->where(['member_id'=>$id])->field("balance_now")->find();
      $balance['balance_now'] = 200 + $ban['balance_now'];
	  $balmb['balance'] = $balance['balance_now'];

	  M("mb_member")->where(['id'=>$id])->save($balmb);
      $res = M("cash")->where(['member_id'=>$id])->save($balance);

       if($res){
			     $msg = array(
				   'status'=> $res,
				   
			   );
			   
			}else{
			 $msg = array(
				   'status'=> $res,
				   
			   );
			}
	 $this->success($msg);
   }

   //充值
   public function balanceadd($id){
	   $data['balance'] = I("request.balance");
	   $data['member_id'] = $id;
	   $res = M("recharge")->add($data);
	     $msg = array(
		    'id' => $res	 
		 );
	 $this->success($msg);
   }

      //充值成功
      public function balanced($id){	 

	  $m = M("mb_member")->find($id);
	  M("mb_member")->where(['id'=>$id])->setInc("balance",I("request.account"));

	  $cash['member_id'] = $id;
	  $cash['balance'] = I("request.account");
	  $cash['balance_now'] = $m['balance'];
	  $cash['balance_frozen'] = $m['balance_frozen'];
	  $payid = time().$id;
	  $cash['pay_id'] = $payid;
	  $cash['is_alipay'] = I("request.pay_mode");
	  $cashid = M("cash")->add($cash);

	  $record = M("cash")->where(['member_id'=>$id])->field("id as object_id,balance")->find();
	  $record['from_member_id'] = $id;
	  $record['to_member_id'] = $id;
	  $record['type'] = '2';
	  $record['income_type'] = '1';

	  M("payment_record")->add($record);

	  /* $record['from_member_id'] = $id;
	  $record['to_member_id'] = 0;
	  $record['type'] = '2';
	  $record['income_type'] = '0';
	  M("payment_record")->add($record);*/

	  $recharge['success'] = '1';
	  $recarge['cash_id'] = $cashid;
	  M("recharge")->where(['id'=>I("request.recharge_id")])->save($recharge);

       if($res){
			     $msg = array(
				   'status'=> $res,
				   
			   );
			   
			}else{
			 $msg = array(
				   'status'=> $res,
				   
			   );
			}
	 $this->success($msg);
   }

   //获取我的银行卡
   public function withdrawbank($id){
       $bank = M("bankcard")->where(['member_id'=>$id,'status = 1 OR status = 2'])->order('id desc')->find(); 
	   $balance = M("cash")->where(['member_id'=>$id])->field("id,balance_now")->find();	
	   $banklist = C('bankcard_types');
	   $i = $bank['card_type'];
	   $bank['card_type_text'] = $banklist[$i];
	   $bank['balance'] = $balance['balance_now'];
	   $bank['cash_id'] = $balance['id'];
	   $this->success($bank);
   }


   //提现
   public function getwithdraw($id){

	   $data['bankcard_id'] = I("request.bankcard_id");
	   $data['balance'] = I("request.balance");
	   $res = M("acount")->add($data);
	    if($res){
		   $msg = array(
			   'status' => '1', 
		   );
		   $this->success($msg);
	   }else{
	   $msg = array(
			   'status' => '0', 
		   );
		   $this->success($msg);
	   }
   }

   //提现记录
   public function withdrawlist($id,$status,$page){	

			$bank = M("bankcard")->where(['member_id'=>$id])->field("id")->select();
			
			if(is_array($bank)&&count($bank)>0){
		  foreach($bank as $key => $value){
		      $banks[$key] = $value['id']; 
		  }

          $bankid['bankcard_id'] = array('in',$banks);

		  if($status == -1){
		    $withdraw = M("acount")->page($page,10)->where([$bankid])->select();
			$count = M("acount")->where([$bankid])->count();
			$count = $count - ($page-1) * 10;
		  }else{
		    $withdraw = M("acount")->page($page,10)->where([$bankid,'status'=>$status])->select();
			$count = M("acount")->where([$bankid,'status'=>$status])->count();
			$count = $count - ($page-1) * 10;
		  }

			$data = array(
			   'list'=>$withdraw,
			   'count'=>$count
			);
	        $this->success($data);
			}
   }

   //收支明细
   public function recordlist($id,$type,$page){
	   if($type == -1){
			$record = M("payment_record")->where("from_member_id = '{$id}' OR to_member_id = '{$id}'")->select();
			$count = M("payment_record")->where("from_member_id = '{$id}' OR to_member_id = '{$id}'")->count();
			$count = $count - ($page-1) * 10;
			foreach($record as $i => $c){
				if($c['type'] == 2){
			   $ordnum = M("order")->field('order_number')->find($c['object_id']);
			   $record[$i]['order_number'] = $ordnum['order_number'];
			   
				}else{
				  $ordnum = M("recharge")->field('id')->find($c['object_id']);
			     $record[$i]['order_number'] = 'xzb'.$ordnum['id'];			     
				}

				$frommember = M("mb_member")->field('nickname')->find($c['to_member_id']);
			     $record[$i]['from_member'] = $frommember['nickname'];
                 $tomember = M("mb_member")->field('nickname')->find($c['from_member_id']);
			     $record[$i]['to_member'] = $tomember['nickname'];
			}
	   }else if($type == 0){
	        $record = M("payment_record")->where(["to_member_id"=>$id,"type = 2"])->select();
			$count = M("payment_record")->where(["to_member_id"=>$id,"type = 2"])->count();
			$count = $count - ($page-1) * 10;
			foreach($record as $i => $c){
			   $ordnum = M("order")->field('order_number')->find($c['object_id']);
			   $record[$i]['order_number'] = $ordnum['order_number'];
			   $frommember = M("mb_member")->field('nickname')->find($c['to_member_id']);
			   $record[$i]['from_member'] = $frommember['nickname'];
               $tomember = M("mb_member")->field('nickname')->find($c['from_member_id']);
			   $record[$i]['to_member'] = $tomember['nickname'];
			}
	   }else if($type == 1){
	        $record = M("payment_record")->where(["to_member_id"=>$id,"type = 2"])->select();
			$count = M("payment_record")->where(["to_member_id"=>$id,"type = 2"])->count();
			$count = $count - ($page-1) * 10;
			foreach($record as $i => $c){
			   $ordnum = M("order")->field('order_number')->find($c['object_id']);
			   $record[$i]['order_number'] = $ordnum['order_number'];
			   $frommember = M("mb_member")->field('nickname')->find($c['to_member_id']);
			   $record[$i]['from_member'] = $frommember['nickname'];
               $tomember = M("mb_member")->field('nickname')->find($c['from_member_id']);
			   $record[$i]['to_member'] = $tomember['nickname'];
			}
	   }else if($type == 2){
	        $record = M("payment_record")->where(["to_member_id"=>$id,"type = 0"])->select();
			$count = M("payment_record")->where(["to_member_id"=>$id,"type = 0"])->count();
			$count = $count - ($page-1) * 10;
			foreach($record as $i => $c){			 		
				 $ordnum = M("recharge")->field('id')->find($c['object_id']);
			     $record[$i]['order_number'] = 'xzb'.$ordnum['id'];			     		
				 $frommember = M("mb_member")->field('nickname')->find($c['to_member_id']);
			     $record[$i]['from_member'] = $frommember['nickname'];
                 $tomember = M("mb_member")->field('nickname')->find($c['from_member_id']);
			     $record[$i]['to_member'] = $tomember['nickname'];
			}
	   }else if($type == 3){
	        $record = M("payment_record")->where(["to_member_id"=>$id,"type = 1"])->select();
			$count = M("payment_record")->where(["to_member_id"=>$id,"type = 1"])->count();
			$count = $count - ($page-1) * 10;
	   }


	   $data = array(
		 'list' => $record,
		 'count' => $count
	   );

	   $this->success($data);
   }

 
}