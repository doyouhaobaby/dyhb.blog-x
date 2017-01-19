<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	登录记录控制器($)*/

!defined('DYHB_PATH') && exit;

class LoginlogController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['loginlog_user']=array('like',"%".G::getGpc('loginlog_user')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('这里为系统登录记录结果，将会实时记录所有会员登录信息。','Loginlog').'</p>'.
				'<p>'.G::L('登录记录有助于提高系统的安全性，防止非法登录。','Loginlog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function clear(){
		$this->display();
	}

	public function clear_all(){
		$oDb=Db::RUN();
		$sSql="TRUNCATE ".PmModel::F()->query()->getTablePrefix()."loginlog";
		$oDb->query($sSql);
		$this->assign('__JumpUrl__',G::U('loginlog/index'));
		$this->S(G::L('清空登录数据成功','Loginlog'));
	}

}
