<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
use \Home\Model\CartModel;
class CartController extends HomeBaseController {
	public function index(){	
		$members = CartModel::buildIndex();
		$this->assign("members",$members);
		$this->display();
	}
	public function changeNum(){
		M("cart")->save($_POST);
		$this->showTrueJson();
	}
	public function delete(){
		M("cart")->delete(I("request.id"));
		$this->showTrueJson();
	}
	public function add(){
		$c = array_merge($_POST,["member_id"=>session("id")]);
		$id = M("cart")->where($c)->getField("id");
		if ($id) {
			M("cart")->where($c)->setInc("count");		
		}else{
			M("cart")->add($c);
		}
		if (IS_AJAX) {
			$this->showTrueJson();
		}else{
			$this->redirect("/home/cart/index");	
		}
	}
	
	public function clear()
	{
		file_put_contents("F://id.txt",I("request.id"));
		M("cart")->where(["member_id" => I("request.id")])->delete();
		$this->showTrueJson();
	}
	
}