<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class BidController extends MemberBaseController {
    public function teacher() {
		//如果有消息ID传过来设置成已读
		/*if(I('id')!=null) {
			$map['readed'] = 1;
			M('message')->where("id=" . I('id'))->save($map);
		}*/
		//我主动投标的
		$bids = M("bid")
			->join("LEFT JOIN demand on bid.demand_id=demand.demand_id")
			->join("LEFT JOIN mb_member ON mb_member.id=demand.member_id")
			->where(['bid.member_id'=>session("id")])
			->field("bid.*,nickname,avatar,demand.description as demand_description,school_id,demand_type,unified_id,public_subject_id")
			->order("bid.id desc")->page($_GET['page']?:1,20)->select();
		$cou= M("bid")
			->join("LEFT JOIN demand on bid.demand_id=demand.demand_id")
			->join("LEFT JOIN mb_member ON mb_member.id=demand.member_id")
			->where(['bid.member_id'=>session("id")])->count();
		$this->assign('cou',$cou);
		$this->assign('bids',$bids);
    	$this->display();
    }
    public function index() {
		//如果有消息ID传过来设置成已读
	/*	if(I('id')!=null) {
			$map['readed'] = 1;
			M('message')->where("id=" . I('id'))->save($map);
		}*/
        $demand=M('demand');
        //分页
        $count_bid =$demand->where(["demand.member_id"=>session("id")])->count();
        $re= $demand->where(["demand.member_id"=>session("id")])->order("add_time desc")->page($_GET['page']?:1,20)->select();
        foreach($re as $k=>$v){
            $result[]=parse_class($v);
        }
        $this->assign('count_bid',$count_bid);
    	$this->assign("result",$result);
    	$this->display();
    }
     public function bidDesc($id) {
    	$bids = M("bid")->join("LEFT JOIN service ON service.service_id=bid.service_id")->where("bid.demand_id=".$id)->field("bid.*,service.service_type,service.description as ser_title")->order("id desc")->select();
    	foreach ($bids as $k => $v) {
    		$res[]=parse_class($v);
    	}
    	//dump($res);
         $is_student = true;
        $this->is_student = $is_student;
    	$this->assign('bid',$res);
    	$this->display();
    }

	public function cart($id,$bid){
		$bids = M("bid")->where(['id'=>$bid])->find();
		$service = M("service")->join("LEFT JOIN mb_member ON service.member_id=mb_member.id")->where(['service_id'=>$id])->field("service.*,mb_member.avatar,mb_member.nickname")->find();
		$service['bid'] = $bid;
		$service['bid_price'] = $bids['bid_price'];
		$this->service = $service;
		$this->display();
	}

	public function confirm($id,$cid,$bid){
		$adds = M("address")->where(["member_id"=>session("id")])->select();
		foreach ($adds as $i => $add) {
			$add["province_name"] = M("area")->where(["id"=>$add["province"]])->getField("title");
			$add["city_name"] = M("area")->where(["id"=>$add["city"]])->getField("title");
			$add["area_name"] = M("area")->where(["id"=>$add["area"]])->getField("title");
			$adds[$i]=$add;	
		}
		$this->assign("adds",$adds);

		$bids = M("bid")->where(['id'=>$bid])->find();
		$ser = M("service")->join("LEFT JOIN mb_member ON service.member_id=mb_member.id")->where(['service_id'=>$id])->field("service.*,mb_member.avatar,mb_member.nickname")->find();
		$dem = M("demand")->where(['demand_id'=>$bids['demand_id']])->field("demand.advance_payment")->find();
		$ser['count'] = $cid;
		$ser['bid'] = $bid;
		$ser['bid_price'] = $bids['bid_price'];
		$ser['advance_payment'] = $dem['advance_payment'];
		$this->ser = $ser;
		$this->display();
	}

	public function order(){
	   $address = M("address")->find(I("request.address_id"));
	   $service = M("service")->find(I("request.id"));
	   $bid = M("bid")->where(['id'=>I("request.bid")])->find();
	   $common = array("to_member_id"=>session("id"),"province"=>$address["province"],"city"=>$address["city"],"area"=>$address["area"],"address"=>$address["address"],"phone"=>$address["phone"]);
	   $order = array("from_member_id"=>$service['member_id'],"order_number"=>"xzb".rand(),"total"=>$bid["bid_price"]*I("request.count"),'service_id'=>$service['service_id'],'count'=>I("request.count"),'bid_id'=>I("request.bid"));
				$order = array_merge($order,$common);
				$id = M("order")->add($order);
				$order_ids[] = $id;
	    $bid['status'] = 2;
		M("bid")->where(['id'=>I("request.bid")])->save($bid);
		$m = M("mb_member")->find(session('id'));
		//发送给老师
		M("message")->add(['type'=>5,'role'=>1,'to_member_id'=>$service['member_id'],'object_id'=>$bid['demand_id'],'content'=>'"'.$m['nickname'].'"同意了你的【'.$service['description'].'】投标申请']);

		M()->commit();
		$this->showTrueJson($order_ids);
	}


	public function pay(){

		$ids = I("request.ids");//取订单号集合		
		if (!is_array($ids) || count($ids) == 0) {
			$this->showFalseJson("没有orderid");
		}//集合中无内容
		$order_id = $ids[0];
		$order = M("order")->where(['id'=>$order_id])->find();
		$bids = M("bid")->where(['id'=>$order['bid_id']])->find();
        $dem = M("demand")->where(['demand_id'=>$bids['demand_id']])->find();
		$odtotal = $order['total'];
		$total = $odtotal - $dem['advance_payment'];
		$this->assign("total",$total);
		
		if($total > 0){ //投标价>托付费用
		
		if (I("request.pay_type")==2) {
			//余额支付
			$m = $this->m;
			if ($m["balance"] < $total) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$total);
				//更新订单
				M("order")->where(["id"=>$order_id])->save(["status"=>1,"pay_time"=>add_time()]);
				//交易记录					
				$record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$total];
				M("payment_record")->add($record);//更新到record表
				//修改托付费用。冻结金额
				if($dem['is_payed'] == 1){
                M("mb_member")->where(["id"=>$m['id']])->setDec("balance_frozen",$dem['advance_payment']);
					//交易记录
                $record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$dem['advance_payment']];
				M("payment_record")->add($record);//更新到record表
				}
				$dem['is_payed'] = 0;
				$dem['advance_payment'] = 0;
				M("demand")->where(['demand_id'=>$dem['demand_id']])->save($dem);			
			
				$ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();

			 //生成系统消息
		       $message['type'] = 6;
		       $message['readed'] = 0;
		       $message['to_member_id'] = $ms['id'];
		       $message['object_id'] = $order_id;
		       $message['content'] = '你好！'.$m['nickname'].'已托付款项'.$odtotal.'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		       $message['role'] = 1;
		       M("message")->add($message);

				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }
				
				M()->commit();
				$this->showTrueJson();
			}
		}else if (I("request.pay_type") == 1) {
			//支付宝
			$total = $total * 100;
			$out_trade_no = pay_id($order_id);
			$ds = M("service")->join("LEFT JOIN `order` ON `order`.service_id=service.service_id")->where(['`order`.id'=>$order_id])->getField('description',true);
			$subject = implode(",", $ds);
			$html = go_alipay(U('/member/bid/alipay'),$out_trade_no,$subject,$total);
			$this->show($html);
		}else{
			//微信
			$total = $total * 100;
			$out_trade_no = time()."_dataBid";
		    $code = go_wxPay($out_trade_no,$total,$order_id);
            $this->showTrueJson(['code'=>$code,'id'=>$order_id,'type'=>'dataBid']);
		}

		}else if($total == 0){ //投标价=托付费用			
			 //解冻冻结金额
		   M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$dem['advance_payment']);
		  
			//修改订单状态
			M("order")->where(["id"=>$order_id])->save(["status"=>1,"pay_time"=>add_time()]);				
			$fee = M('order')->getFieldById($order_id,'total');
			$record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$fee];
			M("payment_record")->add($record);//更新到record表

			 //修改托付费用
		   $dem['advance_payment'] = 0;
		   $dem['is_payed'] = 0;
		   M("demand")->where(['demand_id'=>$bids['demand_id']])->save($dem);

			M()->commit();
				$ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();

				 //生成系统消息
		       $message['type'] = 6;
		       $message['readed'] = 0;
		       $message['to_member_id'] = $ms['id'];
		       $message['object_id'] = $order_id;
		       $message['content'] = '你好！'.$m['nickname'].'已托付款项'.$odtotal.'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		       $message['role'] = 1;
		       M("message")->add($message);

				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }
		    $this->showTrueJson();

		}else{ //投标价 < 托付费
			 //解冻冻结金额
		   M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$dem['advance_payment']);

		   $fee = M('order')->getFieldById($order_id,'total');
		   	$record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$fee];

			M("payment_record")->add($record);//更新到record表

		   //修改托付费用
		   $dem['advance_payment'] = 0;
		   $dem['is_payed'] = 0;
		   M("demand")->where(['demand_id'=>$bids['demand_id']])->save($dem);
			 //返回余额
			 $be = abs($total);
             M("mb_member")->where(['id'=>session('id')])->setInc("balance",$be);
			//修改订单状态
			M("order")->where(["id"=>$order_id])->save(["status"=>1,"pay_time"=>add_time()]);
				M()->commit();
			
		
			$ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();

			 //生成系统消息
		       $message['type'] = 6;
		       $message['readed'] = 0;
		       $message['to_member_id'] = $ms['id'];
		       $message['object_id'] = $order_id;
		       $message['content'] = '你好！'.$m['nickname'].'已托付款项'.$odtotal.'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		       $message['role'] = 1;
		       M("message")->add($message);

				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }

			M("payment_record")->add($record);//���µ�record��

		    $this->showTrueJson();

		}		
	}

	function alipay($out_trade_no){
		
		$order_id = get_pay_id($out_trade_no);	
		$order = M("order")->where(['id'=>$order_id])->find();
		$verify_result = validate_alipay();

		if($verify_result) {//验证成功

            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
            $bids = M("bid")->where(['id'=>$order['bid_id']])->find();
            $dem = M("demand")->where(['demand_id'=>$bids['demand_id']])->find();
			$fee = $order['total'] - $dem['advance_payment'];

	            //生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //更新order
	            M("order")->where(['id'=>$order_id])->save(['status'=>1,'cash_id'=>$cash_id,'pay_time'=>add_time()]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>5,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $fee,'object_id'=>$order_id,
	                );
	            $record_id = M("payment_record")->add($record);//first

				$cash['payment_record_id'] = $record_id;
		        M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second

				//修改托付费用。冻结金额
				if($dem['is_payed'] == 1){
                M("mb_member")->where(["id"=>$member['id']])->setDec("balance_frozen",$dem['advance_payment']);

				//交易记录
                $record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$dem['advance_payment']];
				M("payment_record")->add($record);//更新到record表

				$dem['is_payed'] = 0;
				$dem['advance_payment'] = 0;
				M("demand")->where(['demand_id'=>$dem['demand_id']])->save($dem);
				
				}
				
				$ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();

				 //生成系统消息
		       $message['type'] = 6;
		       $message['readed'] = 0;
		       $message['to_member_id'] = $ms['id'];
		       $message['object_id'] = $order_id;
		       $message['content'] = '你好！'.$m['nickname'].'已托付款项'.$order['total'].'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		       $message['role'] = 1;
		       M("message")->add($message);

				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }

            M()->commit();
		}else{

			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/member/order'));
	}
		

	public function buy($bid){
	
		$bids = M("bid")->find($bid);
		$id = $bids['service_id'];
        $did = $bids['demand_id'];
		$s = M("service")->find($id);
		$d = M("demand")->find($did);
		if (IS_POST) {
			$order = $_POST;
			$order['service_id'] = $id;
			$order['member_id'] = session("id");
			$order['bid'] = $bid;
			$order_id = M("service_order")->add($order);

			 //修改投标状态
			$bids['status'] = 2;
		    M("bid")->where(['id'=>$bid])->save($bids);
			$service = M("service")->find($id);	
			//$total = D('ServiceOrder')->fee($order_id);
			$total = m("service_order_view")->field('fee')->find($order_id);
			$total = $total['fee'];
			$fee = $total - $d['advance_payment'];
			if($fee == 0){
				//修改用户冻结费用

				M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$d['advance_payment']);

				//生成交易记录

                $record = ['type'=>6,'income_type'=>0,'object_id'=>$order_id,'balance'=>$total];
				M("payment_record")->add(array_merge($record,['from_member_id'=>session('id'),'to_member_id'=>0]));


				$d['advance_payment'] = 0;
				$d['is_payed'] = 0;
				M("demand")->where(['id'=>$d['demand_id']])->save($d);
				//更新订单
				M("service_order")->where(['id'=>$order_id])->save(['status'=>1]);
				
              $ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$total.'元费用到新助邦，您可以开始辅导学员了！请尽快拟定ta的学习规划！登录www.alhelp.net查看详情');
		     }
		
                M()->commit();
				$this->showTrueJson();
			}else if($fee > 0){

			   if ($_POST['pay_type'] == 2) {//余额
				if ($this->m['balance'] < $fee) {
					$this->showFalseJson('余额不足');
				}
				M()->startTrans();
				//减钱
				M("mb_member")->where(['member_id'=>session("id")])->setDec("balance",$fee);
				//更新订单
				M("service_order")->where(['id'=>$order_id])->save(['status'=>1]);
				//生成record
				$record = ['type'=>6,'income_type'=>0,'object_id'=>$order_id,'balance'=>$fee];
				
				M("payment_record")->add(array_merge($record,['from_member_id'=>session('id'),'to_member_id'=>0]));

                 if($d['is_payed'] == 1){
				 M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$d['advance_payment']);
				 $record = array('type'=>6,'income_type'=>0,
                'from_member_id'=>session("id"),'to_member_id'=>0,
                'balance' => $dem['advance_payment'],'object_id'=>$order_id
                );
				$d['advance_payment'] = 0;
				$d['is_payed'] = 0;
				M("demand")->where(['demand_id'=>$d['demand_id']])->save($d);
				 }

				   $ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$total.'元费用到新助邦，您可以开始辅导学员了！请尽快拟定ta的学习规划！登录www.alhelp.net查看详情');
		     }

				M()->commit();
				$this->showTrueJson();

			}else if ($_POST['pay_type'] == 1) {//支付宝
				$fee = $fee * 100;
				$html_text = go_alipay(U('/member/bid/alipayb'),
					pay_id($order_id),
					$service['description'],$fee);
				$this->show($html_text);
			}else{//微信
				$fee = $fee * 100;
				$out_trade_no = time()."_classBid";
    		    $code = go_wxPay($out_trade_no,$fee,$order_id);
                $this->showTrueJson(['code'=>$code,'id'=>$order_id,'type'=>'classBid']);
			}
			}else{
			  //修改用户冻结费用
				M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$d['advance_payment']);
				

				//增加用户余额
				$be = abs($fee);
				M("mb_member")->where(['id'=>session('id')])->setInc("balance",$be);
				//生成交易记录
                $record = ['type'=>6,'income_type'=>0,'object_id'=>$order_id,'balance'=>$total];
				M("payment_record")->add(array_merge($record,['from_member_id'=>session('id'),'to_member_id'=>0]));

				$d['advance_payment'] = 0;
				$d['is_payed'] = 0;
				M("demand")->where(['id'=>$d['demand_id']])->save($d);
			   
			     $ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$total.'元费用到新助邦，您可以开始辅导学员了！请尽快拟定ta的学习规划！登录www.alhelp.net查看详情');
		     }			   
                M()->commit();
				$this->showTrueJson();
			}
			
		}else{
			$this->s = $s;
			$this->d = $d;
			$this->b = $bids;
			$this->display();
		}
	}



	public function alipayb($out_trade_no){
		$order_id = get_pay_id($out_trade_no);
		$order = M("service_order")->find($order_id);
		$verify_result = validate_alipay();

		if($verify_result) {//验证成功
            M()->startTrans();
            $total = m("service_order_view")->field('fee')->find($order_id);
			$total = $total['fee'];
            $member = M('mb_member')->find(session("id"));
			$bid = M("bid")->where(['id'=>$order['bid']])->find();
			$dem = M("demand")->where(['demand_id'=>$bid['demand_id']])->find();

            //生成cash
            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
            //更新order
            M("service_order")->where(['id'=>$order_id])->save(['status'=>1,'cash_id'=>$cash_id]);
            //生成record			
            M("payment_record")->add($record);
            $record = array('type'=>6,'income_type'=>1,
                'from_member_id'=>session("id"),'to_member_id'=>0,
                'balance' => $fee,'object_id'=>$order_id
                );
            $record_id = M("payment_record")->add($record);
			$cash['payment_record_id'] = $record_id;
		    M("cash")->where(['id'=>$cash_id])->save($cash);

			$record = array('type'=>6,'income_type'=>0,
             'from_member_id'=>session("id"),'to_member_id'=>session('id'),
              'balance' => $fee,'object_id'=>$order_id
              );

			if($dem['is_payed'] == 1){
			 M("mb_member")->where(['id'=>session('id')])->setDec("balance_frozen",$d['advance_payment']);
			  $record = array('type'=>6,'income_type'=>0,
                'from_member_id'=>session("id"),'to_member_id'=>0,
                'balance' => $dem['advance_payment'],'object_id'=>$order_id
                );	
				$dem['advance_payment'] = 0;
				$dem['is_payed'] = 0;
				M("demand")->where(['demand_id'=>$dem['demand_id']])->save($dem);
			}

			  $ms = M("mb_member")->where(['id'=>$bids['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$total.'元费用到新助邦，您可以开始辅导学员了！请尽快拟定ta的学习规划！登录www.alhelp.net查看详情');
		     }

            M()->commit();
		}else{
            M("service_order")->where(['id'=>$order_id])->save(['status'=>2]);

			E('还没考虑那种情况会验证失败');

		}
		redirect(U('/member/ServiceOrder'));
	}
		
}