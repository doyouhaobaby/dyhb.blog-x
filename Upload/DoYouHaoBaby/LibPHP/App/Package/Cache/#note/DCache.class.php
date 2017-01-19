<?dyhb
abstract class DCache{

	/**
	 * 操作句柄
	 *
	 * @var resource
	 * @access protected
	 */
	protected $_hHandel;

	/**
	 * 是否连接
	 *
	 * @var bool
	 * @access protected
	 */
	protected $_bConnected;

	/**
	 * 缓存存储前缀
	 *
	 * @var string
	 * @access protected
	 */
	protected $_sPrefix='~@';

	/**
	 * 缓存连接参数
	 *
	 * @var array
	 * @access protected
	 */
	protected $_arrOptions=array();

	/**
	 * 缓存时间 < 单位s >
	 *
	 * @access protected
	 * @var
	 */
	protected $_nCacheTime=0;

	/**
	 * 是否开启缓存
	 *
	 * @access protected
	 * @var
	 */
	protected $_bCacheEnabled=TRUE;

	/**
	 * 使用内容储存缓存数据
	 *
	 * @access protected
	 * @var
	 */
	static public $CACHES=array();

	/**
	 * 缓存类型列表
	 *
	 * @access public
	 * @var
	 */
	const MEMORY_CACHE='memory';
	const FILE_CACHE='file';
	const APC_CACHE='apc';
	const XCACHE_CACHE='xcache';
	const MEMCACHE_CACHE='memcache';
	const SHMOP_CACHE='shmop';
	const EACCELERATOR_CAHCE='eaccelerator';

	/**
	 * 缓存内容
	 *
	 * @access public
	 * @var
	 */
	public $_sCacheType=self::MEMORY_CACHE;

	/**
	 * 缺省回调处理函数
	 *
	 * @access public
	 * @var
	 */
	static private $Callback ;

	/**
	 * 注册一个 函数(方法)，用户取得系统缺省的当前对象
	 *
	 * @access public
	 * @param $Callback callback|null 函数
	 * @static
	 * @return oldValue
	 */
	static public function regCallback($Callback=null){}

	/**
	 * 取得一个缺省的当前对象
	 *
	 * @access public
	 * @param $Parameter
	 * @static
	 * @return DCache
	 */
	static public function getDefaultCache(){}

	/**
	 * 获取一个缓存内容，过期或者不存在返回NULL
	 *
	 * @access public
	 * @param $sCacheName string 缓存项目名字
	 * @return string,null
	 */
	public function &__get($sCacheName){}

	/**
	 * 设置一个缓存内容
	 *
	 * @access public
	 * @param string $sCacheName 缓存名字
	 * @param mixed $Data 缓存内容
	 * @return string,bool
	 */
	public function __set($sCacheName,$Data){}

	/**
	 * 删除一个缓存内容
	 *
	 * @access public
	 * @param string $sCacheName 缓存名字
	 * @return bool
	 */
	public function __unset($sCacheName){}

	/**
	 * 设置一个配置项数据
	 *
	 * @access public
	 * @param string $sOptionName 配置项名字
	 * @param mixed $Value 配置项数据
	 * @return void
	 */
	public function setOption($sOptionName,$Value){}

	/**
	 * 获取一个配置项数据
	 *
	 * @access public
	 * @param $sOptionName string 配置项名字
	 * return mixed
	 */
	public function getOption($sOptionName){}

	/**
	 * 返回缓存设置
	 *
	 * @access public
	 * @param array|null $arrOptions 需要更新的配置
	 * @return array
	 */
	public function backOptions($arrOptions=null){}

	/**
	 * 读取缓存次数
	 *
	 * @access public
	 * @param int $nTimes 次数
	 * @retrun int
	 */
	public function Q($nTimes=''){}

	/**
	 * 写入缓存次数
	 *
	 * @access public
	 * @param int $nTimes 次数
	 * return int
	 */
	public  function W($nTimes=''){}

	/**
	 * 加密缓存缓存名字
	 *
	 * @access public
	 * @param string $sCacheName 缓存名字
	 * @return string
	 */
	 public function encryptKey($sCacheName){}

	/**
	 * 检查缓存路径是否正确，并且尝试生成路径目录
	 *
	 * @access protected
	 * @return boolen
	 */
	protected function checkCachePath(){}

}
