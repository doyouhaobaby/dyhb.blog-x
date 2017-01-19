<?php
/**

 //  [Websetup!] 图像界面工具
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | websetup 基本配置文件
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

if(is_file(APP_RUNTIME_PATH.'/Temp/AppPathCookie.php')){// 加载项目配置
	$sAppPath=trim(file_get_contents(APP_RUNTIME_PATH.'/Temp/AppPathCookie.php'));
	if(is_file($sAppPath.'/App/Config/Config.php')){
		$arrConfig=array_merge($arrConfig,(array)(include($sAppPath.'/App/Config/Config.php')));
		unset($arrAppConfig);
	}
	if(is_file($sAppPath.'/App/Config/ExtendConfig.php')) {
		foreach((array)(include $sAppPath.'/App/Config/ExtendConfig.php') as $sVal){
			if(is_file($sAppPath.'/App/Config/ExtendConfig/'.ucwords($sVal).'.php')){
					$arrConfig=array_merge($arrConfig,(array)(include $sAppPath.'/App/Config/ExtendConfig/'.ucwords($sVal).'.php'));
					unset($arrExtendConfig);
				}
				else{
					E('Extend config file :'.$sAppPath.'/App/Config/ExtendConfig/'.ucwords($sVal).'.php'.' not exists !','#fff');
				}
		}
	}
}
$arrConfig['URL_MODEL']=0;
$arrConfig['URL_PATHINFO_MODEL']=2;
$arrConfig['URL_HTML_SUFFIX']='';
$arrConfig['SHOW_DB_TIMES']=false;
return $arrConfig;
