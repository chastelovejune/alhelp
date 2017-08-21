<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class ShareController extends HomeBaseController {
	public function add(){
		$this->mustLogin();
		if (IS_POST) {
			$m = M("mb_member")->find(session("id"));
			$info = $_POST;
			$info = array_merge($info,["member_id"=>$m['id'],"nickname"=>$m['nickname']]);
			$attachment = upload();
			if ($attachment) {
				$info['attachment']  =$attachment['attachment'];
			}
			$id = M("share")->add($info);
			$this->showTrueJson($id);
		}
		$type = $_GET['type'] ?: 0;
		$this->assign("type",$type);
		$this->display();
	}

	public function show(){
		$s = M("share")->find(I("request.id"));
		M("share")->where(['id'=>I("request.id")])->setInc("views");
		$this->assign("s",$s);
		$this->display();
	}
	public function read(){
		M("share")->where(['id'=>I("request.id")])->setInc("views");
		$this->showTrueJson();
	}
	public function download($id){
		$this->mustLogin();
		$i = M("share")->find($id);
		$m = M("mb_member")->find(session("id"));
		if($i['download_type']==1){//需要积分
			if ($m['score'] >= $i["download_cost"]) {
				//说明积分足够,减去积分
				$rest=$m['score']-$i["download_cost"];
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
		}elseif($i['download_type']==2){
			//需要金币
				if ($m['coin'] >= $i["download_cost"]) {
					$rest = $m['coin'] - $i["download_cost"];
					//更新积分
					$re = M('mb_member')->where('id=' . session("id"))->setField("coin", $rest);
					//返回下载地址
					$data['suc'] = true;
					$data['type'] = 2;
					$data['message'] = "金币下载成功";
					$data['url'] = $i['attachment'];
					$this->ajaxReturn($data);
				}else{
					$this->showFalseJson("所需金币不足");
				}

			}elseif($i['download_type']==3){//RMB下载

						//支付处理业务逻辑  待写						
						$data['suc']=true;
		            	$data['message']="下载需支付".$i['download_cost']."元";
			            $data['url']=$id;
			            $this->ajaxReturn($data);

		}else if($i['download_type'] == 0){
		//返回下载地址
				$data['suc']=true;
				$data['type']=0;
				$data['message']="下载成功";
				$data['url']=$i['attachment'];
				$this->ajaxReturn($data);
		}else{
			$this->showFalseJson("下载不成功");
		}
	}

	public function pay($id){
	  $info = M("share")->find($id);
	  $cost = $info['download_cost'];
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
				$record = ['type'=>13,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$cost];
				M("payment_record")->add($record);
				if($info['member_id']>0){
				$record = ['type'=>13,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>0,'to_member_id'=>$info['member_id'],'balance'=>$cost];
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
			$ds = M("share")->where(['id'=>I("request.id")])->find();
			$html = go_alipay(U('/home/share/alipay'),$out_trade_no,$ds['title'],$cost);
			echo $html;
			die;
		}else if(I("request.pay_type")==0){ //微信
			$cost = $cost * 100;
			$w['status'] = 0;
			$wid = M("wx_notify")->add($w);
			$ids = array($id,session('id'),$wid);
			$out_trade_no = time()."_sharedownload_".I("request.id");
    		$code = go_wxPay($out_trade_no,$cost,json_encode($ids));
            $this->showTrueJson(['code'=>$code,'id'=>$ids,'type'=>'sharedownload']);
		}
	  }else{		  
		  $this->cost = $info['download_cost'];
	     $this->display();
	  }
	}

	function alipay($out_trade_no){
		$id = get_pay_id($out_trade_no);
		$info = M("share")->find($id);
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
			//生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$info['download_cost'],'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>12,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $info['download_cost'],'object_id'=>$id,
	                );
	           $record_id = M("payment_record")->add($record);//first

			   //收支记录
				$cash['payment_record_id'] = $record_id;
		        M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second

			if($info['member_id']>0){
			   M("mb_member")->where(["id"=>$info['member_id']])->setInc("balance",$info['download_cost']);
			    $record = array('type'=>12,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$info['member_id'],
	                'balance' => $info['download_cost'],'object_id'=>$id,
	                );
	           $record_id = M("payment_record")->add($record);
			}
                M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		
		redirect(U('/home/share/down',['id'=>$id]));
	}

	public function down($id){
	   $info = M("share")->find($id);
	   $this->info = $info;
	   $this->display();
	}
}