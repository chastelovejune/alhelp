<?php

namespace Api\Controller;

use Api\Common\ApiBaseController;

class IndexController extends ApiBaseController {
    public function index() {
    	$this->showTrueJson([["id"=>1,"name"=>"abcd"],["id"=>2,"name"=>"bbb"]]);
    }
}