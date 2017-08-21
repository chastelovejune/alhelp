<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
require '../lib/curd.trail.php';
class MemberPostController extends HomeBaseController {
	use \curd;
	public function index(){
		$mps = D("MemberPost")->mps();
		{
			$_c = clone $mps;
			$this->count = $_c->count();
		}

		if(I("request.stype") == "id"){
		$keywords  =I("request.keywords");
	     	if ($keywords) {
	     		$where['_string'] = "(member_post.member_id = '{$keywords}')";
	     	}
		}else{
		    $keywords  =I("request.keywords");
	     	if ($keywords) {
	     		$where['_string'] = "(member_nickname LIKE '%{$keywords}%')";
	     	}
		}

		if(session('id')== 0){
		  $where['member_post.view_permission'] = 0;
		}

		$where['member_post.status'] = 1;
		$mps = $mps->page($_GET['page'] ?: 1,20);
		$mps = $mps->where($where);

		$school_id = I("request.school_id");
		if($school_id > 0){
		  $f_s = M("school")->find($school_id);
		  if($f_s['type'] == 3){
			  $zys = M("zysc_view")->where(['yuan_id'=>$school_id])->field('school_id')->select();
			  foreach($zys as $k => $v){
	           $sids[] = $v['school_id'];
	          }
			  $c = M("circle")->where(['object_id'=>['in',$sids]])->field("id")->select(); 
			  foreach($c as $i => $s){
	           $cids[] = $s['id'];
	          }
			  if(count($cids)){
			  $mps = $mps->where(['circle_id'=>['in',$cids]])->order('id desc')->select();
			  }
		  }else if($f_s['type'] == 2){
			  $c = M("circle")->where(['object_id'=>$school_id])->field("id")->find(); 
			  $mps = $mps->where(['circle_id'=>$c['id']])->order('id desc')->select();
		  }else{
              $zys = M("zysc_view")->where(['area_id'=>$school_id])->field('school_id')->select();
                foreach($zys as $k => $v){
	           $sids[] = $v['school_id'];
	          }
			  $c = M("circle")->where(['object_id'=>['in',$sids]])->field("id")->select(); 
			  foreach($c as $i => $s){
	           $cids[] = $s['id'];
	          }
			  if(count($cids)){
			  $mps = $mps->where(['circle_id'=>['in',$cids]])->order('id desc')->select();
			  }
		  }
		   
		}else{
		   $mps = $mps->select();
		}
		foreach($mps as $i => $c){
			$ans = M("answer")->where(['object_id'=>$c['id']])->find();
			$mps[$i]['answer'] = $ans;
		$an = M("answer")->where(['object_id'=>$c['id'],'member_id'=>session("id")])->find();
		if($an){
		    $mps[$i]['ansed'] = '1';
		}else{
			$mps[$i]['ansed'] = '0';
		}
		if($c['member_id'] == session('id')){
		   $mps[$i]['isme'] = '1';
		}else{
		   $mps[$i]['isme'] = '0';
		}
		}

		$this->assign("mps",$mps);
	   $this->display();
	}

	
	public function add(){
		if (!session("id")) {
			$this->showFalseJson("必须登录");
		}
		M()->startTrans();		
		$member = M("mb_member")->find(session("id"));
		$mp = $_POST;
		$mp["member_id"]=$member["id"];
		$mp["member_nickname"] = $member["nickname"];
		$mp['add_time'] = date('Y-m-d H:i:s');
		$id = M("member_post")->add($mp);

		$images = I("request.image");
		foreach ($images as  $path) {
			$attachment = ["path"=>$path,"member_id"=>$member['id'],"object_id"=>$id];
			M("attachment")->add($attachment);
		}

		if(I("request.status")==1){
			if(!M("member_follow_circle")->where(['member_id'=>session('id'),'circle_id'=>I("request.circle_id")])->find()){
		  $cm['member_id'] = session('id');
		  $cm['circle_id'] = I("request.circle_id");
		  M("member_follow_circle")->add($cm);
		  //加入校友圈群聊
		  $m = M("mb_member")->find(session('id'));
		  $c = M("circle")->where(['id'=>I("request.circle_id")])->find();
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

		M()->commit();
		$this->showTrueJson($id);				
	}

	//获取圈子信息
	public function circle($id){
	   $circle = M("circle")->where(['object_id'=>$id])->field("id,circle_name")->find();
	   $this->showTrueJson($circle);
	}

	public function relay(){
		 $this->mustLogin();
		if (!session("id")) {
			$this->showFalseJson("必须登录");
		}
		M()->startTrans();
		$member = M("mb_member")->find(session("id"));
		$mp = $_POST;
		$mp["member_id"]=$member["id"];
		$mp["member_nickname"] = $member["nickname"];
		$fmp = M("member_post")->where(['id' => I("request.fid")])->field('pid')->find();
		if($fmp['pid'] == 0){
		  $mp['pid'] = I("request.fid");
		  M("member_post")->where(['id' => I("request.fid")])->setInc('f_number');
		}else{
		  $mp['pid'] = $fmp['pid'];
		  M("member_post")->where(['id' => I("request.fid")])->setInc('f_number');
		  M("member_post")->where(['id' => $fmp['pid']])->setInc('f_number');
		}
		$id = M("member_post")->add($mp);
		M()->commit();
		//$this->showTrueJson($id);	
		redirect(U('/home/memberPost'));
	}

	public function pay($id){	
		if(IS_POST){
           $reward = M("member_post")->field("reward")->find(I("request.id"));
			$total = $reward['reward'];
			if (I("request.pay_type")==2) {
			//余额支付
			$m = M("mb_member")->find(session('id'));			
			if ($m["balance"] < $total) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$total);
				//更新余额(扣钱)

				//生成流水
				$record = ['type'=>7,'income_type'=>0,'object_id'=>I("request.id"),'from_member_id'=>session('id'),'to_member_id'=>0,'balance'=>$total];
				M("payment_record")->add($record);//更新到record表

				//修改说说状态
				$mp = M("member_post")->where(['id'=>I("request.id")])->find();
				$mp['status'] = '1';
				M("member_post")->where(['id'=>I("request.id")])->save($mp);
				if(!M("member_follow_circle")->where(['member_id'=>session('id'),'circle_id'=>$mp['circle_id']])->find()){
				 $cm['member_id'] = session('id');
		         $cm['circle_id'] = $mp['circle_id'];
		         M("member_follow_circle")->add($cm);
				}
				
				M()->commit();
				$this->showTrueJson();
			}
		}else if (I("request.pay_type") == 1) {
			//支付宝	
			$total = $total * 100;
			$out_trade_no = pay_id(I("request.id"));
			$ds = M("member_post")->where(['id'=>I("request.id")])->find();
			$ds['content'] = "有偿问答".I("request.id");
			$html = go_alipay(U('/home/MemberPost/alipay'),$out_trade_no,$ds['content'],$total);
			echo $html;
			die;
			$this->show($html);			
		}else{
			//微信
			$total = $total * 100;
			$out_trade_no = time()."_quiz_".I("request.id");
    		$code = go_wxPay($out_trade_no,$total,I("request.id"));
            $this->showTrueJson(['code'=>$code,'id'=>I("request.id"),'type'=>'quiz']);
		}
		}else{
		  $reward = M("member_post")->where(['id'=>$id])->field('reward')->find();
		  $this->reward = $reward;
		  $this->display();
		}
		
	}

	function alipay($out_trade_no){
		$mpid = get_pay_id($out_trade_no);
		$mps = M("member_post")->find($mpid);
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
			//生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$mps['reward'],'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>7,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $mps['reward'],'object_id'=>$mpid,
	                );
	           $record_id = M("payment_record")->add($record);//first

				$cash['payment_record_id'] = $record_id;
		        M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second

				$mps['status'] = '1';
				M("member_post")->where(['id'=>$mpid])->save($mps);	
				if(!M("member_follow_circle")->where(['member_id'=>session('id'),'circle_id'=>$mp['circle_id']])->find()){
				 $cm['member_id'] = session('id');
		         $cm['circle_id'] = $mp['circle_id'];
		         M("member_follow_circle")->add($cm);
				}			

            M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/home/memberPost'));
	}

	//有偿提问-指定回答人
	public function quiz(){	
		$this->mustLogin();
		$invite = I('id');
		$m['id'] = $invite;
		$this->invite = $m;
		$this->display();
	}

	public function quizconfirm($id){
	  $mps = M("member_post")->where(['id'=>$id])->find();
	  $this->showTrueJson($mps);
	}

	public function ok($id){
		$this->mustLogin();
		$mps = M("member_post")->where(['id'=>$id])->find();
		$this->o = $mps;
		$this->display();
	}

	//抢答
	public function answer($id){
		$this->mustLogin();
		if(IS_POST){ 
		 if (!session("id")) {
			$this->showFalseJson("必须登录");
		}
		M()->startTrans();
		$ans = $_POST;		
		$ans['member_id'] = session('id');
		$ans['status'] = 1;
		$ans['object_id']=$id;
		$ans['content'] = addslashes(I("request.content"));
		$object_id = M("answer")->add($ans);	
		M()->commit();
				
		  $this->showTrueJson($object_id);
		}
		$this->display();
	}

	public function ansselect(){
		//修改状态
		$ans['accept'] = '1';
		M("answer")->where(['id'=>I("request.id")])->save($ans);
		$ans = M("answer")->where(['id'=>I("request.id")])->find();
		$mp['accept'] = I("request.id");
		M("member_post")->where(['id'=>$ans['object_id']])->save($mp);
		$mp=M("member_post")->where(['id'=>$ans['object_id']])->find();
		//转账
		M("mb_member")->where(["id"=>$ans['member_id']])->setInc("balance",$mp['reward']);
		//生成流水 新助邦->答案提供者
        $record = array('type'=>7,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$ans['member_id'],
	                'balance' => $mp['reward'],'object_id'=>$mp['id'],
	                );
         M("payment_record")->add($record);//second
         

		 //生成系统消息
		 $mes['type'] = 11;
		 $mes['to_member_id'] = $ans['member_id'];
		 $mes['object_id'] = $mp['id'];
		 $mes['role'] = 1;
		 M("message")->add($mes);
         M()->commit();

		$this->showTrueJson();
	}

	public function answerdetail($id){
	  $answer = M("answer")->find($id);
	  $answer['content'] = html_entity_decode($answer['content'], ENT_QUOTES, 'UTF-8');
	  $this->answer = $answer;
	  $this->display();
	}

	public function benefit($id){
		$this->mustLogin();
	    if(IS_POST){			 
				$mps = M("member_post")->find($id);
				$ans = M("answer")->where(['id'=>$mps['accept']])->find();
				$total = $mps['reward'] * 0.1;
			if (I("request.pay_type")==2) {
			//余额支付
			$m = M("mb_member")->find(session('id'));			
			if ($m["balance"] < $total) {//余额不足
				$this->showFalseJson("余额不足");
			}else{
				M()->startTrans();
				//少钱
				M("mb_member")->where(["id"=>$m['id']])->setDec("balance",$total);
				//更新余额(扣钱)

				//添加查看答案的人
				$bem['member_id'] = session('id');
				$bem['member_post_id'] = $id;
				$bem['add_time'] = date('Y-m-d H:i:s');
				M("benefit")->add($bem);
              
				//转账
				$mq = $total * 0.2;
		        $mb = $total * 0.8;
		        M("mb_member")->where(['id'=>$mps['member_id']])->setInc("balance",$mq);
                M("mb_member")->where(['id'=>$ans['member_id']])->setInc("balance",$mb);

				//生成流水 偷看人微银->新助邦 新助邦->提问人 新助邦->答案人
				 $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>session("id"),'to_member_id'=>0,
	                'balance' => $total,'object_id'=>$id,
	                );
		          M("payment_record")->add($record);

		         $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$mps['member_id'],
	                'balance' => $mq,'object_id'=>$id,
	                );
		         M("payment_record")->add($record);

		         $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$ans['member_id'],
	                'balance' => $mb,'object_id'=>$id,
	                );
		         M("payment_record")->add($record);
						
				M()->commit();
				$this->showTrueJson();
			}
		}else if (I("request.pay_type") == 1) {
			//支付宝		
			$out_trade_no = pay_id($id);
			$ds = M("member_post")->where(['id'=>$id])->find();
			$ds['content'] = "查看答案有偿问答";
			$total = $total * 100;
			$html = go_alipay(U('/home/MemberPost/alipayb'),$out_trade_no,$ds['content'],$total);
			echo $html;
			die;
			$this->show($html);
		
		}else{
			//微信
			$ids = array($id,session('id'));
			$total = $total * 100;
			$out_trade_no = time()."_benefit_".$id;
		    $code = go_wxPay($out_trade_no,$total,json_encode($ids));
            $this->showTrueJson(['code'=>$code,'id'=>$id,'type'=>'benefit']);		
		}
		}		 
		$reward = M("member_post")->where(['id'=>$id])->field('reward')->find();
		$reward['reward'] = $reward['reward'] * 0.1;
		$this->reward = $reward;
		$this->display();	
	}


	function alipayb($out_trade_no){
		$mpid = get_pay_id($out_trade_no);
		$mps = M("member_post")->find($mpid);
		$fee = $mps['reward'] * 0.1;
		$verify_result = validate_alipay();
		if($verify_result) {//验证成功
            M()->startTrans();
            $member = M('mb_member')->find(session("id"));
			//添加查看答案的人
				$bem['member_id'] = session('id');
				$bem['member_post_id'] = $mpid;
				$bem['add_time'] = date('Y-m-d H:i:s');
				M("benefit")->add($bem);
              
			 $ans = M("answer")->where(['id'=>$mps['accept']])->find();
			 //放款
			 $tom1 = M("mb_member")->where(['id'=>$ans['member_id']])->find(); //被悬赏者
			 $tom2 = M("mb_member")->where(['id'=>$mps['member_id']])->find(); //提问者

			 $balance = $fee * 0.8;;				
			 $tom1['balance'] = $tom1['balance'] + $balance;          
			 M("mb_member")->where(['id'=>$ans['member_id']])->save($tom1);

			 $balance = $fee * 0.2;
			 $tom2['balance'] = $tom2['balance'] + $balance;
			 M("mb_member")->where(['id'=>$mps['member_id']])->save($tom2);

			//生成cash
	            $cash_id=M("cash")->add(['member_id'=>session("id"),'balance'=>$fee,'balance_now'=>$member['balance'],'balance_frozen'=>$member['balance_frozen'],'pay_id'=>$_GET['trade_no'],'is_alipay'=>1]);
	            //生成record,2条（分别是从自己的实际账户到自己微银，从自己的微银到新助邦微银)
	            $record = array('type'=>8,'income_type'=>1,
	                'from_member_id'=>session("id"),'to_member_id'=>session("id"),
	                'balance' => $fee,'object_id'=>$mpid,
	                );
	            $record_id = M("payment_record")->add($record);//first

				$cash['payment_record_id'] = $record_id;
		        M("cash")->where(['id'=>$cash_id])->save($cash);

				$record["to_member_id"] = 0;//去新助邦微银
				$record["income_type"] = 0;//站内
				M("payment_record")->add($record);//second

				 //新助邦微银->被悬赏者微银
			   $balance = $fee * 0.8;
			   $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$tom1['id'],
	                'balance' => $balance,'object_id'=>$mpid);
				 M("payment_record")->add($record);
			
				//新助邦微银->提问者微银
				$balance = $fee * 0.2;
			   $record = array('type'=>8,'income_type'=>0,
	                'from_member_id'=>0,'to_member_id'=>$tom2['id'],
	                'balance' => $balance,'object_id'=>$mpid);
				 M("payment_record")->add($record);

            M()->commit();
		}else{
			E('还没考虑哪种情况会验证失败/(ㄒoㄒ)/~~');
		}
		redirect(U('/home/memberPost/anscontent',['id'=>$mpid]));
	}

	//是否偷听过
	public function isbenefit(){
		if(M("benefit")->where(['object_id'=>I("request.id"),'member_id'=>session('id')])->find()){
		      $be['isbe'] = '1';
			  $this->showTrueJson();
		} else{
		  $be['isbe'] = '0';
		   $this->showTrueJson();
		}  
	}

	public function anscontent(){
	  $mp = M("member_post")->where(['id'=>I("request.id")])->find();
	  $ans = M("answer")->where(['id'=>$mp['accept']])->find();
	   $ans['content'] = html_entity_decode($ans['content'], ENT_QUOTES, 'UTF-8');
	  $this -> ans = $ans;
	  $this->display();	  
	}

    public function praise(){
	 //  $this->mustLogin();
	 if (!session("id")) {
			$this->showFalseJson("必须登录");
		}else{
		  if(M("praise")->where(['type = 0','member_id'=>session('id'),'object_id'=>I("request.id")])->find()){
		   M("praise")->where(['type = 0','member_id'=>session('id'),'object_id'=>I("request.id")])->delete();
		   M("member_post")->where(['id'=>I("request.id")])->setDec('praise_num');
		  $this->showTrueJson();
	     }else{
		   $praise['member_id']=session('id');
		   $praise['object_id']=I("request.id");
		   $praise['type']=0;
		   $praise['add_time'] = date("Y-m-d H:i:s");
		   M("praise")->add($praise);
		 M("member_post")->where(['id'=>I("request.id")])->setInc('praise_num');
		  $this->showTrueJson();
	   }
		}
	          
	}

	public function announcement(){
		M("member_post")->where(["id"=>I("request.id")])->save(['circle_announcement'=>1]);
		$this->showTrueJson();
	}
	public function unannouncement(){
		M("member_post")->where(["id"=>I("request.id")])->save(['circle_announcement'=>0]);
		$this->showTrueJson();
	}
	public function update($id){
		M("member_post")->where(['id'=>$id])->save($_POST);
	}
	public function show(){
		$this->display();
	}

	public function mpcount(){
		$mps = M("member_post")->where(['member_id'=>session('id')])->field("id")->select();
	   foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	   $count0 =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $count1 =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	   $count = $count0 + $count1;
	   $this->assign("mps",$mps);
	   $this->showTrueJson($count);	
	}

	public function commentcount(){
	  $mps = M("member_post")->where(['member_id'=>session('id')])->field("id")->select();
	  foreach($mps as $i => $c){
	    $mpids[] = $c['id'];
	   }
	  $count =M("comment")->where(['comment_type = 3','object_id' => ['in', $mpids],'readed = 0'])->count();
	  $this->showTrueJson($count);
	}

	public function praisecount(){
	  $mps = M("member_post")->where(['member_id'=>session('id')])->field("id")->select();
	  foreach($mps as $i => $c){
	  $mpids[] = $c['id'];
	 }
	 $count =M("praise")->where(['type = 0','object_id' => ['in', $mpids],'readed = 0'])->count();
	 $this->showTrueJson($count);
	}

	/*public function resetcircle($page){
	   $mps = M("member_post")->page($page,500)->field("id,circle_id")->select();
	   foreach($mps as $i => $c){
	      if($c['circle_id']>0){
			  if(M('circle')->where(['object_id'=>$c['circle_id']])->find()){
		         $cid = M('circle')->where(['object_id'=>$c['circle_id']])->field('id')->find();
			     $mps[$i]['circle_id'] = $cid['circle_id'];
			     M("member_post")->where(['id'=>$c['id']])->save($mps[$i]);
			  }else if(M('zysc_view')->where(['yuan_id'=>$c['circle_id']])->find()){
			      $cid = M('zysc_view')->where(['yuan_id'=>$c['circle_id']])->field("circle_id")->find();
                  $mps[$i]['circle_id'] = $cid['circle_id'];
			      M("member_post")->where(['id'=>$c['id']])->save($mps[$i]);
			  }else if(M('zysc_view')->where(['zhuanye_id'=>$c['circle_id']])->find()){
			      $cid = M('zysc_view')->where(['zhuanye_id'=>$c['circle_id']])->field("circle_id")->find();
                  $mps[$i]['circle_id'] = $cid['circle_id'];
			      M("member_post")->where(['id'=>$c['id']])->save($mps[$i]);
			  }
		  }
	   }
	   $this->showTrueJson();
	}*/
}