<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class DemandController extends MemberBaseController {
    public function index($demand_type) {
    
      
        $demands = M("demand")->where(["demand_type"=>$demand_type]);
        $keywords  =I("request.keywords");
        if ($keywords) {
            $where['detail'] = ['like',"%{$keywords}%"];
            $where['description'] = ['like',"%{$keywords}%"];
            $where['_logic'] = 'OR';
            $demands = $demands->where(['_complex'=>$where]);
        }
        $demands = $demands->where(['member_id'=>session("id")]);
        $demands = $demands->order('demand_id desc')->select();
       
        $this->assign("demands", $demands);
		//dump($demands);
        $this->display();
    }
    public function soldout(){

    		$id=I('id');
    		$data['status']=3;//状态3为下架 
    		$re=M('demand')->where('demand_id='.$id)->save($data);
    		if($re){
    			$this->ajaxReturn($re);
    		}
    } 
    public function onsold(){

            $id=I('id');
            $data['status']=0;//状态3为下架 
            $re=M('demand')->where('demand_id='.$id)->save($data);
            if($re){
                $this->ajaxReturn($re);
            }
    }
	
    
}
