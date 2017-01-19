<?dyhb
class Socket{

	/**
	 * 配置
	 *
	 * @access public
	 * @var array
	 */
	protected $_arrConfig=array();
	public $_oConnection=null;
	public $_bConnected=false;
	public $_arrError=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param array $arrConfig 连接配置
	 * @return void
	 */
	public function __construct($arrConfig=array()){}

	/**
	 * 连接
	 *
	 * @access public
	 * @return void
	 */
	public function connect(){}

	/**
	 * 错误输出
	 *
	 * @access public
	 * @return void
	 */
	public function error(){}

	/**
	 * 写入数据
	 *
	 * @access public
	 * @param $sData string 数据
	 * @return bool
	 */
	public function write($sData){}

	/**
	 * 读取数据
	 *
	 * @access public
	 * @param int $nLength 数据长度
	 * @return void
	 */
	public function read($nLength=1024){}

	/**
	 * 关闭连接
	 *
	 * @access public
	 * @return bool
	 */
	public function disConnect(){}

	/**
	 * 关闭前执行
	 *
	 * @access protected
	 * @return void
	 */
 	public  function __destruct(){}

}
