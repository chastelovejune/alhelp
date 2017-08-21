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
				$record = ['type'=>6,'income_type'=>0,'object_id'=>$order_id,'balance'=>$fee];
				M("payment_record")->add(array_merge($record,['from_member_id'=>session('id'),'to_member_id'=>0]));
				

				$service = M("service")->field("member_id")->find($order['service_id']);

				$m = M("mb_member")->where(['member_id'=>session("id")])->find();
		//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] =$service['member_id'];
		$message['object_id'] = $order_id;
		if($order['package_id'] == 0){
		  $message['content'] = '学生已托付了费用到新助邦，您可以开始辅导学员了！ ';
		}else{
		  $message['content'] = '学生已托付了费用到新助邦，请先签约，尽快开始辅导学员!';
		}
		$message['role'] = 1;
		M("message")->add($message);

		  $ms = M("mb_member")->where(['id'=>$service['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
				if($order['package_id'] == 0){
		    	    $sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$fee.'元费用到新助邦，您可以开始辅导学员了！登录www.alhelp.net查看详情');
				}else{
					$sms->sendToServer($ms['phone'],'你好,'.$m['nickname'].'已托付'.$fee.'元费用到新助邦，请尽快签约，尽快开始辅导学员！登录www.alhelp.net查看详情');
				}
		     }

			 M()->commit();
				$this->showTrueJson($order_id);
			}else if ($_POST['pay_type'] == 1) {//支付宝
				$fee = $fee * 100;
				$html_text = go_alipay(U('/home/service/alipay'),
					pay_id($order_id),
					$service['description'],$fee);
				$this->show($html_text);
			}else{//微信
				$fee = $fee * 100;
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
            M("payment_record")->add($record);

			$service = M("service")->field("member_id")->find($order['service_id']);
	 	//生成系统消息
		$message['type'] = 6;
		$message['readed'] = 0;
		$message['to_member_id'] =$service['member_id'];
		$message['object_id'] = $order_id;
		if($order['package_id'] == 0){
		  $message['content'] = '学生已托付了费用到新助邦，您可以开始辅导学员了！ ';
		}else{
		  $message['content'] = '学生已托付了费用到新助邦，请先签约，尽快开始辅导学员!';
		}
		$message['role'] = 1;
		M("message")->add($message);

		  $ms = M("mb_member")->where(['id'=>$service['member_id']])->find();
				if(!empty($ms['phone'])){
			     require "../lib/Sms.class.php";
			    $sms = new \Sms();
				if($order['package_id'] == 0){
		    	    $sms->sendToServer($ms['phone'],'你好,'.$member['nickname'].'已托付'.$fee.'元费用到新助邦，您可以开始辅导学员了！登录www.alhelp.net查看详情');
				}else{
					$sms->sendToServer($ms['phone'],'你好,'.$member['nickname'].'已托付'.$fee.'元费用到新助邦，请尽快签约，尽快开始辅导学员！登录www.alhelp.net查看详情');
				}
		     }
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
			$re= M("mb_member")->where(['id'=>session("id")])->Field("phone_verified,qq,wx,phone,nickname")->find();
			//如果没通过验证
			if($re['phone_verified']!=1) {
				$code = cookie("phoneCode");
						if ($code != I("request.phone_verified")) {
							$this->showFalseJson("手机验证码错误！");
						}
				
						if($code = I("request.phone_verified"))
							{	//验证手机成功，修改字段状态
						unset($data);
						$data['phone_verified']=1;
						$data['qq']=I("request.qq");
						$data['wx']=I("request.wechat");
								$data['phone']=I("request.mobile");
						M("mb_member")->where(['id'=>session("id")])->save($data);
							}
			}else{
			M()->startTrans();
			//go_log($_POST);
			if ($_POST['school_id']>0) {
				$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'],'school_id');
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

			$image = upload("image");
			$d = $_POST;
		//dump($image);
			//dump($_POST['image']);
			$d["image"] = $image;
			
				$da['qq']=$d['qq'];
				$da['wx']=$d['wechat'];
				M("mb_member")->where(['id'=>session("id")])->save($da);
				
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
				//根据用户需求去匹配相同专业推荐信息
				unset($map);
				//dump($d);
				$map['unified_id'] = $d['unified_id'] ?: 0;
				$map['public_subject_id'] = $d['public_subject_id'] ?: 0;
				$map['school_id'] = $d['school_id'] ?: 0;
				$demand_re = M('demand')->where($map)->group('member_id')->select();
				//dump($service_re);

				$mb = M('mb_member');
				//循环取出专业匹配老师的手机号，循环发送信息
				//发送系统消息,给老师，发给自己
				unset($data);
				$data['type'] = 10;
				$data['to_member_id'] = session("id");
				$data['object_id'] = $cid;//CID是服务的ID
				$data['content'] = "你好！已有学生发布了匹配的专业课需求啦！";//"'".$re['nickname'] . "同学发布的【" . $d['description'] . "】需求和你发布的服务专业匹配";
				$data['role'] = 1;//角色1老师。但是这里跳转要跳转需求详情页
				M('message')->add($data);
				require "../lib/Sms.class.php";
				$sms = new \Sms();
				foreach ($demand_re as $k => $v) {
					$phone = $mb->field("phone,phone_verified,nickname")->find($v['member_id']);
					//发送给学生
					unset($data);
					$data['type'] = 10;
					$data['to_member_id'] = $v['member_id'];
					$data['object_id'] =$cid;
					$data['content'] = "'".$re['nickname'] . "'老师发布的【" . $d['description'] . "】服务和你发布的需求专业匹配";
					$data['role'] = 0;
					M('message')->add($data);
					//发送手机短信给老师
					if ($phone['phone_verified'] == 1) {
						//手机验证才能发送短信
						if (!empty($phone['phone'])) {
							//dump($phone['phone']);
							$sms->sendToServer($phone['phone'], "你好！已有【" . $re['nickname'] . "】学长发布了匹配的专业课需求啦！请到“新助邦考研平台”微信公众号的个人中心，点击“系统消息”可直接联系到对方！");
						}
					}
				}
		
			$this->showTrueJson($cid);
		}
		}
		//2016.11.3显示平台管理费  新加  author:chastelove
		$member_id= session("id");
		$re=M("mb_member")->field("answer_rete")->join('left join member_group on member_group.id=mb_member.id')->where('mb_member.id='.$member_id)->find();
		//dump($re);
		
		
		$cost=$re['answer_rete'];
		//dump($cost);
		//获取用户是否验证手机
		$re= M("mb_member")->where(['id'=>session("id")])->Field("phone_verified,qq,wx,phone")->find();
		//dump($re);
		$this->assign('phone_verified',$re['phone_verified']);
		$this->assign('qq',$re['qq']);
		$this->assign('wx',$re['wx']);
		$this->assign('phone',$re['phone']);
		$this->assign('cost',$cost);//取出用户组的税率
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
			$sql4 = M("mb_member")->join("LEFT JOIN `bid` on `bid`.member_id=mb_member.id")->where(['`bid`.service_id'=>$id])->field("mb_member.nickname,mb_member.avatar,mb_member.id,`bid`.status,2 as type")->buildSql();
	        $hzfs= M()->table("($sql1 UNION $sql2 UNION $sql3 UNION $sql4) as t")->group('id')->select();
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
	 		//dump($hzfs);
	        $this->hzfs = $hzfs;
		}
		//取出同一个人同类专业发布的直播课
	if($service['unified_id']!=0 ||$service['public_subject_id']!=0||$service['school_id']!=0){
		$map['member_id']=$service['member_id'];
		$map['unified_id']=$service['unified_id'];
		$map['public_subject_id']=$service['public_subject_id'];
		$map['school_id']=$service['school_id'];
		$openclass=M('open_class')->where($map)->limit(2)->order("add_time desc")->select();
	}
		//dump($openclass);
		$this->assign('openclass',$openclass);
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
	     	$where["service.status"]=0;
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
			$services = $services->page($_GET['page']?:1,20);
		}
		if(I("request.major_id") >0){
			$mj = M("mj_major")->find(I("request.major_id"));
			if($mj['mj_type']==1){
			$sid = M("school")->where(['major_id'=>I("request.major_id")])->field("id")->select();
			  foreach($sid as $k => $v){
	           $sids[] = $v['id'];
	       }
		   if(count($sids)>0){
	       $services = $services->where(['service.school_id'=>['in',$sids]])->select();	
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
	            $services = $services->where(['service.school_id'=>['in',$sids]])->select();	
	         }
			}
		}else{
     	$services = $services->select();
	   }
    	
     	foreach ($services as $k => $v) {
     		$re[]=parse_class($v);
     	}
     	
     	$this->services = $re;	
		$this->display();
	}
	
	public function praise()
	{
		//cookie('praise')=1;//写入一个cookie判断有没有点过赞，因为这个数据库没有设计是谁点的赞
		$m = M("service")->where(["service_id"=>I("request.s_id")])->find();
		
		if($m['member_id'] == I("request.m_id"))
		{
			$this->showFalseJson("您不能赞自己哦");
		}
		else
		{
		//if(empty(cookie('praise'))){
			M("service")->where(['service_id' => I('request.s_id')])->setInc("praise_num",1);
			$this->showTrueJson("点赞成功");
			//}else{
			//M("service")->where(['service_id' => I('request.s_id')])->setDnc("praise_num",1);
			//$this->showTrueJson("你已取消点赞");
			//}
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
		$this->showTrueJson($result,"收藏成功");
	}
	}
}
