<?dyhb
class Backup{

	/**
	 * 备份最大
	 *
	 * @var int( kb )
	 */
	protected $_nMaxSize=2097152; // 2M

	/**
	 * 是否能写入
	 *
	 * @var bool
	 */
	protected $_bIsShort=false;

	/**
	 * 备份每次条数
	 *
	 * @var int
	 */
	protected $_nOffset=300;

	/**
	 * 备份sql语句
	 *
	 * @var string
	 */
	protected $_sDumpSql='';

	/**
	 * sql语句记录
	 *
	 * @var int
	 */
	protected $_nSqlNum=0;

	/**
	 * 错误消息
	 *
	 * @var string
	 */
	protected $_sErrorMessage='';

	/**
	 * 数据库连接
	 *
	 * @var DbConnect
	 */
	protected $_oDbConnect;

	/**
	 * 数据库编码
	 *
	 * @var DbConnect
	 */
	protected $_sDbChar='utf8';

	/**
	 * 类的构造函数
	 *
	 * @access public
	 * @param
	 *
	 * @return void
	 */
	public function __construct(&$oDbConnect,$nMaxSize=0,$sDbChar='utf8'){}

	public function setIsShort($bIsShort=true){}

	public function setMaxSize($nMaxSize){}

	public function getDumpSql(){}

	/**
	 * 获取指定表的定义
	 *
	 * @access public
	 * @param string $sTable 数据表名
	 * @param boolen $bAddDrop 是否加入drop table
	 * @return string 表的定义
	 */
	public function getTableDf($sTable,$bAddDrop=false){}

	/**
	 * 获取指定表的数据定义
	 *
	 * @access public
	 * @param string $sTable 表名
	 * @param int $nPos 备份开始位置
	 * @return int $nPostPos 记录位置
	 */
	public function getTableData($sTable,$nPos){}

	/**
	 * 备份一个数据表
	 *
	 * @access public
	 * @param string $sPath 保存路径表名的文件
	 * @param int $nVol 卷标
	 * @return array $arrTables 未备份完的表列表
	 */
	public function dumpTable($sPath,$nVol){}

	/**
	 * 生成备份文件头部
	 *
	 * @access public
	 * @param int $nVol 文件卷数
	 * @return string $sHead 备份文件头部
	 */
	public function makeHead($nVol){}

	/**
	 *  获取备份文件信息
	 *
	 * @access public
	 * @param string $sPath 备份文件路径
	 * @return array $arrInfo 信息数组
	 */
	static public function getHead($sPath){}

	/**
	 * 将文件中数据表列表取出
	 *
	 * @access public
	 * @param string $sPath 文件路径
	 * @return array 数据列表数据
	 */
	public function getTablesList($sPath){}

	/**
	 * 将数据表数组写入指定文件
	 *
	 * @access public
	 * @param string $sPath 文件路径
	 * @param array $arrData 要写入的数据
	 * @return boolen
	 */
	public function putTablesList($sPath,$arrData){}

	/**
	 * 返回一个随机的名字
	 *
	 * @access public
	 * @static
	 * @return string 随机名称
	 */
	static public function getRandomName(){}

	/**
	 * 返回错误信息
	 *
	 * @access public
	 * @return void
	 */
	public function getErrorMessage(){}

	protected function qualifyStr($sValue){}

}
