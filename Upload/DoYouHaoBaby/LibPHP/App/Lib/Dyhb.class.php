<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Dyhb框架核心类($) */

!defined('DYHB_PATH') && exit;

class Dyhb{

	private static $INSTANCES=array();

	static public function instance($sClass,$Args=null,$sMethod=null,$MethodArgs=null){
		$sIdentify=$sClass.serialize($Args).$sMethod.serialize($MethodArgs);// 惟一识别号
		if(!isset(self::$INSTANCES[$sIdentify])){
			if(class_exists($sClass)){
				$oClass=$Args===null?new $sClass():new $sClass($Args);
				if(!empty($sMethod) && method_exists($oClass,$sMethod)){
					self::$INSTANCES[$sIdentify]=$MethodArgs===null?call_user_func(array(&$oClass,$sMethod)):call_user_func_array(array(&$oClass,$sMethod),array($MethodArgs));
				}
				else{
					self::$INSTANCES[$sIdentify]=$oClass;
				}
			}
			else{
				G::E(sprintf('class %s is not exists',$sClass));
			}
		}
		return self::$INSTANCES[$sIdentify];
	}

	static public function cache($sId,array $arrOption=null,$sBackendClass=null,$bReturnObj=false){
		static $oObj=null;
		if(is_null($sBackendClass)){
			if(is_null($oObj)){
				$oObj=self::instance($GLOBALS['_commonConfig_']['RUNTIME_CACHE_BACKEND'],$arrOption);
			}
			$arrOldOptions=$oObj->backOptions();
			$oObj->backOptions($arrOption);
			if($bReturnObj===true){return $oObj;}
			$sResult=$oObj->getCache($sId);
			$oObj->backOptions($arrOldOptions);
			return $sResult;
		}
		else{
			$oObj=self::instance($sBackendClass);
			$oObj->backOptions($arrOption);
			if($bReturnObj===true){return $oObj;}
			return $oObj->getCache($sId);
		}
	}

	static public function writeCache($sId,$Data,array $arrOption=null,$sBackendClass=null){
		static $oObj=null;
		if(is_null($sBackendClass)){
			if(is_null($oObj)){
				$oObj=self::instance($GLOBALS['_commonConfig_']['RUNTIME_CACHE_BACKEND'],$arrOption);
			}
			$arrOldOptions=$oObj->backOptions();
			$oObj->backOptions($arrOption);
			$bResult=$oObj->setCache($sId,$Data);
			$oObj->backOptions($arrOldOptions);
			return $bResult;
		}
		else{
			$oObj=self::instance($sBackendClass);
			$oObj->backOptions($arrOption);
			return $oObj->setCache($sId,$Data);
		}
	}

	static public function deleteCache($sId,array $arrOption=null,$sBackendClass=null,$bReturnObj=false){
		static $oObj=null;
		if(is_null($sBackendClass)){
			if(is_null($oObj)){
				$oObj=self::instance($GLOBALS['_commonConfig_']['RUNTIME_CACHE_BACKEND'],$arrOption);
			}
			$arrOldOptions=$oObj->backOptions();
			$oObj->backOptions($arrOption);
			$bResult=$oObj->deleleCache($sId);
			$oObj->backOptions($arrOldOptions);
			return $bResult;
		}
		else{
			$oObj=self::instance($sBackendClass);
			$oObj->backOptions($arrOption);
			return $oObj->deleleCache($sId);
		}
	}

	public static function normalize($Input,$sDelimiter=',',$bAllowedEmpty=false){
	 	if(is_array($Input) || is_string($Input)){
	 		if(!is_array($Input)){
				$Input=explode($sDelimiter,$Input);
			}
			$Input=array_filter($Input);// 过滤null
			if($bAllowedEmpty===true){
				return $Input;
			}
			else{
				$Input=array_map('trim',$Input);
				return array_filter($Input,'strlen');
			}
	 	}
	 	else{
	 		return $Input;
	 	}
	}

}
