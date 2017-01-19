<?dyhb
class DbConnectMysql extends DbConnect{

	/**
	 * 连接到MySQL数据库公共方法
	 *
	 * @access public
	 * @param mixed $Config 配置
	 * @param num $nLinkid 连接
	 * @return void
	 */
	public function commonConnect($Config='',$nLinkid=0){}

	/**
	 * 关闭数据库连接
	 *
	 * @access public
	 * @param $hDbConnect handel|null 数据库连接句柄
	 * @param $bCloseALL bool 是否关闭所有数据连接
	 * @return bool
	 */
	public function disConnect($hDbConnect=null,$bCloseAll=false){}

	/**
	 * 执行SQL语句
	 *
	 * @access public
	 * @param string $sSql sql字符串
	 * @param $bIsMaster=false bool 是否主服务器
	 */
	public function query_($sSql,$bIsMaster=false){}

	/**
	 * 选择数据库
	 *
	 * @access public
	 * @param string $sDbName  数据库名字
	 * @param handle $hDbHandle=null 数据库句柄
	 * @return bool
	 */
	public function selectDb($sDbName,$hDbHandle=null){ }

	/**
	 * 获取数据库版本
	 *
	 * @access public
	 * @param int $nLinkid 连接Id
	 * @return void
	 */
	public function databaseVersion($nLinkid=0){}

	/**
	 * 错误日志
	 *
	 * @access public
	 * @param $sMsg string 错误消息
	 * @param $hConnect handel 数据库连接句柄
	 * @return void
	 */
	public function errorLogMessage($sMsg='',$hConnect=null){}

	/**
	 * 返回 当前连接上，某个数据库中 的所有数据表
	 *
	 * @access public
	 * @param $sSql string SQL语句
	 * @param $nOffset int 开始值
	 * @param $nLength int 长度
	 * @param $arrInput=null array 数据
	 * @param $bLimit=true bool 是否带上limit
	 * @return array
	 */
	public function selectLimit($sSql,$nOffset=0,$nLength=30,$arrInput=null,$bLimit=true){}

	/**
	 * 返回当前连接中 所有数据库名称
	 *
	 * @access public
	 * @return array,false
	 */
	public function getDatabaseNameList(){}

	/**
	 * 返回 当前连接上，某个数据库中 的所有数据表
	 *
	 * @access public
	 * @param $sDb=null string 指定的数据库名，如果为null 则使用当前设定的值
	 * @return array
	 */
	public function getTableNameList($sDbName=null){}

	/**
	 * 获取数据库字段信息
	 *
	 * @access public
	 * @param $sTableName string 数据库表名字
	 * @param $sDbName＝null string 数据库名字
	 * @return
	 */
	public function getColumnNameList($sTableName,$sDbName=null){}

	/**
	 * 查询当前连接中，是否存在一个数据库
	 *
	 * @access public
	 * @param $sDbName string 数据库 库名
	 * @return bool
	 */
	public function isDatabaseExists($sDbName){}

	/**
	 * 查询一个数据表是否存在
	 *
	 * @access public
	 * @param $sTableName string 数据表
	 * @param $sDbName=null string 数据库，如果为null 则使用默认
	 * @return bool
	 */
	public function isTableExists($sTableName,$sDbName=null){}

	/**
	 * 返回刚刚插入的行的主键值
	 *
	 * @access public
	 * @return int 如果没有连接或者查询失败,返回0,成功返回ID
	 */
	public function getInsertId(){}

	/**
	 * 获取记录集里面的记录条数 (用于Select操作)
	 *
	 * @access public
	 * @param $hRes=null string 数据库query result结果记录集
	 * @return int 如果上一次无结果集或者记录结果集为空,返回0,否则返回结果集数量
	 */
	public function getNumRows($hRes=null){}

	/**
	 * 获取受到影响的记录数量 (用于Update/Delete/Insert操作)
	 *
	 * @access public
	 * @return int 如果没有连接或者影响记录为空,否则返回影响的行数量
	 */
	public function getAffectedRows(){}

	/**
	 * 锁表
	 *
	 * @param string $sTableName 需要锁定表的名称
	 * @return mixed 成功返回执行结果，失败返回错误对象
	 */
	public function lockTable($sTableName){}

	/**
	 * 对锁定表进行解锁
	 *
	 * @access public
	 * @param string $sTableName 需要锁定表的名称
	 * @return mixed 成功返回执行结果，失败返回错误对象
	 */
	public function unlockTable($sTableName){}

	/**
	 * 设置自动提交模块的方式（针对InnoDB存储引擎）
	 *
	 * < 一般如果是不需要使用事务模式，建议自动提交为1，这样能够提高InnoDB存储引擎的执行效率，如果是事务模式，那么就使用自动提交为0 >
	 *
	 * @access public
	 * @param bool $bAutoCommit 如果是true则是自动提交，每次输入SQL之后都自动执行，缺省为false
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	public function setAutoCommit($bAutoCommit=false){}

	/**
	 * 开始一个事务过程（针对InnoDB引擎，兼容使用 BEGIN 和 START TRANSACTION）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	public function startTransaction(){}

	/**
	 *  事务结束
	 *
	 * @access public
	 * @return void
	 */
	public function endTransaction(){}

	/**
	 * 提交一个事务（针对InnoDB存储引擎）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	public function commit(){}

	/**
	 * 发生错误，会滚一个事务（针对InnoDB存储引擎）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	public function rollback(){}

}
