<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   初始化基本配置($)*/

!defined('DYHB_PATH') && exit;

if(!is_file(APP_PATH.'/App/~Runtime/Config.php')){
	require(DYHB_PATH.'/LibPHP/Common/AppConfig.inc.php');
}
$GLOBALS['_commonConfig_']=G::C();
