<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Mysql数据连接管理类($) */

!defined('DYHB_PATH') && exit;

define('CLIENT_MULTI_RESULTS',131072);

class DbConnectMysql extends DbConnect{

	public function commonConnect($Config='',$nLinkid=0){
		if(!isset($this->_arrHConnect[$nLinkid ])){
			$this->_arrCurrentDbConfig=$Config;// 赋值给当前数据库连接配置
			$nHost=$Config['db_host'].($Config['db_port']?":{$Config['db_port']}":'');// 端口处理
			if(empty($Config['connect'])){// 如果设置了数据连接，则不连接
				if($this->_bPConnect){// 是否永久连接
					$this->_arrHConnect[$nLinkid]=mysql_pconnect($nHost,$Config['db_user'],$Config['db_password'],CLIENT_MULTI_RESULTS);
				}
				else{
					$this->_arrHConnect[$nLinkid]=mysql_connect($nHost,$Config['db_user'],$Config['db_password'],true,CLIENT_MULTI_RESULTS);
				}
			}
			else{
				$this->_arrHConnect[$nLinkid]=$Config['connect'];
			}
			if(!$this->_arrHConnect[$nLinkid]){// 判断是否成功连接上数据
				G::E(G::L('数据库连接失败，请检查你的数据库信息是否正确，连接数据库的配置如下：%s','DbDyhb',null,G::dump($Config,false)));
				return false;
			}
			$this->_hCurrentConnect=$this->_arrHConnect[$nLinkid];
			if(empty($Config['db_name'])|| !mysql_select_db($Config['db_name'],$this->_arrHConnect[$nLinkid])){// 尝试请求数据
				G::E(G::L('数据库不存在在或者错误，请检查你的数据库信息是否正确，连接数据库的配置如下：%s','DbDyhb',null,G::dump($Config,false)));
				return false;
			}
			$nDbVersion=$this->databaseVersion();// 获取Mysql数据库版本,尝试兼容性纠正
			if($nDbVersion >="4.1"){// 使用UTF8存取数据库 需要mysql 4.1.0以上支持
				$sCharset=isset($Config['db_char'])?$Config['db_char']:$GLOBALS['_commonConfig_']['DB_CHAR'];// 获取数据库字符集
				if(!mysql_query("SET character_set_connection=".$sCharset.",character_set_results=".$sCharset.",character_set_client=binary")){
					G::E(sprintf("Set db_host ‘%s’ charset=%s failed.",$nHost,$sCharset));
					return false;
				}
			}
			if($nDbVersion > '5.0.1'){// 忽略严格模式
				if(!mysql_query("SET sql_mode=''",$this->_arrHConnect[$nLinkid])){
					G::E('Set sql_mode failed.',$this->_arrHConnect[$nLinkid]);
					return false;
				}
			}
			$this->_bConnected=true;// 标记连接成功
		}
		return $this->_arrHConnect[$nLinkid];
	}

	public function disConnect($hDbConnect=null,$bCloseAll=false){
		if($hDbConnect && is_resource($hDbConnect)){// 关闭指定数据库连接
			mysql_close($hDbConnect);
			$hDbConnect=null;
		}
		if($bCloseAll){// 关闭所有数据库连接
			if($this->_hWriteConnect && is_resource($this->_hWriteConnect)){
				mysql_close($this->_hWriteConnect);
				$this->_hWriteConnect=null;
			}
			if(is_array($this->_arrHReadConnect)&& !empty($this->_arrHReadConnect)){
				foreach($this->_arrHReadConnect as $hConnect){
					if($hConnect && is_resource($hConnect)){
						mysql_close($hConnect);
					}
				}
				$this->_arrHReadConnect=array();
			}
			$this->_arrHConnect=array();
		}
		return true;
	}

	public function query_($sSql,$bIsMaster=false){
		$sSql=trim($sSql);// 过滤SQL语句
		if($sSql==""){// sql语句为空，则返回错误
			$this->errorMessage('Sql query is empty.');
			return false;
		}
		if(!$GLOBALS['_commonConfig_']['DB_RW_SEPARATE'] || $this->_bSingleHost){// 是否只有一台数据库机器
			$bIsMaster=true;
		}
		$sType='';// 获取执行SQL的数据库连接
		if($bIsMaster){
			$sType=trim(strtolower(substr(ltrim($sSql),0,6)));
		}
		if($bIsMaster || $sType!="select"){// 主服务或者是非查询，那么连接写服务器
			$hDbConnect=$this->writeConnect();
		}
		else{// 否则连接读服务器
			$hDbConnect=$this->readConnect();
		}
		if(!$hDbConnect || !is_resource($hDbConnect)){
			$this->_hCurrentConnect=null;
			$this->errorMessage(sprintf("Not availability db connection. Query SQL:%s",$sSql));
			return;
		}
		$this->_hCurrentConnect=$hDbConnect;// 执行查询
		$this->setLastSql($sSql);// 记录最后查询的sql语句
		$this->_hQueryResult=null;
		if($this->_bIsRuntime){// 是否记录数据库查询时间
			$nStartTime=E::getMicrotime();
			$this->_hQueryResult=mysql_query($sSql,$hDbConnect);
			$nRunTime=E::getMicrotime()- $nStartTime; // 记录sql运行时间
			$this->setQueryTime($nRunTime);
		}
		else{// 直接查询
			$this->_hQueryResult=mysql_query($sSql,$hDbConnect);
		}
		$this->Q(1);
		if($this->_bLogEnabled){// 记录执行的SQL
			$this->debug();
		}
		if($this->_hQueryResult===false){// 判断数据库查询是否正确
			$this->errorMessage(sprintf("Query sql failed. SQL:%s",$sSql),$hDbConnect);
		}
		return $this->_hQueryResult;
	}

	public function selectDb($sDbName,$hDbHandle=null){
		if($hDbHandle && is_resource($hDbHandle)){// 重新选择一个连接的数据库
			if(!mysql_select_db($sDbName,$hDbHandle)){
				G::E('Select database:$sDbName failed.');
				return false;
			}
			return true;
		}
		if($this->_hWriteConnect && is_resource($this->_hWriteConnect)){// 重新选择所有连接的数据库&读数据库连接
			if(!mysql_select_db($sDbName,$this->_hWriteConnect)){
				G::E('Select database:$dbName failed.');
				return false;
			}
		}
		if(is_array($this->_arrHReadConnect && !empty($this->_arrHReadConnect))){// 写数据库连接
			foreach($this->_arrHReadConnect as $hConnect){
				if($hConnect && is_resource($hConnect)){
					if(!mysql_select_db($sDbName,$hConnect)){
						G::E('Select database:$sDbName failed.');
						return false;
					}
				}
			}
		}
		$this->_arrHConnect=array();// 重设所有数据库连接
		if(is_array($this->_arrHReadConnect)&& !empty($this->_arrHReadConnect)){
			$this->_arrHConnect=array_merge($this->_arrHReadConnect);
		}
		$this->_arrHConnect[]=$this->_hWriteConnect;
		$this->_hCurrentConnect=$this->_hWriteConnect;// 将当前连接切换到主服务器
		return true;
	}

	public function databaseVersion($nLinkid=0){
		if(!$nLinkid){
			$nLinkid=$this->_hCurrentConnect;
		}
		if($nLinkid){
			$this->_nVersion=mysql_get_server_info($nLinkid);
		}else{
			$this->_nVersion=mysql_get_server_info();
		}
		return $this->_nVersion;
	}

	public function errorMessage($sMsg='',$hConnect=null){
		if($sMsg=='' && !$hConnect){// 不存在消息返回
			return false;
		}
		$sMsg="MySQL Error:<br/>{$sMsg}";// 错误消息
		if($hConnect && is_resource($hConnect)){
			$sMsg.="<br/>MySQL Message:<br/>".mysql_error($hConnect);
			$sMsg.="<br/>MySQL Code:<br/>".mysql_errno($hConnect);
			$this->_nErrorCode=mysql_errno($hConnect);
		}
		$sMsg.="<br/>MySQL Time:<br/>[". date("Y-m-d H:i:s")."]";
		G::E($sMsg);
	}

	public function selectLimit($sSql,$nOffset=0,$nLength=30,$arrInput=null,$bLimit=true){
		if($bLimit===true){
			if(!is_null($nOffset)){
				$sSql.=' LIMIT ' .(int)$nOffset;
				if(!is_null($nLength)){
					$sSql.=',' .(int)$nLength;
				}
				else{
					$sSql.=',18446744073709551615';
				}
			}
			elseif(!is_null($nLength)){
				$sSql.=' LIMIT ' .(int)$nLength;
			}
		}
		return $this->exec($sSql,$arrInput);
	}

	public function getDatabaseNameList(){
		$sSql="SHOW DATABASES ;";// 执行
		$hResult=$this->query_($sSql);
		if($hResult===false || !is_resource($hResult)){// 失败
			G::E(G::L('无法取得数据库名称清单','DbDyhb'));
		}
		$arrReturn=array();// 获取结果
		while($arrRes=mysql_fetch_row($hResult)){
			$arrReturn[]=$arrRes[0];
		}
		return $arrReturn ;
	}

	public function getTableNameList($sDbName=null){
		if($sDbName===null){// 确定数据库
			$sQueryDb=$this->getCurrentDb();
		}
		else{
			$sQueryDb=&$sDbName;
		}
		$sSql="SHOW TABLES;";// 执行
		$hResult=$this->query($sSql,$sQueryDb);
		if($hResult===false || !is_resource($hResult)){// 失败
			G::E(G::L('无法取得数据表名称清单','DbDyhb'));
			return false;
		}
		$arrReturn=array();
		while($arrRes=mysql_fetch_row($hResult)){
			$arrReturn[]=$arrRes[0];
		}
		return $arrReturn;
	}

	public function getColumnNameList($sTableName,$sDbName=null){
		if($sDbName===null){// 确定数据库
			$sQueryDb=$this->getCurrentDb();
		}
		else{
			$sQueryDb=&$sDbName;
		}
		$sSql="SHOW COLUMNS FROM {$sTableName}";// 执行
		$hResult=$this->query($sSql,$sQueryDb);
		if($hResult===false|| !is_resource($hResult)){// 失败
			G::E(G::L('无法取得数据表 < %s > 字段名称清单','DbDyhb',null,$sTableName));
		}
		$arrReturn=array();
		while($arrRes=mysql_fetch_object($hResult)){
			if(is_object($arrRes)){// 进一步处理获取主键和自动增加
				$arrRes=get_object_vars($arrRes);
			}
			$arrReturn[]=$arrRes['Field'];// 获取结果
			$sPrimary=$arrRes['Key']=='PRI' ?$arrRes['Field']: $sPrimary;
			$sAuto=!empty($arrRes['Extra'])?$arrRes['Field']: $sAuto;
		}
		$this->_sPrimary=$sPrimary;// 获取主键和自动增长
		$this->_sAuto=$sAuto;
		$this->_arrColumnNameList=$arrReturn;
		return $arrReturn;
	}

	public function isDatabaseExists($sDbName){}

	public function isTableExists($sTableName,$sDbName=null){}

	public function getInsertId(){
		$hDbConnect=$this->writeConnect();
		if(($nLastId=mysql_insert_id($hDbConnect))>0){
			return $nLastId;
		}
		return $this->getOne("SELECT LAST_INSERT_ID()",'',true);
	}

	public function getNumRows($hRes=null){
		if(!$hRes || !is_resource($hRes)){
			$hRes=$this->_hQueryResult;
		}
		return mysql_num_rows($hRes);
	}

	public function getAffectedRows(){
		$hDbConnect=$this->writeConnect();
		if(($nAffetedRows=mysql_affected_rows($hDbConnect))>=0){
			return $nAffetedRows;
		}
		return $this->getOne("SELECT ROW_COUNT()","",true);
	}

	public function lockTable($sTableName){
		return $this->query_("LOCK TABLES $sTableName",true);
	}

	public function unlockTable($sTableName){
		return $this->query_("UNLOCK TABLES $sTableName",true);
	}

	public function setAutoCommit($bAutoCommit=false){
		$bAutoCommit=($bAutoCommit ?1:0);
		return $this->query_("SET AUTOCOMMIT=$bAutoCommit",true);
	}

	public function startTransaction(){
		if(!$this->_hCurrentConnect)return false;// 没有当前数据库连接，直接返回
		if($this->_nTransTimes==0 && !$this->query_("BEGIN")){// 数据rollback 支持
			mysql_query('START TRANSACTION',$this->_hCurrentConnect);
		}
		$this->_nTransTimes++;
		return;
	}

	public function endTransaction(){}

	public function commit(){
		if($this->_nTransTimes > 0){
			$this->_nTransTimes=0;
			if(!$this->query_("COMMIT",true)){
				return false;
			}
		}
		return $this->setAutoCommit(true);
	}

	public function rollback(){
		if($this->_nTransTimes > 0){
			$this->_nTransTimes=0;
			if(!$this->query_("ROLLBACK",true)){
				return false;
			}
		}
		return $this->setAutoCommit(true);
	}

	public function nextId($sTableName,$sFieldName,$nStartValue=1){
		$sSeqTableName=$this->qualifyId("{$sTableName}_{$sFieldName}_seq");
		$sNextSql=sprintf('UPDATE %s SET id=LAST_INSERT_ID(id + 1)',$sSeqTableName);
		$nStartValue=intval($nStartValue);
		$bSuccessed=false;
		$this->exec($sNextSql);// 首先产生下一个序列值
		if($this->getAffectedRows()>0){
			$bSuccessed=true;
		}
		else{
			$this->exec(sprintf('CREATE TABLE %s (id INT NOT NULL)',$sSeqTableName));// 产生序列值失败，创建序列表
		}
		if(!$bSuccessed){
			$count=$this->getOne(sprintf('SELECT COUNT(*)FROM %s',$sSeqTableName));
			$count=array_shift($count);
			if($count==0){// 没有更新任何记录或者新创建序列表，都需要插入初始的记录
				$sSql=sprintf('INSERT INTO %s VALUES (%s)',$sSeqTableName,$nStartValue);
				$this->exec($sSql);
			}
			$this->exec($sNextSql);
		}
		$nInsertId=$this->getInsertId();// 获得新的序列值
		return $nInsertId;
	}

	public function identifier($sName){
		return ($sName !='*')?"`{$sName}`":'*';
	}

	public function qualifyStr($Value){
		if(is_array($Value)){// 数组，递归
			foreach($Value as $nOffset=>$sV){
				$Value[$nOffset]=$this->qualifyStr($sV);
			}
			return $Value;
		}
		if(is_int($Value)){ return $Value; }// int
		if(is_bool($Value)){
			return $Value?$this->getTrueValue(): $this->getFalseValue();
		}
		if(is_null($Value)){// Null值
			return $this->getNullValue();
		}
		if(!($Value instanceof DbExpression)){
			return G::getMagicQuotesGpc()?"'".$Value."'":"'".mysql_real_escape_string($Value,$this->getCurrentConnect())."'";
		}
		return $Value->makeSql($this);
	}

	public function metaColumns($sTableName){
		static $arrTypeMapping=array(
		'bit'=>'int1',
		'tinyint'=>'int1',
		'bool'=>'bool',
		'boolean'=>'bool',
		'smallint'=>'int2',
		'mediumint'=>'int3',
		'int'=>'int4',
		'integer'=>'int4',
		'bigint'=>'int8',
		'float'=>'float',
		'double'=>'double',
		'doubleprecision'=> 'double',
		'float unsigned'=> 'float',
		'decimal'=>'dec',
		'dec'=>'dec',
		'date'=>'date',
		'datetime'=>'datetime',
		'timestamp'=>'timestamp',
		'time'=>'time',
		'year'=>'int2',
		'char'=>'char',
		'nchar'=>'char',
		'varchar'=>'varchar',
		'nvarchar'=>'varchar',
		'binary'=>'binary',
		'varbinary'=>'varbinary',
		'tinyblob'=>'blob',
		'tinytext'=>'text',
		'blob'=>'blob',
		'text'=>'text',
		'mediumblob'=>'blob',
		'mediumtext'=>'text',
		'longblob'=>'blob',
		'longtext'=>'text',
		'enum'=> 'enum',
		'set'=> 'set'
		);
		$oRs=$this->exec(sprintf('SHOW FULL COLUMNS FROM %s',$this->qualifyId($sTableName)));// 返回查询结果对象
		$arrRet=array();
		$oRs->_nFetchMode=Db::FETCH_MODE_ASSOC;
		$oRs->_bResultFieldNameLower=true;
		while(($arrRow=$oRs->fetch())){
			$arrField=array();
			$arrField['name']=$arrRow['field'];
			$sType=strtolower($arrRow['type']);
			$arrField['scale']=null;
			$arrQuery=false;
			if(preg_match('/^(.+)\((\d+),(\d+)/',$sType,$arrQuery)){
				$arrField['type']=$arrQuery[1];
				$arrField['length']=is_numeric($arrQuery[2])?$arrQuery[2]:-1;
				$arrField['scale']=is_numeric($arrQuery[3])?$arrQuery[3]:-1;
			}
			elseif(preg_match('/^(.+)\((\d+)/',$sType,$arrQuery)){
				$arrField['type']=$arrQuery[1];
				$arrField['length']=is_numeric($arrQuery[2])?$arrQuery[2]:-1;
			}
			elseif(preg_match('/^(enum)\((.*)\)$/i',$sType,$arrQuery)){
				$arrField['type']=$arrQuery[1];
				$arrValue=explode(",",$arrQuery[2]);
				$arrField['enums']=$arrValue;
				$nLen=max(array_map("strlen",$arrValue))-2; // PHP >=4.0.6
				$arrField['length']=($nLen>0)?$nLen:1;
			}
			else{
				$arrField['type']=$sType;
				$arrField['length']=-1;
			}
			$arrField['ptype']=isset($arrTypeMapping[strtolower($arrField['type'])])?$arrTypeMapping[strtolower($arrField['type'])]:$arrField['type'];
			$arrField['not_null']=(strtolower($arrRow['null'])!='yes');
			$arrField['pk']=(strtolower($arrRow['key'])=='pri');
			$arrField['auto_incr']=(strpos($arrRow['extra'],'auto_incr')!==false);
			if($arrField['auto_incr']){
				$arrField['ptype']='autoincr';
			}
			$arrField['binary']=(strpos($sType,'blob')!==false);
			$arrField['unsigned']=(strpos($sType,'unsigned')!==false);
			$arrField['has_default']=$arrField['default']=null;
			if(!$arrField['binary']){
				$sD=$arrRow['default'];
				if(!is_null($sD)&& strtolower($sD)!='null'){
					$arrField['has_default']=true;
					$arrField['default']=$sD;
				}
			}
			if(!is_null($arrField['default'])){
				switch ($arrField['ptype']){
					case 'int1':
					case 'int2':
					case 'int3':
					case 'int4':
						$arrField['default']=intval($arrField['default']);
						break;
					case 'float':
					case 'double':
					case 'dec':
						$arrField['default']=doubleval($arrField['default']);
						break;
					case 'bool':
						$arrField['default']=(bool)$arrField['default'];
				}
			}
			$arrRet[strtolower($arrField['name'])]=$arrField;
		}
		return $arrRet;
	}

}
