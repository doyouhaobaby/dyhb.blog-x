<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	消息提示插件管理页面管理程序($)*/

!defined('DYHB_PATH') && exit;

/**
// 经典Hello world
echo 'Hello World';
*/

if(!$_POST['ok']){
	$this->assign('nValue',6);
	Template::in(true);// 演示缓存保持到插件目录中，如果移除则保存在普通模板缓存目录中
	$this->display(DYHB_PATH.'/../Public/Plugin/dyhbblogx_tips/template/admin_test.html');
	Template::in(false);// 还原状态
}else{
	$this->assign('__WaitSecond__',5);
	$this->S(G::dump($_POST,false));
}
