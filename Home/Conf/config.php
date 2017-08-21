<?php
return array(
		/* 00-注册命名空间*/
		'AUTOLOAD_NAMESPACE' => array(
				'Common' => COMMON_PATH,//Common 移动根目录
				'Lib'   => COMMON_PATH . 'Lib',//lib 第三方类库
		),
		/* 09-模板引擎设置 */
		'TMPL_ACTION_ERROR'     =>  COMMON_PATH.'View/jump.html', // 默认错误跳转对应的模板文件
		'TMPL_ACTION_SUCCESS'   =>  COMMON_PATH.'View/jump.html', // 默认成功跳转对应的模板文件
		'TMPL_EXCEPTION_FILE'   =>  COMMON_PATH.'View/exception.html',// 异常页面的模板文件
);