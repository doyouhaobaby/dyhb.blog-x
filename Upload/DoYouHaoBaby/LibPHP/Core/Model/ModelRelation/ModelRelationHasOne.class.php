<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   ModelRelationHasOne类封装了对象见的一对一关系($)*/

!defined('DYHB_PATH') && exit;

class ModelRelationHasOne extends ModelRelationHasMany{

	public $_bOneToOne=true;
	public $_sOnSave='replace';

	public function onSourceSave(Model $oSource,$nRecursion){
		$this->init_();
		$sMappingName=$this->_sMappingName;
		if($this->_sOnSave==='skip' || $this->_sOnSave===false || !isset($oSource->{$sMappingName})){
			return $this;
		}
		$sSourceKeyValue=$oSource->{$this->_sSourceKey};
		$oObj=$oSource->{$sMappingName};
		$oObj->changePropForce($this->_sTargetKey,$sSourceKeyValue);
		$oObj->save($nRecursion-1,$this->_sOnSave);
		return $this;
	}

	public function addRelatedObject(Model $oSource,Model $oTarget){
		return $this;
	}

}
