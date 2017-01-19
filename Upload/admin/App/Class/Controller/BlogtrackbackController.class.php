<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	日志引用控制器($)*/

!defined('DYHB_PATH') && exit;

class BlogtrackbackController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以进行日志引用的配置。','blogtrackback').'</p>'.
				'<p>'.G::L('配置好后，日志引用数据将会在文章内容中显示。','blogtrackback').'</p>'.
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
				$this->E(G::L('日志引用配置数据更新失败'));
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->S(G::L('日志引用配置数据更新成功'));
	}

}
