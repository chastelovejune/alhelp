<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;
use Think\Think;

class AppsettingController extends ApiBaseController {
      
	  //app����
	  public function appupdate(){
	       $appupdate = M("app_update")->select();
		   $this->success($appupdate);
	  }
}