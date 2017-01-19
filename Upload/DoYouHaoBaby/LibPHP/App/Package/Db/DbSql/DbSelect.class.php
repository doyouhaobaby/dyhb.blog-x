<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   SQL Select 子句($) */

!defined('DYHB_PATH') && exit;

class DbSelect{

	const DISTINCT='distinct';
	const COLUMNS='columns';
	const FROM='from';
	const UNION='union';
	const WHERE='where';
	const GROUP='group';
	const HAVING='having';
	const ORDER='order';
	const LIMIT_COUNT='limitcount';
	const LIMIT_OFFSET='limitoffset';
	const LIMIT_QUERY='limitquery';
	const FOR_UPDATE='forupdate';
	const AGGREGATE='aggregate';
	const USED_LINKS='used_links';
	const NON_LAZY_QUERY='non_lazy_query';
	const AS_ARRAY='as_array';
	const AS_COLL='as_coll';
	const LINK_FOR_RECURSION='link_for_recursion';
	const PAGE_SIZE='page_size';
	const PAGE_BASE='page_base';
	const CURRENT_PAGE='current_page';
	const PAGED_QUERY='paged_query';
	const INNER_JOIN='inner join';
	const LEFT_JOIN='left join';
	const RIGHT_JOIN='right join';
	const FULL_JOIN='full join';
	const CROSS_JOIN='cross join';
	const NATURAL_JOIN='natural join';
	const RECURSION='recursion';
	const SQL_WILDCARD='*';
	const SQL_SELECT='SELECT';
	const SQL_UNION='UNION';
	const SQL_UNION_ALL='UNION ALL';
	const SQL_FROM='FROM';
	const SQL_WHERE='WHERE';
	const SQL_DISTINCT='DISTINCT';
	const SQL_GROUP_BY='GROUP BY';
	const SQL_ORDER_BY='ORDER BY';
	const SQL_HAVING='HAVING';
	const SQL_FOR_UPDATE='FOR UPDATE';
	const SQL_AND='AND';
	const SQL_AS='AS';
	const SQL_OR='OR';
	const SQL_ON='ON';
	const SQL_ASC='ASC';
	const SQL_DESC='DESC';
	const SQL_COUNT='COUNT';
	const SQL_MAX='MAX';
	const SQL_MIN='MIN';
	const SQL_AVG='AVG';
	const SQL_SUM='SUM';
	protected static $_arrOptionsInit=array(
	self::DISTINCT=>false,
	self::COLUMNS=>array(),
	self::AGGREGATE=>array(),
	self::UNION=>array(),
	self::FROM=>array(),
	self::WHERE=>null,
	self::GROUP=>array(),
	self::HAVING=>null,
	self::ORDER=>array(),
	self::LIMIT_COUNT=>1,
	self::LIMIT_OFFSET=>null,
	self::LIMIT_QUERY=>false,
	self::FOR_UPDATE=>false
	);
	protected static $_arrAggregateTypes=array(
	self::SQL_COUNT=>self::SQL_COUNT,
	self::SQL_MAX=>self::SQL_MAX,
	self::SQL_MIN=>self::SQL_MIN,
	self::SQL_AVG=>self::SQL_AVG,
	self::SQL_SUM=>self::SQL_SUM
	);
	protected static $_arrJoinTypes=array(
	self::INNER_JOIN=>self::INNER_JOIN,
	self::LEFT_JOIN=>self::LEFT_JOIN,
	self::RIGHT_JOIN=>self::RIGHT_JOIN,
	self::FULL_JOIN=>self::FULL_JOIN,
	self::CROSS_JOIN=>self::CROSS_JOIN,
	self::NATURAL_JOIN=>self::NATURAL_JOIN
	);
	protected static $_arrUnionTypes=array(
	self::SQL_UNION=>self::SQL_UNION,
	self::SQL_UNION_ALL=>self::SQL_UNION_ALL
	);
	protected static $_arrQueryParamsInit=array(
	self::USED_LINKS=>array(),
	self::NON_LAZY_QUERY=>array(),
	self::AS_ARRAY=>true,
	self::AS_COLL=>false,
	self::RECURSION=>1,
	self::LINK_FOR_RECURSION=>null,
	self::PAGED_QUERY=>false
	);
	protected $_arrOptions=array();
	protected $_arrQueryParams;
	protected $_currentTable;
	protected $_arrJoinedTables=array();
	protected $_arrColumnsMapping=array();
	private static $_nQueryId=0;
	protected $_arrLinks=array();
	protected $_oMeta;
	protected $_bForUpdate=false;
	private $_oConnect;
	private $_oSubSqlGroup=null;
	private $_sSubSqlGroup=null;
	private $_oSubSqlReturnColumnList=null;
	private $_sSubSqlReturnColumnList=null;
	private $_oSubSqlOn=null;
	private $_sSubSqlOn=null;
	protected $_sLastSql='';

	public function __construct(DbConnect $oConnect=null){
		$this->_oConnect=$oConnect;// 初始化数据
		self::$_nQueryId ++;
		$this->_arrOptions=self::$_arrOptionsInit;
		$this->_arrQueryParams=self::$_arrQueryParamsInit;
	}

	public function setConnect(DbConnect $oConnect){
		$this->_oConnect=$oConnect;
		return $this;
	}

	public function getConnect(){
		return $this->_oConnect;
	}

	public function getLastSql(){
		return $this->_sLastSql;
	}

	public function getCount(){
		$hHandle=$this->getQueryHandle();
		if($hHandle===false){return false;}
		return $hHandle->getCount();
	}

	public function getCounts($Field='*',$sAlias='row_count'){
		$arrRow=$this->count($Field,$sAlias)->query();
		return $arrRow[$sAlias];
	}

	public function getAvg($Field,$sAlias='avg_value'){
		$arrRow=$this->avg($Field,$sAlias)->query();
		return $arrRow[$sAlias];
	}

	public function getMax($Field,$sAlias='max_value'){
		$arrRow=$this->max($Field,$sAlias)->query();
		return $arrRow[$sAlias];
	}

	public function getMin($Field,$sAlias='min_value'){
		$arrRow=$this->min($Field,$sAlias)->query();
		return $arrRow[$sAlias];
	}

	public function getSum($Field,$sAlias='sum_value'){
		$arrRow=$this->sum($Field,$sAlias)->query();
		return $arrRow[$sAlias];
	}

	public function get($nNum=null,$IncludedLinks=null){
		if(!is_null($nNum)){
			return $this->top($nNum)->query($IncludedLinks);
		}
		else{
			return $this->query($IncludedLinks);
		}
	}

	public function getById($Id,$IncludedLinks=null){
		if($this->_oMeta->_nIdNameCount !=1){
			G::E(G::L('getById 方法只适用于单一主键模型' ,'DbDyhb'));
		}
		return $this->where(array(reset($this->_oMeta->_sIdName)=>$Id))->getOne($IncludedLinks);
	}

	public function getOne($IncludedLinks=null){
		return $this->one()->query($IncludedLinks);
	}

	public function getAll($IncludedLinks=null){
		if($this->_arrOptions[self::LIMIT_QUERY]){
			return $this->query($IncludedLinks);
		}
		else{
			return $this->all()->query($IncludedLinks);
		}
	}

	public function getColumn($sColumn,$sSepa='-'){
		if(strpos($sColumn,',')){// 多个字段
			$this->all();
		}
		$this->setColumns($sColumn);
		$hHandle=$this->getQueryHandle();
		if($hHandle===false){return false;}
		return $hHandle->getColumn($sColumn,$sSepa);
	}

	public function query($arrIncludedLinks=null){
		$this->_arrQueryParams[self::NON_LAZY_QUERY]=Dyhb::normalize($arrIncludedLinks);
		if($this->_arrQueryParams[self::AS_ARRAY]){
			return $this->queryArray_(true);
		}
		else{
			return $this->queryObjects_();
		}
	}

	public function getQueryHandle(){
		$sSql=$this->makeSql();// 构造查询 SQL，并取得查询中用到的关联
		$nOffset=$this->_arrOptions[self::LIMIT_OFFSET];
		$nCount=$this->_arrOptions[self::LIMIT_COUNT];
		if(is_null($nOffset)&& is_null($nCount)){
			$result=$this->_oConnect->exec($sSql);
			return $result;
		}
		else{
			$result=$this->_oConnect->selectLimit($sSql,$nOffset,$nCount);
			return $result;
		}
	}

	public function __call($sMethod,array $arrArgs){
		if(strncasecmp($sMethod,'get',3)===0){
			$sMethod=substr($sMethod,3);
			if(strpos(strtolower($sMethod),'start')!==false){//support get10start3 etc.
				$arrValue=explode('start',strtolower($sMethod));
				$nNum=intval(array_shift($arrValue));
				$nOffset=intval(array_shift($arrValue));
				return $this->limit($nOffset - 1,$nNum);
			}
			elseif(strncasecmp($sMethod,'By',2)===0){// support getByName getByNameAndSex etc.
				$sMethod=substr($sMethod,2);
				$arrKeys=explode('And',$sMethod);
				if(count($arrKeys)!=count($arrArgs)){
					G::E(G::L('参数数量不对应','DbDyhb'));
				}
				return $this->where(array_change_key_case(array_combine($arrKeys,$arrArgs),CASE_LOWER))->getOne();
			}
			elseif(strncasecmp($sMethod,'AllBy',5)===0){// support getAllByNameAndSex etc.
				$sMethod=substr($sMethod,5);
				$arrKeys=explode('And',$sMethod);
				if(count($arrKeys)!=count($arrArgs)){
					G::E(G::L('参数数量不对应','DbDyhb'));
				}
				return $this->where(array_change_key_case(array_combine($arrKeys,$arrArgs),CASE_LOWER))->getAll();
			}
			return $this->top(intval(substr($sMethod,3)));
		}
		elseif(method_exists($this->_oMeta->_sClassName,'find_'.$sMethod)){// ArticleModel::F()->hot()->getOne()	,static method `find_on_hot` must define in ArticleModel
			array_unshift($arrArgs,$this);
			return call_user_func_array(array($this->_oMeta->_sClassName,'find_'.$sMethod),$arrArgs);
		}
		G::E(G::L('DbSelect 没有实现魔法方法 %s.','DbDyhb',null,$sMethod));
	}

	protected function queryArray_($bCleanUp=true){
		$hHandle=$this->getQueryHandle();
		if($hHandle===false)return false;
		if($this->_arrQueryParams[self::RECURSION]>0 && $this->_arrQueryParams[self::USED_LINKS]){
			$arrRefsValue=null;// 对关联表进行查询，并组装数据
			$arrRefs=null;
			$arrUsedAlias=array_keys($this->_arrQueryParams[self::USED_LINKS]);
			$arrRowset=$hHandle->fetchAllRefby($arrUsedAlias,$arrRefsValue,$arrRefs,$bCleanUp);
			$arrKeys=array_keys($arrRowset);
			foreach($this->_arrQueryParams[self::USED_LINKS] as $oLink){// 进行关联查询，并组装数据集
				foreach($arrKeys as $sKey){// ModelRelation
					$arrRowset[$sKey][$oLink->_sMappingName]=$oLink->_sOneToOne ?null :array();
				}
				$sTka=$oLink->_sTargetKeyAlias;
				$sSka=$oLink->_sSourceKeyAlias;
				if(empty($arrRefsValue[$sSka])){
					continue;
				}
				$oSelect=$oLink->_sTargetTable
							->find("[{$oLink->_sTargetKey}] IN(?)",$arrRefsValue[$sSka])
							->recursion($this->_arrQueryParams[self::RECURSION] - 1)
							->linkForRecursion($oLink)
							->order($oLink->_sOnFindOrder)
							->select($oLink->_onFindKeys)
							->where($oLink->_onFindWhere);
				if($oLink->_sType==Db::MANY_TO_MANY){
					$oSelect->join($oLink->_sMidTable->_sName,"[{$oLink->_sMidTargetKey}]=[{$oLink->_sTargetKey}]");
				}
				if(is_int($oLink->_onFind)){
					$oSelect->limit(0,$oLink->_onFind);
				}
				elseif(is_array($oLink->_onFind)){
					$oSelect->limit($oLink->_onFind[0],$oLink->_onFind[1]);
				}
				else{
					$oSelect->all();
				}
				$arrTargetRowset=$oSelect->queryArray(false);
				if($oLink->_sOnFind===1){
					$arrTargetRowset=array(
						$arrTargetRowset
					);
				}
				if($oLink->_sOneToOne){// 组装数据集
					foreach(array_keys($arrTargetRowset)as $offset){
						$sV=$arrTargetRowset[$offset][$sTka];
						unset($arrTargetRowset[$offset][$sTka]);
						$nI=0;
						foreach($arrRefs[$sTka][$sV] as $arrRow){
							$refs[$sTka][$sV][$nI][$oLink->_sMappingName]=$arrTargetRowset[$offset];
							unset($refs[$sTka][$sV][$nI][$sSka]);
							$nI ++;
						}
					}
				}
				else{
					foreach(array_keys($arrTargetRowset)as $offset){
						$sV=$arrTargetRowset[$offset][$sTka];
						unset($arrTargetRowset[$offset][$sTka]);
						$nI=0;
						foreach($arrRefs[$sSka][$sV] as $arrRow){
							$arrRefs[$sSka][$sV][$nI][$oLink->_sMappingName][]=$arrTargetRowset[$offset];
							unset($arrRefs[$sSka][$sV][$nI][$sSka]);
						}
					}
				}
			}
			unset($arrRefs);
			unset($arrRefsValue);
			unset($arrRow);
			if($this->limit==1){
				$arrRow=reset($arrRowset);
				unset($arrRowset);
			}
		}
		else{
			if(isset($arrRow))// 非关联查询
				unset($arrRow);
			if(isset($arrRowset))
				unset($arrRowset);
			if($this->_arrOptions[self::LIMIT_COUNT]==1){
				$arrRow=$hHandle->fetch();
			}
			else{
				$arrRowset=$hHandle->getAllRows();
			}
		}
		if(count($this->_arrOptions[self::AGGREGATE])&& isset($arrRowset)){
			if(empty($this->_arrOptions[self::GROUP])){
				return reset($arrRowset);
			}
			else{
				return $arrRowset;
			}
		}
		if(isset($arrRow)){
			return $arrRow;
		}
		else{
			if(!isset($arrRowset))$arrRowset=array();
			return $arrRowset;
		}
	}

	protected function queryObjects_(){
		$hHandle=$this->getQueryHandle();// 执行查询，获得一个查询句柄&$this->_query_params[self::USED_LINKS] 是查询涉及到的关联（关联别名=>关联对象）
		if($hHandle===false)return false;
		$sClassName=$this->_oMeta->_sClassName;
		$arrRowset=array();
		$this->_arrQueryParams[self::USED_LINKS]=$this->_arrQueryParams[self::USED_LINKS];
		$arrNoLazyQuery=Dyhb::normalize($this->_arrQueryParams[self::NON_LAZY_QUERY]);
		while(($arrRow=$hHandle->fetch())){
			if($this->_oMeta->_sInheritTypeField){
				$sClassName=$arrRow[$this->_oMeta->_sInheritTypeField ];
			}
			$oObj=new $sClassName($arrRow,Db::FIELD,true);
			if(is_array($arrNoLazyQuery)){
				foreach($arrNoLazyQuery as $sAssoc){
					$oObj->{$sAssoc};
				}
			}
			$arrRowset[]=$oObj;
		}
		if(empty($arrRowset)){
			if(! $this->_arrOptions[self::LIMIT_QUERY]){// 没有查询到数据时，返回 Null 对象或空集合
				return $this->_oMeta->newObj();
			}
			else{
				if($this->_arrQueryParams[self::AS_COLL]){
					return new ModelRelationColl($this->_oMeta->_sClassName);
				}
				else{
					return array();
				}
			}
		}
		if(!$this->_arrOptions[self::LIMIT_QUERY]){
			return reset($arrRowset);// 创建一个单独的对象
		}
		else{
			if($this->_arrQueryParams[self::AS_COLL]){
				return ModelRelationColl::createFromArray($arrRowset,$this->_oMeta->_sClassName);
			}
			else{
				return $arrRowset;
			}
		}
	}

	public function isError(){
		return $this->_bIsError;
	}

	public function setIsError($isError=true){
		$bOldValue=$this->_bIsError;
		$this->_bIsError=$isError;
		return $bOldValue;
	}

	public function getErrorMessage(){
		return $this->_sErrorMessage;
	}

	public function setErrorMessage($sMessage=''){
		$sOldValue=$this->_sErrorMessage;
		$this->_sErrorMessage=$sMessage;
		$this->setIsError(true);
		return $sOldValue;
	}

	public function distinct($bFlag=true){
		$this->_arrOptions[self::DISTINCT]=(bool)$bFlag;
		return $this;
	}

	public function from($Table,$Cols=self::SQL_WILDCARD){
		$this->_currentTable=$Table;
		return $this->join_(self::INNER_JOIN,$Table,$Cols);
	}

	public function columns($Cols=self::SQL_WILDCARD,$Table=null){
		if(is_null($Table)){
			$Table=$this->getCurrentTableName_();
		}
		$this->addCols_($Table,$Cols);
		return $this;
	}

	public function setColumns($Cols=self::SQL_WILDCARD,$Table=null){
		if(is_null($Table)){
			$Table=$this->getCurrentTableName_();
		}
		$this->_arrOptions[self::COLUMNS]=array();
		$this->addCols_($Table,$Cols);
		return $this;
	 }

	public function where($Cond /* args */){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		return $this->addConditions_($Cond,$arrArgs,self::WHERE,true);
	}

	public function orWhere($Cond /* args */){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		return $this->addConditions_($Cond,$arrArgs,self::WHERE,false);
	}

	public function link($Link){
		if(! is_array($Link)){// 非数组
			$arrLinks=array($Link);
		}
		else{// 数组
			$arrLinks=$Link;
		}
		foreach($arrLinks as $Link){
			if($Link instanceof ModelRelation){
				$this->_arrLinks[$Link->_sMappingName]=$Link;
			}
			else{
				G::E(G::L('关联必须是 DbActiveRecordAssociation 类型' ,'DbDyhb'));// 关联必须是 DbActiveRecordAssociation 类型.
			}
		}
		return $this;
	}

	public function join($Table,$Cols=self::SQL_WILDCARD,$Cond /* args */){
		$arrArgs=func_get_args();
		return $this->join_(self::INNER_JOIN,$Table,$Cols,$Cond,array_slice($arrArgs,3));
	}

	public function joinInner($Table,$Cols=self::SQL_WILDCARD,$Cond){
		$arrArgs=func_get_args();
		return $this->join_(self::INNER_JOIN,$Table,$Cols,$Cond,array_slice($arrArgs,3));
	}

	public function joinLeft($Table,$Cols=self::SQL_WILDCARD,$Cond){
		$arrArgs=func_get_args();
		return $this->join_(self::LEFT_JOIN,$Table,$Cols,$Cond,array_slice($arrArgs,3));
	}

	public function joinRight($Table,$Cols=self::SQL_WILDCARD,$Cond){
		$arrArgs=func_get_args();
		return $this->join_(self::RIGHT_JOIN,$Table,$Cols,$Cond,array_slice($arrArgs,3));
	}

	public function joinFull($Table,$Cols=self::SQL_WILDCARD,$Cond){
		$arrArgs=func_get_args();
		return $this->join_(self::FULL_JOIN,$Table,$Cols,$Cond,array_slice($arrArgs,3));
	}

	public function joinCross($Table,$Cols=self::SQL_WILDCARD){
		return $this->join_(self::CROSS_JOIN,$Table,$Cols);
	}

	public function joinNatural($Table,$Cols=self::SQL_WILDCARD){
		return $this->join_(self::NATURAL_JOIN,$Table,$Cols);
	}

	public function union($Select=array(),$sType=self::SQL_UNION){
		if(! is_array($Select)){
			$Select=array($Select);
		}
		if(!isset(self::$_arrUnionTypes[$sType])){
			G::E(G::L('无效的 UNION 类型 %s','DbDyhb',null,$sType));
		}
		foreach($Select as $Target){
			$this->_arrOptions[self::UNION][]=array($Target,$sType);
		}
		return $this;
	}

	public function group($Expr){
		if(!is_array($Expr)){// 表达式
			$Expr=array($Expr);
		}
		foreach($Expr as $Part){
			if($Part instanceof DbExpression){
				$Part=$Part->makeSql($this->_oConnect,$this->getCurrentTableName_(),$this->_arrColumnsMapping);
			}
			else{
				$Part=$this->_oConnect->qualifySql($Part,$this->getCurrentTableName_(),$this->_arrColumnsMapping);
			}
			$this->_arrOptions[self::GROUP][]=$Part;
		}
		return $this;
	}

	public function having($Cond /* args */){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		return $this->addConditions_($Cond,$arrArgs,self::HAVING,true);
	}

	public function orHaving($Cond){
		$arrArgs=func_get_args();
		array_shift($arrArgs);
		return $this->addConditions_($Cond,$arrArgs,self::HAVING,false);
	}

	public function order($Expr){
		if(!is_array($Expr)){// 非数组
			$Expr=array($Expr);
		}
		$arrM=null;
		foreach($Expr as $Val){
			if($Val instanceof DbExpression){
				$Val=$Val->makeSql($this->_oConnect,$this->getCurrentTableName_(),$this->_arrColumnsMapping);
				if(preg_match('/(.*\W)('.self::SQL_ASC.'|'.self::SQL_DESC.')\b/si',$Val,$arrM)){
					$Val=trim($arrM[1]);
					$sDir=$arrM[2];
				}
				else{
					$sDir=self::SQL_ASC;
				}
				$this->_arrOptions[self::ORDER][]=$Val.' '.$sDir;
			}
			else{
				$arrCols=explode(',',$Val);
				foreach($arrCols as $Val){
					$Val=trim($Val);
					if(empty($Val)){
						continue;
					}
					$sCurrentTableName=$this->getCurrentTableName_();
					$sDir=self::SQL_ASC;
					$arrM=null;
					if(preg_match('/(.*\W)('.self::SQL_ASC.'|'.self::SQL_DESC.')\b/si',$Val,$arrM)){
						$Val=trim($arrM[1]);
						$sDir=$arrM[2];
					}
					if(!preg_match('/\(.*\)/',$Val)){
						if(preg_match('/(.+)\.(.+)/',$Val,$arrM)){
							$sCurrentTableName=$arrM[1];
							$Val=$arrM[2];
						}
						if(isset($this->_arrColumnsMapping[$Val])){
							$Val=$this->_arrColumnsMapping[$Val];
						}
						$Val=$this->_oConnect->qualifyId("{$sCurrentTableName}.{$Val}");
					}
					$this->_arrOptions[self::ORDER][]=$Val.' '.$sDir;
				}
			}
		}
		return $this;
	}

	public function one(){
		$this->_arrOptions[self::LIMIT_COUNT]=1;
		$this->_arrOptions[self::LIMIT_OFFSET]=null;
		$this->_arrOptions[self::LIMIT_QUERY]=false;
		return $this;
	}

	public function all(){
		$this->_arrOptions[self::LIMIT_COUNT]=null;
		$this->_arrOptions[self::LIMIT_OFFSET]=null;
		$this->_arrOptions[self::LIMIT_QUERY]=true;
		return $this;
	}

	public function limit($nOffset=0,$nCount=30){
		$this->_arrOptions[self::LIMIT_COUNT]=abs(intval($nCount));
		$this->_arrOptions[self::LIMIT_OFFSET]=abs(intval($nOffset));
		$this->_arrOptions[self::LIMIT_QUERY]=true;
		return $this;
	}

	public function top($nCount=30){
		return $this->limit(0,$nCount);
	}

	public function forUpdate($bFlag=true){
		$this->_bForUpdate=(bool)$bFlag;
		return $this;
	}

	public function count($Field='*',$sAlias='row_count'){
		return $this->addAggregate_(self::SQL_COUNT,$Field,$sAlias);
	}

	public function avg($Field,$sAlias='avg_value'){
		return $this->addAggregate_(self::SQL_AVG,$Field,$sAlias);
	}

	public function max($Field,$sAlias='max_value'){
		return $this->addAggregate_(self::SQL_MAX,$Field,$sAlias);
	}

	public function min($Field,$sAlias='min_value'){
		return $this->addAggregate_(self::SQL_MIN,$Field,$sAlias);
	}

	public function sum($Field,$sAlias='sum_value'){
		return $this->addAggregate_(self::SQL_SUM,$Field,$sAlias);
	}

	public function asObj($sClassName){
		$this->_oMeta=ModelMeta::instance($sClassName);
		$this->_arrQueryParams[self::AS_ARRAY]=false;
		return $this;
	}

	public function asArray(){
		$this->_oMeta=null;
		$this->_arrQueryParams[self::AS_ARRAY]=true;
		return $this;
	}

	public function asColl($bAsColl=true){
		$this->_arrQueryParams[self::AS_COLL]=$bAsColl;
		return $this;
	}

	public function columnMapping($Name,$sMappingTo=NULL){
		if(is_array($Name)){
			$this->_arrColumnsMapping=array_merge($this->_arrColumnsMapping,$Name);
		}
		else{
			if(empty($sMappingTo)){
				unset($this->_arrColumnsMapping[$Name]);
			}
			else{
				$this->_arrColumnsMapping[$Name]=$sMappingTo;
			}
		}
		return $this;
	}

	public function recursion($nRecursion){
		$this->_arrQueryParams[self::RECURSION]=abs(intval($nRecursion));
		return $this;
	}

	public function linkForRecursion(ModelRelation $oLink){
		$this->_arrQueryParams[self::LINK_FOR_RECURSION]=$oLink;
		return $this;
	}

	public function getOption($sOption){
		$sOption=strtolower($sOption);
		if(! array_key_exists($sOption,$this->_arrOptions)){
			G::E(G::L('无效的部分名称 %s' ,'DbDyhb',null,$sOption));
		}
		return $this->_arrOptions[$sOption];
	}

	public function reset($sOption=null){
		if($sOption==null){// 设置整个配置
			$this->_arrOptions=self::$_arrOptionsInit;
			$this->_arrQueryParams=self::$_arrQueryParamsInit;
		}
		elseif(array_key_exists($sOption,self::$_arrOptionsInit)){
			$this->_arrOptions[$sOption]=self::$_arrOptionsInit[$sOption];
		}
		return $this;
	}

	public function makeSql(){
		$arrSql=array(
			self::SQL_SELECT
		);
		foreach(array_keys(self::$_arrOptionsInit)as $sOption){
			if($sOption==self::FROM){
				$arrSql[self::FROM]='';
			}
			else{
				$sMethod='parse'.ucfirst($sOption).'_';
				if(method_exists($this,$sMethod)){
					$arrSql[$sOption]=$this->$sMethod();
				}
			}
		}
		$arrSql[self::FROM]=$this->parseFrom_();
		foreach($arrSql as $nOffset=>$sOption){// 删除空元素
			if(trim($sOption)==''){
				unset($arrSql[$nOffset]);
			}
		}
		$this->_sLastSql=implode(' ',$arrSql);
		return $this->_sLastSql;
	}

	protected function parseDistinct_(){
		if($this->_arrOptions[self::DISTINCT]){
			return self::SQL_DISTINCT;
		}
		else{
			return '';
		}
	}

	protected function parseColumns_(){
		if(empty($this->_arrOptions[self::COLUMNS])){
			return '';
		}
		if($this->_arrQueryParams[self::PAGED_QUERY]){
			return 'COUNT(*)';
		}
		$arrColumns=array();// $this->_arrOptions[self::COLUMNS] 每个元素的格式
		foreach($this->_arrOptions[self::COLUMNS] as $arrEntry){
			list($sTableName,$Col,$sAlias)=$arrEntry;// array($currentTableName,$Col,$sAlias | null)
			if($Col instanceof DbExpression){// $Col 是一个字段名或者一个 DbExpression 对象
				$arrColumns[]=$Col->makeSql($this->_oConnect,$sTableName,$this->_arrColumnsMapping);
			}
			else{
				if(isset($this->_arrColumnsMapping[$Col])){
					$Col=$this->_arrColumnsMapping[$Col];
				}
				$Col=$this->_oConnect->qualifyId("{$sTableName}.{$Col}");
				if($Col!=self::SQL_WILDCARD && $sAlias){
					$arrColumns[]=$this->_oConnect->qualifyId($Col,$sAlias,'AS');
				}
				else{
					$arrColumns[]=$Col;
				}
			}
		}
		if($this->_arrQueryParams[self::RECURSION]>0){// 确定要查询的关联，从而确保查询主表时能够得到相关的关联字段
			foreach($this->_arrLinks as $oLink){
				$oLink->init();
				if(!$oLink->_bEnabled || $oLink->_bOnFind===false || $link->_bOnFind==='skip'){
					continue;
				}
				$oLink->init();
				$arrColumns[]=$this->qualifyId($oLink->_oSourceTable->getConnect(),$oLink->_sSourceKey,$oLink->_sSourceKeyAlias,'AS');
				$this->_arrQueryParams[self::USED_LINKS][$link->_sSourceKeyAlias]=$oLink;
			}
		}
		if($this->_arrQueryParams[self::LINK_FOR_RECURSION]){// 如果指定了来源关联，则需要查询组装数据所需的关联字段
			$oLink=$this->_arrQueryParams[self::LINK_FOR_RECURSION];
			$arrColumns[]=$this->qualifyId($oLink->_oTargetTable->getConnect(),$oLink->_sTargetKey,$link->_sTargetKeyAlias,'AS');
		}
		return implode(',',$arrColumns);
	}

	protected function parseAggregate_(){
		$arrColumns=array();
		foreach($this->_arrOptions[self::AGGREGATE] as $arrAggregate){
			list(,$sField,$sAlias)=$arrAggregate;
			if($sAlias){
				$arrColumns[]=$sField.' AS '.$sAlias;
			}
			else{
				$arrColumns[]=$sField;
			}
		}
		return(empty($arrColumns))?'':implode(',',$arrColumns);
	}

	protected function parseFrom_(){
		$arrFrom=array();
		foreach($this->_arrOptions[self::FROM] as $sAlias=>$arrTable){
			$sTmp='';
			if(!empty($arrFrom)){// 如果不是第一个 FROM，则添加 JOIN
				$sTmp.=' '.strtoupper($arrTable['join_type']).' ';
			}
			if($sAlias==$arrTable['table_name']){
				$sTmp.=$this->_oConnect->qualifyId("{$arrTable['schema']}.{$arrTable['table_name']}");
			}
			else{
				$sTmp.=$this->_oConnect->qualifyId("{$arrTable['schema']}.{$arrTable['table_name']}",$sAlias);
			}
			if(!empty($arrFrom) && !empty($arrTable['join_cond'])){// 添加 JOIN 查询条件
				$sTmp.="\n  ".self::SQL_ON.' '.$arrTable['join_cond'];
			}
			$arrFrom[]=$sTmp;
		}
		if(!empty($arrFrom)){
			return "\n ".self::SQL_FROM.' '.implode("\n",$arrFrom);
		}
		else{
			return '';
		}
	}

	protected function parseUnion_(){
		$sSql='';
		if($this->_arrOptions[self::UNION]){
			$nOptions=count($this->_arrOptions[self::UNION]);
			foreach($this->_arrOptions[self::UNION] as $nCnt=>$arrUnion){
				list($oTarget,$sType)=$arrUnion;
				if($oTarget instanceof DbRecordSet){
					$oTarget=$oTarget->makeSql();
				}
				$sSql.=$oTarget;
				if($nCnt < $nOptions - 1){
					$sSql.=' '.$sType.' ';
				}
			}
		}
		return $sSql;
	}

	protected function parseWhere_(){
		$sSql='';
		if(! empty($this->_arrOptions[self::FROM])&& ! is_null($this->_arrOptions[self::WHERE])){
			$sWhere=$this->_arrOptions[self::WHERE]->makeSql($this->_oConnect,$this->getCurrentTableName_(),null,array($this,'parseTableName_'));
			if(!empty($sWhere)){
				$sSql.="\n ".self::SQL_WHERE.' '.$sWhere;
			}
		}
		return $sSql;
	}

	protected function parseGroup_(){
		if(!empty($this->_arrOptions[self::FROM])&& ! empty($this->_arrOptions[self::GROUP])){
			return "\n ".self::SQL_GROUP_BY.' '.implode(",\n\t",$this->_arrOptions[self::GROUP]);
		}
		return '';
	}

	protected function parseHaving_(){
		if(!empty($this->_arrOptions[self::FROM])&& ! empty($this->_arrOptions[self::HAVING])){
			return "\n ".self::SQL_HAVING.' '.implode(",\n\t",$this->_arrOptions[self::HAVING]);
		}
		return '';
	}

	protected function parseOrder_(){
		if(!empty($this->_arrOptions[self::ORDER])){
			return "\n ".self::SQL_ORDER_BY.' '.implode(',',array_unique($this->_arrOptions[self::ORDER]));
		}
		return '';
	}

	protected function parseForUpdate_(){
		if($this->_arrOptions[self::FOR_UPDATE]){
			return "\n ".self::SQL_FOR_UPDATE;
		}
		return '';
	}

	protected function join_($sJoinType,$Name,$Cols,$Cond=null,$arrCondArgs=null){
		if(!isset(self::$_arrJoinTypes[$sJoinType])){
			G::E(G::L('无效的 JOIN 类型 %s','DbDyhb',null,$sJoinType));
		}
		if(count($this->_arrOptions[self::UNION])){
			G::E(G::L('不能在使用 UNION 查询的同时使用 JOIN 查询','DbDyhb'));// 不能在使用 UNION 查询的同时使用 JOIN 查询.
		}
		$arrM=array();// 根据 $Name 的不同类型确定数据表名称、别名
		if(empty($Name)){// 没有指定表，获取默认表
			$Table=$this->getCurrentTableName_();
			$sAlias='';
		}
		elseif(is_array($Name)){// $Name为数组配置
			foreach($Name as $sAlias=>$Table){
				if(!is_string($sAlias)){
					$sAlias='';
				}
				break;
			}
		}
		elseif($Name instanceof DbTableEnter){// 如果为DbTableEnter的实例
			$Table=$Name;
			$sAlias='';
		}
		elseif(preg_match('/^(.+)\s+AS\s+(.+)$/i',$Name,$arrM)){// 字符串指定别名
			$Table=$arrM[1];
			$sAlias=$arrM[2];
		}
		else{
			$Table=$Name;
			$sAlias='';
		}
		if($Table instanceof DbTableEnter){// 确定 table_name 和 schema
			$sSchema=$Table->_sSchema;
			$sTableName=$Table->_sPrefix.$Table->_sName;
		}
		else{
			$arrM=explode('.',$Table);
			if(isset($arrM[1])){
				$sSchema=$arrM[0];
				$sTableName=$arrM[1];
			}
			else{
				$sSchema=null;
				$sTableName=$Table;
			}
		}
		$sAlias=$this->uniqueAlias_(empty($sAlias)?$sTableName:$sAlias);// 获得一个唯一的别名
		if(!($Cond instanceof DbCond)){// 处理查询条件
			$Cond=DbCond::createByArgs($Cond,$arrCondArgs);
		}
		$sWhereSql=$Cond->makeSql($this->_oConnect,$sAlias,$this->_arrColumnsMapping);
		$this->_arrOptions[self::FROM][$sAlias]=array(// 添加一个要查询的数据表
			'join_type'=>$sJoinType,'table_name'=>$sTableName,'schema'=>$sSchema,'join_cond'=>$sWhereSql
		);
		$this->addCols_($sAlias,$Cols);// 添加查询字段
		return $this;
	}

	protected function addCols_($sTableName,$Cols){
		$Cols=Dyhb::normalize($Cols);
		if(is_null($sTableName))$sTableName='';
		$arrM=null;
		if(is_object($Cols)&&($Cols instanceof DbExpression)){// Cols为对象
			$this->_arrOptions[self::COLUMNS][]=array($sTableName,$Cols,null);
		}
		else{
			foreach($Cols as $sAlias=>$Col){
				if(is_string($Col)){
					foreach(Dyhb::normalize($Col)as $sCol){// 将包含多个字段的字符串打散
						$currentTableName=$sTableName;
						if(preg_match('/^(.+)\s+'.self::SQL_AS.'\s+(.+)$/i',$sCol,$arrM)){// 检查是不是 "字段名 AS 别名"这样的形式
							$sCol=$arrM[1];
							$sAlias=$arrM[2];
						}
						if(preg_match('/(.+)\.(.+)/',$sCol,$arrM)){// 检查字段名是否包含表名称
							$currentTableName=$arrM[1];
							$sCol=$arrM[2];
						}
						if(isset($this->_arrColumnsMapping[$sCol])){
							$sCol=$this->_arrColumnsMapping[$sCol];
						}
						$this->_arrOptions[self::COLUMNS][]=array(
							$currentTableName,$sCol,is_string($sAlias)?$sAlias:null
						);
					}
				}
				else{
					$this->_arrOptions[self::COLUMNS][]=array($sTableName,$Col,is_string($sAlias)?$sAlias :null);
				}
			}
		}
	}

	protected function addConditions_($Cond,array $arrArgs,$sPartType,$bBool){
		if(!($Cond instanceof DbCond)){// DbCond对象
			if(empty($Cond)){
				return $this;
			}
			$Cond=DbCond::createByArgs($Cond,$arrArgs,$bBool);
		}
		if(is_null($this->_arrOptions[$sPartType])){// 空，直接创建DbCond
			$this->_arrOptions[$sPartType]=new DbCond();
		}
		if($bBool){// and类型
			$this->_arrOptions[$sPartType]->andCond($Cond);
		}
		else{// or类型
			$this->_arrOptions[$sPartType]->orCond($Cond);
		}
		return $this;
	}

	protected function getCurrentTableName_(){
		if(is_array($this->_currentTable)){// 数组
			while(list($sAlias,)=each($this->_currentTable)){
				$this->_currentTable=$sAlias;
				return $sAlias;
			}
		}
		elseif(is_object($this->_currentTable)){
			return $this->_currentTable->_sPrefix.$this->_currentTable->_sName;
		}
		else{
			return $this->_currentTable;
		}
	}

	public function parseTableName_($sTableName){
		if(strpos($sTableName,'.')!==false){// 获取表模式
			list($sSchema,$sTableName)=explode('.',$sTableName);
		}
		else{
			$sSchema=null;
		}
		if(is_null($this->_oMeta)|| ! isset($this->_oMeta->associations[$sTableName])){
			return $sTableName;
		}
		$oAssoc=$this->_oMeta->assoc($sTableName)->init();
		$oTargetTable=$assoc->_oTargetMeta->_oTable;
		if($sSchema && $oTargetTable->_sSchema && $oTargetTable->_sSchema !=$sSchema){
			return "{$sSchema}.{$sTableName}";
		}
		$sAssocTableName=$oAssoc->_oTargetMeta->_oTable->getFullTableName();
		$sCurrentTableName=$this->getCurrentTableName_();
		switch($oAssoc->_sType){
			case Db::HAS_MANY:
			case Db::HAS_ONE:
			case Db::BELONGS_TO:
				$sKey="{$oAssoc->_sType}-{$sAssocTableName}";
				if(!isset($this->_arrJoinedTables[$sKey])){
					$sJoinCondExtra='';// 支持额外join 条件设定，用于关联查询
					if(isset($this->_oMeta->_arrProps[$oAssoc->_sMappingName]['assoc_params']['join_cond_extra'])){
						$sJoinCondExtra=" AND ".trim($this->_oMeta->_arrProps[$assoc->_sMappingName]['assoc_params']['join_cond_extra']);
					}
					$this->joinInner($sAssocTableName,'',"{$sAssocTableName}.{$oAssoc->_sTargetKey}="."{$sCurrentTableName}.{$oAssoc->_sSourceKey}{$sJoinCondExtra}");
					$this->_arrJoinedTables[$sKey]=true;
				}
				break;
			 case Db::MANY_TO_MANY:
					$sMidTableName=$oAssoc->_oMidTable->getFullTableName();
					$sKey="{$oAssoc->_sType}-{$sMidTableName}";
					if(!isset($this->_joined_tables[$sKey])){
					$this->joinInner($sMidTableName,'',"{$sMidTableName}.{$oAssoc->_sMidSourceKey}="."{$sCurrentTableName}.{$oAssoc->_sMidSourceKey}");
					$this->joinInner($sAssocTableName,'',"{$sAssocTableName}.{$oAssoc->_sTargetKey}="."{$sMidTableName}.{$oAssoc->_arrMidTargetKey}");
					$this->_arrJoinedTables[$sKey]=true;
				}
				break;
		}
		return $sAssocTableName;
	}

	protected function addAggregate_($sType,$Field,$sAlias){
		$this->_arrOptions[self::COLUMNS]=array();
		$this->_arrQueryParams[self::RECURSION]=0;
		if($Field instanceof DbExpression){
			$Field=$Field->makeSql($this->_oConnect,$this->getCurrentTableName_(),$this->_arrColumnsMapping);
		}
		else{
			if(isset($this->_arrColumnsMapping[$Field])){
				$Field=$this->_arrColumnsMapping[$Field];
			}
			$Field=$this->_oConnect->qualifySql($Field,$this->getCurrentTableName_(),$this->_arrColumnsMapping);
			$Field="{$sType}($Field)";
		}
		$this->_arrOptions[self::AGGREGATE][]=array(
			$sType,$Field,$sAlias
		);
		$this->_arrQueryParams[self::AS_ARRAY]=true;
		return $this;
	}

	private function uniqueAlias_($Name){
		if(empty($Name)){
			return '';
		}
		if(is_array($Name)){// 数组，返回最后一个元素
			$sC=end($Name);
		}
		else{// 字符串
			$nDot=strrpos($Name,'.');
			$sC=($nDot===false)?$Name :substr($Name,$nDot + 1);
		}
		for($nI=2; array_key_exists($sC,$this->_arrOptions[self::FROM]); ++ $nI){
			$sC=$Name.'_'.(string)$nI;
		}
		return $sC;
	}

}
