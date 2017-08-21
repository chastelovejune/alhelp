<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class InformationController extends HomeBaseController {
	private function _info($type){
		//type是大分类   0下载 1资讯  2分享 3公告 4我们 5 院校动态
		$infos = M("information")->join("LEFT JOIN mb_member ON information.member_id=mb_member.id")->field("information.*,avatar");
		{//搜索
	     	$keywords  =I("request.keywords");
	     	$where['type'] = $type;
	     	if ($keywords) {
	     		$where['_string'] = "(title LIKE '%{$keywords}%') OR (content LIKE '%{$keywords}%')";
	     	}
	     	if (I("request.type2") != "") {
	     		$where['type2'] = I("request.type2");
	     	}
			$where['information.status']=0;//启用状态
			$count =$infos->where($where)->count();
	 		$infos  =$infos->where($where);
		}
		{//排序
	     	$infos = parent::sort($infos);
		}
     	$infos = $infos->page($_GET['page']?:1,20)->select();
     //	dump($infos);
		foreach($infos as $k=>$v){
			$info[]=parse_class($v);
			//dump($info);
		}
		//dump($infos);
		$this->assign("count",$count);
		$this->assign('type',$type);
     	$this->assign("infos",$info);
		$this->display("index");
	}
//下载
	public function index(){
		$this->_info(0);
	}
	//资讯
	public function index1(){
		$this->_info(1);
	}
	//分享
	public function index2(){
		$tmp = M("share")->join('LEFT JOIN zysc_view ON share.school_id=zysc_view.zhuanye_id')->join("LEFT JOIN mb_member ON mb_member.id=share.member_id")->field("share.*,mb_member.avatar,mb_member.nickname");
		$keywords  =I("request.keywords");
     	if ($keywords) {
     		$tmp = $tmp->where(['_string'=>"(title LIKE '%{$keywords}%') OR (content LIKE '%{$keywords}%')"]);
     	}
		$tmp = parent::sort($tmp);
		$tmp = $tmp->select();
		foreach($tmp as $i => $share){
			$share = parse_class($share);
			$tmp[$i] = $share;
		}
		$this->assign("all",$tmp);
		$shares = [[],[],[]];//防止null,导致merge出空的
		foreach ($tmp as $share) {
			$shares[$share['type']][] = $share;
		}
		$this->assign("shares",$shares);
		$this->display("index");
	}
	//公告
	public function index3(){
		$this->_info(3);
	}
	//院区动态
	public function index4(){
		$this->_info(5);
	}
	public function add(){
		if (IS_POST) {
			M()->startTrans();
			$m = M("mb_member")->where(['id'=>session("id")])->find();
			$info = $_POST;
			$info = array_merge($info,["member_id"=>$m['id'],"nickname"=>$m['nickname']]);
			$upload = upload();
			$image = $upload['image'];
			$attachment = $upload['attachment'];
			if ($image) {
				$info['image']  =$image;
			}
			if ($attachment) {
				$info['attachment'] = $attachment;	
			}
			$id = M("information")->add($info);
			M()->commit();
			$this->showTrueJson($id);
		}
		$this->display();
	}
	
	public function show($id){
		$id=I('request.id');
		$info = M("information")->field("*,(SELECT collection.id FROM collection WHERE collection.type=1 AND collection.object_id=information.id) AS collection_id")->where('id='.$id)->find();
		$this->assign("info",$info);
		$this->display();
	}
	public function download($id){
		$this->mustLogin();
		$i = M("information")->find($id);
		$m = M("mb_member")->find(session("id"));
		if($i['attachment_type']==1){//需要积分
					if ($m['score'] >= $i["attachment_score"]) {
						//说明积分足够,减去积分
						$rest=$m['score']-$i["attachment_score"];
						//更新积分
						$re=M('mb_member')->where('id='.session("id"))->setField("score",$rest);
						//返回下载地址
						$data['suc']=true;
						$data['type']=1;
						$data['message']="积分下载成功";
						$data['url']=$i['attachment'];
						$this->ajaxReturn($data);
					}else{
						$this->showFalseJson("所需积分不足");
					}
		}elseif($i['attachment_type']==2){
			//需要RMB，跳转支付界面,处理支付功能
			$data['suc']=true;
			//$data['type']=2;
			$data['message']="下载需支付".$i['attachment_cost']."元";
			//$data['url']=$i['attachment'];
			$data['url']=$id;
			$this->ajaxReturn($data);
		}elseif($i['attachment_type']==0){
			$data['suc']=true;
			$data['message']="下载免费不消耗任何费用";
			$data['url']=$i['attachment'];
			$this->ajaxReturn($data);
		}elseif($i['attachment_type']==3){
			if ($m['coin'] >= $i["attachment_coin"]) {
				$rest=$m['coin']-$i["attachment_coin"];
				$re=M('mb_member')->where('id='.session("id"))->setField("coin",$rest);
				//返回下载地址
				$data['suc']=true;
				$data['type']=1;
				$data['message']="金币下载成功";
				$data['url']=$i['attachment'];
				$this->ajaxReturn($data);
			}else{
				$this->showFalseJson("所需金币不足");
			}

		}
		else{
			$this->showFalseJson("下载不成功");
		}
	}
	public function read(){
		M("information")->where(['id'=>I("request.id")])->setInc("views");
		$this->showTrueJson();
	}
	public function zan(){
		M("information")->where(['id'=>I('request.id')])->setInc("praise_num");
		$this->showTrueJson();
	}

	public function pay($id){
	  $info = M("information")->find($id);
	  $cost = $info['attachment_cost'];
	  if(IS_POST){
	     if (I("request.pay_type")==2) {
			//余额支付
			$m = M("mb_member")->find(session('id'));			
			if ($m["balance"] < $cost) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$cost);
				//打钱给用户
				if($info['member_id']>0){
				  M("mb_member")->where(["id"=>$info['member_id']])->setInc("balance",$cost);
				}

				//生成流水
				$record = ['type'=>12,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$cost];
				M("payment_record")->add($record);
				if($info['member_id']>0){
				$record = ['type'=>12,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>0,'to_member_id'=>$info['member_id'],'balance'=>$cost];
				M("payment_record")->add($record);
				}
				M()->commit();
                $data['suc']=true;
				$data['msg']="支付成功，正在下载";
				$data['url']=$info['attachment'];
				$this->ajaxReturn($data);
			}
		}else if(I("request.pay_type")==1){ //支付宝
			$cost = $cost * 100;
			$out_trade_no = pay_id(I("request.id"));
			$ds = M("information")->where(['id'=>I("request.id")])->find();
			$html = go_alipay(U('/home/Information/alipay'),$out_trade_no,$ds['title'],$cost);
			echo $html;
			die;
		}else if(I("request.pay_type")==0){ //微信
			$cost = $cost * 100;
			$w['status'] = 0;
			$wid = M("wx_notify")->add($w);
			$ids = array($id,session('id'),$wid);
			$out_trade_no = time()."_download_".I("request.id");
    		$code = go_wxPay($out_trade_no,$cost,json_encode($ids));
            $this->showTrueJson(['code'=>$code,'id'=>$ids,'type'=>'download']);
		}
	  }else{		  
		  $this->cost = $info['attachment_cost'];
	     $this->display();
	  }
	}

	function alipay($out_trade_no){
		$id = get_pay_id($out_trade_no);
		$info = M("information")->find($id);
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
			//生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$info['attachment_cost'],'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>12,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $info['attachment_cost'],'object_id'=>$id,
	                );
	           $record_id = M("payment_record")->add($record);//first

			   //收支记录
				$cash['payment_record_id'] = $record_id;
		        M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second

			if($info['member_id']>0){
			   M("mb_member")->where(["id"=>$info['member_id']])->setInc("balance",$info['attachment_cost']);
			    $record = array('type'=>12,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$info['member_id'],
	                'balance' => $info['attachment_cost'],'object_id'=>$id,
	                );
	           $record_id = M("payment_record")->add($record);
			}
                M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/home/information/down',['id'=>$id]));
	}

	public function getwxpaystatus($id){
	   $w = M("wx_notify")->find($id);
	   $this->showTrueJson($w);
	}

	public function getinfo($id){
		$info = M("information")->find($id);
		$this->showTrueJson($info);
	}


	public function down($id){
	   $info = M("information")->find($id);
	   $this->info = $info;
	   $this->display();
	}

}