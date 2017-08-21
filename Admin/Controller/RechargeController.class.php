<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class RechargeController extends AdminBaseController{
  protected $table_name = "recharge";
  public function index(){
    $list = M("recharge")->join("LEFT JOIN cash ON cash_id=cash.id")->join("LEFT JOIN mb_member ON recharge.member_id=mb_member.id")->field("recharge.*,mb_member.nickname,cash.balance_now,cash.balance_frozen")->order('recharge.id desc')->select();
    $this->assign_list($list); 
    $this->display();
  }	
}