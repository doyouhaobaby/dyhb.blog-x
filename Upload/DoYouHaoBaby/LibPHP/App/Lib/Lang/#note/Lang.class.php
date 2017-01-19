<?dyhb
class Lang{

	/**
	 * 当前语言
	 *
	 * @access public
	 * @var const
	 */
	const CURRENT_LANGUAGE=null;

	/**
	 * 语言名称
	 *
	 * @access private
	 * @var string
	 */
	private $_sLangName;

	/**
	 * 当前语言包
	 *
	 * @access private
	 * @var LangPackage
	 */
	private $_oCurrentPackage=null;

	/**
	 * 已创建的语言实例（享元模式）
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	static private $LANG_INSES;

	/**
	 * 当前语言实例
	 *
	 * @access private
	 * @var Lang
	 * @static
	 */
	static private $_oCurrentLang;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param string $sLangName 语言名称
	 * @static
	 * @return void
	 */
	private function __construct($sLangName){}

	/**
	 * 根据语言名称，创建或取得已创建的语言实例（享元模式）
	 *
	 * @access public
	 * @param string $sLangName 语言名称
	 * @static
	 * @return Lang
	 */
	static public function getLang($sLangName){}

	/**
	 * 设置当前语言包
	 *
	 * @access public
	 * @param Lang|string $Lang 语言实例，或语言名称
	 * @static
	 * @return $oOldValue
	 */
	static public function setCurrentLang($Lang){}

	/**
	 * 返回当前语言包
	 *
	 * @access public
	 * @static
	 * @return Lang
	 */
	static public function getCurrentLang(){}

	/**
	 * 返回语言包名称
	 *
	 * @access public
	 * @return string
	 */
	public function getLangName(){}

	/**
	 * 创建或取得已创建的语言包
	 *
	 * @access public
	 * @param string $sPackageName 语言包名称
	 * @return LangPackage
	 */
	public function getPackage($sPackageName){}

	/**
	 * 返回当前语言包
	 *
	 * @access public
	 * @return LangPackage|null
	 */
	public function getCurrentPackage(){}

	/**
	 * 设置当前语言包
	 *
	 * @access public
	 * @param string|LangPackage $Package 语言包名称或已创建的实例
	 * @return $oOldValue
	 */
	public function setCurrentPackage($Package){}

	/**
	 * 从当前语言的当前语言包取得一条语句
	 *
	 * @access public
	 * @param string $sValue 语句
	 * @param mixed Argvs 参数
	 * @return string
	 */
	static public function set($sValue/*Argvs*/){}

	/**
	 * 根据语句内容产生Key
	 *
	 * @access public
	 * @param string $sValue 语句内容
	 * @static
	 * @return string
	 */
	static public function makeValueKey($sValue){}

	/**
	 * 从指定语言的指定语言包取得一条语句
	 *
	 * @access public
	 * @param sting $sKey 原始key
	 * @param string|LangPackage|null $Package 语言包名称、语言包实例或null(使用当前语言包)
	 * @param string|Lang|null|$Lang 语言名称、语言实例或null(使用当前语言)
	 * @param string $Value 字符串
	 * @param mixed Argvs 代入语句的参数
	 * @return string
	 */
	static public function setEx($sValue,$Package=null,$Lang=null/*Argvs*/){}

	/**
	 * 将路径整理成统一的格式
	 *
	 * < 斜线方向一致，清除路径中的 “..”、“.” 以及连续“/” ，并且目录结尾统一没有“/”（或“\”）>
	 *
	 * @access public
	 * @param string $sPath 路径
	 * @param bool $bUnix=true 返回Unix路径风格
	 * @static
	 * @return string
	 */
	static public function tidyPath($sPath,$bUnix=true){}

}
