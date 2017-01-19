<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   ModelRelationHasMany类封装了对象见的一对多关系($)*/

!defined('DYHB_PATH') && exit;

class ModelRelationHasMany extends ModelRelation{

	public $_bOneToOne=false;
	public $_onDelete='cascade';
	public $_sOnSave='save';

	public function init_(){
		if($this->_bInited){ return $this; }
		parent::init_();
		$arrP=$this->_arrInitConfig;
		$this->_sSourceKey=!empty($arrP['_sSourceKey'])?$arrP['_sSourceKey']:reset($this->_oSourceMeta->_arrIdName);
		$this->_sTargetKey=!empty($arrP['_sTargetKey'])?$arrP['_sTargetKey']:reset($this->_oSourceMeta->_arrIdName);
		unset($this->_arrInitConfig);
		return $this;
	}

	public function regCallback(array $arrAssocInfo){
		return $this;
	}

	public function addTarget(Model $oSource,Model $oTarget){
		$this->init_();
		$sMappingName=$this->_sMappingName;
		if(!isset($oSource->{$sMappingName})){
			$oSource->{$sMappingName}=array($oTarget);
		}
		else{
			$oSource[$sMappingName][]=$oTarget;
		}
	}

	public function onSourceSave(Model $oSource, $nRecursion){
		$this->init_();
		$sMappingName=$this->_sMappingName;
		if($this->_sOnSave=='skip' || $this->_sOnSave===false || !isset($oSource->{$sMappingName})){
			return $this;
		}
		$sSourceKeyValue=$oSource->{$this->_sSourceKey};
		foreach($oSource->{$sMappingName} as $oObj){
			$oObj->changePropForce($this->_sTargetKey,$sSourceKeyValue);
			$oObj->save($nRecursion-1,$this->_sOnSave);
		}
		return $this;
	}

	public function onSourceDestroy(Model $oSource){
		$this->init_();
		if($this->_onDelete === false || $this->_onDelete == 'skip'){ return $this; }
		$sSourceKeyValue=$oSource->{$this->_sSourceKey};
		$arrCond=array($this->_sTargetKey=>$sSourceKeyValue);
		if($this->_onDelete===true || $this->_onDelete=='cascade'){
			$this->_oTargetMeta->destroyWhere($arrCond);
		}
		elseif($this->_onDelete=='reject'){
			$arrRow=$this->_oTargetMeta->find($arrCond)->count()->query();
			if(intval($arrRow['row_count'])> 0){
				G::E(G::L('对象 %s 的关联 %s 拒绝了对象的删除操作.','dyhb',null,$this->_oSourceMeta->_sClassName,$this->_sMappingName));
			}
		}
		else{
			$sFill=($this->_onDelete=='set_null')?null:$this->_sOnDeleteSetValue;
			$this->_oTargetMeta->updateWhere($arrCond,array($this->_sTargetKey=>$sFill));
		}
		return $this;
	}

	public function addRelatedObject(Model $oSource,Model $oTarget){
		$this->init_();
		$oTarget->changePropForce($this->_sTargetKey,$oSource->{$this->_sSourceKey});
		$oTarget->save(0,$this->_sOnSave);
		return $this;
	}

}
