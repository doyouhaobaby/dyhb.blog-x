<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模型行为扩展($)*/

!defined('DYHB_PATH') && exit;

abstract class ModelBehavior implements IModelCallback{

	protected $_oMeta;
	protected $_arrSettings=array();
	private $_arrDynamicMethods=array();
	private $_arrStaticMethods=array();
	private $_arrEventHandlers=array();
	private $_arrGetters=array();
	private $_arrSetters=array();
	protected $_bIsError=false;
	protected $_sLastErrorMessage;

	public function __construct(ModelMeta $oMeta,array $arrSettings){
		$this->_oMeta=$oMeta;// 设置元对象
		foreach($arrSettings as $sKey=>$sValue){
			if(array_key_exists($sKey,$this->_arrSettings)){
				$this->_arrSettings[$sKey]=$sValue;
			}
		}
		$this->bind();
	}

	static public function normalizeConfig($arrConfig){
		$arrRet=array();
		foreach($arrConfig as $key=>$value){
			if (is_int($key) && !is_array($value)){
				$arrRet[$value]=array();
			}
			else{
				$arrRet[$key]=$value;
			}
		}
		return $arrRet;
	}

	public function bind(){
		$this->addStaticMethod_('isBehaviorError',array($this,'isError')); // 判断模式扩展扩展是否出错
		$this->addStaticMethod_('getBehaviorErrorMessage',array($this,'getErrorMessage')); // 获取模式扩展的错误信息
	}

	public function unbind(){
		foreach($this->_arrDynamicMethods as $sMethodName){// 移除插件的动态方法
			$this->_oMeta->removeDynamicMethod($sMethodName);
		}
		foreach($this->_arrStaticMethods as $sMethodName){// 移除插件的动态方法
			$this->_oMeta->removeStaticMethod($sMethodName);
		}
		foreach($this->_arrEventHandlers as $arrValue){// 移除插件的事件处理函数
			list($sEventType,$callback)=$arrValue;
			$this->_oMeta->removeEventHandler($sEventType,$callback);
		}
		foreach($this->_arrGetters as $sPropName){// 移除插件的getter方法
			$this->_oMeta->unsetPropGetter($sPropName);
		}
		foreach($this->_arrSetters as $sPropName){// 移除插件的setter方法
			$this->_oMeta->unsetPropSetter($sPropName);
		}
	}

	protected function addDynamicMethod_($sMethodName,$Callback,$arrCustomParameters=array()){
		$this->_oMeta->addDynamicMethod($sMethodName,$Callback,$arrCustomParameters);
		$this->_arrDynamicMethods[]=$sMethodName;
	}

	protected function addStaticMethod_($sMethodName,$Callback,$arrCustomParameters=array()){
		$this->_oMeta->addStaticMethod($sMethodName,$Callback,$arrCustomParameters);
		$this->_arrStaticMethods[]=$sMethodName;
	}

	protected function addEventHandler_($nEventType,$Callback,$arrCustomParameters=array()){
		$this->_oMeta->addEventHandler($nEventType,$Callback,$arrCustomParameters);
		$this->_arrEventHandlers[]=array($nEventType,$Callback);
	}

	protected function setPropGetter_($sPropName,$Callback,$arrCustomParameters=array()){
		$this->_oMeta->setPropGetter($sPropName, $Callback,$arrCustomParameters);
		$this->_arrGetters[]=$sPropName;
	}

	protected function setPropSetter_($sPropName,$Callback,$arrCustomParameters=array()){
		$this->_oMeta->setPropSetter($sPropName,$Callback,$arrCustomParameters);
		$this->_arrSetters[]=$sPropName;
	}

	public function setIsError($bIsError=false){
		$bOldValue=$this->_bIsError;
		$this->_bIsError=$bIsError;
		return $bOldValue;
	}

	public function setErrorMessage($sErrorMessage=''){
	    self::setIsError(true);
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
