<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class CommentController extends HomeBaseController{
	public function add(){
    if(!session('id')){
			$url = U('/home/member/login');
			redirect($url."?url=".$_SERVER['HTTP_REFERER']);
    }
		$c = $_POST;
		$c["member_id"] = session("id");
		if ($c['pid']) {
			$p = M("comment")->find($c['pid']);
			$c["comment_type"]  =$p["comment_type"];
			$c["object_id"] = $p["object_id"];
		}
		M()->startTrans();
		$comment_id = M("comment")->add($c);	
		
		if ($c['comment_type'] == 3) {
			M("member_post")->where(["id"=> $c['object_id']])->setInc("comments_num");
		}

		switch ($c['comment_type']) {
			case 0:
				$member = M("mb_member")->where("id = (select member_id from open_class where open_class_id= {$c['object_id']})")->find();
				break;
			case 1:
				$member = M("mb_member")->where("id = (select member_id from service where service_id= {$c['object_id']})")->find();
				break;
			case 2:
				$member = M("mb_member")->where("id = (select member_id from demand where demand_id= {$c['object_id']})")->find();
				break;
			case 3:
				$member = M("mb_member")->where("id = (select member_id from member_post where id= {$c['object_id']})")->find();
				break;
			case 4:
				$member = M("mb_member")->where("id = (select member_id from information where id= {$c['object_id']})")->find();
				
				break;
			case 5:
				$member = M("mb_member")->where("id = (select member_id from share where id= {$c['object_id']})")->find();
	
				break;
			case 6:
				$member = M("mb_member")->where("id = (select member_id from activity where id= {$c['object_id']})")->find();
				break;
			default:
				break;
		}
		$m = $this->m;
		//先给自己发
		//$c1 = "你已对".$member['nickname']."进行了评价";
		//M("message")->add(['type'=>4,'to_member_id'=>session("id"),'object_id'=>$comment_id,'content'=>$c1]);
		//给别人发
		//$c2 = $m['nickname']."已对你进行了评价";
		//M("message")->add(['type'=>4,'to_member_id'=>$member['id'],'object_id'=>$comment_id,'content'=>$c2]);
		M()->commit();
		if (IS_AJAX) {
			if ($c['pid']) {
				//SELECT comment.*,mb_member.nickname,mb_member.avatar,(SELECT nickname FROM mb_member WHERE id=_mid) as pnickname FROM `comment` LEFT JOIN mb_member ON member_id=mb_member.id  WHERE  comment.pid = _pid;
				$this->sc = M("comment")
				->join("left join comment as pc on pc.id=comment.pid")
				->join("left join mb_member on mb_member.id=comment.member_id")
				->join("left join mb_member as pm on pm.id=pc.member_id")
				->where(['comment.id'=>$comment_id])
				->field('comment.*,mb_member.nickname,mb_member.avatar,pm.nickname as pnickname')
				->find();
				$this->display("Common/_sub_comment");
			}else{				
				$this->comments = M("comment")
				->join("LEFT JOIN mb_member ON member_id=mb_member.id")
				->where(['comment.id'=>$comment_id])
				->field("comment.*,mb_member.nickname,mb_member.avatar")
				->select();				
				$this->display("Common/_comments");			
			}
			
		}else{
			redirect($_SERVER["HTTP_REFERER"]);
		}
	}

	public function delete(){
		$id = I("request.id");
		$c = M("comment")->field("object_id")->find($id);
		M("member_post")->where(["id"=> $c['object_id']])->setDec("comments_num");
		M("comment")->where(["pid"=>$id])->delete();
		M("comment")->delete($id);
		$this->showTrueJson();
	}
	public function zan(){
		$id = I("request.id");
		M("comment")->where(["id"=>$id])->setInc("praise_num");
		$this->showTrueJson();
	}
	public function unzan(){
		$id = I("request.id");
		M("comment")->where(["id"=>$id])->setDec("praise_num");
		$this->showTrueJson();
	}

	public function come(){
	  $com = M("comment")->select();
	  $this->showTrueJson($com);
	} 
}
