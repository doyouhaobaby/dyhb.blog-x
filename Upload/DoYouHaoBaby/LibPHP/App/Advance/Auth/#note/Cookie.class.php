<?dyhb
class Cookie{

	/**
	 * 单件实例
	 *
	 * @access private
	 * @var Session
	 * @static
	 */
	static private $_oGlobalInstance;

	/**
	 * Cookie过期时间
	 *
	 * @access protected
	 * @var int
	 * @static
	 */
	static protected $_nExpireSec;

	/**
	 * cookie路径
	 *
	 * @access protected
	 * @var string
	 * @static
	 */
	static protected $_sCookiePath;

	/**
	 * CookieDomain跨域
	 *
	 * @access protected
	 * @var string
	 * @static
	 */
	static protected $_sCookidDomain;

	/**
	 * CookiePrefix 前缀
	 *
	 * @access protected
	 * @var string
	 * @static
	 */
	static protected $_sCookiePrefix;

	/**
	 * CookieSecure 是否通过安全https推送
	 *
	 * @access protected
	 * @var bool
	 * @static
	 */
	static protected $_bCookieSecure;

	/**
	 * 获取Cookie对象实例
	 *
	 * @access public
	 * @param $nExpireSec=900 int 过期时间
	 * @param $sSessionPath string Session路径
	 * @param $sCookidDomain string
	 * @param $sCookieExpire string
	 * @param $bCookieSecure null|bool
	 * @return DSession
	 */
	static public function startCookie( $nExpireSec=900,$sCookiePath=null,$sCookidDomain=null,$sCookiePrefix=null,$bCookieSecure=null){}

	/**
	 * 判断Cookie是否存在
	 *
	 * @access public
	 * @param string $sName Cookie名字
	 * @return void
	 */
	public function issetCookie($sName){}

	/**
	 * 获取某个Cookie值
	 *
	 * @access public
	 * @param string $sName Cookie名字
	 * @return mixted
	 */
	public function getCookie($sName){}

	/**
	 * 设置某个Cookie值
	 *
	 * @access public
	 * @param string $sName Cookie名字
	 * @param mixed $Value Cookie值
	 * @param int $nExpire 过期时间
	 * @param string $sPath Cookie路径
	 * @param string $sDomain 域名
	 * @param bool $bSecure 是否通过安全http发送
	 * @return void
	 */
	public function setCookie($sName,$Value,$nExpire=null,$sPath=null,$sDomain=null,$bSecure=null){}

	/**
	 * 删除某个Cookie值
	 *
	 * @access public
	 * @param string $sName Cookie名字
	 * @access void
	 */
	public function deleteCookie($sName){}

	/**
	 * 清空Cookie值
	 *
	 * @access public
	 * @return int
	 */
	public function clearCookie(){}

}
