<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class IndexController extends MemberBaseController {
    public function index() {
    	$type = I("request.type") ?: 0;
    	switch ($type) {
    		case 0:
    			break;
    		case 1://收入

    			break;
    		case 2://支出

    			break;
    		default:
    			break;
    	}
    	$this->display();
    }

    public function paypassword(){
		  if (IS_POST) {
            $m = M("mb_member")->find(session("id"));
            if (md5($m["paypassword"]) != md5(I("request.old"))) {
                $this->showFalseJson("老密码错误");
            }
            M("mb_member")->where(["id" => session("id")])->save(["paypassword" => md5(I("request.new1"))]);
            $this->showTrueJson("修改成功");
        }
	  $this->display("index");
	}

    public function bankcard() {
    	if (IS_POST) {
    		$card = $_POST;
    		$card['member_id'] = session("id");
    		$id = M("bankcard")->add($card,[],true);
    		$this->showTrueJson($id);
    	}
		$this->display("index");
    }

    function alipay($out_trade_no){
    	/*
	    [body] => 本次充值金额为1
    [buyer_email] => 457609375@qq.com
    [buyer_id] => 2088502648205402
    [exterface] => create_direct_pay_by_user
    [is_success] => T
    [notify_id] => RqPnCoPT3K9%2Fvwbh3InWf0984aR6PXHkvzLFB2yyNRU%2FEPvrUJXbr1TeRzyY5bxU6ASe
    [notify_time] => 2016-08-15 09:54:05
    [notify_type] => trade_status_sync
    [out_trade_no] => 6
    [payment_type] => 1
    [seller_email] => 3130748981@qq.com
    [seller_id] => 2088411764673830
    [subject] => 新助帮账户充值
    [total_fee] => 0.01
    [trade_no] => 2016081521001004400230132112
    [trade_status] => TRADE_SUCCESS
    [sign] => UReqzlThSrtAildwK7cnWd+wseOskG2lN80TztjkVAdvGw69kPUXjgS6gzKZtYjD5y1IIHvo5ng04FAds/bpGJU1o6B
+yaevdIhPelMsGKbg+1fALM1Gz88NlJ+gUA7PdPXUcSWHIKEj7P6OnihIq8/r9S8xgcWK/TWFkfpT8Pg=
    [sign_type] => RSA
    	*/
    $recharge_id = get_pay_id($out_trade_no);
    $recharge = M("recharge")->find($recharge_id);
	$verify_result = validate_alipay();
	if($verify_result) {//验证成功
        M()->startTrans();
        //加钱
        M("mb_member")->where(['id'=>session("id")])->setInc("balance",$recharge['balance']);
        //cash
        $m = M("mb_member")->find(session("id"));
        $cash_id = D("Cash")->alipayRecharge($recharge['balance'],$_GET['trade_no']);
        //修改rechar为成功
        M("recharge")->where(['id'=>$recharge['id']])->save(['success'=>1,'cash_id'=>$cash_id]);
        //record
        $record = array('type'=>2,'income_type'=>1,
            'from_member_id'=>session("id"),'to_member_id'=>session("id"),
            'balance' => $recharge['balance'],
            );
       $record_id = M("payment_record")->add($record);
	   $cash['payment_record_id'] = $record_id;
		M("cash")->where(['id'=>$cash_id])->save($cash);

		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = session('id');
		$message['object_id'] = session('id');
		$message['content'] = '您已成功充值'.$recharge['balance'].'元.';
		$message['role'] = 0;
		M("message")->add($message);

        M()->commit();
	}
	else {
        E('没考虑这种');
	}
	redirect(U('/member/index/balance'));
	}

    public function balance(){
    	if (IS_POST) {
            $total = I("request.balance");
			$total = $total * 100;
            $recharge_id = M("recharge")->add(['member_id'=>session('id'),'balance'=>$_POST['balance']]);
			if (I("request.pay_type") ==1) {
				$html_text = go_alipay(U('/member/index/alipay'),pay_id($recharge_id),"新助帮账户充值",$total);
				$this->show($html_text);
			}else{
                //Array ( [appid] => wxb48c9ae28cbf3da9 [code_url] => weixin://wxpay/bizpayurl?pr=5kretzq [mch_id] => 1240252602 [nonce_str] => AC2IgF5OA4F174WH [prepay_id] => wx20160817174350923d8b7a930367460143 [result_code] => SUCCESS [return_code] => SUCCESS [return_msg] => OK [sign] => B86620560AF58C9D74D1EF081B2A31EC [trade_type] => NATIVE ) 
                $out_trade_no = time()."_recharge_".$recharge_id;
    		    $code = go_wxPay($out_trade_no,$total,$recharge_id);
                $this->showTrueJson(['code'=>$code,'id'=>$recharge_id,'type'=>'recharge']);
			}
    	}else{
    		$this->display("index");
    	}
    }
    public function tixian(){
    	if (IS_POST) {
    		$id = M("acount")->add($_POST);
			//生成消息
			$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = session('id');
		$message['object_id'] = session('id');
		$message['content'] = '您的提现申请已收到，我们将在2个工作日处理！';
		$message['role'] = 0;
		M("message")->add($message);
    		$this->showTrueJson($id);
    	}
		$this->display("index");
    }
    public function minxi(){
        $type = I("request.type")?:0;
        $this->type = $type;
        $rs = M("payment_record")
        ->where(['_complex'=>['from_member_id'=>session("id"),'to_member_id'=>session("id"),"_logic"=>"OR"]])->field("payment_record.*,
            case type 
                when 1 then '提现' 
				when 2 then '充值'
				when 3 then '托付费用'
				when 4 then (select description from open_class where open_class.open_class_id=object_id)
				when 5 then (select description from order_view where order_view.id=object_id)
				when 6 then (select description from service_order_view where service_order_view.id=object_id)
				when 7 then '有偿问答'
				when 8 then '偷听答案'
				when 9 then '退款'
				when 10 then '管理费' 
				when 11 then '解冻托付费用'
				when 12 then '下载'
				when 13 then '下载'
            end as order_name
            ")->order('id desc');
        switch ($type) {
            case 0:
                break;
            case 1:
                $rs = $rs->where(['type'=>['in',[2,4,5,6,7,8,9,11,12,13]],'to_member_id'=>session('id')]);
                break;
            case 2:
                $rs = $rs->where(['type'=>['in',[3,4,5,6,7,8,9,10,12,13]],'from_member_id'=>session('id'),'income_type = 0']);
                break;
            case 3:
                $rs = $rs->where(['type'=>2]);
                break;
            case 4:
                $rs = $rs->where(['type'=>1]);
                break;
		    case 5:
                $rs = $rs->where(['type'=>['in',[3,11]]]);
                break;
            default:
                # code...
                break;
        }
        $_c = clone $rs;
        $this->count = $_c->count();
        $page = I('request.page')?:1;
        $this->rs = $rs->page($page,10)->select();
		$this->display("index");
    }
    public function bao(){
		$this->display("index");
    }
    public function teacherCenter(){
        $sub = M("contract")->where(['status'=>1])->field("order_id")->buildSql();
        //分页
        $services = M("service_order")
        ->join("LEFT JOIN service ON service.service_id=service_order.service_id")
        ->where(['service.member_id'=>session("id")])
        ->where(["_complex" => ['_logic'=>'or','num'=>['gt',0],"service_order.id in {$sub}"]])
            ->group("service_id") ->field("service.*,service_order.id as order_id")->page($_GET['page']?:1,20)->order('add_time desc')->select();
        $cou=count($services);
        $this->assign("cou",$cou);
        $this->services = $services;
        $this->display();
    }
    public function studentCenter(){
        $sql = M("contract")->where(['status'=>1])->field("order_id")->buildSql();
        $orders = M("service_order_view")
        ->where(["_complex" => ['num'=>['gt',0],'_logic'=>'or',"id IN $sql"],'student_id'=>session("id")])
        ->select();
        $this->orders = $orders;
        $this->display();
    }

}
