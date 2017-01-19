<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   配置类($) */

!defined('DYHB_PATH') && exit;

class Config{

	private $_oKey;

	public function __construct($sStoreDirectory){
		$this->_oKey=new ConfigKey($sStoreDirectory);
	}

	public function setItem($sItemName,$Data){
		return $this->_oKey->setItem($sItemName,$Data);
	}

	public function getItem($sItemName){
		return $this->_oKey->getItem($sItemName);
	}

	public function deleteItem($sItemName){
		return $this->_oKey->deleteItem($sItemName);
	}

	public function hasItem($sItemName){
		try{$ItemValue=$this->getItem($sItemName);}
		catch(Exception $oE){return false;}
		return($ItemValue!==null);
	}

	public function getItems(){
		return $this->_oKey->getItems();
	}

	public function cleanItems(){
		return $this->_oKey->cleanItems();
	}

}
