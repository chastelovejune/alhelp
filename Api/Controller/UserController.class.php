<?php
namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;


class UserController extends ApiBaseController {
    
	//用户登录
	  public function login(){
       			
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['passwordCredentials'];
						
            $member = M("mb_member")->where("email = '{$user['username']}' OR phone = '{$user['username']}' OR nickname='{$user['username']}' ")->find();
		
            if (!$member) {  
				 $data = array(
					'state' => '用户不存在'
				);
                $this->success($data);
			  
            }else if($member["password"] != $this->hashPwd($user['password'])){
     			 $data = array(
					'state' => '密码错误'
				);
                $this->success($data);
                 
            }else{
               session("id",$member["id"]);
				$user = M("mb_member")->where("email = '{$user['username']}' OR phone = '{$user['username']}' OR nickname='{$user['username']}' ")->field("id,nickname,is_male,follow_num,fans_num,chat_id")->find();
				 $data = array(
					 'list' => $user,
					'state' => '登录成功'
				);
                $this->success($data);               
            }              
    }

    //第三方登录
	public function login1(){
		if(IS_POST){
		$type = I("request.type");	
		if($type == "qq"){ //QQ
		   if(M("mb_member")->where(['qq_openid'=>I("request.uid")])->find()){ //是否已登录过
		      $user = M("mb_member")->where(['qq_openid'=>I("request.uid")])->field("id,nickname,is_male,follow_num,fans_num,qq_openid as uid,chat_id")->find();
			  $user['type'] = $type;
				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
		   }else{
		        $data['qq_openid'] = I("request.uid");  
                $m = array_merge($data,["nickname"=>"xzb".time()]);
                $m['avatar'] = "http://www.alhelp.net/Ucenter/images/noavatar_big.gif";
				$pwdinit = rand(100000, 999999);
				$pwd =  $this->hashPwd($pwdinit);
				$m['password'] = $pwd;
                $id = M("mb_member")->add($m);  
				$m['chat_id'] = 20000 + $id;
                M("mb_member")->where(['id'=>$id])->save($m);
				 $user = M("mb_member")->where(['id'=>$id])->field("id,nickname,is_male,follow_num,fans_num,qq_openid as uid,chat_id")->find();
				  $user['type'] = $type;

				 $im = new \Org\Util\Im();
				  $im->member_create($user['chat_id'],$user['nickname']);

				    //发送系统消息	    
				  $mes = ['type'=>0,'readed'=>0,'object_id'=>0,'content'=>'欢迎加入新助邦！你的初始注册名是：'.$user['nickname'].',初始密码是'.$pwdinit.'请到个人中心修改你的密码','to_member_id'=>$id,'role'=>0];
				  M("message")->add($mes);

				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
		   }
		}else if($type == "wx"){ //微信

			if(M("mb_member")->where(['wx'=>I("request.uid")])->find()){ //是否已登陆过
			     $user = M("mb_member")->where(['wx'=>I("request.uid")])->field("id,nickname,is_male,follow_num,fans_num,wx as uid,chat_id")->find();
				  $user['type'] = $type;
				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
			}else{
			     $data['wx'] = I("request.uid");
				  $m = array_merge($data,["nickname"=>"xzb".time()]);
                $m['avatar'] = "http://www.alhelp.net/Ucenter/images/noavatar_big.gif";
				$pwdinit = rand(100000, 999999);
				$pwd =  $this->hashPwd($pwdinit);
				$m['password'] = $pwd;
                $id = M("mb_member")->add($m);  
				$m['chat_id'] = 20000 + $id;
                M("mb_member")->where(['id'=>$id])->save($m);
				 $user = M("mb_member")->where(['id'=>$id])->field("id,nickname,is_male,follow_num,fans_num,wx as uid,chat_id")->find();
				  $user['type'] = $type;

				    $im = new \Org\Util\Im();
				  $im->member_create($user['chat_id'],$user['nickname']);

				   //发送系统消息	
				  $mes = ['type'=>0,'readed'=>0,'object_id'=>0,'content'=>'欢迎加入新助邦！你的初始注册名是：'.$user['nickname'].',初始密码是'.$pwdinit.'请到个人中心修改你的密码','to_member_id'=>$id,'role'=>0];
				  M("message")->add($mes);

				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
			}
		
		}else{ //微博
		    if(M("mb_member")->where(['weibo_uid'=>I("request.uid")])->find()){ //是否已登陆过
			     $user = M("mb_member")->where(['weibo_uid'=>I("request.uid")])->field("id,nickname,is_male,follow_num,fans_num,weibo_uid as uid,chat_id")->find();
				  $user['type'] = $type;
				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
			}else{
			     $data['weibo_uid'] = I("request.uid");
				  $m = array_merge($data,["nickname"=>"xzb".time()]);
                $m['avatar'] = "http://www.alhelp.net/Ucenter/images/noavatar_big.gif";
				$pwdinit = rand(100000, 999999);
				$pwd =  $this->hashPwd($pwdinit);
				$m['password'] = $pwd;
                $id = M("mb_member")->add($m);  
				$m['chat_id'] = 20000 + $id;
                M("mb_member")->where(['id'=>$id])->save($m);
				 $user = M("mb_member")->where(['id'=>$id])->field("id,nickname,is_male,follow_num,fans_num,weibo_uid as uid,chat_id")->find();
				  $user['type'] = $type;

				  $im = new \Org\Util\Im();
				  $im->member_create($user['chat_id'],$user['nickname']);

				  //发送系统消息	
				  $mes = ['type'=>0,'readed'=>0,'object_id'=>0,'content'=>'欢迎加入新助邦！你的初始注册名是：'.$user['nickname'].',初始密码是'.$pwdinit.'请到个人中心修改你的密码','to_member_id'=>$id,'role'=>0];
				  M("message")->add($mes);
				  
				 $data = array(
					 'list' => $user,
					'state' => '1'
				);
				
                $this->success($data);
			}	
		}
		}
	}

   //用户注册 ，注册成功，同时生成im会员
	public function register(){

        if (IS_POST) {
            $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['register'];         			
       if ($user['username'] && M("mb_member")->where(["nickname"=>$user['username']])->find()) {
		        $data = array(
					'state' => '用户名已存在'
				);
                $this->success($data);
        	}else if ($user['phone'] && M("mb_member")->where(["phone"=>$user['phone']])->find()) {
				 $data = array(
					'state' => '电话已存在'
				);
                $this->success($data);
        	}else{
                $m = $_POST;
				  $m['nickname']=$user['username'];
				  $m['phone']=$user['phone'];
				  $m['password']=$this->hashPwd($user['password']);
                  $m['avatar'] = "http://www.alhelp.net/Ucenter/images/noavatar_big.gif";
				  $m['phone_verified'] = 1;
        		  $uid = M("mb_member")->add($m);

				  $m['chat_id'] = 20000 + $uid;
                M("mb_member")->where(['id'=>$id])->save($m);

				  $im = new \Org\Util\Im();
				  $im->member_create($$m['chat_id'],$user['nickname']);
               
                  $data = array(			        
		            'state' => '注册成功'			
		            );
                  $this->success($data);				
				
        	}
        }else{
           
        } 
    }
  
     //修改用户名片
     public function updateuserinfo(){
          if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['cardInfo'];  
			M("mb_member")->where(["id"=>$user['id']])->save($user);
			 $data = array(			        
		            'state' => '修改成功'			
		            );
			$this->success($data);
		  }
     }

     //账户与安全
	 public function security(){
	      if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];  
			$usersec = M("mb_member")->where(["id"=>$user['id']])->field("id,phone_verified,email_verified,phone,email,qq,status,is_male")->find();
			$this->success($usersec);
		  }
	 }

	 //绑定手机
	 public function phoneverified(){
	    if(IS_POST){
			$where['id'] = array('neq',I("request.id"));
			$where['phone'] = array('eq',I("request.phone"));
		   if(M("mb_member")->where($where)->find()){
		       $data = array(			        
		            'state' => '号码已被绑定'			
		            );
			$this->success($data);
		   }else{
			   $m['phone'] = I("request.phone");
			   $m['phone_verified'] = 1;
			   M("mb_member")->where(['id'=>I("request.id")])->save($m);
		     $data = array(			        
		            'state' => '1'			
		            );
			$this->success($data);
		   }
		}
	 }

     //修改用户信息
	 public function update(){
	    if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userInfo'];  

			M("mb_member")->where(["id"=>$user['id']])->save($user);
			 $data = array(			        
		            'state' => '修改成功'			
		            );
			$this->success($data);
		}
	 }

     //更换头像
	 public function head(){
	     $data['avatar'] = I("request.avatar");
          $res = M("mb_member")->where(["id"=>I("request.id")])->save($data);
		  if($res){
		    $msg = array(
	          'status' => '1'
	         );
		 $this->success($msg);
		  }else{
		   $msg = array(
	          'status' => '0'
	         );
		 $this->success($msg);
		  }
	 }

    //修改密码
	public function modifypassword(){
	     if(IS_POST){
			 //$posw = json_decode(I("request.passwordInfo")],true);
			 $posw = I("request.passwordInfo");
		     $data['password'] = md5($posw['confirmPassword']);
			$res = M("mb_member")->where(["id"=>$posw['id']])->save($data);			
			 if($res){
		   $msg = array(
			 'status' => '1'   
		   );
		   $this->success($msg);
		}else{
		 $msg = array(
			 'status' => '0'   
		   );
		   $this->success($msg);
		}
		 }
	}

	//找回密码
	public function findpassword(){
	 
			if(IS_POST){
			 $posw = I("request.findPassword");
		     $data['password'] = md5($posw['confirmPassword']);
			$res = M("mb_member")->where(["phone"=>$posw['phone']])->save($data);			
			 if($res){
		   $msg = array(
			 'status' => '1'   
		   );
		   $this->success($msg);
		}else{
		 $msg = array(
			 'status' => '0'   
		   );
		   $this->success($msg);
		}
			} 
	}

	//设置支付密码
	public function paypassword(){
	
	     if(IS_POST){
		     $data['paypassword'] = md5(I("request.paypassword"));
			$res = M("mb_member")->where(["id"=>I("request.id")])->save($data);			
			 if($res){
		   $msg = array(
			 'status' => '1'   
		   );
		   $this->success($msg);
		}else{
		 $msg = array(
			 'status' => '0'   
		   );
		   $this->success($msg);
		}
		 }
	
	}

	//判断支付密码
	public function getpaypassword(){
		if(IS_POST){
			
		$password = M("mb_member")->where(['id'=>I("request.id")])->field("paypassword")->find();	

		if($password){
			if($password['paypassword'] != md5(I("request.paypassword"))){
			   $data = array(
				   'status' => '密码错误'
			   );
			}else{
			    $data = array(
				   'status' => '1'
			   );
			}
		}else{
			$data = array(
				'status' => "请先设置支付密码"
			);
		}
		$this->success($data);
		}
	}

	//修改状态
	public function status(){
		 if(IS_POST){
			
			 $posw = I("request.member");
		     $data['status'] = $posw['status'];
			$res = M("mb_member")->where(["id"=>$posw['id']])->save($data);			
			 if($res){
		   $msg = array(
			 'status' => '1'   
		   );
		   $this->success($msg);
		}else{
		 $msg = array(
			 'status' => '0'   
		   );
		   $this->success($msg);
		}
		 }
	}

     //获取我的名片
	 public function card(){
	     if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];  
			$usersec = M("mb_member")->where(["id"=>$user['id']])->field("sign,friend_description")->find();
			
			$this->success($usersec);
		  }
	 }

     //获取我的信息、根据id获取用户信息
	 public function userface(){
	 	  if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];  
			$usersec = M("mb_member")->where(["id"=>$user['id']])->field("avatar")->find();
			
			$this->success($usersec);
		  }
	 }
     
	 //根据id获取用户名片
	 public function usercard(){
	  if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];  
			$usersec = M("mb_member")->where(["id"=>$user['id']])->field("nickname,sign,friend_description")->find();
			
			$this->success($usersec);
		  }
	 }

     //根据id获取用户信息
	 public function userinfo(){
	   if(IS_POST){
		    $c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];  
			$usersec = M("mb_member")->where(["id"=>$user['id']])->find();
			
			$this->success($usersec);
		  }
	 }

     //获取用户的粉丝
	 public function fans($page){
	   
	  if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId']; 
			$follow = M("follow")->page($page,10)->where(['befollowed'=>$user['id']])->field("follow.follower")->select();			
			$count = M("follow")->where(['befollowed'=>$user['id']])->count();
			 $count = $count - ($page-1) * 10;

		if(is_array($follow)&&count($follow)>0){
		  foreach($follow as $key => $value){
		      $followid[$key] = $value['follower'];
		  }

		  $befollowed['id'] = array('in',$followid);
		  $fans = M("mb_member")->where($befollowed)->field("id,avatar,nickname")->select();
		  

	   foreach ($follow as $i => $c) {
        $res = M("follow")->where(['befollowed'=>$c['follower'],'follower'=>$user['id']])->find();
        if($res){
		   $mul[$i] = '1';
		}else{
		    $mul[$i] = '0';
		}
      }

	   foreach ($fans as $i => $c) {
		   $fans[$i]['is_mutual'] = $mul[$i];		    
      }

           }else{
		      $fans = array();
		   }
		   

		  $data = array(
			  'list'=>$fans,
			  'count'=>$count
		  );

		  $this->success($data);
		}
	  
	 }
    
	//获取用户关注的人
    public function follow($page){
	    if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId']; 
			$follow = M("follow")->page($page,10)->where(['follower'=>$user['id']])->field("follow.befollowed")->select();	
			$count = M("follow")->where(['follower'=>$user['id']])->count();
			 $count = $count - ($page-1) * 10;
			
			if(is_array($follow)&&count($follow)){
				 foreach($follow as $key => $value){
		      $followid[$key] = $value['befollowed'];
		  } 	   
		  $befollowed['id'] = array('in',$followid);
		  $followers = M("mb_member")->where($befollowed)->field("id,avatar,nickname")->select();

		foreach ($follow as $i => $c) {
        $res = M("follow")->where(['follower'=>$c['befollowed'],'befollowed'=>$user['id']])->find();
         if($res){
		   $mul[$i] = '1';
		}else{
		    $mul[$i] = '0';
		}
      }

	   foreach ($followers as $i => $c) {
		   $followers[$i]['is_mutual'] = $mul[$i];		    
      }


			}else{
				$followers = array();
				}	

           
				$data = array(
				   'list' => $followers,
					'count'=> $count
				);

		  $this->success($data);

		}
	}
   

  //获取关注状态
  public function isfollowed($uid,$id){
     $result = M("follow")->where(['befollowed'=>$uid,'follower'=>$id])->find();
	 if($result){
			   $msg = array(  
				   'status'=> '1', //已关注
				   
			   );
			   $this->success($msg);
			}else{
			    $msg = array(
				   'status'=> '0', //未关注
				   
			   );
			   $this->success($msg);
			}
  }

  //关注/取消关注某人
  public function followed(){
     if(IS_POST){
	    $c = file_get_contents('php://input');
        $post_data = json_decode($c, true);
		$followed = $post_data['follow'];  
		if($followed['status']){
		$data['befollowed'] = $followed['befollowed'];
		$data['follower'] = $followed['follower'];
		$data['add_time'] = date("Y-m-d H:i:s");

		$follow = M("follow"); 
            $follow->create();
			$result =$follow->add($data);

			 $member = M("mb_member");			   
			   $member -> create();			   
			   $member->where(['id'=>$followed['follower']])->setInc('follow_num');	
			   $member->where(['id'=>$followed['befollowed']])->setInc('fans_num');
			   
			if($result){
				 $msg = array(  
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			   
			}else{
			   
			}
		}else{
		  $result = M("follow")->where(['befollowed'=>$followed['befollowed'],'follower'=>$followed['follower']])->delete();
		  $member = M("mb_member");			   
			   $member -> create();			   
			   $member->where(['id'=>$followed['follower']])->setDec('follow_num');	
			   $member->where(['id'=>$followed['befollowed']])->setDec('fans_num');
		  if($result){
			   	 $msg = array(  
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
			}else{
			   
			}
		}
        
	 }
  }

}