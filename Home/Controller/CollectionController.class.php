<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class CollectionController extends HomeBaseController{
	public function add(){
		$c = $_POST;
		$c["member_id"] = session("id");
		$id = M("collection")->add($c);
		$this->showTrueJson($id);
	}
	public function delete(){
		$c = $_POST;
		$c['member_id'] = session("id");
		M("collection")->where($c)->delete();
		$this->showTrueJson($id);
	}
}