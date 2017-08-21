<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
require "../lib/curd.trail.php";
class RemarkController extends HomeBaseController {
	public function add(){
		$id=M("remark")->add($_POST);
		$r = M("remark")->find($id);
		$this->showTrueJson(to_minute($r['add_time']));
	}
	public function curdAdd(){
		$id=M("remark")->add($_POST);
		$r = M("remark")->find($id);
		$this->showTrueJson(to_minute($r['add_time']));
	}
}