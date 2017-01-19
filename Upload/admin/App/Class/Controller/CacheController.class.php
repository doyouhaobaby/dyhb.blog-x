<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	缓存服务控制器($)*/

!defined('DYHB_PATH') && exit;

class CacheController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('缓存技术','cache').'</p>'.
				'<p>'.G::L('缓存技术最大的目录是减少数据库、IO等开销。将常用的数据以文本或者其它方式缓存起来，下次运行时直接从缓存中读取数据。','cache').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _admin_get_admin_help_description(){
		return $this->_index_get_admin_help_description();
	}

	public function _show_get_admin_help_description(){
		return '<p>'.G::L('通过缓存查看功能，你可以清楚地看到缓存中的数据。','cache').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _admin_show_get_admin_help_description(){
		return $this->_show_get_admin_help_description();
	}

	public function index(){
		$arrOptions=array('global_option',G::L('站点配置'));
		$sGlobalOptionCachePath =DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache/~@'.'global_option'.'.php';
		$this->assign('arrGlobalOption',$this->get_a_cache_file_info($sGlobalOptionCachePath,$arrOptions));
		$arrFrontOptions=array(
			array('widget_yearhotpost',G::L('年度排行日志挂件')),
			array('widget_yearcommentpost',G::L('年度最受欢迎排行日志挂件')),
			array('widget_uploadcategory',G::L('附件分类挂件')),
			array('widget_theme',G::L('主题切换挂件')),
			array('widget_taotao',G::L('心情挂件')),
			array('widget_static',G::L('博客统计挂件')),
			array('widget_recentpost',G::L('最新日志挂件')),
			array('widget_randpost',G::L('随机日志挂件')),
			array('widget_pagepost',G::L('页面挂件')),
			array('widget_monthhotpost',G::L('当月排行日志挂件')),
			array('widget_monthcommentpost',G::L('当月最受欢迎排行日志挂件')),
			array('widget_link',G::L('友情衔接挂件')),
			array('widget_lang',G::L('语言包切换挂件')),
			array('widget_hottag',G::L('热门标签挂件')),
			array('widget_hotpost',G::L('热门日志挂件')),
			array('widget_guestbook',G::L('留言板挂件')),
			array('widget_dayhotpost',G::L('今日排行日志挂件')),
			array('widget_daycommentpost',G::L('今日最受欢迎排行日志挂件')),
			array('widget_comment',G::L('评论挂件')),
			array('widget_commentpost',G::L('评论最多日志挂件')),
			array('widget_category',G::L('分类挂件')),
			array('widget_calendar',G::L('日历挂件')),
			array('widget_archive',G::L('日志归档挂件')),
			array('widget_recentimage',G::L('最新图片')),
		);
		$arrOptionDatas=array();
		foreach($arrFrontOptions as $arrFrontOption){
			$sFrontOptionCachePath =DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache/~@'.$arrFrontOption[0].'.php';
			$arrOptionDatas[]=$this->get_a_cache_file_info($sFrontOptionCachePath,$arrFrontOption);
		}
		$this->assign('arrOptionDatas',$arrOptionDatas);
		$this->assign_system_meta('blog');
		$this->assign_syetem_config('blog');
		$this->display();
	}

	public function assign_syetem_config($sApp='admin'){
		$arrConfigOptions=array('config',G::L('系统配置缓存'));
		$sConfigFile=DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Config.php';
		$this->assign('arrConfigs',$this->get_a_cache_file_info($sConfigFile,$arrConfigOptions));
	}

	public function assign_system_meta($sApp='admin'){
		$arrAllTables=$this->get_all_tables();
		$arrTableOptionDatas=array();
		foreach($arrAllTables as $sTable){
			$sMetaOptionCachePath =DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Data/DbMeta'.'/~@'.$sTable.'.php';
			$arrTableOptionDatas[]=$this->get_a_cache_file_info($sMetaOptionCachePath,array($sTable,G::L('数据库表字段信息')));
		}
		$this->assign('arrTableOptionDatas',$arrTableOptionDatas);
	}

	public function get_all_tables(){
		$oDb=Db::RUN();
		$arrTables=$oDb->getConnect()->getCol("SHOW TABLES LIKE '".$GLOBALS['_commonConfig_']['DB_PREFIX']."%'");
		return $arrTables;
	}

	public function update(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				Cache_Extend::config('blog');
				break;
			case 'global_option':
				Cache_Extend::global_option('blog');
				break;
			default:
				if($sExtend=='meta'){
					call_user_func(array('Cache_Extend','meta_cache'),array($sType,'blog'));
				}
				else{
					call_user_func(array('Cache_Extend','front_'.$sType));
				}
				break;
		}
		$this->S(G::L('缓存文件已经被成功重建了，数据已经更新！'));
	}

	public function get_a_cache_file_info($sFilePath,$arrOptions){
		$arrInfo=array();
		if(is_file($sFilePath)){
			$arrInfo['name']=$arrOptions[0];
			$arrInfo['description']=$arrOptions[1];
			$arrInfo['size']=numBitunit(filesize($sFilePath));
			$arrInfo['mtime']=date('Y-m-d H:i',@filemtime($sFilePath));
		}
		else{
			$arrInfo['name']=$arrOptions[0];
			$arrInfo['description']=$arrOptions[1];
			$arrInfo['size']=G::L('未知');
			$arrInfo['mtime']=G::L('未知');
		}
		return $arrInfo;
	}

	public function show(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				$sConfigFile=DYHB_PATH.'/../blog/App/~Runtime/Config.php';
				if(is_file($sConfigFile)){
					$arrConfigOption=(array)(include $sConfigFile);
				}
				else{
					$arrConfigOption=array();
				}
				$this->assign('arrConfigOption',$arrConfigOption);
				$this->display('cache+config');
				break;
			case 'global_option':
				$this->assign('arrGlobalOption',Model::C('global_option'));
				$this->display('cache+global_option');
				break;
			default:
				if($sExtend=='meta'){
					$arrOptionMetaDatas=Model::C($sType,'',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbMeta'));
					$this->assign('arrOptionMetaDatas',$arrOptionMetaDatas);
					$this->assign('arrHeaderItems',array_keys(reset($arrOptionMetaDatas[0])));
					$this->display('cache+meta');
				}
				else{
					$arrOptionDatas=Model::C($sType,'',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
					if(!empty($arrOptionDatas) && !in_array($sType,array('widget_theme','widget_lang','widget_static','widget_calendar','widget_archive'))){
						$this->assign('arrHeaderItems',array_keys(reset($arrOptionDatas)));
					}
					$this->assign('arrOptionDatas',$arrOptionDatas);
					if(in_array($sType,array('widget_theme','widget_lang','widget_static','widget_calendar','widget_archive'))){
						$this->display('cache+widget');
					}
					else{
						$this->display();
					}
				}
				break;
		}
	}

	public function admin(){
		$arrOptions=array('global_option',G::L('站点配置'));
		$sGlobalOptionCachePath =DYHB_PATH.'/../admin/App/~Runtime/Data/DbCache/~@'.'global_option'.'.php';
		$this->assign('arrGlobalOption',$this->get_a_cache_file_info($sGlobalOptionCachePath,$arrOptions));
		$arrUserData=UserModel::M()->userData();
		$arrAdminOptions=array(
			array('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])),G::L('权限缓存')),
			array('menu_top_cache_'.$arrUserData['user_id'],G::L('顶部菜单缓存')),
			array('_Menu_'.$GLOBALS['_commonConfig_']['USER_AUTH_KEY'].'_'.$arrUserData['user_id'],G::L('边栏菜单缓存')),
		);
		$arrOptionDatas=array();
		foreach($arrAdminOptions as $arrAdminOption){
			$sAdminOptionCachePath=DYHB_PATH.'/../admin/App/~Runtime/Data/'.($arrAdminOption[0]=='access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']))? 'AccessList':'MenuList').'/~@'.md5($arrAdminOption[0]).'.php';
			$arrOptionDatas[]=$this->get_a_cache_file_info($sAdminOptionCachePath,$arrAdminOption);
		}
		$this->assign('arrOptionDatas',$arrOptionDatas);
		$this->assign_system_meta('admin');
		$this->assign_syetem_config('admin');
		$this->display();
	}

	public function wap(){
		$arrOptions=array('global_option',G::L('站点配置'));
		$sGlobalOptionCachePath =DYHB_PATH.'/../wap/App/~Runtime/Data/DbCache/~@'.'global_option'.'.php';
		$this->assign('arrGlobalOption',$this->get_a_cache_file_info($sGlobalOptionCachePath,$arrOptions));
		$this->assign_system_meta('wap');
		$this->assign_syetem_config('wap');
		$this->display();
	}

	public function admin_update(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				Cache_Extend::config('admin');
				break;
			case 'global_option':
				Cache_Extend::global_option();
				break;
			default:
				if($sExtend=='meta'){
					call_user_func(array('Cache_Extend','meta_cache'),array($sType,'admin'));
				}
				break;
		}
		$this->S(G::L('缓存文件已经被成功重建了，数据已经更新！'));
	}

	public function wap_update(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				Cache_Extend::config('wap');
				break;
			case 'global_option':
				Cache_Extend::global_option('wap');
				break;
			default:
				if($sExtend=='meta')
					call_user_func(array('Cache_Extend','meta_cache'),array($sType,'wap'));
				break;
		}
		$this->S(G::L('缓存文件已经被成功重建了，数据已经更新！'));
	}

	public function admin_show(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				$sConfigFile=DYHB_PATH.'/../admin/App/~Runtime/Config.php';
				if(is_file($sConfigFile)){
					$arrConfigOption=(array)(include $sConfigFile);
				}
				else{
					$arrConfigOption=array();
				}
				$this->assign('arrConfigOption',$arrConfigOption);
				$this->display('cache+config');
				break;
			case 'global_option':
				$this->assign('arrGlobalOption',Model::C('global_option'));
				$this->display('cache+global_option');
				break;
			default:
				if($sExtend=='meta'){
					$arrOptionMetaDatas=Model::C($sType,'',array('cache_path'=>DYHB_PATH.'/../admin/App/~Runtime/Data/DbMeta'));
					$this->assign('arrOptionMetaDatas',$arrOptionMetaDatas);
					$this->assign('arrHeaderItems',array_keys(reset($arrOptionMetaDatas[0])));
					$this->display('cache+meta');
				}
				break;
		}
	}

	public function wap_show(){
		$sType=G::getGpc('type','G');
		$sExtend=G::getGpc('extend','G');
		switch($sType){
			case 'config':
				$sConfigFile=DYHB_PATH.'/../wap/App/~Runtime/Config.php';
				if(is_file($sConfigFile)){
					$arrConfigOption=(array)(include $sConfigFile);
				}
				else{
					$arrConfigOption=array();
				}
				$this->assign('arrConfigOption',$arrConfigOption);
				$this->display('cache+config');
				break;
			case 'global_option':
				$this->assign('arrGlobalOption',Model::C('global_option'));
				$this->display('cache+global_option');
				break;
			default:
				if($sExtend=='meta'){
					$arrOptionMetaDatas=Model::C($sType,'',array('cache_path'=>DYHB_PATH.'/../wap/App/~Runtime/Data/DbMeta'));
					$this->assign('arrOptionMetaDatas',$arrOptionMetaDatas);
					$this->assign('arrHeaderItems',array_keys(reset($arrOptionMetaDatas[0])));
					$this->display('cache+meta');
				}
				break;
		}
	}

	public function widget(){
		$arrWidgets=G::getGpc('widget','P');
		$sExtend=G::getGpc('extend','G');
		foreach($arrWidgets as $sWidget){
			call_user_func(array('Cache_Extend','front_'.$sWidget));
		}
		$this->S(G::L('批量更新Widget 缓存成功了！'));
	}

	public function meta(){
		$arrMetas=G::getGpc('meta','P');
		$sExtend=G::getGpc('extend','G');
		foreach($arrMetas as $sMeta){
			call_user_func(array('Cache_Extend','meta_cache'),array($sMeta,$sExtend));
		}
		$this->S(G::L('批量更新字段缓存成功了！'));
	}

}
