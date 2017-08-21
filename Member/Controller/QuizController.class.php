<?php

namespace Member\Controller;

use Member\Common\MemberBaseController;

class QuizController extends MemberBaseController {
    public function index() {
		$this->quizmessage();
    	$this->display();
    }
	//我的回答
	public function ans(){
		$answer = M("answer")->table("answer answer,member_post member_post")->page($_GET['page'] ?: 1,10)->order('answer.id desc')->where(['answer.member_id'=>session('id'),'answer.object_id = member_post.id','member_post.status = 1'])->field("answer.id as aid,answer.content as acontent,answer.add_time as ans_time,member_post.*")->select();
		foreach($answer as $i =>$c){
			$ma = M("mb_member")->field("avatar")->find(session('id'));
			$answer[$i]['avatar'] = $ma['avatar'];
			$qm = M("mb_member")->field("avatar")->find($c['member_id']);
			$answer[$i]['qavatar'] = $qm['avatar'];
			$answer[$i]['acontent'] = html_entity_decode($c['acontent'], ENT_QUOTES, 'UTF-8');
		}
		$this->quizmessage();
		$this->ans = $answer;
		$this->display();
		//$this->showTrueJson($answer);
	}
	//回答我的
	public function ansm(){
        $answer = M("answer")->table("answer answer,member_post member_post")->page($_GET['page'] ?: 1,10)->order('answer.id desc')->where(['member_post.member_id'=>session('id'),'answer.object_id = member_post.id','member_post.status = 1'])->field("answer.id as aid,answer.content as acontent,answer.add_time as ans_time,answer.member_id as anm,member_post.*")->select();
		foreach($answer as $i =>$c){
			$ma = M("mb_member")->field("avatar,nickname")->find($c['anm']);
			$answer[$i]['anickname'] = $ma['nickname'];
			$answer[$i]['avatar'] = $ma['avatar'];
			$qm = M("mb_member")->field("avatar")->find(session('id'));
			$answer[$i]['qavatar'] = $qm['avatar'];		
			$answer[$i]['acontent'] = html_entity_decode($c['acontent'], ENT_QUOTES, 'UTF-8');
		}

		$mp = M("member_post")->where(['member_id'=>session('id'),'reward > 0','status = 1'])->field("id")->select();
	  foreach($mp as $i => $c){
	    $mpids[] = $c['id'];
	   }
	    if (count($mpids) > 0) {
              M("answer")->where(['object_id'=>['in',$mpids]])->save(['readed'=>true]);
			  $benefitcount =M("benefit")->where(['member_post_id' => ['in', $mpids],'readed = 0'])->count();
	          $this->assign("benefitcount",$benefitcount);
          }

		$this->ans = $answer;
		$this->display();
	}
	//我被选中
	public function accept(){
       $answer = M("answer")->table("answer answer,member_post member_post")->page($_GET['page'] ?: 1,10)->order('answer.id desc')->where(['answer.member_id'=>session('id'),'answer.accept = 1','answer.object_id = member_post.id','member_post.status = 1'])->field("answer.id as aid,answer.content as acontent,answer.add_time as ans_time,member_post.*")->select();
		foreach($answer as $i =>$c){
			$ma = M("mb_member")->field("avatar")->find(session('id'));
			$answer[$i]['avatar'] = $ma['avatar'];
			$qm = M("mb_member")->field("avatar")->find($c['member_id']);
			$answer[$i]['qavatar'] = $qm['avatar'];
			$answer[$i]['acontent'] = html_entity_decode($c['acontent'], ENT_QUOTES, 'UTF-8');
		}
		$mp = M("member_post")->where(['member_id'=>session('id'),'reward > 0','status = 1'])->field("id")->select();
	  foreach($mp as $i => $c){
	    $mpids[] = $c['id'];
	   }
	    if (count($mpids) > 0) {
              M("message")->where(['object_id'=>['in',$mpids],'type = 11'])->save(['readed'=>true]);
          }
		$this->quizmessage();
		$this->ans = $answer;
		$this->display();
	}

	//偷看答案的人
	public function benefit(){
      $bes = M("benefit")->table("benefit be,member_post mp")->where(['be.member_post_id = mp.id','mp.member_id'=>session('id'),'mp.status = 1','mp.reward > 0'])->field("mp.*,mp.id as mpid,mp.member_id as mp_member_id,be.*")->select();	
	  foreach($bes as $i => $c){
	    $bem = M("mb_member")->field('avatar,nickname')->find($c['member_id']);
		$bes[$i]['bavatar'] = $bem['avatar'];
		$bes[$i]['bnickname'] = $bem['nickname'];
        $mpm = M("mb_member")->field('avatar,nickname')->find($c['mp_member_id']);
		$bes[$i]['avatar'] = $mpm['avatar'];
		$bes[$i]['nickname'] = $mpm['nickname'];
	  }
	  	$mp = M("member_post")->where(['member_id'=>session('id'),'reward > 0','status = 1'])->field("id")->select();
	  foreach($mp as $i => $c){
	    $mpids[] = $c['id'];
	   }
	    if (count($mpids) > 0) {
              M("benefit")->where(['member_post_id'=>['in',$mpids]])->save(['readed'=>true]);
			 $answercount =M("answer")->where(['object_id' => ['in', $mpids],'readed = 0'])->count();
             $this->assign("answercount",$answercount);
          }
		$this->bes = $bes;
		$this->display();
	}

	//我看的答案
	public function benefitme(){
		$besm = M("benefit")->table('benefit be,member_post mp')->where(['be.member_post_id = mp.id','be.member_id'=>session('id'),'mp.status = 1','mp.reward > 0'])->field('mp.*,mp.member_id as mp_member_id,mp.id as mpid,be.*')->select();
      foreach($besm as $i => $c){
	    $bem = M("mb_member")->field('avatar,nickname')->find($c['member_id']);
		$besm[$i]['bavatar'] = $bem['avatar'];
		$besm[$i]['bnickname'] = $bem['nickname'];
        $mpm = M("mb_member")->field('avatar,nickname')->find($c['mp_member_id']);
		$besm[$i]['avatar'] = $mpm['avatar'];
		$besm[$i]['nickname'] = $mpm['nickname'];
	  }
	  $this->quizmessage();
      $this->besm = $besm;
	  $this->display();
	}

	//编辑
	public function answer($id){
		if(IS_POST){
		$ans = $_POST;
        M("answer")->where(['id'=>$id])->save($ans);	
		$this->showTrueJson();
		}
		$answer = M("answer")->find($id);
		$this->answer = $answer;
	   $this->display();
	}

	//回答我的、看我答案的消息
	public function quizmessage(){
	   $mps = M("member_post")->where(['member_id'=>session('id'),'reward > 0','status = 1'])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   if(count($mpids)){
	  $answercount =M("answer")->where(['object_id' => ['in', $mpids],'readed = 0'])->count();
       $this->assign("answercount",$answercount);

	    $benefitcount =M("benefit")->where(['member_post_id' => ['in', $mpids],'readed = 0'])->count();
	   $this->assign("benefitcount",$benefitcount);

	    $acceptcount =M("message")->where(['object_id' => ['in', $mpids],'readed = 0','type = 11'])->count();
	   $this->assign("acceptcount",$acceptcount);
	   }
	}

}