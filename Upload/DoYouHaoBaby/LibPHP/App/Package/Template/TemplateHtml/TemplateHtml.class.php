<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   HTML Template界面模版类($)*/

!defined('DYHB_PATH') && exit;

class TemplateHtml extends Template{

	static private $_sTemplateDir;
	private $_arrVariable=array();

	static public function loadDefaultParses(){
		include_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Parser/TemplateHtmlParsers_.php');
		TemplateHtmlGlobalParser::regToParser();// 全局
		TemplateHtmlPhpParser::regToParser();// PHP
		TemplateHtmlCodeParser::regToParser();// 代码
		TemplateHtmlNodeParser::regToParser();// 节点
		TemplateHtmlRevertParser::regToParser(); // 反向
		TemplateHtmlGlobalRevertParser::regToParser(); // 全局反向
	}

	static public function setTemplateDir($sDir){
		A::DDIR($sDir);
		return self::$_sTemplateDir=$sDir;
	}

	static public function findTemplate($arrTemplateFile){
		$sTemplateFile=isset($arrTemplateFile['theme'])?$arrTemplateFile['theme'].'/':'';
		$sTemplateFile.=$arrTemplateFile['file'];
		if(is_file(self::$_sTemplateDir.'/'.$sTemplateFile)){
			return self::$_sTemplateDir.'/'.$sTemplateFile;
		}
		if(defined('DOYOUHAOBABY_TEMPLATE_BASE') && !isset($arrTemplateFile['theme']) && ucfirst(DOYOUHAOBABY_TEMPLATE_BASE)!==TEMPLATE_NAME ){// 依赖模板 兼容性分析
			$sTemplateDir=str_replace('/Theme/'.TEMPLATE_NAME.'/','/Theme/'.ucfirst(DOYOUHAOBABY_TEMPLATE_BASE).'/',self::$_sTemplateDir.'/');
			if(is_file($sTemplateDir.'/'.$sTemplateFile)){
				return $sTemplateDir.'/'.$sTemplateFile;
			}
		}
		if(!isset($arrTemplateFile['theme']) && 'Default'!==TEMPLATE_NAME){// Default模板 兼容性分析
			$sTemplateDir=str_replace('/Theme/'.TEMPLATE_NAME.'/','/Theme/Default/',self::$_sTemplateDir.'/');
			if(is_file($sTemplateDir.'/'.$sTemplateFile)){
				return $sTemplateDir.$sTemplateFile;
			}
		}
		return null;
	}

	public function putInTemplateObj(TemplateObj $oTemplateObj){
		$oTopTemplateObj=$this->TEMPLATE_OBJS[0];
		$oTopTemplateObj->addTemplateObj($oTemplateObj);// 插入
	}

	protected function bParseTemplate_(&$sCompiled){
		$oTopTemplateObj=new TemplateHtmlObj($sCompiled);// 创建顶级TemplateObj
		$oTopTemplateObj->locate($sCompiled,0);
		$this->clearTemplateObj();
		Template::putInTemplateObj($oTopTemplateObj);
	}

	public function includeChildTemplate($sTemplateFile,$sCharset='',$sContentType='',$nVarFlags=''){
		if(!is_file($sTemplateFile)){
			$bExistsFile=false;// 默认主题自动导向
			if(defined('DOYOUHAOBABY_TEMPLATE_BASE') && ucfirst(DOYOUHAOBABY_TEMPLATE_BASE)!==TEMPLATE_NAME){// 依赖主题自动导向
				$sReplacePath='/Theme/'.TEMPLATE_NAME.'/';
				$sTargetPath='/Theme/'.ucfirst(DOYOUHAOBABY_TEMPLATE_BASE).'/';
				$sTemplateFile2=str_replace($sReplacePath,$sTargetPath,$sTemplateFile);
				if(is_file($sTemplateFile2)){
					$sTemplateFile=&$sTemplateFile2;
					$bExistsFile=true;
				}
				else{unset($sTemplateFile2);}
			}
			if($bExistsFile===false && 'Default'!==TEMPLATE_NAME){// 默认主题自动导向
				$sReplacePath='/Theme/'.TEMPLATE_NAME.'/';
				$sTargetPath='/Theme/Default/';
				$sTemplateFile=str_replace($sReplacePath,$sTargetPath,$sTemplateFile);
				if(is_file($sTemplateFile))$bExistsFile=true;
			}
			if($bExistsFile===false){
				E(G::L('警告：对不起子模板：%s不存在','Dyhb',null,$sTemplateFile).'<br>'.G::L('无法找到模板文件<br>%s','Dyhb',null,$sTemplateFile));
				return;
			}
		}
		$this->display($sTemplateFile,$sCharset,$sContentType,true,$nVarFlags);
	}

	public function display($TemplateFile,$sCharset='utf-8',$sContentType='text/html',$bDisplayAtOnce=true){
		$TemplateFileOld=$TemplateFile;
		if(is_string($TemplateFile) && is_file($TemplateFile)){
			$this->_sThemeName=TEMPLATE_NAME;
		}else{
			if(is_array($TemplateFile) && !empty($TemplateFile['theme'])){
				$this->_sThemeName=$TemplateFile['theme'];
			}else{
				$this->_sThemeName=TEMPLATE_NAME;
			}
			$TemplateFile=self::findTemplate($TemplateFile);
		}
		if(!is_file($TemplateFile)){
			$TemplateFile=$TemplateFile?$TemplateFile:$TemplateFileOld;
			G::E(G::L('无法找到模板文件<br>%s','Dyhb',null,is_array($TemplateFile)?implode(' ',$TemplateFile):$TemplateFile));
		}
		$arrVars=&$this->_arrVariable;
		if(is_array($arrVars) and count($arrVars)){
			extract($arrVars,EXTR_PREFIX_SAME,'tpl_');
		}
		$sCompiledPath=$this->getCompiledPath($TemplateFile);// 编译文件路径
		if($this->isCompiledFileExpired($TemplateFile,$sCompiledPath)){// 重新编译
			$this->loadParses();
			$this->compile($TemplateFile,$sCompiledPath);
		}
		$sReturn=null;
		if($bDisplayAtOnce===false){// 需要返回
			ob_start();
			include $sCompiledPath;
			$sReturn=ob_get_contents();
			ob_end_clean();
			return $sReturn;
		}
		else{// 不需要返回
			include $sCompiledPath;
		}
		return $sReturn;
	}

	public function setVar($Name,$Value=null){
		if(is_string($Name)){
			$sOldValue=isset($this->_arrVariable[$Name])?$this->_arrVariable[$Name]:null;
			$this->_arrVariable[$Name]=&$Value;
			return $sOldValue;
		}
		elseif(is_array($Name)){
			foreach($Name as $sName=>&$EachValue){
				$this->setVar($sName,$EachValue);
			}
		}
	}

	public function getVar($sName){
		return isset($this->_arrVariable[$sName])?$this->_arrVariable[$sName]:null;
	}

}
