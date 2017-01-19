<?dyhb
class DbTableEnter{

	/**
	 * 数据表的 schema
	 *
	 * @access public
	 * @var string
	 */
	public $_sSchema;

	/**
	 * 数据表名称
	 *
	 * @access public
	 * @var string
	 */
	public $_sName;

	/**
	 * 数据表前缀
	 *
	 * @access public
	 * @var string
	 */
	public $_sPrefix;

	/**
	 * 主键字段名，如果是多个字段，则以逗号分割，或使用数组
	 *
	 * @access protected
	 * @var string|array
	 */
	protected $_pk;

	/**
	 * 指示是否使用了复合主键
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_bIsCpk;

	/**
	 * 指示主键字段的总数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPkCount;

	/**
	 * 数据表的元数据
	 *
	 * < 元数据是一个二维数组，每个元素的键名就是全小写的字段名，而键值则是该字段的数据表定义。>
	 *
	 * @access protected
	 * @static
	 * @var array
	 */
	protected static $_arrMeta=array();

	/**
	 * 数据表的字段名
	 *
	 * @access protected
	 * @static
	 * @var array
	 */
	protected static $_arrFields=array();

	/**
	 * 指示表数据入口是否已经初始化
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_bInited;

	/**
	 * 分布式服务器（包括非分布式服务器）的当前正在连接的数据库配置信息
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrCurrentDbConfig;

	/**
	 * 数据库查询是否出错
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsError=false;

	/**
	 * 数据库查询出错消息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sErrorMessage;

	/**
	 * 数据访问对象
	 *
	 * @access protected
	 * @var DbConnect
	 */
	protected $_oConnect;

	/**
	 * 数据唯一实例
	 *
	 * @access protected
	 * @var Db
	 */
	protected $_oDb;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param  $arrCondig  array  配置
	 * @return void
	 */
	 //'table_config' =>array(
	 //  //第一个为主库
	 //   array(
	 //
	 //  'db_host'=>'localhost',
	 //  'db_port'=>3360,
	 //  'db_type'=>'mysql',
	 //  'db_user'=>'root',
	 //  'db_password'=>'',
	 //  'db_name'=>'',
	 //  'db_dsn'=>'',
	 //  'db_params'=>'',
	 //  'db_schema'=>'',
	 //  'db_prefix'=>'',
	 //  'table_name'=>'',
	 //  'connect'=>'',
	 //  'pk'=>'',
	 //   ),
	 //
	 // //剩下的都是从库
	 //   array(
	 //
	 //  'db_host'=>'localhost',
	 //  'db_port'=>3360,
	 //  'db_type'=>'mysql',
	 //  'db_user'=>'root',
	 //  'db_password'=>'',
	 //  'db_name'=>'',
	 //  'db_dsn' =>'',
	 //  'db_params'=>'',
	 //  'db_schema'=>'',
	 //  'db_prefix'=>'',
	 //  'table_name'=>'',
	 //  'connect'=>'',
	 //  'pk'=>   '',
	 //   ),
	 // ),
	 //
	 // //普通数据库连接
	 //'table_config' =>array(
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
	public function __construct(array $arrConfig=null){}

	/**
	 * 创建一条记录
	 *
	 * @access public
	 * @param array $arrRow 要插入的记录
	 * @param boolean $bReturnPkValues  是否返回新建记录的主键值
	 * @return array|null
	 */
	public function insert(array $arrRow, $bReturnPkValues=false){}

	/**
	 * 删除符合条件的记录
	 *
	 * @access public
	 * @param mixed $Where
	 * @param ...
	 * @param $Order='' string|array|DbSubSqlOrder Order 子句
	 * @param $Limit='' int(string='5'),array,DbSubSqlLimit Limit 子句
	 * @return void
	 */
	public function delete( $Where /* 最后两个参数为order,limit,如果没有这个条件，请务必在后面添加上null,或者‘’占位 */){}

	/**
	 * 更新记录
	 *
	 * < 如果 $Row 参数中包含所有主键字段的值，并且没有指定 $where 参数，则假定更新主键字段值相同的记录。>
	 * < 如果 $Row 是一个 DbExpression 表达式，则根据表达式内容更新数据库。>
	 *
	 * @access public
	 * @param array|DbExpression $Row 要更新的记录值
	 * @param mixed $Where 更新条件
	 * @param ...
	 * @param $Order=''string,array,DbSubSqlOrder Order 子句
	 * @param $Limit=''int(string='5'),array,DbSubSqlLimit Limit 子句
	 * @return void
	 */
	public function update($Row,$Where=null/* 最后两个参数为order,limit,如果没有这个条件，请务必在后面添加上null,或者‘’占位 */){}

	/**
	 * 发起一个查询，获得一个 DbSqlSelect 查询对象
	 *
	 * @access public
	 * @return DbSelect
	 */
	public function tableSelect(){}

	/**
	 * 为当前数据表的指定字段产生一个序列值
	 *
	 * @access public
	 * @param string $sFieldName  字段名字
	 * @return mixed
	 */
	public function nextId($sFieldName=null,$nStart=1){}

	/**
	 * 返回数据库唯一实例
	 *
	 * @access public
	 * @return Db
	 */
	public function getDb(){}

	/**
	 * 设置数据库唯一实例
	 *
	 * @access public
	 * @param  $oDb  Db  数据库唯一实例
	 * @return Db
	 */
	public function setDb($oDb){}

	/**
	 * 返回该表数据入口对象使用的数据访问对象
	 *
	 * @access public
	 * @return DbConnect
	 */
	public function getConnect(){}

	/**
	 * 设置数据库访问对象
	 *
	 * @access public
	 * @param DbConnect $oConnect 数据库连接对象
	 * @return void
	 */
	public function setConnect(DbConnect $oConnect){}

	/**
	 * 返回数据表的完整名称（含 schema 和前缀）
	 *
	 * @access public
	 * @return string
	 */
	public function getFullTableName(){}

	/**
	 * 返回所有字段的元数据
	 *
	 * @access public
	 * @return array
	 */
	public function columns(){}

	/**
	 * 返回主键字段名
	 *
	 * @access public
	 * @return array
	 */
	public function getPk(){}

	/**
	 * 设置数据表的主键
	 *
	 * @access public
	 * @param array|string $Pk 主键
	 * @return oldValue
	 */
	public function setPk($Pk){}

	/**
	 * 确认是否是复合主键
	 *
	 * @access public
	 * @return boolean
	 */
	public function isCompositePk(){}

	/**
	 * 初始化ModelMeta访问数据入口
	 *
	 * @access public
	 * @return void
	 */
	public function init(){}

	/**
	 * 设置表数据入口使用的数据库访问对象
	 *
	 * < 继承类可以覆盖此方法来自行控制如何设置数据库访问对象。>
	 *
	 * @access protected
	 * @return void
	 */
	protected function setupConnect_(){}

	/**
	 * 设置数据ModelMeta访问名称
	 *
	 * < 继承类可覆盖此方法来自行控制如何设置数据表名称。>
	 *
	 * @access protected
	 * @return void
	 */
	protected function setupTableName_(){}

	/**
	 * 设置当前ModelMeta访问的元数据
	 *
	 * @access protected
	 * @return void
	 */
	protected function setupMeta_(){}

	/**
	 * 设置数据表的主键
	 *
	 * 继承类可覆盖此方法来自行控制如何设置数据表主键。
	 *
	 * @access protected
	 */
	protected function setupPk_(){}

	/**
	 * 设置模型是否错误
	 *
	 * @access protected
	 * @param $bIsError bool 验证是否错误
	 * @static
	 * @return oldValue
	 */
	protected function setIsError($bIsError=false){}

	/**
	 * 设置模型错误消息
	 *
	 * @access public
	 * @static
	 * @param $sErrorMessage string 验证错误消息
	 * @return oldValue
	 */
	protected function setErrorMessage($sErrorMessage=''){}

	/**
	 * SQL 执行是否出错
	 *
	 * access public
	 * @return bool
	 */
	public function isError(){}

	/**
	 * 返回错误代码或信息
	 *
	 * @access public
	 * @return string
	 */
	public function getErrorMessage(){}

}
