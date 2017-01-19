<?php
/* [DoYouHaoBaby!](C)Dianniu From 2010.
   ModelRelationManyToMany类封装了对象见的多对多关系($)*/

!defined('DYHB_PATH' ) && exit;

class ModelRelationManyToMany extends ModelRelation{

	public $_bOneToOne=false;

	public function init_(){
		if($this->_bInited){ return $this; }
		parent::init_();
		$arrP=$this->_arrInitConfig;
		if(empty($arrP['_sMidClass'] )){
			$this->_oMidMeta=null;// 如果没有指定中间表对应的 Model 类，则使用表数据入口直接处理中间表
			if(empty($arrP['_sMidTableName'])){
				$sT1=$this->_oSourceMeta->_oTable->_sName;// 尝试自动确定中间表名称
				$sT2=$this->_oTargetMeta->_oTable->_sName;
				if($sT1 <= $sT2 ){
					$sMidTableName=$sT1.'_has_'.$sT2;
				}
				else{
					$sMidTableName=$sT2.'_has_'.$sT1;
				}
			}
			else{
				$sMidTableName=$arrP['_sMidTableName'];
			}
			$this->_oMidTable=new DbTableEnter(array('name'=>$sMidTableName));
		}
		else{
			$this->_oMidMeta=ModelMeta::instance($arrP['_sMidClass']);// 如果中间表作为实体，则由指定的 Model 继承类负责处理中间表
			$this->_oMidTable=$this->_oMidMeta->_oTable;
		}
		$this->_sSourceKey=!empty($arrP['_sSourceKey'])?$arrP['_sSourceKey']:reset($this->_oSourceMeta->_arrIdName);
		$this->_sTargetKey=!empty($arrP['_sTargetKey'])?$arrP['_sTargetKey']:reset($this->_oTargetMeta->_arrIdName);
		$this->_sMidSourceKey=!empty($arrP['_sMidSourceKey'])?$arrP['_sMidSourceKey']:reset($this->_oSourceMeta->_arrIdName);
		$this->_sMidTargetKey=!empty($arrP['_sMidTargetKey'])?$arrP['_sMidTargetKey']:reset($this->_oTargetMeta->_arrIdName);
		$this->_sMidMappingTo=!empty($arrP['_sMidMappingTo'])?$arrP['_sMidMappingTo']:'mid_data';
		$sClass=$this->_oTargetMeta->_sClassName;
		unset($this->_arrInitConfig );
		return $this;
	}

	public function onSourceSave(Model $oSource,$nRecursion){
		return $this->updateRelation_($oSource,$nRecursion);
	}

	public function onSourceDestroy(Model $oSource){
		$oSource->{$this->_sMappingName}=array();
		return $this->updateRelation_($oSource);
	}

	public function bindRelatedObject(Model $oSource,Model $oTarget){
		$this->init_();
		if(!$this->_oMidMeta){
			$oConnect=$this->_oMidTable->getConnect();
			$oTarget->save($this->_sOnSave);
			$sSourceKeyValue=$oSource->{$this->_sSourceKey};
			$sTargetKeyValue=$oTarget->{$this->_sTargetKey};
			$sSql=sprintf('SELECT COUNT(*)FROM %s WHERE %s=%s AND %s=%s',
					$this->qualifyId($oConnect,$this->_oMidTable->getFullTableName()),
					$this->qualifyId($oConnect,$this->_sMidSourceKey),
					$this->qualifyStr($oConnect,$sSourceKeyValue),
					$this->qualifyId($oConnect,$this->_sMidTargetKey),
					$this->qualifyStr($oConnect,$sTargetKeyValue)
			);

			if(intval($oConnect->getOne($sSql))< 1){
				$this->_oMidTable->insert(
					array($this->_sMidSourceKey=>$sSourceKeyValue,$this->_sMidTargetKey=>$sTargetKeyValue)
				);
			}
		}
		return $this;
	}

	public function unbindRelatedObject(Model $oSource,Model $oTarget){}

	protected function updateRelation_(Model $oSource,$nRecursion=1){
		$this->init_();
		if(!isset($oSource->{$this->_sMappingName})){
			return $this;
		}
		if($this->_oMidMeta ){
			return $this->updateRelationByMeta_($oSource,$oSource->{$this->_sMappingName},$nRecursion);
		}
		else{
			return $this->updateRelationByTable_($oSource,$oSource->{$this->_sMappingName},$nRecursion);
		}
	}

	protected function updateRelationByMeta_(Model $oSource,$arrTargets,$nRecursion){
		foreach($arrTargets as $oObj){
			$oObj->save($nRecursion-1,$this->_sOnSave);
			$sV=$oObj->{$this->_sTargetKey};
			$arrTargets[$sV]=$oObj;
		}
		$sSourceKeyValue=$oSource->{$this->_sSourceKey};// 取出现有的关联信息
		$oExists=$this->_oMidMeta->find(array($this->_sMidSourceKey=>$sSourceKeyValue))
						->all()
						->asColl()
						->query();

		$arrAdded=array();// 然后确定要添加和删除的关联信息
		foreach($arrTargets as $oTarget){
			$sV=$oTarget->{$this->_sTargetKey};
			if(!$oExists->search($this->_sTargetKey,$sV)){
				$arrAdded[]=$sV;
			}
		}
		$arrRemoved=array();
		foreach($oExists as $oExist){
			$sV=$oExist->{$this->_sTargetKey};
			if(!isset($arrTargets[$sV])){
				$arrRemoved[]=$sV;
			}
		}

		foreach($arrAdded as $sMidTargetKeyValue){// 添加新增的关联信息
			$oMid=$this->_oMidMeta->newObj();
			$oMid->changePropForce($this->_sMidSourceKey,$sSourceKeyValue);
			$oMid->changePropForce($this->_sMidTargetKey,$sMidTargetKeyValue);
			$oMid->save();
		}

		if(!empty($arrRemoved)){// 删除多余的关联信息
			$this->_oMidMeta->destroyWhere("{$this->_sMidSourceKey}=? AND {$this->_sMidTargetKey} IN(?)",$sSourceKeyValue,$arrRemoved);
		}
		return $this;
	}

	protected function updateRelationByTable_(Model $oSource,$arrTargets,$nRecursion){
		$oConnect=$this->_oMidTable->getConnect();// 取出现有的关联信息
		$arrTargetKeyValues=array();
		foreach($arrTargets as $oObj){
			$oObj->save($this->_sOnSave,$nRecursion-1);
			$arrTargetKeyValues[]=$oObj->{$this->_sTargetKey};
		}
		$sSourceKeyValue=$oSource->{$this->_sSourceKey};
		$sSql=sprintf('SELECT %s FROM %s WHERE %s=%s',
			$this->qualifyId($oConnect,$this->_sMidTargetKey),
			$this->qualifyId($oConnect,$this->_oMidTable->getFullTableName()),
			$this->qualifyId($oConnect,$this->_sMidSourceKey),
			$this->qualifyStr($oConnect,$sSourceKeyValue)
		);

		$arrExistsMid=$oConnect->getCol($sSql);
		$arrInsertMid=array_diff($arrTargetKeyValues,$arrExistsMid);// 然后确定要添加和删除的关联信息
		$arrRemoveMid=array_diff($arrExistsMid,$arrTargetKeyValues);
		foreach($arrInsertMid as $sMidTargetKeyValue){// 添加新增的关联信息
			$this->_oMidTable->insert(array($this->_sMidSourceKey=>$sSourceKeyValue,$this->_sMidTargetKey=>$sMidTargetKeyValue));
		}
		
		if(!empty($arrRemoveMid )){// 删除多余的关联信息
			$this->_oMidTable->delete("{$this->_sMidSourceKey}=? AND {$this->_sMidTargetKey} IN(?)",$sSourceKeyValue,$arrRemoveMid);
		}
		return $this;
	}

}
