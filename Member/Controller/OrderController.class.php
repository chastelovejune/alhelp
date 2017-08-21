<?php
namespace Member\Controller;
use Member\Common\MemberBaseController;
class OrderController extends MemberBaseController {
	public function index(){
		//显示的时候,先暂时只显示子订单

      $orders = D("Order")->relation(true)
      ->where(['sub_order_ids'=>''])
      ->order('`order`.id desc');
      if ($this->is_student) {
  	      $orders = $orders->where(['to_member_id'=>session("id")]);
      }else{
	      $orders = $orders->where(['from_member_id'=>session("id")]);
      }
      {
				$copy = clone $orders;
				$this->count = $copy->count();
				unset($copy);
			}
			$orders = $orders->page($_GET['page']?:1,10);
      $orders = $orders->select();

      $this->orders = $orders;
		$this->display();
	}

	public function tuo(){
		$ds = M("demand")
		->where(['member_id'=>session("id")])
		->join('left join mb_member on mb_member.id=member_id')
		->field("demand.*,avatar,mb_member.nickname")
		->order("demand_id desc")->page($_GET['page']?:1,10)
		->select();
			$count=M("demand")->where(['advance_payment'=>['gt',0],'member_id'=>session("id")])->count();
		$this->ds = $ds;
		$this->assign('count',$count);
		$this->display();
	}
	
	public function untuo($id){
		$demand = M("demand")->find($id);
		$m = M("mb_member")->find($demand['member_id']);
		//修改托付状态
		$demand['is_payed'] = 0;
		$demand['advance_payment'] = 0;
		M("demand")->where(['demand_id'=>$demand['demand_id']])->save($demand);
		//解冻托付费用
		M("mb_member")->where(['id'=>$m['id']])->setDec("balance_frozen",$demand['cost']);
		M("mb_member")->where(['id'=>$m['id']])->setInc("balance",$demand['cost']);
      //  M()->execute("update demand left join mb_member on demand.member_id =mb_member.id set balance=balance+demand.advance_payment,is_payed=0 where demand_id=$id;");
	  //生成收支记录
	    $record = ['type'=>11,'income_type'=>0,'object_id'=>$demand['demand_id'],'from_member_id'=>$m['id'],'to_member_id'=>$m['id'],'balance'=>$demand['cost']];
			M("payment_record")->add($record);
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $demand['member_id'];
		$message['object_id'] = $demand['demand_id'];
		$message['content'] = '您已成功解冻托付费用'.$demand['cost'].'元，费用已放至您的账户余额中。可到个人中心进行查看';
		$message['role'] = 0;
		M("message")->add($message);
        $this->showTrueJson();
    }

	public function rating($id){
		if (IS_POST) {
			M()->startTrans();
			$r = $_POST;
			$r['order_id']=$id;
			M("rating")->add($r);
			M("order")->where(['id'=>$id])->save(['status'=>4]);
			M()->commit();
			$this->showTrueJson();
		}
		$order = M("order_view")->find($id);
		$order = parse_class($order);
		$this->o = $order;
		$this->display();
	}

	public function service(){
		$orders  = M("service_order_view");
		if ($this->is_student) {
			$orders = $orders->where(['student_id'=>session("id")]);
		}else{
			$orders = $orders->where(['teacher_id'=>session("id")]);
		}
		{
				$copy = clone $orders;
				$this->count = $copy->count();
				unset($copy);
			}
		$orders = $orders->order('id desc')->page($_GET['page']?:1,10);
		$this->orders = $orders->select();
		$this->display();
	}

    //确认收货
	public function receive($id){
		M()->startTrans();
		M("order")->where(["id"=>$id])->save(["status"=>3]);
		$order = M("order")->find($id);
		//转钱给老师
		D("PaymentRecord")->goAdd($order);
		$fee = $order['total']*0.9;
		M("mb_member")->where(['id'=>$order['from_member_id']])->setInc("balance",$fee);

		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['from_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '学生已确认收货！款项将在2个工作日转至您的新助邦账户';
		$message['role'] = 1;
		M("message")->add($message);
		
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['from_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '款项'.$order['total'].'元已转至您的新助邦账户！请查看。';
		$message['role'] = 1;
		M("message")->add($message);

		//生成交易记录
		$record = ['type'=>5,'income_type'=>0,'object_id'=>$order['id'],'from_member_id'=>0,'to_member_id'=>$order['from_member_id'],'balance'=>$order['total']];
		M("payment_record")->add($record);
		$record = ['type'=>10,'income_type'=>0,'object_id'=>$order['id'],'from_member_id'=>$order['from_member_id'],'to_member_id'=>0,'balance'=>$order['total']*0.1];
		M("payment_record")->add($record);

		M()->commit();
		$this->showTrueJson();
	}

	public function status0($id){
		M("order")->where(["id"=>I("request.id")])->save(["status"=>10]);
		$this->showTrueJson();
	}

	public function status1($id){
		M("order")->where(["id"=>I("request.id")])->save(["status"=>5]);
		$this->showTrueJson();
	}

	public function pay(){
		$order = M("order")->join("LEFT JOIN service ON order.service_id=service.service_id")->where(["`order`.id"=>I("request.id")])->join("LEFT JOIN mb_member ON mb_member.id=service.member_id")->field("`order`.*,service.description,nickname,avatar,cost")->find();
		$this->assign("order",$order);
		$this->display();
	}
	
	//学生退款(退款是针对未发货而言的，如果5天不发货自动退款，如果不足5天就要经过老师同意)
	public function remoney()
	{
		$order_time = M("order")->where(["id"=>I("request.id")])->getField("pay_time");
		$day = (time() - strtotime($order_time)) / (3600*24);

		$order = M("order")->where(['id'=>I('request.id')])->find();
		$m = M("mb_member")->find($order['to_member_id']);
        //生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['from_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '你好！'.$m['nickname'].'已申请退款，请与学生进行沟通。';
		$message['role'] = 1;
		M("message")->add($message);
		
		if($day >= 5)
		{
			M()->startTrans();			
			$record = ['type'=>9,'income_type'=>0,'object_id'=>I('request.id'),'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
			M("payment_record")->add($record);
			M("mb_member")->where(['id'=>$order['to_member_id']])->setInc("balance",$order['total']);
			M("order")->where(["id"=>I("request.id")])->setField("status",6);

			M()->commit();
			
			$this->showTrueJson();
		}else{
			M("order")->where(["id"=>I("request.id")])->setField("status",5);
			$this->showFalseJson("尚未满5天，请等待老师同意退款");
		}
		
	}
	
	public function remoneyTeacher()
	{
		M()->startTrans();
		$order = M("order")->where(['id'=>I('request.id')])->find();
		$record = ['type'=>2,'income_type'=>0,'object_id'=>I('request.id'),'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
		M("payment_record")->add($record);
		M("mb_member")->where(['id'=>$order['to_member_id']])->setInc("balance",$order['total']);
		M("order")->where(["id"=>I("request.id")])->setField("status",6);
		M()->commit();
		
		$this->showTrueJson();
	}
	
	
	//学生退货
	public function refund(){
		if (IS_POST) {
			M("order")->save(array_merge(['status'=>7],$_POST));
			$this->display("refund_post");
		}else{
			$this->display("refund_get");
		}
	}
	public function refundTeacher($id){
		if (IS_POST) {//老师同意退货
			M("order")->where(["id"=>$id])->save(['status'=>8]);

			$this->showTrueJson();
		}
		$this->o = M('order')->find($id);
		$this->display();
	}
	
	//学生取消退货
	public function unRefund(){
		M("order")->save(["id"=>I("request.id"),"status"=>2,"refund_reason"=>0,"refund_description"=>""]);
		redirect(U('/member/order/index'));
	}
	

	//同意退货成功
	public function okRefundOk($id){
		M("order")->where(["id"=>$id])->setField("status",8);
		$this->display();
	}
	//退货发货状态, 老师收到退货
	public function statusEight($id){
		if (IS_POST) {
			M("order")->where(["id"=>$id])->save(["status"=>10]);
			$this->showTrueJson();
		}
		$this->o = M('order')->find($id);
		$this->trans = M("transport")->where(['order_id'=>$id,'type'=>1])->find();
		
		M()->startTrans();
			
		//退钱给学生
		$order = M("order")->find($id);
		$record = ['type'=>9,'income_type'=>0,'object_id'=>$id,'from_member_id'=>0,'to_member_id'=>$order['to_member_id'],'balance'=>$order['total']];
		M("payment_record")->add($record);
		M("mb_member")->where(['id'=>$order['to_member_id']])->setInc("balance",$order['total']);

		$m = M("mb_member")->find($order['from_member_id']);
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['to_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '你好！'.$m['nickname'].'成功接收您发出的退货';
		$message['role'] = 0;
		M("message")->add($message);
		
		M()->commit();
		
		$this->display();
	}
	//老师邮寄东西
	public function tTransport(){
		if (IS_POST) {
			M()->startTrans();
			M("transport")->add($_POST);
			M("order")->where(["id"=>I("request.id")])->save(["status"=>2]);

			$order = M("order")->where(["id"=>I("request.id")])->find();
			$m = M("mb_member")->find($order['from_member_id']);
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['to_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '你好！'.$m['nickname'].'****（老师昵称）已成功发货给您，订单号'.$order['order_number'].'，请注意查收，请在15天内确认。';
		$message['role'] = 0;
		M("message")->add($message);

			M()->commit();
			$this->showTrueJson();
		}else{
			$this->display();
		}
	}
	//学生邮寄东西,status6
	public function transport(){
		if (IS_POST) {
			M()->startTrans();
			M("transport")->add($_POST);
			M("order")->where(["id"=>I("request.id")])->save(["status"=>9]);
			
			$order = M("order")->where(["id"=>I("request.id")])->find();
			$m = M("mb_member")->find($order['to_member_id']);
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = $order['from_member_id'];
		$message['object_id'] = $order['id'];
		$message['content'] = '你好！'.$m['nickname'].'的退货已发货，请注意查收。请在7天内于以确认，否则系统会默认您已确认收到货。';
		$message['role'] = 1;
		M("message")->add($message);
			M()->commit();
			$this->showTrueJson();
		}else{
			$this->display();
		}
	}
}