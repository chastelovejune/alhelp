<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
class CommentController extends AdminBaseController{
  protected $table_name = "comment";
  public function index(){
    $list = M("comment")->join('left join mb_member on comment.member_id=mb_member.id')->page(I('request.page'),20)->select();
    $this->assign_list($list);
    $this->assign_page("comment");
    $this->display();
  }	
}
