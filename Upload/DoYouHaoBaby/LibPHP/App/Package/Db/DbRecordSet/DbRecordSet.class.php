<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   数据库 记录集($)*/

!defined('DYHB_PATH') && exit;

abstract class DbRecordSet{

	public $_nFetchMode;
	public $_bResultFieldNameLower=false;
	private $_arrData=array();
	protected $_nCount=0;
	protected $_oConnect;
	protected $_runSelectSql='';
	private $_hResult=null;

	public function __construct(DbConnect $oConnect,$nFetchMode=Db::FETCH_MODE_ARRAY){
		$this->_oConnect=$oConnect;
		$this->_nFetchMode=$nFetchMode;
	}

	public function __destruct(){
		$this->free();
	}

	public function setConnect(DbConnect $oConnect){
		$this->_oConnect=$oConnect;
		return $this;
	}

	public function getConnect(){
		return $this->_oConnect;
	}

	public function valid(){
		return $this->_hHandle!=null;
	}

	abstract public  function free();

	public function reset($sOption=null){
		if($sOption==null){
			$this->_arrOptions=self::$_arrOptionsInit;
			$this->_arrQueryParams=self::$_arrQueryParamsInit;
		}
		elseif(array_key_exists($sOption,self::$_arrOptionsInit)){
			$this->_arrOptions[$sOption]=self::$_arrOptionsInit[$sOption];
		}
		return $this;
	}

	public function query($Sql){
		$this->_runSelectSql=$Sql;
		$oConnect=$this->getConnect();// 执行查询
		$Res=$oConnect->query($Sql);
		$this->_nCount=-1;// 重置
		$this->_arrData=array();
		if(!$Res){
			return false;
		}
		$this->setQueryResultHandle($Res);
		return true;
	}

	public function setQueryResultHandle($hRes){
		$hOldValue=$this->_hResult;
		$this->_hResult=$hRes;
		return $hOldValue;
	}

	public function getQueryResultHandle(){
		return $this->_hResult;
	}

	abstract public function getCount();

	abstract public function fetch();

	public function getColumn($sColumn,$sSepa='-'){
		if(strpos($sColumn,',')){// 多个字段
			$arrRes=$this->getAllRows();
			if(!empty($arrRes)){
				$sColumn=explode(',',$sColumn);
				$sKey=array_shift($sColumn);
				$arrCols=array();
				foreach($arrRes as $sResult){
					$sName=$sResult[$sKey];
					$arrCols[$sName]='';
					foreach($sColumn as $sVal){$arrCols[$sName].=$sResult[$sVal].$sSepa;}
					$arrCols[$sName]=substr($arrCols[$sName],0,-strlen($sSepa));
				}
				return $arrCols;
			}
		}
		else{
			$arrResult=$this->fetch();
			if(!empty($arrResult)){
				return reset($arrResult);
			}
		}
		return null;
	}

	public function getRow($nRow=0){
		$arrRow=$this->fetch();
		if(isset($arrRow[$nRow])){
			return $arrRow[$nRow];
		}else{
			return null;
		}
	}

	public function getAllRows(){
		$arrRet=array();
		while($arrRow=$this->fetch()){
			$arrRet[]=$arrRow;
		}
		return $arrRet;
	}

	public function fetchCol($nCol=0){
		$nOldValue=$this->_nFetchMode;
		$this->_nFetchMode=Db::FETCH_MODE_ARRAY;
		$arrCols=array();
		while (($arrRow=$this->fetch())){
			$arrCols[]=$arrRow[$nCol];
		}
		$this->_nFetchMode=$nOldValue;
		return $arrCols;
	}

	public function fetchAllRefby(array $arrFields,&$arrFieldsValue,&$arrRef,$bCleanUp){
		$arrRef=$arrFieldsValue=$arrData=array();// 初始化查询参数
		$nOffset=0;
		if ($bCleanUp){// 获取结果后释放内存
			while(($arrRow=$this->fetch())){
				$arrData[$nOffset]=$arrRow;
				foreach ($arrFields as $sField){
					$sFieldValue=$arrRow[$sField];
					$arrFieldsValue[$sField][$nOffset]=$sFieldValue;
					$arrRef[$sField][$sFieldValue][]=&$arrData[$nOffset];
					unset($arrData[$nOffset][$sField]);
				}
				$nOffset++;
			}
		}
		else{
			while(($arrRow=$this->fetch())){
				$arrData[$nOffset]=$arrRow;
				foreach($arrFields as $sField){
					$sFieldValue=$arrRow[$sField];
					$fields_value[$sField][$nOffset]=$sFieldValue;
					$arrRef[$sField][$sFieldValue][]=&$arrData[$nOffset];
				}
				$nOffset++;
			}
		}
		return $arrData;
	}

	public function fetchObject($sClassName,$bReturnFirst=false){
		$arrObjs=array();
		$bIsAr=is_subclass_of($sClassName,'Model');
		while($arrRow=$this->fetch()){
			$oObj=$bIsAr?new $sClassName($arrRow,Db::FIELD,true):new $sClassName($arrRow);
			if($bReturnFirst)return $oObj;
			$arrObjs[]=$oObj;
		}
		return Coll::createFromArray($arrObjs,$sClassName);
	}

}
