<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class ProductController extends ApiBaseController {
     
     //添加购物车
     public function cartadd(){
	    if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$cart = $post_data['cart'];
            $carte = M("cart")->where(['member_id'=>$cart['id'],'service_id'=>$cart['service_id']])->find();
			
			if($carte){
			 $res = M("cart")->where(['member_id'=>$cart['id'],'service_id'=>$cart['service_id']])->setInc('count');
			}else{

			$data['member_id'] = $cart['id'];
			$data['service_id'] = $cart['service_id'];						
			$res = M("cart")->add($data);
			
			}
			if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}
		}
	 }


	 //购物车列表
	 public function cartlist($id){
	     $cart = M("cart")->where(['member_id'=>$id])->select();
		 foreach($cart as $i => $c){
		     $c['product'] = M("service")->find($c['service_id']);
             $cart[$i]=$c;
		 }
		 $this->success($cart);
	 }

     //修改购物车数量及删除商品
	 public function cartchange(){
	    	    if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$cart = $post_data['cart'];			
			
			if($cart['type'] == 0){  //0添加，1减少,2删除
			   $res = M("cart")->where(['member_id'=>$cart['id'],'service_id'=>$cart['service_id']])->setInc('count');
			}else if($cart['type'] == 1){
			   $res = M("cart")->where(['member_id'=>$cart['id'],'service_id'=>$cart['service_id']])->setDec('count');
			}else{
			   $res = M("cart")->where(['member_id'=>$cart['id'],'service_id'=>$cart['service_id']])->delete();
			}
           
			if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}
		}
	 }

     //收货地址列表
	 public function address($id){
	     $address = M("address")->where(['member_id'=>$id])->select();
		 foreach($address as $i => $c){
		   $c['proname'] = M("area")->field("title")->find($c['province']);
		   $address[$i]=$c;
		    $c['cityname'] = M("area")->field("title")->find($c['city']);
		   $address[$i]=$c;
		   $c['areaname'] = M("area")->field("title")->find($c['area']);
		   $address[$i]=$c;
		 }
		 $this->success($address);	 
	}


    //删除收货地址
	public function addressdel($id){
	    $res = M("address")->where(['id'=>$id])->delete();
        if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}
	}


   //设置默认收货地址
   public function addressdefault($uid,$id){
     
     $default = M("address")->where(['member_id'=>$uid])->field("is_default")->select();
	
	 foreach($default as $key => $value){
		 $default[$key]['is_default'] = '0';
	     M("address")->save($default[$key]);
	 }

	 $default['is_default'] = '1';
	 $res = M("address")->where(['id'=>$id])->save($default);

        if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}
   }


   //获取收货地址详情
   public function addressdetail($id){
       $address = M("address")->where(['id'=>$id])->find();
	   $this->success($address);
   }
 
   //获取地区
   public function areaid(){
       $pid = I("request.pid") ?: 0;
       $a = M("area")->where(['pid'=>$pid])->select();
	   $this->success($a);
   }

   //添加收货地址
   public function addressadd(){
	   $data['member_id'] = I("request.id");
	   $data['province'] = I("request.province_id");
	   $data['city'] = I("request.city_id");
	   $data['area'] = I("request.area_id");
	   $data['address'] = I("request.address");
	   $data['name'] = I("request.name");
	   $data['phone'] = I("request.phone");
	   $data['is_default'] = I("request.defaultaddress");

	   $res = M("address")->add($data);

      if($res){
       $msg = array(
	     'status' => '1'
	   );
		 $this->success($msg);

		}else{
		  $msg = array(
	     'status' => '0'
	   );
		 $this->success($msg);
		}
   }

   //修改收货地址
   public function adderssedit(){
	   $data['province'] = I("request.province_id");
	   $data['city'] = I("request.city_id");
	   $data['area'] = I("request.area_id");
	   $data['address'] = I("request.address");
	   $data['name'] = I("request.name");
	   $data['phone'] = I("request.phone");
	  
	   $res = M("address")->where(['id'=>I("request.id")])->save($data);

      if($res){
       $msg = array(
	     'status' => '1'
	   );
		 $this->success($msg);

		}else{
		  $msg = array(
	     'status' => '0'
	   );
		 $this->success($msg);
		}
   }

   //生成服务订单
   public function addpackorder(){
       if(IS_POST){

            $m = M("mb_member")->find(I("request.member_id"));

			if(I("request.payMode") == '3'){
				  $cashid = '0';
			}else{

	  		//生成现金流
		   $cash['member_id'] = I("request.member_id");
		   $cash['balance'] = I("request.total");
		   $cash['balance_now'] = $m['balance'];
		   $cash['pay_id'] = I("request.pay_id");
		    if(I("request.payMode") == '1'){
		      $cash['is_alipay'] = '1';
		  }else{
			  $cash['is_alipay'] = '0';
		  }
		  $cashid = M("cash")->add($cash);
			}
		   //生成订单
	       $data['service_id'] = I("request.service_id");
		   $data['num'] = I("request.num");
		   $data['member_id'] = I("request.member_id");
		   $data['package_id'] = I("request.package_id");
		   $data['cash_id'] = $cashid;
		   $data['status'] = '1';
		   $paid = M("service_order")->add($data);

		   if(I("request.payMode") == '3'){ //余额支付
		    $record = array('type'=>6,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>0,
	                'balance' => I("request.total"),'object_id'=>$paid,
	        );
	            M("payment_record")->add($record);//first
		   }else{
		    //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)		
	         $record = array('type'=>6,'income_type'=>1,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => I("request.total"),'object_id'=>$paid,
	        );
	            M("payment_record")->add($record);//first
				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second	
		   }
       
		   $package = M("service_order")->where(['service_id'=>I("request.service_id"),'member_id'=>I("request.member_id")])->find();

		    $service = M("service")->find(I("request.service_id"));
		    $ms = M("mb_member")->where(['id'=>$service['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.I("request.total").'元费用到新助邦，您可以开始辅导学员了！请尽快拟定ta的学习规划！登录www.alhelp.net查看详情');
		     }

			   $this->success($package);
			}
	   
   }

	//生成订单(此方法没用，用了网页版的)
	public function addorder(){
	  
	   if(IS_POST){
	       $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);						
			$item = $post_data['item'];			
			$data = json_decode($item,true);
            $addressid = $post_data['address_id'];

			$address = M("address")->where(['id'=>$addressid])->find();

            foreach($data as $key => $value){
				$cart = M("cart")->where(['id'=>$data[$key]['cart_id']])->find();
				$order['from_member_id'] = $cart['member_id'];
				$order['to_member_id'] = $data[$key]['business'];
			    $ddnumber=substr(date("ymdHis"),2,8).mt_rand(100000,999999);
	            $ddnumber = 'xzb'.$ddnumber;
				$order['order_number'] = $ddnumber;
				$order['province'] = $address['province'];
				$order['city'] = $address['city'];
				$order['area'] = $address['area'];
				$order['address'] = $address['address'];
				$order['phone'] = $address['phone'];
				$total = $cart['count'] * $data[$key]['cost'];
				$order['total'] = $total;
				$order['count'] = $cart['count'];
				$order['service_id'] = $cart['service_id'];
				M("order")->add($order);
				M("cart")->where(['id'=>$data[$key]['cart_id']])->delete();
			}
            
			$order=M("order")->select();

			$this->success($order);

	   }
	   
	}

	//修改订单状态
	public function orderchange(){
		if(IS_POST){ 
				
	     $ordnum = $_POST['order_number'];		 
		 $status = I("request.status");
		 $ord = json_decode($ordnum,true);	
		
		 $m = M("mb_member")->find(I("request.member_id"));
		
		 if(is_array($ord)&&count($ord)){    
		 foreach($ord as $i => $c){			 	
	      $order = M("order")->where(['id'=>$c])->find();
			 //生成现金流
			 if(I("request.payMode") == '3'){
				 $cashid = 0;
			 }else{
			  $cash['member_id'] = I("request.member_id");
		  $cash['balance'] = $order['total'];
		  $cash['balance_now']= $m['balance'];
		  $cash['pay_id'] = I("request.pay_id");
		  if(I("request.payMode") == '1'){
		      $cash['is_alipay'] = '1';
		  }else{
			  $cash['is_alipay'] = '0';
		  }
		  $cashid = M("cash")->add($cash);
			 }
		 
		  //修改订单
		  $data['status'] = $status;
		  $data['pay_time'] = date('Y-m-d H:i:s');
		  $data['cash_id'] = $cashid;
		  $id = M("order")->where(['id'=>$c])->save($data);

		  if(I("request.payMode")=='3'){ //余额支付

            $record = array('type'=>5,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>0,
	                'balance' => $order['total'],'object_id'=>$order['id'],
	        );
	            M("payment_record")->add($record);

		  }else{ //支付宝、微信支付

		     //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)	
	         $record = array('type'=>5,'income_type'=>1,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => $order['total'],'object_id'=>$order['id'],
	        );
	            M("payment_record")->add($record);//first
				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second	
		  }
		  	$ms = M("mb_member")->where(['id'=>$order['from_member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
		    	$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付款项，请在5天内发货；发货后请即刻填写货运单！登录www.alhelp.net查看详情');
		     }
		  
		 }

	   $msg = array(
	     'status' => '1'
	   );
		 $this->success($msg);

		}else{
		  $msg = array(
	     'status' => '0'
	   );
		 $this->success($msg);
		}
		}	
	}

	//修改订单
	public function orderstatus($id){
	     $data['status'] = I("request.status");
         $res =  M("order")->where(['id'=>$id])->save($data);
      if($res){
			$msg = array(
			  'status' => '1',	
		      'position' => I("request.position")
			);
		}else{
		  $msg = array(
			  'status' => '0',	
              'position' => '-1'
			);
		}
		$this->success($msg);
	}

	//收货
	public function orderconfirm($id){

		$position = I("request.postion");

		//修改订单状态
	   $data['status'] = I("request.status");
         $res =  M("order")->where(['id'=>$id])->save($data);

		 //给钱给老师
		 $order = M("order")->where(['id'=>$id])->find();
		 $m = M("mb_member")->where(['id'=>$order['from_member_id']])->find();
		 $m['balance'] = $m['balance'] + $order['total']*0.9;
		 M("mb_member")->where(['id'=>$order['from_member_id']])->save($m);

		 //生成流水，新助邦->老师用户
		 $balance = $order['total'];
		  $record = array('type'=>5,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$order['from_member_id'],
	                'balance' => $balance,'object_id'=>$order['id'],
	        );
	            M("payment_record")->add($record);//first

		//平台管理费		
		$manbalance = $order['total'] * 0.1;
		 $record = array('type'=>10,'income_type'=>0,
	                'from_member_id'=>$order['from_member_id'],'to_member_id'=>0,
	                'balance' => $manbalance,'object_id'=>$order['id'],
	        );
	            M("payment_record")->add($record);
      if($res){
			$msg = array(
			  'status' => '1',	
		      'position' => $position,
			);
			$this->success($msg);
		}else{
		  $msg = array(
			  'status' => '0',	
              'position' => '-1',
			);
			$this->success($msg);
		}
		
	}

	//删除订单
	public function orderdel($id){
		$res = M("order")->where(['id'=>$id])->delete();
		if($res){
			$msg = array(
			  'status' => '1',
			  'position' => I("request.position")
			);
		}else{
		  $msg = array(
			  'status' => '0',
		      'position' => '-1'
			);
		}
		$this->success($msg);
	}

    //支付成功，查看订单
	public function payrecord(){
	    if(IS_POST){
		   $ordnum = $_POST['order_number'];	 
		   $order = json_decode($ordnum,true);	
		   $ord = array();

		   foreach($order as $i => $c){
		     $oitem = M("order")->where(['id'=>$c])->find();
			 $ord[$i]=$oitem;
		   }

		    foreach ($ord as $i => $c) {
        $c['title'] = M("service")->field("description")->find($c['service_id']);
        $ord[$i]=$c;
      }		  		
		   $this->success($ord);
		}
	}
    
	//获取订单列表
	public function orderlist($type,$page){	    
		        		
		   $order_type = M("service")->where(['service_type = 0'])->field("service_id")->select();
			 
		if(is_array($order_type)&&count($order_type)){
		   foreach($order_type as $key => $value){
		      $ordert[$key] = $value['service_id']; 
			  }
		}

		$orders['service_id'] = array('in',$ordert);

		if(I("request.role") == '1'){ //获取学生
		if($type == 99){
		    $orderlist = M("order")->page($page,10)->where(['to_member_id'=>I("request.id")])->order('id desc')->select();
		    $count =M("order")->where(['to_member_id'=>I("request.id")])->count();
		    $count = $count - ($page-1) * 10;
		}else{
		    $orderlist = M("order")->page($page,10)->where(['to_member_id'=>I("request.id"),'status'=>$type])->order('id desc')->select();
		    $count =M("order")->where(['to_member_id'=>I("request.id"),'status'=>$type])->count();
		    $count = $count - ($page-1) * 10;
		}
		}else{ //获取老师
		if($type == 99){ 
		    $orderlist = M("order")->page($page,10)->where(['from_member_id'=>I("request.id")])->order('id desc')->select();
		    $count =M("order")->where([$orders,'to_member_id'=>I("request.id")])->count();
		    $count = $count - ($page-1) * 10;
		}else{
		    $orderlist = M("order")->page($page,10)->where(['from_member_id'=>I("request.id"),'status'=>$type])->order('id desc')->select();
		    $count =M("order")->where(['to_member_id'=>I("request.id"),'status'=>$type])->count();
		    $count = $count - ($page-1) * 10;
		}
		}

		foreach($orderlist as $key => $value){
		    $ser = M('service')->where(['service_id'=>$orderlist[$key]['service_id']])->field("description,image")->find();
			$orderlist[$key]['description'] = $ser['description'];
			$orderlist[$key]['avatar']=$ser['image'];
		}

		$data = array(
			'page' => $page,
			'list' => $orderlist,
			'count' => $count
		);

		$this->success($data);
			
	}


    //获取订单详情
	public function orderdetail($id){

		$order = M("order")->where(['id'=>$id])->find();
	  $order['service'] = M("service")->where(['service_id'=>$order['service_id']])->field('description')->find();	
	  // $order['products'] = array(
		//   'service' => M("service")->where(['service_id'=>$order['service_id']])->field('description')->find(),	
	  // );
	   $order['city'] = M("area")->where(['id'=>$order['city']])->field('title')->find();	
	   $order['area'] = M("area")->where(['id'=>$order['area']])->field('title')->find();
	    $order['name'] = M("address")->where(['member_id'=>$order['to_member_id']])->field('name')->find();
	   $this -> success($order);
	}


    //获取答疑授课订单
	public function classorder($id,$page,$role){

		
		if($role == 1){ //学生
	     	$orders = M("service_order_view")->where(['student_id'=>$id])->page($page,10)->field("service_order_view.*,teacher_avatar as avatar,teacher_nickname as nickname")->select();
			$count = M("service_order_view")->where(['student_id'=>$id]) ->count();

		}else{
            $orders = M("service_order_view")->where(['teacher_id'=>$id])->page($page,10)->field("service_order_view.*,student_avatar as avatar,student_nickname as nickname")->select();
			$count = M("service_order_view")->where(['teacher_id'=>$id]) ->count();
		}

		
		$data = array(
			'list'=>$orders,
			'count'=>$count
		);
		$this->success($data);

	}
    


	//订单筛选图书资料
	public function bookorderlist($role_type,$status){
	  if(IS_POST){
         
		 	$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];

		$order_type = M("service")->where(['service_type = 1'])->field("service_id")->select();

		if(is_array($order_type)&&count($order_type)){
		   foreach($order_type as $key => $value){
		      $ordert[$key] = $value['id']; 
			  }
		}

		$orders['object_id'] = array('in',$ordert);

		if($role_type == 0){  //0.学生 1.老师
		   $order = M("order")->where([$orders,'to_member_id'=>$user['id'],'status'=>$status])->select();
		   
		}else{
		   $order = M("order")->where([$orders,'from_member_id'=>$user['id'],'status'=>$status])->select();
		}

	/*	$data = array(
			'list' => $order,
		    'count' => $count,
			
		);*/


		$this->success($order);
			}
	}

	//订单评价
	public function evu(){
		$res = M("rating")->add($_POST);
		if($res){
			$data['status'] = '4';
			M("order")->where(['id'=>I("request.order_id")])->save($data);
			$order = M("order")->find(I("request.order_id"));
			$m1 = M("mb_member")->find($order['from_member_id']);
			$m2 = M("mb_member")->find($order['to_member_id']);
			$c1 = "你已对{$m1['member_name']}进行了评价";
			M("message")->add(['type'=>1,'to_member_id'=>$order['to_member_id'],'object_id'=>$order['id'],'content'=>$c1]);
			M("message")->add(['type'=>1,'to_member_id'=>$order['from_member_id'],'object_id'=>$order['id'],'content'=>"{$m2['nickname']}已对你进行了评价"]);
			M()->commit();
			$msg = array(
				'status'=> '1'
			);
		}else{
			$msg = array(
				'status'=>'0'
			);
		}
		$this->success($msg);
	}

	//发货
	public function deliver(){
		M("transport")->add($_POST);
		$data['status'] = '2';
	    $res =  M("order")->where(['id'=>I("request.order_id")])->save($data);
        if($res){			
			$msg = array(
				'status'=> '1'
			);
		}else{
			$msg = array(
				'status'=>'0'
			);
		}
		$this->success($msg);
	}


}