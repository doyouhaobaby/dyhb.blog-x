<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	消息提示插件前台页面管理程序($)*/

!defined('DYHB_PATH') && exit;

/**
// 经典Hello world
echo 'Hello World';
*/

// 通过类似的URL访问这个
// http://localhost/upload/?c=plugin&a=index&id=dyhbblogx_tips:fronttest

//if(!$_POST['ok']){
	Template::in(true);// 演示缓存保持到插件目录中，如果移除则保存在普通模板缓存目录中
	$this->display(DYHB_PATH.'/../Public/Plugin/dyhbblogx_tips/template/front_test.html');
	Template::in(false);// 还原状态
//}else{
	//$this->assign('__WaitSecond__',5);
	//$this->S(G::dump($_POST,false));
//}
