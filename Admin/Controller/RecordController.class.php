<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class RecordController extends AdminBaseController{
	public function index(){
    $rs = M("payment_record")->join("LEFT JOIN mb_member as m1 ON from_member_id=m1.id")->join("LEFT JOIN mb_member as m2 ON to_member_id=m2.id")
    ->field("payment_record.*,m1.nickname as from_member_name,m2.nickname as to_member_name")
    ->page(I('request.page'),20)
    ->order('id desc')
    ->select();
    $this->rs = $rs;
    $this->assign_page("payment_record");
		$this->display();
	}
  public function index1(){
    $this->display();
  }
  public function index2(){
  	$this->display(); 
  }
  public function index3(){
    $this->display();
  }	
}