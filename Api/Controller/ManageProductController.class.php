<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class ManageProductController extends ApiBaseController {

    //下架/上架服务
	public function soldoutservice($status,$id){

		$data['status'] = $status;
		$res = M("service")->where(['service_id' => $id])->save($data);
		 if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   
			}else{
			 $msg = array(
				   'status'=> '0',
				   
			   );
			   
			}
			$this->success($msg);
	}

	    //下架/上架需求
	public function soldoutdemand($status,$id){

		$data['status'] = $status;
		$res = M("demand")->where(['demand_id' => $id])->save($data);
		 if($res){
			  $msg = array(
				   'status'=> '1',
				   
			   );
			   
			}else{
			 $msg = array(
				   'status'=> '0',
				   
			   );
			   
			}
			$this->success($msg);
	}


}