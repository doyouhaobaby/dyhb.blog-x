<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   数据库表入口($) */

!defined('DYHB_PATH') && exit;

class DbTableEnter{

	public $_sSchema;
	public $_sName;
	public $_sPrefix;
	protected $_pk;
	protected $_bIsCpk;
	protected $_nPkCount;
	protected static $_arrMeta=array();
	protected static $_arrFields=array();
	protected $_bInited;
	protected $_arrCurrentDbConfig;
	protected $_bIsError=false;
	protected $_sErrorMessage;
	protected $_oConnect;
	protected $_oDb;
	protected static $_arrDsn=array();

	public function __construct(array $arrConfig=null){
		$this->_arrConfig =$arrConfig;
		$arrConfig=array_shift($arrConfig);
		if(!empty($arrConfig['db_schema'])){$this->_sSchema=$arrConfig['db_schema'];}
		if(!empty($arrConfig['table_name'])){$this->_sName=$arrConfig['table_name'];}
		if(!empty($arrConfig['db_prefix'])){$this->_sPrefix=$arrConfig['db_prefix'];}
		if(!empty($arrConfig['pk'])){$this->_pk=$arrConfig['pk'];}
		if(!empty($arrConfig['connect'])){$this->setConnect($arrConfig['connect']);}
	}

	public function insert(array $arrRow,$bReturnPkValues=false){
		if(!$this->_bInited){$this->init();}
		$arrInsertId=array();
		if($bReturnPkValues){
			$bUseAutoIncr=false;
			if($this->_bIsCpk){
				foreach($this->_pk as $sPk){// 假定复合主键必须提供所有主键的值
					$arrInsertId[$sPk]=$arrRow[$sPk];
				}
			}
			else{
				$sPk=isset($this->_pk[0])?$this->_pk[0]:'';// 如果只有一个主键字段，并且主键字段不是自增，则通过 nextId() 获得一个主键值
				if(empty($arrRow[$sPk])){
					unset($arrRow[$sPk]);
					if(isset(self::$_arrMeta[$this->_sCacheId][$sPk]) and !self::$_arrMeta[$this->_sCacheId][$sPk]['auto_incr']){
						$arrRow[$sPk]=$this->nextId($sPk);
						$arrInsertId[$sPk]=$arrRow[$sPk];
					}
					else{
						$bUseAutoIncr=true;
					}
				}
				else{
					$arrInsertId[$sPk]=$arrRow[$sPk];
				}
			}
		}
		else{
			$sPk=$this->_pk[0];
			if(!$this->_bIsCpk && ! self::$_arrMeta[$this->_sCacheId][$sPk]['auto_incr'] && empty($arrRow[$sPk])){
				$sPk=$this->_pk[0];// 如果只有一个主键字段，并且主键字段不是自增，则通过 nextID() 获得一个主键值
				$arrRow[$sPk]=$this->nextId($sPk);
			}
		}
		$this->getDb()->insert($arrRow,$this->getFullTableName(),self::$_arrFields[$this->_sCacheId]);
		if($bReturnPkValues){
			if($bUseAutoIncr){// 创建主表的记录成功后，尝试获取新记录的主键值
				$arrInsertId[$sPk]=$this->_oConnect->getInsertId();
			}
			return $arrInsertId;
		}
		else{
			return false;
		}
	}

	public function delete($Where /* 最后两个参数为order,limit,如果没有这个条件，请务必在后面添加上null,或者‘’占位 */){
		if(!$this->_bInited){
			$this->init();
		}
		if(is_int($Where) || ((int) $Where==$Where && $Where>0)){
			if ($this->_bIsCpk){// 如果 $Where 是一个整数，则假定为主键字段值
				G::E(G::L('使用复合主键时，不允许通过直接指定主键值来删除记录。' ,'DbDyhb'));
			}
			else{
				$Where=array(array($this->_pk[0]=>(int)$Where));
			}
		}
		else{
			$Where=func_get_args();
		}
		if(count($Where)>=3){
			$limit=array_pop($Where);// Limit
			$order=array_pop($Where);// Order
		}
		else{
			$limit='';// Limit
			$order='';// Order
		}
		if($limit===null){
			$limit='';
		}
		if($order===null){
			$order ='';
		}
		$this->getDb()->delete($this->getFullTableName(),$Where,$order,$limit);
		return $this->_oConnect->getAffectedRows();
	}

	public function update($Row,$Where=null/* 最后两个参数为order,limit,如果没有这个条件，请务必在后面添加上null,或者‘’占位 */){
		if(!$this->_bInited){
			$this->init();
		}
		if(is_null($Where)){
			if(is_array($Row)){
				$Where=array();
				foreach($this->_pk as $sPk){
					if(!isset($Row[$sPk]) || strlen($Row[$sPk]==0)){
						$Where=array();
						break;
					}
					$Where[$sPk]=$Row[$sPk];
				}
				$Where=array($Where);
			}
			else{
				$Where=null;
			}
		}
		elseif($Where){
			$Where=func_get_args();
			array_shift($Where);
		}
		if(count($Where)>=3){
			$limit=array_pop($Where);// Limit
			$order=array_pop($Where);// Order
		}
		else{
			$limit='';// Limit
			$order='';// Order
		}
		if($limit===null){
			$limit='';
		}
		if($order===null){
			$order ='';
		}
		$this->getDb()->update($this->getFullTableName(),$Row,$Where,$order,$limit,self::$_arrFields[$this->_sCacheId]);
		return $this->_oConnect->getAffectedRows();
	}

	public function tableSelect(){
		if(!$this->_bInited){
			$this->init();
		}
		$oSelect=$this->_oDb->select($this);
		return $oSelect;
	}

	public function nextId($sFieldName=null,$nStart=1){
		if(!$this->_bInited){
			$this->init();
		}
		if(is_null($sFieldName)){
			$sFieldName=$this->_pk[0];
		}
		return $this->connect->nextId($sFieldName,$this->getFullTableName(),$nStart);
	}

	public function getDb(){
		if(!$this->_bInited){
			$this->init();
		}
		return $this->_oDb;
	}

	public function setDb($oDb){
		if(!$this->_bInited){
			$this->init();
		}
		$this->_oDb=$oDb;
	}

	public function getConnect(){
		if(!$this->_bInited){
			$this->init();
		}
		return $this->_oConnect;
	}

	public function setConnect(DbConnect $oConnect){
		static $oDbObjParseDsn=null;
		$this->_oConnect=$oConnect;
		if(empty($this->_sSchema)){
			$this->_sSchema=$oConnect->getSchema();
		}
		if(empty($this->_sPrefix)){
			$this->_sPrefix=$oConnect->getTablePrefix();
		}
	}

	public function getFullTableName(){
		if (!$this->_bInited){
			$this->setupConnect_();
		}
		return (! empty($this->_sSchema) ? "`{$this->_sSchema}`." : ''). "`{$this->_sPrefix}{$this->_sName}`";
	}

	public function columns(){
		if(!$this->_bInited){
			$this->init();
		}
		return self::$_arrMeta[$this->_sCacheId];
	}

	public function getPk(){
		if(!$this->_bInited){
			$this->init();
		}
		return $this->_pk;
	}

	public function setPk($Pk){
		$oldValue=$this->_pk;
		$this->_pk=Dyhb::normalize($Pk);
		$this->_nPkCount=count($this->_pk);
		$this->_bIsCpk=$this->_nPkCount > 1;
		return $oldValue;
	}

	public function isCompositePk(){
		if(!$this->_bInited){
			$this->init();
		}
		return $this->_bIsCpk;
	}

	public function init(){
		if($this->_bInited){ return; }
		$this->_bInited=true;
		$this->setupConnect_();
		$this->setupTableName_();
		$this->setupMeta_();
		$this->setupPk_();
	}

	protected function setupConnect_(){
		if(!is_null($this->_oConnect)){
			return;
		}
		$oDb=Db::RUN($this->_arrConfig);
		$this->setConnect($oDb->getConnect());
		$this->setDb($oDb);
	}

	protected function setupTableName_(){
		if(empty($this->_sName)){
			$this->_sName=substr($this->_sName,0,-2);
		}
		elseif(strpos($this->_sName,'.')){
			list($this->_sChema,$this->_sName)=explode('.',$this->_sName);
		}
	}

	protected function setupMeta_(){
		$sTableName=$this->getFullTableName();
		$this->_sCacheId=trim($sTableName,'`');
		if(isset(self::$_arrMeta[$this->_sCacheId])){
			return;
		}
		$bCached=$GLOBALS['_commonConfig_']['DB_META_CACHED'];
		if($bCached){
			$arrData=Dyhb::cache($this->_sCacheId,array('encoding_filename'=>false,'cache_path'=>APP_RUNTIME_PATH.'/Data/DbMeta'));
			if(is_array($arrData) && !empty($arrData)){
				self::$_arrMeta[$this->_sCacheId]=$arrData[0];
				self::$_arrFields[$this->_sCacheId]=$arrData[1];
				return;
			}
		}
		$arrMeta=$this->_oConnect->metaColumns($sTableName);
		$arrFields=array();
		foreach($arrMeta as $field){
			$arrFields[$field['name']]=true;
		}
		self::$_arrMeta[$this->_sCacheId]=$arrMeta;
		self::$_arrFields[$this->_sCacheId]=$arrFields;
		$arrData=array($arrMeta,$arrFields);
		if($bCached){
			Dyhb::writeCache($this->_sCacheId,$arrData,array('encoding_filename'=>false,'cache_path'=>APP_RUNTIME_PATH.'/Data/DbMeta'));
		}
	}

	protected function setupPk_(){
		if (empty($this->_pk)){
			$this->_pk=array();
			foreach(self::$_arrMeta[$this->_sCacheId] as $arrField){
				if($arrField['pk']){
					$this->_pk[]=$arrField['name'];
				}
			}
		}
		$this->setPk($this->_pk);
	}

	protected function setIsError($bIsError=false){
		$bOldValue=$this->_bIsError;
		$this->_bIsError=$bIsError;
		return $bOldValue;
	}

	protected function setErrorMessage($sErrorMessage=''){
		$this->setIsError(true);
		$sOldValue=$this->_sErrorMessage;
		$this->_sErrorMessage=$sErrorMessage;
		return $sOldValue;
	}

	public function isError(){
		return $this->_bIsError;
	}

	public function getErrorMessage(){
		return $this->_sErrorMessage;
	}

}
