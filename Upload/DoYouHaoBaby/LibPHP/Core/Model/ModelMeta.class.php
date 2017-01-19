<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   ModelMeta元模式($)*/

!defined('DYHB_PATH')&& exit;

class ModelMeta{

	public $_arrMethods=array();
	public $_arrStaticMethods=array();
	public $_arrDefaultProp=array();
	public $_arrCallback=array();
	public $_arrAttrAccessible=array();
	public $_arrAttrProtected=array();
	public $_arrPropToField=array();
	public $_arrFieldToProp=array();
	public $_arrProp=array();
	protected static $_arrMeta=array();
	public $_arrBelongstoProp=array();
	public $_arrRelation=array();
	public $_sInheritTypeField;
	public $_arrIdName;
	public $_nIdNameCount;
	public $_sSourceKey;
	public $_sInheritBaseClass;
	public $_oTable;
	public $_arrTableMeta;
	public $_arrCheck=array();
	public $_arrCreateReject=array();
	public $_arrUpdateReject=array();
	public $_arrAutofill=array();
	public $_arrPostMapField=array();
	protected $_arrBehaviors=array();
	protected static $_arrRelationTypes=array(
	Db::HAS_ONE,
	Db::HAS_MANY,
	Db::BELONGS_TO,
	Db::MANY_TO_MANY
	);
	protected static $_arrCheckOptions=array(
	'allow_null'=>false,
	'check_all_rules'=>false
	);
	protected $_sLastErrorMessage='';
	protected $_bIsError=false;
	protected static $_arrMapConfigKeys=array(
	'mapping_name'=>'_sMappingName',
	'source_key'=>'_sSourceKey',
	'target_key'=>'_sTargetKey',
	'on_find'=>'_onFind',
	'on_find_where'=>'_onFindWhere',
	'on_find_order'=>'_sOnFindOrder',
	'on_find_keys'=>'_onFindKeys',
	'on_delete'=>'_onDelete',
	'on_delete_set_value'=>'_onDeleteSetValue',
	'on_save'=>'_sOnSave',
	'mid_source_key'=>'_sMidSourceKey',
	'mid_target_key'=>'_sMidTargetKey',
	'mid_on_find_keys'=>'_midOnFindKeys',
	'mid_mapping_to'=>'_sMidMappingTo',
	'mid_class'=>'_sMidClass',
	'enabled'=>'_bEnabled',
	'target_class'=>'_sTargetClass',
	);

	protected function __construct($sClass){
		$this->init_($sClass);
	}

	static public  function instance($sClass){
		if (!isset(self::$_arrMeta[$sClass])){
			self::$_arrMeta[$sClass]=new self($sClass);
			self::$_arrMeta[$sClass]->initInstance_();
		}
		return self::$_arrMeta[$sClass];
	}

	public function find(){
		return $this->findByArgs(func_get_args());
	}

	public function findByArgs(array $arrArgs=array()){
		$oSelect=new DbSelect($this->_oTable->getConnect());
		$oSelect->asColl()->from($this->_oTable)->asObj($this->_sClassName);
		$nC=count($arrArgs);
		if ($nC>0){
			if($nC==1 && is_int($arrArgs[0]) && $this->_nIdNameCount==1){
				$oSelect->where(array(reset($this->_arrIdName)=> $arrArgs[0]));
			}
			else{
				call_user_func_array(array($oSelect,'where'),$arrArgs);
			}
		}
		if ($this->_sInheritBaseClass && $this->_sInheritTypeField){
			$select->where(array($this->_sInheritTypeField => $this->_sClassName));// 如果是来自某个继承类的查询，则限定只能查询该类型的对象
		}
		return $oSelect;
	}

	public function updateWhere(array $arrAttribs){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		$arrObjs=$this->findByArgs($arrArgs)->all()->query();
		foreach($arrObjs as $oObj){
			$oObj->changeProps($arrAttribs);
			$oObj->save(0,'update');
			unset($oObj);
		}
	}

	public function updateDbWhere(){
		$arrArgs=func_get_args();
		call_user_func_array(array($this->_oTable,'update'),$arrArgs);
	}

	public function destroyWhere(){
		$arrObjs=$this->findByArgs(func_get_args())->all()->query();
		$nC=count($arrObjs);
		foreach ($arrObjs as $oObj){
			$oObj->destroy();
			unset($oObj);
		}
		return $nC;
	}

	public function deleteWhere(){
		$arrArgs=func_get_args();
		call_user_func_array(array($this->_oTable,'delete'),$arrArgs);
		if($this->_oTable->isError()){
			$this->setErrorMessage($this->getErrorMessage());
			return false;
		}
	}

	public function newObj(array $Data=null){
		return new $this->_sClassName($Data);
	}

	public function addRelation($sPropName,$sRelationType,array $arrConfig){
		if(!in_array($sRelationType,array(Db::HAS_ONE,Db::HAS_MANY,Db::BELONGS_TO,Db::MANY_TO_MANY))){
			A::ASSERT_(0);
		}
		switch ($sRelationType){
			case Db::HAS_ONE:
			case Db::HAS_MANY:
				 if (empty($arrConfig['target_key'])){
					 $arrConfig['target_key']=strtolower(substr($this->_sClassName,0,-5)).'_id';
				 }
				 break;
			case Db::BELONGS_TO:
				if (empty($arrConfig['source_key'])){
					$arrConfig['source_key']=strtolower($arrConfig['relation_class']).'_id';
				}
				break;
			case Db::MANY_TO_MANY:
				if(empty($arrConfig['mid_source_key'])){
					$arrConfig['mid_source_key']=strtolower($this->_sClassName).'_id';
				}
				if(empty($arrConfig['mid_target_key'])){
					$arrConfig['mid_target_key']=strtolower($arrConfig['relation_class']).'_id';
				}
		}

		$arrRelation=$arrConfig['relation_params'];
		$arrRelation['mapping_name']=$sPropName;
		$arrRelation['target_class']=$arrConfig['relation_class'];
		if(isset($arrRelation[$sRelationType])){unset($arrRelation[$sRelationType]);}
		$arrRelationMap=array();// 建立传递过来的数据映射
		foreach($arrRelation as $sKey=>$value){
			if(isset(self::$_arrMapConfigKeys[$sKey])){
				$arrRelationMap[self::$_arrMapConfigKeys[$sKey]]=$value;
			}
		}

		// 创建一个关联对象
		$oRelation=ModelRelation::create($sRelationType,$arrRelationMap,$this);
		$oRelation->regCallback($arrRelationMap);
		$this->_arrRelation[$sPropName]=$oRelation;
		if($oRelation->_sType==Db::BELONGS_TO){
			$oRelation->init_();
			$this->_arrBelongstoProp[$oRelation->_sSourceKey]=$oRelation;
		}
		return $oRelation;
	}

	public function removeRelation($sPropName){
		unset($this->_arrProp[$sPropName]);
		unset($this->_arrRelations[$sPropName]);
		return $this;
	}

	public function relatedObj(Model $oObj,$sPropName){
		if(!class_exists($this->_arrProp[$sPropName]['relation_class'])){
			A::ASSERT_(0);
		}

		$oTargetMeta=self::instance($this->_arrProp[$sPropName]['relation_class']);
		$oRelation=$this->relation($sPropName)->init_();
		$sourceKeyValue=$oObj->{$oRelation->_sSourceKey};
		if(empty($sourceKeyValue)){
			if($oRelation->_bOneToOne){
				return $oTargetMeta->newObj();
			}
			else{
				return new ModelRelationColl($oTargetMeta->_sClassName);
			}
		}

		switch($oRelation->_sType){
			case Db::HAS_ONE:
			case Db::HAS_MANY:
			case Db::BELONGS_TO:
				$oSelect=$oTargetMeta->find(array($oRelation->_sTargetKey=>$sourceKeyValue));
				break;
			case Db::MANY_TO_MANY:
				$oRelation->_oMidTable->init();
				$oSelect=$oTargetMeta->find("[{$oRelation->_sTargetKey}]=[m.{$oRelation->_sMidTargetKey}]")->joinInner(array('m'=>$oRelation->_oMidTable), null,"[{$oRelation->_sMidSourceKey}]=?", $sourceKeyValue);
				break;
			default:
				G::E(G::L('无效的关联类型 %s.','dyhb',null,$oRelation->_sType));
		}

		if(!empty($oRelation->_onFindWhere)){// where条件
			call_user_func_array(array($oSelect,'where'),$oRelation->_onFindWhere);
		}

		if(!empty($oRelation->_sOnFindOrder)){// order条件
			$oSelect->order($oRelation->_sOnFindOrder);
		}

		if($oRelation->_onFind==='all' || $oRelation->_onFind===true){// 是否全部记录
			$oSelect->all();
		}
		elseif(is_int($oRelation->_onFind)){// 一定条件记录
			$oSelect->limit(0,$oRelation->_onFind);
		}
		elseif(is_array($oRelation->_onFind)){// 范围
			$oSelect->limit($oRelation->_onFind[0],$oRelation->_onFind[1]);
		}

		if($oRelation->_bOneToOne){// 一对一
			$arrObjects=$oSelect->query();
			if(count($arrObjects)){
				return(is_object($arrObjects))?$arrObjects->first():reset($arrObjects);
			}
			else{
				return $oTargetMeta->newObj();
			}
		}
		else{// 一对多
			return $oSelect->asColl()->query();
		}
	}

	public function relation($sPropName){
		if(!isset($this->_arrRelation[$sPropName])){
			A::ASSERT_(0);
		}
		return $this->_arrRelation[$sPropName];
	}

	public function addProp($sPropName, array $arrConfig){
		if(isset($this->_arrProp[ $sPropName ])){
			G::E(G::L('尝试添加的属性 %s 已经存在。','dyhb',null,$sPropName));
		}
		$arrConfig=array_change_key_case($arrConfig,CASE_LOWER);
		$arrParams=array('relation' =>false);
		$arrParams['readonly']=isset($arrConfig['readonly'])?(bool)$arrConfig['readonly']:false;
		if(!empty($arrConfig['field_name'])){// 确定属性和字段名之间的映射关系
			$sFieldName=$arrConfig['field_name'];
		}
		else{
			$sFieldName=isset($this->_arrTableMeta[$sPropName])?$this->_arrTableMeta[$sPropName]['name']:$sPropName;
		}
		$this->_arrPropToField[$sPropName]=$sFieldName;
		$this->_arrFieldToProp[$sFieldName]=$sPropName;
		$sMetaKeyName=strtolower($sFieldName);// 根据数据表的元信息确定属性是否是虚拟属性
		if(!empty($this->_arrTableMeta[ $sMetaKeyName ])){
			$arrParams['virtual']=false;// 如果是非虚拟属性，则根据数据表的元信息设置属性的基本验证策略
			$arrFieldMeta=$this->_arrTableMeta[$sMetaKeyName];
			$arrParams['default_value']=$arrFieldMeta['default'];
			$arrParams['ptype']=$arrFieldMeta['ptype'];
		}
		else{
			$arrParams['virtual']=true;
			$arrParams['default_value']=null;
			$arrParams['ptype']='varchar';
		}

		foreach(self::$_arrRelationTypes as $sType){// 处理对象聚合
			if(empty($arrConfig[$sType])){
				continue;
			}
			$arrParams['relation']=$sType;
			$arrParams['relation_class']=$arrConfig[$sType];
			$arrParams['relation_params']=$arrConfig;
			$this->_arrRelation[$sPropName]=$arrParams;
		}

		if(!$arrParams['virtual'] || $arrParams['relation']){// 设置属性信息
			$this->_arrDefaultProp[$sPropName]=$arrParams['default_value'];
		}
		$this->_arrProp[$sPropName]=$arrParams;

		if(!empty($arrConfig['setter'])){
			$arrSetterParams=!empty($arrConfig['setter_params'])?(array)$arrConfig['setter_params']:array();// 设置 getter 和 setter
			if(is_array($arrConfig['setter'])){
				$this->setPropSetter($sPropName, $arrConfig['setter'], $arrSetterParams);
			}
			else{
				if(strpos($arrConfig['setter'],'::')){
					$arrConfig['setter']=explode('::',$arrConfig['setter']);
				}
				$this->setPropSetter($sPropName, $arrConfig['setter'], $arrSetterParams);
			}
		}

		if(!empty($arrConfig['getter'])){
			$arrGetterParams=!empty($arrConfig['getter_params'])?(array)$arrConfig['getter_params']:array();
			if(is_array($arrConfig['getter'])){
				$this->setPropGetter($sPropName, $arrConfig['getter'], $arrGetterParams);
			}
			else{
				if(strpos($arrConfig['getter'],'::')){
					$arrConfig['getter']=explode('::',$arrConfig['getter']);
				}
				$this->setPropGetter($sPropName,$arrConfig['getter'],$arrGetterParams);
			}
		}
		return $this;
	}

	public function hasBindBehavior($sName){
		return isset($this->_arrBehaviors[$sName])?true:false;
	}

	public function bindBehaviors($Behaviors,array $arrConfig=null){
		$arrBehaviors=Dyhb::normalize($Behaviors);
		if (!is_array($arrConfig)){
			$arrConfig=array();
		}
		else{
			$arrConfig=array_change_key_case($arrConfig,CASE_LOWER);
		}
		foreach($arrBehaviors as $sName){
			$sName=strtolower($sName);
			if(isset($this->_arrBehaviors[$sName])){// 已经绑定过的插件不再绑定
				continue;
			}
			$sClass='ModelBehavior'.ucfirst($sName);// 载入插件
			$arrSettings=(!empty($arrConfig[$sName]))?$arrConfig[$sName]:array();// 构造行为插件
			$this->_arrBehaviors[$sName]=new $sClass($this, $arrSettings);
		}
		return $this;
	}

	public function unbindBehaviors($Behaviors){
		$arrBehaviors=Dyhb::normalize($Behaviors);
		foreach ($arrBehaviors as $sName){
			$sName=strtolower($sName);
			if (!isset($this->_arrBehaviors[$sName])){
				continue;
			}
			$this->_arrBehaviors[$sName]->unbind();
			unset($this->_arrBehaviors[$sName]);
		}
		return $this;
	}

	public function addDynamicMethod($sMethodName,$callback,array $arrCustomParameters=array()){
		if(!empty($this->_arrMethods[$sMethodName])){
			G::E(G::L('指定的动态方法名 %s 已经存在于 %s 对象中.','dyhb',null,$sMethodName,$this->_sClassName));
		}
		$this->_arrMethods[$sMethodName]=array($callback,$arrCustomParameters);
		return $this;
	}

	public function removeDynamicMethod($sMethodName){
		unset($this->_arrMethods[$sMethodName]);
		return $this;
	}

	public function addStaticMethod($sMethodName,$callback,array $arrCustomParameters=array()){
		if(!empty($this->_arrStaticMethods[$sMethodName])){
			G::E(G::L('指定的静态方法名 %s 已经存在于 %s 对象中.','dyhb',null,$sMethodName,$this->_sClassName));
		}
		$this->_arrStaticMethods[$sMethodName]=array($callback,$arrCustomParameters);
		return $this;
	}

	public function removeStaticMethod($sMethodName){
		unset($this->_arrStaticMethods[$sMethodName]);
		return $this;
	}

	public function setPropSetter($sPropName,$hCallback,array $arrCustomParameters=array()){
		if (isset($this->_arrProp[$sPropName])){
			$this->_arrProp[$sPropName]['setter']=array($hCallback,$arrCustomParameters);
		}
		else{
			$this->addProp($sPropName,array('setter'=>$hCallback,'setter_params'=>$arrCustomParameters));
		}
		return $this;
	}

	public function unsetPropSetter($sPropName){
		if (isset($this->_arrProp[$sPropName])){
			unset($this->_arrProp[$sPropName]['setter']);
		}
		return $this;
	}

	public function setPropGetter($sName,$hCallback,array $arrCustomParameters=array()){
		if (isset($this->_arrProp[$sName])){
			$this->_arrProp[$sName]['getter']=array($hCallback,$arrCustomParameters);
		}
		else{
			$this->addProp($sName,array('getter'=>$hCallback,'getter_params'=>$arrCustomParameters));
		}
	}

	public function unsetPropGetter($sPropName){
		if(isset($this->_arrProp[$sPropName])){
			unset($this->_arrProp[$sPropName]['getter']);
		}
		return $this;
	}

	public function addEventHandler($sEventType,$Callback,array $arrCustomParameters=array()){
		$this->_arrCallback[$sEventType][]=array($Callback,$arrCustomParameters);
		return $this;
	}

	public function removeEventHandler($sEventType,$Callback){
		if (empty($this->_arrCallback[$sEventType])){
			return $this;
		}
		foreach($this->_arrCallback[$sEventType] as $offset=>$arrValue){
			if($arrValue[0]==$Callback){
				unset($this->_arrCallback[$sEventType][$offset]);
				return $this;
			}
		}
		return $this;
	}

	private function init_($sClass){
		$this->_sClassName=$sClass;
		$arrRef=(array)call_user_func(array($sClass,'init__'));
		if (!empty($arrRef['inherit'])){
			$this->_sInheritBaseClass=$arrRef['inherit'];
			 $arrBaseRef=(array)call_user_func(array($this->_sInheritBaseClass,'init__'));// 继承类的 init__()方法只需要指定与父类不同的内容
			 $arrRef=array_merge_recursive($arrBaseRef,$arrRef);
		}

		$this->_sInheritTypeField=!empty($arrRef['inherit_type_field'])?$arrRef['inherit_type_field']:null;// 被继承的类
		$arrTableConfig=!empty($arrRef['table_config'])?(array)$arrRef['table_config']:array();// 设置表数据入口对象
		if (!empty($arrRef['table_class'])){
			$this->_oTable=$this->tableByClass_($arrRef['table_class'],$arrTableConfig);
		}
		else{
			$this->_oTable=$this->tableByName_($arrRef['table_name'],$arrTableConfig);
		}

		$this->_arrTableMeta=$this->_oTable->columns();
		if(empty($arrRef['props']) || ! is_array($arrRef['props'])){// 根据字段定义确定字段属性
			$arrRef['props']=array();
		}

		foreach($arrRef['props'] as $sPropName=>$arrConfig){
			$this->addProp($sPropName,$arrConfig);
		}

		foreach($this->_arrTableMeta as $sPropName=>$field){// 将没有指定的字段也设置为对象属性
			if (isset($this->_arrPropToField[$sPropName]))continue;
			$this->addProp($sPropName,$field);
		}

		// 设置其他选项
		if(!empty($arrRef['create_reject'])){
			$this->_arrCreateReject=array_flip(Dyhb::normalize($arrRef['create_reject']));
		}
		if(!empty($arrRef['update_reject'])){
			$this->_arrUpdateReject=array_flip(Dyhb::normalize($arrRef['update_reject']));
		}
		if(!empty($arrRef['post_map_field'])){
			$this->_arrPostMapField=$arrRef['postMapField'];
		}
		if(!empty($arrRef['autofill']) && is_array($arrRef['autofill'])){
			$this->_arrAutofill=$arrRef['autofill'];
		}
		if(!empty($arrRef['attr_accessible'])){
			$this->_arrAttrAccessible=array_flip(Dyhb::normalize($arrRef['attr_accessible']));
		}
		if(!empty($arrRef['attr_protected'])){
			$this->_arrAttrProtected=array_flip(Dyhb::normalize($arrRef['attr_protected']));
		}

		// 准备验证规则
		if(empty($arrRef['check']) || ! is_array($arrRef['check'])){
			$arrRef['check']=array();
		}
		$this->_arrCheck=$this->prepareCheckRules_($arrRef['check']);
		$arrPk=$this->_oTable->getPk();// 设置对象ID属性名
		$this->_arrIdName=array();
		foreach($this->_oTable->getPk() as $sPk){
			$sPn=$this->_arrFieldToProp[$sPk];
			$this->_arrIdName[$sPn]=$sPn;
		}
		$this->_nIdNameCount=count($this->_arrIdName);
		if (isset($arrRef['behaviors'])){// 绑定行为插件
			$arrCconfig=isset($arrRef['behaviors_settings'])?$arrRef['behaviors_settings']:array();
			$this->bindBehaviors($arrRef['behaviors'],$arrCconfig);
		}
	}

	protected function prepareCheckRules_($arrPolicies,array $arrRef=array(),$bSetPolicy=true){
		$arrCheck=$this->_arrCheck;
		foreach($arrPolicies as $sPropName=>$arrPolicie){
			if(!is_array($arrPolicie)){
				continue;
			}
			$arrCheck[$sPropName]=array('check'=>self::$_arrCheckOptions,'rules'=>array());
			if(isset($this->_arrPropsToField[$sPropName])){
				$sFn=$this->_arrPropsToField[$sPropName];
				if(isset($this->_arrTableMeta[ $sFn ])){
					$arrCheck[$sPropName]['check']['allow_null']=!$this->_arrTableMeta[$sFn]['not_null'];
				}
			}

			if(!$bSetPolicy){
				unset($arrCheck[$sPropName]['check']);
			}
			foreach($arrPolicie as $sOption=>$rule){
				if(isset($arrCheck[$sPropName]['policy'][$sOption])){
					$arrCheck[$sPropName]['policy'][$sOption]=$rule;
				}
				elseif($sOption==='on_create' || $sOption==='on_update'){// 解析 on_create 和 on_update 规则
					$rule=array($sOption=>(array)$rule);
					$arrRet=$this->prepareCheckRules_($rule, $arrCheck[$sPropName]['rules'],false);
					$arrCheck[$sPropName][$sOption]=$arrRet[$sOption];
				}
				elseif($sOption==='include'){
					$arrInclude=Dyhb::normalize($rule);
					foreach($arrInclude as $sRuleName){
						if(isset($arrRef[$sRuleName])){
							$arrCheck[$sPropName]['rules'][$sRuleName]=$arrRef[$sRuleName];
						}
					}
				}
				elseif(is_array($rule)){
					if(is_string($sOption)){
						$sRuleName=$sOption;
					}
					else{
						$sRuleName=$rule[0];
						if(is_array($sRuleName)){
							$sRuleName=$sRuleName[count($sRuleName)-1];
						}
						if(isset($arrCheck[$sPropName]['rules'][$sRuleName])){
							$sRuleName.='_'.($sOption+1);
						}
					}
					$arrCheck[$sPropName]['rules'][$sRuleName]=$rule;
				}
				else{
					G::E(G::L('指定了无效的验证规则 %s.','dyhb',null,$sOption.' - '.$rule));
				}
			}
		}
		return $arrCheck;
	}

	protected function tableByName_($sTableName,array $arrTableConfig=array()){
		$arrTableConfig=$this->parseDsn($arrTableConfig,$sTableName);
		$oTable=Dyhb::instance('DbTableEnter',$arrTableConfig);
		return $oTable;
	}

	protected function tableByClass_($sTableClass,array $arrTableConfig=array()){
		$arrTableConfig=$this->parseDsn($arrTableConfig,$sTableClass,true);
		$oTable=Dyhb::instance($sTableClass,$arrTableConfig);
		return $oTable;
	}

	protected function parseDsn($arrTableConfig, $sTableName,$bByClass=false){
		if (is_array($arrTableConfig) && E::oneImensionArray($arrTableConfig)){
			if($bByClass===false){$arrTableConfig['table_name']=$sTableName;}
			$arrDsn[]=$arrTableConfig;
		}
		else{
			if($bByClass===false){
				foreach($arrTableConfig as $nKey=>$arrValue){
					if($bByClass===false){$arrTableConfig[$nKey]['table_name']=$sTableName;}
				}
			}
			$arrDsn=$arrTableConfig;
		}
		return $arrDsn;
	}

	private function initInstance_(){
		foreach(array_keys($this->_arrRelation) as $sPropName){
			$arrConfig=$this->_arrRelation[$sPropName];
			if(is_array($arrConfig)){
				$this->addRelation($sPropName, $arrConfig['relation'],$arrConfig);
			}
		}
	}

	public function changes(){
		return $this->_arrChangedProps;
	}

	public function check(array $arrData,$arrProps=null,$sMode='all'){
		if (!is_null($arrProps)){
			$arrProps=Dyhb::normalize($arrProps,',',true);// 这里不过滤空值
		}
		else{
			$arrProps=$this->_arrPropToField;
		}

		$arrError=array();
		if(empty($sMode))$sMode='';// 初始化模式
		$sMode='on_' . strtolower($sMode);
		foreach($this->_arrCheck as $sProp=>$arrPolicy){
			if(!isset($arrProps[$sProp])){
				continue;
			}
			if(!isset($arrData[$sProp])){
				$arrData[$sProp]=null;
			}
			if(isset($this->_arrBelongstoProp[$sProp]) && empty($arrPolicy['rules'])){
				continue;
			}
			if(isset($arrPolicy[$sMode])){
				$arrPolicy=$arrPolicy[$sMode];
			}
			if(is_null($arrData[$sProp])){
				if(isset($this->_autofill[$sProp])){// 对于 null 数据，如果指定了自动填充，则跳过对该数据的验证
					continue 2;
				}
				if (isset($arrPolicy['policy'])&& !$arrPolicy['policy']['allow_null']){// allow_null 为 false 时，如果数据为 null，则视为验证失败
					$arrError[$sProp]['not_null']='not null';
				}
				elseif(empty($arrPolicy['rules'])){
					continue;
				}
			}

			foreach ($arrPolicy['rules'] as $sIndex => $arrRule){// 验证规则
				$sExtend='';// 附加规则
				if(array_key_exists('extend',$arrRule)){
					 $sExtend=strtolower($arrRule['extend']);
					 unset($arrRule['extend']);
				}
				$sCondition='';// 验证条件
				if(array_key_exists('condition',$arrRule)){
	 				 $sCondition=strtolower($arrRule['condition']);
	 				 unset($arrRule['condition']);
				}
				$sTime='';// 验证时间
				if(array_key_exists('time',$arrRule)){
	 				 $sTime=strtolower($arrRule['time']);
	 				 unset($arrRule['time']);
				}

				$sCheck=array_shift($arrRule);// 验证规则
				$sMsg=array_pop($arrRule);// 验证消息
				array_unshift($arrRule,$arrData[$sProp]);
				$arrCheckInfo=array('field'=>$sProp,'extend'=>$sExtend,'message'=>$sMsg,'check'=>$sCheck,'rule'=>$arrRule);	// 组装成验证信息
				if($sTime!='' and $sTime!='all' and $sMode!='on_'.$sTime){// 如果设置了验证时间，且验证时间不为all，而且验证时间不合模式相匹配，那么路过验证
					continue;
				}

				$bResult=true;
				switch(strtoupper($sCondition)){// 判断验证条件
					case Model::MUST_TO_CHECKDATE:// 必须验证不管表单是否有设置该字段
						$bResult=$this->checkField_($arrData,$arrCheckInfo);
						break;
					case Model::VALUE_TO_CHECKDATE:// 值不为空的时候才验证
						if(isset($arrData[$sProp]) and '' != trim($arrData[$sProp]) and 0!= trim($arrData[$sProp])){
							$bResult=$this->checkField_($arrData,$arrCheckInfo);
						}
						break;
					default:// 默认表单存在该字段就验证
						if(isset($arrData[$sProp])){
							$bResult=$this->checkField_($arrData,$arrCheckInfo);
						}
						break;
				}

				if($bResult===Check::SKIP_OTHERS){
					break;
				}
				elseif(!$bResult || $this->isError()){
					$arrError[ $sProp ][$sIndex]=$this->getErrorMessage();
					$this->setIsError(false);// 还原，防止下次认证错误
					$this->_sLastErrorMessage ='';
					if(isset($arrPolicy['policy']) && !$arrPolicy['policy']['check_all_rules']){
						break;
					}
				}
			}
		}
		return $arrError;
	}

	private function checkField_($arrData,$arrCheckInfo){
		 $bResult=true;
		 switch($arrCheckInfo['extend']){
			case 'function':// 使用函数进行验证
			case 'callback':// 调用方法进行验证
				$arrArgs=isset($arrCheckInfo['rule'])?$arrCheckInfo['rule']:array();
				if(isset($arrData['field'])){
					array_unshift($arrArgs,$arrData['field']);
				}
				if('function'==$arrCheckInfo['extend']){
					if(function_exists($arrCheckInfo['extend'])){
						$bResult =call_user_func_array($arrCheckInfo['check'], $arrArgs);
					}
					else{
						A::ASSERT_(0);
					}
				}
				else{
					if(is_array($arrCheckInfo['check'])){// 如果$sContent为数组，那么该数组为回调，先检查一下
						A::ISCALLBACK($arrCheckInfo['check']);// 检查是否为有效的回调
					}
					else{// 否则使用模型中的方法进行填充
						$oModel=null;
						eval('$oModel=new '.$this->_sClassName.'();');
						$bResult = call_user_func_array(array($oModel,$arrCheckInfo['check']),$arrArgs);
					}
				}

				if($bResult===false){
					if(empty($arrCheckInfo['message']))$arrCheckInfo['message']=G::L('模型回调验证失败','dyhb');
					$this->setErrorMessage($arrCheckInfo['message']);
				}
				return $bResult;
				break;
			case 'confirm': // 验证两个字段是否相同
				$bResult=$arrData[$arrCheckInfo['field']]==$arrData[$arrCheckInfo['check']];
				if($bResult===false){
					if(empty($arrCheckInfo['message']))$arrCheckInfo['message']=G::L('模型验证两个字段是否相同失败','dyhb');
					$this->setErrorMessage($arrCheckInfo['message']);
				}
				return $bResult;
				break;
			case 'in': // 验证是否在某个数组范围之内
				 $bResult= in_array($arrData[$arrCheckInfo['field']],$arrData[$arrCheckInfo['check']]);
				 if($bResult===false){
					if(empty($arrCheckInfo['message']))$arrCheckInfo['message'] =G::L('模型验证是否在某个范围失败','dyhb');
					$this->setErrorMessage($arrCheckInfo['message']);
				}
				return $bResult;
				break;
			case 'equal': // 验证是否等于某个值
				$bResult= $arrData[$arrCheckInfo['field']]==$arrCheckInfo['check'];
				if($bResult===false){
					if(empty($arrCheckInfo['message']))$arrCheckInfo['message']=G::L('模型验证是否等于某个值失败','dyhb');
					$this->setErrorMessage($arrCheckInfo['message']);
				}
				return $bResult;
				break;
			case 'regex':
			default:  // 默认使用正则验证 可以使用验证类中定义的验证名称
				$oCheck =Check::RUN();
				$bResult=Check::checkByArgs($arrCheckInfo['check'], $arrCheckInfo['rule']);
				if($bResult==Check::SKIP_OTHERS){
					break;
				}
				if($bResult===false || $oCheck->isError()){
					if(empty($arrCheckInfo['message']))$arrCheckInfo['message']=$oCheck->getErrorMessage();
					$this->setErrorMessage($arrCheckInfo['message']);
					return $bResult;
				}
				break;
		 }
		 return $bResult;
	}

	public function addCheck($sPropName, $Check){
		$arrP=array($sPropName=>array($Check));
		$arrR=$this->prepareCheckRules_($arrP);
		if (!empty($arrR[ $sPropName ]['rules'])){
			foreach($arrR[$sPropName]['rules'] as $rule){
				$this->_arrCheck[ $sPropName ]['rules'][]=$rule;
			}
		}
		return $this;
	}

	public function propCheck($sPropName){
		if(isset($this->_arrCheck[$sPropName])){
			return $this->_arrCheck[$sPropName];
		}
		return array('policy'=>self::$_arrCheckOptions,'rules'=>array());
	}

	public function allCheck(){
		return $this->_arrCheck;
	}

	public function removePropCheck($sPropName){
		if (isset($this->_arrCheck[ $sPropName ])){
			unset($this->_arrCheck[ $sPropName ]);
		}
		return $this;
	}

	public function removeAllCheck(){
		$this->_arrCheck=array();
		return $this;
	}

	public function __call($sMethodName,array $arrArgs){
		if (isset($this->_arrStaticMethods[$sMethodName])){
			$Callback=$this->_arrStaticMethods[$sMethodName];
			foreach ($arrArgs as $arg){
				array_push($Callback[1],$arg);
			}
			return call_user_func_array($Callback[0],$Callback[1]);
		}
		G::E(G::L('未定义的方法 %s.','dyhb',null,$sMethodName));
	}

	public function setIsError($bIsError=false){
		$bOldValue=$this->_bIsError;
		$this->_bIsError=$bIsError;
		return $bOldValue;
	}

	public function setErrorMessage($sErrorMessage=''){
		$this->setIsError(true);
		$sOldValue=$this->_sLastErrorMessage;
		$this->_sLastErrorMessage=$sErrorMessage;
		return $sOldValue;
	}

	public function isError(){
		return $this->_bIsError;
	}

	public function getErrorMessage(){
		return $this->_sLastErrorMessage;
	}

}
