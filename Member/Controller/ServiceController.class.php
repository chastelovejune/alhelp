<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class ServiceController extends MemberBaseController {
    public function index($service_type) {
        $services = M("service")->where(["service_type"=>$service_type]);
        $keywords  =I("request.keywords");
        if ($keywords) {
            $where['detail'] = ['like',"%{$keywords}%"];
            $where['description'] = ['like',"%{$keywords}%"];
            $where['_logic'] = 'OR';
            $services = $services->where(['_complex'=>$where]);
        }
        $services = $services->where(['member_id'=>session("id")]);
        {
				$copy = clone $services;
				$this->count = $copy->count();
				unset($copy);
			}
		$services = $services->page($_GET['page']?:1,10);
		$services = $services->order('add_time desc')->select();
        $this->assign("service_type",$service_type);
        $this->assign("services", $services);
        $this->display();
    }
    
    public function orders($id){
        $service = M("service")->find($id);
        //访问过设置为已读
        /*
        $map['is_read']=1;
        M('learning_teaching_plan')->where("id=".$id)->save($map);*/
        if ($service['member_id'] != session("id")) {//只有老师自己可以看
            $this->redirect('/member/MemberPost');
        }
        $service = parse_class($service); 
        $service['orders'] = M("service_order_view")->join("member on member_id=member.id")->where(['service_id'=>$service['service_id'],'package_id'=>['gt',0]])->field("service_order_view.*,avatar,nickname,phone,qq,member.description as member_description")->order('service_order_view.id desc')->select();
        $this->service = $service;
       // dump($service);
        $this->display();
    }

    public function audition($id){
      /*  $map['is_read']=1;
        M('audition')->where("service_id=".$id)->save($map);//把此条记录设置为已读*/
        $service = M("service")->find(I('request.id'));
        if ($service['member_id'] != session("id")) {//只有老师自己可以看
            $this->redirect('/member/MemberPost');
        }
        $service = parse_class($service); 
        $service['auditions'] = M("audition")->join("member on member_id=member.id")->where(['service_id'=>$service['service_id']])->field("audition.*,avatar,nickname,phone,qq")->order("add_time desc")->select();
     //   dump($service);
        $this->service = $service;
        $this->display();
    }
	//上下架商品
	public function down()
	{
		M("service")->where(['service_id'=>I('request.id')])->setField("status",3);
	}
	public function up(){
		M("service")->where(['service_id'=>I('request.id')])->setField("status",0);
	}
    public function remark($id){
        $data['teacher_remark']=I('remark');
        M('service_order')->where("id=".$id)->save($data);
    }
}
