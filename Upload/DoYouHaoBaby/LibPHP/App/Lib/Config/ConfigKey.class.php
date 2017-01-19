<?php
/* [DoYouHaoBaby!](C)Dianniu From 2010.
   文件系统配置键($)*/

!defined('DYHB_PATH') && exit;

class ConfigKey{

	private $_sStoreDirectory;
	private $_arrItems=array();
	private $_bUpdate=false;
	const ITEM_DATA_FILENAME='Config.php';

	public function __construct($sStoreDirectory){
		if(!is_dir($sStoreDirectory)){E(G::L('$sStoreDirectory必须为一个有效的目录。','LibDyhb',null));}
		if(!is_writable($sStoreDirectory)){E(G::L('目录无法写入:%s','LibDyhb',null,$sStoreDirectory));}
		$this->_sStoreDirectory=G::tidyPath($sStoreDirectory);
		$sItemDataFile=$this->_sStoreDirectory.'/'.self::ITEM_DATA_FILENAME;
		if(is_file($sItemDataFile)){
			$this->_bUpdate=false;
			$arrValue=(array)include $sItemDataFile;
			if(!$arrValue){$this->_arrItems=array();}
			else{$this->_arrItems=$arrValue;}
		}
		else{
			$this->_bUpdate=true;
			$this->_arrItems=array();
		}
	}

	public function __destruct(){
		if(is_dir($this->_sStoreDirectory)){$this->save();}
	}

	public function getName(){
		return basename($this->_sStoreDirectory);
	}

	public function save(){
		if(!$this->_bUpdate){return true;}
		$sItemDataFile=$this->_sStoreDirectory.'/'.self::ITEM_DATA_FILENAME;
		if(!file_put_contents($sItemDataFile,"<?php\n /* DoYouHaoBaby Framework Config File,Do not to modify this file! */ \n return ".var_export($this->_arrItems,true)."\n?>")){return false;}
		$this->_bUpdate=false;
		return true;
	}

	public function delete(){
		if(!@rmdir($this->_sStoreDirectory)){return false;}
		$this->_arrItems=array();
		$this->_bUpdate=false;
		return true;
	}

	public function setItem($sItemName,$Data){
		if(!is_scalar($Data)&& !is_array($Data)){E(G::L('ConfigKey::setItem()只能保存基本数据类型','LibDyhb'));}
		$sName=strval($sItemName);
		$this->_arrItems[$sName]=$Data;
		$this->_bUpdate=true;
	}

	public function getItem($sItemName){
		return array_key_exists($sItemName,$this->_arrItems)?$this->_arrItems[$sItemName]:null;
	}

	public function deleteItem($sItemName){
		if(array_key_exists($sItemName,$this->_arrItems)){
			unset($this->_arrItems[$sItemName]);
			return true;
		}else{
			return false;
		}
	}

	public function getItems(){
		return $this->_arrItems;
	}

	public function setItems(array $arrItems){
		$this->_arrItems=$arrItems;
		$this->_bUpdate=true;
	}

	public function clearItems(){
		$arrOldValue=$this->_arrItems;
		$this->_arrItems=array();
		return $arrOldValue;
	}

}
