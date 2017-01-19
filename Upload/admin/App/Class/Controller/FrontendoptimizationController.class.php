<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	前端优化控制器($)*/

!defined('DYHB_PATH') && exit;

class FrontendoptimizationController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以对前端进行设置，更新前端的Javascript和Css代码。','frontendoptimization').'</p>'.
				'<p>'.G::L('这里主要是压缩Javascript和Css代码。','frontendoptimization').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrOptionData=&$this->_arrOptions;
		$this->assign('arrOptions',$arrOptionData);
		$this->display();
	}

	public function update_javascript(){
		$arrOption=G::getGpc('configs','P');
		$this->update_config($arrOption);
		if($arrOption['javascript_dir']=='cache'){
			Cache_Extend::front_javascript();
		}
		$this->S(G::L('前端缓存更新成功'));
	}

	public function update_css(){
		Cache_Extend::front_css();
		$this->S(G::L('前端缓存更新成功'));
	}

	public function update_config($arrOption){
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
	}

}
