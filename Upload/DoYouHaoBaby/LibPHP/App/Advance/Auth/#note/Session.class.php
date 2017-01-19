<?dyhb
class Session{

	/**
	 * 单件实例
	 * 
	 * @access private
	 * @var Session
	 * @static 
	 */
	static private $_oGlobalInstance;

	/**
	 * session过期时间
	 * 
	 * @access protected
	 * @static
	 * @var int
	 */
	static protected $_nExpireSec;

	/**
	 * session识别名字
	 * 
	 * @access protected
	 * @static
	 * @var Session 
	 */
	static protected $_sSessionKey;

	/**
	 * session路径
	 * 
	 * @access protected
	 * @static
	 * @var string
	 */
	static protected $_sSessionPath;

	/**
	 * CookieDomain跨域session访问
	 * 
	 * @access protected
	 * @static
	 * @var string
	 */
	static protected $_sCookidDomain;

	/**
	 * Session序列化回调函数
	 * 
	 * @access protected
	 * @static
	 * @var callback
	 */
	static protected $_SesssionCallback;

	/**
	 * Session前缀
	 * 
	 * @access protected
	 * @static
	 * @var string
	 */
	static protected $_sSessionPrefix;

	/**
	 * 获取Session对象实例
	 * 
	 * @access public
	 * @param $nExpireSec=900 int 过期时间
	 * @param $sSessionKey=900 string
	 * @param $sSessionPrefix string  Session前缀
	 * @param $sSessionPath string  Session路径
	 * @param $sCookidDomain string
	 * @param $SesssionCallback callback
	 * @return DSession
	 */
	static public function startSession( $nExpireSec=900,$sSessionKey=null,$sSessionPrefix=null,$sSessionPath=null,$sCookidDomain=null,$SesssionCallback=null){}

	/**
	 * Session 初始化
	 *
	 * @access private
	 * @return boolean
	 */
	private function init_(){}

	/**
	 * 启动Session
	 *   
	 * @access public
	 * @return void
	 */
	private function start(){}

	/**
	 * 只能通过 $this->startSession() 创建对象
	 * 
	 * @access private 
	 * @return void 
	 */
	private function session(){}

	/**
	 * 取回 Session 键名
	 * 
	 * @access public
	 * @return string
	 */
	public function getSessionKey(){}

	/**
	 * 设置变量 
	 *
	 * @access public 
	 * @param $sVarName string 变量名称 
	 * @param $Value=null mxied 变量值 
	 * @return oldValue 
	 * @see D$this->setVariable()
	 */
	public function setVariable ($sVarName,$Value=null){}

	/**
	 * 取回变量名字 
	 *
	 * @access public
	 * @param $sVarName string 变量名称 
	 * @return mxied 
	 * @see D$this->getVariable()
	 */
	public function getVariable($sVarName){}

	/**
	 * 获取变量引用 
	 *
	 * @access public 
	 * @param $sVarName string 变量名称 
	 * @return variable ref 
	 * @see D$this->getVariableRef()
	 */
	public function &getVariableRef($sVarName){}
	
	/**
	 * 删除变量 
	 *
	 * @access public 
	 * @param $sVarName string 变量名称 
	 * @return bool 
	 * @see D$this->deleteVariable()
	 */
	public function deleteVariable($sVarName){}

	/**
	 * 判断变量是否设置 
	 *
	 * @access public 
	 * @param $sVarName string 变量名称 
	 * @return bool
	 * @see D$this->issetVariable()
	 */
	public function issetVariable($sVarName){}

	/**
	 * 清空变量 
	 *
	 * @access public 
	 * @return void 
	 * @see D$this->clearVariable()
	 */
	public function clearVariable(){}

	/**
	 * 销毁Session
	 *
	 * @access public
	 * @return void
	 */
	public function destroy(){}

	/**
	 * 暂停Session
	 *
	 * @access public
	 * @return void
	 */
	public function pause(){}
	
	/**
	 * 终止会话
	 * 
	 * @access public
	 * @return bool
	 */
	public function terminateSession(){}

	/**
	 * 清空Local
	 *   
	 * @access public
	 * @return void
	 */
	public function clearLocal(){}

	/**
	 * 检测SessionID
	 *  
	 * @access public
	 * @return void
	 */
	public function detectId(){}

	/**
	 * 设置或者获取当前Session name
	 *
	 * @access public
	 * @param string $sName session名称
	 * @return string 返回之前的Session name
	 */
	public function name($sName=null){}

	/**
	 * 设置或者获取当前SessionID
	 *
	 * @param string $sid sessionID 
	 * @access public
	 * @return void 返回之前的sessionID
	 */
	public function id($sid=null){}

	/**
	 * 设置或者获取当前Session保存路径
	 *
	 * @param string $sPath 保存路径名
	 * @access public
	 * @return string
	 */
	public function path($sPath=null){}

	/**
	 * 设置Session 过期时间
	 *
	 * @param integer $nTime 过期时间
	 * @param boolean $bAdd 是否为增加时间	 
	 * @access public
	 * @return void
	 */
	public function setExpire( $nTime,$bAdd=false){}

	/**
	 * 设置Session 闲置时间
	 *
	 * @param integer $nTime 闲置时间
	 * @param boolean $bAdd 是否为增加时间	
	 * @access public
	 * @return void
	 */
	public function setIdle($nTime,$bAdd=false){}

	/**
	 * 取得Session 有效时间
	 *  
	 * @access public
	 * @return void
	 */
	public function sessionValidThru(){}

	/**
	 * 检查Session 是否过期
	 *	 
	 * @access public
	 * @return boolean
	 */
	public function isExpired(){}

	/**
	 * 检查Session 是否闲置
	 * 
	 * @access public
	 * @return void
	 */
	public function isIdle(){}


	/**
	 * 更新Session 闲置时间
	 *
	 * @access public
	 * @return void
	 */
	public function updateIdle(){}

	/**
	 * 设置Session 对象反序列化时候的回调函数
	 *
	 * < 返回之前设置 >
	 *
	 * @param string $Callback 回调函数方法名  
	 * @access public
	 * @return boolean
	 */
	public function setCallback( $Callback=null){}

	/**
	 * 检查Session 是否新建
	 *
	 * @access public
	 * @return boolean 
	 */
	public function isNew(){}

	/**
	 * 取得当前项目的Session 值
	 *
	 * < 返回之前设置 >
	 *
	 * @param string $sName
	 
	 * @access public
	 * @return boolean
	 */
	public function getLocal($sName){}

	/**
	 * 设置当前项目的Session 值
	 *
	 * < 返回之前设置 >
	 *
	 * @param string $sName
	 * @param mixed $Value
	 
	 * @access public
	 * @return boolean
	 */
	public function setLocal($sName,$Value){}

	/**
	 * 检查Session 值是否已经设置 
	 *
	 * @param string $sName
	 
	 * @access public
	 * @return boolean
	 */
	public  function isSetLocal($sName){}

	/**
	 * 设置或者获取 Session localname
	 *
	 * @param string $sName 设置的session名
	 * @access public
	 * @return string
	 */
	public function localName($sName=null){}

	/**
	 * 当前Session文件名
	 *
	 * @access public
	 * @return string
	 */
	public function getFilename(){}

	/**
	 * 设置Session cookie_domain
	 *
	 * < 返回之前设置 >
	 *
	 * @param string $sSessionDomain
	 * @access public
	 * @return string
	 */
	public function setCookieDomain($sSessionDomain=null){}

	/**
	 * 设置Session 是否使用cookie
	 *
	 * < 返回之前设置 >
	 *
	 * @param boolean $bUseCookies 是否使用cookie
	 * @access public
	 * @return boolean
	 */
	public function useCookies($bUseCookies=null){}

	/**
	 * 设置Session use_trans_sid
	 *
	 * < 返回之前设置 >
	 *
	 * @param int $nUseTransSid   
	 * @access public
	 * @return int
	 */
	public function useTransSid($nUseTransSid=null){}

	/**
	 * 设置Session gc_maxlifetime值
	 *
	 * < 返回之前设置 >
	 *
	 * @param int $nGcMaxlifetime  
	 * @access public
	 * @return int
	 */
	public function setGcMaxLifetime($nGcMaxlifetime=null){}

	/**
	 * 设置Session gc_probability 值
	 *
	 * < 返回之前设置 
	 *   session.gc_probability ＝ 1 
	 *   默认情况下，session.gc_probability ＝ 1，session.gc_divisor ＝100，也就是说有1%的可能性会启动GC。
	 *   GC的工作，就是扫描所有的session信息，用当前时间减去session的最后修改时间（modified date），同session.gc_maxlifetime
	 *   参数进行比较，如果生存时间已经超过gc_maxlifetime，就把该session删除。>
	 *
	 * @param int $nGcProbability
	 * @access public
	 * @return int
	 */
	public function setGcProbability($nGcProbability=null){}

}
