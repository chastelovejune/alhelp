<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class TalkController extends ApiBaseController {

	//发说说 备注：如果是有偿提问，生成流水
	public function addtalk(){

	    $data['member_id'] = I("request.id");
		$data['information_id'] = I("request.infor");
		$data['circle_id'] = I("request.circle");
		$name = M("mb_member")->where(['id'=>I("request.id")])->field("nickname")->find();
		$data['member_nickname'] = $name['nickname'];
		$data['content'] = I("request.content");
		$data['view_permission'] = I("request.view");
		$data['reward'] = I("request.reward");
		$data['invite'] = I("request.invite");
		$data['fid'] = I("request.fid");
		$data['status'] = '1';
		$data['add_time'] = date("Y-m-d H:i:s");

		if(I("request.fid")!=0){
	    	M("member_post")->where(['id'=>I("request.fid")])->setInc('f_number');
		}
		
		$t = M("member_post")->where(['id'=>I("request.fid")])->find();
		if($t['pid'] == 0){			
			$data['pid'] = I("request.fid");
		}else{
			$data['pid'] = $t['pid'];
            M("member_post")->where(['id'=>$t['pid']])->setInc('f_number');
		}

        $res = M("member_post")->add($data);


		if(I("request.type") == '1'){ //有偿提问
			
			if(I("request.payMode") == '3'){ //余额支付
				 $record = array('type'=>7,'income_type'=>0,
	                'from_member_id'=>I("request.id"),'to_member_id'=>0,
	                'balance' => I("request.reward"),'object_id'=>$res);
				 M("payment_record")->add($record);
			}else{ 

				$cash['member_id'] = I("request.id");
				$cash['balance'] = I("request.reward");
				$cash['pay_id'] = I("request.payId");
				$cash['is_alipay'] = I("request.payMode");
				M("cash")->add($cash);

			   $record = array('type'=>7,'income_type'=>1,
	                'from_member_id'=>I("request.id"),'to_member_id'=>I("request.id"),
	                'balance' => I("request.reward"),'object_id'=>$res);
	            M("payment_record")->add($record);//first
				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second	
			}
		}
		
		$images = I("request.images");

		foreach ($images as  $path) {
			$attachment = ["path"=>$path,"member_id"=>I("request.id"),"object_id"=>$res];
			M("attachment")->add($attachment);
		}						

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


	//点赞说说 
	public function praise($id,$uid){

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$id,'type = 0'])->find()){
			
		    M("member_post")->where(['id' => $id])->setDec('praise_num');
		   
		    M("praise")->where(['member_id'=>$uid,'object_id'=>$id])->delete();
			$praisenum = M("member_post")->where(['id' => $id])->field("praise_num")->find();
			$data = array(
				'praise_num' => $praisenum['praise_num'],
				'is_praised' => 0
			);
			$this->success($data);
		
		}else{
		  M("member_post")->where(['id' => $id])->setInc('praise_num');
		    $data['type'] = 0;
		    $data['member_id'] = $uid;
		    $data['object_id'] = $id;
		    $data['add_time'] = date("Y-m-d H:i:s");
		    M("praise")->add($data);
			$praisenum = M("member_post")->where(['id' => $id])->field("praise_num")->find();
			$data = array(
				'praise_num' => $praisenum['praise_num'],
				'is_praised' => 1
			);
			$this->success($data);
		}	
	}



	 //获取说说首页
	public function talklist($schoolid,$page,$uid){

		if($schoolid == 0){
			
			$talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('is_top desc,id desc')->where(['member_post.member_id = mb_member.id','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select();
	        
	        $count =M("member_post")->where(['member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where(['member_post.member_id = mb_member.id','circle_id'=>$schoolid,'member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select();
	        $count =M("member_post")->where(['circle_id'=>$schoolid,'member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		}

		  foreach ($talklist as $i => $c) {
            $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
            $talklist[$i]=$c;
		    $c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select(); //获取图片
		    $talklist[$i]=$c;
		  if($c['information_id'] != '0'){
		    $content = $c['content']."<a href='http://www.alhelp.com/home_information_show_id_".$c['information_id'].".html'></a>";
		    $talklist[$i]['content']=$content;
		   }

		//获取转发说说
		$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
		$talklist[$i]=$c; 		
		
		if($c['accept']){   //获取选中答案
		$acm = M("answer")->field("member_id,add_time")->find($c['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talklist[$i]['acceptm'] = $ac;
		}

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$c['id'],'type = 0'])->find()){ //判断点赞情况
		  $talklist[$i]['is_praised'] = 1;
		}else{
		  $talklist[$i]['is_praised'] = 0;
		}

		//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$c['id'],'ans.member_id = m.id'])->field("m.avatar")->select();		
		$talklist[$i]['answer']= $ans;
		
		//是否偷看过
		if(M("benefit")->where(['member_id'=>$uid,'member_post_id'=>$c['id']])->find()){
		   $talklist[$i]['isbenefit'] = 1;
		}else{
	    	$talklist[$i]['isbenefit'] = 0;
		}

			//邀请人是否已回答
		if($c['invite']){
			if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->find()){
				$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->field('add_time')->find();
				$c['mb_invite']['ans_time'] = $a['add_time'];
		        $talklist[$i]=$c;					
			}else{
			    $c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 0;
		        $talklist[$i]=$c;	
			}
		}

		//是否抢答过
		 if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$uid])->find()){
		    $talklist[$i]['isansed'] = 1;
		 }else{
		   $talklist[$i]['isansed'] = 0;
		 }
  
      }	
	  	     		
		$data = array(
			'list' => $talklist,
		    'count' => $count,
			
		);

		$this->success($data);		
	}

	//说说详情
	public function talkdetail($id,$uid){
		$talk= M("member_post")->table('member_post member_post,mb_member mb_member')->where(['member_post.id'=>$id,'member_post.member_id = mb_member.id','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->find();
       $talk['school'] = M("circle")->field("circle_name as title")->find($talk['circle_id']);
		$talk['attachment'] = M("attachment")->where(['object_id'=>$talk['id'],'type = 0'])->field("path")->select(); //获取图片

		if($talk['information_id'] != '0'){
		   $content = $talk['content']."<a href='http://www.alhelp.com/home_information_show_id_".$talk['information_id'].".html'></a>";
		   $talk['content']=$content;
		}

		$talk['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($talk['invite']); //获取邀请人
        
		$talk['f_content'] = M("member_post")->field("id,member_nickname,content")->find($talk['pid']);//获取转发说说

		if($talk['accept']){   //获取选中答案
		$acm = M("answer")->field("member_id,add_time")->find($talk['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talk['acceptm'] = $ac;
		}

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$talk['id'],'type = 0'])->find()){ //判断点赞情况
		  $talk['is_praised'] = 1;
		}else{
		  $talk['is_praised'] = 0;
		}

			//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$talk['id'],'ans.member_id = m.id'])->field("m.avatar")->select();
		$talk['answer']= $ans;

		//是否偷看过
		if(M("benefit")->where(['member_id'=>$uid,'member_post_id'=>$id])->find()){
		   $talk['isbenefit'] = 1;
		}else{
	    	$talk['isbenefit'] = 0;
		}
		
			//邀请人是否已回答
		if($talk['invite']){
			if(M("answer")->where(['object_id'=>$talk['id'],'member_id'=>$talk['invite']])->find()){
				$talk['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($talk['invite']);
				$talk['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$talk['id'],'member_id'=>$talk['invite']])->field('add_time')->find();
				$talk['mb_invite']['ans_time'] = $a['add_time'];
		      					
			}else{
			    $talk['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($talk['invite']);
				$talk['mb_invite']['isansed'] = 0;
		        	
			}
		}

			//是否抢答过
		if(M("answer")->where(['object_id'=>$talk['id'],'member_id'=>$uid])->find()){
		   $talk['isansed'] = 1;
		}else{
		   $talk['isansed'] = 0;
		}

		//
		if($talk['reward']){
		   $acount = M("answer")->where(['object_id'=>$talk['id']])->count('id');
		   $bcount = M("benefit")->where(['member_post_id'=>$talk['id']])->count('id');
           $talk['acount'] = $acount;
		   $talk['bcount'] = $bcount;
		  }

		$this->success($talk);
		
	}

	 //获取我关注的人的说说
    public function talklistfollow($schoolid,$page,$uid){
		if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId']; 
			$follow = M("follow")->where(['follower'=>$user['id']])->field("follow.befollowed")->select();
			if(is_array($follow)&&count($follow)){
				 foreach($follow as $key => $value){
		      $followid[$key] = $value['befollowed'];
		  }

		  $follower['member_id'] = array('in',$followid);

		if($schoolid === null){
	        $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$follower,'member_post.member_id = mb_member.id','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where([$follower,'circle_ids'=>$schoolid,'member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where([$follower,'member_post.member_id = mb_member.id','circle_ids'=>$schoolid,'member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where([$follower,'circle_ids'=>$schoolid,'member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		
			} }else{
			    $talklist = array();
				$count = '0';
			}
     		
				  foreach ($talklist as $i => $c) {
        $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;

		//获取转发说说
		$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
		$talklist[$i]=$c;

		if($c['accept']){
		$acm = M("answer")->field("member_id,add_time")->find($c['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talklist[$i]['acceptm'] = $ac;
		}

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$c['id'],'type = 0'])->find()){
		  $talklist[$i]['is_praised'] = 1;
		}else{
		  $talklist[$i]['is_praised'] = 0;
		}

		//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$c['id'],'ans.member_id = m.id'])->field("m.avatar")->select();
		$talklist[$i]['answer']= $ans;

		//是否偷看过
		if(M("benefit")->where(['member_id'=>$uid,'member_post_id'=>$c['id']])->find()){
		   $talklist[$i]['isbenefit'] = 1;
		}else{
	    	$talklist[$i]['isbenefit'] = 0;
		}

			//邀请人是否已回答
		if($c['invite']){
			if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->find()){
				$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->field('add_time')->find();
				$c['mb_invite']['ans_time'] = $a['add_time'];
		        $talklist[$i]=$c;					
			}else{
			    $c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 0;
		        $talklist[$i]=$c;	
			}
		}

		//是否抢答过
		 if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$uid])->find()){
		    $talklist[$i]['isansed'] = 1;
		 }else{
		   $talklist[$i]['isansed'] = 0;
		 }
  

      }	

		$data = array(
			'list' => $talklist,
		    'count' => $count,
			
		);

		$this->success($data); 
		}	
	}

	 //获取某人的说说
	public function talklistme($page){
	  		
		if(IS_POST){
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
			$user = $post_data['userId'];
		
	   if($schoolid === null){

            $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where(['member_post.member_id '=>$user['id'],'member_post.member_id = mb_member.id','reward <= 0','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where(['member_post.member_id '=>$user['id'],'reward <= 0','member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;

	       
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where(['member_post.member_id '=>$user['id'],'member_post.member_id = mb_member.id','reward <= 0','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select(); 
	        $count =M("member_post")->where(['member_post.member_id '=>$user['id'],'reward <= 0','member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		} 
	  

     foreach ($talklist as $i => $c) {
        $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;	
		
		//获取转发说说
		$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
		$talklist[$i]=$c;
      }	
				
		$data = array(
			'list' => $talklist,
		    'count' => $count,
			
		);

		$this->success($data);
		}
	   
	}

	//获取有偿提问
	public function quizelist($schoolid,$page,$uid){
		if($schoolid == 0){
			
			$talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('is_top desc,id desc')->where(['member_post.member_id = mb_member.id','reward > 0 ','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select();
	        
	        $count =M("member_post")->where(['reward > 0','member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		}else{
		    $talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('id desc')->where(['member_post.member_id = mb_member.id','circle_id'=>$schoolid,'reward >0','member_post.status = 1'])->field("mb_member.avatar,member_post.*")->select();
	        $count =M("member_post")->where(['circle_id'=>$schoolid,'reward > 0','member_post.status = 1'])->count();
	        $count = $count - ($page-1) * 10;
		}

		  foreach ($talklist as $i => $c) {
        $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;

		if($c['information_id'] != '0'){
		   $content = $c['content']."<a href='http://www.alhelp.com/home_information_show_id_".$c['information_id'].".html'></a>";
		   $talklist[$i]['content']=$content;
		}
		
		
		//获取转发说说
		$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
		$talklist[$i]=$c;	    

		if($c['accept']){
		$acm = M("answer")->field("member_id,add_time")->find($c['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talklist[$i]['acceptm'] = $ac;
		}

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$c['id'],'type = 0'])->find()){
		  $talklist[$i]['is_praised'] = 1;
		}else{
		  $talklist[$i]['is_praised'] = 0;
		}

		//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$c['id'],'ans.member_id = m.id'])->field("m.avatar")->select();
		$talklist[$i]['answer']= $ans;

		//是否偷看过
		if(M("benefit")->where(['member_id'=>$uid,'member_post_id'=>$c['id']])->find()){
		   $talklist[$i]['isbenefit'] = 1;
		}else{
	    	$talklist[$i]['isbenefit'] = 0;
		}

			//邀请人是否已回答
		if($c['invite']){
			if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->find()){
				$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->field('add_time')->find();
				$c['mb_invite']['ans_time'] = $a['add_time'];
		        $talklist[$i]=$c;					
			}else{
			    $c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 0;
		        $talklist[$i]=$c;	
			}
		}

		//是否抢答过
		 if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$uid])->find()){
		    $talklist[$i]['isansed'] = 1;
		 }else{
		   $talklist[$i]['isansed'] = 0;
		 }
  

      }	
	  	     		
		$data = array(
			'list' => $talklist,
		    'count' => $count,			
		);

		$this->success($data);	
	}


	//获取我的有偿提问
	public function userquize($page,$id){
	        $answer = M("answer")->table("answer answer,member_post member_post")->page($page,5)->where(['answer.member_id'=>$id,'answer.object_id = member_post.id','member_post.status = 1'])->field("member_post.*,answer.id as aid")->select();
            $count1 = M("member_post")->table("answer answer,member_post member_post")->where(['answer.member_id'=>$id,'answer.object_id = member_post.id','member_post.status = 1'])->count();

			$talklist = M("member_post")->table('member_post member_post')->page($page,5)->order('is_top desc,id desc')->where(['reward > 0 ','member_id'=>$id,'status = 1'])->select();
	        
	        $count2 =M("member_post")->where(['reward > 0','member_id'=>$id,'status = 1'])->count();

            $count = $count1 + $count2;
	        $count = $count - ($page-1) * 10;

			$talklist = array_merge_recursive($talklist, $answer);

			foreach ($talklist as $i => $c) {

				$m =  M("mb_member")->field("avatar")->find($c['member_id']);
		        $talklist[$i]['avatar'] = $m['avatar'];	
              
                $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
                $talklist[$i]=$c;
		        $c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		        $talklist[$i]=$c;

		     if($c['information_id'] != '0'){
		       $content = $c['content']."<a href='http://www.alhelp.com/home_information_show_id_".$c['information_id'].".html'></a>";
		       $talklist[$i]['content']=$content;
	        	}
			
			//获取转发说说
	     	$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
	     	$talklist[$i]=$c;   		

			 if($c['accept']){
		$acm = M("answer")->field("member_id,add_time")->find($c['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talklist[$i]['acceptm'] = $ac;
		}

		$m = M("mb_member")->field("avatar")->find($c['member_id']);
		$talklist[$i]['avatar'] = $m['avatar'];

		//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$c['id'],'ans.member_id = m.id'])->field("m.avatar")->select();
		$talklist[$i]['answer']= $ans;

		//是否偷看过
		if(M("benefit")->where(['member_id'=>$id,'member_post_id'=>$c['id']])->find()){
		   $talklist[$i]['isbenefit'] = 1;
		}else{
	    	$talklist[$i]['isbenefit'] = 0;
		}

		//邀请人是否已回答
		if($c['invite']){
			if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->find()){
				$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->field('add_time')->find();
				$c['mb_invite']['ans_time'] = $a['add_time'];
		        $talklist[$i]=$c;					
			}else{
			    $c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 0;
		        $talklist[$i]=$c;	
			}
		}
      }	
	  	     		
		$data = array(
			'list' => $talklist,
		    'count' => $count,			
		);

		$this->success($data);	
	}


	public function getmemberquiz($page,$id,$uid){
		$talklist = M("member_post")->table('member_post member_post,mb_member mb_member')->page($page,10)->order('is_top desc,id desc')->where(['member_post.member_id = mb_member.id','reward > 0 ','member_post.status = 1','member_post.member_id'=>$id])->field("mb_member.avatar,member_post.*")->select();
	        
	    $count =M("member_post")->where(['reward > 0','member_post.status = 1'])->count();
	    $count = $count - ($page-1) * 10;

		  foreach ($talklist as $i => $c) {
        $c['school'] = M("circle")->field("circle_name as title")->find($c['circle_id']);
        $talklist[$i]=$c;
		$c['attachment'] = M("attachment")->where(['object_id'=>$c['id'],'type = 0'])->field("path")->select();
		$talklist[$i]=$c;

		if($c['information_id'] != '0'){
		   $content = $c['content']."<a href='http://www.alhelp.com/home_information_show_id_".$c['information_id'].".html'></a>";
		   $talklist[$i]['content']=$content;
		}
		
		$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
		$talklist[$i]=$c;	
		
		//获取转发说说
		$c['f_content'] = M("member_post")->field("id,member_nickname,content")->find($c['pid']);
		$talklist[$i]=$c;	    

		if($c['accept']){
		$acm = M("answer")->field("member_id,add_time")->find($c['accept']);
		$acceptm = M("mb_member")->field("nickname,avatar")->find($acm['member_id']);
		$ac['nickname'] = $acceptm['nickname'];
        $ac['avatar'] = $acceptm['avatar'];
		$ac['add_time'] = $acm['add_time'];
		$talklist[$i]['acceptm'] = $ac;
		}

		if (M("praise")->where(['member_id'=>$uid,'object_id'=>$c['id'],'type = 0'])->find()){
		  $talklist[$i]['is_praised'] = 1;
		}else{
		  $talklist[$i]['is_praised'] = 0;
		}

		//获取答案
		$ans = M("answer")->table("answer ans,mb_member m")->where(['object_id'=>$c['id'],'ans.member_id = m.id'])->field("m.avatar")->select();
		$talklist[$i]['answer']= $ans;

		//是否偷看过
		if(M("benefit")->where(['member_id'=>$uid,'member_post_id'=>$c['id']])->find()){
		   $talklist[$i]['isbenefit'] = 1;
		}else{
	    	$talklist[$i]['isbenefit'] = 0;
		}

		//邀请人是否已回答
		if($c['invite']){
			if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->find()){
				$c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 1;
                $a = M("answer")->where(['object_id'=>$c['id'],'member_id'=>$c['invite']])->field('add_time')->find();
				$c['mb_invite']['ans_time'] = $a['add_time'];
		        $talklist[$i]=$c;					
			}else{
			    $c['mb_invite'] = M("mb_member")->field("avatar,nickname")->find($c['invite']);
				$c['mb_invite']['isansed'] = 0;
		        $talklist[$i]=$c;	
			}
		}

		//是否抢答过
		 if(M("answer")->where(['object_id'=>$c['id'],'member_id'=>$uid])->find()){
		    $talklist[$i]['isansed'] = 1;
		 }else{
		   $talklist[$i]['isansed'] = 0;
		 }
  

      }	
	  	     		
		$data = array(
			'list' => $talklist,
		    'count' => $count,			
		);

		$this->success($data);	

	}

	//抢答
	public function ans(){
		if(IS_POST){			
			$ans = $_POST;
			$ans['content'] = addslashes(I("request.content"));
			$ansid = M("answer")->add($ans);		
			$images = I("request.images");

	    	foreach ($images as  $path) {
			  $attachment = ["path"=>$path,"member_id"=>I("request.member_id"),"object_id"=>$ansid,"type"=>3];
			  M("attachment")->add($attachment);
		    }	
			 
			 if($ansid){
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

	//编辑答案
	public function editans(){
	if(IS_POST){			

		    $ans['content'] = I("request.content");
			$ans['add_time'] = date("Y-m-d H:i:s");
			$res = M("answer")->where(['id'=>I("request.object_id")])->save($ans);
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

	//获取答案
	public function getanscontent($id){
	   $content = M("answer")->where(['id'=>$id])->find();
	   $content['content'] = html_entity_decode($content['content'], ENT_QUOTES, 'UTF-8');
	   $data = array(
		   'con' => $content
	   );
	   $this->success($content);
	}

	//获取答案图片
	public function ansimage(){
		if(IS_POST){
			$ansid = M("answer")->where(['object_id'=>I("request.object_id"),'member_id'=>I("request.member_id")])->find();
			$img = M("attachment")->where(['object_id'=>$ansid['id'],'type = 3'])->select();
			$this->success($img);
		}
	}

		//获取答案图片
	public function ansimage1($id,$mid){
		
			$ansid = M("answer")->where(['object_id'=>$id,'member_id'=>$mid])->find();
			$img = M("attachment")->where(['object_id'=>$ansid['id'],'type = 3'])->select();
			$this->success($img);
		
	}


	//查看所有答案
	public function anslist(){
		if(IS_POST){
			$answer = M("answer")->table("answer answer , mb_member mb_member")->where(['answer.member_id = mb_member.id','answer.object_id'=>I("request.id")])->field("answer.*,mb_member.avatar,mb_member.nickname")->select();

			foreach($answer as $i => $c){
			   $answer[$i]['content'] = html_entity_decode($c['content'], ENT_QUOTES, 'UTF-8');
			}

			/*foreach($answer as $i => $c){
				$img = M("attachment")->where(['object_id'=>$c['id'],'type = 3'])->field("path")->select();
				$answer[$i]['attachment'] = $img;
			}*/

			$this->success($answer);
		}
	}

	//选中答案
	public function ansselect(){
        if(IS_POST){
			$ans['accept'] = 1;
			M("answer")->where(['id'=>I("request.id")])->save($ans);
			$answer = M("answer")->where(['id'=>I("request.id")])->find();
			$post['accept'] = I("request.id");
			$res = M("member_post")->where(['id'=>$answer['object_id']])->save($post);

			$talk = M("member_post")->where(['id'=>$answer['object_id']])->find();
			
			//放款
			$tom = M("mb_member")->where(['id'=>$answer['member_id']])->find();
			$tom['balance'] = $talk['reward'] + $tom['balance'];
			M("mb_member")->where(['id'=>$answer['member_id']])->save($tom);

			//生成流水 备注：新助邦->被悬赏人
              $record = array('type'=>2,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$answer['member_id'],
	                'balance' => $talk['reward'],'object_id'=>$talk['id']);
				 M("payment_record")->add($record);

			if($res){
				$msg = array(
					'status' => 1
				);
			}else{
				$msg = array(
					'status' => 0
				);
			}
			$this->success($msg);
		}
	}

     //添加查看答案人员
	 public function addbenefit(){
		 if(IS_POST){
			 
			 $be['member_id'] = I("request.benefit");
			 $be['member_post_id'] = I("request.id");
			 $be['readed'] = 0;
			 $res = M("benefit")->add($be);

			 $talk = M("member_post")->where(['id'=>I("request.id")])->find();
			 $ans = M("answer")->where(['id'=>$talk['accept']])->find();
			 //放款
			 $tom1 = M("mb_member")->where(['id'=>$ans['member_id']])->find(); //被悬赏者
			 $tom2 = M("mb_member")->where(['id'=>$talk['member_id']])->find(); //提问者
			
			 $balance = I("request.total") * 0.8;;				
			 $tom1['balance'] = $tom1['balance'] + $balance;          
			 M("mb_member")->where(['id'=>$ans['member_id']])->save($tom1);

			 $balance = I("request.total") * 0.2;
			 $tom2['balance'] = $tom2['balance'] + $balance;
			 M("mb_member")->where(['id'=>$talk['member_id']])->save($tom2);

			 $m = M("mb_member")->where(['id'=>I("request.member_id")])->find();
			 //生成流水
			 if(I("request.payMode") == "3"){ //余额支付，不用生成现金表   
				 
			 }else{
				 $cash['member_id'] = I("request.member_id");
				 $cash['balance'] = I("request.total");
				 $cash['balance_now'] = $m['balance'];
				 $cash['balance_frozen'] = $m['balance_frozen'];
				 $cash['pay_id'] = $m['payId'];
				 $cash['is_alipay'] = $m['payMode'];
				 $cash_id = M("cash")->add($cash);
             
			  //付款者->付款者微银
              $record = array('type'=>8,'income_type'=>1,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>I("request.member_id"),
	                'balance' => I("request.total"),'object_id'=>$talk['id']);
			  $record_id = M("payment_record")->add($record);

			  $cash['payment_record_id'] = $record_id;
			  M("cash")->where(['id'=>$cash_id])->save($cash);

			 }
			
			  //付款者微银->新助邦微银
              $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>I("request.member_id"),'to_member_id'=>0,
	                'balance' => I("request.total"),'object_id'=>$talk['id']);
				 M("payment_record")->add($record);
             
			   //新助邦微银->被悬赏者微银
			   $balance = I("request.total") * 0.8;
			   $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$tom1['id'],
	                'balance' => $balance,'object_id'=>$talk['id']);
				 M("payment_record")->add($record);
			
				//新助邦微银->提问者微银
				$balance = I("request.total") * 0.2;
			   $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$tom2['id'],
	                'balance' => $balance,'object_id'=>$talk['id']);
				 M("payment_record")->add($record);
			 

			 if($res){
				$msg = array(
					'status' => 1
				);
			}else{
				$msg = array(
					'status' => 0
				);
			}
			$this->success($msg);
		 }
	 }

	//答案内容
	public function anscontent(){
		if(IS_POST){
			$answer = M("answer")->where(['id'=>I("request.id")])->find();
			$answer['content'] = html_entity_decode($answer['content'], ENT_QUOTES, 'UTF-8');
			$this->success($answer);
		}
	}

	public function test1($id){
	    $answer = M("answer")->where(['id'=>$id])->find();
			$answer['content'] = html_entity_decode($answer['content'], ENT_QUOTES, 'UTF-8');
			$this->success($answer);
	}

	//查看看答案的人
	public function benefits($id){
		$benefit = M("benefit")->table("benefit be,mb_member mb")->where(['be.member_post_id'=>$id,'be.member_id = mb.id'])->field('be.*,mb.avatar,mb.nickname')->select();
		$this->success($benefit);
	}

	//点赞列表
	public function praiselist($id){
		$praise = M("praise")->where(['object_id'=>$id,'type = 0'])->select();
		foreach($praise as $i => $c){
           $m = M("mb_member")->field("nickname,avatar")->find($c['member_id']);
		   $praise[$i]['avatar'] = $m['avatar'];
		   $praise[$i]['nickname'] = $m['nickname'];
		}
		$this->success($praise);
	}
	

	//转发列表
	public function flist($id){
		$talk= M("member_post")->table('member_post member_post,mb_member mb_member')->where(['member_post.fid'=>$id,'member_post.member_id = mb_member.id'])->field("mb_member.avatar,mb_member.nickname,member_post.add_time")->select(); 
        $this->success($talk);
	}

		//获取说说评论
		public function commentlist($id){

			//$where = "SELECT * FROM comment as node,comment as parent where node.pid BETWEEN parent.pid AND parent.id and node.id-node.pid=1;";

			//$where = "SELECT * FROM comment as node,comment as parent where node.object_id = $id AND node.comment_type = 3 AND parent.object_id = $id AND parent.comment_type = 3 AND node.pid BETWEEN parent.pid AND parent.id and node.id-node.pid=1 group by parent.id;";
		

			//$where = "SELECT * FROM 
			 // (select * from 
			  //     comment as node,comment as parent where node.pid BETWEEN parent.pid AND parent.id and node.id-node.pid=1) as t
			//	   where object_id = $id AND comment_type = 3;";

			
			$where = "SELECT 
                    a.*
                      FROM
               comment a
                  INNER JOIN
               comment b ON a.pid = b.id
                  WHERE
               a.object_id = $id AND a.comment_type = 3";

	          $comment1 = M("comment")->where(['object_id'=>$id,'comment_type = 3'])->select();
					

			  foreach($comment1 as $i => $c){

				  $m = M("mb_member")->where(['id'=>$c['member_id']])->field('avatar,nickname')->find();
				  $comment1[$i]['avatar'] = $m['avatar'];
                  $comment1[$i]['nickname'] = $m['nickname'];

				  $com = M("comment")->where(['id'=>$c['pid']])->find();
				  $c = M("mb_member")->where(['id'=>$com['member_id']])->field('nickname')->find();
				  $comment1[$i]['to'] = $c['nickname'];
			  }

			$this->success($comment1);		
		}

		public function comc($id,$pid,$com){
			foreach($com as $i => $c){
				$com[$i] = $this->comchild($id,$pid);
			}
			return $com;
		}

		public function comchild($id,$pid){
			$comment = M("comment")->where(['object_id'=>$id,'comment_type = 3','pid' => $pid])->find();
			return $comment;
		}


    //评论
    public function comment(){
	    if(IS_POST){		
			$c = file_get_contents('php://input');
            $post_data = json_decode($c, true);
            $comment = $post_data['comment'];
            
			$data['pid'] = $comment['pid'];
			$data['member_id'] = $comment['id'];
			$data['comment_type'] = $comment['type'];
			$data['object_id'] = $comment['object_id'];
			$data['content'] = $comment['content'];
			$data['add_time'] = date("Y-m-d H:i:s");

			$com = M('comment');
			$com -> create();
			$result=$com->add($data);
            
			if($result){
			    
               $talk = M("member_post");			   
			   $talk -> create();
			   
			   $talk->where(['id'=>$comment['object_id']])->setInc('comments_num');
			   $num = M("member_post")->where(['id'=>$comment['object_id']])->field("comments_num")->find();

			  $msg = array(
				   'status'=> '1',
				   'comments_num' => $num['comments_num']
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
    
	//删除说说评论/回复
	public function commentdel($id){

		$objectid = M("comment")->where(['id'=>$id])->field("object_id")->find();
 
		 $talk = M("member_post");	
	     $talk -> create();			   
		 $talk->where(['id'=>$objectid['object_id']])->setDec('comments_num');
		 
		$res = M("comment")->where(['id'=>$id])->delete();

		if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}

	}

	//删除说说
	public function talkdel($id){
		$res = M("member_post")->where(['id'=>$id])->delete();
		if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}
	}


   //设置说说热门
	public function talkhot($id){
	    $talk['is_hot'] = 1;
		$res = M("member_post")->where(['id'=>$id])->save($talk);
	if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}
	}

	   //取消说说热门
	public function talkhotoff($id){
	    $talk['is_hot'] = 0;
		$res = M("member_post")->where(['id'=>$id])->save($talk);
	if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}
	}

    //设置说说公告
    public function talkann($id){
	   $talk['is_announcement'] = 1;
	   $res = M("member_post")->where(['id'=>$id])->save($talk);
       if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}
	}

	//取消说说公告
    public function talkannoff($id){
	   $talk['is_announcement'] = 0;
	   $res = M("member_post")->where(['id'=>$id])->save($talk);
       if($res){
			
		  $msg = array(
				   'status'=> '1',
				   
			   );
			   $this->success($msg);
		}
	}

	//测试
	public function de($id){
		M("answer")->where(['object_id'=>$id])->delete();
		$this->success("ok");
	}

    //测试
	public function dem($id){
		M("member_post")->where(['id'=>$id])->delete();
		$this->success("ok");
	}

	//test
	public function test(){
		echo "hello";
	}

}