<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class MemberPostController extends MemberBaseController {
    public function index() {
	  $mps = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   if(count($mpids)){
	    $commentcount =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
       $this->assign("commentcount",$commentcount);

	   $praisecount =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $this->assign("praisecount",$praisecount);
	   }	 
    	$this->display();
    }

    //�ҵ�����
	public function comments(){	  
	  $mps = M("member_post")->table("comment comment,member_post member_post")->where(['comment.member_id'=>session('id'),'member_post.id = comment.object_id','comment_type = 3','reward = 0','member_post.status = 1'])->field("member_post.id as mpid,member_post.content as mpcontent,member_post.member_id as mpmember,comment.*")->order('comment.id desc')->select();
	foreach($mps as $i => $c){
	   $m = M("mb_member")->field("avatar")->find($c['member_id']);
       $mps[$i]['avatar'] = $m['avatar'];
	   $mpm = M("mb_member")->field("nickname")->find($c['mpmember']);
	   $mps[$i]['nickname'] = $mpm['nickname'];
	}
	 
	 $this->assign("comments",$mps);

	  $mps = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   if(count($mpids)){
	  $commentcount =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
       $this->assign("commentcount",$commentcount);

	    $praisecount =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $this->assign("praisecount",$praisecount);
	   }

    	$this->display();
	}

	//@�ҵ�
	public function clicked(){
		$_mps  = M("member_post")
	     ->join("LEFT JOIN mb_member ON mb_member.id=member_id")
	     ->join("LEFT JOIN circle ON circle_id=circle.id")
	     ->page($_GET['page'] ?: 1,10)
	     ->where(["member_id"=>$m['id'],'reward = 0','member_post.status = 1'])
	     ->order('id DESC')
	     ->field("member_post.*,mb_member.nickname,mb_member.avatar,circle.circle_name")
	     ->select();
        $this->assign("_mps",$_mps);

     $mps = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   if(count($mpids)){
	  $commentcount =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
       $this->assign("commentcount",$commentcount);

       $praisecount =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $this->assign("praisecount",$praisecount);
	   }

    	$this->display();
	}
	
	//�����ҵ�
	public function commentd(){
	  $mps = M("member_post")->table("comment comment,member_post member_post")->where(['member_post.member_id'=>session('id'),'member_post.id = comment.object_id','comment_type = 3','member_post.reward = 0','member_post.status = 1'])->field("member_post.id as mpid,member_post.content as mpcontent,comment.*")->order('comment.id desc')->select();
	foreach($mps as $i => $c){
	   $m = M("mb_member")->field("nickname,avatar")->find($c['member_id']);
	   $mps[$i]['nickname'] = $m['nickname'];
       $mps[$i]['avatar'] = $m['avatar'];
	}
	  $this->assign("comments",$mps);

	   $mps = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	    if (count($mpids) > 0) {
             M("comment")->where(['object_id'=>['in',$mpids]])->save(['readed'=>true]);
			$praisecount =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	        $this->assign("praisecount",$praisecount);
          }

		    
	 
	  $this->display();
	}

	//�����ҵ�
	public function praised(){
	   $praise = M("member_post")->table("member_post mp,praise pa")->where(['mp.member_id'=>$_SESSION[id],'mp.reward = 0','mp.status = 1','mp.id = pa.object_id','pa.type = 0'])->field("mp.content,pa.*")->order('pa.id desc')->select();
	  foreach($praise as $i => $c){
	   $m = M("mb_member")->field('avatar,nickname')->find($c['member_id']);
	   $praise[$i]['avatar'] = $m['avatar'];
	   $praise[$i]['nickname'] = $m['nickname'];
	  }
      $this->assign("praise",$praise);

	   $mp = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mp as $i => $c){
	    $mpids[] = $c['id'];
	   }

	    if (count($mpids) > 0) {
                M("praise")->where(['object_id'=>['in',$mpids]])->save(['readed'=>true]);
				$commentcount =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
               $this->assign("commentcount",$commentcount);
          }
	 
    	$this->display();

	}

	//ת���ҵ�
	public function relayed(){
	     $mps = M("member_post")->table("member_post a,member_post b")->where(['a.member_id'=>$_SESSION[id],'a.status = 1','a.reward =0','a.id=b.pid'])->field('a.content as fcontent,b.*')->order('b.id desc')->select();
	  foreach($mps as $i => $c){
	   $m = M("mb_member")->field("avatar")->find($c['member_id']);
	   $mps[$i]['avatar'] = $m['avatar'];
	  }
	  $this->assign("relays",$mps);

	   $mps = M("member_post")->where(['member_id'=>session('id'),'reward = 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   if(count($mpids)){
	  $commentcount =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
       $this->assign("commentcount",$commentcount);

	    $praisecount =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $this->assign("praisecount",$praisecount);
	   }
    	$this->display();
	}

	//ɾ��˵˵��ͬʱɾ������
	public function deletemp($id){
		M("member_post")->delete($id);
		M("comment")->where(["object_id"=>$id])->delete();		
		$this->showTrueJson();
	}

	//ɾ������
	public function deletecomment($id){
		$c = M("comment")->field("object_id")->find($id);
		M("member_post")->where(["id"=> $c['object_id']])->setDec("comments_num");
		M("comment")->where(["pid"=>$id])->delete();
		M("comment")->delete($id);
		$this->showTrueJson();
	}
	public function sendSign(){
		$data['sign']=$_REQUEST['sign'];
		$re=M('mb_member')->where("id=".session('id'))->save($data);
		if($re){
			$this->showTrueJson();
		}
	}
}