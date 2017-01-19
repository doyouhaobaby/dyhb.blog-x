<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   后台首页显示($)*/

!defined('DYHB_PATH') && exit;

class IndexController extends InitController{

	public function index(){
		if($GLOBALS['___login___']===false){
			UserModel::M()->clearThisCookie();// 清理COOKIE
			$this->assign('__JumpUrl__',G::U('public/login'));
			$this->E(G::L('你没有登录！'));
		}
		$this->assign('sDoyouhaobabyXBlogAdmin',G::L('DYHB.BLOG X! 后台管理'));
		$this->display();
	}

}
