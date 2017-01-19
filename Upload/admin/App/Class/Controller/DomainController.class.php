<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	域名管理控制器($)*/

!defined('DYHB_PATH') && exit;

class DomainController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['domain_name']=array('like',"%".G::getGpc('domain_name')."%");
	}

	public function bInput_change_ajax_(){
		$sInputAjaxField=G::getGpc('input_ajax_field');
		$sInputAjaxVal=G::getGpc('input_ajax_val');
		$oCheck=Check::RUN();
		if($sInputAjaxField=='domain_ip' && !Check::C($sInputAjaxVal,'ip')){
			$this->E(G::L('IP格式不正确！','app'));
		}
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('使用域名绑定可以更好地规划你的站点。','domain').'</p>'.
				'<p>'.G::L('你可以直接点击域名解析项，将会生产表单，你可以编辑。','domain').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('你可以在本页添加域名解析支持。','domain').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

}
