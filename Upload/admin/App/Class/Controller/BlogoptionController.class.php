<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	博客配置处理控制器($)*/

!defined('DYHB_PATH') && exit;

class BlogoptionController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function index(){
		$arrOptionData=&$this->_arrOptions;
		$arrOptionData['blogfile_thumb_width_heigth']=implode('|',unserialize($arrOptionData['blogfile_thumb_width_heigth']));
		$arrOptionData['all_allowed_upload_type']=implode('|',unserialize($arrOptionData['all_allowed_upload_type']));
		$arrOptionData['not_limit_leech_domail']=implode('|',unserialize($arrOptionData['not_limit_leech_domail']));

		$arrWaterMarkPosition=array(0=>G::L('随机添加'),1=>G::L('顶端居左'),2=>G::L('顶端居中'),3=>G::L('顶端居右'),4=>G::L('中部居左'),5=>G::L('中部居中'),6=>G::L('中部居右'),7=>G::L('底端居左'),8=>G::L('底端居中'),9=>G::L('底端居右'));
		$this->assign('arrWaterMarkPosition',$arrWaterMarkPosition);

		$arrUrlModels=array(
			array(
				'name'=>G::L('普通模式'),
				'value'=>0,
			),
			array(
				'name'=>G::L('PATHINFO模式'),
				'value'=>1,
			),
			array(
				'name'=>G::L('REWRITE模式'),
				'value'=>2,
			),
			array(
				'name'=>G::L('兼容模式'),
				'value'=>3,
			),
		);
		$this->assign('arrUrlModels',$arrUrlModels);
		$this->assign('arrFrontLangs',E::listDir(DYHB_PATH.'/../blog/App/Lang'));
		$this->assign('arrAdminLangs',E::listDir(DYHB_PATH.'/../admin/App/Lang'));
		$this->assign('arrOptions',$arrOptionData);
		$this->display();
	}

	public function update_config(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			if(in_array($sKey,array('not_limit_leech_domail','all_allowed_upload_type','blogfile_thumb_width_heigth'))){
				$val=serialize(explode('|',$val));
			}
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
		Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		@unlink(DYHB_PATH.'/../blog/App/~Runtime/Config.php');
		$this->S(G::L('配置文件更新成功了！'));
	}
	public function _index_get_admin_help_description(){
		return '<p>'.G::L('博客配置系统的所有配置信息，你在这里都可以修改。系统预留了大量的开关，你可以更具实际需要开启相应的功能。','blogoption').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function clear_html_dir(){
		$sDeleteDir=DYHB_PATH.'/../blog/App/Html';
		if(is_dir($sDeleteDir)){@rmdir($sDeleteDir);}
		if(!is_dir($sDeleteDir)&&!G::makeDir($sDeleteDir)){$this->S(G::L('创建缓存缓存目录%s失败','Blog',null,$sDeleteDir));}
		$this->S(G::L('清理缓存目录成功','Blog'));
	}

}
