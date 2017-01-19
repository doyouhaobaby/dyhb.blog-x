<?dyhb
/**
 * 缓存说明
 *
 * <!-- 关于启用子目录  -->
 * <  如果大于 1，则会在缓存目录下创建子目录保存缓存文件。
 *    如果要写入的缓存文件超过 500 个，目录深度设置为 1 或者 2 较为合适。
 *    如果有更多文件，可以采用更大的缓存目录深度。>
 *
 * <!-- 关于serialize -->
 * <  自动序列化数据后再写入缓存，默认为 true
 *    可以很方便的缓存 PHP 变量值（例如数组），但要慢一点。>
 *
 * <!--关于 encoding_filename-->
 * <  编码缓存文件名，默认为 true
 *    如果缓存ID存在非文件名字符，那么必须对缓存文件名编码。>
 *
 * <!-- 关于test_method -->
 * <  检验缓存内容完整性的方式，默认为 crc32
 *   crc32 速度较快，而且安全。md5 速度最慢，但最可靠。strlen 速度最快，可靠性略差。>
 */
class FileCache extends DCache{

	/**
	 * 默认的缓存配置
	 *
	 * @var array
	 */
	protected $_arrOptions=array();

	/**
	 * 固定要写入缓存文件头部的内容
	 *
	 * @var string
	 */
	static protected $_sStaticHead='<?php die(); ?>';

	/**
	 * 固定头部的长度
	 *
	 * @var int
	 */
	static protected $_nStaticHeadLen=15;

	/**
	 * 缓存文件头部长度
	 *
	 * @var int
	 */
	static protected $_nHeadLen=64;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $arrOptions array 初始化配置
	 */
	public function __construct($arrOptions=array()){}

	/**
	 * 是否连接成功
	 *
	 * @access public
	 * @return boolen
	 */
	private function isConnected(){}

	/**
	 * 检查一个缓存在给定时长是否过期
	 *
	 * @access public
	 * @param $sCacheName 缓存项目名字
	 * @param $nTime 缓存时间，单位秒（s）,-1表示存在缓存即有效
	 * @return void
	 */
	public function checkCache($sCacheName,$nTime=-1){}

	/**
	 * 获取一个缓存内容，过期或者不存在返回NULL
	 *
	 * @access public
	 * @param $sCacheName string 缓存项目名字
	 * @return string,null
	 */
	public function getCache($sCacheName){}

	/**
	 * 设置一个缓存内容
	 *
	 * @access public
	 * @param string $sCacheName 缓存名字
	 * @param string $sData 缓存内容
	 * @return string,bool
	 */
	public function setCache($sCacheName,$Data){}

	/**
	 * 删除一个缓存内容
	 *
	 * @access public
	 * @param $sCacheName 缓存名字
	 * @return bool
	 */
	public function deleleCache($sCacheName){}

	/**
	 * 判断一个缓存内容是否存在
	 *
	 * @access public
	 * @param $sCacheName 缓存名字
	 * @return bool
	 */
	public function existCache($sCacheName){}

	/**
	 * 清空缓存
	 *
	 * @access public
	 * @return bool
	 */
	public function clearCache(){}

	/**
	 * 取得变量的存储文件名
	 *
	 * @access private
	 * @param  string $sCacheName 缓存变量名
	 * @return string
	 */
	private function getCacheFilePath($sCacheName){ }

	/**
	 * 写入文件数据到文件 < 缓存到文件  >
	 *
	 * @access public
	 * @param string $sFileName 文件名字
	 * @param string $sData 写入文件内容
	 * return int 文件大小
	 */
	 public function writeData($sFileName,$sData){}

	/**
	 * 获得数据的校验码
	 *
	 * @param string $sData  待加密的数据
	 * @param string $sType  加密方式
	 * @return string
	 */
	protected function encryptData($sData,$sType){}

}
