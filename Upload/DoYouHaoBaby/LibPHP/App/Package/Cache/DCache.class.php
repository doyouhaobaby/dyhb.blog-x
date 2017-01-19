<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   缓存抽象类($) */

!defined('DYHB_PATH') && exit;

abstract class DCache{

	protected $_hHandel;
	protected $_bConnected;
	protected $_sPrefix='~@';
	protected $_arrOptions=array();
	protected $_nCacheTime=0;
	protected $_bCacheEnabled=TRUE;
	static public $CACHES=array();
	const MEMORY_CACHE='memory';
	const FILE_CACHE='file';
	const APC_CACHE='apc';
	const XCACHE_CACHE='xcache';
	const MEMCACHE_CACHE='memcache';
	const SHMOP_CACHE='shmop';
	const EACCELERATOR_CAHCE='eaccelerator';
	public $_sCacheType=self::MEMORY_CACHE;
	static private $Callback;

	static public function regCallback($Callback=null){
		$oOldValue=self::$Callback;
		self::$Callback=$Callback;
		return $oOldValue;
	}

	static public function getDefaultCache(){
		$oCache=call_user_func(self::$Callback);
		return $oCache;
	}

	public function &__get($sCacheName) {
		if(!$this->_bCacheEnabled) return;
		return $this->getCache($sCacheName);
	}

	public function __set($sCacheName,$Data) {
		if(!$this->_bCacheEnabled) return;
		return $this->setCache($sCacheName,$Data);
	}

	public function __unset($sCacheName) {
		if(!$this->_bCacheEnabled) return;
		$this->deleleCache($sCacheName);
	}

	public function setOption($sOptionName,$Value) {
		$this->_arrOptions[$sOptionName]=$Value;
	}

	public function getOption($sOptionName) {
		return $this->_arrOptions[$sOptionName];
	}

	public function backOptions($arrOptions=null){
		if(isset($arrOptions['cache_time'])){
			$this->_nCacheTime=!empty($arrOptions['cache_time'])?(int)$arrOptions['cache_time']:86400;
			unset($arrOptions['cache_time']);
		}
		!is_null($arrOptions)?$this->_arrOptions=array_merge($this->_arrOptions,$arrOptions):$this->_arrOptions;
		return $this->_arrOptions;
	}

	public function Q($nTimes='') {
		static $nTimes=0;
		if(empty($nTimes)){return $nTimes;}
		else{$nTimes++;}
	}

	public function W($nTimes='') {
		static $nTimes=0;
		if(empty($nTimes)){return $nTimes;}
		else{$nTimes++;}
	}

	public function encryptKey($sCacheName){
		 return md5($sCacheName);
	}

	protected function checkCachePath(){
		if(empty($this->_arrOptions['cache_path'])){
			G::E('Cache directory can not be null.');
		}
		if(!is_dir($this->_arrOptions['cache_path'])){
			G::makeDir($this->_arrOptions['cache_path']);
		}
		$arrStat=stat($this->_arrOptions['cache_path']);
		$nDirPerms=$arrStat['mode']&0007777; // Get the permission bits.
		$nFilePerms=$nDirPerms&0000666; // Remove execute bits for files.
		if(!is_dir($this->_arrOptions['cache_path'])) {
			if(!G::makeDir($this->_arrOptions['cache_path'])){return false;}
			chmod($this->_arrOptions['cache_path'],$nDirPerms);
		}
	}

}
