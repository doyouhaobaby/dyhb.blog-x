<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   对 PHP 原生Session 函数库的封装($)*/

!defined('DYHB_PATH') && exit;

define("HTTP_SESSION_STARTED",1);
define("HTTP_SESSION_CONTINUED",2);

class Session{

	static private $_oGlobalInstance;
	static protected $_nExpireSec;
	static protected $_sSessionKey;
	static protected $_sSessionPath;
	static protected $_sCookidDomain;
	static protected $_SesssionCallback;
	static protected $_sSessionPrefix;

	static public function startSession($nExpireSec=900,$sSessionKey=null,$sSessionPrefix=null,$sSessionPath=null,$sCookidDomain=null,$SesssionCallback=null) {
		self::$_nExpireSec=$nExpireSec;
		self::$_sSessionKey=$sSessionKey;
		self::$_sSessionPrefix=$sSessionPrefix;
		self::$_sSessionPath=$sSessionPath;
		self::$_sCookidDomain=$sCookidDomain;
		self::$_SesssionCallback=$SesssionCallback;
		if(!isset($_SESSION)){
			$this->init_();
			if(!$this->start()){
				return null;
			}
		}
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new Session();
		}
		return self::$_oGlobalInstance;
	}

	private function init_(){
		ini_set('session.auto_start',0);// 设置session不自动启动
		if(is_null($this->detectId())) {
			$this->id(uniqid(dechex(mt_rand())));
		}
		$this->setCookieDomain(self::$_sCookidDomain);// 设置Session有效域名
		$this->localName(APP_NAME);// 设置当前项目运行脚本作为Session本地名
		$this->name(self::$_sSessionKey);
		$this->path(self::$_sSessionPath);
		$this->setCallback(self::$_SesssionCallback);
	}

	private function start(){
		if(!session_start()){
			return null;
		}
		if(!isset($_SESSION['__HTTP_Session_Info'])) {
			$_SESSION['__HTTP_Session_Info']=HTTP_SESSION_STARTED;
		}
		else{
			$_SESSION['__HTTP_Session_Info']=HTTP_SESSION_CONTINUED;
		}
		$this->setExpire(self::$_nExpireSec);
	}

	private function session(){}

	public function getSessionKey(){
		return session_id();
	}

	public function setVariable($sVarName, $Value=null){
		if($Value===null){
			unset($_SESSION[self::$_sSessionPrefix.$sVarName]);
			return;
		}
		$sOldValue=isset($_SESSION[self::$_sSessionPrefix.$sVarName])? $_SESSION[self::$_sSessionPrefix.$sVarName]:null;
		$_SESSION[self::$_sSessionPrefix.$sVarName]=$Value;
		return $sOldValue;
	}

	public function getVariable($sVarName){
		return isset($_SESSION[self::$_sSessionPrefix.$sVarName])?$_SESSION[self::$_sSessionPrefix.$sVarName ]:null;
	}

	public function &getVariableRef($sVarName){
		return isset($_SESSION[ self::$_sSessionPrefix.$sVarName ])? $_SESSION[ self::$_sSessionPrefix.$sVarName ]: null;
	}

	public function deleteVariable($sVarName){
		return session_unregister(self::$_sSessionPrefix.$sVarName);
	}

	public function issetVariable($sVarName){
		return isset($_SESSION[self::$_sSessionPrefix.$sVarName] );
	}

	public function clearVariable(){
		$_SESSION=array();
	}

	public function destroy(){
		session_destroy();
	}

	public function pause(){
		session_write_close();
	}
	
	public function terminateSession(){
		$this->clearVariable();
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(),'',time()-42000,'/');
		}
		$this->destroy();
	}

	public function clearLocal(){
		$local=$this->localName();
		unset($_SESSION[$local]);
	}

	public function detectId(){
		if(session_id()!=''){
			return session_id();
		}
		if($this->useCookies()) {
			if(isset($_COOKIE[$this->name()])){
				return $_COOKIE[$this->name()];
			}
		}
		else{
			if(isset($_GET[$this->name()])){
				return $_GET[$this->name()];
			}
			if(isset($_POST[$this->name()])){
				return $_POST[$this->name()];
			}
		}
		return null;
	}

	public function name($sName=null){
		return isset($sName)?session_name($sName):session_name();
	}

	public function id($sid=null){
		return isset($sid)?session_id($sid):session_id();
	}

	public function path($sPath=null){
		return !empty($sPath)?session_save_path($sPath):session_save_path();
	}

	public function setExpire($nTime, $bAdd=false){
		if($bAdd){
			if(!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
				$_SESSION['__HTTP_Session_Expire_TS']=CURRENT_TIMESTAMP + $nTime;
			}
			$nCurrentGcMaxLifetime=$this->setGcMaxLifetime(null);// 更新session.gc_maxlifetime
			$this->setGcMaxLifetime($nCurrentGcMaxLifetime + $nTime);
		}
		elseif(!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
			$_SESSION['__HTTP_Session_Expire_TS']=$nTime;
		}
	}

	public function setIdle($nTime, $bAdd=false){
		if($bAdd){
			$_SESSION['__HTTP_Session_Idle']=$nTime;
		}
		else{
			$_SESSION['__HTTP_Session_Idle']=$nTime-CURRENT_TIMESTAMP;
		}
	}

	public function sessionValidThru(){
		if(!isset($_SESSION['__HTTP_Session_Idle_TS']) || !isset($_SESSION['__HTTP_Session_Idle'])) {
			return 0;
		}
		else{
			return $_SESSION['__HTTP_Session_Idle_TS'] + $_SESSION['__HTTP_Session_Idle'];
		}
	}

	public function isExpired(){
		if(isset($_SESSION['__HTTP_Session_Expire_TS']) && $_SESSION['__HTTP_Session_Expire_TS'] < CURRENT_TIMESTAMP) {
			return true;
		} 
		else{
			return false;
		}
	}

	public function isIdle(){
		if(isset($_SESSION['__HTTP_Session_Idle_TS']) &&(($_SESSION['__HTTP_Session_Idle_TS'] + $_SESSION['__HTTP_Session_Idle']) < CURRENT_TIMESTAMP)) {
			return true;
		} 
		else {
			return false;
		}
	}

	public function updateIdle(){
		$_SESSION['__HTTP_Session_Idle_TS']=CURRENT_TIMESTAMP;
	}

	public function setCallback($Callback=null){
		$return=ini_get('unserialize_callback_func');
		if(!empty($Callback)) {
			ini_set('unserialize_callback_func',$Callback);
		}
		return $return;
	}

	public function isNew(){
		return !isset($_SESSION['__HTTP_Session_Info']) || $_SESSION['__HTTP_Session_Info']==HTTP_SESSION_STARTED;
	}

	public function getLocal($sName){
		$sLocal=$this->localName();
		if(!is_array($_SESSION[$sLocal])) {
			$_SESSION[$sLocal]=array();
		}
		return $_SESSION[$sLocal][$sName];
	}

	public function setLocal($sName,$Value){
		$sLocal=$this->localName();
		if(!is_array($_SESSION[$sLocal])) {
			$_SESSION[$sLocal]=array();
		}
		if(null===$Value) {
			unset($_SESSION[$sLocal][$sName]);
		}
		else{
			$_SESSION[$sLocal][$sName]=$Value;
		}
		return;
	}

	public function isSetLocal($sName){
		$sLocal=$this->localName();
		return isset($_SESSION[$sLocal][$sName]);
	}

	public function localName($sName=null){
		$sReturn=(isset($GLOBALS['__HTTP_Session_Localname']))?$GLOBALS['__HTTP_Session_Localname']:null;
		if(!empty($sName)) {
			$GLOBALS['__HTTP_Session_Localname']=md5($sName);
		}
		return $sReturn;
	}

	public function getFilename(){
		return $this->path().'/sess_'.session_id();
	}

	public function setCookieDomain($sSessionDomain=null){
		$sReturn=ini_get('session.cookie_domain');
		if(!empty($sSessionDomain)){
			ini_set('session.cookie_domain',$sSessionDomain);// 跨域访问Session
		}
		return $sReturn;
	}

	public function useCookies($bUseCookies=null) {
		$nReturn=ini_get('session.use_cookies')?true : false;
		if(isset($bUseCookies)) {
			ini_set('session.use_cookies',$bUseCookies?1:0);
		}
		return $nReturn;
	}

	public function useTransSid($nUseTransSid=null){
		$nReturn=ini_get('session.use_trans_sid')?true : false;
		if(isset($nUseTransSid)){
			ini_set('session.use_trans_sid',$nUseTransSid?1:0);
		}
		return $nReturn;
	}

	public function setGcMaxLifetime($nGcMaxlifetime=null){
		$nReturn=ini_get('session.gc_maxlifetime');
		if(isset($nGcMaxlifetime) && is_int($nGcMaxlifetime) && $nGcMaxlifetime >= 1){
			ini_set('session.gc_maxlifetime',$nGcMaxlifetime);
		}
		return $nReturn;
	}

	public function setGcProbability($nGcProbability=null){
		$nReturn=ini_get('session.gc_probability');
		if(isset($nGcProbability) && is_int($nGcProbability) && $nGcProbability >= 1 && $nGcProbability<=100) {
			ini_set('session.gc_probability',$nGcProbability);
		}
		return $nReturn;
	}

}
