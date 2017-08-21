<?php
namespace Admin\Controller;
use Admin\Common\AdminBaseController;
//use Org\Util\Date;

class LunbotuController extends AdminBaseController{
    protected $table_name = "Lunbotu";
  public function index(){
      $re=M('lunbotu')->order("sort asc")->select();
      $this->assign("re",$re);
    $this->display();
  }
    public function add(){
        if(IS_POST) {
            $image = upload("image");
            $d=$_POST;
            $d["img"] = $image;
            $d['title']=$_POST['title'];
            $d['link']=$_POST['link'];
            $d['add_time']=date('Y-m-d h:i:s',time());
            if(M('lunbotu')->add($d)){
                $this->success("添加成功");
            }
        }
        $this->display();
    }
    public function stop($id){
        if(M('lunbotu')->where("id=".$id)->setField("status",1)){
            $this->success("禁用成功");
        }
    }
    public function start($id){
        if(M('lunbotu')->where("id=".$id)->setField("status",0)){
            $this->success("启用成功");
        }
    }
    public function sort($sort,$id){
        if(M('lunbotu')->where("id=".$id)->setField("sort",$sort)){
            $this->showTrueJson("设置成功");
        }

    }
}
