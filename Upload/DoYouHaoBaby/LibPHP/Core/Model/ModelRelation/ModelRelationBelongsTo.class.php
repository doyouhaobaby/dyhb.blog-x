<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   ModelAssociationBelongsTo类封装了对象见的隶属关系($)*/

!defined('DYHB_PATH') && exit;

class ModelRelationBelongsTo extends ModelRelation{

	public $_bOneToOne=true;
	public $_onDelete='skip';
	public $_sOnSave='skip';

	public function init_(){
		if($this->_bInited){ return $this; }
		parent::init_();
		$this->_sSourceKey=!empty($arrP['_sSourceKey'])?$arrP['_sSourceKey']:reset($this->_oTargetMeta->_arrIdName);
		$this->_sTargetKey=!empty($arrP['_sTargetKey'])?$arrP['_sTargetKey']:reset($this->_oTargetMeta->_arrIdName);
		unset($this->_arrInitConfig);
		return $this;
	}

	public function onSourceSave(Model $oSource,$nRecursion){
		return $this;
	}

	public function onSourceDestroy(Model $oSource){
		return $this;
	}

}
