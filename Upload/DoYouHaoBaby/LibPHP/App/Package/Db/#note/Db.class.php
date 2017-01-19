<?dyhb
class Db{

	/**
	 * 数据库工厂名 < 默认 >
	 *
	 * @access public
	 * @var string
	 */
	static public $_sDefaultFactoryName='DbFactoryMysql';

	/**
	 * 当前数据库工厂名
	 *
	 * @access public
	 * @var string
	 */
	public $_sFactoryName='';

	/**
	 * 当前数据库工厂实例
	 *
	 * @access public
	 * @var DbFactory
	 */
	static public $_oFactory;

	/**
	 * 当前数据库全局对象工厂实例
	 *
	 * @access public
	 * @var DbFactory
	 */
	public $_oGlobalFactory;

	/**
	 * 当前数据库连接对象
	 *
	 * @access public
	 * @var DbFactory
	 */
	public $_oConnect=null;

	/**
	 * Master数据库配置 < 数据库写配置 >
	 *
	 * @access public
	 * @var array
	 */
	static public $_arrWriteDbConfig=array();

	/**
	 * Slave数据库连接 < 数据库读配置 >
	 *
	 * @access public
	 * @var array
	 */
	static  public $_arrReadDbConfig=array();

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
	 * 是否只有一台Master数据库服务器
	 *
	 * @access public
	 * @var bool
	 */
	static public $_bSingleHost=true;

	/**
	 * 初始化的时候是否要连接到数据库
	 *
	 * @access public
	 * @var bool
	 */
	static public $_bIsInitConnect=true;

	/**
	 * 默认数据库连接对象
	 *
	 * @access private
	 * @var Db
	 * @static
	 */
	static private $_oDefaultDbIns;

	/**
	 * Db 数据库架构参数格式
	 *
	 * @const
	 * @var string
	 */
	const PARAM_QM='?'; // 问号作为参数占位符
	const PARAM_CL_NAMED=':'; // 冒号开始的命名参数
	const PARAM_DL_SEQUENCE='$'; // $符号开始的序列
	const PARAM_AT_NAMED='@'; // @开始的命名参数

	/**
	 * Db 数据库架构查询结果返回格式
	 *
	 * @const
	 * @var string
	 */
	const FETCH_MODE_ARRAY=1; // 返回的每一个记录就是一个索引数组
	const FETCH_MODE_ASSOC=2; // 返回的每一个记录就是一个以字段名作为键名的数组

	/**
	 * Db 数据库关联模式
	 *
	 * @const
	 * @var string
	 */
	const HAS_ONE='has_one'; // 一对一关联
	const HAS_MANY='has_many'; // 一对多关联
	const BELONGS_TO='belongs_to';  // 从属关联
	const MANY_TO_MANY='many_to_many'; // 多对多关联

	/**
	 * Db 数据库架构字段和属性名映射
	 *
	 * @const
	 * @var string
	 */
	const FIELD='field'; // 字段
	const PROP='prop'; // 属性

	/**
	 * 数据库dns配置
	 *
	 * <!-- 配置参考 -->
	 *
	 * < 说明：可以只设置一个主库，每一项中的db_dsn,db_params不是必须的 >
	 * < $_arrDns=array(
	 *
	 *  //第一个为主库
	 *  array(
	 *
	 *    'db_host'=>'localhost',
	 *    'db_port'=>3360,
	 *    'db_type'=>'mysql',
	 *    'db_user'=>'root',
	 *    'db_password'=>'',
	 *    'db_name'=>'',
	 *    'db_dsn'=>'',
	 *    'db_params'=>'',
	 *    'db_schema'=>'',
	 *    'db_prefix'=>'',
	 *  ),
	 *
	 *  //剩下的都是从库
	 *  array(
	 *
	 *    'db_host'=>'localhost',
	 *    'db_port'=>3360,
	 *    'db_type'=>'mysql',
	 *    'db_user'=>'root',
	 *    'db_password'=>'',
	 *    'db_name'=>'',
	 *    'db_dsn'=>'',
	 *    'db_params'=>'',
	 *    'db_schema'=>'',
	 *    'db_prefix'=>'',
	 *  )
	 *  ) >
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	 // 以下为数据库ModelMeta使用的配置，比一般的配置多了几项
	 // 多的几项用于轻松实现领域模型
	 //分布式数据库连接
	 //'table_config'=>array(
	 //  //第一个为主库
	 //   array(
	 //   'db_host'=>'localhost',
	 //   'db_port'=>3360,
	 //   'db_type'=>'mysql',
	 //   'db_user'=>'root',
	 //   'db_password'=>'',
	 //   'db_name'=>'',
	 //   'db_dsn'=>'',
	 //   'db_params'=>'',
	 //   'db_schema'=>'',
	 //   'db_prefix'=>'',
	 //   'table_name'=>'',
	 //   'connect'=>'',
	 //   'pk'=>'',
	 //   ),
	 //
	 // //剩下的都是从库
	 //   array(
	 //   'db_host'=>'localhost',
	 //   'db_port'=>'3360,
	 //   'db_type'=>'mysql',
	 //   'db_user'=>'root',
	 //   'db_password'=>'',
	 //   'db_name'=>'',
	 //   'db_dsn'=>'',
	 //   'db_params'=>'',
	 //   'db_schema'=>'',
	 //   'db_prefix'=>'',
	 //   'table_name'=>'',
	 //   'connect'=>'',
	 //   'pk'=>'',
	 //   ),
	 // ),
	 //
	 // //普通数据库连接
	 // 'table_config'=>array(
	 //
	 // 'db_host'=>'localhost',
	 // 'db_port'=>3360,
	 // 'db_type'=>'mysql',
	 // 'db_user'=>'root',
	 // 'db_password'=>'',
	 // 'db_name'=>'',
	 // 'db_dsn'=>'',
	 // 'db_params'=>'',
	 // 'db_schema'=>'',
	 // 'db_prefix'=>'',
	 // 'table_name'=>'',
	 // 'connect'=>'',
	 // 'pk'=>'',
	 // ),
	static public $_arrDsn=array();

	/**
	 * 数据库工厂名
	 *
	 * @access public
	 * @param array $Dsn 数据库全局配置
	 * @return void
	 */
	public function __construct($Dsn=null){}

	/**
	 * 创建一个数据库Db对象实例
	 *
	 * @access public
	 * @param $Dsn='' array|string|null DSN配置
	 * @param $sId=null string|null 数据库连接对象ID
	 * @param $bDefaultIns='' bool 全局实例
	 * @param $bConnect='' bool 连接到数据库
	 * @return Db
	 */
	static public function createDbInstance($Dsn=null,$sId=null,$bDefaultIns=true,$bConnect=true){}

	/**
	 * 返回数据库工厂
	 *
	 * @access public
	 * @return DbFactory
	 */
	public function getFactory(){}

	/**
	 * 连接到数据库服务器
	 *
	 * 传递配置信息，配置信息数组结构：
	 *
	 * <!-- 数据库配置演示 -->
	 *
	 * $arrMasterConfig=array(8) {
	 *
	 *  ["db_type"]=> string(5) "mysql"
	 *  ["db_user"]=> string(4) "root"
	 *  ["db_password"]=> string(6) "123456"
	 *  ["db_host"]=> string(9) "localhost"
	 *  ["db_port"]=> string(4) "3306"
	 *  ["db_name"]=> string(4) "test"
	 *  ["db_dsn"]=> string(0) ""
	 *  ["db_params"]=> string(0) ""
	 * }
	 *
	 * @access public
	 * @param array $arrMasterConfig 主服务器配置数据
	 * @param array $arrSlaveConfig 从服务器配置数据
	 * @param bool $bSingleHost 是否只有一台主机
	 * @param bool $bIsInitConnect 是否初始化数据库连接
	 * @param string $sId 数据库对象连接ID
	 * @return void
	 */
	public function connect($arrMasterConfig=array(),$arrSlaveConfig=array(),$bSingleHost=true,$bIsInitConnect=false,$sId=null){}

	/**
	 * 关闭数据库连接
	 *
	 * @access public
	 * @param $hDbConnect handel 数据库连接句柄
	 * @param $bCloseALL bool 是否关闭所有数据连接
	 * @return bool
	 */
	public function disConnect($hDbConnect=null,$bCloseAll=false){}


	/**
	 * 创建一个数据库Db对象实例 < createDbInstance的别名 >
	 *
	 * @access public
	 * @param $Dsn='' array|string|null DSN配置
	 * @param $sId=null string|null 数据库连接对象ID
	 * @param $bDefaultIns='' bool 全局实例
	 * @param $bConnect='' bool 连接到数据库
	 * @return Db
	 */
	static public function RUN($Dsn=null,$sId=null,$bDefaultIns=true,$bConnect=true){}

	/**
	 * 增加数据库连接 < 相同类型的,从服务器 >
	 *
	 * @access protected
	 * @param mixed $Config 数据库连接信息
	 * @param mixed $nLinkNum 创建的连接序号
	 * @return void
	 */
	public function addConnect($Config,$nLinkNum=null){}

	/**
	 * 切换数据库连接
	 *
	 * @access protected
	 * @param integer $linkNum 创建的连接序号
	 * @return void
	 */
	public function switchConnect($nLinkNum){}

	/**
	 * 设置一个连接对象
	 *
	 * @access public
	 * @param $oConnect DbConnect DbConnect 连接对象
	 * @return
	 */
	public function setConnect(DbConnect $oConnect){}

	/**
	 * 返回连接对象
	 *
	 * @access public
	 * @return DbConnect
	 */
	public function getConnect(){}

	/**
	 * 选择数据库
	 *
	 * @access public
	 * @param string $sDbName 数据库名字
	 * @param handle $hDbHandle=null 数据库句柄
	 * @return bool
	 */
	public function selectDb($sDbName,$hDbHandle=null){}

	/**
	 * 执行 SQL 语句
	 *
	 * @access public
	 * @param $Sql SQL语句
	 * @param $sDb='' string 指定数据库 < 如果为空'' ，则使用当前数据库 >
	 * @return bool|resource
	 */
	public function query($Sql,$sDb=''){}

	/**
	 * 插入数据
	 *
	 * @access public
	 * @param $arrData array 用于操作的数据表的数据
	 * @param $sTableName string 用于操作的数据表
	 * @param  string|array $RstrictedFields 限定只使用哪些字段
	 * @param $bReplace=false bool Repalce 或 Insert
	 * @return bool
	 */
	public function insert(array $arrData,$sTableName='',array $RstrictedFields=null,$bReplace=false){}

	/**
	 * 删除记录
	 *
	 * @access public
	 * @param $sTableName 数据表
	 * @param $Where='' string,array Where 子句
	 * @param $Order='' string Order 子句
	 * @param $Limit='' int(string='5') Limit 子句
	 * @return bool
	 */
	public function delete($sTableName,$arrWhere=null,$Order=null,$Limit=null){}

	/**
	 * 执行 Update
	 *
	 * @access public
	 * @param $sTableName 数据表
	 * @param $Row array 数据值对
	 * @param $Where='' string,array Where 子句
	 * @param $Order='' string Order 子句
	 * @param $Limit='' int(string='5') Limit 子句
	 * @param $RstrictedFields  array 字段
	 * @return bool
	 */
	public function update($sTableName,$Row,array $Where=null,$Limit='',$Order='',array $RstrictedFields=null){}

	/**
	 * 执行数据表查询
	 * 可依据参数 $sReturn 返回不同类型的查询结果： handle,DbRecordSet,DbSqlSelect
	 *
	 * @access public
	 * @param $TableName array|object|string 数据表
	 * @return DbSelect
	 */
	public function select($TableName){}

	/**
	 * 返回数据表的完整名称（含 schema 和前缀）
	 *
	 * @access public
	 * @param  string $sTableName 数据库表名字
	 * @return string
	 */
	public function getFullTableName($sTableName=''){}

	 /**
	 * 执行查询，返回第一条记录的第一个字段
	 *
	 * @access public
	 * @param string $sSql SQL语句
	 * @param array $arrInput 输入数据
	 * @return mixed
	 */
	public function getOne($sSql,$arrInput=null){}

	/**
	 * 执行一个查询并返回记录集，失败时抛出异常
	 *
	 * < getAll() 等同于执行下面的代码：
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
	 * @return mixed
	 */
	public function getRow($sSql,array $arrInput=null){}

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

}
