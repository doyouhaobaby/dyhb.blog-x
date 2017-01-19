<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模板恢复编译($)*/

!defined('DYHB_PATH') && exit;

class TemplateHtmlRevertCompiler{

	static private $_oGlobalInstance;

	protected function __construct(){}

	public function compile(TemplateObj $oObj){
		$sCompiled=$oObj->getCompiled();
		$sCompiled=base64_decode($sCompiled);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function getGlobalInstance(){
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new TemplateHtmlRevertCompiler();
		}
		return self::$_oGlobalInstance;
	}

}
