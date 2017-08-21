<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class AddressController extends HomeBaseController {
	public function add(){
		$add = $_POST;
		$add["member_id"] = session("id");
		$id = M("address")->add($add);
		if (I("request.is_default") == 1) {
			M("address")->where(["member_id" => session("id")])->save(["is_default"=>0]);
			M("address")->where(["id" => $id])->save(["is_default"=>1]);
		}
		if (M("address")->where(["member_id" => session("id")])->count() == 1) {//第一个
			M("address")->save(['is_default'=>1,'id'=>$id]);
		}
		$this->showTrueJson();
	}
	public function update(){
		M("address")->where(["id"=>I("request.id")])->save($_POST);
		if (I("request.is_default") == 1) {
			M("address")->where(["member_id" => session("id")])->save(["is_default"=>0]);
			M("address")->where(["id" => I("request.id")])->save(["is_default"=>1]);
		}
		$this->showTrueJson();
	}
	public function delete(){
		M("address")->where(["id"=>I("request.id")])->delete();
		$this->redirect("/home/order/confirm");	
	}
}
