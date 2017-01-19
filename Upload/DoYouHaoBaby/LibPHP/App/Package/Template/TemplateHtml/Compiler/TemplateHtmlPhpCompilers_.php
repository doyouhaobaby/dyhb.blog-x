<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模板全局Php编译器($)*/

!defined('DYHB_PATH') && exit;

class TemplateHtmlPhpCompiler{

	static private $_oGlobalInstance;

	public function __construct(){}

	public function compile(TemplateObj $oObj){
		$sCompiled = $oObj->getCompiled();// 获取编译内容
		if($GLOBALS['_commonConfig_']['PHP_OFF']===false){// 是否允许模板中存在php代码
			$arrPreg[]='/<\?(=|php|)(.+?)\?>/is';
			$arrReplace[]='&lt;?\\1\\2?&gt;';
			$sCompiled=preg_replace($arrPreg,$arrReplace,$sCompiled);
		}
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function getGlobalInstance(){
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new TemplateHtmlPhpCompiler();
		}
		return self::$_oGlobalInstance;
	}

}
