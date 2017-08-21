<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class DemandController extends ApiBaseController {

   //需求列表
    public function index($demand_type,$page) {
		
	
		if($demand_type == -1){
			$demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc,is_payed desc')->where(['demand.member_id = mb_member.id','demand.status = 0'])->field("mb_member.avatar,demand.*")->select();
			$count =M("demand")->count();
			$count = $count - ($page-1) * 10;
			}
		else{
		$demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc,is_payed desc')->where(['demand_type'=>$demand_type , 'demand.member_id = mb_member.id','demand.status = 0'])->field("mb_member.avatar,demand.*")->select();
			$count =M("demand")->where(['demand_type'=>$demand_type])->count();
			$count = $count - ($page-1) * 10;
		}
		
        
		foreach($demand as $index => $d){
           $d = parse_class($d);
           $demand[$index]=$d;
         }
	     		
		$data = array(
			'list' => $demand,
		    'count' => $count,
			
		);

		$this->success($data);
    }


   //服务列表
	public function ser($service_type,$page){
       
		if($service_type == -1){
			$service = M("service")->page($page,10)->order('service_id desc')->where(['service.status = 0'])->field("service.*,image as avatar")->select();
			$count =M("service")->count();
			$count = $count - ($page-1) * 10;
			}
		else{
		$service = M("service")->page($page,10)->order('service_id desc')->where(['service_type'=>$service_type ,'service.status = 0'])->field("service.*,image as avatar")->select();
		$count =M("service")->where(['service_type'=>$service_type])->count();
		$count = $count - ($page-1) * 10;
		}
		
        
		foreach($service as $index => $d){
           $d = parse_class($d);
           $service[$index]=$d;
         }
	     		
		$data = array(
			'list' => $service,
		    'count' => $count,
			
		);

		$this->success($data);
	}

	//需求详情
	public function getd($id){
	   $demand = M("demand")->find($id);
	   $demand = parse_class($demand);
	   $data = array(
		   'data' => $demand
	   );
	   $this->success($data);
	}

	//服务详情
	public function gets($id){
	   $service = M("service")->find($id);
       $service = parse_class($service);
	   $data = array(
		   'data' => $service
	   );
	   $this->success($data);
	}

	//判断是否已投标
	public function getbidstatus($id,$uid){
	   if(M("bid")->where(['demand_id'=>$id,'member_id'=>$uid])->find()){
	       $data = array(
			   'status'=>1
		   );
	   }else{
	     $data = array(
			   'status'=>0
		   );
	   }
	   $this->success($data);
	}

    //获取详细描述
	public function  getserdes($id){
		$des['detail'] = M("service")->field("detail")->find($id);
		$des['image'] = M("service")->field("image")->find($id);
		$atta = M("attachment")->where(['object_id'=>$id,'type = 2'])->field("id,path")->select();
		$des['attachment'] = $atta;
		$this->success($des);
		}

        //添加图片
		public function addImage(){
		
		$images = I("request.images");
		foreach ($images as  $path) {
			$attachment = ["path"=>$path,"member_id"=>I("request.id"),"object_id"=>I("request.object_id"),"type"=>2];
			M("attachment")->add($attachment);
		}
	    $data = array(
			'status' => 1,
	
			
		);
		$this->success($data);
		}

    //获取我的/某人的需求列表
    public function mydemand($demand_type,$page){
	   
	   if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];

		if($demand_type == -1){
			$demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc')->where(['demand.member_id'=>$user['id'],'demand.member_id = mb_member.id'])->field("mb_member.avatar,demand.*")->select();
			$count =M("demand")->where(['demand.member_id'=>$user['id']])->count();
			$count = $count - ($page-1) * 10;
			}
		else{
		$demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc')->where(['demand.member_id'=>$user['id'],'demand_type'=>$demand_type , 'demand.member_id = mb_member.id'])->field("mb_member.avatar,demand.*")->select();
			$count =M("demand")->where(['demand.member_id'=>$user['id'],'demand_type'=>$demand_type])->count();
			$count = $count - ($page-1) * 10;
		}
		
        
		foreach($demand as $index => $d){
           $d = parse_class($d);
           $demand[$index]=$d;
         }
	     		
		$data = array(
			'list' => $demand,
		    'count' => $count,
			
		);

		$this->success($data);
		}
	}

    //获取我的/某人的服务列表
	public function myService($service_type,$page){
	   if(IS_POST){
		   	$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];

	      if($service_type == -1){
			$service = M("service")->table('service service,mb_member mb_member')->page($page,10)->order('service_id desc')->where(['service.member_id'=>$user['id'],'service.member_id = mb_member.id'])->field("mb_member.avatar,service.*")->select();
			$count =M("service")->where(['service.member_id'=>$user['id']])->count();
			$count = $count - ($page-1) * 10;
			}
		 else{
		    $service = M("service")->table('service service,mb_member mb_member')->page($page,10)->order('service_id desc')->where(['service.member_id'=>$user['id'],'service_type'=>$service_type , 'service.member_id = mb_member.id'])->field("mb_member.avatar,service.*")->select();
		    $count =M("service")->where(['service.member_id'=>$user['id'],'service_type'=>$service_type])->count();
		    $count = $count - ($page-1) * 10;
		}
		
        
		foreach($service as $index => $d){
           $d = parse_class($d);
           $service[$index]=$d;
         }
	     		
		$data = array(
			'list' => $service,
		    'count' => $count,
			
		);

		$this->success($data);
	   }
	}
   
   //根据学校id获取需求列表
   public function schooldemand($schoolid,$demand_type,$page){
	   if($demand_type==-1){
           $demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc,is_payed desc')->where(['school_id'=>$schoolid,'demand.member_id = mb_member.id'])->field("mb_member.avatar,demand.*")->select();
	       $count = M("demand")->where(['school_id'=>$schoolid])->count();
	       $count = $count - ($page-1) * 10;
	   }else{
          $demand = M("demand")->table('demand demand,mb_member mb_member')->page($page,10)->order('demand_id desc,is_payed desc')->where(['school_id'=>$schoolid,'demand_type'=>$demand_type,'demand.member_id = mb_member.id'])->field("mb_member.avatar,demand.*")->select();
	      $count = M("demand")->where(['school_id'=>$schoolid,'demand_type'=>$demand_type])->count();
	      $count = $count - ($page-1) * 10;
	 }
      
	  $data = array(
			'list' => $demand,
		    'count' => $count,
			
		);
	 $this->success($data);
   }

   //根据学校id获取服务列表
   public function schoolservice($schoolid,$service_type,$page){
	  if($service_type==0){
         $service = M("service")->talbe('service service,mb_member mb_member')->page($page,10)->order('service_id desc')->where(['school_id'=>$schoolid,'service.member_id = mb_member.id'])->field("mb_member.avatar,service.*")->select();
	     $count = M("service")->where(['school_id'=>$schoolid])->count();
	     $count = $count - ($page-1) * 10;
	  }else{
         $service = M("service")->talbe('service service,mb_member mb_member')->page($page,10)->order
			 ('service_id desc')->where(['school_id'=>$schoolid,'service_type'=>$service_type,'service.member_id = mb_member.id'])->field("mb_member.avatar,service.*")->select();
	     $count = M("service")->where(['school_id'=>$schoolid,'service_type'=>$service_type])->count();
	     $count = $count - ($page-1) * 10;
	  }
      $data = array(
			'list' => $service,
		    'count' => $count,
			
		);
	 $this->success($data);
   }
	
    
  //公开课列表
	public function openclass($page){
       $openclass = M("open_class")->table('open_class open_class,mb_member mb_member')->page($page,10)->order('open_class_id desc')->where(['open_class.member_id = mb_member.id'])->field("mb_member.avatar,open_class.*")->select();
	   $count =M("open_class")->count();
	   $count = $count - ($page-1) * 10;
       
		foreach($openclass as $index => $d){
           $d = parse_class($d);
           $openclass[$index]=$d;
         }
	     		
		$data = array(
			'list' => $openclass,
		    'count' => $count,
			
		);

		$this->success($data);
	}
     
	 //根据学校id获取公开课列表
	 public function schoolopencls($schoolid,$page){
       $openclass = M("open_class")->table('open_class open_class,mb_member mb_member')->page($page,10)->order('open_class_id desc')->where(['open_class.member_id = mb_member.id','school_id'=>$schoolid])->field("mb_member.avatar,open_class.*")->select();
	   $count =M("open_class")->count();
	   $count = $count - ($page-1) * 10;
       
		foreach($openclass as $index => $d){
           $d = parse_class($d);
           $openclass[$index]=$d;
         }
	     		
		$data = array(
			'list' => $openclass,
		    'count' => $count,
			
		);

		$this->success($data);
	 }

    //我的公开课
    public function myopenclass($id,$page){
       $openclass = M("open_class")->table('open_class open_class,mb_member mb_member')->page($page,10)->order('open_class_id desc')->where(['open_class.member_id'=>$id,'open_class.member_id = mb_member.id'])->field("mb_member.avatar,open_class.*")->select();
	   $count =M("open_class")->where(['member_id'=>$id])->count();
	   $count = $count - ($page-1) * 10;
       
		foreach($openclass as $index => $d){
           $d = parse_class($d);
           $openclass[$index]=$d;
         }
	     		
		$data = array(
			'list' => $openclass,
		    'count' => $count,
			
		);

		$this->success($data);
    }

	//删除公开课
	public function delopenclass($id){
	     $res = M("open_class")->where(['open_class_id' => $id]) -> delete();
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


	//试听列表
	public function listen($id){
		$listen = M("apply_open_class")->table("apply_open_class open_class,mb_member mb_member")->where(['open_class_id'=>$id,'open_class.member_id = mb_member.id'])->field("open_class.apply_time as add_time,mb_member.nickname")->select();
		$data = array(
			'list'=>$listen
		);
		$this->success($data);
	}

	//申请公开课试听
	public function applyclass(){
		if(M("apply_open_class")->where(['member_id'=>I("request.member_id"),'open_class_id'=>I("request.class_id")])->find()){
		   $data=array(
			   'status' => '0'
		   );
		 
		   $this->success($data);
		}else{
		   $data['member_id'] = I("request.member_id");
		   $data['open_class_id'] = I("request.class_id");
		   $data['apply_time'] = date("Y-m-d H:i:s");

 		   $res = M("apply_open_class")->add($data);

		   $op = M("open_class")->find(I("request.class_id"));
		   //发送系统消息给老师
		$re=M('mb_member')->field('nickname')->where("id=".I("request.member_id"))->find();
		$data['to_member_id']=$op['member_id'];
		$data['type']=1;
		$data['readed']=0;
		$data['role'] = 1;
		$data['object_id']=I('request.class_id');
		$data['content']=$re['nickname']."学生已申请试听你的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'];
		M('message')->add($data);
		//发送系统消息给学生
		unset($data);
		$data['to_member_id']=I("request.member_id");
		$data['type']=1;
		$data['readed']=0;
		$data['role'] = 0;
		$data['object_id']=I('request.class_id');
		$data['content']="你已经申请试听".$op['teacher']."的".$op['description']."的直播课,上课时间".$op['open_class_time']."地点".$op['classroom'];
		M('message')->add($data);

		   $msg = array(
				   'status'=> '1',
				   
			   );
		  $this->success($msg);
		}       
	}

	//试听收费公开课
	public function applyclasspay(){
	     if(IS_POST){
		    $type = I("request.payMode");
			$ops = M("open_class")->find(I("request.class_id"));
			$m = M("mb_member")->find(I("request.member_id"));
		  
		  //添加试听
		   $data['member_id'] = I("request.member_id");
		   $data['open_class_id'] = I("request.class_id");
		   $data['apply_time'] = date("Y-m-d H:i:s");

 		   $res = M("apply_open_class")->add($data);

		   //打钱给老师
		   M("mb_member")->where(['id'=>$ops['member_id']])->setInc("balance",$ops['price']);

			if($type == 3){
				//生成交易记录
			   $record = array('type'=>4,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>0,
	                'balance' => $ops['price'],'object_id'=>$ops['open_class_id'],
	        );
	            M("payment_record")->add($record);
			 $record = array('type'=>4,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$ops['member_id'],
	                'balance' =>  $ops['price'],'object_id'=>$ops['open_class_id'],
	        );
	            M("payment_record")->add($record);
			}else{
			   //生成现金表
			   $cash = array('member_id'=>$m['id'],'balance'=>$ops['price'],'balance_now'=>$m['balance'],'balance_frozen'=>$m['balance_frozen'],
				   'pay_id'=>I("request.payId"),'is_alipay'=>I("request.payMode"),);
			   M("cash")->add($cash);
	        
			//交易记录
			  $record = array('type'=>4,'income_type'=>1,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => $ops['price'],'object_id'=>$ops['open_class_id'],
	        );
			  M("payment_record")->add($record);
			   $record = array('type'=>4,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>0,
	                'balance' => $ops['price'],'object_id'=>$ops['open_class_id'],
	        );
	            M("payment_record")->add($record);
			 $record = array('type'=>4,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$ops['member_id'],
	                'balance' =>  $ops['price'],'object_id'=>$ops['open_class_id'],
	        );
	            M("payment_record")->add($record);
			}

				   //发送系统消息给老师
		$data['to_member_id']=$ops['member_id'];
		$data['type']=9;
		$data['readed']=0;
		$data['role'] = 1;
		$data['object_id']=I('request.class_id');
		$data['content']=$m['nickname']."学生已申请试听你的直播课,上课时间".$ops['open_class_time']."地点".$ops['classroom'];
		M('message')->add($data);
		//发送系统消息给学生
		unset($data);
		$data['to_member_id']=I("request.member_id");
		$data['type']=9;
		$data['readed']=0;
		$data['role'] = 0;
		$data['object_id']=I('request.class_id');
		$data['content']="你已经申请试听".$ops['teacher']."的".$ops['description']."的直播课,上课时间".$ops['open_class_time']."地点".$ops['classroom'];
		M('message')->add($data);

		   $msg = array(
				   'status'=> '1',
				   
			   );
		  $this->success($msg);

		 }
	}

	//查看公开课试听状态
	public function isapply(){
	   if(M("apply_open_class")->where(['member_id'=>I("request.member_id"),'open_class_id'=>I("request.class_id")])->find()){
	   $msg = array(
				   'status'=> '1',
				   
			   );
		  $this->success($msg);
	   }else{
	   $msg = array(
				   'status'=> '0',
				   
			   );
		  $this->success($msg);
	   }

	}


	   //获取我的粉丝的说说
    public function talklistfans($schoolid,$page){

      if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId']; 
			$follow = M("follow")->where(['befollowed'=>$user['id']])->field("follow.follower")->select();			
				
		if(is_array($follow)&&count($follow)>0){
		  foreach($follow as $key => $value){
		      $followid[$key] = $value['follower']; 
		  }

		  $befollowed['member_id'] = array('in',$followid);

	   if($schoolid === null){
	        $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$befollowed,'member_post.member_id = mb_member.id'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where([$befollowed,'circle_ids'=>$schoolid])->count();
	        $count = $count - ($page-1) * 10;
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$befollowed,'member_post.member_id=mb_member.id','circle_ids'=>$schoolid])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where([$befollowed,'circle_ids'=>$schoolid])->count();
	        $count = $count - ($page-1) * 10;
		} 

				}else{
				
				$talklist = array();
				$count = '0';
				}

       foreach ($talklist as $i => $c) {
        $c['school'] = M("school")->field("title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;
      }	

				
		$data = array(
			'list' => $talklist,
		    'count' => $count,
			
		);

		$this->success($data);
	  }
	}
 

    //获取名师说说
	public function talklistteacher($schoolid,$page){
		  $member = M("mb_member")->order('id desc')->where(['mb_member.recommend_as_teacher = 1'])->field("mb_member.id")->select();
		if(is_array($member)&&count($member)>0)  { 
		  foreach($member as $key => $value){
		      $memberid[$key] = $value['id'];
		  }
		

		  $memberteacher['member_id'] = array('in',$memberid);

	 if($schoolid === null){		  
            $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$memberteacher,'member_post.member_id =mb_member.id'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where($memberteacher)->count();
	        $count = $count - ($page-1) * 10;
	       
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$memberteacher,'member_post.member_id = mb_member.id','circle_ids'=>$schoolid])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where([$memberteacher,'circle_ids'=>$schoolid])->count();
	        $count = $count - ($page-1) * 10;
		} }else{
		  
		  $talklist = array();
		  $count = '0';
		
		}
	     	
	 foreach ($talklist as $i => $c) {
        $c['school'] = M("school")->field("title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;
      }	
				
		$data = array(
			'list' => $talklist,
		    'count' => $count,
			
		);

		$this->success($data);
	}


	//获取评论列表
	public function commentlist($id,$comment_type){
	  
	  $comment = M("comment")->where(['object_id'=>$id,'comment_type'=>$comment_type])->select();
	  
	  $this->success($comment);
	
	}
	
	//发布服务
	public function releaseser(){
		 
	    if(IS_POST){
		
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$service = $post_data['service'];

			$member_id = $service['member_id'];
			$data['member_id']=$member_id;
		    $member_name = M("mb_member")->where(['id'=>$member_id])->field("nickname")->find();
			$data['member_name']=$member_name['nickname'];
			$data['service_type']=$service['demand_type'];
			$data['description']=$service['description'];
			$data['detail'] = $service['content'];			
			$data['require_identity']=$service['require_identity'];
			$data['require_authenticate']=$service['require_authenticate'];
			$data['require_security']=$service['require_security'];
			$data['profes_type']=intval($service['profes_type']);
			$data['major_id']=$service['major_id'];
			$data['school_id']=$service['school_id'];
			$data['public_subject_id']=intval($service['public_subject_id']);
			$data['cost']=$service['cost'];
			$data['qq']=$service['qq'];
			$data['wechat']=$service['weichat'];
			$data['mobile']=$service['mobile'];
			$data['add_time']=date("Y-m-d H:i:s");
			$data['unified_id'] = $service['unified_id'];
			$data['image'] = $service['images'];
						

			$ser = M("service"); 
            $ser->create();
			$result =$ser->add($data);
			
			//套餐
			$serpack = $service['packages'];
			$pack = json_decode($serpack,true);
					
			if(is_array($pack)&&count($pack)){
			   foreach ($pack as $package) {
				$package['service_id'] = $result;
				M("service_package")->add($package);
			   }
			}

			//加入圈子
			$m = M("mb_member")->find($member_id);
			if($service['school_id'] > 0){
			   $sid = M("zysc_view")->where(['zhuanye_id'=>$service['school_id']])->find();
			   $c = M("circle")->where(['object_id'=>$sid['school_id']])->find();
			  if(M("circle_member")->where(['circle_id'=>$c['id'],'member_id'=>$member_id])->find()){
			
			  }else{
			    $cm['circle_id'] = $c['id'];
			    $cm['member_id'] = $member_id;
			    M("circle_member")->add($cm);
				//加入校友圈群聊
				$im = new \Org\Util\Im();	
				if(M("group_manage")->where(['circle_id'=>$c['id']])->find()){
				$g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	      
	        	$im->group_join($m['chat_id'],$g['group_id']);
				}else{
				  $gid = $im->group_create('19999',$c['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $c['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
				}
		      }
			}	
			
			$map['unified_id'] = $service['unified_id'] ?: 0;
			$map['public_subject_id'] = $service['public_subject_id'] ?: 0;
			$map['school_id'] = $service['school_id'] ?: 0;
			$service_re = M('demand')->where($map)->group('member_id')->select();
			if(count($service_re)>0){
			   M("message")->add(['type'=>10,'to_member_id'=>$member_id,'object_id'=>$result,'content'=>'你好！已有学生发布了匹配的专业课服务啦！','role'=>1]);
			}
			require "../lib/Sms.class.php";
			$sms = new \Sms();
			foreach ($service_re as $k => $v) {
				$phone = M("mb_member")->field("phone,phone_verified,nickname")->find($v['member_id']);
				//发送系统消息,给学生
				$mess['type'] = 10;
				$mess['to_member_id'] = $v['member_id'];
				$mess['object_id'] = $v['demand_id'];
				$mess['content'] = "'".$m['nickname'] . "'同学发布的【" . $data['description'] . "】服务和你发布的【".$v['description']."】需求专业匹配";
				$mess['role'] = 0;
				M('message')->add($mess);				
				//发送手机短信给学生
				if ($phone['phone_verified'] == 1) {
					if (!empty($phone['phone'])) {
						$sms->sendToServer($phone['phone'], "你好！已有'" . $m['nickname'] . "'同学发布了匹配的专业课服务啦！请到“新助邦考研平台”微信公众号的个人中心，点击“系统消息”可直接联系到对方！");
					}
				}
			}		
					
			if($result){
				$msg = array(
				   'status'=> '1',				   
			   );
			   $this->success($msg);
			}else{
			   
			}
		}

	}
    

	//发布需求
	public function releasedem(){
	   if(IS_POST){		
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$service = $post_data['service'];

			$member_id = $service['member_id'];
			$data['member_id']=$member_id;
			$member_id = $service['member_id'];
			$data['member_id']=$member_id;
		    $member_name = M("mb_member")->where(['id'=>$member_id])->field("nickname")->find();
			$data['member_name']=$member_name['nickname'];
			$data['demand_type']=$service['demand_type'];
			$data['description']=$service['description'];
			$data['detail'] = $service['content'];
			$data['require_identity']=$service['require_identity'];
			$data['require_authenticate']=$service['require_authenticate'];
			$data['require_security']=$service['require_security'];
			$data['profes_type']=intval($service['profes_type']);
			$data['major_id']=$service['major_id'];
			$data['school_id']=$service['school_id'];
			$data['unified_id'] = $service['unified_id'];
			$data['public_subject_id']=intval($service['public_subject_id']);
			$data['cost']=$service['cost'];
			$data['qq']=$service['qq'];
			$data['mobile']=$service['mobile'];
			$data['add_time']=date("Y-m-d H:i:s");

			$ser = M("demand"); 
            $ser->create();
			$result = $ser->add($data);

			//加入圈子
			$m = M("mb_member")->find($member_id);
			if($service['school_id'] > 0){
			$sid = M("zysc_view")->where(['zhuanye_id'=>$service['school_id']])->find();
			$c = M("circle")->where(['object_id'=>$sid['school_id']])->find();
			if(M("circle_member")->where(['circle_id'=>$c['id'],'member_id'=>$member_id])->find()){
			
			}else{
			  $cm['circle_id'] = $c['id'];
			  $cm['member_id'] = $member_id;
			  M("circle_member")->add($cm);
			  //加入校友圈群聊			
				$im = new \Org\Util\Im();	
				if(M("group_manage")->where(['circle_id'=>$c['id']])->find()){
				$g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	      
	        	$im->group_join($m['chat_id'],$g['group_id']);
				}else{
				  $gid = $im->group_create('19999',$c['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $c['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
				}
			}
			}

			$map['unified_id'] = $service['unified_id'] ?: 0;
			$map['public_subject_id'] = $service['public_subject_id'] ?: 0;
			$map['school_id'] = $service['school_id'] ?: 0;
			$service_re = M('service')->where($map)->group('member_id')->select();
			if(count($service_re)>0){
			   M("message")->add(['type'=>10,'to_member_id'=>$member_id,'object_id'=>$result,'content'=>'你好！已有老师发布了匹配的专业课服务啦！','role'=>0]);
			}
			require "../lib/Sms.class.php";
			$sms = new \Sms();
			foreach ($service_re as $k => $v) {
				$phone = M("mb_member")->field("phone,phone_verified,nickname")->find($v['member_id']);
				//发送系统消息,给老师
				$mess['type'] = 10;
				$mess['to_member_id'] = $v['member_id'];
				$mess['object_id'] = $v['service_id'];
				$mess['content'] = "'".$m['nickname'] . "'同学发布的【" . $data['description'] . "】需求和你发布的【".$v['description']."】服务专业匹配";
				$mess['role'] = 1;
				M('message')->add($mess);				
				//发送手机短信给老师
				if ($phone['phone_verified'] == 1) {
					if (!empty($phone['phone'])) {
						$sms->sendToServer($phone['phone'], "你好！已有'" . $m['nickname'] . "'同学发布了匹配的专业课需求啦！请到“新助邦考研平台”微信公众号的个人中心，点击“系统消息”可直接联系到对方！");
					}
				}
			}		
			

			if($result){
				$msg = M("demand")->find($result);
				$m = M("mb_member")->where(['id'=>$service['member_id']])->field("nickname")->find();
				$msg['nickname'] = $m['nickname'];
				
			   $this->success($msg);
			}else{
			   
			}
	}

	}

		//发布公开课
	public function releaseclass(){
	   if(IS_POST){		
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$service = $post_data['openclass'];

			$member_id = $service['member_id'];
			$data['member_id']=$member_id;
			$member_id = $service['member_id'];
			$data['member_id']=$member_id;
		    $member_name = M("mb_member")->where(['id'=>$member_id])->field("nickname")->find();
			$data['member_name']=$member_name['nickname'];
			$data['description']=$service['description'];
			$data['content_course'] = $service['content_course'];
			$data['content_reference'] = $service['content_fef'];
			$data['classroom'] = $service['classroom'];
			$data['open_class_time'] = $service['time'];
            $data['remarks'] = $service['remarks'];
			$data['major_id']=$service['major_id'];
			$data['school_id']=$service['school_id'];
			$data['unified_id'] = $service['unified_id'];
			$data['public_subject_id']=intval($service['public_subject_id']);
			$data['price']=$service['price'];			
			$data['add_time']=date("Y-m-d H:i:s");

			$ser = M("open_class"); 
            $ser->create();
			$result =$ser->add($data);

			//加入圈子
			if($service['school_id'] > 0){
			$sid = M("zysc_view")->where(['zhuanye_id'=>$service['school_id']])->find();
			$c = M("circle")->where(['object_id'=>$sid['school_id']])->find();
			if(M("circle_member")->where(['circle_id'=>$c['id'],'member_id'=>$member_id])->find()){
			
			}else{
			  $cm['circle_id'] = $c['id'];
			  $cm['member_id'] = $member_id;
			  M("circle_member")->add($cm);
			  //加入校友圈群聊
				$m = M("mb_member")->find($member_id);
				$im = new \Org\Util\Im();	
				if(M("group_manage")->where(['circle_id'=>$c['id']])->find()){
				  $g = M("group_manage")->where(['circle_id'=>$c['id']])->find();	      
	        	  $im->group_join($m['chat_id'],$g['group_id']);
				}else{
				  $gid = $im->group_create('19999',$c['circle_name']);
				  $g['group_id'] = $gid;
				  $g['is_circle'] = 1;
				  $g['circle_id'] = $c['id'];
				  M("group_manage")->add($g);
                  $im->group_join($m['chat_id'],$gid);
				}
			}
			}
			
			if($result){
				$msg = array(
				   'status'=> '1',				   
			   );
			   $this->success($msg);
			}else{
			  $msg = array(
				   'status'=> '0',				   
			   );
			   $this->success($msg); 
			}
	}

	}

	//添加补充说明
	public function explainadd(){
		if(IS_POST){
			$res = M("remark")->add($_POST);
			$msg = array(
				'status' => '1'
			);
			$this->success($msg);
		}
	}

   //查询补充说明
	public function explainlist($id,$type){
		$plain = M("remark")->where(['is_demand'=>$type,'object_id'=>$id])->select();		
		$this->success($plain);
	}

	//删除需求
	public function demandcel($id){
	    $res = M("demand")->where(['demand_id'=>$id])->delete();
		if($res){
		  $msg = array(
			 'status' => '1' 
		  );
		}else{
		   $msg = array(
			  'status' => '0' 
		   );
		}

		$this->success($msg);
	}

		//删除需求
	public function servcecel($id){
	    $res = M("service")->where(['service_id'=>$id])->delete();
		if($res){
		  $msg = array(
			 'status' => '1' 
		  );
		}else{
		   $msg = array(
			  'status' => '0' 
		   );
		}

		$this->success($msg);
	}

	//说说消息
	public function talknews($id,$page){
	    $comment = M("comment")->field("object_id")->select();		
	
		if(is_array($comment)&&count($comment)){
		      foreach($comment as $key => $value){
		      $objectid[$key] = $value['object_id'];
		  }
		 
		     $talk['member_post.id'] = array('in',$objectid);

            
			$talkid =  M("member_post")->where([$talk,'member_post.member_id'=>$id])->field("member_post.id")->select();
			
			
			if(is_array($talkid)&&count($talkid)){
			  foreach($talkid as $key => $value){
		      $commentid[$key] = $value['id'];
		      }
			}

			$comments['object_id'] = array('in',$commentid);

			if(is_array($commentid)&&count($commentid)){
			$commentcon = M("comment")->table('comment comment,mb_member mb_member')->page($page,10)->where([$comments,'comment.member_id =mb_member.id','comment_type=3'])->field("mb_member.avatar,mb_member.nickname,comment.*")->select();

			$count = M("comment")->where([$comments,'comment_type=3'])->count();
			$count = $count - ($page-1)*10;
			}else{
			   $commentcon = array();
			   $count = 0;
			}
			
      foreach ($commentcon as $i => $c) {
        $c['member'] = M("member_post")->find($c['object_id']);
        $commentcon[$i]=$c;
      }

	  $data = array(
		  'list' => $commentcon,
		  'count' => $count
	  );
			$this->success($data);
		}
		
	}

    //服务套餐
	public function serpackage($id){
	   $package = M("service_package")->where(['service_id'=>$id])->select();
	   $this->success($package);
	}

	//获取需求合作方列表
	public function demteam($id){
		$bid = M("bid")->table("bid bid,mb_member mb_member")->where(['demand_id'=>$id,'member_id = mb_member.id'])->field("bid.description as content,add_time,mb_member.nickname")->select();
		$data = array(
		   'list' => $bid	
		);
		$this->success($data);
	}

	//获取服务合作方列表
	public function serteam($id){
	   $audition = M("audition")->table("audition audition,mb_member mb_member")->where(['service_id'=>$id,'member_id = mb_member.id'])->field("audition.wish_time as add_time,content,mb_member.nickname")->select();
	   $data = array(
		   'list' => $audition	
		);
		$this->success($data);
	}

}
