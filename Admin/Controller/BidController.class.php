<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class BidController extends AdminBaseController{
	function index(){
		$bids = D("Bid")->relation(true)->page(I('request.page'), 20)->select();
		$this->bids = $bids;
		$this->assign_page("order");
		$this->display();
	}
	function delete()
	{
		$id=I('id');
		//因为数据库默认为0,0改为上架状态，1改为逻辑删除
		if(M('bid')->where("id=".$id)->setField('status',1)){
			$this->success("投标被逻辑删除了");
		}else{
			$this->success("已经删除了");
		}
	}
	function getout()
	{
		$id=I('id');
		//因为数据库默认为0,0改为上架状态，1改为逻辑删除
		if(M('bid')->where("id=".$id)->setField('status',3)){
			$this->success("投标被作废了");
		}else{
			$this->success("已经作废了");
		}
	}
}