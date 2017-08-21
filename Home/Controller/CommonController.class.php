<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class CommonController extends HomeBaseController {
  //发送短信通知
  public function sendPhoneMsg($phone,$content){
    require "../lib/Sms.class.php";
    $sms = new \Sms();
    $sms->sendToServer($phone,$content);  
  }
  //验证码
	public function verifyCode(){
        ob_clean();
	    $code = new \Think\Verify();
	    $code->length = 4;
	    $code->codeSet = '0123456789';
	    $code->entry();
	}
	
	//先用cookit偷个懒, 有空再改 ,为了安全改用session
	public function sendPhoneCode($phone,$findpwd=''){
		require "../lib/Sms.class.php";
		$sms = new \Sms();
		$code = rand(1000,9999);
		$re=M('mb_member')->where("phone=".$phone)->find();
		if($findpwd==1){
			if(!$re){
				$this->showFalseJson("你的手机未注册!");
				exit;
			}
		}else{
			if($re){
				$this->showFalseJson("你的手机已被其他用户绑定!");
				exit;
			}
		}
		if( $sms->sendToServer($phone, "你的验证码是".$code) ){
			cookie("phoneCode",$code);
			//	file_put_contents("F://code.txt",$code);	
 			$data = array(
 				'signKey' => $code,
			);
			$this->showTrueJson($data);
		}else{
			$this->showFalseJson($sms->error);
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
		$url = $this->url()."home_member_findPwd.html?email=" . base64_encode($email);
		$result = send_mail($email,C('waketitle')."重置密码链接", '请点击链接进入密码重置：<a href="'.$url.'" target=_blank>'.$url.'</a><p>如果不能点击，可直接复制以上链接在浏览器打开。<p>'.C('waketitle').' '.date('Y-m-d H:i:s'));
		\Think\Log::write("邮件发送结果".$result,"INFO");
		//cookie("emailCode",$code);
		if ($result === true) {
		//	$this->showTrueJson();  
			$url = $email_address[ explode("@",$email)[1] ];  
			$this->success('重置密码链接已发送到您邮箱，即将跳转到你邮箱...',"http://" . $url);
		}else{
			$this->error('邮件发送失败，请返回重试',$this->url()."home_member_forget2.html");
		}
	}
	public function config($key){
		$this->showTrueJson(C($key));
	}
	public function js(){
		$this->display("../Application/Home/View/Common/js.js");
	}

    public function wxNotify(){
     $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        \Think\Log::write("微信的东西".$xml);
		require_once "../lib/wx/lib/WxPay.Api.php";
		require_once '../lib/wx/lib/WxPay.Notify.php';
		require_once '../lib/wx/PayNotifyCallBack.class.php';

        $notify = new \PayNotifyCallBack();
		$res = $notify->Handle(false);
		\Think\Log::write($res);		
    }

    public function wxPay($code,$id,$type){
    	if ($type == 'recharge') { //充值
    		$fee = M("recharge")->getFieldById($id,'balance');
    	}elseif ($type == 'serviceOrder') { //答疑授课订单
    		$fee = M('service_order_view')->getFieldById($id,'fee');
    	}else if($type == 'demand'){ //托付费用
    		$fee = M("demand")->getFieldByDemandId($id,'cost');
    	}else if($type == 'order'){ //资料订单
    		go_assert(is_array($id),'id必须是数组');
    		$fee = M("order")->where(['id'=>['in',$id]])->sum('total');
    	}else if($type == 'quiz'){ //有偿问答
		    $fee = M("member_post")->getFieldById($id,'reward');
		}else if($type == 'benefit'){ //偷看答案
		    $fee = M("member_post")->getFieldById($id,'reward');
			$fee = $fee * 0.1;
		}else if($type == 'openClass'){ //直播课
		    $op = M("open_class")->where(['open_class_id'=>$id])->field("price")->find();
			$fee = $op['price'];
		}else if($type == 'dataBid'){ //资料投标
    		$order = M("order")->where(['id'=>$id])->find();
			$bid = M("bid")->where(['id'=>$order['bid']])->find();
			$dem = M("demand")->where(['demand_id'=>$bid['demand_id']])->find();
			$fee = $order['total'] - $dem['advance_payment'];
		}else if($type == 'classBid'){ //答疑授课投标
            $total = M('service_order_view')->getFieldById($id,'fee');
			$order = M("service_order")->find($id);
            $bid = M("bid")->where(['id'=>$order['bid']])->find();
			$dem = M("demand")->where(['demand_id'=>$bid['demand_id']])->find();
			$fee = $total - $dem['advance_payment'];
		}else if($type == 'download'){
		  $info = M("information")->where(['id'=>$id[0]])->find();
			$fee = $info['attachment_cost'];
		}else if($type == 'sharedownload'){
		 $info = M("share")->where(['id'=>$id[0]])->find();
			$fee = $info['download_cost'];
		}else{
    		E("未知类型");
    	}
    	$this->fee = $fee;
    	$this->display();
    }
    public function upload(){
    	$data = upload();  
    	if (count($data) == 1) {
    		$data = array_values($data)[0];
    	}
    	$this->showTrueJson($data);
    }
    /*点赞功能
@param type:说说0，需求1，服务2  直播课3  4咨询
@param  member_post  demand  service
@param object_id  对应表中纪录的ID
@param member_id   点赞的人
同一条纪录不能反复点赞
@author:chastelove
 */
public function newPraise(){
  $type=I('type');
  $object_id=I('object_id');
  $member_id=I('member_id');
  $map['type']=$type;
  $map['object_id']=$object_id;
  $map['member_id']=$member_id;
  $re=M('praise')->where($map)->find();
  if($re){//存在点赞过取消点赞
      if($type==0){
      unset($re);
          $re=M("member_post")->where("id=".$object_id)->find();
          if($re['member_id']==$member_id){
            $this->showFalseJson("您不能赞自己哦");
          }else{
          M('member_post')->where("id=".$object_id)->setDec('praise_num',1);
          M('praise')->where($map)->delete();
          $this->showFalseJson("取消点赞");
    }
    }
    if($type==1){
      unset($re);
          $re=M("demand")->where("demand_id=".$object_id)->find();
          if($re['member_id']==$member_id){
            $this->showFalseJson("您不能赞自己哦");
          }else{
           M('demand')->where("demand_id=".$object_id)->setDec('praise_num',1);
            M('praise')->where($map)->delete();
         $this->showFalseJson("取消点赞");
    }
  }
    if($type==2){
      unset($re);
          $re=M("service")->where("service_id=".$object_id)->find();
          if($re['member_id']==$member_id){
            $this->showFalseJson("您不能赞自己哦");
          }else{
            M('service')->where("service_id=".$object_id)->setDec('praise_num',1);
             M('praise')->where($map)->delete();
         $this->showFalseJson("取消点赞");
     }
    }
    if($type==3){
      unset($re);
          $re=M("open_class")->where("open_class_id=".$object_id)->find();
          if($re['member_id']==$member_id){
            $this->showFalseJson("您不能赞自己哦");
          }else{
            M('open_class')->where("open_class_id=".$object_id)->setDec('praise_num',1);
             M('praise')->where($map)->delete();
         $this->showFalseJson("取消点赞");
     }
    }
      if($type==4){
          unset($re);
          $re=M("information")->where("id=".$object_id)->find();
          if($re['member_id']==$member_id){
              $this->showFalseJson("您不能赞自己哦");
          }else{
              M('information')->where("id=".$object_id)->setDec('praise_num',1);
              M('praise')->where($map)->delete();
              $this->showFalseJson("取消点赞");
          }
      }
  }
  if($type==0){
    unset($re);
        $re=M("member_post")->where("id=".$object_id)->find();
        if($re['member_id']==$member_id){
          $this->showFalseJson("您不能赞自己哦");
        }else{
        M('member_post')->where("id=".$object_id)->setInc('praise_num',1);
        $data['type']=$type;
        $data['object_id']=$object_id;
        $data['member_id']=$member_id;
        $data['add_time']=date('Y-m-d h:i:s',time());
         M('praise')->add($data);
        $this->showTrueJson("点赞成功");
  }
  }
  if($type==1){
    unset($re);
        $re=M("demand")->where("demand_id=".$object_id)->find();
        if($re['member_id']==$member_id){
          $this->showFalseJson("您不能赞自己哦");
        }else{
         M('demand')->where("demand_id=".$object_id)->setInc('praise_num',1);
         $data['type']=$type;
        $data['object_id']=$object_id;
        $data['member_id']=$member_id;
        $data['add_time']=date('Y-m-d h:i:s',time());
         M('praise')->add($data);
       $this->showTrueJson("点赞成功");
  }
}
  if($type==2){
    unset($re);
        $re=M("service")->where("service_id=".$object_id)->find();
        if($re['member_id']==$member_id){
          $this->showFalseJson("您不能赞自己哦");
        }else{
          M('service')->where("service_id=".$object_id)->setInc('praise_num',1);
          $data['type']=$type;
        $data['object_id']=$object_id;
        $data['member_id']=$member_id;
        $data['add_time']=date('Y-m-d h:i:s',time());
         M('praise')->add($data);
       $this->showTrueJson("点赞成功");
   }
  }
  if($type==3){
    unset($re);
        $re=M("open_class")->where("open_class_id=".$object_id)->find();
        if($re['member_id']==$member_id){
          $this->showFalseJson("您不能赞自己哦");
        }else{
          M('open_class')->where("open_class_id=".$object_id)->setInc('praise_num',1);
          $data['type']=$type;
        $data['object_id']=$object_id;
        $data['member_id']=$member_id;
        $data['add_time']=date('Y-m-d h:i:s',time());
         M('praise')->add($data);
       $this->showTrueJson("点赞成功");
   }
  }
    if($type==4){
        unset($re);
        $re=M("information")->where("id=".$object_id)->find();
        if($re['member_id']==$member_id){
            $this->showFalseJson("您不能赞自己哦");
        }else{
            M('information')->where("id=".$object_id)->setInc('praise_num',1);
            $data['type']=$type;
            $data['object_id']=$object_id;
            $data['member_id']=$member_id;
            $data['add_time']=date('Y-m-d h:i:s',time());
            M('praise')->add($data);
            $this->showTrueJson("点赞成功");
        }
    }
}
/*end*/
function editorImage()
{
    // Allowed extentions.
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    // Get filename.
    $temp = explode(".", $_FILES["file"]["name"]);
    // Get extension.

    $extension = end($temp);
    // An image check is being done in the editor but it is best to
    // check that again on the server side.
    // Do not use $_FILES["file"]["type"] as it can be easily forged.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
    if ((($mime == "image/gif") || ($mime == "image/jpeg") || ($mime == "image/pjpeg") || ($mime == "image/x-png") || ($mime == "image/png")) && in_array($extension, $allowedExts)) {
        // Generate new random name.
        $path = "./Uploads/" . date('Y-m-d') . '/';
        if(!is_dir($path)) mkdir($path);
        $name = $path .sha1(microtime()) . "." . $extension;
        // Save file in the uploads folder.
        move_uploaded_file($_FILES["file"]["tmp_name"], $name);
        // Generate response.

        //$response = new StdClass;
       // $response->link = $name;
        $response = [];
        $response['link'] = $name;
        echo stripslashes(json_encode($response));
    }
}
}
