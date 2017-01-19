<?dyhb
/**
 *  分页类 < 本类可以被继承 >
 *
 *　<!-- 分页方法 -->
 *  < $oPage = Page::RUN( 1000,5,$_GET['page'],'page={page}' );
 *    $oPage = Page::RUN( 1000,5,$_GET['page'],'list-{page}.html' );
 *    $oPage -> P(); >
 */
class Page{

	/**
	 * 总记录数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nCount;

	/**
	 * 每页记录数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nSize;

	/**
	 * 当前页
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPage;

	/**
	 * 当前页开始记录
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPageStart;

	/**
	 * 跳转额外参数
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sParameter;

	/**
	 * 总页数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPageCount;

	/**
	 * 页面url
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sPageUrl;

	/**
	 * 起始页
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPageI;

	/**
	 * 结束页
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPageUb;

	/**
	 * 限制
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPageLimit;

	/**
	 * 是否显示页面跳转
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bPageSkip = false;

	/**
	 * 是否显示当前记录
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bCurrentRecord=false;

	/**
	 * 上一页，下一页等没有衔接的时候是否显示
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bDisableDisplay=false;

	/**
	 * 认证器对象实例
	 *
	 * @access protected
	 * @var self
	 */
	protected static $_oDefaultDbIns=null;

	/**
	 * 构造函数(私有)
	 *
	 * @access protected
	 * @param $nCount  int 总数
	 * @param $nSize  int 每页数
	 * @param $nPage  int 当前page页面
	 * @param $sPageUrl string url样式
	 * @return void
	 */
	protected function __construct($nCount=0,$nSize=1,$nPage=1,$sPageUrl=''){}

	/**
	 * 创建一个分页实例
	 *
	 * @access public
	 * @static
	 * @param $nCount int 总数
	 * @param $nSize int 每页数
	 * @param $nPage int 当前page页面
	 * @param $sPageUrl string url样式
	 * @param $bDefaultIns bool 是否强制新创建一个验证器
	 * @return Page
	 */
	public static function RUN($nCount=0,$nSize=1,$nPage=1,$sPageUrl='',$bDefaultIns=true){}

	/**
	 * 输出
	 *
	 * @access public
	 * @param mixed $Id 页面HTML Id值
	 * @return string
	 */
	public function P($Id='pagenav'){}

	/**
	 * 设置 上一页，下一页等没有衔接的时候是否显示
	 *
	 * @access public
	 * @param bool $bDisableDisplay =true
	 * @return oldValue
	 */
	public function setDisableDisplay($bDisableDisplay=true){}

	/**
	 * 是否显示当前记录
	 *
	 * @access public
	 * @param bool $bCurrentRecord =true
	 * @return oldValue
	 */
	public function setCurrentRecord($bCurrentRecord=true){}

	/**
	 * 是否显示页面跳转
	 *
	 * @access public
	 * @param bool $bPageSkip =true
	 * @return oldValue
	 */
	public function setPageSkip($bPageSkip=true){}

	/**
	 * 设置URL额外参数
	 *
	 * @access public
	 * @param string $sParameter 额外参数
	 * @return oldValue
	 */
	public function setParameter($sParameter=''){}

	/**
	 * 总记录数
	 *
	 * @access public
	 * @return int
	 */
	public function returnCount(){}

	/**
	 * 每页数量
	 *
	 * @access public
	 * @return int
	 */
	public function returnSize(){}

	/**
	 * 当前页数
	 *
	 * @access public
	 * @return int
	 */
	public function returnPage(){}

	/**
	 * 当前页码开始记录数
	 *
	 * @access public
	 * @return int
	 */
	public function returnPageStart(){}

	/**
	 * 总页数
	 *
	 * @access public
	 * @return int
	 */
	public function returnPageCount(){}

	/**
	 * 起始页
	 *
	 * @access public
	 * @return int
	 */
	public function returnPageI(){}

	/**
	 * 结束页
	 *
	 * @access public
	 * @return int
	 */
	public function returnPageUb(){}

	/**
	 * 限制
	 *
	 * @access public
	 * @return int
	 */
	public function returnPageLimit(){}

	/**
	 * 判断是否为数字
	 *
	 * @access private
	 * @param mixed $Id
	 * @return int
	 */
	private function numeric($Id){}

	/**
	 * 地址替换
	 *
	 * @access private
	 * @param int $nPage Page页面
	 * @return string
	 */
	private function pageReplace($nPage){}

	/**
	 * 首页
	 *
	 * @access private
	 * @return string
	 */
	private function home(){}

	/**
	 * 上一页
	 *
	 * @access protected
	 * @return string
	 */
	protected function prev(){}

	/**
	 * 下一页
	 *
	 * @access protected
	 * @return string
	 */
	protected function next(){}

	/**
	 * 尾页
	 *
	 * @access protected
	 * @return void
	 */
	protected function last(){}

}
