<?php
namespace Home\Model;
use Think\Model;

class CartModel extends BaseModel {
	public function buildIndex(){
		$member_ids = M("cart")->join("LEFT JOIN service ON cart.service_id=service.service_id")->distinct(true)->field("service.member_id")->where(["cart.member_id"=>session("id")])->select();
		foreach ($member_ids as $member_id) {
			$member_id = $member_id["member_id"];
			$member = M("mb_member")->field("nickname,avatar,id")->where(["id"=>$member_id])->find();
			$member["carts"] = M("cart")
			->join("LEFT JOIN service ON cart.service_id=service.service_id")
			->where(["cart.member_id" => session("id"),"service.member_id"=>$member_id]);
			$cart_ids = I("request.cart_ids");
			if (array_count($cart_ids) > 0) {
				$member["carts"] = $member["carts"]->where(['cart.id'=>['in',$cart_ids]]);
			}
			$member["carts"] = $member["carts"]->field("cart.*,service.description,cost,count")->select();
			if (count($member["carts"]) > 0) {
				$members[] = $member;
			}
		}
		return $members;
	}  
}