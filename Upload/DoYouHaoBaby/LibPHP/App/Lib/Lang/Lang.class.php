<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   语言管理类($) */

!defined('DYHB_PATH') && exit;

class Lang{

	const CURRENT_LANGUAGE=null;
	private $_sLangName;
	private $_oCurrentPackage=null;
	static private $LANG_INSES;
	static private $_oCurrentLang;

	private function __construct($sLangName){
		$this->_sLangName=$sLangName;
	}

	static public function getLang($sLangName){
		if(isset(self::$LANG_INSES[$sLangName])){return self::$LANG_INSES[$sLangName];}
		$oLang=new Lang($sLangName);
		self::$LANG_INSES[$sLangName]=$oLang;
		if(!self::$_oCurrentLang){// 若无当前语言实例,自动设置为当前语言实例
			self::$_oCurrentLang=$oLang;
		}
		return $oLang;
	}

	static public function setCurrentLang($Lang){
		$oOldValue=self::getCurrentLang();
		if(is_string($Lang)){self::$_oCurrentLang=self::getLang($Lang);}
		elseif($Lang instanceof Lang){self::$_oCurrentLang=$Lang;}
		else{E('Parameters $Lang must be a name(String type), the language object ever have be created,or null(the current language pack)');}
		return $oOldValue;
	}

	static public function getCurrentLang(){
		return self::$_oCurrentLang;
	}

	public function getLangName(){
		return $this->_sLangName;
	}

	public function getPackage($sPackageName){
		$oThePackage=LangPackage::getPackage($this->getLangName(),$sPackageName);
		if(!$oThePackage){E('Can not find the language pack according to the parameters \$sPackageName({$sPackageName}.');}
		if(!$this->getCurrentPackage()){$this->setCurrentPackage($oThePackage);}// 若无当前语言包，自动设为当前语言包
		return $oThePackage;
	}

	public function getCurrentPackage(){
		return $this->_oCurrentPackage;
	}

	public function setCurrentPackage($Package){
		$oOldValue=$this->getCurrentPackage();
		if(is_string($Package)){$this->_oCurrentPackage=$this->getPackage($Package);}
		elseif($Package instanceof LangPackage){$this->_oCurrentPackage=$Package;}
		else{E('Parameters $Packaqe must be a language  name(String type), the language pack object ever have be created,or null(the current language pack)');}
		return $oOldValue;
	}

	static public function set($sValue/*Argvs*/){
		$arrArgs=func_get_args();
		unset($arrArgs[0]);
		array_unshift($arrArgs,$sValue,null,null);
		return call_user_func_array(array(__CLASS__,'setEx'),$arrArgs);
	}

	static public function makeValueKey($sValue){
		return md5($sValue);
	}

	static public function setEx($sValue,$Package=null,$Lang=null/*Argvs*/){
		$sKey=self::makeValueKey($sValue);
		if(is_string($Lang)){$oTheLang=self::getLang($Lang);}// 取得语言享员对象
		elseif(is_object($Lang)){$oTheLang=$Lang;}// 已创建的语言对象
		elseif($Lang===null){
			$oTheLang=self::getCurrentLang();
			if(!$oTheLang){E('Not specify the current language ,triggering an exception!');}
		}// 缺省：当前语言对象
		if(is_string($Package)){$oThePackage=$oTheLang->getPackage($Package);}
		elseif(is_object($Package)){$oThePackage=$Package;}
		elseif($Package===null){
			$oThePackage=$oTheLang->getCurrentPackage();
			if(!$oThePackage){E('Not specify the current language ,triggering anexception !');}
		}
		if($oThePackage->has($sKey)){$sReallyValue=$oThePackage->get($sKey);}// 语句存在
		else{
			$sReallyValue=$sValue;
			$oThePackage->set($sKey,$sReallyValue);
		}
		if(func_num_args()> 3){// 代入参数
			$arrArgs=func_get_args();
			$arrArgs[0]=$sReallyValue;
			unset($arrArgs[1],$arrArgs[2]);
			$sReallyValue=call_user_func_array('sprintf',$arrArgs);
		}
		return $sReallyValue;
	}

	static public function tidyPath($sPath,$bUnix=true){
		$sRetPath=str_replace('\\','/',$sPath);
		$sRetPath=preg_replace('|/+|','/',$sRetPath);
		$arrDirs=explode('/',$sRetPath);
		$arrDirs2=array();
		while(($sDirName=array_shift($arrDirs))!==null){
			if($sDirName=='.'){
				continue;
			}
			if($sDirName=='..'){
				if(count($arrDirs2)){
					array_pop($arrDirs2);
					continue;
				}
			}
			array_push($arrDirs2,$sDirName);
		}
		$sRetPath=implode('/',$arrDirs2);
		if(is_dir($sRetPath)){
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.= '/';
			}
		}
		else if(preg_match("|\.$|",$sPath)){
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.= '/';
			}
		}
		$sRetPath=str_replace(':/',':\\',$sRetPath);
		if(!$bUnix){
			$sRetPath=str_replace('/','\\',$sRetPath);
		}
		return rtrim($sRetPath,'\\/');
	}

}
