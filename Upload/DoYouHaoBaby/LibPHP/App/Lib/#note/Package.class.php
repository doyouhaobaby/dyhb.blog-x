<?dyhb
class Package{

	/**
	 * 对象注册表
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	static private $OBJECTS=array();

	/**
	 * 类搜索路径
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	static private $CLASS_PATHS=array();

	/**
	 * DoYouHaoBaby Framework类和接口命名查找正则
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	static private $_arrClassRegex=array();

	/**
	 * 自动加载
	 *
	 * @access private
	 * @var bool
	 * @static
	 */
	 static private $_bAutoLoad=true;

	/**
	 * ClassPath文件名字
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	 static private $CLASS_PATH='Class.inc.php';


	/**
	 * 已经导入的包Dir
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrImportedPackDir=array();

	/**
	 * class文件命名规则正则
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrClassFilePat=array();

	/**
	 * interface文件命名规则正则
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrInterPat=array() ;

	/**
	 * instance文件命名规则正则
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrInsPat=array();

	/**
	 * Not find files
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrNotFiles=array();

	/**
	 * 包对照表
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	 static private $_arrSystemPackage=array();

	/**
	 * 导入整个包
	 *
	 * @access public
	 * @param string $sPackage 被导入的包的路径或包的名字
	 * @param bool $bForce 强制重建Classes Path文件
	 * @static
	 * @return void
	 */
	static public function import($sPackage,$bForce=false){}

	/**
	 * 添加一个包路径
	 *
	 * @access public
	 * @param string $sPackageName 包名字
	 * @param string $sPackagePath 包路径
	 * @return void
	 * @static
	 */
	static public function addPackagePath($sPackageName,$sPackagePath){}

	/**
	 * 注册类-类文件
	 *
	 * @access public
	 * @param string $sClass 类名
	 * @param string $sPath 类路径
	 * @return void
	 * @static
	 */
	static public function regClass($sClass,$sPath){}

	/**
	 * 导入一个类
	 *
	 * @access public
	 * @param string $sClass 类名或类文件完整路径
	 * @param string|null $sPackageName=null 类文件的路径或所在包的名字
	 * @static
	 * @return void
	 */
	static public function importC($sClass,$sPackageName=null){}

	/**
	 * 导入一个接口
	 *
	 * @access public
	 * @param string $sInterface 类名或类文件完整路径
	 * @param string $sPackage=null 类所在的包
	 * @static
	 * @return void
	 */
	static public function importI($sInterface,$sPackage=null){}

	/**
	 * 自动加载类/接口文件开关
	 *
	 * @access public
	 * @param bool $bAutoload=true 是否自动加载
	 * @return $bOldValue
	 */
	static public function setAutoload($bAutoload){}

	/**
	 * 自动加载类/接口文件状态
	 *
	 * @access public
	 * @return bool
	 */
	static public function getAutoload(){}

	/**
	 * __autoload 的实际处理函数
	 *
	 * @access public
	 * @param string $sClassName 正在载入的类名或接口名
	 * @static
	 * @return void
	 */
	static public function autoLoad($sClassName){}

	/**
	 * 判断是否存在接口或者方法
	 *
	 * < 在执行php原生函数class_exists()或interface_exists()的时候
	 *   如果存在符合文件命名规则的类(接口)文件，框架会自动加载 >
	 * < 此方法与之效果等同，但是提供是否自动加载的选择。>
	 *
	 * @access public
	 * @param string $sClassName 类或接口名称
	 * @param bool $bIntere=false 指示参数$sClassName是否是一个接口
	 * @param string $bAutoload=false 如果依据文件命名规则找到类文件，是否自动加载
	 * @return bool
	 */
	static public function classExists($sClassName,$bInter=false,$bAutoload=false){}


	/**
	 * 通过包的名字得到完整路径
	 *
	 * @access public
	 * @param string $sPackName 包的名字，DoYouHaoBaby的一个代表包
	 * @static
	 * @return string|null
	 */
	static public function getPackagePath($sPackName){}

	/**
	 * 扫描目录下的类
	 *
	 * @access private
	 * @param string $sDirectory 待扫描的路径
	 * @param string $sPreFilename
	 * @access private
	 * @static
	 * @return array
	 */
	static private function viewClass($sDirectory,$sPreFilename=''){}

	/**
	 * 给入一个类（或接口）的名称，按照DoYouHaoBaby的文件命名规则到指定的目录中寻找该类（或接口）的文件
	 *
	 * @access public
	 * @param string|array $Dirs 指定的目录，可以是array（多个目录）,或string（单个目录）
	 * @param string $sClassName 类（或接口）名称
	 * @param array $arrPat 命名规则
	 * @static
	 * @return string|false 如果没有找到，返回false
	 */
	static public function findClassFile($sClassName,$Dirs=null,$arrPat=null){}

	/**
	 * 将一个JS文件目录整个打包
	 *
	 * @access private
	 * @param string $sDir 打包目录
	 * @return void
	 */
	static private function packupJs($sDir,$sPackagePath){}

}
