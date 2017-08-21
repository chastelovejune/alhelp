<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
use Home\Common\Alhelp_Constant;
require "../lib/curd.trail.php";
class DemandController extends HomeBaseController {
	var $table_name = "demand";
	use \curd;

    //托付费用
	public function buy($id){
		$demand = M("demand")->find($id);
		$fee = $demand['cost'];
		$m = M("mb_member")->find(session("id"));
		//go_assert($fee>0,'托付费用大于0才需要购买');
		if (IS_POST) {
			$pay_type = I("request.pay_type");
			if ($pay_type == 0) {//微信
				$fee = $fee * 100;
				$out_trade_no = time()."_demand_".$id;
    		    $code = go_wxPay($out_trade_no,$fee,$id);
	            $this->showTrueJson(['code'=>$code,'id'=>$id,'type'=>'demand']);
			}elseif ($pay_type==1) {//支付宝
				$fee = $fee * 100;
				$html_text = go_alipay(U('/home/demand/alipay'),
					pay_id($id),
					$demand['description'],$fee);
				$this->show($html_text);
			}else{//余额
				
				if ($m["balance"] < $fee) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$fee);
				//更新冻结金额
				M("mb_member")->where(['id'=>$m['id']])->setInc("balance_frozen",$fee);
				//更新托付状态
				$demand['is_payed'] = 1;
				$demand['advance_payment'] = $fee;
				M("demand")->where(['demand_id'=>$demand['demand_id']])->save($demand);
				//生成交易记录
				$record = ['type'=>3,'income_type'=>0,'object_id'=>$demand['demand_id'],'from_member_id'=>session('id'),'to_member_id'=>session('id'),'balance'=>$fee];
					M("payment_record")->add($record);//更新到record表
				M()->commit();
				//生成系统消息
				$message['type'] = 6;
				$message['readed'] = 0;
				$message['to_member_id'] = session('id');
				$message['object_id'] = $demand['demand_id'];
				$message['content'] = '您已成功托付'.$demand['cost'].'元，您托付的款项已放至您的冻结金额中。可到个人中心进行查看';
				$message['role'] = 0;
				M("message")->add($message);
				$this->showTrueJson();
			}
			}
		}
		$this->demand = $demand;
		$this->display();
	}

    //托付费用
	public function alipay($out_trade_no){
		$id = get_pay_id($out_trade_no);
		$member = M("mb_member")->find(session('id'));
		
		//更新托付状态
		$demand = M("demand")->find($id);
		$demand['is_payed'] = 1;
		$demand['advance_payment'] = $demand['cost'];
		M("demand")->where(['demand_id'=>$id])->save($demand);
		//更新冻结金额
		M("mb_member")->where(['id'=>$member['id']])->setInc("balance_frozen",$demand['cost']);
		//生成现金记录
		$cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$demand['cost'],'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
		//生成交易记录
		$record = ['type'=>3,'income_type'=>1,'object_id'=>$demand['demand_id'],'from_member_id'=>session('id'),'to_member_id'=>session('id'),'balance'=>$demand['cost']];
		$record_id = M("payment_record")->add($record);//更新到record表
		$cash['payment_record_id'] = $record_id;
		M("cash")->where(['id'=>$cash_id])->save($cash);
		$record = ['type'=>3,'income_type'=>0,'object_id'=>$demand['demand_id'],'from_member_id'=>session('id'),'to_member_id'=>session('id'),'balance'=>$demand['cost']];
		M("payment_record")->add($record);//更新到record表

		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] = session('id');
		$message['object_id'] = $demand['demand_id'];
		$message['content'] = '您已成功托付'.$demand['cost'].'元，您托付的款项已放至您的冻结金额中。可到个人中心进行查看';
		$message['role'] = 0;
		M("message")->add($message);

		$this->redirect('/home/demand/detail',['id'=>$id]);
	}

	public function add()
	{
		$this->mustLogin();
		//$code = cookie("phoneCode");
		//dump($code);

		if (IS_POST) {
			//go_log($_POST);
			$re = M("mb_member")->where(['id' => session("id")])->Field("phone_verified,qq,wx,phone,nickname")->find();
			//如果没通过验证
			if ($re['phone_verified'] != 1) {
				$code = cookie("phoneCode");
				if ($code != I("request.phone_verified")) {
					$this->showFalseJson("手机验证码错误！");
				}

				if ($code = I("request.phone_verified")) {    //验证手机成功，修改字段状态
					unset($data);
					$data['phone_verified'] = 1;
					$data['qq'] = I("request.qq");
					$data['wx'] = I("request.wechat");
					$data['phone']=I("request.mobile");
					M("mb_member")->where(['id' => session("id")])->save($data);
				}
			}

			M()->startTrans();
			if ($_POST['school_id'] > 0) {
				$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'], 'school_id');
				D("CircleMember")->addBySchoolId($school_id);
				//加入校友圈群聊
				$m = M("mb_member")->find(session('id'));
				$c = M("circle")->where(['object_id'=>$school_id])->find();
				$im = new \Org\Util\Im();	
				if(M("group_manage")->where(['circle_id'=>$c['id']])->find()){
				$g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	      
	        	$im->group_join($m['chat_id'],$g['group_id']);
				}else{
				  $gid = $im->group_create('19999',$c['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $c['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
				}
			}

			$d = $_POST;
			$da['qq'] = $d['qq'];
			$da['wx'] = $d['wechat'];
			M("mb_member")->where(['id' => session("id")])->save($da);
			$d["member_id"] = session("id");
			$d['member_name'] = M("mb_member")->where(['id' => session("id")])->getField("nickname");
			$cid = M("demand")->add($d);
			M()->commit();
			//根据用户需求去匹配相同专业推荐信息
			unset($map);
			//dump($d);
			$map['unified_id'] = $d['unified_id'] ?: 0;
			$map['public_subject_id'] = $d['public_subject_id'] ?: 0;
			$map['school_id'] = $d['school_id'] ?: 0;
			$service_re = M('service')->where($map)->group('member_id')->select();
			//dump($service_re);

			$mb = M('mb_member');
			//发送给学生(发送给自己)
			unset($data);
			$data['type'] = 10;
			$data['to_member_id'] = session("id");
			$data['object_id'] =  $cid;
			$data['content'] = "你好！已有学长发布了匹配的专业课服务啦！";
			$data['role'] = 0;
			M('message')->add($data);
			//循环取出专业匹配老师的手机号，循环发送信息
			require "../lib/Sms.class.php";
			$sms = new \Sms();
			foreach ($service_re as $k => $v) {
				$phone = $mb->field("phone,phone_verified,nickname")->find($v['member_id']);
				//发送系统消息,给老师
				unset($data);
				$data['type'] = 10;
				$data['to_member_id'] = $v['member_id'];
				$data['object_id'] = $v['service_id'];
				$data['content'] = "'".$re['nickname'] . "'同学发布的【" . $d['description'] . "】需求和你发布的服务专业匹配";
				$data['role'] = 1;
				M('message')->add($data);
				//发送手机短信给老师
				if ($phone['phone_verified'] == 1) {
					//手机验证才能发送短信
					if (!empty($phone['phone'])) {
						//dump($phone['phone']);
						$sms->sendToServer($phone['phone'], "你好！已有'" . $re['nickname'] . "'学长发布了匹配的专业课服务啦！请到“新助邦考研平台”微信公众号的个人中心，点击“系统消息”可直接联系到对方！");
					}
				}
			}

			$this->showTrueJson($cid);
		}

		//获取用户是否验证手机
		$re= M("mb_member")->where(['id'=>session("id")])->Field("phone_verified,qq,wx,phone")->find();
		//dump($re);
		$this->assign('phone_verified',$re['phone_verified']);
		$this->assign('qq',$re['qq']);
		$this->assign('wx',$re['wx']);
		$this->assign('phone',$re['phone']);
		$this->display();
	}

	public function detail()
	{   
		$demand_id=I('id');
		$demand=D('Demand')->relation(true)->find($demand_id);
		$owner=$demand['Member'];
		$this->assign('owner',$owner);
		$demand = parse_class($demand);
		//dump($demand);
		
		$this->assign('demand',$demand);
		if (IS_AJAX) {
			$this->showTrueJson($demand);		
		}else{
			$this->display();
		}
	}
	public function lists()
	{      
		    $demands = M("demand")->join("LEFT JOIN member ON demand.member_id=member.id")->join("LEFT JOIN zysc_view ON zysc_view.zhuanye_id=demand.school_id")->field("demand.*,avatar,sign,follow_num,fans_num,share_num,collection_num");
		
		{//搜索
			$where = array();
	     	$keywords  =I("request.keywords");
	     	if ($keywords) {
	     		$where['_string'] = "(detail LIKE '%{$keywords}%') OR (demand.description LIKE '%{$keywords}%')";
	     	}
	     	$demand_type = I("request.demand_type");
	     	if (strlen($demand_type)>0) {
	     		$where["demand_type"] = $demand_type;
	     	}
			$where["demand.status"]=0;
	     	$where = array_merge($where,parent::where3type());
	 		$demands  =$demands->where($where);
	 		$this->where = $where;
		}
		{//排序
			$order = parent::sort(null,'demand.demand_id');
			$order = "is_payed desc,".$order;
	     	$demands = $demands->order($order);
	     	//注：原按照is_payed desc,advance_payment desc,排序，现在改为最新的排序
		}
		{//分页
			{
				$copy = clone $demands;
				$this->count = $copy->count();
				unset($copy);
			}
			$demands = $demands->page($_GET['page']?:1,20);
		}
		
		if(I("request.major_id") >0){
			$mj = M("mj_major")->find(I("request.major_id"));
			if($mj['mj_type']==1){
			  $sid = M("school")->where(['major_id'=>I("request.major_id")])->field("id")->select();
			    foreach($sid as $k => $v){
	             $sids[] = $v['id'];
	           }
		     if(count($sids)>0){
	            $demands = $demands->where(['demand.school_id'=>['in',$sids]])->select();	
	         }
			}else{
			   $mjs = M("mj_major")->where(['pid'=>I("request.major_id")])->field('id')->select();
               foreach($mjs as $k => $v){
	             $mjids[] = $v['id'];
	           }
			   $sid = M("school")->where(['major_id'=>['in',$mjids]])->field("id")->select();
			    foreach($sid as $k => $v){
	             $sids[] = $v['id'];
	           }
		     if(count($sids)>0){
	            $demands = $demands->where(['demand.school_id'=>['in',$sids]])->select();	
	         }
			}
		}else{
           $demands = $demands->select();	
	   }

	/*   $unified_id = I("request.unified_id");
	   $utype = I("request.utype");
	   if($utype == 0){
		   $uid = M("unified")->where(['cid'=>$unified_id])->field('id')->select();
		    foreach($uid as $k => $v){
	             $uids[] = $v['id'];
	           }			 
		 if(count($uids)>0){
           $demands = $demands->where(['demand.unified_id'=>['in',$uids]])->select();
		 }else{
		     $demands = $demands->select();
		  }
	   }else{
		   //$demands = $demands->where(['demand.unified_id'=>$unified_id])->select();
		    $demands = $demands->select();
	   }*/

     	$this->demands = $demands;	
		$this->display();
	}

	//这是专业反馈，需求和服务通用一个
	public function addmajor(){
		if(IS_POST){
			$data['member_id']=session("id");
			$re=M("mb_member")->where(['id'=>session("id")])->Field("nickname")->find();
			$data['member_nickname']=$re['nickname'];
			$data['content']=I('content');
			$data['add_time']=date('Y-m-d H:i:s',time());
			$re=M('feedback')->add($data);
			if($re){
				$this->ajaxReturn($re);
		}else{
				$this->showTrueJson("请先登录吧，再发送消息");
			}
		}
	}
		public function praise()
	{
		
		
		$m = M("demand")->where(["demand_id"=>I("request.s_id")])->find();
		
		if($m['member_id'] == I("request.m_id"))
		{
			$this->showFalseJson("您不能赞自己哦");
		}
		else
		{
		
			M("demand")->where(['demand_id' => I('request.s_id')])->setInc("praise_num",1);
			$this->showTrueJson("点赞成功");
			
		}
	
}
	public function collection(){
		$data['member_id']=I("request.m_id");
		$data['object_id']=I("request.s_id");
		$data['type']=1;//代表服务
		$re=M("collection")->where($data)->find();
		if($re){

			M("collection")->where($data)->delete();
		$this->showFalseJson("已取消收藏");
	}else{
		$result=M("collection")->add($data);
		$this->showTrueJson("收藏成功");
	}
	}

}
