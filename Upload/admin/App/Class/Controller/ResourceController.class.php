<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	资源提供商控制器($)*/

!defined('DYHB_PATH') && exit;

class ResourceController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('扩展中心将显示由资源提供商提供各类 DYHB.BLOG_X! 插件、风格等扩展资源。','resource').'</p>'.
				'<p>'.'This is just wait to do!'.'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$this->display();
	}

}
