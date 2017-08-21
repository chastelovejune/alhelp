<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class AreaController extends AdminBaseController{
	public function index(){
		$page = I("request.page") ?: 1;
		$areas = M("area")->join('area as parea on parea.id=area.pid')->field("area.*,parea.title as ptitle")->page($page,20)->select();
		$this->areas  = $areas;
		$this->assign_page(M("area")->count());
		$this->display();
	}
}