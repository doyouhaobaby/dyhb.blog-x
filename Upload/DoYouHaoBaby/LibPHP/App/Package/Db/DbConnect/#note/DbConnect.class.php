<?dyhb
abstract class DbConnect{

	/**
	 * 数据库操作记录
	 *
	 * @access protected
	 * @var string
	 */
	static public $_nQueryCount=0;

	/**
	 * SQL LOG调试
	 *
	 * @access protected
	 * @var string
	 */
	protected $_bDebug=false;

	/**
	 * 指示返回结果集的形式
	 *
	 * @access protected
	 * @var const && int
	 */
	protected $_nFetchMode=Db::FETCH_MODE_ASSOC;

	/**
	 * 默认的 schema
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sSchema='';

	/**
	 * Master数据库配置 < 数据库写配置 >
	 *
	 * @access public
	 * @var array
	 */
	public $_arrWriteDbConfig=array();

	/**
	 * Slave数据库连接 < 数据库读配置 >
	 *
	 * @access public
	 * @var array
	 */
	public $_arrReadDbConfig=array();

	/**
	 * 当前数据库连接配置
	 *
	 * @access public
	 * @var array
	 */
	public $_arrCurrentDbConfig=array();

	/**
	 * 是否只有一台Master数据库服务器
	 *
	 * @access public
	 * @var bool
	 */
	public $_bSingleHost=true;

	/**
	 * 初始化的时候是否要连接到数据库
	 *
	 * @access public
	 * @var bool
	 */
	public $_bIsInitConnect=false;

	/**
	 * Master数据库连接
	 *
	 * @access public
	 * @var array
	 */
	public $_hWriteConnect=null;

	/**
	 * Slave数据库连接
	 *
	 * @access public
	 * @var array
	 */
	public $_arrHReadConnect=array();

	/**
	 * 所有数据库连接
	 *
	 * @access public
	 * @var array
	 */
	public $_arrHConnect=array();

	/**
	 * 当前数据库连接对象
	 *
	 * @access protected
	 * @var DbConnect
	 */
	public $_hCurrentConnect=null;

	/**
	 * 是否永久连接数据库
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bPConnect=false;

	/**
	 * 数据库数字版本
	 *
	 * @access protected
	 * @var int
	 */
	public $_nVersion;

	/**
	 * 数据库是否已经连接上了
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bConnected;

	/**
	 * 是否开启日志记录
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bLogEnabled=FALSE;

	/**
	 * 记录数据库最后运行的SQL语句
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_sLastSql='';

	/**
	 * 数据库查询结果资源句柄
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_hQueryResult=null;

	/**
	 * 是否记录SQL运行时间
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsRuntime=true;

	/**
	 * SQL执行时间
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nRunTime=0;

	/**
	 * 缺省数据库
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sDefaultDatabase='';

	/**
	 * 事务启动次数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nTransTimes=0;

	/**
	 * 当前表主键
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sPrimary;

	/**
	 * 当前表自动增长
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sAuto;

	/**
	 * 指示是否将查询结果中的字段名转换为全小写
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_bResultFieldNameLower=false;

	/**
	 * 指示使用何种样式的参数占位符
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sParamStyle=Db::PARAM_QM;

	/**
	 * 用于描绘 true、false 和 null 的数据库值
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nTrueValue=1; //int
	protected $_nFalseValue=0; //int
	protected $_sNullValue='NULL'; //string

	/**
	 * 指示驱动是否支持原生的参数绑定
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_bBindEnabled=false;

	/**
	 * 数据库表达式
	 *
	 * @var array
	 */
	protected $_arrComparison=array();

	public function __construct(){}

	/**
	 * 数据库调试 记录当前SQL
	 *
	 * @access protected
	 * @return void
	 */
	protected function debug(){}

	/**
	 * 查询次数更新或者查询
	 *
	 * @access public
	 * @param mixed $nTimes
	 * @return void
	 */
	public function Q($nTimes=''){}

	/**
	 * 连接数据库服务器
	 *
	 * 传递配置信息，配置信息数组结构：
	 *
	 * <!-- 数据库配置演示 -->
	 *
	 * $arrMasterConfig=array(8){
	 *
	 *  ["db_type"]=> string(5)"mysql"
	 *  ["db_user"]=> string(4)"root"
	 *  ["db_password"]=> string(6)"123456"
	 *  ["db_host"]=> string(9)"localhost"
	 *  ["db_port"]=> string(4)"3306"
	 *  ["db_name"]=> string(4)"test"
	 *  ["db_dsn"]=> string(0)""
	 *  ["db_params"]=> string(0)""
	 *  ["db_schema"]=> ''
	 *  ["connect"]=> ''
	 *  ["pk"]=>''
	 *  ["table_name"]
	 * }
	 *
	 * @access public
	 * @param array $arrMasterConfig 主服务器配置数据
	 * @param array $arrSlaveConfig 从服务器配置数据
	 * @param bool $bSingleHost 是否只有一台主机
	 * @param bool $bIsInitConnect 是否初始化数据库连接
	 * @return void
	 */
	public function connect($arrMasterConfig=array(),$arrSlaveConfig=array(),$bSingleHost=true,$bIsInitConnect=false){}

	/**
	 * 数据库调试 记录当前SQL
	 *
	 * @access protected
	 * @return void
	 */
	protected function debug(){}

	/**
	 * 查询次数更新或者查询
	 *
	 * @access public
	 * @param mixed $nTimes
	 * @return void
	 */
	public function Q($nTimes=''){}

	/**
	 * 连接到MySQL数据库公共方法
	 *
	 * @access public
	 * @param mixed $Config 配置
	 * @param num $nLinkid 连接
	 * @return void
	 */
	abstract public function commonConnect($Config='',$nLinkid=0);

	/**
	 * 关闭数据库连接
	 *
	 * @access public
	 * @param $hDbConnect handel|null 数据库连接句柄
	 * @param $bCloseALL bool 是否关闭所有数据连接
	 * @return bool
	 */
	abstract public function disConnect($hDbConnect=null,$bCloseAll=false);

	/**
	 * 切换数据库连接
	 *
	 * @access protected
	 * @param integer $linkNum 创建的连接序号
	 * @return void
	 */
	public function switchConnect($nLinkNum){}

	/**
	 * 增加数据库连接 < 相同类型的 >
	 *
	 * @access protected
	 * @param mixed $Config 数据库连接信息
	 * @param mixed $nLinkNum 创建的连接序号
	 * @return void
	 */
	public function addConnect($Config,$nLinkNum=null){}

	/**
	 * 获取Master的写数据连接
	 *
	 * @access public
	 * @return handle,bool
	 */
	public function writeConnect(){}

	/**
	 * 获取Slave的读数据连接
	 *
	 * @access public
	 * @return handel,bool
	 */
	public function readConnect(){}

   /**
	 * 执行SQL语句
	 *
	 * @access public
	 * @param string $sSql sql字符串
	 * @param $bIsMaster=false bool 是否主服务器
	 */
	abstract public function query_($sSql,$bIsMaster=false);

	/**
	 * 选择数据库
	 *
	 * @access public
	 * @param string $sDbName 数据库名字
	 * @param handle $hDbHandle=null 数据库句柄
	 * @return bool
	 */
	abstract public function selectDb($sDbName,$hDbHandle=null);

	/**
	 * 获取数据库版本
	 *
	 * @access public
	 * @param int $nLinkid 连接Id
	 * @return void
	 */
	abstract public function databaseVersion($nLinkid=0);

	/**
	 * 错误日志
	 *
	 * @access public
	 * @param $sMsg string 错误消息
	 * @param $hConnect handel 数据库连接句柄
	 * @return void
	 */
	abstract public function errorLogMessage($sMsg='',$hConnect=null);

	/**
	 * 返回 当前连接上，某个数据库中 的所有数据表
	 *
	 * @access public
	 * @param $sSql string SQL语句
	 * @param $nOffset int 开始值
	 * @param $nLength int 长度
	 * @param $arrInput=null array 数据
	 * @return array
	 */
	abstract public function selectLimit($sSql,$nOffset=0,$nLength=30,$arrInput=null);

	/**
	 * 返回当前连接中 所有数据库名称
	 *
	 * @access public
	 * @return array,false
	 */
	abstract public function getDatabaseNameList();

	/**
	 * 返回 当前连接上，某个数据库中 的所有数据表
	 *
	 * @access public
	 * @param $sDb=null string 指定的数据库名，如果为null 则使用当前设定的值
	 * @return array
	 */
	abstract public function getTableNameList($sDbName=null);

	/**
	 * 获取数据库字段信息
	 *
	 * @access public
	 * @param $sTableName string 数据库表名字
	 * @param $sDbName＝null string 数据库名字
	 * @return
	 */
	abstract public function getColumnNameList($sTableName,$sDbName=null);

	/**
	 * 查询当前连接中，是否存在一个数据库
	 *
	 * @access public
	 * @param $sDbName string 数据库 库名
	 * @return bool
	 */
	abstract public function isDatabaseExists($sDbName);

	/**
	 * 查询一个数据表是否存在
	 *
	 * @access public
	 * @param $sTableName string 数据表
	 * @param $sDbName=null string 数据库，如果为null 则使用默认
	 * @return bool
	 */
	abstract public function isTableExists($sTableName,$sDbName=null);

	/**
	 * 返回数据表的完整名称（含 schema 和前缀）
	 *
	 * < 获取当前正在连接的数据库全名 >
	 *
	 * @access public
	 * @param  string $sTableName  数据库表名字
	 * @return string
	 */
	public function getFullTableName($sTableName=''){}

	/**
	 * 数据库NULL结果处理
	 *
	 * @access public
	 * @param $Value mixed 待处理的值
	 */
	public function dumpNullString($Value){}

	/**
	 * 执行 SQL 语句
	 *
	 * @access public
	 * @param $Sql DbGenericType::$_sql SQL语句
	 * @param $sDb=null string 指定数据库 < 如果为空'' ，则使用当前数据库 >
	 * @return bool,resource
	 */
	public function query($Sql,$sDb=''){}

	/**
	 * 执行一个查询，返回一个查询对象或者 boolean 值，出错时抛出异常
	 *
	 * < $sSql 是要执行的 SQL 语句字符串，而 $arrInputarr 则是提供给 SQL 语句中参数占位符需要的值。>
	 *
	 * < 如果执行的查询是诸如 INSERT、DELETE、UPDATE 等不会返回结果集的操作，
	 *   则 exec()执行成功后会返回 true，失败时将抛出异常。>
	 *
	 * < 如果执行的查询是 SELECT 等会返回结果集的操作，
	 *   则 exec()执行成功后会返回一个 DbResult 对象，失败时将抛出异常。>
	 *
	 * < DbResult 对象封装了查询结果句柄，而不是结果集。
	 *   因此要获得查询的数据，需要调用 DbResult 的 fetchAll()等方法。>
	 *
	 * < 如果希望执行 SQL 后直接获得结果集，可以使用驱动的 getAll()、getRow()等方法。>
	 *
	 * <!-- example: -->
	 *
	 * < $sSql="INSERT INTO posts(title,body)VALUES(?,?)";
	 *   $dbo->exec($sSql,array($title,$body)); >
	 *
	 * <!-- example: -->
	 *
	 * < $sSql="SELECT * FROM posts WHERE post_id < 12";
	 *   $handle=$dbo->exec($sSql);
	 *   $rowset=$handle->fetchAll();
	 *   $handle->free(); >
	 *
	 * @access public
	 * @param  string $sSql  SQL语句
	 * @param  array|string('')$arrInput 占位符值
	 * @return DbResult
	 */
	public function exec($sSql,$arrInput=null){}

	/**
	 * 返回刚刚插入的行的主键值
	 *
	 * @access public
	 * @return int 如果没有连接或者查询失败,返回0,成功返回ID
	 */
	abstract public function getInsertId();

	/**
	 * 获取记录集里面的记录条数(用于Select操作)
	 *
	 * @access public
	 * @param $hRes=null string 数据库query result结果记录集
	 * @return int 如果上一次无结果集或者记录结果集为空,返回0,否则返回结果集数量
	 */
	abstract function getNumRows($hRes=null);

	/**
	 * 获取受到影响的记录数量(用于Update/Delete/Insert操作)
	 *
	 * @access public
	 * @return int 如果没有连接或者影响记录为空,否则返回影响的行数量
	 */
	abstract public function getAffectedRows();

	/**
	 * 锁表表
	 *
	 * @param string $sTableName 需要锁定表的名称
	 * @return mixed 成功返回执行结果，失败返回错误对象
	 */
	abstract public function lockTable($sTableName);

	 /**
	 * 对锁定表进行解锁
	 *
	 * @access public
	 * @param string $sTableName 需要锁定表的名称
	 * @return mixed 成功返回执行结果，失败返回错误对象
	 */
	abstract public function unlockTable($sTableName);

	/**
	 * 设置自动提交模块的方式（针对InnoDB存储引擎）
	 * 一般如果是不需要使用事务模式，建议自动提交为1，这样能够提高InnoDB存储引擎的执行效率，如果是事务模式，那么就使用自动提交为0
	 *
	 * @access public
	 * @param bool $bAutoCommit 如果是true则是自动提交，每次输入SQL之后都自动执行，缺省为false
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	abstract public function setAutoCommit($bAutoCommit=false);

	/**
	 * 开始一个事务过程（针对InnoDB引擎，兼容使用 BEGIN 和 START TRANSACTION）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	abstract public function startTransaction();

	/**
	 *  事务结束
	 *
	 * @access public
	 * @return void
	 */
	abstract public function endTransaction();

	/**
	 * 提交一个事务（针对InnoDB存储引擎）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	abstract public function commit();

	/**
	 * 发生错误，会滚一个事务（针对InnoDB存储引擎）
	 *
	 * @access public
	 * @return mixed 成功返回true，失败返回错误对象
	 */
	abstract public function rollback();

	/**
	 * 执行查询，返回第一条记录的第一个字段
	 *
	 * @access public
	 * @param string $sSql SQL语句
	 * @param array $arrInput 输入数据
	 * @param bool $bLimit 是否加上limit限制
	 * @return mixed
	 */
	public function getOne($sSql,$arrInput=null,$bLimit=true){}

	/**
	 * 执行一个查询并返回记录集，失败时抛出异常
	 *
	 * < getAll()等同于执行下面的代码：
	 *   $rowset=$oDb->exec($sSql,$arrInput)->fetchAll(); >
	 *
	 * @access public
	 * @param string $sSql
	 * @param array $arrInput
	 * @return array
	 */
	public function getAllRows($sSql,array $arrInput=null){}

	/**
	 * 执行查询，返回第一条记录
	 *
	 * @param string $sSql
	 * @param array $arrInput
	 * @param bool $bLimit 是否加上limit限制
	 * @return mixed
	 */
	public function getRow($sSql,array $arrInput=null,$bLimit=true){}

	/**
	 * 执行查询，返回结果集的指定列
	 *
	 * @access public
	 * @param string|resource $sSql
	 * @param int $nCol 要返回的列，0 为第一列
	 * @param array $arrInput
	 * @return mixed
	 */
	public function getCol($sSql,$nCol=0,array $arrInput=null){}

	/**
	 * 取得数据库表达式
	 *
	 * @access public
	 * @return array
	 */
	public function getComparison(){}

	/**
	 * 取得数据库是否绑定原生参数
	 *
	 * @access public
	 * @return bool
	 */
	public function getBindEnabled(){}

	/**
	 * 取得真值
	 *
	 * @access public
	 * @return int
	 */
	public function getTrueValue(){}

	/**
	 * 取得错误值
	 *
	 * @access public
	 * @return int
	 */
	public function getFalseValue(){}

	/**
	 * 取得Null值
	 *
	 * @access public
	 * @return string
	 */
	public function getNullValue(){}

	/**
	 * 取得当前占位符
	 *
	 * @access public
	 * @return string
	 */
	public function getParamStyle(){}

	/**
	 * 取得字段是否转化为小写
	 *
	 * @access public
	 * @return bool
	 */
	public function getResultFieldNameLower(){}

	/**
	 * 取得字段是否转化为小写
	 *
	 * access public
	 * @param bool $bIsLower
	 * @return oldValue
	 */
	public function setResultFieldNameLower($bIsLower=true){}

	/**
	 * 设置是否开启日志
	 *
	 * access public
	 * @param bool $bLogEnabled
	 * @return oldValue
	 */
	public function setLogEnabled($bLogEnabled=true){}

	/**
	 * 设置一个数据库连接句柄到对象中
	 *
	 * @access public
	 * @param $hConnectHandle handle 数据库连接句柄
	 * @return oldValue
	 */
	public function setConnectHandle($hConnectHandle){}

	/**
	 * 数据库是否已经连接上
	 *
	 * @access public
	 * @return bool
	 */
	public function isConnected(){}

	/**
	 * 获取当前数据库名字
	 *
	 * @access public
	 * @return string
	 */
	public function getCurrentDb(){}

	/**
	 * 返回最后查询资源
	 *
	 * @access public
	 * @return string
	 */
	public function getQueryResult(){}

	/**
	 * 获取当前操作的数据库连接资源
	 *
	 * @access public
	 * @return resouce 返回当前正在执行操作的数据库链接资源
	 */
	public function getCurrentConnect(){}

	/**
	 * 返回错误代码
	 *
	 * @access public
	 * @return string
	 */
	public function getErrorCode(){}

	/**
	 * 返回数据库对象对应的 schema
	 *
	 * @return string
	 */
	public function getSchema(){}

	/**
	 * 返回当前数据库连接的前缀
	 *
	 * @access public
	 * @return string
	 */
	public function getTablePrefix(){}

	/**
	 * 返回SQL最后操作的数据库记录结果集
	 *
	 * @access public
	 * @return mixed 最后结果集，可能是数组或者普通单个元素值
	 */
	public function getDbLastRecord(){}

	/**
	 * 设置最后执行的SQL语句
	 *
	 * @access protected
	 * @param $Sql string,DbSql
	 * @return oldValue
	 */
	protected function setLastSql($Sql){}

	/**
	 * 获取最后执行的Sql语句
	 *
	 * @access public
	 * @return string
	 */
	public function getLastSql(){}

	/**
	 * 设置查询耗时
	 *
	 * @access protected
	 * @param $nSpecSec num 查询时间
	 * @return oldValue
	 */
	protected function setQueryTime($nSpecSec){}

	/**
	 * 取得查询耗时
	 *
	 * @access public
	 * @return int
	 */
	public function getQueryTime(){}

	/**
	 * 获取执行时间
	 *
	 * @access public
	 * @return float
	 */
	public function getQueryFormatTime(){}

	/**
	 * 获取事务启动次数
	 *
	 * @access public
	 * @return int
	 */
	public function getTransTimes(){}

	/**
	 * 获取主键
	 *
	 * @access public
	 * @return string
	 */
	public function getPrimary(){}

	/**
	 * 获取自动增长字段
	 *
	 * @access public
	 * @return string
	 */
	public function getAuto(){}

	/**
	 * 设置数据库永久连接
	 *
	 * @access protected
	 * @param $nSpecSec int 查询时间
	 * @return oldValue
	 */
	protected function setPConnect($bPConnect){}

	/**
	 * 获取数据库是否永久连接
	 *
	 * @access public
	 * @return string
	 */
	public function getPConnect(){}

	/**
	 * 获取自动数据库数字版本
	 *
	 * @access public
	 * @return string
	 */
	public function getVersion(){}

	/**
	 * 获取数据库列表
	 *
	 * @access public
	 * @return array
	 */
	public function getDatebaseList(){}

	/**
	 * 获得完全限定名 < 字段 >
	 *
	 * access public
	 * @param string $sName
	 * @param string $sAlias
	 * @param string|null $sAs
	 * @return string
	 */
	public function qualifyId($sName,$sAlias=null,$sAs=null){}

	abstract public function identifier($sName);

	/**
	 * 将 SQL 中用“[]”指示的字段名转义为完全限定名
	 *
	 * @access public
	 * @param string $sSql 要处理的 SQL 字符串
	 * @param string $sTableName 转义字段名时，使用什么数据表名称
	 * @param array|null $arrMapping 字段名映射，用于将字段名转换为映射名
	 * @param callback|null $hCallback 如果提取到数据表名称，则调用回调函数进行转换
	 * @return string 转义后的 SQL 字符串
	 */
	public function qualifySql($sSql,$sTableName,array $arrMapping=null,$hCallback=null){ }

	/**
	 * 获得多个完全限定名
	 *
	 * @access protected
	 * @param array|string $Names 名字
	 * @param string|null $sAs AS
	 * @return array
	 */
	protected function qualifyIds($Names,$sAs=null){}

	/**
	 * 将 SQL 语句中的参数占位符替换为相应的参数值
	 *
	 * @access protected
	 * @param string $sSql 要处理的 SQL 字符串
	 * @param array|null $arrParams 占位符对应的参数值
	 * @param enum|null $ParamStyle 占位符样式 < 枚举 >
	 * @param boolean $bReturnParametersCount 是否返回占位符个数
	 * @return string|array
	 */
	protected function qualifyInto($sSql,array $arrParams=null,$ParamStyle=null,$bReturnParametersCount=false){}

	/**
	 * 转义值
	 *
	 * < 为了能够在 SQL 语句中安全的插入数据，应该用 qualifyStr() 方法将数据中的特殊字符转义 >
	 *
	 * < example:
	 *   $sParam = "It's live";
	 *   $sParam = $dbo->qualifyStr($sParam);
	 *   $sSql = "INSERT INTO posts (title) VALUES ({$sParam})";
	 *   $dbo->exec($sSql); >
	 *
	 * < 但更有效，而且更简单的方式是使用参数占位符：
	 *
	 * < example:
	 *   $sParam = "It's live";
	 *   $sSql = "INSERT INTO posts (title) VALUES (?)";
	 *   $dbo->exec($sSql,array($sParam)); >
	 *
	 * < 而且对于 Oracle 等数据库，由于限制每条 SQL 语句不能超过 4000 字节，
	 *   因此在插入包含大量数据的记录时，必须使用参数占位符的形式。>
	 *
	 * < example:
	 *   $sTitle = isset($POST['title']) ? $POST['title'] : null;
	 *   $sBody = isset($POST['body']) ? $POST['body'] : null; >
	 *
	 *   ... 检查 $title、$body 是否为空 ...
	 *
	 * < $sSql = "INSERT INTO posts (title,body) VALUES (:title,:body)";
	 *   $dbo->exec($sql,array('title' => $sTitle,'body' => $sBody)); >
	 *
	 * @access public
	 * @param mixed $Value 值
	 * @return string
	 */
	protected function qualifyStr($Value){}

	/**
	 * where分析
	 *
	 * @access protected
	 * @param mixed $Where
	 * @return string
	 */
	protected function qualifyWhere($Where){}

	/**
	 * 特殊条件分析
	 *
	 * @access protected
	 * @param string $sKey
	 * @param mixed $val
	 * @return string
	 */
	protected function qualifyDyhbWhere($sKey,$val){}

	/**
	 * 取得规范化的表名字
	 *
	 * @access protected
	 * @param string $sTableName 表名字
	 * @param string $sSchema Schema
	 * @param string $sAlias 别名
	 * @return array
	 */
	 protected function qualifyTable($sTableName,$sSchema=null,$sAlias=null){}

	/**
	 * 取得规范化的字段名字
	 *
	 * @access public
	 * @param string $sFieldName 字段名字
	 * @param string $sTableName 表名字
	 * @param string $sSchema Schema
	 * @param string $sAlias 别名
	 * @return array
	 */
	public function qualifyField($sFieldName,$sTableName=null,$sSchema=null,$sAlias=null){}

	/**
	 * 返回输入数组键名及其对应的参数占位符和转义后的字段名
	 *
	 * @access public
	 * @param array $arrInput  输出数据
	 * @param array $arrRestrictedFields 字段
	 * @param string $sParamStyle 占位符
	 * @return array
	 */
	public function getPlaceHolder(array $arrInput,array $arrRestrictedFields=null,$sParamStyle=null){}

	abstract public function nextId($sTableName,$sFieldName,$nStartValue=1);

	/**
	 * 分析 SQL 中的字段名、查询条件，返回符合规范的 SQL 语句
	 *
	 * @access public
	 * @param string $sTableName
	 * @return string
	 */
	public function parseSql($sTableName){}

	/**
	 * 分析 SQL 中的字段名、查询条件，返回符合规范的 SQL 语句（内部调用版本）
	 *
	 * < 与 parseSQL() 的区别在于 parseSQLInternal() 用第三参数来传递所有的占位符参数及参数值。
	 *   并且 parseSQLInternal() 的返回结果是一个数组，
	 *   分别由处理后的 SQL 语句、从 SQL 语句中分析出来的数据表名称、分析用到的参数个数组成。 >
	 *
	 * < list($sql,$used_tables,$args_count)=parseSQLInternal(...); >
	 *
	 * @access public
	 * @param string $sTableName
	 * @param array $arrArgs
	 * @return array
	 */
	public function parseSqlInternal($sTableName,array $arrArgs=null){}

	/**
	 * 按照模式 2（数组）对查询条件进行分析
	 *
	 * @param string $sTableName
	 * @param array $arrValue
	 * @param array $arrArgs
	 * @return array
	 */
	public function parseSqlArray_($sTableName,array $arrValue,array $arrArgs){}

	/**
	 * 按照模式 1（字符串）对查询条件进行分析
	 *
	 * @param string $table_name
	 * @param string $where
	 * @param array $args
	 *
	 * @return array
	 */
	public function parseSqlString_($sTableName,$sWhere,array $arrArgs){}

	abstract public function metaColumns($sTableName);

	/**
	 * 还原SQL
	 *
	 * @access public
	 * @param $sSql string SQL语句
	 * @param $arrInput array|null 占位符值
	 * @return string
	 */
	public function fakeBind($sSql,$arrInput){}

}
