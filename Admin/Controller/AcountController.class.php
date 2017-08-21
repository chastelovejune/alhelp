<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class AcountController extends AdminBaseController{
  protected $table_name = "acount";
  public function index(){
    $list = M("acount")->join("LEFT JOIN bankcard on bankcard_id=bankcard.id")->join("LEFT JOIN mb_member ON bankcard.member_id=mb_member.id")->order('acount.id desc')->field("acount.*,mb_member.nickname,bankcard.card_type,bankcard.member_id,bankcard.card_num,bankcard.card_name,mb_member.balance as mb_balance,balance_frozen")->select();
    $this->assign_list($list); 
    $this->display();
  }	
  public function ok(){
    M()->startTrans();
    $acount = M("acount")->join("LEFT JOIN bankcard ON bankcard.id=bankcard_id")->field('acount.*,member_id')->where(['acount.id'=>I("request.id")])->find();
    M("mb_member")->where(['id'=>$acount['member_id']])->setDec("balance",$acount['balance']);
	$m = M("mb_member")->where(['id'=>$acount['member_id']])->find();
	$cash['is_alipay'] = I("request.status");
	$cash['acount_id'] = I("request.id");
	$cash['member_id'] = $acount['member_id'];
	$cash['balance'] = $acount['balance'];
	$cash['balance_now'] = $m['balance'];
	$cash['balance_frozen'] = $m['balance_frozen'];
	$cash_id = M("cash")->add($cash);
    M("acount")->where(['id'=>I("request.id")])->save(['status'=>1,'cash_id'=>$cash_id]);

    $record = array('type'=>1,'income_type'=>1,
                'from_member_id'=>$acount['member_id'],'to_member_id'=>$acount['member_id'],
                'balance' => $acount['balance'],'object_id'=>$acount['id']
                );
    $record_id = M("payment_record")->add($record);
	$cash['payment_record_id'] = $record_id;
	M("cash")->where(['id'=>$cash_id])->save($cash);

	//系统消息
	if(I("request.status") == 0){
	  $type = '微信';
	}else if(I("request.status") == 1){
	   $type = '支付宝';
	}else{
	  $type = '银行卡';
	}
	$message['type'] = 6;
	$message['readed'] = 0;
	$message['to_member_id'] = $acount['member_id'];
	$message['object_id'] = $acount['member_id'];
	$message['content'] = '你的提现已经转入您的账户('.$type.'账号），请查收！';
	$message['role'] = 0;
	M("message")->add($message);

    M()->commit();
    $this->showTrueJson();
  }

}
