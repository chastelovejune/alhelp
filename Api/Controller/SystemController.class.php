<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class SystemController extends ApiBaseController {

	public function systemnews($type,$id,$page){
		if($type == -1){
		   $sysnews = M("message")->page($page,10)->order('id desc')->where(['to_member_id'=>$id])->select();
		   $count = M("message")->where(['to_member_id'=>$id])->count();
		   $count = $count - ($page-1) * 10;
		}else{
		   $sysnews = M("message")->page($page,10)->order('id desc')->where(['type'=>$type,'to_member_id'=>$id])->select();
		   $count = M("message")->where(['type'=>$type,'to_member_id'=>$id])->count();
		   $count = $count - ($page-1) * 10;
		}
		
		$data = array(
		   'list' => $sysnews,
			'count' => $count
		);
		$this->success($data);
	}

	public function unreadnews($id){
	  $news = M("message")->order('id desc')->order('id desc')->where(['to_member_id'=>$id,'readed = 0','type <> 11'])->select();
	  $data = array(
		  'list' => $news
	  );
	  $this->success($data);
	}

	//设置消息为已读
	public function readed($id){
	    $ms['readed'] = 1;
		M("message")->where(['id'=>$id])->save($ms);
        $data = array(
		  'status' => 1
	     );
		$this->success($data);
	}

	//获取所有消息
	public function getnews($type,$id,$page){
	   $news = M("message")->where(['type'=>$type,'to_member_id'=>$id])->page($page,20)->select();
       $count = M("message")->where(['type'=>$type,'to_member_id'=>$id])->count();
	   $count = $count - ($page-1) * 10;
	   $data = array(
		   'list' => $news,
		   'count' => $count
		);
		$this->success($data);
	}

}