<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class ShareController extends AdminBaseController{
	  protected $table_name = "share";
	public function index()
	{
		$arr=M('share')->page($_GET['page']?:1,20)->order('id desc')->select();
		foreach($arr as $k =>$v){
			$list[]=parse_class($v);
	}
		$cou=M('share')->count();
		//dump($list);
		$this->assign_page($cou);
		$this->assign('list',$list);
		$this->display();
	}
	public function edit($id){
		$info=M('share')->find($id);
		$info=parse_class($info);
		$this->assign("info",$info);
		if(IS_POST){
			$d['title']= $_POST['title'];
			$d['type']= $_POST['type'];
			$d['content']= $_POST['content'];
			$d['nickname']= $_POST['name'];
			$d['url']= $_POST['url'];
			$d['download_type']= $_POST['download_type'];
			$d['attachment_name'] = $_POST['attachment_name'];
			if($d['download_type']==1||$d['download_type']==2||$d['download_type']==3) {
				$d['download_cost'] = $_POST['download_cost'];
			}
			$attachment=upload();
			if($attachment){
				$d['attachment'] = $attachment['attachment'];
			}
			if($_POST['unified_id']!=0){
				$d['public_subject_id']=0;
				$d['school_id']=0;
				$d['unified_id']=$_POST['unified_id'];
			}
			if($_POST['school_id']!=0){
				$d['public_subject_id']=0;
				$d['school_id']=$_POST['school_id'];
				$d['unified_id']=0;
			}
			if($_POST['public_subject_id']!=0){
				$d['public_subject_id']=$_POST['public_subject_id'];
				$d['school_id']=0;
				$d['unified_id']=0;
			}
			if(M('share')->where('id='.$id)->save($d)){
				$this->success("保存成功",U('index'));
			}
		}
		$this->display();
	}
	/*public function add(){
		if(IS_POST){
			$d['title']= $_POST['title'];
			$d['type']= $_POST['type'];
			$d['content']= $_POST['content'];
			$d['nickname']= $_POST['name'];
			$d['url']= $_POST['url'];
			if($_POST['unified_id']!=0){
				$d['unified_id']=$_POST['unified_id'];
			}
			if($_POST['school_id']!=0){
				$d['school_id']=$_POST['school_id'];
			}
			if($_POST['public_subject_id']!=0){
				$d['public_subject_id']=$_POST['public_subject_id'];
			}
			if(M('share')->add($d)){
				$this->success("保存成功",U('index'));
			}
		}
		$this->display();
	}*/
}
