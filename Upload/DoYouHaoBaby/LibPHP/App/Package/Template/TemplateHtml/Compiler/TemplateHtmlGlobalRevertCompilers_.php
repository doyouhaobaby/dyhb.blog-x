<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模板全局恢复编译($)*/

!defined('DYHB_PATH') && exit;

class TemplateHtmlGlobalRevertCompiler{

	static private $_oGlobalInstance;

	protected function __construct(){}

	public function compile(TemplateObj $oObj){
		$sCompiled=$oObj->getContent();
		$sCompiled=base64_decode($sCompiled);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function getGlobalInstance(){
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new TemplateHtmlGlobalRevertCompiler();
		}
		return self::$_oGlobalInstance;
	}

}
