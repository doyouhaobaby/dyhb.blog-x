<?dyhb
class Log{

	/**
	 * 日志优先级设置，级别由低到高
	 *
	 * @access const
	 * @var String
	 * @const
	 */
	const EMERG='EMERG';// 严重错误: 导致系统崩溃无法使用
	const ALERT='ALERT';// 警戒性错误: 必须被立即修改的错误
	const CRIT='CRIT';// 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
	const ERR='ERR';// 一般错误: 一般性错误
	const WARN='WARN';// 警告性错误: 需要发出警告的错误
	const NOTICE='NOTIC';// 通知: 程序可以运行但是还不够完美的错误
	const INFO='INFO';// 信息: 程序输出信息
	const DEBUG='DEBUG';// 调试: 调试信息
	const EXCEPTION='EXCEPTION';//异常日志记录
	const SQL='SQL';// SQL：SQL语句 注意只在调试模式开启时有效

	/**
	 * 日志记录方式
	 *
	 * @access const
	 * @var int
	 * @const
	 */
	const SYSTEM=0;
	const MAIL=1;
	const TCP=2;
	const FILE=3;

	/**
	 * 日志内容
	 *
	 * @access public
	 * @var array
	 * @static
	 */
	static public $_arrLog=array();

	/**
	 * 日志内容日期格式
	 *
	 * @access public
	 * @var String
	 * @static
	 */
	static public $_sFormat='[ c ]';

	/**
	 * 日志文件名日期格式
	 *
	 * @access public
	 * @var String
	 * @static
	 */
	static public $_sFileNameFormat='Y-m-d';

	/**
	 * 日志文件大小限制
	 *
	 * @access public
	 * @var int
	 * @static
	 */
	static public $_sLogFileSize=2097152;

	/**
	 * 日志数量
	 *
	 * @access public
	 * @var int
	 * @static
	 */
	static public $_nLogCount=0;

	/**
	 * 日志路径
	 *
	 * @access public
	 * @var string
	 * @static
	 */
	static public $_sFilePath;

	/**
	 * 记录日志 并且会过滤未经设置的级别
	 *
	 * @static
	 * @access public
	 * @param string $sMessage 日志信息
	 * @param string $sLevel 日志级别
	 * @param boolean $bRecord 是否强制记录
	 * @return void
	 */
	static public function R($sMessage,$sLevel=self::ERR,$bRecord=false){}

	/**
	 * 日志保存
	 * @static
	 * @access public
	 * @param integer $sTyp 日志记录方式
	 * @param string $sFilePath 写入日志路径
	 * @param string $sExtra 额外参数
	 * @return void
	 */
	static public function S($sType=self::FILE,$sFilePath='',$sExtra=''){}

	/**
	 * 日志直接写入
	 *
	 * @static
	 * @access public
	 * @param string $sMessage 日志信息
	 * @param string $sLevel 日志级别
	 * @param integer $sType 日志记录方式
	 * @param string $sFilePath 写入目标
	 * @param string $sExtra 额外参数
	 * @return void
	 */
	static public function W($sMessage,$sLevel=self::ERR,$sType=self::FILE,$sFilePath='',$sExtra=''){}

	/**
	 * 清理日志
	 *
	 * @access public
	 * @static
	 */
	static public function clearItem(){}

	/**
	 * 删除日志文件
	 *
	 * @access public
	 * @static
	 */
	static public function deleteLogFile(){}

	/**
	 * 备份日志，生成新日志
	 *
	 * @access public
	 * @param $sFilePath string 日志路径
	 * @static
	 */
	static public function newLogFile($sFilePath){}

	/**
	 * 获取日志路径
	 *
	 * @access public
	 * @param $sFilePath string 日志路径
	 * @return string
	 */
	static public function getLogFilePath($sFilePath=''){}

	/**
	 * 获取日志记录格式化时间
	 *
	 * @access public
	 * @return void
	 */
	static public function getFormatTime(){}

	/**
	 * 获取日志记录数量
	 *
	 * @access public
	 * @return void
	 */
	static public function getLogCount(){}

}
