<?dyhb
class LangPackage{

	/**
	 * 语言包名称
	 *
	 * @access private
	 * @var string
	 */
	private $_sPackageName='';

	/**
	 * 语言名称
	 *
	 * @access private
	 * @var string
	 */
	private $_sLangName='';

	/**
	 * 语言包文件 的 完整路径
	 *
	 * @access private
	 * @var string
	 */
	private $_sPackagePath='';

	/**
	 * 语句值
	 *
	 * @access private
	 * @var array
	 */
	private $LANGS=array();

	/**
	 * 是否更新
	 *
	 * @access private
	 * @var bool
	 */
	private $_bNeedUpdated=false;

	/**
	 * 语言包的更新时间
	 *
	 * @access private
	 * @var int
	 */
	private $_nUpdateTime=0;

	/**
	 * 语言包目录
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	static private $LANG_PACKAGE_DIRS=array();

	/**
	 * 所有已经载入的语言包
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	static private $LANG_PACKAGES=array();

	/**
	 * 用于查找查找过的路径（用于无法找到语言包的时候抛出异常的信息）
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	static private $_arrHistoryFindLangPath=array();

	/**
	 * 添加一个语言包目录
	 *
	 * @access public
	 * @param string $sPath 目录路径
	 * @static
	 * @return void
	 */
	static public function addPackageDir($sPath){}

	/**
	 * 清空语言包目录
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function clearPackageDir(){}

	/**
	 * 取得一个语言包享员对象
	 *
	 * @access public
	 * @param string $sPackageName 语言包名称
	 * @param string $sLangName='' 语言名称
	 * @return string
	 */
	static public function getPackage($sLangName,$sPackageName){}

	/**
	 * 根据语言包名称，在指定目录内找到语言包
	 *
	 * @access private
	 * @param string $sDir 指定目录
	 * @param string $sLangName 语言名称
	 * @param string $sPackageName 语言包名称
	 * @return string
	 * @static
	 */
	static private function findPackage($sDir,$sLangName,$sPackageName=null){}

	/**
	 * 从指定的语言包文件中读出语句
	 *
	 * @access public
	 * @param string $sPackagePath
	 * @return void
	 */
	public function loadPackageFile($sPackagePath){}

	/**
	 * 创建语言包实例
	 *
	 * @access private
	 * @param string $sLangName 语言名称
	 * @param string $sPackageName 语言包名称
	 * @param string $sPackagePath 语言包文件的完整路径
	 * @return LangPackage
	 */
	static private function createPackage($sLangName,$sPackageName,$sPackagePath){}

	/**
	 * 构造函数，只能通过 self::createPackage()创建
	 *
	 * @access public
	 * @param string $sLangName 语言名称
	 * @param string $sPackageName 语言包名称
	 * @param string $sPackagePath 语言包文件的完整路径
	 * @return void
	 */
	private function __construct($sLangName,$sPackageName,$sPackagePath){}

	/**
	 * 析构函数
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct(){}

	/**
	 * 从语言包文件中读出语句
	 *
	 * @access public
	 * @return array
	 */
	public function load(){}

	/**
	 * 保存到语言包文件
	 *
	 * @access public
	 * @return void
	 */
	public function save(){}

	/**
	 * 配置项数据过滤
	 *
	 * @param string|bool $sValue 待过滤的数据项
	 * @return string
	 */
	private function filterOptionValue($sValue ){}

	/**
	 * 返回语言包名称
	 *
	 * @access public
	 * @return string
	 */
	public function getName(){}

	/**
	 * 返回语言名称
	 *
	 * @access public
	 * @return string
	 */
	public function getLangName(){}

	/**
	 * 设置一条语句
	 *
	 * @access public
	 * @param string $sKey 语句的key
	 * @param string $sValue 语句内容
	 * @return string
	 */
	public function set($sKey,$sValue){}

	/**
	 * 取回一条语句
	 *
	 * @access public
	 * @param string $sKey 语句的Key
	 * @return string
	 */
	public function get($sKey){}

	/**
	 * 检查一条语句是否存在
	 *
	 * @access public
	 * @param string $sKey 语句的Key
	 * @return
	 */
	public function has($sKey){}

	/**
	 * 相对语言包文件是否改动
	 *
	 * @access public
	 * @return bool
	 */
	public function isUpdated(){}

	/**
	 * 取得语言包的更新时间（该时间为所有语言包文件的最新时间）
	 *
	 * @access public
	 * @return int
	 */
	public function getUpdateTime(){}

}
