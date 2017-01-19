<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   插件控制器($) */

!defined('DYHB_PATH') && exit;

class PluginController extends CommonController{

	public function index(){
		define('CURSCRIPT','plugin');
		define('IS_PLUGIN',TRUE);
		$sIdentifier=G::getGpc('id','G');
		$sModule=G::getGpc('module','G');
		$sModule=$sModule!==NULL?$sModule:$sIdentifier;
		$sMnid='plugin_'.$sIdentifier.'_'.$sModule;
		$arrPluginModule=array('dir'=>preg_match("/^[a-z]+[a-z0-9_]*$/i",$sIdentifier)?$sIdentifier:'');
		$arrActivePlugins=Model::C('plugin_active','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));// plugin menu
		if($arrActivePlugins===false){
			Cache_Extend::get_cachedata_plugin();
			$arrActivePlugins=Model::C('plugin_active','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}
		if(empty($sIdentifier) || !preg_match("/^[a-z0-9_\-]+$/i",$sModule) || !in_array($sIdentifier,$arrActivePlugins)){
			$this->E(G::L('插件不存在！'));
		}elseif(@!file_exists(DYHB_PATH.($sModFile='/../Public/Plugin/'.$arrPluginModule['dir'].'/'.$sModule.'.inc.php'))) {
			$this->E(G::L('插件模块%s不存在！','app',null,'{DYHB_PATH}'.$sModFile));
		}
		include DYHB_PATH.$sModFile;
	}

}
