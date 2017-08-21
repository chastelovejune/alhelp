<?php
namespace Common\Model;

class CashModel extends \Think\Model {
	public function id2time($cash_id){
        $add_time = M("cash")->where(['id'=>$cash_id])->getField("unix_timestamp(add_time)");
		return $add_time;
	}
	
	function bytime($time){
    	$add_time = date("Y-m-d H:i:s",$time);
    	return M("cash")->getByAddTime($add_time);
  	}

  	function alipayRecharge($balance,$pay_id){
  		go_assert(session("id"),'必须登陆');
        $m = M("mb_member")->find(session("id"));;
        $cash = ['balance_now'=>$m['balance'],'balance_frozen'=>$m['balance_frozen'],'pay_id'=>$pay_id,'member_id'=>session("id"),'is_alipay'=>1,'balance'=>$balance];
        $cash_id = M("cash")->add($cash);
        return $cash_id;
  	}

  	function wxRecharge($member_id,$balance,$pay_id){
        $m = M("mb_member")->find($member_id);;
	    $cash = ['balance_now'=>$m['balance'],'balance_frozen'=>$m['balance_frozen'],'pay_id'=>$pay_id,'member_id'=>$member_id,'is_alipay'=>0,'balance'=>$balance];
	    $cash_id = M("cash")->add($cash);
        return $cash_id;
  	}

  	function tixian($acount_id){
    	$acount = M("acount")->join("LEFT JOIN bankcard ON bankcard.id=bankcard_id")->field('acount.*,member_id')->where(['acount.id'=>$acount_id])->find();
    	$m = M("mb_member")->find($acount['member_id']);
  		$cash = array('member_id'=>$acount['member_id'],
	      'balance'=>$acount['balance'],'balance_now'=>$m['balance'],
	      'balance_frozen'=>$m['balance_frozen']);
	    $cash_id = M("cash")->add($cash);
	    return $cash_id;
  	}
}