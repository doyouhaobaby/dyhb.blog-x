<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   全局控制器($)*/

!defined('DYHB_PATH') && exit;

/** PHP __autoload自动载入 */
function __autoload($sClassName){Package::autoLoad($sClassName);}

class App{

	private static $_oControl;
	private static $_bEmptyModel=false;

	static private function init_(){
		header('DoYouHaoBaby-Framework | '.DYHB_VERSION);
		header("Content-type:text/html;charset=utf-8");
		session_start();// 初始化Session
		LangPackage::addPackageDir(DYHB_PATH.'/Resource/Lang'); // 添加语言包目录
		register_shutdown_function(array('RunTime','runtimeShutdown'));// 注册运行时
		RunTime::registerShutdown(array('RunTime','exitBeforeShutdown'));
		set_error_handler(array('RunTime','errorHandel'));

		Package::import(DYHB_PATH.'/LibPHP');// 载入 DoYouHaoBaby 框架
		//if(!is_file(DYHB_PATH.'/LibCom/Js/Dyhb.package.js')){Package::importJsPackage('Dyhb',false);}

		if(function_exists('date_default_timezone_set')){
			date_default_timezone_set($GLOBALS['_commonConfig_']['TIME_ZONE']);
		}
		if($GLOBALS['_commonConfig_']['START_GZIP'] && function_exists('gz_handler')){
			ob_start('gz_handler');
		}

		$oUrl=new Url();// URL解析
		$oUrl->parseUrl();
		require(APP_PATH.'/App/DoYouHaoBaby.php');// 载入项目初始化文件
		self::checkTemplate();// 检查语言包和模板
		self::checkLanguage();
 		self::constantDefine();// 定义常量

		if($GLOBALS['_commonConfig_']['HTML_CACHE_ON']){// 开启静态缓存
			if(file_exists(APP_PATH.'/App/Config/Html.php')){
				G::C('_HTML_',(array)(include APP_PATH.'/App/Config/Html.php'));
			}
			Html::R();
		}
		Package::import(APP_PATH.'/App/Class');
		return;
	}

	static public function RUN(){
		self::init_();
		self::execute();
		if($GLOBALS['_commonConfig_']['LOG_RECORD']){Log::S();}
		return;
	}

	static public function execute(){
		$sModule=ucfirst(MODULE_NAME)."Controller";
		if(Package::classExists($sModule,false,true)){$oModule=new $sModule();}
		elseif(isset($GLOBALS['_commonConfig_'][strtoupper('_M_'.MODULE_NAME)])){
			$sModule=ucfirst(strtolower($GLOBALS['_commonConfig_'][strtoupper('_M_'.MODULE_NAME)]))."Controller";
			if(!class_exists($sModule,false)){G::E(G::L('%s 的扩展模块%s 不存在','dyhb',null,MODULE_NAME,$sModule));}
			$oModule=new $sModule();
		}else{
			$oModule=self::emptyModule();
		}

		if($oModule===false){$bResult=self::display();}
		self::$_oControl=$oModule;
		if(method_exists( $oModule,'init__')){call_user_func(array($oModule,'init__'));}
		if(defined('DOYOUHAOBABY_TEMPLATE_BASE') && ucfirst(DOYOUHAOBABY_TEMPLATE_BASE)!==TEMPLATE_NAME && is_dir(APP_PATH."/Theme/".ucfirst(DOYOUHAOBABY_TEMPLATE_BASE)."/Public/Php/Lang")){LangPackage::addPackageDir(APP_PATH."/Theme/".ucfirst(DOYOUHAOBABY_TEMPLATE_BASE)."/Public/Php/Lang");}
		if(method_exists( $oModule,'b'.ucfirst(ACTION_NAME).'_')){call_user_func(array($oModule,'b'.ucfirst(ACTION_NAME).'_'));}
		if(method_exists( $oModule,ACTION_NAME)){
			call_user_func(array($oModule,ACTION_NAME));
			$bResult=true;
		}else{
			$bResult=self::emptyAction($oModule);
		}
		if($bResult===false){$bResult=self::display();}
		if(method_exists($oModule,'a'.ucfirst(ACTION_NAME).'_')){call_user_func(array($oModule,'a'.ucwords(ACTION_NAME).'_'));}
	}

	private static function emptyModule(){
		self::$_bEmptyModel=true;
		$sModule=ucfirst(strtolower($GLOBALS['_commonConfig_']['EMPTY_MODULE_NAME']))."Controller";
		if(!Package::classExists($sModule,false,true)){return false;}
		return new $sModule();
	}

	private static function emptyAction($oModule){
		if(method_exists( $oModule,$GLOBALS['_commonConfig_']['EMPTY_ACTION_NAME'])){call_user_func(array($oModule,$GLOBALS['_commonConfig_']['EMPTY_ACTION_NAME']));}
		else{return false;}
	}

	static private function display(){
		$oController=new Controller();
		return $oController->display();
	}

	static private function checkTemplate(){
		if(isset($_GET['t'])){
			$sTemplateSet=ucfirst($_GET['t']);
			G::cookie(APP_NAME.'_template',$sTemplateSet,3600);
		}else{
			if(G::cookie(APP_NAME.'_template')){$sTemplateSet=G::cookie(APP_NAME.'_template');}
			else{
				$sTemplateSet=ucfirst($GLOBALS['_commonConfig_']['TPL_DIR']);
				G::cookie(APP_NAME.'_template',$sTemplateSet,3600);
			}
		}

		define('TEMPLATE_NAME',$sTemplateSet);
		define('TEMPLATE_PATH',APP_PATH.'/Theme/'.TEMPLATE_NAME);
		TemplateHtml::setTemplateDir(APP_PATH.'/Theme/'.TEMPLATE_NAME);

		if(is_dir(APP_PATH."/Theme/{$sTemplateSet}/Public/Php/Lang")){LangPackage::addPackageDir(APP_PATH."/Theme/{$sTemplateSet}/Public/Php/Lang");}
		if($sTemplateSet!='Default' && is_dir(APP_PATH."/Theme/Default/Public/Php/Lang")){LangPackage::addPackageDir(APP_PATH."/Theme/Default/Public/Php/Lang");}
		return;
	}

	static private function checkLanguage(){
		LangPackage::addPackageDir(APP_PATH.'/App/Lang');
		if(isset($_GET['l'])){
			$sLangSet=ucfirst($_GET['l']);
			G::cookie(APP_NAME.'_language',$sLangSet,3600);
		}
		elseif(G::cookie(APP_NAME.'_language')){$sLangSet=G::cookie(APP_NAME.'_language');}
		elseif($GLOBALS['_commonConfig_']['AUTO_ACCEPT_LANGUAGE'] && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrMatches);
			$sLangSet=ucfirst($arrMatches[1]);
			G::cookie(APP_NAME.'_language',$sLangSet,3600);
		}
		else{$sLangSet=ucfirst($GLOBALS['_commonConfig_']['LANG']);}
		define('LANG_NAME',$sLangSet);
		Lang::setCurrentLang($sLangSet);
		define('LANG_PATH',APP_PATH.'/App/Lang/'.LANG_NAME);
		return;
	}

	static private function constantDefine(){
		define('__ENTER__',basename(__APP__));
		define('__FRAMEWORK__',__ROOT__.'/'.G::getRelativePath(__ROOT__.'/'.APP_NAME,WEB_ADMIN_HTTPPATH));// 内部目录入口路径
		define('__LIBCOM__',__FRAMEWORK__.'/LibCom');// LibCom目录路径
		define('__APPPUB__',__ROOT__.'/'.APP_NAME.'/Static');// 项目入口公用静态资源目录(也叫做公共目录)
		define('__THEME__',__ROOT__.'/'.APP_NAME.'/Theme');// 模板目录
		define('__TMPL__',__THEME__.'/'.TEMPLATE_NAME);// 项目资源目录
		define('__PUBLIC__',__ROOT__.'/Public');// 网站公共文件目录
		define('__TMPLPUB__',__TMPL__.'/Public');// 项目公共文件目录
		define('__TMPL_FILE_NAME__',__TMPL__.'/'.($GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/' && MODULE_NAME==='public'?'Public':MODULE_NAME).$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'].ACTION_NAME.$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX']);// 当前文件路径
		define('__TMPL_FILE_PATH__',TEMPLATE_PATH.'/'.($GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/' && MODULE_NAME==='public'?'Public':MODULE_NAME).$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'].ACTION_NAME.$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX']);
	}

	static public function U(){
		return "var _ROOT_='".__ROOT__."',_MODULE_NAME_='".MODULE_NAME."',_ACTION_NAME_='".ACTION_NAME."',_ENTER_ ='".__ENTER__."',_APP_VAR_NAME_='app',_CONTROL_VAR_NAME_='c',_ACTION_VAR_NAME_='a',_URL_HTML_SUFFIX_='".$GLOBALS['_commonConfig_']['URL_HTML_SUFFIX']."';";
	}

	static public function U2(){
		return "var _ROOT_='".__ROOT__."',_MODULE_NAME_='".MODULE_NAME."',_ACTION_NAME_='".ACTION_NAME."',_APP_NAME_ ='".APP_NAME."',_APP_VAR_NAME_='app',_CONTROL_VAR_NAME_='c',_ACTION_VAR_NAME_='a',_URL_HTML_SUFFIX_='".$GLOBALS['_commonConfig_']['URL_HTML_SUFFIX']."';";
	}

}
