<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Web URL分析器($)*/

!defined('DYHB_PATH') && exit;

class Url{

	protected $_sLastRouterName=null;
	protected $_arrLastRouteInfo=array();
	static private $_sBaseUrl;
	static private $_sBaseDir;
	static private $_sRequestUrl;
	private $_oRouter=null;
	public $_sControllerName;
	public $_sActionName;

	public function parseUrl(){
		$nUrlModel=$GLOBALS['_commonConfig_']['URL_MODEL'];
		if($nUrlModel===URL_REWRITE){// 如果为重写模式
			$sUrl=dirname(_PHP_FILE_);
			if($sUrl=='/' || $sUrl=='\\'){$sUrl='';}
			define('PHP_FILE',$sUrl);
		}
		elseif($nUrlModel===URL_COMPAT){
			define('PHP_FILE',_PHP_FILE_.'?s=');
		}
		else{
			define('PHP_FILE',_PHP_FILE_);
		}
		$sDepr=$GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'];
		if($nUrlModel){
			$this->filterPathInfo();
			if($GLOBALS['_commonConfig_']['START_ROUTER']){
				$arrRouterInfo=$this->getRouterInfo();
				if(empty($arrRouterInfo)){$_GET=array_merge($this->parsePathInfo(),$_GET);}
				else{$_GET=array_merge($this->getRouterInfo(),$_GET);}
			}
			else{$_GET=array_merge($this->parsePathInfo(),$_GET);}
		}
		else{
			if($GLOBALS['_commonConfig_']['START_ROUTER'] && isset($_GET['r'])){
			 	$arrRouterInfo=$this->getRouterInfo();
				if(!empty($arrRouterInfo)){$_GET=array_merge($arrRouterInfo,$_GET);}
				else{$_GET=array_merge($this->getRouterInfo(),$_GET);}
			}
			else{$_GET=array_merge($this->parsePathInfo(),$_GET);}
		}
		define('MODULE_NAME',$_GET['c']=$this->getControl('c'));
		define('ACTION_NAME',$_GET['a']=$this->getAction('a'));
		define('__SELF__',$_SERVER['REQUEST_URI']);// 当前页面地址
		define('__APP__',PHP_FILE);
		define('__URL__',__APP__.'/'.MODULE_NAME);
		define('__ACTION__',__URL__.$sDepr.ACTION_NAME);
		$_REQUEST=array_merge($_POST,$_GET);
	}

	private function getRouterInfo(){
		if(is_null($this->_oRouter)){
			$this->_oRouter=new Router($this);
		}
		$this->_oRouter->import();// 导入路由规则
		$this->_arrLastRouteInfo=$this->_oRouter->G();// 获取路由信息
		$this->_sLastRouterName =$this->_oRouter->getLastRouterName();
		return $this->_arrLastRouteInfo;
	}

	public function getLastRouterName(){
		return $this->_sLastRouterName;
	}

	public function getLastRouterInfo(){
		return $this->_arrLastRouteInfo;
	}

	public function requestUrl(){
		if(self::$_sRequestUrl)return self::$_sRequestUrl;
		if(isset($_SERVER['HTTP_X_REWRITE_URL'])){
			$sUrl=$_SERVER['HTTP_X_REWRITE_URL'];
		}
		elseif(isset($_SERVER['REQUEST_URI'])){
			$sUrl=$_SERVER['REQUEST_URI'];
		}
		elseif(isset($_SERVER['ORIG_PATH_INFO'])){
			$sUrl=$_SERVER['ORIG_PATH_INFO'];
			if(!empty($_SERVER['QUERY_STRING'])){
				$sUrl.='?'.$_SERVER['QUERY_STRING'];
			}
		}
		else{
			$sUrl='';
		}
		self::$_sRequestUrl=$sUrl;
		return $sUrl;
	}

	public function baseDir(){
		if(self::$_sBaseDir)return self::$_sBaseDir;
		$sBaseUrl=$this->baseUrl();
		if(substr($sBaseUrl, - 1, 1)== '/'){
			$sBaseDir=$sBaseUrl;
		}
		else{
			$sBaseDir=dirname($sBaseUrl);
		}
		self::$_sBaseDir=rtrim($sBaseDir,'/\\').'/';
		return self::$_sBaseDir;
	}

	public function baseUrl(){
		if(self::$_sBaseUrl)return self::$_sBaseUrl;
		$sFileName=basename($_SERVER['SCRIPT_FILENAME']);
		if(basename($_SERVER['SCRIPT_NAME'])===$sFileName){
			$sUrl=$_SERVER['SCRIPT_NAME'];
		}
		elseif(basename($_SERVER['PHP_SELF'])===$sFileName){
			$sUrl=$_SERVER['PHP_SELF'];
		}
		elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$sFileName){
			$sUrl=$_SERVER['ORIG_SCRIPT_NAME'];
		}
		else{
			$sPath=$_SERVER['PHP_SELF'];
			$arrSegs=explode('/',trim($_SERVER['SCRIPT_FILENAME'],'/'));
			$arrSegs=array_reverse($arrSegs);
			$nIndex=0;
			$nLast=count($arrSegs);
			$sUrl='';
			do{
				$sSeg=$arrSegs[$nIndex];
				$sUrl='/'.$sSeg.$sUrl;
				++ $nIndex;
			}while(($nLast>$nIndex) && (false!==($nPos=strpos($sPath,$sUrl))) && (0!=$nPos));
		}
		$sRequestUrl=$this->requestUrl();
		if(0===strpos($sRequestUrl, $sUrl)){
			self::$_sBaseUrl=$sUrl;
			return self::$_sBaseUrl;
		}
		if(0===strpos($sRequestUrl, dirname($sUrl))){
			self::$_sBaseUrl=rtrim(dirname($sUrl),'/').'/';
			return self::$_sBaseUrl;
		}
		if(!strpos($sRequestUrl,basename($sUrl))){
			return '';
		}
		if((strlen($sRequestUrl)>=strlen($sUrl)) && ((false!==($nPos=strpos($sRequestUrl,$sUrl))) && ($nPos!==0))){
			$sUrl=substr($sRequestUrl,0,$nPos+strlen($sUrl));
		}
		self::$_sBaseUrl=rtrim($sUrl,'/').'/';
		return self::$_sBaseUrl;
	}

	public function pathinfo(){
		if(!empty($_SERVER['PATH_INFO']))return $_SERVER['PATH_INFO'];
		$sBaseUrl=$this->baseUrl();
		if(null ===($sRequestUrl=$this->requestUrl()))return '';
		if(($nPos=strpos($sRequestUrl,'?'))){
			$sRequestUrl=substr($sRequestUrl,0,$nPos);
		}
		if((null!==$sBaseUrl) && (false===($sPathinfo=substr($sRequestUrl,strlen($sBaseUrl))))){
			$sPathinfo='';
		}
		elseif(null===$sBaseUrl){
			$sPathinfo=$sRequestUrl;
		}
		return $sPathinfo;
	}

	 public function parsePathInfo(){
		$arrPathInfo=array();
		$sPathInfo=&$_SERVER['PATH_INFO'];
		if($GLOBALS['_commonConfig_']['URL_PATHINFO_MODEL']==2){
			$arrPaths=explode($GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'],trim($sPathInfo,'/'));
			if(!isset($_GET['c'])){// 还没有定义模块名称
				$arrPathInfo['c']=array_shift($arrPaths );
			}
			$arrPathInfo['a']=array_shift($arrPaths);
			for($nI=0,$nCnt=count($arrPaths);$nI<$nCnt;$nI++){
				if(isset($arrPaths[$nI+1])){
					$arrPathInfo[$arrPaths[$nI]]=(string)$arrPaths[++$nI];
				}
				elseif($nI==0){
					$arrPathInfo[$arrPathInfo['a']]=(string)$arrPaths[$nI];
				}
			}
		}
		else{
			$bRes=preg_replace('@(\w+)'.$GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR'].'([^,\/]+)@e', '$arrPathInfo[\'\\1\']="\\2";',$sPathInfo);
		}
		return $arrPathInfo;
	}

	protected function getControl($sVar){
		$sControl=(!empty($_GET[$sVar])?$_GET[$sVar]:$GLOBALS['_commonConfig_']['DEFAULT_CONTROL']);
		$this->_sControllerName=strtolower($sControl);
		return $this->_sControllerName;
	}

	protected function getAction($sVar){
		$sAction=!empty($_POST[$sVar])?$_POST[$sVar]:(!empty($_GET[$sVar])?$_GET[$sVar]:$GLOBALS['_commonConfig_']['DEFAULT_ACTION']);
		$this->_sActionName=strtolower($sAction);
		return $this->_sActionName;
	}

	public function control(){
		return $this->_sControllerName;
	}

	public function action(){
		return $this->_sActionName;
	}

	public function filterPathInfo(){
		if(!empty($_GET['s'])){
			$sPathInfo=$_GET['s'];
			unset($_GET['s']);
		}
		else{
			$sPathInfo=$this->pathinfo();
		}
		$sPathInfo=$this->clearHtmlSuffix($sPathInfo);
		$sPathInfo= empty($sPathInfo)?'/':$sPathInfo;
		$_SERVER['PATH_INFO']=$sPathInfo;
	}

	protected function clearHtmlSuffix($sVal){
		if($GLOBALS['_commonConfig_']['URL_HTML_SUFFIX'] && !empty($sVal)){
			$sSuffix=substr($GLOBALS['_commonConfig_']['URL_HTML_SUFFIX'],1);
			$sVal=preg_replace('/\.'.$sSuffix.'$/','',$sVal);
		}
		return $sVal;
	}

}
