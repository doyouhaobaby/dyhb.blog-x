<?dyhb
class DbFactoryMysql extends DbFactory{

	/**
	 * 创建Mysql数据库连接对象
	 *
	 * @access public
	 * @return DbConnect
	 */
	 public function createConnect(){}

	/**
	 * 创建一个数据库 记录集
	 *
	 * @access public
	 * @param $oConn DbConnect 数据库连接
	 * @param $nFetchMode=Db::FETCH_MODE_ASSOC int 返回类型：Db::FETCH_MODE_ASSOC(字段为键名),Db::FETCH_MODE_ARRAY(索引)
	 * @return DbRecordSet
	 */
	public function createRecordSet(DbConnect $oConn,$nFetchMode=Db::FETCH_MODE_ASSOC){}

}
