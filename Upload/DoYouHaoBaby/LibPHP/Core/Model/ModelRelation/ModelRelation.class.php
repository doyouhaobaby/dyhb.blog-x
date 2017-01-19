<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   ModelRelation封装Model之间的关联关系($)*/

!defined('DYHB_PATH') && exit;

class ModelRelation {

	public $_sSourceKey;
	public $_sTargetKey;
	public $_onFind='all';
	public $_onFindWhere=null;
	public $_sOnFindOrder=null;
	public $_onFindKeys='*';
	public $_onDelete='skip';
	public $_onDeleteSetValue=null;
	public $_sOnSave='skip';
	public $_sMidSourceKey;
	public $_sMidTargetKey;
	public $_midOnFindKeys=null;
	public $_sMidMappingTo;
	public $_bEnabled=true;
	public $_sType;
	protected $_bInited=false;
	public $_sMappingName;
	public $_sSourceKeyAlias;
	public $_sTargetKeyAlias;
	public $_oTargetMeta;
	public $_oSourceMeta;
	public $_oMidMeta;
	public $_oMidTable;
	public $_sTargetClass='';
	public $_bOneToOne=false;
	protected $_arrInitConfig;
	protected static $_arrInitConfigKeys=array(
	'_sMappingName',
	'_sSourceKey',
	'_sTargetKey',
	'_onFind',
	'_onFindWhere',
	'_sOnFindOrder',
	'_onFindKeys',
	'_onDelete',
	'_onDeleteSetValue',
	'_sOnSave',
	'_sMidSourceKey',
	'_sMidTargetKey',
	'_midOnFindKeys',
	'_sMidMappingTo',
	'_bEnabled',
	'_sTargetClass',
	);

	protected function __construct($sType,array $arrConfig,ModelMeta $oSourceMeta){
		$this->_sType=$sType;
		foreach(self::$_arrInitConfigKeys as $sKey){
			if(! empty($arrConfig[$sKey])){
				$this->{$sKey}=$arrConfig[$sKey];
			}
		}
		$this->_arrInitConfig=$arrConfig;
		$this->_oSourceMeta=$oSourceMeta;
	}

	static function create($sType,array $arrConfig,ModelMeta $oSourceMeta){
		if(empty($arrConfig['_sMappingName'])){// 缺少关联对象mapping_name属性，则抛出异常
			G::E(G::L('创建关联必须指定关联的 mapping_name 属性。' ,'dyhb'));
		}
		else{
			$arrConfig['_sMappingName']=$arrConfig['_sMappingName'];
		}

		if(!in_array($sType,array(Db::HAS_ONE,Db::HAS_MANY,Db::BELONGS_TO,Db::MANY_TO_MANY))){// $sType参数检测
			G::E(G::L('无效的关联类型 %s。','dyhb',null ,$sType));
		}

		switch($sType){// 根据不同的关联类型创建不同的关联类型
			case Db::HAS_ONE:
				return new ModelRelationHasOne(Db::HAS_ONE,$arrConfig,$oSourceMeta);
			case Db::HAS_MANY:
				return new ModelRelationHasMany(Db::HAS_MANY,$arrConfig,$oSourceMeta);
			case Db::BELONGS_TO:
				return new ModelRelationBelongsTo(Db::BELONGS_TO,$arrConfig,$oSourceMeta);
			case Db::MANY_TO_MANY:
				return new ModelRelationManyToMany(Db::MANY_TO_MANY, $arrConfig, $oSourceMeta);
			default:
				 G::E(G::L('无效的关联类型 %s。','dyhb',null ,$sType));
		}
	}

	public function init_(){
		if(!$this->_bInited){
			$this->_oTargetMeta=ModelMeta::instance($this->_arrInitConfig['_sTargetClass']);
			$this->_sSourceKeyAlias=$this->_sMappingName.'_source_key';
			$this->_sTargetKeyAlias=$this->_sMappingName.'_target_key';
			$this->_bInited=true;
		}
		return $this;
	}

	public function regCallback(array $arrAssocInfo){
		return $this;
	}

	protected function qualifyStr(DbConnect $oConnect,$sValue){
		$oDbQualifyStr=DbConnect::getFactory()->createDbQualifyStr($oConnect,$sValue);
		return $oDbQualifyStr->makeSql();
	}

	protected function qualifyId(DbConnect $oConnect,$sValue){
		$oDbQualifyId=DbConnect::getFactory()->createDbQualifyId($oConnect,$sValue);
		return $oDbQualifyId->makeSql();
	}

}
