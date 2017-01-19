<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模板全局编译器($)*/

!defined('DYHB_PATH') && exit;

class TemplateHtmlGlobalCompiler{

	static private $_oGlobalInstance;

	public function __construct(){}

	public function compile(TemplateObj $oObj){
		$sCompiled=TemplateHtmlGlobalParser::encode($oObj->getCompiled());
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function getGlobalInstance(){
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new TemplateHtmlGlobalCompiler();
		}
		return self::$_oGlobalInstance;
	}

}
