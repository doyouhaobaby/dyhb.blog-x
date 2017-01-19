<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   MySQL 数据库记录集($) */

!defined('DYHB_PATH') && exit;

class DbRecordSetMysql extends DbRecordSet{

	public function free(){
		$hResult=$this->getQueryResultHandle();// 获取查询结果指针
		if($hResult){ mysql_free_result($hResult); }
		$this->setQueryResultHandle(null);
	}

	public function getCount(){
		if($this->_nCount>=0){// 直接返回结果
			return $this->_nCount;
		}
		$hResult=$this->getQueryResultHandle();// 获取查询结果指针
		$this->_nCount=mysql_num_rows($hResult);
		return $this->_nCount;
	}

	public function fetch(){
		$hResult=$this->getQueryResultHandle();
		if($this->_nFetchMode==Db::FETCH_MODE_ASSOC){// 以关联数组的方式返回数据库结果记录
			$arrRow=mysql_fetch_assoc($hResult);
			if($this->_bResultFieldNameLower && $arrRow){
				$arrRow=array_change_key_case($arrRow,CASE_LOWER);
			}
		}else{// 以索引数组的方式返回结果记录
			$arrRow=mysql_fetch_array($hResult);
		}
		return $arrRow;
	}

}
