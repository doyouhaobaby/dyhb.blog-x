<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Wap控制器($)*/

!defined('DYHB_PATH') && exit;

class WapoptionController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以进行Wap的配置。','Wapoption').'</p>'.
				'<p>'.G::L('Wap 将会控制Wap的显示。','Wapoption').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrOptionData=&$this->_arrOptions;
		$this->assign('arrOptions',$arrOptionData);
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
				$this->E(G::L('Wap配置数据更新失败','Wapoption'));
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->S(G::L('Wap配置数据更新成功','Wapoption'));
	}

}
