<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   视图管理类($)*/

!defined('DYHB_PATH') && exit;

abstract class View{

	private $_oPar;
	private $_oTemplate;

	public function __construct($oPar=null){
		$this->_oPar=$oPar;
		$this->init__();
	}

	public function init__(){}

	public function parseTemplateFile($sTemplateFile){
		$arrTemplateInfo=array();
		if(empty($sTemplateFile)){
			$sSuffix=$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX'];
			$arrTemplateInfo=array('file'=>($GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/' && MODULE_NAME==='public'?'Public':MODULE_NAME).$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'].ACTION_NAME.$sSuffix);
		}
		elseif(!strpos($sTemplateFile,':\\') && strpos($sTemplateFile,'/')!==0 && !is_file($sTemplateFile)){// D:\phpcondition\......排除绝对路径分析
			if(strpos($sTemplateFile,'@')){// 分析主题
				$arrArray=explode('@',$sTemplateFile);
				$arrTemplateInfo['theme']=ucfirst(strtolower(array_shift($arrArray)));
				$sTemplateFile=array_shift($arrArray);
			}
			$sTemplateFile =str_replace('+',$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'],$sTemplateFile);//模块和操作&分析文件
			$sSuffix=$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX'];
			$arrTemplateInfo['file']=$sTemplateFile.$sSuffix;
		}
		if(!empty($arrTemplateInfo)){
			return $arrTemplateInfo;
		}
		else{
			return $sTemplateFile;
		}
	}

	public function getTemplate(){
		if(is_null($this->_oTemplate)){
			$this->_oTemplate=self::createShareTemplate();
		}
		return $this->_oTemplate;
	}

	public function setTemplate(Template $oTemplate){
		$oOldValue=$this->_oTemplate;
		$this->_oTemplate=$oTemplate;
		return $oOldValue;
	}

	public function getPar(){
		if($this->_oPar===null){
			return null;
		}else{
			return $this->_oPar;
		}
	}

}
