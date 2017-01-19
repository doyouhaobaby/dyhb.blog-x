<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	相关日志控制器($)*/

!defined('DYHB_PATH') && exit;

class RelatedblogController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以进行相关日志的配置。','relatedblog').'</p>'.
				'<p>'.G::L('配置好后，相关日志数据将会在文章内容中显示。','relatedblog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$this->assign('arrOptions',$this->_arrOptions);
		$this->display();
	}

	public function update(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E(G::L('相关日志配置数据更新失败'));
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->S(G::L('相关日志配置数据更新成功'));
	}

}
