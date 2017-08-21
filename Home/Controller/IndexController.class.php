<?php
namespace Home\Controller;


/*
 * 登录页控制器
 * coder : leebin
 * createTime:2016-05-13
 */

class IndexController extends \Home\Common\HomeBaseController {
	
    public function index(){

    	$recommendSs = M("mb_member")->where(["recommend_as_student"=>1]);
        if (session("id")) {
            $recommendSs->field('mb_member.*,(select follow.id from follow where follower='.session("id").' and befollowed = mb_member.id )as isFed');
        }
        $recommendSs = $recommendSs->select();
    	$recommendTs = M("mb_member")->where(["recommend_as_teacher"=>1]);
        if (session("id")) {
            $sql = M("follow")->where(["follower"=>session("id"),"befollowed = mb_member.id"])->field("id")->buildSql();
            $recommendTs=$recommendTs->field("mb_member.*,$sql as isFed");
        }
    	$recommendTs = $recommendTs->select();
    	$this->assign("students",$recommendSs);
    	$this->assign("teachers",$recommendTs);
      //  $sliders = M("slide")->where(["type"=>0])->select();
     //   $this->assign("sliders",$sliders);

	   $latest_demands=M('demand')->join("LEFT JOIN mb_member ON mb_member.id=member_id")->field("demand.*,avatar")->where(array("demand.status"=>'0'))->order("demand.add_time desc")->limit(5)->select();
	
		foreach($latest_demands as $key=>$value)
		{	
			//var_dump($value);
            $value = parse_class($value);
            $latest_demands[$key]  =$value;
		}
        $this->assign("latest_demands",$latest_demands);
		
		$latest_services=M('service')->join("LEFT JOIN mb_member ON mb_member.id=member_id")->where(array("service.status"=>'0'))->order("service.add_time desc")->field("service.*,avatar")->limit(5)->select();
		foreach($latest_services as $key=>$value)
		{
            $value = parse_class($value);
            $latest_services[$key]  =$value;
		}
        $this->assign("latest_services",$latest_services);

        $classes = M("open_class")->limit(5)->select();
        foreach ($classes as $key => $value) {
            $value = parse_class($value);
            $classes[$key]  =$value;
        }
        $this->assign("classes",$classes);
        $types=M('information_type')->where("type=6")->limit(6)->select();//先取出大分类
        foreach($types as $k=>$v){      //循环取出子分类和标题信息
            if($v['pid']!=0) {
                $info = M("information")->where(['type' => 6, 'type2' => $v['id']])->limit(3)->select();
                $types[$k]['info']=$info;
            }else{
                unset($types[$k]);
            }
        }
        $this->assign("types",$types);
        //取出轮播图
        $lunbotu=M('lunbotu')->where("status=0")->order("sort asc")->select();
        $this->assign("lunbotu", $lunbotu);
        $this->display();
    }
    //地区搜索进入圈子
    public function search(){
        if(IS_POST){
           $Select1_school= $_POST['Select1_school'];
            $circle=M('circle')->field('id')->where('object_id='.$Select1_school)->find();
            redirect(U('home/circle/memberPost',['id'=>$circle['id']]));
        }
    }
}
