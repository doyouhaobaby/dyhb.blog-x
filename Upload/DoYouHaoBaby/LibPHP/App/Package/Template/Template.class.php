<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模板处理类($)*/

!defined('DYHB_PATH') && exit;

abstract class Template{

	protected $TEMPLATE_OBJS=array();
	static public $_arrParses=array();
	protected $_sCompiledFilePath;
	protected $_sThemeName='';
	protected $_bIsChildTemplate=FALSE;
	static protected $_bWithInTheSystem=FALSE;

	public function loadParses(){
		$sClassName=get_class($this);// 具体的类
		call_user_func(array($sClassName,'loadDefaultParses'));// 载入默认的分析器
	}

	public function putInTemplateObj(TemplateObj $oTemplateObj){
		$this->TEMPLATE_OBJS[]=$oTemplateObj;
	}

	public function clearTemplateObj(){
		$nCount=count($this->TEMPLATE_OBJS);
		$this->TEMPLATE_OBJS= array();
		return $nCount;
	}

	public function compile($sTemplatePath,$sCompiledPath=''){
		A::DFILE($sTemplatePath);
		if($sCompiledPath==''){
			$sCompiledPath=$this->getCompiledPath($sTemplatePath);
		}
		$sCompiled=file_get_contents($sTemplatePath);
		foreach(self::$_arrParses as $sParserName){
			$oParser=Dyhb::instance($sParserName);
			$this->bParseTemplate_($sCompiled);
			$oParser->parse($this,$sTemplatePath,$sCompiled);// 分析
			$sCompiled=$this->compileTemplateObj();// 编译
		}
		if(defined('TMPL_STRIP_SPACE')){
			$arrFind=array("~>\s+<~","~>(\s+\n|\r)~");
			$arrReplace=array("><",">");
			$sCompiled=preg_replace($arrFind, $arrReplace, $sCompiled);
		}
		$sStr="<?php !defined('DYHB_PATH')&& exit; /* DoYouHaoBaby Framework ".(G::L('模板缓存文件 生成时间：','Dyhb')).date('Y-m-d H:i:s', CURRENT_TIMESTAMP). "  */ ?>\r\n";
		$sCompiled=$sStr.$sCompiled;
		$sCompiled=str_replace(array("\r","\n"),'__dyhb_framework_pk_with_you__',$sCompiled);
		$sCompiled=preg_replace("/(__dyhb_framework_pk_with_you__)+/i",'__dyhb_framework_pk_with_you__',$sCompiled);
		$sCompiled=str_replace('__dyhb_framework_pk_with_you__',(IS_WIN?"\r\n":"\n"),$sCompiled);// 解决不同操作系统源代码换行混乱
		$this->makeCompiledFile($sTemplatePath,$sCompiledPath,$sCompiled);// 生成编译文件
		return $sCompiledPath;
	}

	protected function bParseTemplate_(&$sCompiled){}

	protected function compileTemplateObj(){
		$sCompiled='';// 逐个编译TemplateObj
		foreach($this->TEMPLATE_OBJS as $oTemplateObj){
			$oTemplateObj->compile();
			$sCompiled.=$oTemplateObj->getCompiled();
		}
		return $sCompiled;
	}

	public function getCompiledPath($sTemplatePath){
		if(self::$_bWithInTheSystem===true){// 如果保存在系统内部
			$this->_sCompiledFilePath=dirname($sTemplatePath).'/Compiled/'.basename($sTemplatePath).'.compiled.php';
			return $this->_sCompiledFilePath;
		}
		$sTemplatePath =str_replace('\\','/',$sTemplatePath);// URL 斜线风格引起的异常
		$arrValue=explode('/' ,$sTemplatePath);
		$sFileName=array_pop($arrValue);
		// D:\phpcondition\......排除绝对路径分析
		// /home1/...... 排序Linux环境下面的绝对路径分析
		if( strpos($sTemplatePath,':/') && strpos($sTemplatePath,'/')!==0 && $GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='_' && !strpos($sFileName,'_')){
			$sFileName=!empty($arrValue)?ucfirst(array_pop($arrValue)).'/'.$sFileName:'Public/'.$sFileName;
		}
		$this->_sCompiledFilePath=APP_PATH.'/App/~Runtime/Cache/'.($this->_sThemeName?ucfirst($this->_sThemeName).'/':'').($GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/'?ucfirst(MODULE_NAME).'/':'').$sFileName.'.compiled.php';
		return $this->_sCompiledFilePath;
	}

	static public function in($bWithInTheSystem=false){
		$bOldValue =self::$_bWithInTheSystem;
		self::$_bWithInTheSystem=$bWithInTheSystem;
		return $bOldValue;
	}

	public function returnCompiledPath(){
		return $this->_sCompiledFilePath;
	}

	protected function isCompiledFileExpired($sTemplatePath,$sCompiledPath){
		if(!is_file($sCompiledPath)){
			return true;
		}
		if($GLOBALS['_commonConfig_']['CACHE_LIFE_TIME']==-1){// 编译过期时间为-1表示永不过期
			return false;
		}
		if(filemtime($sCompiledPath)+$GLOBALS['_commonConfig_']['CACHE_LIFE_TIME']<CURRENT_TIMESTAMP){
			return true;
		}
		if(filemtime($sTemplatePath)>=filemtime($sCompiledPath)){
			return true;
		}
		return false;
	}

	protected function makeCompiledFile($sTemplatePath,$sCompiledPath,&$sCompiled){
		!is_file($sCompiledPath) && !is_dir(dirname($sCompiledPath)) && G::makeDir(dirname($sCompiledPath));
		file_put_contents($sCompiledPath,$sCompiled);
	}

}
