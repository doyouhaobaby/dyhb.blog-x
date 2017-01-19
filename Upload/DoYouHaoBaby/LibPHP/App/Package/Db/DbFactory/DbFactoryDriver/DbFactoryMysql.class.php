<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Mysql数据库工厂类，用于生成数据库相关对象($) */

!defined('DYHB_PATH') && exit;

class DbFactoryMysql extends DbFactory{

	public function createConnect(){
		return new DbConnectMysql();
	}

	public function createRecordSet(DbConnect $oConn,$nFetchMode=Db::FETCH_MODE_ASSOC){
		return new DbRecordSetMysql($oConn,$nFetchMode);
	}

}
