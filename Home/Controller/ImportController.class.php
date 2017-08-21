<?php
namespace Home\Controller;

class ImportController extends \Home\Common\HomeBaseController {
	
	//导入会员表
    public function index(){
		$page = intval($_GET['page']); $page = empty($page) ? 1 : $page;
		$size = 100; $start = ($page-1) * $size; $int = 0;
		
		$dbOld = M()->db(1,"mysql://root:@localhost:3306/alhelpold");
		$list = $dbOld->table('member')->limit($start, $size)->select();
		foreach($list as $rs){
			$data = array(
				'id'=>$this->defValue($rs['id']),
				'last_serve_time'=>$this->defValue($rs['last_serve_time']),
				'last_comment_time'=>$this->defValue($rs['last_comment_time']),
				'pid'=>$this->defValue($rs['pid']),
				'coin'=>$this->defValue($rs['coin']),
				'score'=>$this->defValue($rs['score']),
				'member_group_id'=>$this->defValue($rs['member_group_id']),
				'province'=>intval($rs['province']),
				'city'=>intval($rs['city']),
				'area'=>intval($rs['area']),
				'vip_order'=>$this->defValue($rs['is_vip_order']),
				'hot'=>$this->defValue($rs['hot']),
				'last_post_time'=>$this->defValue($rs['last_post_time']),
				'follow_num'=>$this->defValue($rs['follow_num']),
				'question_num'=>$this->defValue($rs['question_num']),
				'fans_num'=>$this->defValue($rs['fans_num']),
				'demand_num'=>$this->defValue($rs['demand_num']),
				'serve_num'=>$this->defValue($rs['serve_num']),
				'news_num'=>$this->defValue($rs['news_num']),
				'praise_num'=>$this->defValue($rs['praise_num']),
				'avatar'=>empty($rs['avatar']) ? '/Ucenter/images/noavatar_big.gif' : $rs['avatar'],
				'avatar1'=>$this->defValue($rs['avatar1']),
				'email'=>$this->defValue($rs['email']),
				'nickname'=>$this->defValue($rs['nickname']),
				'account'=>$this->defValue($rs['account']),
				'phone_verified'=>$this->defValue($rs['phone_verified']),
				'phone'=>$this->defValue($rs['phone']),
				'address'=>$this->defValue($rs['address']),
				'yy'=>$this->defValue($rs['yy']),
				'password'=>$this->defValue($rs['password']),
				'paypassword'=>$this->defValue($rs['paypassword']),
				'reg_ip'=>$this->defValue($rs['reg_ip']),
				'last_login_ip'=>$this->defValue($rs['last_login_ip']),
				'qq'=>$this->defValue($rs['qq']),
				'balance'=>$this->defValue($rs['balance']),
				'balance_frozen'=>$this->defValue($rs['balance_frozen']),
				'bond'=>$this->defValue($rs['bond']),
				'status'=>$this->defValue($rs['status']),
				'is_male'=>$this->defValue($rs['is_male']),
				'is_realname'=>$this->defValue($rs['is_realname']),
				'recommend_as_student'=>$this->defValue($rs['recommend_as_student']),
				'recommend_as_teacher'=>$this->defValue($rs['recommend_as_teacher']),
				'reg_time'=>$this->defValue($rs['reg_time']),
				'last_login_time'=>$this->defValue($rs['last_login_time']),
				'email_verified'=>1,
				'is_v'=>$this->defValue($rs['is_vip']),
				'default_circle_id'=>$this->defValue($rs['community_id']),
			);
			$int++;
			try{
				M('mb_member')->add($data);
				//weixin {"openid":"o8272t9SKiOuH5Dwam402Jyi3brw","nickname":"Cherring","sex":2,"language":"zh_CN","city":"\u4e09\u4e9a","province":"\u6d77\u5357","country":"\u4e2d\u56fd","headimgurl":"http:\/\/wx.qlogo.cn\/mmopen\/ibibkXDRkSibH9wxFicG1bZnn88jiaut9wWcW9Artbfx07pZYCw8t0oAYtgSVd5U13E9QCUG0QbzpN2X0xuBpIkj3Yk6bBd6youqf\/0","privilege":[]}
				//qq {"ret":0,"msg":"","is_lost":0,"nickname":"\u9f99\u601d\u51e1","gender":"\u7537","province":"\u6e56\u5317","city":"\u5b5d\u611f","year":"2005","figureurl":"http:\/\/qzapp.qlogo.cn\/qzapp\/101164434\/06EA45FDBD29A84BC165C1C86D1A39B9\/30","figureurl_1":"http:\/\/qzapp.qlogo.cn\/qzapp\/101164434\/06EA45FDBD29A84BC165C1C86D1A39B9\/50","figureurl_2":"http:\/\/qzapp.qlogo.cn\/qzapp\/101164434\/06EA45FDBD29A84BC165C1C86D1A39B9\/100","figureurl_qq_1":"http:\/\/q.qlogo.cn\/qqapp\/101164434\/06EA45FDBD29A84BC165C1C86D1A39B9\/40","figureurl_qq_2":"http:\/\/q.qlogo.cn\/qqapp\/101164434\/06EA45FDBD29A84BC165C1C86D1A39B9\/100","is_yellow_vip":"0","vip":"0","yellow_vip_level":"0","level":"0","is_yellow_year_vip":"0"}
				//webo {"id":2995687801,"idstr":"2995687801","class":1,"screen_name":"\u4e39\u4e392995687801","name":"\u4e39\u4e392995687801","province":"14","city":"1","location":"\u5c71\u897f \u592a\u539f","description":"","url":"","profile_image_url":"http:\/\/tp2.sinaimg.cn\/2995687801\/50\/0\/0","profile_url":"u\/2995687801","domain":"","weihao":"","gender":"f","followers_count":11,"friends_count":64,"pagefriends_count":0,"statuses_count":37,"favourites_count":23,"created_at":"Wed Oct 10 22:29:30 +0800 2012","following":false,"allow_all_act_msg":false,"geo_enabled":true,"verified":false,"verified_type":-1,"remark":"","status":{"created_at":"Sat Oct 25 21:02:47 +0800 2014","id":3.7696672289352e+15,"mid":"3769667228935249","idstr":"3769667228935249","text":"\u5f88\u597d\u7528 http:\/\/t.cn\/R7iha2x","source_type":1,"source":"<a href=\"http:\/\/app.weibo.com\/t\/feed\/3habA6\" rel=\"nofollow\">\u7ea2\u5305\u9501\u5c4fapp<\/a>","favorited":false,"truncated":false,"in_reply_to_status_id":"","in_reply_to_user_id":"","in_reply_to_screen_name":"","pic_urls":[],"geo":null,"annotations":[{"client_mblogid":"f4200326-cd9b-48db-b7ee-58051677fcbb","shooting":1}],"reposts_count":0,"comments_count":0,"attitudes_count":0,"mlevel":0,"visible":{"type":0,"list_id":0},"darwin_tags":[]},"ptype":0,"allow_all_comment":true,"avatar_large":"http:\/\/tp2.sinaimg.cn\/2995687801\/180\/0\/0","avatar_hd":"http:\/\/tp2.sinaimg.cn\/2995687801\/180\/0\/0","verified_reason":"","verified_trade":"","verified_reason_url":"","verified_source":"","verified_source_url":"","follow_me":false,"online_status":0,"bi_followers_count":3,"lang":"zh-cn","star":0,"mbtype":0,"mbrank":0,"block_word":0,"block_app":0,"credit_score":80}
				$member_bind = M('member_bind')->where(array('uid'=>$rs['id']))->find();
				if($member_bind){
					M('member_bind')->add($member_bind);
					
					
					$info = unserialize($member_bind['info']);
					switch ($member_bind['type']){
						case 'weixin':
							$data = array(
									'qq_openid'=>$info['openid'],
									'nickname'=>$info['nickname'],
									'is_male'=>($info['sex']==2) ? 1 : 0,
									'avatar'=>$info['headimgurl'],
							);
							break;
						case 'Qq':
							$data = array(
									'nickname'=>$info['nickname'],
									'is_male'=>($info['gender']=='男') ? 1 : 0,
									'avatar'=>$info['figureurl_qq_2'],
							);
							break;
						case 'Weibo':
							$data = array(
									'weibo_uid'=>$info['id'],
									'nickname'=>$info['name'],
									'weibo_nickname'=>$info['name'],
									'avatar'=>$info['avatar_large'],
							);
							break;
					}
					if($data) M('mb_member')->where(array('id'=>$rs['id']))->save($data);
				}
			}catch (\Exception $e){
				continue;
			}
		}
		if($list && $int == $size){
			$this->success('成功导入'.M('mb_member')->field('count(*) as num')->find()['num'].'条。', '?page='.($page+1));
		}else{
			die('已经导入完成，共计'.M('mb_member')->field('count(*) as num')->find()['num'].'条！');
		}
    }
    
    //导入找需求表 demand
    public function demand(){
    	$page = intval($_GET['page']); $page = empty($page) ? 1 : $page;
    	$size = 100; $start = ($page-1) * $size; $int = 0;
    
    	$dbOld = M()->db(1,"mysql://root:@localhost:3306/alhelpold");
    	$list = $dbOld->table('demand')->limit($start, $size)->select();
    	foreach($list as $rs){
    		$data = array(
    				'demand_id'=>$this->defValue($rs['id']),
    				'member_id'=>$this->defValue($rs['member_id']),
    				'praise_num'=>$this->defValue($rs['praise_num']),
    				'review_num'=>$this->defValue($rs['review_num']),
    				'public_subject_id'=>$this->defValue($rs['pid']),
    				'qq'=>$this->defValue($rs['qq']),
    				'member_name'=>$this->defValue($rs['member_nickname']),
    				'demand_type'=>$this->defValue($rs['demand_type']),
    				'require_identity'=>$this->defValue($rs['require_identity']),
    				'require_authenticate'=>$this->defValue($rs['require_authenticate']),
    				'require_security'=>$this->defValue($rs['require_security']),
    				'profes_type'=>$this->defValue($rs['profes_type']),
    				'status'=>$this->statusValue($rs['status']),
    				'description'=>$this->defValue($rs['description']),
    				'mobile'=>$this->defValue($rs['mobile']),
    				'cost'=>$this->defValue($rs['cost']),
    				'school_id'=>$this->defValue($rs['university']),
    				'detail'=>$this->defValue($rs['content_demand']),
    				'demand_plain'=>$this->defValue($rs['content_reference']),
    				'add_time'=>$this->defValue($rs['add_time']),
    				'update_time'=>$this->defValue($rs['update_time']),
    		);
    		$int++;
    		try{
    			M('demand')->add($data);
    		}catch (\Exception $e){
    			continue;
    		}
    	}
    	if($list && $int == $size){
    		$this->success('成功导入'.M('demand')->field('count(*) as num')->find()['num'].'条。', '?page='.($page+1));
    	}else{
    		die('已经导入完成，共计'.M('demand')->field('count(*) as num')->find()['num'].'条！');
    	}
    }
    
    private function defValue($v){
    	return is_null($v) ? '' : $v;
    }
    
    private function statusValue($v){
    	//旧网站 -1 删除 0 未审核 1 已审核 2未通过
    	//新网站 0上架  4禁用 1 未审核  2删除  3下架
    	if($v==-1){
    		return 2;
    	}
    	if($v==0){
    		return 1;
    	}
    	if($v==1){
    		return 0;
    	}
    	if($v==2){
    		return 4;
    	}
    }
}
