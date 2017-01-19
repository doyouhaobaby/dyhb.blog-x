<?dyhb
class Rss{

	/**
	 * RSS频道名
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sChannelTitle='';

	/**
	 * RSS频道链接
	 *
	 * @access protected
	 * @var string
	 */
	 protected $_sChannelLink='';

	/**
	 * RSS频道描述
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sChannelDescription='';

	/**
	 * RSS频道所使用的语言
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sLanguage='zh_CN';

	/**
	 * RSS文档创建日期，默认为今天
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sPubDate='';
	protected $_sLastBuildDate='';

	/**
	 * RSS文档频道提供者
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sGenerator='';

	/**
	 * RSS频道使用的小图标的URL
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sChannelImgurl='';

	/**
	 * RSS单条信息的数组
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrItems=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param string $sTitle RSS频道名
	 * @param string $sLink RSS频道链接
	 * @param string $sDescription RSS频道描述
	 * @param string $sImgUrl RSS频道图标
	 * @return void
	 */
	public function __construct($sTitle, $sLink, $sDescription, $sImgUrl=''){}

	/**
	 * __get魔术方法
	 *
	 * @access public
	 * @return
	 */
	public function &__get($sKey){}

	/**
	 * __set魔术方法
	 *
	 * @access public
	 * @return
	 */
	public function __set($sKey,$sValue){}

	/**
	 * 设置受保护变量
	 *
	 * @access public
	 * @param string $sKey 变量名
	 * @param string $sValue 变量的值
	 * @return oldValue
	 */
	 public function setOption($sKey,$sValue){}

	/**
	 * 获取受保护变量
	 *
	 * @access public
	 * @param string $sKey 变量名
	 * @return oldValue
	 */
	 public function getOption($sKey,$sValue){}

	/**
	 * 添加RSS项
	 *
	 * @access public
	 * @param string $title 日志的标题
	 * @param string $link 日志的链接
	 * @param string $description 日志的摘要
	 * return void
	 */
	 public function addItem($sTitle,$sLink,$sDescription){}

	/**
	 * RSS头部
	 *
	 * @access public
	 * @return string
	 */
	public function rssHeader(){}

	/**
	 * RSS底部
	 *
	 * @access public
	 * @return string
	 */
	public function rssFooter(){}

	/**
	 * 输出RSS的XML为字符串
	 *
	 * @access public
	 * @return string
	 */
	public function fetch(){}

	/**
	 * 取得RSS 内容
	 */
	 public function getRssBody(){}

	/**
	 * 输出RSS的XML到浏览器
	 *
	 * @access public
	 * @return void
	 */
	public function display($sRssBody=''){}

}
