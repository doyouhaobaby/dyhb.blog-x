<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模型($)*/

!defined('DYHB_PATH') && exit;

interface IModel{}

class Model implements IModel,IModelCallback,ArrayAccess{

	const AUTOFILL_DATETIME='DATETIME';
	const AUTOFILL_TIMESTAMP='TIMESTAMP';
	const AUTOFILL_DATE='DATE_'; // 与系统函数date()冲突 ，所以我们在后面加了一个‘_’分割
	const AUTOFILL_TIME='TIME_'; // 与系统函数time()冲突
	const MODEL_ALL='ALL'; // 任何时间填充
	const MODEL_CREATE='CREATE'; // 创建的时候填充
	const MODEL_UPDATE='UPDATE'; // 更新的时候填充
	const EXISTS_TO_CHECKDATE='EXIST'; // 存在字段即验证
	const MUST_TO_CHECKDATE='MUST'; // 必须验证
	const VALUE_TO_CHECKDATE='NOTEMPTY'; // 不为空即验证
	const FIELD_DATELINE='DATELINE'; // 任何时候用当前Linux时间戳进行填充
	const FIELD_CREATE_DATELINE='CREATE_DATELINE'; // 创建对象的时候使用当前Linux时间戳进行填充
	const FIELD_UPDATE_DATELINE='UPDATE_DATELINE'; // 更新对象的时候使用当前Linux时间戳进行填充
	private $_sName;
	protected $_bIsError=false;
	protected $_sErrorMessage;
	protected $_arrProp;
	protected $_sClassName;
	private static $_arrMeta;
	private $_arrChangedProp=array();
	protected $_id=false;
	protected $_bAutofill=true;

	public function __construct($Data=null,$sNamesStyle=Db::PROP,$bFromStorage=FALSE,$sName=''){
		if(empty($sName)){// 设置模型名字
			$sName=get_class($this);
		}
		if(empty($sNamesStyle)){
			$sNamesStyle=Db::PROP;
		}
		$this->setName($sName);
		$this->_sClassName=$sName;

		// 判断是否存在Meta对象，否则创建
		if (! isset(self::$_arrMeta[$this->_sClassName])){
			self::$_arrMeta[$this->_sClassName]=ModelMeta::instance($this->_sClassName);
		}
		$oMeta=self::$_arrMeta[$this->_sClassName];
		$this->_arrProp=$oMeta->_arrDefaultProp;
		if($Data!==null){// 如果$Data不为NULL，则改变属性值
			$this->changeProp($Data,$sNamesStyle,null,$bFromStorage,true);
		}
		$this->afterInit_();
		$this->event_(self::AFTER_INIT);
		$this->afterInitPost_();
	}

	public function id($bCached=true){
		if($bCached && $this->_id!==false){
			return $this->_id;
		}
		$arrId=array();
		if(is_array(self::$_arrMeta[$this->_sClassName]->_arrIdName)){
			foreach(self::$_arrMeta[$this->_sClassName]->_arrIdName as $sName){
				$arrId[$sName]=$this->{$sName};
			}
		}
		if (count($arrId)==1){
			$arrId=reset($arrId);
		}
		$this->_id=$arrId;
		return $arrId;
	}

	public function save($nRecursion=99,$sSaveMethod='save'){
		$this->makePostData();
		$sInheritTypeField=self::$_arrMeta[$this->_sClassName]->_sInheritTypeField;// 指定继承类名称的字段名
		if($sInheritTypeField && empty($this->_arrProp[ $sInheritTypeField ])){
			$this->_arrProp[$sInheritTypeField]=$this->_sClassName;
		}
		$this->beforeSave_();// 触发保存数据前事件
		$this->event_(self::BEFORE_SAVE);
		$this->beforeSavePost_();
		$arrId=$this->id(false); // 不使用缓存

		// 程序通过内置方法统一实现
		switch (strtolower($sSaveMethod)){
			case 'create':
				$this->create_($nRecursion);
				break;
			case 'update':
				$this->update_($nRecursion);
				break;
			case 'replace':
				$this->replace_($nRecursion);
				break;
			case 'save':
				default:
				if(!is_array($arrId)){
					if(empty($arrId)){// 单一主键的情况下，如果 $arrId 为空，则 create，否则 update
						$this->create_($nRecursion);
					}
					else{
						$this->update_($nRecursion);
					}
				}
				else{
					$this->replace_($nRecursion);// 复合主键的情况下，则使用 replace 方式
				}
				break;
		}

		// 触发保存后的事件
		$this->afterSave_();
		$this->event_(self::AFTER_SAVE);
		$this->_id=false; // 清除缓存
		return $this;
	}

	public function changePropForce($sPropName,$PropValue){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if (!isset($oMeta->_arrProp[ $sPropName])){
			return $this;
		}
		$bRo=$oMeta->_arrProp[$sPropName]['readonly'];
		self::$_arrMeta[$this->_sClassName]->_arrProp[$sPropName]['readonly']=false;
		$this->{$sPropName}=$PropValue;
		self::$_arrMeta[$this->_sClassName]->_arrProp[$sPropName]['readonly']=$bRo;
		return $this;
	}

	public function changeProp($Prop,$sNamesStyle=Db::PROP,$AttrAccessible=null,$bFromStorage=false,$bIignoreReadonly=false){
		$oMeta=self::$_arrMeta[ $this->_sClassName ];
		if ($AttrAccessible){
			$AttrAccessible=array_flip(Dyhb::normalize($AttrAccessible));
			$bCheckAttrAccessible=true;
		}
		else{
			$bCheckAttrAccessible=!empty($oMeta->_arrAttrAccessible);
			$AttrAccessible=$oMeta->_arrAttrAccessible;
		}
		foreach($Prop as $sPropName => $Value){// 将数组赋值给对象属性
			if($sNamesStyle == Db::FIELD){// 如果是字段，那么建立与元的映射，这样方便后面判断那些数据需要被写入数据库
				if(!isset($oMeta->_arrFieldToProp[ $sPropName])){ continue; }// 字段到属性的映射
				$sPropName=$oMeta->_arrFieldToProp[$sPropName];
			}
			elseif(!isset($oMeta->_arrProp[$sPropName])){
				continue;
			}
			if ($bFromStorage){
				if ($oMeta->_arrProp[$sPropName]['virtual']){
					$this->{$sPropName}=$Value;
					if(isset($this->_arrChangedProp[$sPropName])){
						unset($this->_arrChangedProp[$sPropName]);
					}
				}
				else{
					$this->_arrProp[$sPropName]=is_null($Value)? NULL : $this->dbFieldtypeCheck_($sPropName,$Value);
				}
			}
			else{
				if ($bCheckAttrAccessible){
					if (!isset($AttrAccessible[$sPropName])){ continue; }
				}
				elseif (isset($oMeta->_arrAttrProtected[$sPropName])){
					continue;
				}
				if ($bIignoreReadonly){
					$this->changePropForce($sPropName,$Value);
				}
				else{
					$this->{$sPropName}=$Value;
				}
			}
		}
		return $this;
	}

	public function get($sPropName){
		return $this->__get($sPropName);
	}

	public function &__get($sPropName){
		if (!isset(self::$_arrMeta[$this->_sClassName]->_arrProp[$sPropName ])){
			G::E(G::L('属性：%s不存在。','dyhb',null,$sPropName));
		}
		$arrConfig=self::$_arrMeta[$this->_sClassName]->_arrProp[$sPropName];
		// 如果指定了属性的 getter，则通过 getter 方法来获得属性值
		if (!empty($arrConfig['getter'])){
			list($callback,$arrCustomParameters)=$arrConfig['getter'];
			if(!is_array($callback)){
				$callback=array($this,$callback);
				$arrArgs=array($sPropName,$arrCustomParameters,&$this->_arrProp);
			}
			else{
				$arrArgs=array($this,$sPropName,$arrCustomParameters,&$this->_arrProp);
			}
			return call_user_func_array($callback,$arrArgs);
		}
		if(!isset($this->_arrProp[$sPropName]) && $arrConfig['relation']){
			$this->_arrProp[$sPropName]=self::$_arrMeta[$this->_sClassName]->relatedObj($this,$sPropName);
		}
		return $this->_arrProp[$sPropName];
	}

	public function set($sPropName,$Value){
		$this->__set($sPropName,$Value);
	}

	public function __set($sPropName,$Value){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if(!isset($oMeta->_arrProp[$sPropName])){
			G::E(G::L('属性：%s不存在。','dyhb',null,$sPropName));
		}
		$arrConfig=$oMeta->_arrProp[$sPropName];
		if($arrConfig['readonly']){
			G::E(G::L('属性：%s为只读，无法改变值','dyhb',null,$sPropName));
		}
		if(!empty($arrConfig['setter'])){
			list($callback,$arrCustomParameters)=$arrConfig['setter'];
			if(!is_array($callback)){
				$callback=array($this,$callback);
				$arrArgs=array($Value,$sPropName,$arrCustomParameters,&$this->_arrProp);
			}
			else {
				$arrArgs=array($this,$Value,$sPropName,$arrCustomParameters,&$this->_arrProp);
			}
			return call_user_func_array($callback,$arrArgs);
		}
		if(isset($arrCondig['relation']) and $arrConfig['relation']){
			if($arrConfig['relation']==Db::HAS_ONE || $arrConfig['relation']==Db::BELONGS_TO){
				A::INSTANCE($Value,$arrConfig['relation_class'],G::L('属性：%s的正确类型为：%s,而现在的类型为%s','dyhb',null,$sPropName,$arrConfig['relation_class'],gettype($Value)));
				$this->_arrProp[$sPropName]=$Value;
			}
			else{
				A::ISTHESE($Value,array('array','Iterator'));
				if(is_array($Value)){// 数组，则创建Coll对象
					$this->_arrProp[$sPropName]=Coll::createFromArray($Value,$arrConfig['relation_class']);
				}
				else{// 直接是Coll的话，就不用创建
					$this->_arrProp[$sPropName]=$Value;
				}
			}
			$this->_arrChangedProp[$sPropName]=$sPropName;// 设置属性为“脏”状态
		}
		elseif(array_key_exists($sPropName,$this->_arrProp) && $this->_arrProp[$sPropName]!==$Value){
			$this->_arrProp[$sPropName]=$this->dbFieldtypeCheck_($sPropName,$Value);
			$this->_arrChangedProp[$sPropName]=$sPropName; // 设置属性为“脏”状态
		}
	}

	public function setAutofill($bAutofill=true){
		$this->_bAutofill=$bAutofill;
	}

	public function __isset($sPropName){
		return array_key_exists($sPropName,$this->_arrProp);
	}

	public function __call($sMethod,array $arrArgs){
		if (isset(self::$_arrMeta[$this->_sClassName]->_arrMethods[$sMethod])){// 设置行为插件的方法
			$arrCallback=self::$_arrMeta[$this->_sClassName]->_arrMethods[$sMethod];// 获取回调函数
			foreach ($arrArgs as $sArg){
				array_push($arrCallback[1],$sArg);
			}
			array_unshift($arrCallback[1],$this);
			return call_user_func_array($arrCallback[0],$arrCallback[1]);// 执行回调函数
		}

		$sPrefix=substr($sMethod,0,3);// getXX()和 setXX()方法
		if ($sPrefix=='get'){
			$sPropName=substr($sMethod,3);
			return $this->{$sPropName};
		}
		elseif($sPrefix=='set'){
			$sPropName=substr($sMethod,3);
			$this->{$sPropName}=reset($arrArgs);
			return null;
		}

		G::E(G::L('类%s不存在方法%s','dyhb',null,$this->_sClassName,$sMethod));
	}

	public function offsetExists($sPropName){
		return array_key_exists($sPropName,$this->_arrProp);
	}

	public function offsetSet($sPropName,$Value){
		$this->{$sPropName}=$Value;
	}

	public function offsetGet($sPropName){
		return $this->{$sPropName};
	}

	public function offsetUnset($sPropName){
		$this->{$sPropName}=null;
	}

	static function collCallback_(){
		return array('tojson' =>'multiToJson');
	}

	static function multiToJson(array $arrObjects,$nRecursion=99,$sNamesStyle=Db::PROP){
		$arrValue=array();
		while(list(,$oObj)=each($arrObjects)){
			$arrValue[]=$oObj->toArray($nRecursion,$sNamesStyle);
		}
		return json_encode($arrValue);
	}

	protected function method_($sMethod){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		return $this->__call($sMethod,$arrArgs);
	}

	protected function event_($sEventName){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if(empty($oMeta->_arrCallback[$sEventName])){
			return;
		}

		// 触发回调事件
		foreach ($oMeta->_arrCallback[$sEventName] as $oCallback){
			array_unshift($oCallback[1],$this);
			call_user_func_array($oCallback[0],$oCallback[1]);
		}
	}

	protected function getPostName($sField){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		$arrMap=$oMeta->_arrPostMapField;
		if($arrMap){
			asort($arrMap);// 对调键和值
			if(isset($arrMap[$sField]) && $arrMap[$sField]!=''){
				return $arrMap[$sField];
			}
		}
		return $sField;
	}

	protected function makePostData(){
		foreach($this->_arrProp as $sField =>$value){
			$sPostName=$this->getPostName($sField);
			if(!isset($this->_arrChangedProp[$sField]) && isset($_POST[ $sPostName ])){
				$value=trim($_POST[ $sPostName ]);
				$this->_arrProp[$sField]=$value;
				$this->_arrChangedProp[$sField]=$value; // 设置属性为“脏”状态
			}
		}
	}

	protected function create_($nRecursion=99){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if($this->_bAutofill===true){
			$this->autoFill_('create');
		}
		$this->beforeCreate_();// 引发 before_create 事件
		$this->event_(self::BEFORE_CREATE);
		$this->beforeCreatePost_();

		if($this->check_('create',true)===false){// 进行create验证
			return false;
		}

		// 特别处理 BELONGS_TO 关联
		foreach($oMeta->_arrBelongstoProp as $sPropName=>$oRelation){
			$sMappingName=$oRelation->_sMappingName;
			$sSourceKey=$oRelation->_sSourceKey; // 关联中，识别来源对象的属性名，如果不设置则以来源对象的主键为准。
			if (empty($this->_arrProp[$sMappingName])){
				if (empty($this->_arrProp[$sSourceKey])){
					if($this->_arrProp[$sSourceKey]===$oMeta->_arrProp[$sSourceKey]['default_value'] && !is_null($oMeta->_arrProp[$sSourceKey]['default_value'])){
						$this->changePropForce($sSourceKey,$oMeta->_arrProp[$sSourceKey]['default_value']);
					}
					else{
						if(empty($oRelation->_oSourceMeta->_arrProp[$sMappingName]['relation_params']['skip_empty'])){
							A::ASSERT_(0);
						}
					}
				}
			}
			else{
				$oBelongsto=$this->_arrProp[$sMappingName];
				$this->changePropForce($sSourceKey,$oBelongsto->{$oRelation->_sTargetKey});// sTargetKey关联对象中的目标属性
			}
		}

		// 准备要保存到数据库的数据
		$arrSaveData=array();
		foreach($this->_arrProp as $sPropName=>$sValue){
			if (isset($oMeta->_arrCreateReject[$sPropName]) || (isset($oMeta->_arrProp[$sPropName]) and $oMeta->_arrProp[$sPropName ]['virtual'])){
				continue;
			}

			// 过滤NULL值
			if($sValue!==null){
				if(isset($oMeta->_arrPropToField[ $sPropName ])){
					$arrSaveData[$oMeta->_arrPropToField[ $sPropName ]]=$sValue;
				}
			}
		}

		// 将名值对保存到数据库
		$arrPk=$oMeta->_oTable->insert($arrSaveData,true);
		if($arrPk===false || $this->isTableError()){
			$this->setErrorMessage($this->getTableErrorMessage());
			return false;
		}

		// 将获得的主键值指定给对象
		foreach ($arrPk as $sFieldName=>$sFieldValue){
			if(isset($oMeta->_arrPropToField[$sFieldName])){
				$this->_arrProp[$oMeta->_arrPropToField[$sFieldName]]=$sFieldValue;
			}
		}

		// 遍历关联的对象，并调用对象的save()方法
		foreach($oMeta->_arrRelation as $sProp => $oRelation){
			if($oRelation->_sType==Db::BELONGS_TO || !array_key_exists($sProp,$this->_arrProp)){
				continue;
			}
			$oRelation->init_();
			$sSourceKeyValue=$this->{$oRelation->_sSourceKey};
			if(strlen($sSourceKeyValue)==0){
				A::ASSERT_(0);
			}
			$oRelation->onSourceSave($this,$nRecursion-1);// 保存源对象数据
		}

		// 引发after_create事件
		$this->afterCreate_();
		$this->event_(self::AFTER_CREATE);
		$this->afterCreatePost_();

		// 清除所有属性的“脏”状态
		$this->_arrChangedProp=array();
	}

	protected function update_($nRecursion=99){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if (!empty($this->_arrChangedProp)){
			if($this->_bAutofill===true){// 这里允许update和all
				$this->autofill_('update');
			}
			$this->beforeUpdate_();
			$this->event_(self::BEFORE_UPDATE);
			$this->beforeUpdatePost_();
			if($this->check_('update',true)===false){// 进行update验证
				return false;
			}
			$arrSaveData=array();
			foreach ($this->_arrProp as $sPropName=>$value){
				if (isset($oMeta->_arrUpdateReject[$sPropName]) || (isset($oMeta->_arrProp[$sPropName]) && $oMeta->_arrProp[$sPropName]['virtual'] && !isset($oMeta->_arrTableMeta[$sPropName]))){
					continue;
				}
				if (isset($this->_arrChangedProp[$sPropName]) && isset($oMeta->_arrPropToField[$sPropName])){
					$arrSaveData[$oMeta->_arrPropToField[$sPropName]]=$value;
				}
			}

			if (!empty($arrSaveData)){
				$arrConditions=array();
				foreach ($oMeta->_oTable->getPk()as $sFieldName){
					$sPropName=$oMeta->_arrFieldToProp[$sFieldName];
					unset($arrSaveData[$sFieldName]);
					$arrConditions[$sFieldName]=$this->_arrProp[$sFieldName];
				}

				if (!empty($arrSaveData)){
					$bResult =$oMeta->_oTable->update($arrSaveData,$arrConditions);
					if($bResult===false || $this->isTableError()){
						$this->setErrorMessage($this->getTableErrorMessage());
						return false;
					}
				}
			}
		}

		// 遍历关联的对象，并调用对象的save()方法
		foreach($oMeta->_arrRelation as $sProp=>$oRelation){
			if(!isset($this->_arrProp[$sProp])){
				continue;
			}
			$oRelation->init_();
			$sSourceKeyValue=$this->{$oRelation->_sSourceKey};
			if(strlen($sSourceKeyValue)== 0){
				A::ASSERT_(0);
			}
			$oRelation->onSourceSave($this,$nRecursion-1);
		}
		$this->afterUpdate_();
		$this->event_(self::AFTER_UPDATE);
		$this->afterUpdatePost_();
		$this->_arrChangedProp=array();// 清除所有属性的“脏”状态
	}

	protected function replace_($nRecursion=99){
		$arrChanges=$this->_arrChangedProp;
		if (empty($arrChanges)){return;}
		try {
			$bResult=$this->create_($nRecursion);// 数据库本身并不支持 replace 操作，所以只能是通过insert操作来模拟
		}catch (Exception $e) {
			$this->_arrChangedProp=$arrChanges;
			$this->update_($nRecursion);
		}
	}

	protected function autofill_($sMode=self::MODEL_ALL){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		$sMode=strtoupper($sMode); // 兼容大小写
		$arrFieldToProp =$oMeta->_arrFieldToProp;// 我们要求数据库字段都以小写为准
		if(array_key_exists(strtolower(self::FIELD_DATELINE),$arrFieldToProp)){// 兼容大小写，字段必须全部为小写&任何时候使用当前时间戳进行填充
			$this->changePropForce(strtolower(self::FIELD_DATELINE),CURRENT_TIMESTAMP);
		}
		if(strtoupper($sMode)==self::MODEL_CREATE and array_key_exists(strtolower(self::FIELD_CREATE_DATELINE),$arrFieldToProp)){// 创建对象的时候
			$this->changePropForce(strtolower(self::FIELD_CREATE_DATELINE),CURRENT_TIMESTAMP);
		}
		if(strtoupper($sMode)==self::MODEL_UPDATE and array_key_exists(strtolower(self::FIELD_UPDATE_DATELINE),$arrFieldToProp)){// 更新对象的时候
			$this->changePropForce(strtolower(self::FIELD_UPDATE_DATELINE),CURRENT_TIMESTAMP);
		}
		$arrFillProps= $oMeta->_arrAutofill;
		$arrData=$this->_arrProp;
		foreach($arrFillProps as $arrValue){
			$sField=array_key_exists(0,$arrValue)?$arrValue[0]:''; // 字段
			$sContent=array_key_exists(1,$arrValue)?$arrValue[1]:''; // 内容
			$sCondition=array_key_exists(2,$arrValue)?$arrValue[2]:''; // 填充条件
			$sExtend=array_key_exists(3,$arrValue)?$arrValue[3]:''; // 附加规则

			if (strtoupper($sContent)===self::AUTOFILL_DATETIME){// 对$sContent进行特殊处理
				$sContent=date('Y-m-d H:i:s',CURRENT_TIMESTAMP); // DATETIME
			}
			elseif(strtoupper($sContent)===self::AUTOFILL_TIMESTAMP){
				$sContent=intval(CURRENT_TIMESTAMP); // TIMESTAMP
			}
			elseif(strtoupper($sContent)===self::AUTOFILL_DATE){
				$sContent=date('Y-m-d',CURRENT_TIMESTAMP); // DATE_
			}
			elseif(strtoupper($sContent)===self::AUTOFILL_TIME){
				$sContent= date('H:i:s',CURRENT_TIMESTAMP); // TIME_
			}

			// 自动填充类型处理,处理类型为空，那么为all
			if(strtoupper($sMode)==strtoupper($sCondition) || strtoupper($sMode)==self::MODEL_ALL || strtoupper($sCondition)==self::MODEL_ALL || $sCondition==''){
				if($sExtend){// 调用附加规则
					switch($sExtend){
						case 'function':// 使用函数进行填充 字段的值作为参数
						case 'callback': // 使用回调方法
							$arrArgs=isset($arrValue[4])?$arrValue[4]:array();// 回调参数
							if(isset($arrData[$sField])){
								array_unshift($arrArgs,$arrData[$sField]);
							}
							if('function'==$sExtend){// funtion回调
								if(function_exists($sContent)){
									$arrData[$sField] =call_user_func_array($sContent,$arrArgs);
								}
								else{
									A::ASSERT_(0);
								}
							}
							else{
								if(is_array($sContent)){
									A::CALLBACK($sContent);
									$arrData[$sField]=call_user_func_array($sContent,$arrArgs);
								}
								else{
									$arrData[$sField]=call_user_func_array(array(&$this,$sContent),$arrArgs);
								}
							}
							break;
						case "field":
							$arrData[$sField]=$arrData[$sContent];
							break;
						case "string":
							$arrData[$sField]=strval($sContent);
							break;
					}
				}
				else{
					$arrData[$sField]=$this->dbFieldtypeCheck_($sField,$sContent);
				}
				$this->_arrChangedProp[$sField]=true;
			}
		}
		$this->_arrProp=$arrData;
		return $this->_arrProp;
	}

	protected function dbFieldtypeCheck_($sProp,$Data){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		if($GLOBALS['_commonConfig_']['DB_FIELDTYPE_CHECK'] && isset($oMeta->_arrProp[$sProp]['ptype'])){
			$Data=self::typed_($Data,$oMeta->_arrProp[$sProp]['ptype']);
		}
		return $Data;
	}

	public function check_($sMode){
		$this->beforeCheck_();
		$this->event_(self::BEFORE_CHECK);
		$this->beforeCheckPost_();

		$oMeta=self::$_arrMeta[$this->_sClassName];
		if ($sMode == 'create'){// 如果是创建验证，触发相关前置事件
			$this->beforeCheckOnCreate_();
			$this->event_(self::BEFORE_CHECK_ON_CREATE);
			$this->beforeCheckOnCreatePost_();
			$arrCheckProps=array_keys($oMeta->_arrPropToField);
			foreach($oMeta->_arrIdName as $sIdname){
				unset($arrCheckProps[ $sIdname ]);
			}
			$arrCheckProps= array_flip ($arrCheckProps);
		}
		elseif ($sMode == 'update'){// 如果是更新，触发相关前置事件
			$this->beforeCheckOnUpdate_();
			$this->event_(self::BEFORE_CHECK_ON_UPDATE);
			$this->beforeCheckOnUpdatePost_();
			$arrCheckProps=$this->changes();
		}
		$arrError=$oMeta->check($this->_arrProp,$arrCheckProps,$sMode);
		if(!empty($arrError)){
			$sErrorMessage='';
			foreach($arrError as $sField=>$arrValue){
				$sItemErrorMessage='';
				foreach($arrValue as $sK=>$sV){
					$sItemErrorMessage.=G::L('类型(%s)，消息 (%s)','dyhb',null,$sK,$sV).'<br/>';
				}
				$sErrorMessage.=G::L('字段%s的错误信息如下：%s','dyhb',null,$sField,$sItemErrorMessage).'<br/>';
			}
			$this->setErrorMessage($sErrorMessage);
			return false;
		}

		if ($sMode == 'create'){// 触发了创建时的事件
			$this->afterCheckOnCreate_();
			$this->event_(self::AFTER_CHECK_ON_CREATE);
			$this->afterCheckOnCreatePost_();
		}
		elseif ($sMode == 'update'){// 触发了更新的事件
			$this->afterCheckOnUpdate_();
			$this->event_(self::AFTER_CHECK_ON_UPDATE);
			$this->afterCheckOnUpdatePost_();
		}

		// 最后触发验证后的事件
		$this->afterCheck_();
		$this->event_(self::AFTER_CHECK);
		$this->afterCheckPost_();
	}

	public function getClassName(){
		return $this->_sClassName;
	}

	public function getMeta(){
		return self::$_arrMeta[$this->_sClassName];
	}

	public function getTableEnter(){
		return self::$_arrMeta[$this->_sClassName]->_oTable;
	}

	public function getDb(){
		return self::$_arrMeta[$this->_sClassName]->_oTable->getDb();
	}

	public function getTablePrefix(){
		return self::$_arrMeta[$this->_sClassName]->_oTable->getDb()->getConnect()->getTablePrefix();
	}

	public static function C($sId=null,$Data='',array $arrOption=null,$sBackendClass='FileCache',$bReturnObj=false){
		$arrOptionTwo=array('encoding_filename'=>false,'cache_path'=>APP_RUNTIME_PATH.'/Data/DbCache');
		if($arrOption!==null){$arrOption=array_merge($arrOptionTwo,$arrOption);}
		else{$arrOption=&$arrOptionTwo;}
		if($bReturnObj===true){return Dyhb::cache($sId,$arrOption,$sBackendClass,true);}
		if(is_null($Data)){
			return Dyhb::deleteCache($sId,$arrOption,$sBackendClass);
		}
		if($Data==''){
			$oldData=Dyhb::cache($sId,$arrOption,$sBackendClass);
			if($oldData!==false){
				return $oldData;
			}
			else{
				return false;
			}
		}
		else{
			Dyhb::writeCache($sId,$Data,$arrOption,$sBackendClass);
			return true;
		}
	}

	public function hasProp($sPropName){
		return isset(self::$_arrMeta[ $this->_sClassName ]->_arrProp[$sPropName]);
	}

	public function reload(){
		if (self::$_arrMeta[$this->_sClassName]->_nIdNameCount>1){
			$where=$this->id();// 复合主键
		}
		else{
			$where=array(reset(self::$_arrMeta[$this->_sClassName]->_arrIdName)=>$this->id());// 单一主键
		}
		$arrRow=self::$_arrMeta[$this->_sClassName]->find($where)->asArray()->recursion(0)->query();
		$this->changeProps($arrRow,Db::FIELD,null,true);
	}

	public function destroy(){
		$id=$this->id(false); // FIXED! 不使用缓存
		if (empty($id)){
			A::ASSERT_(0);
		}
		// 引发 before_destroy 事件
		$this->beforeDestroy_();
		$this->event_(self::BEFORE_DESTROY);
		$this->beforeDestroyPost_();
		$oMeta=self::$_arrMeta[$this->_sClassName];
		foreach($oMeta->_arrRelation as $oRelation){
			$oRelation->onSourceDestroy($this);
		}

		// 确定删除当前对象的条件
		if($oMeta->_nIdNameCount>1){
			$where=$id;
		}
		else{
			$where=array(reset($oMeta->_arrIdName)=>$id);
		}

		// 从数据库中删除当前对象
		$bResult=$oMeta->_oTable->delete($where);
		if($bResult===false || $this->isTableError()){
			$this->setErrorMessage($this->getTableErrorMessage());
			return false;
		}
		// 引发 after_destroy 事件
		$this->afterDestroy_();
		$this->event_(self::AFTER_DESTROY);
		$this->afterDestroyPost_();
	}

	static protected function typed_($Value,$sPtype){
		switch($sPtype){
			case 'int1':
			case 'int2':
			case 'int3':
			case 'int4':
			case 'timestamp':
				return intval($Value);
			case 'float':
			case 'double':
			case 'dec':
				 return doubleval($Value);
			case 'bool':
				return (bool)$Value;
			case 'date':
			case 'datetime':
				return empty($Value)?null:$Value;
		}
		return $Value;
	}

	public function changes(){
		return $this->_arrChangedProp;
	}

	public function changed($sPropsName=null){
		if (is_null($sPropsName)){// null 判读是否存在属性
			return !empty($this->_arrChangedProp);
		}
		$arrPropsName=Dyhb::normalize($sPropsName);
		foreach ($arrPropsName as $sPropName){
			if(isset($this->_arrChangedProp[$sPropName])){
				return true;
			}
		}
		return false;
	}

	public function willChanged($sPropsName){
		$arrPropsName=Dyhb::normalize($sPropsName);
		foreach($arrPropsName as $sPropsName){
			if(!isset(self::$_arrMeta[$this->_sClassName]->_arrProp[$sPropsName])){
				continue;
			}
			$this->_changedProp[$sPropsName]=$sPropsName;
		}
		return $this;
	}

	public function cleanChanges($Props=null){
		if ($Props){
			$arrProps=Dyhb::normalize($Props);
			foreach ($arrProps as $sProp){
				unset($this->_arrChangedProp[$sProp]);
			}
		}
		else{
			$this->_arrChangedProp=array();
		}
	}

	public function toArray($nRecursion=99,$sNamesStyle=Db::PROP){
		$arrData=array();
		$oMeta=self::$_arrMeta[$this->_sClassName];
		foreach ($oMeta->_arrProp as $sPropName=>$arrConfig){
			if ($sNamesStyle==Db::PROP){
				$sName=$sPropName;
			}
			else{
				$sName=$oMeta->_arrPropToField[$sPropName];
			}
			if($arrConfig['relation']){
				if($nRecursion>0 && isset($this->_arrProp[$sPropName])){
					$arrData[$sName]=$this->{$sPropName}->toArray($nRecursion-1,$sNamesStyle);
				}
			}
			elseif($arrConfig['virtual'] && empty($arrConfig['getter'])){
				continue;
			}
			else{
				$arrData[$sName]=$this->{$sPropName};
			}
		}
		return $arrData;
	}

	public function toJson($nRecursion=99,$sNamesStyle=Db::PROP){
		return json_encode($this->toArray($nRecursion,$sNamesStyle));
	}

	protected function isTableError(){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		return $oMeta->_oTable->isError();
	}

	protected function getTableErrorMessage(){
		$oMeta=self::$_arrMeta[$this->_sClassName];
		return $oMeta->_oTable->getErrorMessage();
	}

	public function __clone(){
	   foreach(self::$_arrMeta[$this->_sClassName]->_arrIdName as $sName){
			$this->_arrProp[$sName]=self::$_arrMeta[$this->_sClassName]->_oTableMeta[$sName]['default'];
		}
		$this->_id=false; // FIXED! 清除缓存
	}

	static public function getModelByGlobalName($sName){
		return isset(self::$_arrGlobalModels[$sName])?self::$_arrGlobalModels[$sName]:null;
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

	public function getName(){
		return $this->_sName;
	}

	public function setName($sName){
		$sOldVar=$this->_sName;
		$this->_sName=$sName;
		return $sOldVar;
	}

	protected function beforeCheck_(){}

	protected function beforeCheckPost_(){}

	protected function beforeCheckOnCreate_(){}

	protected function beforeCheckOnCreatePost_(){}

	protected function afterCheckOnCreate_(){}

	protected function afterCheckOnCreatePost_(){}

	protected function beforeCheckOnUpdate_(){}

	protected function beforeCheckOnUpdatePost_(){}

	protected function afterCheckOnUpdate_(){}

	protected function afterCheckOnUpdatePost_(){}

	protected function afterCheck_(){}

	protected function afterCheckPost_(){}

	protected function afterCreate_(){}

	protected function afterCreatePost_(){}

	protected function beforeUpdate_(){}

	protected function beforeUpdatePost_(){}

	protected function afterUpdate_(){}

	protected function afterUpdatePost_(){}

	protected function beforeDestroy_(){}

	protected function beforeDestroyPost_(){}

	protected function afterDestroy_(){}

	protected function afterDestroyPost_(){}

	protected function beforeCreate_(){}

	protected function beforeCreatePost_(){}

	protected function afterSave_(){}

	protected function afterSavePost_(){}

	protected function beforeSave_(){}

	protected function beforeSavePost_(){}

	protected function afterInit_(){ }

	protected function afterInitPost_(){ }

}
