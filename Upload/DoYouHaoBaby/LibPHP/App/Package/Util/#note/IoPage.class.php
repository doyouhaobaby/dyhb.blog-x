<?dyhb
class IoPage extends Page{

	/**
	 * 要打开的目录
	 *
	 * @access public
	 * @var string
	 */
	public $_sIoPath='';

	/**
	 * 是否返回完整的路径
	 *
	 * @access public
	 * @var bool
	 */
	public $_bFullFilepath=false;

	/**
	 * 是否递归
	 *
	 * @access public
	 * @var bool
	 */
	public $_bRecursion=false;

	/**
	 * 记录类型
	 *
	 * @access public
	 * @const
	 * @var int
	 */
	const DIR_AND_FILE =0;
	const DIR_ONLY=1;
	const FILE_ONLY=2;
	public $_nDataRecordType=self::DIR_ONLY;

	/**
	 * 是否过滤图片
	 *
	 * @access public
	 * @var bool
	 */
	public $_bOnlyImages=false;

	/**
	 * 记录的结果
	 *
	 * @access public
	 * @var array
	 */
	public $_arrData=array();

	/**
	 * 当前分页记录的结果
	 *
	 * @access public
	 * @var array
	 */
	public $_arrReturnData=array();

	/**
	 * 记录总量
	 *
	 * @access public
	 * @var int
	 */
	public $_nTotalCount=0;

	/**
	 * 允许的类型
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAllowedType=array();

	/**
	 * 不允许的类型
	 *
	 * @access public
	 * @var array
	 */
	public $_arrDisAllowedType=array();

	/**
	 * 允许的DIR
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAllowedDir=array();

	/**
	 * 拒绝的DIR
	 *
	 * @access public
	 * @var array
	 */
	public $_arrDisAllowedDir=array();

	/**
	 * 允许的Filename
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAllowedFilename=array();

	/**
	 * 拒绝的Filename
	 *
	 * @access public
	 * @var array
	 */
	public $_arrDisAllowedFilename=array();

	/**
	 * 构造函数
	 *
	 * @access protected
	 * @param string $sIoPath 待访问的目录
	 * @return void
	 */
	protected function __construct($sIoPath='',$nSize=1,$nPage=1,$sPageUrl=''){}

	/**
	 * 创建一个分页实例
	 *
	 * @access public
	 * @static
	 * @param $sIoPath 待访问的目录
	 * @param $nSize int 每页数
	 * @param $nPage int 当前page页面
	 * @param $sPageUrl string url样式
	 * @param $bDefaultIns bool 是否强制新创建一个验证器
	 * @return Page
	 */
	public static function RUN($sIoPath='',$nSize=1,$nPage=1,$sPageUrl='',$bDefaultIns=true){}

	/**
	 * 取得目录中的数据
	 *
	 * @access public
	 * @param  string $sIoPath 待访问的目录
	 * @return array
	 */
	public function getIoData($sIoPath=''){}

	/**
	 * 取得文件扩展名
	 *
	 * @access protected
	 * @return string
	 */
	protected function getExtendName($sFileName){}

	/**
	 * 取得当前分页数据
	 *
	 * @access public
	 * @return array
	 */
	 public function getCurrentData(){}

}
