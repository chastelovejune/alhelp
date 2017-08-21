<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class OpenClassController extends HomeBaseController{
	public function index(){	
		if(session("id")){
			$cs = M("open_class");
			$cs->field("open_class.*,mb_member.nickname,(SELECT apply_open_class.id FROM apply_open_class WHERE apply_open_class.open_class_id=open_class.open_class_id AND apply_open_class.member_id=".session("id").") as apply_id")->join("LEFT JOIN mb_member ON member_id=mb_member.id")->join("LEFT JOIN zysc_view ON open_class.school_id = zhuanye_id");
			
		}else{
			$cs = M("open_class");
		}	
		if($_GET['school_id'] > 0){
			$school_id = $_GET['school_id'];
			$cs->where(['_string' => "zysc_view.school_id = $school_id OR zysc_view.yuan_id = $school_id OR zysc_view.zhuanye_id = $school_id OR zysc_view.area_id = $school_id"]);
		}
		if($_GET['public_subject_id'] > 0){
			$cs->where(["public_subject_id" => $_GET['public_subject_id']]);
		}
		if($_GET['unified_id'] > 0){
			$cs->where(["unified_id" => $_GET['unified_id'],"open_class.status"=>0]);
		}
		$keywords = I('request.keywords');
		if ($keywords) {
			$cs->where(['open_class.description'=>['like',"%$keywords%"]]);
		}
		{
				$copy = clone $cs;
				$this->count = $copy->where('open_class.status=0')->count();
				unset($copy);
		}
		$cs = $cs->page($_GET['page']?:1,20);

		if(I("request.major_id") >0){
			$sid = M("school")->where(['major_id'=>I("request.major_id")])->field("id")->select();
			  foreach($sid as $k => $v){
	           $sids[] = $v['id'];
	          }
		   if(count($sids)>0){			   
	         $cs = $cs->where(['open_class.school_id'=>['in',$sids],'open_class.status=0'])->order('open_class_time desc')->select();
	       }else{
		     $cs = $cs->where('open_class.status=0')->order('open_class_time desc')->select();
		   }
		}else if(I("request.school_id") >0){
		     $f_s = M("school")->find($school_id);
			 if($f_s['type'] == 3){
			  $zys = M("zysc_view")->where(['yuan_id'=>$school_id])->field('zhuanye_id')->select();
			     foreach($zys as $k => $v){
	               $sids[] = $v['zhuanye_id'];
	              }
			   if(count($sids)){
			      $cs = $cs->where(['open_class.school_id'=>['in',$sids],'open_class.status=0'])->order('open_class_time desc')->select();	
			   }else{
		         $cs = $cs->where('open_class.status=0')->order('open_class_time desc')->select();
		       }
			 }else if($f_s['type'] == 2){
               $zys = M("zysc_view")->where(['school_id'=>$school_id])->field('zhuanye_id')->select();
			     foreach($zys as $k => $v){
	               $sids[] = $v['zhuanye_id'];
	              }
			  if(count($sids)){
			     $cs = $cs->where(['open_class.school_id'=>['in',$sids],'open_class.status=0'])->order('open_class_time desc')->select();	
			   }else{
		          $cs = $cs->where('open_class.status=0')->order('open_class_time desc')->select();
		       }
			 }else{
				 $zys = M("zysc_view")->where(['area_id'=>$school_id])->field('zhuanye_id')->select();
			     foreach($zys as $k => $v){
	               $sids[] = $v['zhuanye_id'];
	              }
			if(count($sids)){
			     $cs = $cs->where(['open_class.school_id'=>['in',$sids],'open_class.status=0'])->order('open_class_time desc')->select();	
			  }else{
		        $cs = $cs->where('open_class.status=0')->order('open_class_time desc')->select();
		      }
			 }
		}else{
     	   $cs = $cs->where('open_class.status=0')->order('open_class_time desc')->select();
	   }
		
		$this->cs = $cs;
		$this->display();
	}
	public function add(){
		$this->mustLogin();
		if (IS_POST) {
			$image = upload("image");
			$c = $_POST;
			$c["image"] = $image;
			$c["member_id"] = session("id");
			$c['member_name'] = M("mb_member")->where(['id'=>session("id")])->getField("nickname");
			$cid = M("open_class")->add($c);
			$this->showTrueJson($cid);
		}
		$this->display();
	}
	//需要取消点赞, 要记录谁点的, 待优化
	/*public function praise(){
		M("open_class")->where(["open_class_id"=>I("request.id")])->setInc("praise_num");
		$this->showTrueJson();
	}
	public function unpraise(){
		M("open_class")->where(["open_class_id"=>I("request.id")])->setDec("praise_num");
		$this->showTrueJson();
	}*/
	public function show(){
		$c = M("open_class")->find(I("request.id"));
		$c = parse_class($c);
		$c["apply_id"] = M("apply_open_class")->where(["member_id"=>session("id"),"open_class_id"=>$c["open_class_id"]])->getField("id");
        //查找其他的
        $map['member_id']=array('EQ',$c['member_id']);
        $map['open_class_id']=array('NEQ',$c['open_class_id']);
        $map['school_id']=array('EQ',$c['school_id']);
        $map['public_subject_id']=array('EQ',$c['public_subject_id']);
        $map['unified_id']=array('EQ',$c['unified_id']);
		$map['status']=array('EQ',0);
        $openClass3=M("open_class")->where($map)->limit(3)->select();
        $this->assign("openClass3",$openClass3);
    	$this->assign("open_class",$c);
		$this->display();
	}
	public function apply(){
		$class = M("open_class")->find(I('request.open_class_id'));
		if ($class['member_id'] == session("id")) {
			$this->showFalseJson("不能自己报名自己");
			exit;
		}
		$open_time=strtotime($class['open_class_time']);
		if($open_time<time()){
			unset($ret);
			$ret['message']="该课程已经开课，不能报名了";
			$ret['suc']=0;
			$ret['url']="#";
			$this->ajaxReturn($ret);
			exit;
		}
		if(session("id")==null){
			unset($ret);
			$ret['message']="你必须登录才能报名!";
			$url = U('/home/member/login');
			$url2="/home_OpenClass_index.html";
			$ret['url']=$url."?url=".$url2;
			$ret['suc']=0;
			$this->ajaxReturn($ret);
			exit;
		}
		if(!$class['price']==0){
			$ret['message']="此课程为付费课程，你必须付费才能试听!";
			$ret['url']=U('/home/OpenClass/apay',['id'=>I('request.open_class_id')]);
			$ret['suc']=0;
				$this->ajaxReturn($ret);
				exit;
			}
		$apply = $_POST;

		$apply["member_id"] = session("id");
		M("apply_open_class")->add($apply);
		//发送系统消息给老师
		$re=M('mb_member')->field('nickname')->where("id=".session("id"))->find();
		$class_re=M("open_class")->find(I('request.open_class_id'));
		$data['to_member_id']=$class_re['member_id'];
		$data['type']=9;
		$data['role']=1;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s',time());
		$data['content']="'".$re['nickname']."'学生已申请试听你【".$class_re['description']."】,上课时间".$class_re['open_class_time']."地点".$class_re['classroom'];
		M('message')->add($data);
		//发送系统消息给学生
		unset($data);
		$data['to_member_id']=session("id");
		$data['type']=9;
		$data['role']=0;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s',time());
		$data['content']="你已经申请试听".$class_re['teacher']."的".$class_re['description']."的直播课,上课时间".$class_re['open_class_time']."地点".$class_re['classroom'];
		M('message')->add($data);
		$this->showTrueJson("报名成功");
	}

    public function apay($id){	
		if(IS_POST){
			 $op = M("open_class")->where(['open_class_id'=>$id])->find();
          
			$total = $op['price'];
			if (I("request.pay_type")==2) {
			//余额支付
			$m = M("mb_member")->find(session('id'));			
			if ($m["balance"] < $total) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$total);
				//更新余额(扣钱)

				//生成流水
				$record = ['type'=>4,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$total];
				M("payment_record")->add($record);//更新到record表
				//生成流水
				$record = ['type'=>4,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>0,'to_member_id'=>$op['member_id'],'balance'=>$total];
				M("payment_record")->add($record);//更新到record表

				//打钱给老师
				M("mb_member")->where(['id'=>$op['member_id']])->setInc("balance",$total);

                //添加试听
				$apply['open_class_id'] = $op['open_class_id'];
				$apply["member_id"] = session("id");
                $apply["apply_time"] = date('Y-m-d h:i:s');
		M("apply_open_class")->add($apply);
		//发送系统消息给老师
		$re=M('mb_member')->field('nickname')->where("id=".session("id"))->find();
		$data['to_member_id']=$op['member_id'];
		$data['type']=1;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s');
		$data['content']=$re['nickname']."学生已申请试听你的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'];
		M('message')->add($data);
		//发送系统消息给学生
		unset($data);
		$data['to_member_id']=session("id");
		$data['type']=1;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s');
		$data['content']="你已经申请试听".$op['teacher']."的【".$op['description']."】的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'].",如果没有支付费用的，请尽快支付费用";
		M('message')->add($data);
				
				M()->commit();
				$this->showTrueJson();
			}
		}else if (I("request.pay_type") == 1) {
			//支付宝	
			$total = $total * 100;
			$out_trade_no = pay_id(I("request.id"));
			$ds = M("open_class")->where(['open_class_id'=>$id])->find();
			$html = go_alipay(U('/home/OpenClass/alipay'),$out_trade_no,$ds['description'],$total);
			echo $html;
			die;
			$this->show($html);			
		}else{
			//微信
			$ids = array($id,session('id'));
			$total = $total * 100;
			$out_trade_no = time()."_openClass";
    		$code = go_wxPay($out_trade_no,$total,json_encode($ids));
            $this->showTrueJson(['code'=>$code,'id'=>$id,'type'=>'openClass']);
		}
		}else{			
		  $total = M("open_class")->where(['open_class_id'=>$id])->field('price')->find();
		  $this->ops = $total;
		  $this->display();
		  }
	}


	function alipay($out_trade_no){
		$opid = get_pay_id($out_trade_no);
		$ops = M("open_class")->find($opid);
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
			//打钱给老师
				M("mb_member")->where(['id'=>$ops['member_id']])->setInc("balance",$ops['price']);

			//生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$ops['price'],'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>7,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $ops['price'],'object_id'=>$opid,
	                );
	            $record_id = M("payment_record")->add($record);//first
				 $cash['payment_record_id'] = $record_id;
		         M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second
				//生成流水
				$record = ['type'=>4,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>0,'to_member_id'=>$ops['member_id'],'balance'=>$total];
				M("payment_record")->add($record);//更新到record表

				  //添加试听
				$apply['open_class_id'] = $op['open_class_id'];
				$apply["member_id"] = session("id");
                $apply["apply_time"] = date('Y-m-d h:i:s');
		M("apply_open_class")->add($apply);
		//发送系统消息给老师
		$re=M('mb_member')->field('nickname')->where("id=".session("id"))->find();
		$data['to_member_id']=$op['member_id'];
		$data['type']=1;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s');
		$data['content']=$re['nickname']."学生已申请试听你的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'];
		M('message')->add($data);
		//发送系统消息给学生
		unset($data);
		$data['to_member_id']=session("id");
		$data['type']=1;
		$data['readed']=0;
		$data['object_id']=I('request.open_class_id');
		$data['add_time']=date('Y-m-d h:i:s');
		$data['content']="你已经申请试听".$op['teacher']."的".$op['description']."的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'].",如果没有支付费用的，请尽快支付费用";
		M('message')->add($data);

            M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/home/OpenClass/show',['id'=>$opid]));
	}
   
   public function isapply($id){
      if(M("apply_open_class")->where(['open_class_id'=>$id,'member_id'=>session('id')])->find()){
	    $this->showTrueJson();
	  }
   }
}