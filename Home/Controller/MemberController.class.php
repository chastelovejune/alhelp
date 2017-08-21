<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;


/*
 * 登录页控制器
 * coder : leebin
 * createTime:2016-05-13
 */

class MemberController extends HomeBaseController {
	
	public function validate(){
        $c = M("mb_member")->where($_GET)->count();
        $this->ajaxReturn($c == 0);
    }

    public function isNo(){
        $c = M("mb_member")->where($_GET)->count();
        $this->ajaxReturn($c == 0);
    }    

    public function isYou(){
        $c = M("mb_member")->where($_GET)->count();
        $this->ajaxReturn($c != 0);
    }    

     public function login(){
        if (IS_POST) {
            //go_log($_POST);
			$code = I('code');
			//先检查验证码
			$verify = new \Think\Verify();
			if (!$verify->check($code)){
				$this->showFalseJson('验证码错误');
			}
            $member = M("mb_member")->where("email = '{$_POST['name']}' OR phone = '{$_POST["name"]}' OR nickname='{$_POST['name']}' ")->find();
            if (!$member) {
                $this->showFalseJson("没有这个用户");
            }else if($member["password"] != $this->hashPwd(I("post.password"))){
                $this->showFalseJson("密码错误");
            }elseif($member['status']==2){
                $this->showFalseJson("该用户已经被管理员禁用！");
            }else{
                session("id",$member["id"]);
                $this->showTrueJson("登录成功");
            }
        }else{
            $this->display();
        }
    }

    public function avatar(){
        $avatar = upload("avatar");
        M("mb_member")->where(['member_id'=>session("id")])->save(['avatar'=>$avatar]);
        $this->showTrueJson($avatar);
    }

    public function isLogined(){
        if (session("id")) {
            $this->showTrueJson(session("id"));
        }else{
            $this->showFalseJson();
        }
    }

    public function logout(){
        session("id",null);
        $this->showTrueJson();
    }

    public function follow($member_id){
        $f = array("follower"=>session("id"),"befollowed"=>$member_id);
        M("follow")->add($f);
        $this->showTrueJson();
    }

    public function unfollow($member_id){
        M("follow")->where(array("follower"=>session("id"),"befollowed"=>$member_id))->delete();
        $this->showTrueJson();
    }
    public function register(){
        //如果通过邮箱注册
        if(IS_POST){
            if($_REQUEST['by']=="email"){//Email
                $code =  new \Think\Verify();
                if(!$code->check(I("request.code"))){
                    $this->showFalseJson("验证码错误");
                }
                $result = D("MbMember")->validate($_POST);

                if ($result === true) {
                    $m = $_POST;
                    $flag = $this->sendEmailCode($m['email']);//如果提交成功发送邮箱进行验证
                    if($flag['suc']){
	                    $m['avatar'] = "/Ucenter/images/noavatar_big.gif";
	                    $m['password'] = $this->hashPwd($m['password']);
	                    $id = M("mb_member")->add($m);
	                    session("id",$id);
						$m['chat_id'] = 20000 + $id;
                        M("mb_member")->where(['id'=>$id])->save($m);
	                    $im = new \Org\Util\Im();
	                    $im->member_create($m['chat_id'],I("request.nickname"));
                    }
                    
                    $this->ajaxReturn($flag);
                }else{
                    $this->showFalseJson("非法提交");
                }
            }else{//手机
                if(I("request.phone_verified"))
                {
                    $code = cookie("phoneCode");
                    if( $code != I("request.phone_verified"))
                    {
                        $this->showFalseJson("短信验证码错误");
                    }
                }else{              
                    $this->showFalseJson("没有获取到手机验证码");
                }
                $result = D("MbMember")->validate($_POST);
                if ($result === true) {
                    $m = $_POST;
                    $m['phone_verified']=1;
                    $m['avatar'] = "/Ucenter/images/noavatar_big.gif";
                    $m['password'] = $this->hashPwd($m['password']);
                    $id = M("mb_member")->add($m);
                    session("id",$id);
                    $m['chat_id'] = 20000 + $id;
                    M("mb_member")->where(['id'=>$id])->save($m);
	                $im = new \Org\Util\Im();
	                 $im->member_create($m['chat_id'],I("request.nickname"));
                    $this->showTrueJson("注册成功");
                }else{
                    $this->showFalseJson();
                }
            }
        }else{
            $this->display();
        }
    }

    public function forget1(){
        if (IS_POST) {
            $m = M("mb_member")->where(["phone"=>I("request.phone")])->find();
            if ($m) {
                if (cookie("phoneCode") != I("request.phone_verified")) {
                    $this->showFalseJson("验证码错误");
                }
                $data = array(
                	'phone' => base64_encode(I("request.phone")),
                );
                cookie("phoneCode", null);
                $this->showTrueJson($data); 
            }else{
                $this->showFalseJson("用户不存在");
            }
        }
        $this->display();
    }

    public function bindPhone(){
        if (IS_POST) {
            if (cookie("phoneCode") != I("request.phone_verified")) {
                $this->showFalseJson("验证码错误");
            }
            M("mb_member")->where(['id'=>session("id")])->save(["phone"=>I("request.phone")]);
            $this->showTrueJson(); 
        }
        $this->display();
    }

    public function bindEmail(){
        if (IS_POST) {
            if (cookie("emailCode") != I("request.email_verified")) {
                $this->showFalseJson("验证码错误");
            }
            if(($result = D("MbMember")->save(session("id"),["email"=>I("request.email")])) === true){
                $this->showTrueJson(); 
            }else{
                $this->showFalseJson($result);
            }
        }
        $this->display();
    }

    public function forget2(){
        if (IS_POST) {
            $m = M("mb_member")->where(["email"=>I("request.email")])->find();
            if ($m) {
                if (cookie("emailCode") != I("request.email_verified")) {
                    $this->showFalseJson("验证码错误");
                }
                $this->showTrueJson(); 
            }else{
                $this->showFalseJson("用户不存在");
            }
        }
        $this->display();
    }

    public function flow(){
        if (IS_POST) {
            M()->startTrans();
            $m = $_POST;
			go_log($_POST);
            M("mb_member")->where(['id'=>session("id")])->save($m);
            if($_POST['follow']){
                foreach ($_POST['follow'] as $f) {
                    M("follow")->add(['follower'=>session("id"),'befollowed'=>$f]);
                }
            }
            M()->commit();
            $this->showTrueJson();
        }
		go_log(session("id"));
        $step = $_GET['step'] ?: 0;
        $this->assign("step",$step);
        $this->display();
    }

	public function eduadd(){

	   $info = $_POST;
		$info['member_id'] = session("id");
		
		if ($info['id'] > 0) {
			$id = M("mb_education_info")->save($info);
		}else{
			$id = M("mb_education_info")->add($info);
			$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'], 'school_id');
			D("CircleMember")->addBySchoolId($school_id);
			$m = M("mb_member")->find(session('id'));
			$c = M("circle")->where(['object_id'=>$school_id])->find();
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
		 $infos = M("zysc_view")->join("LEFT JOIN mb_education_info ON mb_education_info.school_id=zysc_view.zhuanye_id")->where(['member_id'=>session('id'),'mb_education_info.id'=>$id])->field("zysc_view.*,mb_education_info.id as education_id,year,type")->find();
		$this->showTrueJson($infos);
	}

	

    public function realname(){
        $profile = $_POST;
		if($profile['realname']){
        $profile['member_id'] = session("id");
        M("mb_profile")->add($profile);
		}
        $this->showTrueJson();
    }

    public function identify(){
        // 身份认证
		
        $profile = $_POST;
		if($profile['description']){
        $profile["member_id"] = session("id");
		$profile['profile_type'] = 1;	
        M("mb_profile")->add($profile);
		}
        $this->showTrueJson();
    }

    public function findPwd(){
        if (IS_POST) { 
            $where = I("request.phone") ?  ["phone"=>base64_decode(I("request.phone"))] : ["email"=>base64_decode(I("request.email"))];
            $m = M('mb_member')->where($where)->find();
            if (!$m) { 
                $this->showFalseJson("用户不存在！");
            }else{
                M("mb_member")->where($where)->save(["password"=>$this->hashPwd(I("request.password"))]);
                $this->showTrueJson();
            } 
        }         
        $this->display();
    }
    public function changePwd(){
        if (IS_POST) {
            $m = M("mb_member")->find(session("id"));
            if ($m["password"] != $this->hashPwd(I("request.old"))) {
                $this->showFalseJson("老密码错误");
            }
            M("mb_member")->where(["id" => session("id")])->save(["password" => $this->hashPwd(I("request.new1"))]);
            $this->showTrueJson("修改成功");
        }
        $this->display();
    }
    public function changePayPwd(){
        if (IS_POST) {
            $m = M("mb_member")->find(session("id"));
            if ($m["paypassword"] != I("request.old")) {
                $this->showFalseJson("老密码错误");
            }
            M("mb_member")->where(["id" => session("id")])->save(["paypassword" => I("request.new1")]);
            $this->showTrueJson("修改成功");
        }
        $this->display();
    }

    public function _empty(){
		unset($re);
        $re = M("mb_member")->find(I("request.id"));
        if (!$re) {
            redirect("/");
        }
        if (ACTION_NAME == "profile") {
            redirect(U("/home/member_memberPost",['id'=>I("request.id")]));
        }
	//ar_dump($re);
        $this->assign("re",$re);
        $this->display("profile");
    }


	
    public function praise(){
        M("mb_member")->where(["id"=>I("request.id")])->setInc("praise_num");
        $this->showTrueJson();
    }
    public function setting(){
		
		$m = M("mb_member")->find(session("id"));
        $this->mustLogin();
		$this->assign("m",$m);
        $this->display();
    }
    public function weiboLogin(){
        require_once("../lib/weibo/saetv2.ex.class.php");
        $weibo = new \SaeTOAuthV2(C("WB_AKEY") , C("WB_SKEY"));
        $weiboUrl = $weibo->getAuthorizeURL(C("WB_CALLBACK_URL"));
        redirect($weiboUrl);
    }
    public function weiboCallback(){
        require_once("../lib/weibo/saetv2.ex.class.php");
        if (I('request.error_code')) {
            $this->redirect('/home/member/login');
        }
        $weibo = new \SaeTOAuthV2(C("WB_AKEY") , C("WB_SKEY"));
        $keys = array('code' => $_REQUEST['code'],"redirect_uri"=> C("WB_CALLBACK_URL"));
        $token = $weibo->getAccessToken( 'code',  $keys) ;
        $c = new \SaeTClientV2( C("WB_AKEY") ,  C("WB_SKEY"), $token['access_token'] );        
        $user = $c->show_user_by_id($token['uid']);
        $weibo = ['weibo_uid'=>$token['uid'],'weibo_nickname'=>$user['name'],'weibo_access_token'=>$token['access_token']];
        //Array ( [access_token] => 2.00gETmVCgOVQpDf1f7bbbc8domcyYE [remind_in] => 2632175 [expires_in] => 2632175 [uid] => 2301883590 )
        if (session("id")) {//已经登录, 绑定用户信息
            M("mb_member")->where(['id'=>session("id")])->save($weibo);
            redirect(U('/home/member/setting',['type'=>4]));
        }else{//没登陆设置一个新用户
            $m = M("mb_member")->where(['weibo_uid'=>$weibo['weibo_uid']])->find();
            if ($m) {
               session("id",$m['id']); 
            }else{
                $m = array_merge($weibo,["nickname"=>"xzb".time()]);
                $passwd=rand(100000, 999999);
                $pwd=$this->hashPwd($passwd);//生成一个随机数密码
                $m['password']=$pwd;
                $m['avatar'] = "/Ucenter/images/noavatar_big.gif";
                $id = M("mb_member")->add($m);
                $this->sendRegisterMsg($id,$passwd);
                session("id",$id);
				$m['chat_id'] = 20000 + $id;
                 M("mb_member")->where(['id'=>$id])->save($m);
	             $im = new \Org\Util\Im();
	             $im->member_create($m['chat_id'],I("request.nickname"));
            }
            redirect("/");
        }
    }
    public function qqLogin(){
        require_once("../lib/qq/qqConnectAPI.php");  
        $qc = new \QC();  
        $qc->qq_login();
    }
    public function qqCallback(){
	
        require_once("../lib/qq/qqConnectAPI.php");  
        $qc = new \QC();
        //53F7057DF0574D858C6E330104A9B724
        //C4556DBFFD471E47F6E597180FD8B999
		$access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $qq = ['qq_openid'=>$openid];
        if (session("id")) {//已经登录, 绑定用户信息
            M("mb_member")->where(['id'=>session("id")])->save($qq);
            $this->redirect('/home/member/setting',['type'=>4]);
        }else{//没登陆设置一个新用户
            $m = M("mb_member")->where(['qq_openid'=>$qq['qq_openid']])->find();
            if ($m) {
               session("id",$m['id']); 
            }else{
                $m = array_merge($qq,["nickname"=>"xzb".time()]);
                $passwd=rand(100000, 999999);
                $pwd=$this->hashPwd($passwd);//生成一个随机数密码 $pwd=$this->hashPwd(rand(100000, 999999));//生成一个随机数密码
                $m['password']=$pwd;
                $m['avatar'] = "/Ucenter/images/noavatar_big.gif";
                $id = M("mb_member")->add($m);
                $this->sendRegisterMsg($id,$passwd);
                session("id",$id);
				$m['chat_id'] = 20000 + $id;
               M("mb_member")->where(['id'=>$id])->save($m);
	            $im = new \Org\Util\Im();
	            $im->member_create($m['chat_id'],I("request.nickname"));
            }
            redirect("/");
        }
		
    }
    public function wxLogin(){
        $backurl=$this->url()."home_member_wxcallback.html";
        $backurl=urlencode($backurl);//回调地址
        //用php自带的函数strpos来检测是否是微信端
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
            // 电脑
            $url = "https://open.weixin.qq.com/connect/qrconnect?appid=wxf863b49e4c21c734&redirect_uri=" . $backurl . "&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
        } else {
            //微信
            $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb48c9ae28cbf3da9&redirect_uri=".$backurl."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

        }
        redirect($url);
    }
    public function wxcallback(){
        $code=$_REQUEST['code'];
       // $appid="wxb48c9ae28cbf3da9";//微信
       // $appid="wxf863b49e4c21c734";//电脑
        //$secret="3a7105e764eb70f78c5c1cd0b0036d24";//电脑
       // $secret="c252620b3d56f9b33c9c404108f98761";//微信
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'MicroMessenger') === false) {
             $appid="wxf863b49e4c21c734";
            $secret="3a7105e764eb70f78c5c1cd0b0036d24";
        } else {
            //微信
            $appid="wxb48c9ae28cbf3da9";
            $secret="c252620b3d56f9b33c9c404108f98761";
        }
        //获取access_token
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        $wx_openid=$jsoninfo['openid'];//获取微信的OPEN_ID
        $data['wx']= $wx_openid;//数据库里WX字段记录微信open_id
        if (session("id")) {//已经登录, 绑定用户信息
            M("mb_member")->where(['id'=>session("id")])->save($data);
            $this->redirect('/home/member/setting',['type'=>4]);
        }else{//没登陆设置一个新用户
            $m = M("mb_member")->where(['wx'=>$data['wx']])->find();
            if ($m) {
                session("id",$m['id']);
            }else{
                $m['nickname'] ="xzb".time();
                 $passwd=rand(100000, 999999);
                 $pwd=$this->hashPwd($passwd);//生成一个随机数密码 $pwd=$this->hashPwd(rand(100000, 999999));//生成一个随机数密码
                $m['password']=$pwd;
                $m['wx']= $data['wx'];
                $m['avatar'] = "/Ucenter/images/noavatar_big.gif";
                $id = M("mb_member")->add($m);
                $this->sendRegisterMsg($id,$passwd);
                session("id",$id);
				$m['chat_id'] = 20000 + $id;
                 M("mb_member")->where(['id'=>$id])->save($m);
	             $im = new \Org\Util\Im();
	             $im->member_create($m['chat_id'],I("request.nickname"));
            }
            redirect("/");
        }

    }
    public function sendRegisterMsg($mb_id,$pwd){
        $d['type']=0;
        $d['to_member_id']=$mb_id;
        $d['add_time']=date('Y-m-d H:i:s');
        $d['content']="你好，欢迎登陆新助邦，你的初始密码是【".$pwd."】请到个人中心修改你的密码";
        M('message')->add($d);
}
    public function update(){
        $m = $_POST;
        if (($result = D("MbMember")->save(session("id"),$m)) === true) {
            $this->showTrueJson();
        }else{
            $this->showFalseJson($result);
        }
    }

	//验证码
	public function code(){
        ob_clean();
		$Verify = new \Think\Verify();
		$Verify->fontSize = 30;
		$Verify->length   = 4;
		$Verify->useNoise = false;
		$Verify->entry();

	}
    public function email($url){
        $re= M('mb_member')->find(session("id"));
        $this->assign("re",$re);
        $this->assign("url",$url);//跳转邮箱的地址
        $this->display();
    }
    public function findok($url){
    	$this->assign("url",$url);//跳转邮箱的地址
    	$this->display();
    }
    public function emailVerified($email){
       $re= M('mb_member')->find(session('id'));

        if(md5($re['email'])==$email){
            $id=session('id');
             $r= M('mb_member')->where('id='.$id)->setField('email_verified',1);
            if($r){
                header("Content-type: text/html; charset=utf-8");

        redirect('/Home_member_flow.html', 2, '验证成功...');
            }else{
                header("Content-type: text/html; charset=utf-8");
             redirect('/Home_member_register.html', 2, '验证失败...');
            }
        }else{
            header("Content-type: text/html; charset=utf-8");
           redirect('/Home_member_register.html', 2, '邮箱验证不正确...');
        }
    }
    public function sendEmailCode($email){
        //	$code = rand(1000,9999);
        //功能：根据用户输入的Email跳转到相应的电子邮箱首页
        $email_address = array("163.com" => "mail.163.com",
            "vip.163.com" => "vip.163.com",
            "126.com" => "mail.126.com",
            "qq.com" => "mail.qq.com",
            "vip.qq.com" => "mail.qq.com",
            "foxmail.com" => "mail.qq.com",
            "gmail.com" => "mail.google.com",
            "sohu.com" => "mail.sohu.com",
            "tom.com" => "mail.tom.com",
            "vip.sina.com" => "vip.sina.com",
            'sina.com.cn' => 'mail.sina.com.cn',
            'sina.com' => 'mail.sina.com.cn',
            'yahoo.com.cn' => 'mail.cn.yahoo.com',
            'yahoo.cn' => 'mail.cn.yahoo.com',
            'yeah.net' => 'www.yeah.net',
            '21cn.com' => 'mail.21cn.com',
            'hotmail.com' => 'www.hotmail.com',
            'sogou.com' => 'mail.sogou.com',
            '188.com' => 'www.188.com',
            '139.com' => 'mail.10086.cn',
            '189.cn' => 'webmail15.189.cn/webmail',
            'wo.com.cn' => 'mail.wo.com.cn/smsmail',
        );
        $url = $this->url()."home_member_emailverified.html?email=" . md5($email);
        $re = send_mail($email,C('waketitle')."完成注册邮箱验证", '请点击链接完成注册邮箱验证：<a href="'.$url.'" target=_blank>'.$url.'</a><p>如果不能点击，可直接复制以上链接在浏览器打开。<p>'.C('waketitle').' '.date('Y-m-d H:i:s'));
        \Think\Log::write("邮件发送结果".$re,"INFO");
        //cookie("emailCode",$code);
        if ($re === true) {
            //	$this->showTrueJson();
            $url = $email_address[ explode("@",$email)[1] ];
            $data['suc']=true;
            $data['type']="email";
            $data['message']="邮箱注册成功";
            $data['url']="http://" . $url;
        }else{
        	$data['suc']=$re;
            $data['message']="抱歉，邮件发送失败，请重试！";
        }
        return $data;
    }

	public function md5pas(){
	  $ms = M("mb_member")->select();
	  foreach($ms as $i => $c){
	     $ms[$i]['password'] = md5($c['password']);
		 $ms[$i]['paypassword'] = md5($c['paypassword']);
		 M("mb_member")->where(['id'=>$c['id']])->save($ms[$i]);
	  }
	  $this->showTrueJson();
	}
}
