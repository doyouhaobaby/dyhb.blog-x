<?dyhb
class Safe{

	/**
	 * 深度字符串替换操作
	 *
	 * < 深度替换操作，比一般的str_replace 过滤严谨。比如 $sSubject='%0%0%0DDD',$Search ='%0D'，
	 *   替换后的结果为'',而不是 '%0%0DD' >
	 *
	 * @access public
	 * @static
	 * @param string|array $Search 带查找的条件，字符串或者数组
	 * @param string $sSubject 带过滤的字符串
	 * @return string
	 */
	public static function deepReplace($Search,$sSubject){}

	/**
	 * 检查和过滤URL
	 *
	 * @access public
	 * @static
	 * @param string $sUrl 待过滤的 URL 地址
	 * @param array $arrProtocols 允许的网络协议
	 * @param string $sContext
	 * @return string
	 */
	public static function escUrl($sUrl,$arrProtocols=null,$sContext='display'){}

	/**
	 * Post,Get对象过滤通用函数(Long大文本检测)
	 *
	 * @access public
	 * @static
	 * @param string $sPost 待过滤的输入数据
	 * @return string
	 */
	public static function longCheck($sPost){ }

	/**
	 * Post,Get对象过滤通用函数(Big大文本检测)
	 *
	 * @access public
	 * @static
	 * @param string $sPost 待过滤的输入数据
	 * @return string
	 */
	public static function bigCheck($sPost){}

	/**
	 * Post,Get对象过滤通用函数(Short短文本检测)
	 *
	 * @access public
	 * @static
	 * @param string $sStr 待过滤的输入数据
	 * @return string
	 */
	public static function shortCheck($sStr){}

	/**
	 * 过滤Script代码
	 *
	 * @access public
	 * @static
	 * @param string $sStr 待过滤的输入数据
	 * @return string
	 */
	public static function filterScript($sStr){}

	/**
	 * 过滤16进制
	 *
	 * @access public
	 * @static
	 * @param string $sInput 待过滤的输入数据
	 * @return string
	 */
	public static function cleanHex($sInput){}

	/**
	 * 限制输入字符长度，防止缓冲区溢出攻击
	 *
	 * @access public
	 * @static
	 * @param string $sStr 字符串
	 * @param int $nMaxSlen 字符串最大长度
	 * @return string
	 */
	public static function lenLimit($sStr,$nMaxSlen){}

	/**
	 * 过滤敏感词语
	 *
	 * @access public
	 * @static
	 * @param string $sContent 待操作的字符串
	 * @param string $sReplace 替换的字符串
	 * @return string
	 */
	public static function filtWord($sContent,$sReplace='*'){}

	/**
	 * 过滤数字
	 *
	 * @access public
	 * @static
	 * @param  string|array $IdStr 待操作的字符串或者数组
	 * @return string(1,2,3,4)
	 */
	public static function filtNumArray($IdStr){}

	/**
	 * 过滤字符
	 *
	 * @access public
	 * @static
	 * @param string|array $StrOrArray 待操作的字符串或者数组
	 * @return string('h','e','l','l','o')
	 */
	public static function filtStrArray($StrOrArray){}

	/**
	 * sql语句过滤
	 *
	 * @access public
	 * @static
	 * @param string $sStr 待过滤的数据
	 * @return string
	 */
	public static function sqlFilter($sStr){}

	/**
	 * 字段过滤
	 *
	 * @access public
	 * @static
	 * @param string|array $Fields 待过滤的字段
	 * @return string
	 */
	public static function filtFields($Fields){}

	/**
	 * 取出允许获取的数据
	 *
	 * @access public
	 * @static
	 * @param string|array $Check $sStr 待过滤的数据
	 * @param array $arrMatchArray 允许输出的数据
	 * @return string
	 */
	public static function checkItem($Check,$arrMatchArray){}

	/**
	 * 字符串过滤
	 *
	 * @access public
	 * @static
	 * @param string|array $StrOrArray 待过滤的数据
	 * @param int $nMaxNum 最大长度
	 * @return string
	 */
	public function strFilter($StrOrArray,$nMaxNum=20000){}

	/**
	 * Html字符串过滤
	 *
	 * @access public
	 * @static
	 * @param string|array $StrOrArray 待过滤的数据
	 * @param int $nMaxNum 最大长度
	 * @return string
	 */
	public function htmlFilter($StrOrArray,$nMaxNum=20000){ }

	/**
	 * 限制访问时间段
	 *
	 * <!-- 使用方法 -->
	 * < E('next Monday-last Sunday')
	 *   E(array('next Monday-last Sunday',//more))
	 *   具体方法来自strtotime函数的使用，见下面 。>
	 *
	 * @access public
	 * @static
	 * @param mixed $LimitTime 时间段
	 * @return void
	 */
	public static function limitTime($LimitTime){}

	/**
	 * 限制ip访问站点
	 *
	 * @access public
	 * @static
	 * @param string|array $LimitIpList 限制IP
	 * @return void
	 */
	public static function limitIp($LimitIpList){}

	/**
	 * 防刷新防代理
	 *
	 * @access public
	 * @static
	 * @param int $nAttackEvasive 限制类型
	 * @return void
	 */
	public function checkAttackEvasive($nAttackEvasive=1){}
