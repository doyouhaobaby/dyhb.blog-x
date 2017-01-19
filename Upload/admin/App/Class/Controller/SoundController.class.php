<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	短消息配置处理控制器($)*/

!defined('DYHB_PATH') && exit;

class SoundController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function index(){
		$this->assign('arrOptions',$this->_arrOptions);
		$this->display();
	}

	public function update_config(){
		$arrOption=G::getGpc('configs','P');
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
		$this->S(G::L('配置文件更新成功了！'));
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('系统短消息提示音，这里可以设置。','sound').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

}
