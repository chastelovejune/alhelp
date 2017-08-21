<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class OrderController extends AdminBaseController{
  protected $table_name = "order";
  public function index(){
	  //显示有点问题。最后调整
	  if(IS_POST){
		$map['order.status']=$_POST['status'];
		  $map['order_number']=$_POST['key'];
		//dump($map);
		  $order1 = M("order")->where($map)->join("LEFT JOIN service ON order.service_id=service.service_id")->find();
		// dump($order1);
		  $this->assign("orders",$order1);
		  $this->display();

	  }else {
		  $orders = M("order")->page(I('request.page'), 20)->join("LEFT JOIN service ON order.service_id=service.service_id")->select();
		  $this->assign("orders", $orders);
		  $this->assign_page("order");
		  $this->display();
	  }
 ;

  }
}