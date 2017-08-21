<?php
namespace Home\Controller;
use Home\Common\HomeBaseController;
use Home\Common\Alhelp_Constant;
require "../lib/curd.trail.php";
class DemandController extends HomeBaseController {
	var $table_name = "demand";
	use \curd;

	public function buy($id){
		$demand = M("demand")->find($id);
		//暂时不考虑订单问题
		$fee = $demand['advance_payment'];
		//go_assert($fee>0,'托付费用大于0才需要购买');
		if (IS_POST) {
			$pay_type = I("request.pay_type");
			if ($pay_type == 0) {//微信
				$out_trade_no = time()."_demand_".$id;
    		    $code = go_wxPay($out_trade_no,$fee,$id);
	            $this->showTrueJson(['code'=>$code,'id'=>$id,'type'=>'demand']);
			}elseif ($pay_type==1) {//支付宝
				$html_text = go_alipay(U('/home/demand/alipay'),
					pay_id($id),
					$demand['description'],$fee);
				$this->show($html_text);
			}else{//余额
				M()->execute("update demand left join mb_member on demand.member_id =mb_member.id set balance=balance-demand.advance_payment,is_payed=1 where demand_id=$id;");
				$this->showTrueJson();
			}
		}
		$this->demand = $demand;
		$this->display();
	}


	public function alipay($out_trade_no){
		$id = get_pay_id($out_trade_no);
		M("demand")->where(['demand_id'=>$id])->save(['is_payed'=>1]);
		$this->redirect('/home/demand/detail',['id'=>$id]);
	}
	public function add()
	{
		$this->mustLogin();
		if (IS_POST) {
			if(cookie("phoneCode")) {
				$code = cookie("phoneCode");
				if ($code != I("request.phone_verified")) {
					$this->showFalseJson("手机验证码错误！");
				}else{
					//验证手机成功，修改字段状态
					unset($data);
					$data['$phone_verified']=1;
					M("mb_member")->where(['id'=>session("id")])->save($data);
				}
			}
			M()->startTrans();
			if ($_POST['school_id']>0) {
				$school_id = M("zysc_view")->getFieldByZhuanyeId($_POST['school_id'],'school_id');
				D("CircleMember")->addBySchoolId($school_id);	
			}
			
			$d = $_POST;
			$d["member_id"] = session("id");
			$d['member_name'] = M("mb_member")->where(['id'=>session("id")])->getField("nickname");
			$cid = M("demand")->add($d);

			M()->commit();
			$this->showTrueJson($cid);
		}
		//获取用户是否验证手机
		$phone_verified= M("mb_member")->where(['id'=>session("id")])->getField("phone_verified");
		$this->assign('phone_verified',$phone_verified);
		$this->display();
	}

	public function detail()
	{   
		$demand_id=I('id');
		$demand=D('Demand')->relation(true)->find($demand_id);
		$owner=$demand['Member'];
		$this->assign('owner',$owner);
		$demand = parse_class($demand);
		$this->assign('demand',$demand);
		if (IS_AJAX) {
			$this->showTrueJson($demand);		
		}else{
			$this->display();
		}
	}
	public function lists()
	{
		$demands = M("demand")->join("LEFT JOIN member ON demand.member_id=member.id")->join("LEFT JOIN zysc_view ON zysc_view.zhuanye_id=demand.school_id")->field("demand.*,avatar,sign,follow_num,fans_num,share_num,collection_num");
		{//搜索
			$where = array();
	     	$keywords  =I("request.keywords");
	     	if ($keywords) {
	     		$where['_string'] = "(detail LIKE '%{$keywords}%') OR (demand.description LIKE '%{$keywords}%')";
	     	}
	     	$demand_type = I("request.demand_type");
	     	if (strlen($demand_type)>0) {
	     		$where["demand_type"] = $demand_type;
	     	}
	     	$where = array_merge($where,parent::where3type());
	 		$demands  =$demands->where($where);
	 		$this->where = $where;
		}
		{//排序
			$order = parent::sort(null,'demand.demand_id');
			$order = "is_payed desc,advance_payment desc,".$order;
	     	$demands = $demands->order($order);
		}
		{//分页
			{
				$copy = clone $demands;
				$this->count = $copy->count();
				unset($copy);
			}
			$demands = $demands->page($_GET['page']?:1,10);
		}
     	$demands = $demands->select();	
     	$this->demands = $demands;	
		$this->display();
	}
	public function addmajor(){
		if(IS_POST){
			$data['content']=I('content');
			$data['time_create']=time();
			$re=M('system_message')->add($data);
			if($re){
				$this->ajaxReturn($re);
		}
		}
	}
}
