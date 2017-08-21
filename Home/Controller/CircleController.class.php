<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
class CircleController extends HomeBaseController {
	/*这里是个坑，它应该是直接在页面里读取了数据，
	所以这个控制器里找不到这个方法，但是可以通过静态页面读取数据
	,注明一下为以后开发人员提示
	author:chastelove  2016.11.16
	*/
	//跳转下面这两个方法openClass,memberPost
	public function _empty(){
		if (!I("request.id")) {
			$this->show("没有id"."/index.php/home_circle_OpenClass_id_1");
		}else{
			$circle = M("circle")->find(I("request.id"));
			$this->assign("circle",$circle);
			$this->display();
		}
	}
	public function show($id){
		$this->redirect('/home/circle/memberPost',['id'=>$id]);
	}
	public function profile($id){
		$this->redirect('/home/circle/memberPost',['id'=>$id]);
	}
	public function index(){
		//$this->mustLogin();
		//默认校友圈
	
		$type = I("request.type") ?: 0;
		$page = I("request.page") ?: 1;

		if(session('id')) {
			$circles = M("circle")->join("LEFT JOIN school ON circle.object_id=school.id")->field("circle.*,school.title,address,telephone,(SELECT COUNT(*) FROM circle_member WHERE circle_id=circle.id)as num,(SELECT id FROM member_follow_circle WHERE circle_id=circle.id AND member_id=".session("id").") as member_follow_circle_id");
		}else{
			//$circles = M("circle")->join("LEFT JOIN school ON circle.object_id=school.id");
			$circles = M("circle")->join("LEFT JOIN school ON circle.object_id=school.id")->field("circle.*,school.title,address,telephone,(SELECT COUNT(*) FROM circle_member WHERE circle_id=circle.id)as num");
		}
		$school_id = I("request.school_id");
		if ($school_id > 0) {
			unset($circles);
			$circles = M("school")->table("school school,circle c")->where('school.type=2')->where(["school.id = $school_id OR school.id IN (SELECT id FROM school where pid=$school_id) OR school.id = (SELECT pid FROM school where id=$school_id)",'school.id = c.object_id','c.circle_type = 0']);
				//$circles = $circles
			//	->where("school.id = $school_id OR school.id IN (SELECT id FROM school where pid=$school_id) OR school.id = (SELECT pid FROM school where id=$school_id)");
			$cm = clone $circles;
			$this->count = $cm->count();
			$circles = $circles->page($page, 20)->field("school.*,c.*")->order("school.add_time desc")->select();
		}else {
			$cm = clone $circles;
			$this->count = $cm->count();
			$circles = $circles->page($page, 20)->where('circle.status=0 AND circle.circle_type=0 AND school.type=2')->field("school.*,circle.*")->order("circle.add_time desc")->select();
		}
	///	dump($circles);
		$this->assign("circles",$circles);
		$this->display();
	}
	public function majorCircle(){
		$page = I("request.page") ?: 1;
		$school_id = I("request.school_id");//为了不改前面的东西发生关系，这个传的就是major_id
		if ($school_id > 0) {
			$re=M('mj_major')->find($school_id);
			$map['circle_type']=1;
			$map['object_id']=$school_id;
			$map['pid']=$re['pid'] ;
			$circles=M('circle')->where($map)->page($page, 20)->select();
			$sum=M('circle')->where($map)->count();
			$this->count = $sum;
			$this->assign("major1_res",$re['mj_name']);
			$this->assign("major1_id",$school_id);
			$mjs = M("mj_major")->where(['pid'=>$school_id])->select();//如果提交表单要继续找下级分类
			if($mjs[0]!=""){
				//通过ID查询父级
				$major1=M('mj_major')->find($school_id);//最低级
				$arr[0]=$major1['mj_name'];
				if($major1['pid']!=0) {
					$major1_res = M('mj_major')->find($major1['pid']);//上一级
					$arr[1]=$major1_res['mj_name'];
					if($major1['pid']!=0) {
						$major1_top = M('mj_major')->find($major1_res['pid']);//顶级
						$arr[2]=$major1_top['mj_name'];
					}
				}
				$this->assign("major1_res",$major1_res['mj_name']);
				$this->assign("major2",$major1['mj_name']);
				$this->assign("major1_top",$major1_top['mj_name']);
				//要查出圈子的ID
				unset($map);
				$map['object_id']=$school_id;
				$map['pid']=$major1['pid'];
				$map['circle_type']=1;
				$major2_res=M('circle')->where($map)->find();
				$this->assign("major2_id",$major2_res['id']);
			}
		}else {
			$circles = M('circle')->where("circle_type=1")->page($page, 20)->select();
			$sum = M('circle')->where("circle_type=1")->count();
			$this->count = $sum;
			$mjs = M("mj_major")->where(['pid'=>0])->select();//查出顶级分类的
		}
		$this->assign("mjs",$mjs);
		$this->assign("circles",$circles);
		$this->display();
	}


	public function follow(){
		$this->mustLogin();
		$data = $_POST;
		$data["member_id"] = session("id");
		M("member_follow_circle")->add($data);
			//加入校友圈群聊
		$m = M("mb_member")->find(session('id'));
		$c = M("circle")->find(I("request.circle_id"));
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
		$this->showTrueJson();
	}
	public function unfollow(){
		$data = $_POST;
		$data["member_id"] = session("id");
		M("member_follow_circle")->where($data)->delete();
		$m = M("mb_member")->field('chat_id')->find(session('id'));
		$im = new \Org\Util\Im();
		$g = M("group_manage")->where(['circle_id'=>I("request.circle_id")])->find();	 
		$im->group_remove($m['chat_id'],$g['group_id']);
		$this->showTrueJson();
	}
	public function majorSchool($title,$schoolId){
		$map['school_id']=array('neq',$schoolId);
		$map['zhuanye_title']=array('like',"%$title%");
		$page = I("request.page") ?: 1;
		$re=M('zysc_view')->where($map)->page($page,20)->select();
		$count=M('zysc_view')->where($map)->count();
		$school=M('school');
		$circle=M('circle');
		foreach($re as $k=>$v){
			$logo=$circle->field('logo')->find($v['circle_id']);
			$address=$school->field('address,telephone,website')->find($v['school_id']);
			$circle_sum=M('circle_member')->where('circle_id='.$v['circle_id'])->count();
			$ma['member_id']=session("id");
			$ma['circle_id']=$v['circle_id'];
			$is_follow=M('member_follow_circle')->where($ma)->find();
			$v['logo']=$logo['logo'];
			$v['address']=$address['address'];
			$v['telephone']=$address['telephone'];
			$v['website']=$address['website'];
			$v['circle_sum']=$circle_sum;
			$v['is_follow']=$is_follow;
			$arr[]=$v;
		}
		$this->assign("count",$count);
		$this->assign("arr",$arr);
		$this->assign('title',$title);
		$this->display("major_school");
	}

	public function resetcircle(){
	    $mcs = M("mj_major")->select();
		foreach($mcs as $i => $c){
		   $mc['circle_type'] = 1;
		   $mc['circle_name'] = $c['mj_name'];
		   $mc['logo'] = '/images/test/180.jpg';
		   $mc['add_time'] = date("Y-m-d H:i:s");
		   $mc['object_id'] = $c['id'];
		   $mc['pid'] = $c['pid'];
		   M("circle")->add($mc);
		}
	}

	public function test(){
	  $this->showTrueJson();
	}

	public function insertlogo(){
	   $s = M("circle")->where(['circle_type = 0'])->select();
	   foreach($s as $i => $c){
	      $s[$i]['logo'] = "/images/school/".$c['object_id'].".png";
		  M("circle")->where(['id'=>$c['id']])->save($s[$i]);
	   }	   
	   $this->showTrueJson();
	}

	public function dataDownload($id,$sort=null){
		$title = M('zysc_view')->where('zhuanye_id='.$id)->find();
		$this->assign('id',$id);
		$this->assign('title',$title['zhuanye_title']);
		if($sort==1){
			//时间排序                              <!--这里排序好像有问题，先记录最后来解决-->
			$share=M('share')->order('add_time asc');
		}elseif($sort==2){
			//访问量
			$share=M('share')->order('views desc');
		}else{
			$share=M('share')->order('add_time desc');
		}
		if($_REQUEST['key']){//搜索功能
			$key=$_REQUEST['key'];
			$map['school_id'] = array('eq', $id);
			$map['title']=array('like',"%$key%");
			$re=$share->where($map)->page($_GET['page']?:1,20)->select();
			foreach($re as $k=>$v){
				$arr[]=parse_class($v);
			}
		}else {
			//所有
			$map['school_id'] = array('eq', $id);
			$title = M('zysc_view')->find($id);
			$re =$share->where($map)->select();
			$map['school_id'] = array('eq', $id);
			$re=$share->where($map)->page($_GET['page'] ?: 1, 20)->select();
			$coun = $share->where($map)->count();//分页
			foreach ($re as $k => $v) {
				$arr[] = parse_class($v);
			}
		}
		//资料
		$map['school_id']=array('eq',$id);
		$map['type']=array('eq',0);
		$data=$share->where($map)->select();
		foreach($data as $k=>$v){
			$arr_data[]=parse_class($v);
		}
		//视频
		$map['school_id']=array('eq',$id);
		$map['type']=array('eq',1);
		$video=$share->where($map)->select();
		foreach($video as $k=>$v){
			$arr_video[]=parse_class($v);
		}
		//心得
		$map['school_id']=array('eq',$id);
		$map['type']=array('eq',2);
		$heart=$share->where($map)->select();
		foreach($heart as $k=>$v){
			$arr_heart[]=parse_class($v);
		}
		$this->assign("arr",$arr);//all
		$this->assign("arr_data",$arr_data);//资料
		$this->assign("arr_video",$arr_video);
		$this->assign("arr_heart",$arr_heart);
		$this->assign('coun',$coun);
		$this->display();

	}
	public function blb($schoolId,$year=2011){//切记里参数不能用下划线
		//报录比  这里要传专业的ID
		unset($map);
		$map['school_id']=$schoolId;
		$map['score_type']=1;
		$blb=M('blb_new')->where($map)->order('year asc')->select();
		//初试分数线，score_type=1
		unset($map);
		$map['school_id']=$schoolId;
		$map['score_type']=1;
		$cs=M('blb_new')->where($map)->order('year asc')->select();
		$this->assign("cs",$cs);
		//初试录取分数线，score_type=2
		unset($map);
		$map['school_id']=$schoolId;
		$map['score_type']=2;
		$lq=M('blb_new')->where($map)->order('year asc')->select();
		$this->assign("lq",$lq);
		//end
		//选项卡，通过年份选择
		unset($map);
		$map['school_id']=$schoolId;
		$map['score_type']=1;
		$map['year']=$year;
		$info=M('blb_new')->where($map)->order('year asc')->find();
		$this->assign('year',$year);
		$this->assign('info',$info);
		//end
		$yjfx=explode('，',$blb[0]['yj']);
		$title=M('zysc_view')->where('zhuanye_id='.$schoolId)->find();
		//$title="行政管理专业";//需要从上个页面传过来
		$this->assign('yjfx',$yjfx);//研究方向
		$this->assign("title",$title['zhuanye_title']);
		$this->assign('blb',$blb);
		$this->display();
	}
}