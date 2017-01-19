<?dyhb
/**
 * PHP 5 基础类描述
 +--------------------------
 class Exception{
	protected $message='Unknown exception';// 异常信息
	protected $code=0;// 用户自定义异常代码
	protected $file;// 发生异常的文件名
	protected $line;// 发生异常的代码行号
	function __construct($message=null,$code=0);
	final function getMessage();// 返回异常信息
	final function getCode();// 返回异常代码
	final function getFile();// 返回发生异常的文件名
	final function getLine();// 返回发生异常的代码行号
	final function getTrace();// backtrace()数组
	final function getTraceAsString();// 已格成化成字符串的getTrace()信息
	// 可重载的方法
	function __toString();// 可输出的字符串
 }
 +--------------------------
 *
 */
class DException extends Exception{

	/**
	 * 是否存在多余信息
	 *
	 * @access private
	 * @var bool
	 * @static
	 */
	private $_bExtra;

	/**
	 * 错误类型
	 *
	 * @access private
	 * @var string
	 * @static
	 */
	private $_sType;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param string $sMessage 错误消息
	 * @param int $nCode 错误代码
	 * @param bool $bExtra 多余调试信息
	 * @return void
	 */
	public function __construct($sMessage,$nCode=0,$bExtra){}

	/**
	 * 缺省异常处理函数
	 *
	 * < 系统中未被俘获的异常统一由此函数处理 >
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function exceptionPro(Exception &$oException){}

	/**
	 * 自定义输出格式
	 *
	 * @access public
	 * @param void
	 * @return string
	 */
	public function __toString(){}

	/**
	 * 默认处理异常异常函数
	 *
	 * @access public
	 * @return array
	 */
	public function defaultHandel(){}

	/**
	 * 异常信息处理
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	static public function generalHandel(Exception &$oException){}

	/**
	 * 格式化异常信息
	 *
	 * @access public
	 * @return string
	 */
	public function formatException(){}

}
