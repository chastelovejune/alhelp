<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class CashController extends AdminBaseController{
  protected $table_name = "cash";
  public function index(){
	$cashs = M("cash")->order('cash.id desc');
	{
		//分页
		$tmp = clone $cashs;
		$this->assign_page($tmp->count());
		unset($tmp);
	}
	/*$cashs = $cashs->table("(
  select *,
  (select id from recharge where recharge.cash_id=cash.id) as recharge_id,
  (select id from acount where acount.cash_id=cash.id) as acount 
  from  cash
)as cash")->field('cash.*,IF(recharge_id>0,0,IF(acount_id>0,1,2)) as type,is_alipay as pay_type,nickname')->join("LEFT JOIN mb_member on mb_member.id=member_id")->page(I("request.page",1),20)->select();*/
     $cashs = $cashs->table('cash cash,mb_member m')->where(['cash.member_id = m.id'])->field('cash.*,cash.is_alipay as pay_type,m.nickname')->page(I("request.page",1),20)->select();
	 foreach($cashs as $i => $c){
	    $pm = M("payment_record")->field('type')->find($c['payment_record_id']);
		$cashs[$i]['type'] = $pm['type'];
	 }
	$this->cashs = $cashs;
  	$this->display();
  }
}