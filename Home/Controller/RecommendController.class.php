<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class RecommendController extends HomeBaseController {
	public function _search($m){
		$keywords = I("request.keywords");
		if ($keywords) {
			$m = $m->where(['nickname'=>['like',"%$keywords%"]]);
		}
		return $m;
	}
	public function teacher(){
		$ts = M("mb_member")->where(['recommend_as_teacher'=>1]);
		$ts = $this->_search($ts);
		$c = clone $ts;
		$this->count = $c->count();
		$ts = $ts->select();
		$this->ts = $ts;
		$this->display();
	}

	public function student(){
		$ss = M("mb_member")->where(['recommend_as_student'=>1]);
		$ss = $this->_search($ss);
		$c = clone $ss;
		$this->count = $c->count();
		$ss = $ss->select();
		$this->ss = $ss;
		$this->display();
	}
}