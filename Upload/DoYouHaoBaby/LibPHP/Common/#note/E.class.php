<?dyhb
class E{

	/**
	 * 转换附件大小单位
	 *
	 * @access public
	 * @static
	 * @access public
	 * @param string $nFileSize 文件大小 kb
	 * @return string 文件大小
	 */
	static public function changeFileSize($nFileSize){}

	/**
	 * 获取页面当前时间
	 *
	 * @access public
	 * @static
	 * @return int
	 */
	static public function getMicrotime(){}

	/**
	 * 判断一个数组是否为一维数组
	 *
	 * @access public
	 * @param array $arrArray 待分析的数组
	 * @static
	 * @return bool
	 */
	static public function oneImensionArray($arrArray){}

	/**
	 * 构造数组下标
	 *
	 * @access public
	 * @param array $arrVars 数组
	 * @param int $nType 构造方式
	 * @param int $nGo 开始
	 * @return void
	 */
	public static function arrayHandler($arrVars,$nType=1,$nGo=2){}

	/**
	 * 获得用户的真实IP地址
	 *
	 * @access  public
	 * @return  string
	 */
	static public function getIp(){ }

	/**
	 * DISCUZ 加密函数
	 * 
	 *  < @example
	 *    $a = authcode('abc', 'ENCODE', 'key');
	 *    $b = authcode($a, 'DECODE', 'key');  // $b(abc)
	 *    $a = authcode('abc', 'ENCODE', 'key', 3600);
	 *    $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空 >
	 *
	 * @access public
	 * @param string $string 原文或者密文
	 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
	 * @param string $key 密钥
	 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
	 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
	 */
	static public function authcode($string,$operation=TRUE,$key=null,$expiry=3600){}

	/**
	 * 验证码转化程序
	 *
	 * @access public
	 * @static
	 * @param int $nSeccode 6位验证码
	 * @param boolean $bChinesecode=false 是否使用中文验证码
	 * @param int $nSeccodeTupe=1 设置创建验证码文字的方式 1：基于字体来创建 2：基于图片来创建文字
	 * @return void
	 */
	public static function seccodeConvert(&$nSeccode,$bChinesecode=false,$nSeccodeTupe=1){}

	/**
	 *  将含有单位的数字转成字节
	 *
	 * @access public
	 * @param string $sVal 带单位的数字
	 * @return int $sVal
	 */
	static public function returnBytes($sVal){}

	/**
	 * 根据目录自动加载所有php文件
	 *
	 * @access public
	 * @static
	 * @param string $path 文件夹路径，不带'/'
	 * @param bool $bInclude=FALSE 是否自动加载
	 * @return array
	 */
	static function includeDirPhpfile($sPath,$bInclude=FALSE){}

	/**
	 * 遍历指定目录中全部一级目录
	 *
	 * @access public
	 * @static
	 * @param string $sDir 文件夹路径
	 * @return array $arrFiles 文件目录的数组
	 * @return array
	 */
	static public function listDir($sDir,$bFullPath=FALSE){}

	/**
	 * 判断类是否存在静态函数
	 *
	 * @access public
	 * @static
	 * @param $sClassName string 类名字
	 * @param $sMethodName string 方法名
	 * @return bool
	 */
	 public static function hasStaticMethod($sClassName,$sMethodName){}

	/**
	 * 序列化失败解决方法 （UTF-8） http://be-evil.org/post-140.html
	 *
	 * @access public
	 * @static
	 * @param string $sSerial 序列化的字符串
	 * @return void
	 */
	static public function mbUnserialize($sSerial){}

	static public function getAvatar($nUid,$sSize='middle'){}

	/**
	 * 取得扩展名
	 *
	 * @access public
	 * @param $sFileName string 文件名
	 * @param $nCase 返回类型 1：大写 2：小写 3：直接返回
	 * @static
	 * @return string
	 */
	static public function getExtName($sFileName,$nCase=0){}

}
