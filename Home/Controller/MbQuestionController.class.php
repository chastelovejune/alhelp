<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class MbQuestionController extends HomeBaseController {
	public function add($types,$answers){
		M()->startTrans();
		M("mb_question")->where(["member_id"=>session("id")])->delete();
		for ($i=0; $i < 3; $i++) { 
			M("mb_question")->add(["member_id"=>session("id"),"type" => $types[$i],"answer"=>$answers[$i]]);
		}
		M()->commit();
		$this->showTrueJson();
	}
}