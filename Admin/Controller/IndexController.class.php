<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
use Org\Util\Date;

class IndexController extends AdminBaseController{
  public function index(){
    $this->display();
  }

  public  function login(){
    if(IS_POST){
      // $condition = ["phone"=>I("post.username"),"email" => I("post.username"),"_logic"=>"OR"];
      // $member = M("mb_member")->where($condition)->find();
      // if($member && $member["password"] == I("post.password")){
      //   session('id',$member["id"]);
    		/* $this->success('登陆成功',U('Index/Index')); */
      // }else{
      //   $this->error("用户名密码错误");
      // }

        //TODO 后台登录逻辑修改
        /**
         * 登录验证病更新登录信息
         */
        $pw=I("post.password");
        $name=I("post.username");
        $map['username']=$name;
        $map['password']=$pw;
        $admin=M('admin')->where($map)->find();
        if (!empty($admin)){
            $update['last_login_ip']=post.get_client_ip();//获取登录的IP地址
            $update['last_login_time']=new Date();//获取登录的时间
            $where['id']=$admin['id'];
            M('admin')->where($where)->save($update);
            session('id',$admin['id']);
            redirect(U('admin/information/index'));
           //$this->success('登陆成功',U('Index/index'));
        }else{
            //redirect(U('/admin/index/login'));
            $this->error("用户名密码错误");
        }
        /**
         *
         */

        //die();
/*      if(I("post.username") == "admin" && I("post.password") == "admin"){
        session('id',1);
    		$this->success('登陆成功',U('Index/index'));
      }else{
        $this->error("用户名密码错误");
      }*/
    }else{
      $this->display();
    }
  }

  public function logout(){
    session("id",null);     
    $this->success('退出成功',U('/admin/index/login'));
  }
   /* public function change(){
        set_time_limit(0);
       $mb= M('mb_member');//用户表
        $info=M('information');
        $type=M('information_type');
        $circle=M('circle');
       $result= $info->select();
        foreach($result as $k=>$v){
            $re=$mb->find($v['member_id']);//查出NICKNAME
           if($v['member_id']==0){
               $d['nickname']="管理员";
           }else{
               $d['nickname']=$re['nickname'];//把nickname存进去
           }
            if($v['type2']!=0) {//把院校动态除去
                $type2 = $type->find($v['type2']);//查出type
            }
            if($v['circle_id']!=0) {
                $map['circle_type'] = 0;
                $map['object_id'] = $v['circle_id'];
                $circle_re = $circle->where($map)->find();//查出圈子ID
                $d['circle_id']=$circle_re['id'];//把circle存进去
            }
            echo "nickname:".$re['nickname']."</br>";
            echo "type:".$type2['type']."</br>";
            echo "circle:".$circle_re['id']."</br>";
            $d['type']=$type2['type'];//把type存进去

            if(M('information')->where('id='.$v['id'])->save($d)){
                echo "ok</br>";
            }else{
                echo "no!!!</br>";
            }
        }
    }
    public function addcontent(){
        //$dbOld = M()->db( 'mysql://alhelp:623027!@#zhangxinyue&X1Z2B3@120.25.161.67:3306/alhelp');

        $con = mysql_connect("120.25.161.67","alhelp","623027!@#zhangxinyue&X1Z2B3");
        if (!$con)
        {die('Could not connect: ' . mysql_error());
        }
        mysql_select_db('alhelp',$cn);
        dump($con);
    }
    public function address(){
        set_time_limit(0);
        ini_set ('memory_limit', '128M');
        $map['address']=array('neq','');
        $copy=M('school_copy')->where($map)->select();
        $school=M('school');
        foreach($copy as $k=>$v){
            if($v['address']!=""){
                $d['address']=$v['address'];
                $d['telephone']=$v['telephone'];
               $re= $school->where("id=".$v['id'])->save($d);
                if($re){
                    $i++;
                    echo "ok,成功保存".$i."条</br>";
                }
            }
        }
    }*/
}
