<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   系统路由解析类($)*/

!defined('DYHB_PATH') && exit;

class Router{

	protected $_sLastRouterName=null;
	protected $_arrLastRouteInfo=array();
	protected $_arrRouters=array();
	protected $_oUrlParseObj=null;

	public function __construct($oUrlParseObj=null){
		if(is_null($oUrlParseObj)){
			$this->_oUrlParseObj=new Url();
		}
		else{
			$this->_oUrlParseObj=$oUrlParseObj;
		}
	}

	public function G($sRouterName=null){
		$sCurrentName=$sRouterName?$sRouterName:$this->getRouterName();
		$arrRouters=&$this->_arrRouters;
		$arrRouteInfo=array();
		if(isset($arrRouters[$sCurrentName])){
			if(!strpos($sCurrentName,'@')){
				$arrRouteInfo=$this->getNormalRoute($sCurrentName,$arrRouters[$sCurrentName]);
			}
			else{
				$arrRouteInfo=$this->getFlowRoute($sCurrentName,$arrRouters[$sCurrentName]);
			}
		}
		$this->_arrRouteInfo=$arrRouteInfo;
		return $this->_arrRouteInfo;
	}

	public function import(array $arrRouters=null,$sCacheId=null){
		if(!$GLOBALS['_commonConfig_']['START_ROUTER']){return false;}
		if($sCacheId){// 如果存在缓存，那么直接导入路由缓存
			$arrRouters=Dyhb::cache($sCacheId, null,$GLOBALS['_commonConfig_']['RUNTIME_CACHE_BACKEND']);
		}
		if(is_null($arrRouters)){
			if(file_exists(APP_PATH.'/App/Config/Router.php')){// 从配置文件中载入路由
				$arrRouters=(array)(include(APP_PATH.'/App/Config/Router.php'));
			}
		}
		if($sCacheId && $arrRouters){
			Dyhb::writeCache($sCacheId,$arrRouters,array('cache_time'=>86400),$GLOBALS['_commonConfig_']['RUNTIME_CACHE_BACKEND']);
		}
		$this->_arrRouters=array_merge($this->_arrRouters,$arrRouters);
		return $this;
	}

	public function add($sRouteName,array $arrRule){
		$this->_arrRouters[$sRouteName]=$arrRule;
		return $this;
	}

	public function remove($sRouteName){
		unset($this->_arrRouters[$sRouteName]);
		return $this;
	}

	public function get($sRouteName){
		return $this->_arrRouters[$sRouteName];
	}

	public function getLastRouterName(){
		return $this->_sLastRouterName;
	}

	public function getLastRouterInfo(){
		 return $this->_arrLastRouteInfo;
	}

	private function parseUrl($Route){
		if(is_string($Route)){$arrArray=array_filter(explode('/',$Route));}
		else{$arrArray=$Route;}
		if(count($arrArray)<2 || count($arrArray)>3){G::E('$Route parameter format error,claiming the $arrArray between the number of elements in the 2-3.');}
		$arrVar=array();
		$arrVar['a']=array_pop($arrArray);
		$arrVar['c']=array_pop($arrArray);
		if(!empty($arrArray)){$arrVar['g']=array_pop($arrArray);}
		return $arrVar;
	}

	private function getRouterName(){
		if(isset($_GET['r'])){
			$sRouteName=$_GET['r'];
			unset($_GET['r']);
		}
		else{
			$sPathInfo=&$_SERVER['PATH_INFO'];
			$arrPaths=explode($GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'],trim($sPathInfo,'/'));
			$sRouteName=array_shift($arrPaths);
		}
		$sRouteName= ucwords(strtolower($sRouteName));
		if(isset($this->_arrRouters[$sRouteName.'@'])){$sRouteName=$sRouteName.'@';}
		$this->_sLastRouterName=$sRouteName;
		return $this->_sLastRouterName;
	}

	private function getNormalRoute($sRouteName,array $arrRule){
		if(isset($arrRule['regex'])){return $this->getRegexRoute_($sRouteName,$arrRule);}
		else{return $this->getSimpleRoute_($sRouteName,$arrRule);}
	}

	private function getFlowRoute($sRouteName,array $arrRule){
		foreach($arrRule as $arrRule){
			$arrVar=$this->getNormalRoute($sRouteName,$arrRule);
			if($arrVar!==null){return $arrVar;}
		}
		return array();
	}

	private function getSimpleRoute_($sRouteName,$arrRule){
		if(count($arrRule)<2 || count($arrRule)>5){
			G::E('$arrRule parameter must be greater than or equal 2,less than or equal 5.');
		}
		$arrVar=$this->parseUrl($arrRule[0]);
		$sPathInfo=&$_SERVER['PATH_INFO'];
		$sDepr=$GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'];
		$sRegx=trim($sPathInfo,'/');
		$arrPaths=array_filter(explode($sDepr,trim(str_ireplace(strtolower($sRouteName),'',$sRegx),$sDepr)));
		if(!empty($arrRule[1]) && in_array($arrRule[1],$arrPaths)){array_shift($arrPaths);}
		$arrVars=explode(',',$arrRule[1]);
		for($nI=0;$nI<count($arrVars);$nI++){$arrVar[ $arrVars[$nI] ] =array_shift($arrPaths);}
		$bResult=preg_replace('@(\w+)\/([^,\/]+)@e','$arrVar[\'\\1\']="\\2";',implode('/',$arrPaths));
		$arrParams=array();
		if(isset($arrRule[2])){
			parse_str($arrRule[2],$arrParams);
			$arrVar=array_merge($arrVar,$arrParams);
		}
		return $arrVar;
	}

	private function getRegexRoute_($sRouteName,$arrRule){
		if(count($arrRule)<3 || count($arrRule)>6){
			G::E('$arrRule parameter must be greater than or equal 3, less than or equal 6.');
		}
		$sPathInfo=&$_SERVER['PATH_INFO'];
		$sDepr=$GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'];
		$sRegx=trim($sPathInfo,'/');
		$sRegx=ltrim($sRegx,strtolower(rtrim($sRouteName,'@')));
		$sTheRegex=array_shift($arrRule);
		$arrMatches=array();
		if(preg_match($sTheRegex,$sRegx,$arrMatches)){
			$arrVar=$this->parseUrl($arrRule[0]);
			$arrVars=explode(',',$arrRule[1]);
			for($nI=0;$nI<count($arrVars);$nI++){$arrVar[$arrVars[$nI]]=$arrMatches[$nI+1];}
			$bResult=preg_replace('@(\w+)\/([^,\/]+)@e', '$arrVar[\'\\1\']="\\2";',trim(str_replace($arrMatches[0],'',$sRegx),'\/'));
			$arrParams=array();
			if(isset($arrRule[2])){
				parse_str($arrRule[2],$arrParams);
				$arrVar=array_merge($arrVar,$arrParams);
			}
			return $arrVar;
		}
		return null;
	}

}
