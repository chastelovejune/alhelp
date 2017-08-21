<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class AuditionController extends AdminBaseController{
	function index(){
		$auditions = D("Audition")->relation(true)->page(I('request.page'), 20)->select();
		//dump($auditions);
		$this->auditions = $auditions;
		$this->assign_page("order");
		$this->display();
	}
	function delete()
	{
		$id=I('id');
		//因为数据库默认为0,0改为上架状态，2改为逻辑删除   (这里为了保持前台的一致。设置3为逻辑删除)
		//["申请中..","已同意","删除","取消","不通过"]
		if(M('audition')->where("id=".$id)->setField('status',2)){
			$this->success("试听被逻辑删除了");
		}else{
			$this->success("已经删除了");
		}
	}
}