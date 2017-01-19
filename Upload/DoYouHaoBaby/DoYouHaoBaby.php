<?php
/* [DoYouHaoBaby!](C)Dianniu From 2010.
	DoYouHaoBaby 入口文件($)*/

$GLOBALS['_beginTime_']=microtime(TRUE);
error_reporting(0);
define('IS_WIN',DIRECTORY_SEPARATOR=='\\'?1:0);
function E($sMessage){
	exit('DoYouHaoBaby Need Exit: '.$sMessage);
}
if(!defined('APP_PATH')){ define('APP_PATH',dirname($_SERVER['SCRIPT_FILENAME']));}
define('APP_RUNTIME_PATH',APP_PATH.'/App/~Runtime');
if(version_compare(PHP_VERSION,'5.0.0','<')){ die('require PHP > 5.0 !');}
define('DYHB_PATH',str_replace('\\','/',dirname(__FILE__)));// DoYouHaoBaby系统目录定义
if(!defined('APP_NAME')){define('APP_NAME',basename(APP_PATH));}
if(is_file(APP_RUNTIME_PATH.'/~Runtime.inc.php')){
	require APP_RUNTIME_PATH.'/~Runtime.inc.php';
}else{
	require(DYHB_PATH.'/LibPHP/Common/InitRuntime.inc.php');
}
