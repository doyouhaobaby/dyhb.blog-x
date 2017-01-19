<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   对 PHP 原生Cookie 函数库的封装($) */

!defined('DYHB_PATH') && exit;

class Cookie{

	static private $_oGlobalInstance;
	static protected $_nExpireSec;
	static protected $_sCookiePath;
	static protected $_sCookidDomain;
	static protected $_sCookiePrefix;
	static protected $_bCookieSecure=false;

	static public function startCookie($nExpireSec=900,$sCookiePath=null,$sCookidDomain=null,$sCookiePrefix=null,$bCookieSecure=false){
		self::$_nExpireSec=$nExpireSec;
		self::$_sCookiePath=$sCookiePath;
		self::$_sCookidDomain=$sCookidDomain;
		self::$_sCookiePrefix=$sCookiePrefix;
		self::$_bCookieSecure=$bCookieSecure;
		if(!self::$_oGlobalInstance){
			self::$_oGlobalInstance=new Cookie();
		}
		return self::$_oGlobalInstance;
	}

	public function issetCookie($sName){
		return isset($_COOKIE[self::$_sCookiePrefix.$sName]);
	}

	public static function getCookieName($sName){
		return self::$_sCookiePrefix.$sName;
	}

	public function getCookie($sName){
		if(!$this->issetCookie($sName))return null;
		$value=$_COOKIE[self::$_sCookiePrefix.$sName];
		$value=unserialize(base64_decode($value));
		return $value;
	}

	public function setCookie($sName,$Value,$nExpire=null,$sPath=null,$sDomain=null,$bSecure=null){
		if($nExpire===null){
			$nExpire=self::$_nExpireSec;
		}
		if($sPath===null){
			$sPath=self::$_sCookiePath;
		}
		if($sDomain===null){
			$sDomain=self::$_sCookidDomain;
		}
		if($bSecure===null){
			$bSecure=self::$_bCookieSecure;
		}
		$nExpire=!empty($nExpire)?CURRENT_TIMESTAMP+$nExpire:0;
		$Value=base64_encode(serialize($Value));
		setcookie(self::$_sCookiePrefix.$sName,$Value,$nExpire,$sPath,$sDomain,$bSecure);
		$_COOKIE[$sName]=$Value;
	}

	public function deleteCookie($sName){
		$this->setCookie($sName,null,-86400*365);
		unset($_COOKIE[self::$_sCookiePrefix.$sName]);
	}

	public function clearCookie($bThisPrefix=true){
		$nCookie=count($_COOKIE);
		foreach($_COOKIE as $sKey=>$Val){
			if($bThisPrefix===true){
				strpos($sKey,self::$_sCookiePrefix)===0 && setcookie($sKey,null,CURRENT_TIMESTAMP-86400*365,self::$_sCookiePath,self::$_sCookidDomain,self::$_bCookieSecure);
				unset($_COOKIE[$sKey]);
			}
			else{
				setcookie($sKey,null,CURRENT_TIMESTAMP-86400*365,self::$_sCookiePath,self::$_sCookidDomain,self::$_bCookieSecure);
				unset($_COOKIE[$sKey]);
			}
		}
		return $nCookie;
	}

}
