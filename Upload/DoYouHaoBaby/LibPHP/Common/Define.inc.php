<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   系统定义文件($)*/

!defined('DYHB_PATH') && exit;

if(!is_file( APP_RUNTIME_PATH.'/~Runtime.inc.lock') ){require(DYHB_PATH.'/LibPHP/Common/InitRuntime.inc.php');}

/** PHP魔术方法  */
if(version_compare(PHP_VERSION,'6.0.0','<')){
	@set_magic_quotes_runtime(0);
	define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?TRUE:FALSE);
}
define('CURRENT_TIMESTAMP',time());/** CURRENT_TIMESTAMP 定义为当前时间，减少框架调用 time()的次数 */
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));/** 定义PHP是否开启内存函数  */
define('IS_CGI',substr(PHP_SAPI,0,3)=='cgi'?1:0);
define('IS_CLI',PHP_SAPI=='cli'?1:0);/** 是否为命令行 */
if(!IS_CLI){
	if(!defined('_PHP_FILE_')){/** PHP 文件 */
		if(IS_CGI){
			$arrTemp=explode('.php',$_SERVER["PHP_SELF"]);// CGI/FASTCGI模式下
			define('_PHP_FILE_',rtrim(str_replace($_SERVER["HTTP_HOST"],'',$arrTemp[0].'.php'),'/'));
		}
		else{define('_PHP_FILE_',rtrim($_SERVER["SCRIPT_NAME"],'/'));}
	}
	if(!defined('__ROOT__')){/** 网站URL根目录 */
		if(strtoupper(APP_NAME)==strtoupper(basename(dirname(_PHP_FILE_)))){$sRoot=dirname(dirname(_PHP_FILE_));}
		else{$sRoot=dirname(_PHP_FILE_);}
		define('__ROOT__',(($sRoot=='/' || $sRoot=='\\')? '':$sRoot));
	}
	/** 支持的URL模式 */
	define('URL_COMMON',0);// 普通模式
	define('URL_PATHINFO',1);// PATHINFO模式
	define('URL_REWRITE',2);// REWRITE模式
	define('URL_COMPAT',3);// 兼容模式

	/** 网站相对根目录 */
	if(!isset($_SERVER['DOCUMENT_ROOT']) OR (isset($_SERVER['PATH_TRANSLATED']) AND !eregi(str_replace('\\','/', dirname($_SERVER['DOCUMENT_ROOT'])),str_replace('\\','/', dirname($_SERVER['PATH_TRANSLATED']))))){
		if(strtoupper(APP_NAME)== strtoupper(basename(dirname(_PHP_FILE_)))){$nLength=strlen(_PHP_FILE_)-strlen(APP_NAME)-1;}
		else{$nLength=strlen(_PHP_FILE_)-1;}
		$_SERVER['DOCUMENT_ROOT']=substr(preg_replace('/\+/','/',$_SERVER['PATH_TRANSLATED']),0, $nLength);
	}
	$_SERVER['DOCUMENT_ROOT']=rtrim($_SERVER['DOCUMENT_ROOT'],'\\/');
	if($_SERVER['DOCUMENT_ROOT']===DYHB_PATH){define('WEB_ADMIN_HTTPPATH','/');}
	else{define('WEB_ADMIN_HTTPPATH',substr(DYHB_PATH,strlen($_SERVER['DOCUMENT_ROOT'])));}
}
define('DYHB_VERSION','1.6_beta');
if(MEMORY_LIMIT_ON){$GLOBALS['_startUseMems_']=memory_get_usage();}
