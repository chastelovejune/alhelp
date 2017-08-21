<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class CommunityNewsController extends ApiBaseController {
   
   //评论消息
   public function commentnews($id,$page){
      $mps = M("member_post")->table("comment comment,member_post member_post")->page($page,20)->where(['member_post.member_id'=>$id,'member_post.id = comment.object_id','comment_type = 3','member_post.reward = 0','member_post.status = 1'])->field("member_post.id as mpid,member_post.content as mpcontent,member_post.circle_id,comment.*")->order('comment.id desc')->select();
	  $count = M("comment")->table("comment comment,member_post member_post")->where(['member_post.member_id'=>$id,'member_post.id = comment.object_id','comment_type = 3','member_post.reward = 0','member_post.status = 1'])->count();
	   $count = $count - ($page-1) * 10;
	 foreach($mps as $i => $c){
	   $m = M("mb_member")->field("nickname,avatar")->find($c['member_id']);
	   $mps[$i]['nickname'] = $m['nickname'];
       $mps[$i]['avatar'] = $m['avatar'];
       $mpm = M("mb_member")->field("nickname,avatar")->find($id);
	   $mps[$i]['mpnickname'] = $mpm['nickname'];
       $mps[$i]['mpavatar'] = $mpm['avatar'];
	   $c = M("circle")->field("circle_name")->find($c['circle_id']);
	   $mps[$i]['circle'] = $c['circle_name'];
	 }
	 $noread = M("comment")->table("comment comment,member_post member_post")->where(['member_post.member_id'=>$id,'member_post.id = comment.object_id','comment_type = 3','member_post.reward = 0','member_post.status = 1','comment.readed = 0'])->count();
	 $data = array(
		 'list'=>$mps,
		 'count'=>$count,
		 'noreadcount'=>$noread
	 );

	 $this->success($data);
	 
   }

   //点赞消息
   public function praisenews($id,$page){
       $praise = M("member_post")->table("member_post mp,praise pa")->page($page,10)->where(['mp.member_id'=>$id,'mp.reward = 0','mp.status = 1','mp.id = pa.object_id','pa.type = 0'])->field("mp.content as mpcontent,mp.circle_id,pa.*")->order('pa.id desc')->select();
	     $count =  M("member_post")->table("member_post mp,praise pa")->where(['mp.member_id'=>$id,'mp.reward = 0','mp.status = 1','mp.id = pa.object_id','pa.type = 0'])->count();
	     $count = $count - ($page-1) * 10;
	  foreach($praise as $i => $c){
	   $m = M("mb_member")->field('avatar,nickname')->find($c['member_id']);
	   $praise[$i]['avatar'] = $m['avatar'];
	   $praise[$i]['nickname'] = $m['nickname'];
	    $mpm = M("mb_member")->field("nickname,avatar")->find($id);
	   $praise[$i]['mpnickname'] = $mpm['nickname'];
       $praise[$i]['mpavatar'] = $mpm['avatar'];
	   $c = M("circle")->field("circle_name")->find($c['circle_id']);
	   $praise[$i]['circle'] = $c['circle_name'];
	  }
	 $noread = M("praise")->table("member_post mp,praise pa")->where(['mp.member_id'=>$id,'mp.reward = 0','mp.status = 1','mp.id = pa.object_id','pa.type = 0','pa.readed = 0'])->count();

	 $data = array(
		 'list'=>$praise,
		 'count'=>$count,
		 'noreadcount'=>$noread
	 );

	 $this->success($data);

   }

   //回答我的
   public function ansmenews($id,$page){
       $answer = M("answer")->table("answer answer,member_post member_post")->page($page,10)->order('answer.id desc')->where(['member_post.member_id'=>$id,'answer.object_id = member_post.id','member_post.status = 1'])->field("answer.id as aid,answer.content as acontent,answer.add_time as ans_time,answer.member_id as anm,member_post.*")->select();
		$count = M("answer")->table("answer answer,member_post member_post")->where(['member_post.member_id'=>$id,'answer.object_id = member_post.id','member_post.status = 1'])->count();
	     $count = $count - ($page-1) * 10;
		foreach($answer as $i =>$c){
			$ma = M("mb_member")->field("avatar,nickname")->find($c['anm']);
			$answer[$i]['anickname'] = $ma['nickname'];
			$answer[$i]['avatar'] = $ma['avatar'];
			$qm = M("mb_member")->field("avatar")->find(session('id'));
			$answer[$i]['qavatar'] = $qm['avatar'];	
		}

	$noread = M("answer")->table("answer answer,member_post mp")->where(['mp.member_id'=>$id,'mp.reward > 0','mp.status = 1','mp.id = answer.object_id'])->count();

	 $data = array(
		 'list'=>$answer,
		 'count'=>$count,
		 'noreadcount'=>$noread
	 );

	 $this->success($data);
   }

   //我被选中
   public function anssenews($id,$page){
	     $answer = M("answer")->table("answer answer,member_post member_post")->order('answer.id desc')->where(['answer.member_id'=>$id,'answer.accept = 1','answer.object_id = member_post.id','member_post.status = 1'])->field("answer.id as aid,answer.content as acontent,answer.add_time as ans_time,member_post.*")->select();
		
		$count = M("answer")->table("answer answer,member_post member_post")->where(['answer.member_id'=>$id,'answer.accept = 1','answer.object_id = member_post.id','member_post.status = 1'])->count();
	    $count = $count - ($page-1) * 10;

		foreach($answer as $i =>$c){
			$ma = M("mb_member")->field("avatar")->find($id);
			$answer[$i]['avatar'] = $ma['avatar'];
			$qm = M("mb_member")->field("avatar")->find($c['member_id']);
			$answer[$i]['qavatar'] = $qm['avatar'];		
		}

    	$noread = M("answer")->table("answer answer,member_post mp")->where(['answer.member_id'=>$id,'answer.accept = 1','answer.object_id = mp.id','mp.status = 1'])->count();

	    $data = array(
		 'list'=>$answer,
		 'count'=>$count,
		 'noreadcount'=>$noread
	    );

	 $this->success($data);
   }

   //看我答案
   public function benefitnews($id,$page){
	   $bes = M("benefit")->table("benefit be,member_post mp")->where(['be.member_post_id = mp.id','mp.member_id'=>$id,'mp.status = 1','mp.reward > 0'])->field("mp.*,mp.id as mpid,mp.member_id as mp_member_id,be.*")->select();	

	   $count = M("benefit")->table("benefit be,member_post mp")->where(['be.member_post_id = mp.id','mp.member_id'=>$id,'mp.status = 1','mp.reward > 0'])->count();
	    $count = $count - ($page-1) * 10;

	  foreach($bes as $i => $c){
	    $bem = M("mb_member")->field('avatar,nickname')->find($c['member_id']);
		$bes[$i]['avatar'] = $bem['avatar'];
		$bes[$i]['anickname'] = $bem['nickname'];       
		$ans = M("answer")->field("content,member_id")->find($c['accept']);
		$ansm = M("mb_member")->field('avatar,nickname')->find($ans['member_id']);
        $bes[$i]['qavatar'] = $ansm['avatar'];
		$bes[$i]['member_nickname'] = $ansm['nickname']; 
		$bes[$i]['content'] = $ans['content'];
	  }
	  
   	$noread = M("benefit")->table("benefit be,member_post mp")->where(['be.member_post_id = mp.id','mp.member_id'=>$id,'mp.status = 1','mp.reward > 0'])->count();

	    $data = array(
		 'list'=>$bes,
		 'count'=>$count,
		 'noreadcount'=>$noread
	    );

	 $this->success($data);
   }

   public function getquiznews($id){
	   $ansmenoread = $this->getansmenoread($id);
	   $meacceptnoread = $this->getmeacceptnoread($id);
	   $benefitnoread = $this->getbenefitnoread($id);
	   $noread = $ansmenoread + $meacceptnoread + $benefitnoread;
	   $data = array(
		   'count' => $noread
	   );
	   $this->success($data);
   }

   public function gettalknews($id){
	   $commentnoread = $this->getcommentnoread($id);
	   $praisenoread = $this->getpraisenoread($id);
	   $noread = $commentnoread + $praisenoread;
	      $data = array(
		   'count' => $noread
	   );
	   $this->success($data);
   }

   public function getcommentnoread($id){
      $noread = M("comment")->table("comment comment,member_post member_post")->where(['member_post.member_id'=>$id,'member_post.id = comment.object_id','comment_type = 3','member_post.reward = 0','member_post.status = 1','comment.readed = 0'])->count();
	  return $noread;
	  //$this->success($noread);
   }

   public function getpraisenoread($id){
	  $noread = M("praise")->table("member_post mp,praise pa")->where(['mp.member_id'=>$id,'mp.reward = 0','mp.status = 1','mp.id = pa.object_id','pa.type = 0','pa.readed = 0'])->count();
     // $this->success($noread);
	 return $noread;
   }

   public function getansmenoread($id){
	   $noread = M("answer")->table("answer answer,member_post mp")->where(['mp.member_id'=>$id,'mp.reward > 0','mp.status = 1','mp.id = answer.object_id','readed = 0'])->count();
      // $this->success($noread);
	  return $noread;
   }

   public function getmeacceptnoread($id){
	   $noread = M("answer")->table("answer answer,member_post mp")->where(['answer.member_id'=>$id,'answer.accept = 1','answer.object_id = mp.id','mp.status = 1','readed = 0'])->count();
	  // $this->success($noread);
	  return $noread;
   }

   public function getbenefitnoread($id){
	   	$noread = M("benefit")->table("benefit be,member_post mp")->where(['be.member_post_id = mp.id','mp.member_id'=>$id,'mp.status = 1','mp.reward > 0','readed = 0'])->count();
		//$this->success($noread);
		return $noread;
   }

   //未读消息
   public function getnoreadcount($id){
	   $asme = $this->getansmenoread($id);
	   $meac = $this->getmeacceptnoread($id);
	   $be = $this->getbenefitnoread($id);
	   $com = $this->getcommentnoread($id);
	   $pa = $this->getpraisenoread($id);
	   $quiz = $asme + $meac + $be;
	   $talk = $com + $pa;
	   $total = $quiz + $talk;
	   $data = array(
		   'asme' => $asme,
		   'meac' => $meac,
		   'be' => $be,
		   'com' => $com,
		   'pa' => $pa,
		   'quiz' => $quiz,
		   'talk' => $talk,
		   'total' => $total
	   );
	   $this->success($data);
   }

   //设置评论消息为已读
   public function setcommentread($id){
	  $nums = $_POST['object_id'];		 
	  $num = json_decode($nums,true);	
	  M("comment")->where(['id'=>['in',$num]])->save(['readed'=>true]);
	  $com = $this->getcommentnoread($id);
      $pa = $this->getpraisenoread($id);
      $talk = $com + $pa;
	  $data = array(
		  'count' => $talk,
		  'com' => $com
	  );
	  $this->success($data);
   }

   //设置点赞消息为已读
   public function setpraiseread($id){
      $nums = $_POST['object_id'];		 
	  $num = json_decode($nums,true);	
	  M("praise")->where(['id'=>['in',$num]])->save(['readed'=>true]);
	  $com = $this->getcommentnoread($id);
      $pa = $this->getpraisenoread($id);
      $talk = $com + $pa;
	  $data = array(
		  'count' => $talk,
		  'pa' => $pa
	  );
	  $this->success($data);
   }

   //设置回答我的消息为已读
   public function setansmeread($id){
       $nums = $_POST['object_id'];		 
	  $num = json_decode($nums,true);	
	  M("answer")->where(['object_id'=>['in',$num]])->save(['readed'=>true]);
      $asme = $this->getansmenoread($id);
	   $meac = $this->getmeacceptnoread($id);
	   $be = $this->getbenefitnoread($id);
	   $quiz = $asme + $meac + $be;
	  $data = array(
		  'count' => $quiz,
		  'asme' => $asme
	  );
	  $this->success($data);
   }

   //设置我被选中消息为已读
   public function setsemeread($id){
	   $nums = $_POST['object_id'];		 
	  $num = json_decode($nums,true);	
	  M("answer")->where(['object_id'=>['in',$num]])->save(['readed'=>true]);
	  $asme = $this->getansmenoread($id);
	   $meac = $this->getmeacceptnoread($id);
	   $be = $this->getbenefitnoread($id);
	   $quiz = $asme + $meac + $be;
	  $data = array(
		  'count' => $quiz,
		  'meac' => $meac
	  );
	  $this->success($data);
   }

   //设置看我答案的消息为已读
   public function setberead($id){
	   $nums = $_POST['object_id'];		 
	  $num = json_decode($nums,true);	
	  M("benefit")->where(['id'=>['in',$num]])->save(['readed'=>true]);
	   $asme = $this->getansmenoread($id);
	   $meac = $this->getmeacceptnoread($id);
	   $be = $this->getbenefitnoread($id);
	   $quiz = $asme + $meac + $be;
	  $data = array(
		  'count' => $quiz,
		  'be' => $be
	  );

	  $this->success($data);
   
   }

}