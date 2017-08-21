<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class InformationController extends AdminBaseController{
  protected $table_name = "information";
    //通过type区分一下栏目，栏目已经写入数据库注释，这里按照以前原来思想 通过2个type区别一下栏目
  public function index($type=0){
   $list= M('information')->where('information.type='.$type)
       ->field('add_time,information.id,information_type.name,nickname,title,attachment_score,views,download_num,status,attachment_type,attachment_cost,attachment_coin')
       ->join('left join information_type ON information.type2=information_type.id')
       ->page($_GET['page']?:1,10)->order("id desc")->select();
    foreach($list as $k =>$v){
      if($v['status']==0){
        $v['status_name']="正常";   
      }else{
        $v['status_name']="禁用";
      }
        $list1[]=$v;
    }
      $type_name=["下载","资讯","分享","公告","我们","院校动态","你该知道的新助邦"];
     $count= M('information')->where('type='.$type)->count();
      $this->assign('type_name',$type_name);
      $this->assign('count',$count);
      $this->assign('type',$type);
    $this->assign('list',$list1);
    $this->display('index0');

  }
  public function stop($id){
    //0  正常   1为禁用
    if(M('information')->where("id=".$id)->setField('status',1)){
      $this->error("已禁用",U('admin/Information/index',1));
    }
  }
    public function start($id){
      //0  正常   1为禁用
      if(M('information')->where("id=".$id)->setField('status',0)){
        $this->success("已启用",U('admin/Information/index',1));
      }
  }
    public function edit($id,$type){
        $type2=M('information_type')->where('type='.$type)->select();
        $type2=$this->tree($type2);//type2为大分类下的子分类  自己添加的分类
        $this->assign('type2',$type2);
        $info=M('information')->find($id);
        $info=parse_class($info);
        $this->assign('info',$info);
        if(IS_POST){
           $d['title']= $_POST['title'];
            $d['type']= $_POST['type'];
            $d['content']= $_POST['content'];
            $d['nickname']= $_POST['name'];
            $d['url']= $_POST['url'];
            if($_POST['attachment_name']) {
                $d['attachment_name'] = $_POST['attachment_name'];
            }
            $d['attachment_type']= $_POST['attachment_type']?:0;
            if($_POST['attachment_score']){
                $d['attachment_score']= $_POST['attachment_score'];
            }
            if($_POST['attachment_cost']){
                $d['attachment_cost']= $_POST['attachment_cost'];
            }
            if($_POST['attachment_coin']){
                $d['attachment_coin']= $_POST['attachment_coin'];
            }
            if($_POST['unified_id']!=0){
                $d['unified_id']=$_POST['unified_id'];
            }
            if($_POST['school_id']!=0){
                $d['school_id']=$_POST['school_id'];
            }
            if($_POST['public_subject_id']!=0){
                $d['public_subject_id']=$_POST['public_subject_id'];
            }
            //上传图片或者附件
            $upload = upload();
            $image = $upload['image'];
            $attachment = $upload['attachment'];
            if ($image) {
                $d['image']  =$image;
            }
            if ($attachment) {
                $d['attachment'] = $attachment;
            }

            $d['circle_id']=$this->getSchoolBycircleID($_POST);
            if(M('information')->where("id=".$id)->save($d)){
                $this->success("保存成功",U('admin/information/index',['type'=>$type]));
            }
        }
        $type_name=["下载","资讯","分享","公告","我们","院校动态","你该知道的新助邦"];
        $this->assign('type',$type);
        $this->assign('type_name',$type_name);
        $this->display();
    }
    public  function add($type){
        $type2=M('information_type')->where('type='.$type)->select();
        $typ2e=$this->tree($type2);
        $this->assign('type2',$typ2e);
        if(IS_POST){
            $d['title']= $_POST['title']?:0;
            $d['type']= $_POST['type']?:0;
            $d['type2']= $_POST['type2']?:0;

            if($_POST['school_id']){
                $d['school_id']= $_POST['school_id']?:0;
            }
            $d['unified_id']= $_POST['unified_id']?:0;
            $d['public_subject_id']= $_POST['public_subject_id']?:0;
            $d['content']= $_POST['content']?:0;
            $d['member_id']=0;//，默认0是管理员
            $d['nickname']= $_POST['name']?:0;
            $d['url']= $_POST['url']?:0;
            $d['add_time']= date('Y-m-d h:i:s',time());
            if($_POST['attachment_name']) {
                $d['attachment_name'] = $_POST['attachment_name'];
            }
            $d['attachment_type']= $_POST['attachment_type']?:0;
                if($_POST['attachment_score']){
                    $d['attachment_score']= $_POST['attachment_score'];
                }
            if($_POST['attachment_cost']){
                $d['attachment_cost']= $_POST['attachment_cost'];
            }
            if($_POST['attachment_coin']){
                $d['attachment_coin']= $_POST['attachment_coin'];
            }
            //上传图片或者附件
            $upload = upload();
            $image = $upload['image'];
            $attachment = $upload['attachment'];
            if ($image) {
                $d['image']  =$image;
            }
            if ($attachment) {
                $d['attachment'] = $attachment;
            }
            
            $d['circle_id']=$this->getSchoolBycircleID($_POST);
            
            if(M('information')->add($d)){  
                $this->success("保存成功",U('admin/information/index',['type'=>$type]));
            }
        }
        $type_name=["下载","资讯","分享","公告","我们","院校动态","你该知道的新助邦"];
        $this->assign('type_name',$type_name);
        $this->assign('type',$type);
        $this->display();
    }
     
    
    private function getSchoolBycircleID($arr){
    	if($arr['daxue']){
            $circle = M('zysc_view')->where(array('school_id'=>$arr['daxue']))->getField('circle_id');
            //dump(M()->getLastSql());exit;
        }
        if(!$circle) $circle=0;
    	return $circle;
    }
} 
