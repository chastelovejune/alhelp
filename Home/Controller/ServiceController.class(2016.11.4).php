<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
require "../lib/curd.trail.php";
class ServiceController extends HomeBaseController {
	var $table_name = "service";
	use \curd;
	public function buy($id){
		if (IS_POST) {
			$order = $_POST;
			$order['service_id'] = $id;
			$order['member_id'] = session("id");
			$order_id = M("service_order")->add($order);
			$service = M("service")->find($id);	
			$fee = D('ServiceOrder')->fee($order_id);
			
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
				$record = ['type'=>3,'income_type'=>0,'object_id'=>$order_id,'balance'=>$fee];
				M("payment_record")->add(array_merge($record,['from_member_id'=>session('id'),'to_member_id'=>0]));
				M()->commit();
				$this->showTrueJson();
			}else if ($_POST['pay_type'] == 1) {//支付宝
				$html_text = go_alipay(U('/home/service/alipay'),
					pay_id($order_id),
					$service['description'],$fee);
				$this->show($html_text);
			}else{//微信
				$out_trade_no = time()."_serviceOrder_".$order_id;
    		    $code = go_wxPay($out_trade_no,$fee,$order_id);
            $this->showTrueJson(['code'=>$code,'id'=>$order_id,'type'=>'serviceOrder']);
			}
		}else{
			$s = M("service")->find($id);
			$this->s = $s;
			$this->display();
		}
	}

	public function alipay($out_trade_no){
		$order_id = get_pay_id($out_trade_no);
		$order = M("service_order")->find($order_id);
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $fee = D("ServiceOrder")->fee($order_id);
            $member = M('mb_member')->find(session("id"));
            //生成cash
            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
            //更新order
            M("service_order")->where(['id'=>$order_id])->save(['status'=>1,'cash_id'=>$cash_id]);
            //生成record
            $record = array('type'=>3,'income_type'=>1,
                'from_member_id'=>session("id"),'to_member_id'=>0,
                'balance' => $fee,'object_id'=>$order_id
                );
            M("payment_record")->add($record);
            M()->commit();
		}else{
            M("service_order")->where(['id'=>$order_id])->save(['status'=>2]);
			E('还没考虑那种情况会验证失败');
		}
		redirect(U('/member/ServiceOrder'));
	}

	public function add()
	{
		$this->mustLogin();
		if (IS_POST) {
			M()->startTrans();
			go_log($_POST);
			if ($_POST['school_id']>0) {
				$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'],'school_id');
				D("CircleMember")->addBySchoolId($school_id);	
			}

			$image = upload("image");
			$d = $_POST;
			$d["image"] = $image;
			$d["member_id"] = session("id");
			$d['member_name'] = M("mb_member")->where(['id'=>session("id")])->getField("nickname");
			$mc = M("service");
			if(!$mc->validate(C('validates.service'))->create($d)){
				M()->rollback();
				$this->showFalseJson($mc->getError());
			}
			$cid = $mc->add();
			foreach (I("request.packages") as $package) {
				$package['service_id'] = $cid;
				M("service_package")->add($package);
			}
			M()->commit();
		
			$this->showTrueJson($cid);
		}
		$this->display();
	}

	public function detail($id)
	{
		$service=D('Service')->relation(true)->find($id);
		$this->assign('owner',$service['Member']);
		$service = parse_class($service);
		$this->assign('service',$service);
		for ($i=1; $i <= 5; $i++) { 
			$c = M("service_package")->where(['service_id'=>I('request.id'),"type{$i}"=>1])->count();
			$package['type'.$i] = $c;
		}
		$this->package  =$package;
		// print_r($package);
		// 		print_r($service);die;

		{
			//合作方
	        // $hzfs = M("mb_member")->where("(id IN (select to_member_id from `order` where service_id=".$service_id.")) OR (id IN (select member_id from `service_order` where service_id=".$service_id.")) OR (id IN (select member_id from `audition` where service_id=".$service_id."))")->order("id DESC")->limit(10)->field("id,nickname,avatar")->select();
	        $sql1 = M("mb_member")->join("LEFT JOIN `order` on `order`.to_member_id=mb_member.id")->where(['`order`.service_id'=>$id])->field("mb_member.nickname,mb_member.avatar,mb_member.id,`order`.status,0 as type")->buildSql();
	        $sql2 = M("mb_member")->join("LEFT JOIN `service_order` on `service_order`.member_id=mb_member.id")->where(['`service_order`.service_id'=>$id])->field("mb_member.nickname,mb_member.avatar,mb_member.id,`service_order`.status,1 as type")->buildSql();
	        $sql3 = M("mb_member")->join("LEFT JOIN `audition` on `audition`.member_id=mb_member.id")->where(['`audition`.service_id'=>$id])->field("mb_member.nickname,mb_member.avatar,mb_member.id,`audition`.status,2 as type")->buildSql();
	        $hzfs= M()->table("($sql1 UNION $sql2 UNION $sql3) as t")->group('id')->select();
	        if (session("id") == $service['member_id']) {
	        	foreach ($hzfs as $index => $value) {
	        		$type = $value['type'];
	        		$status = $value['status'];
	        		switch ($type) {
	        			case 0:
	        				$value['status_name'] = C("order_status")[$status];
	        				break;
	        			case 1:
	        				$value['status_name'] = C("service_order_status")[$status];
	        				break;
	        			case 2:
	        				$value['status_name'] = C("audition_status")[$status];
	        				break;
	        		}
	        		$hzfs[$index]  =$value;
	        	}
	        }
	        $this->hzfs = $hzfs;
		}
		$this->display();
	}

	public function lists()
	{
		$services = M("service")->join("LEFT JOIN member ON service.member_id=member.id")->join("LEFT JOIN zysc_view ON zysc_view.zhuanye_id=service.school_id")->field("service.*,avatar,sign,follow_num,fans_num,share_num,collection_num");
		{//搜索
			$where = array();
	     	$keywords  =I("request.keywords");
	     	if ($keywords) {
	     		$where['_string'] = "(detail LIKE '%{$keywords}%') OR (service.description LIKE '%{$keywords}%')";
	     	}
	     	$service_type = I("request.service_type");
	     	if (strlen($service_type) >0) {
	     		$where["service_type"] = $service_type;
	     	}
	     	$where = array_merge($where,parent::where3type());
	 		$services  =$services->where($where);
	 		$this->where = $where;
		}
		{//排序
	     	$services = parent::sort($services,'service.service_id');
		}
		{//分页
			$copy = clone $services;
			$this->count = $copy->count();
			unset($copy);
			$services = $services->page($_GET['page']?:1,10);
		}
     	$services = $services->select();	
     	$this->services = $services;	
		$this->display();
	}
	
	public function praise()
	{
		$m = M("service")->where(["service_id"=>I("request.s_id")])->find();
		
		if($m['member_id'] == I("request.m_id"))
		{
			$this->showFalseJson("您不能赞自己哦");
		}
		else
		{
			M("service")->where(['service_id' => I('request.s_id')])->setInc("praise_num",1);
			$this->showTrueJson();
		}
	}
	
}
