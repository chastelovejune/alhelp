<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
use \Home\Model\CartModel;
use \Think\Log;
class OrderController extends HomeBaseController {
	
	public function add(){
		     
		M()->startTrans();
		//一个卖家的多个商品,一个父订单,多个子订单, 父订单出现原因是一次买了多个你的商品,同时余罪4和5,退货是子订单, 参照淘宝
		// 一个子订单可以多个商品
		//多个卖家生成多个单, 分别算运费
		$members = CartModel::buildIndex();
		$address = M("address")->find(I("request.address_id"));
		Log::write("address:".print_r($address,true)."members".print_r($members,true));
		$common = array("to_member_id"=>session("id"),"province"=>$address["province"],"city"=>$address["city"],"area"=>$address["area"],"address"=>$address["address"],"phone"=>$address["phone"]);
		$order_ids = array();
		foreach ($members as $index => $m) {
			if (count($m["carts"]) > 1) {//一个卖家,多个商品情况,有父订单
				$total = 0;
				foreach ($m['carts'] as $i => $cart) {
					$order = array("from_member_id"=>$m['id'],"order_number"=>"xzb".rand(),"total"=>$cart["cost"]*$cart['count'],'service_id'=>$cart['service_id'],'count'=>$cart['count']);
					$order = array_merge($order,$common);
					$ids[] = M("order")->add($order);
					$total += $order['total'];
				}
				$porder = array("from_member_id"=>$m['id'],"order_number"=>"xzb".rand(),"total"=>$total,"sub_order_ids"=>json_encode($ids));
				$porder = array_merge($porder,$common);
				Log::write("porder:".print_r($porder,true));
				$pid = M("order")->add($porder);
				M("order")->where(["id" => ["in",$ids]])->save(["pid"=>$pid]);
				$order_ids = array_merge($order_ids,$ids);
			}else{
				$cart = $m["carts"][0]; 
				$order = array("from_member_id"=>$m['id'],"order_number"=>"xzb".rand(),"total"=>$cart["cost"]*$cart['count'],'service_id'=>$cart['service_id'],'count'=>$cart['count']);
				$order = array_merge($order,$common);
				Log::write("只有一个单, 数据是".print_r($order,true));
				$id = M("order")->add($order);
				$order_ids[] = $id;
			}
		}
		$cart_ids = I("request.cart_ids");
		if (array_count($cart_ids) > 0) {
			M("cart")->where(['id' => ['in', $cart_ids]])->delete();
		}else{
			M("cart")->where(['member_id'=>session("id")])->delete();
		}
		M()->commit();
		$this->showTrueJson($order_ids);
	}

	public function confirm(){
		$this->mustLogin();
		$adds = M("address")->where(["member_id"=>session("id")])->select();
		foreach ($adds as $i => $add) {
			$add["province_name"] = M("area")->where(["id"=>$add["province"]])->getField("title");
			$add["city_name"] = M("area")->where(["id"=>$add["city"]])->getField("title");
			$add["area_name"] = M("area")->where(["id"=>$add["area"]])->getField("title");
			$adds[$i]=$add;	
		}
		$this->assign("adds",$adds);
		$members = CartModel::buildIndex();
		$this->assign("members",$members);
		foreach ($members as $member) {
			foreach ($member['carts'] as $c) {
				$fee += $c['cost'] * $c['count'];
			}
		}
		$this->fee = $fee;
		$this->display();
	}

	public function pay(){
		Log::write('支付方式'.I("request.pay_type"),'INFO');
		$ids = I("request.ids");//取订单号集合
		if (!is_array($ids) || count($ids) == 0) {
			$this->showFalseJson("没有orderid");
		}//集合中无内容
		foreach ($ids as $id) {
			$order = M("order")->find($id);
			if ($order['status'] != 0) {
				E("订单状态错误");
			}
			$total += $order['total'];
		}//得到订单价格总和
		
		$this->assign("total",$total);
		if (I("request.pay_type")==2) {
			//余额支付
			$m = $this->m;
			if ($m["balance"] < $total) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$total);
				//更新余额(扣钱)
				M("order")->where(["id"=>["in",$ids]])->save(["status"=>1,"pay_time"=>add_time()]);
				//更新到订单表
				foreach ($ids as $order_id) {
					$fee = M('order')->getFieldById($order_id,'total');
					$record = ['type'=>5,'income_type'=>0,'object_id'=>$order_id,'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$fee];
					M("payment_record")->add($record);//更新到record表

					$tm = M('order')->find($order_id);
			  	 	$ms = M("mb_member")->where(['id'=>$tm['from_member_id']])->find();

					//生成系统消息
		            $message['type'] = 6;
		            $message['readed'] = 0;
		            $message['to_member_id'] = $ms['id'];
		            $message['object_id'] = $order_id;
		            $message['content'] = '你好！'.$m['nickname'].'已托付款项'.$tm['total'].'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		            $message['role'] = 1;
		            M("message")->add($message);

					//短信通知老师
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }
				}
				M()->commit();
				
			    
				$this->showTrueJson();
			}
		}else if (I("request.pay_type") == 1) {
			//支付宝
			$total = $total * 100;
			$out_trade_no = pay_id(json_encode($ids));
			$ds = M("service")->join("LEFT JOIN `order` ON `order`.service_id=service.service_id")->where(['`order`.id'=>['in',$ids]])->getField('description',true);
			$subject = implode(",", $ds);
			$html = go_alipay(U('/home/Order/alipay'),$out_trade_no,$subject,$total);
			$this->show($html);
		}else{
			//微信
			$total = $total * 100;
			$out_trade_no = time()."_order_no";
		    $code = go_wxPay($out_trade_no,$total,json_encode($ids));
            $this->showTrueJson(['code'=>$code,'id'=>$ids[0],'type'=>'order']);
		}
	}
	function alipay($out_trade_no){
		$_tmp = get_pay_id($out_trade_no);
		$ids = json_decode($_tmp);
		$orders = M("order")->where(['id'=>['in',$ids]])->select();
		$verify_result = validate_alipay();
		go_log($verify_result);
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
            foreach ($orders as $order) {
            	$order_id  =$order['id'];
            	$fee = $order['total'];
	            //生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //更新order
	            M("order")->where(['id'=>$order_id])->save(['status'=>1,'cash_id'=>$cash_id,'pay_time'=>add_time()]);
	            if ($order['pid']>0) {//父订单
		            M("order")->where(['id'=>$order['pid']])->save(['status'=>1,'cash_id'=>$cash_id]);
	            }
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

				$tm = M('order')->find($order_id);
			  	 	$ms = M("mb_member")->where(['id'=>$tm['from_member_id']])->find();

					//生成系统消息
		            $message['type'] = 6;
		            $message['readed'] = 0;
		            $message['to_member_id'] = $ms['id'];
		            $message['object_id'] = $order_id;
		            $message['content'] = '你好！'.$member['nickname'].'已托付款项'.$tm['total'].'元到新助邦，请在5天内发货（逾期学生有权利退款）；发货后请即刻填写货运单，因为从货运单日期开始计时，学生将在15天内完成确认付款。';
		            $message['role'] = 1;
		            M("message")->add($message);

				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$member['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }
				
            }
            M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/member/order'));
	}

	function detail($id){
		if (IS_AJAX) {
			$this->showTrueJson(M("order")->find($id));
		}else{
			
			$type = I("request.type",0);
			$this->type = $type;
			$ids = is_array($_GET['id'])?$_GET['id']:array($_GET['id']);
			$orders = M("order_view")->where(['id'=>['in',$ids]])->select();
			$this->orders = $orders;
			$this->fee = M("order_view")->where(['id'=>['in',$ids]])->sum('total');
			
			/*/--微信支付---
			M()->startTrans();
            $member = M('mb_member')->find(session("id"));
            foreach ($orders as $order) {
            	$order_id  =$order['id'];
            	$fee = $order['total'];
	            //生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['id'],'is_alipay'=>0]);
	            //更新order
	            M("order")->where(['id'=>$order_id])->save(['status'=>1,'cash_id'=>$cash_id]);
	            if ($order['pid']>0) {//父订单
		            M("order")->where(['id'=>$order['pid']])->save(['status'=>1,'cash_id'=>$cash_id]);
	            }
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>2,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $fee,'object_id'=>$order_id,
	                );
	            M("payment_record")->add($record);//first
				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($recode);//second
				
            }
            M()->commit();
			
			*/	
			$this->display();
		}
	}

	function ok($id){
		$this->mustLogin();
		$orders = M("order_view")->where(['id'=>['in',$id]])->select();
		$this->orders = $orders;
		$this->o = $orders[0];
		go_assert($this->o['status']==1,'订单状态错误');
		$this->display();
	}
}
