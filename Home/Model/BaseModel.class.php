<?php
namespace Home\Model;
use Think\Model;

/*****
 *		基类模型 符合单表的操作
 *		editor	张利彬
 *		date	2015-10-07  13:34:57
 *****/

class BaseModel extends Model
{
	
	/*
	 * $tableName 表名
	 * $where 是查询条件
	 * 查询个数
	 */
	public function  ModelCount($tableName,$where){
		$count=M($tableName)->where($where)->count();
		return $count;
	}
	
	
	/*
	 * 查询所有列表 默认是根据id排序
	 * $tableName 表名
	 * $field 要查询的字段 默认为*
	 * $where 是查询条件
	 * $order 排序字段
	 * 单表查询
	 */
	public function  getModelAllList($tableName,$field='',$where,$order=array('id'=>'desc')){
		if($field=="") return  M($tableName)->where($where)->order($order)->select();
		else 		   return	M($tableName)->getField($field)->where($where)->order($order)->select();
	}
	
	
	/*
	 * 查询列表
	 * $tableName 表名
	 * $field 要查询的字段 默认为*
	 * $where 是查询条件
	 * $order 排序字段
	 * 单表查询 
	 */
	public function  getModelList($tableName,$where,$order=array('id'=>'desc'),$p=1,$size){
		
		//return  M($tableName)->where($where)->order($order)->limit(($p-1)*$size,$size)->select();
		  return  M($tableName)->where($where)->order($order)->page($p,$size)->select();
		//else 		   return	M($tableName)->getField($field)->where($where)->order($order)->page($p,$size)->select();
	}
	
	/*
	 * 单表查询一条记录
	 * $tableName 表名
	 * $field 要查询的字段 默认为*
	 * $where 是查询条件
	 *
	 */
	public function  getOneModelByWhere($tableName,$where){
		 return  M($tableName)->where($where)->find();
		 
	}
	
	public function  getOneModelFieldByWhere($tableName,$field,$where){
		 return	M($tableName)->getField($field)->where($where)->find();
	}
	
	/*
	 * 查询末一个字段
	 * $tableName 表名
	 * $field 要查询的字段 默认为*
	 * $where 是查询条件
	 * $order 排序字段
	 * 单表查询
	 */
	public function  getModelField($tableName,$where,$field){
				$field 	=	M($tableName)->where($where)->getField($field);
		return $field;
	}
	
	
	public function  delModeLogicDeleteTime($tableName,$where){
				$SUCCESS =  M($tableName)->where($where)->setField('delete_time',NULL);
		return $SUCCESS;
	}
    
    /*
	 * 删除模块并插入垃圾箱
     * $tableName 表名
     * $id  主键id，根据主键删除记录时使用
     * $where  特殊条件删除时使用，例如删除时不以主键为条件
	 */         
	public function delModelToDustbin($tableName,$id,$where='')
	{
            $result1 	=  true;
            $result2 	=  true;
            
            //查询一条要删除的数据
            $deleteModel 			=  M($tableName);
            if(empty($where)){
            	
            	$temp_map["id"] = $id;
                $deleteRecord    	=  $deleteModel->where($temp_map)->find();
            }
            else{
                $deleteRecord 		= $deleteModel->where($where)->select();
            }
            
            $deleteJson   =  json_encode($deleteRecord);
		
            //置入垃圾箱后物理删除数据
            $dusbinModel            =  M("t_dustbin");
            $dustbin["id"]      	=  getid();
            $dustbin["table_name"]  =  $tableName;
            $dustbin["json_data"]   =  $deleteJson;
            
            $model       = new Model();
            $model->startTrans();
            
            
            $result1 = $dusbinModel->data($dustbin)->add();
            if(empty($where)){
            	$temp_map["id"] = $id;
                $result2 = $deleteModel->where($temp_map)->delete(); 		
            }
            else{
                $result2 = $deleteModel->where($where)->delete();
            }
            if(!empty($result1) && !empty($result2) ){
            	$model->commit();
                return true;
            }else{
            	$model->rollback();
                return  false;
            }            
        }
	/*
	 * 逻辑删除
	 * $tableName 表名
	 * $where 约束条件
	 * $data 更新内容$data = array('name'=>'ThinkPHP','email'=>'ThinkPHP@gmail.com');
	 */
	public function  delModeLogicByField($tableName,$where,$data){
		 		$SUCCESS  =M($tableName)->where($where)->setField($data);
		return $SUCCESS;
	}
	
	/*
	 * 物理删除
	 * $tableName 表名
	 * $where 约束条件
	 */
	public function  delModePhysical($tableName,$where){
		$SUCCESS = M($tableName)->where($where)->delete();
		return $SUCCESS;
	}
	
	
	/*
	 * 通用添加方法
	 * 
	 */
	public function addModel($tableName,$data){
				$SUCCESS =  M($tableName)->data($data)->add();
		return $SUCCESS;
	}
	
	/*
	 * 通用批量添加方法
	 *
	 */
	public function addBatchModel($tableName,$dataList){
		$SUCCESS =  M($tableName)->addAll($dataList);;
		return $SUCCESS;
	}
 
	/*
	 * 更新全部数据
	 * $tableName 表名
	 * 更新的数据 $data
	 */
	public function editeModel($tableName,$data){
		$SUCCESS =  M($tableName)->data($data)->save();
		return $SUCCESS;
	}

        /*
	 * 根据where条件更新数据
	 * $tableName 表名
	 * $data  更新的数据 
	 */
        public function editModelByWhere($tableName,$data,$where){
                $SUCCESS =  M($tableName)->data($data)->where($where)->save();
		return $SUCCESS;
        }
	/*
	 * 更新全部数据
	 * $tableName 表名
	 * $data  更新的数据
	 * $where 约束条件
	 */
	public function editeModelByFiled($tableName,$data,$where){
		$SUCCESS = M($tableName)->where($where)->setField($data);
		return $SUCCESS;
	}
}

