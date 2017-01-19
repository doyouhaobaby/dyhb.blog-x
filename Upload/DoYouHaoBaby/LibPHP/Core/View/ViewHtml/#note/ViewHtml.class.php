<?dyhb
class ViewHtml extends View{

	/**
	 * 是否分享全局Template控件
	 *
	 * @access private
	 * @static
	 * @var object
	 */
	static private $_oShareGlobalTemplate;

	/**
	 * 模版名字
	 *
	 * @access private
	 * @var bool
	 */
	private $_sTemplate;

	/**
	 * 模板运行时间
	 *
	 * @access private
	 * @var int
	 */
	private $_nRuntime;

	/**
	 * trace调试变量容器
	 *
	 * @access private
	 * @var array
	 */
	private $_arrTrace;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sName string 视图名称
	 * @param $oParent IViewContainer 父视图 或 控制器
	 * @param $sTemplate string 模板文件名称
	 * @param $oTemplate=null ITemplateHtml Template 对象
	 * @return void
	 */
	public function __construct($sName,IViewContainer $oPar,$sTemplate=null,ITemplateHtml $oTemplate=null){}

	/**
	 * 为所有 TemplateHtml 维护一个共享的TemplateHtml 全局实例
	 *
	 * @access public
	 * @return TemplateHtml
	 */
	static public function createShareTemplate(){}

	/**
	 * 显示视图
	 *
	 * @access public
	 * @return void
	 */
	public function display($sTemplateFile='',$sCharset='',$sContentType='text/html'){}

	/**
	 * Trace变量赋值
	 *
	 * @access public
	 * @param mixed $Title trace名字
	 * @param mixed $Value trace值
	 */
	public function trace($Title,$Value=''){}

	/**
	 * 显示页面Trace信息
	 *
	 * @access public
	 * @return string
	 */
	public function templateTrace(){}

	/**
	 * 模板调试台
	 *
	 * @access public
	 * @return string
	 */
	public function templateDebug(){}

	/**
	 * 获取模板运行时间
	 *
	 * @access public
	 * @return int
	 */
	public function getMicrotime(){}

	/**
	 * 设置视图变量
	 *
	 * @access public
	 * @param $Name array|string 变量名称 或 包含所有变量的数组
	 * @param $Value mixed 变量值
	 * @return oldValue
	 */
	public function assign($Name,$Value=null){}

	/**
	 * 取得视图变量
	 *
	 * @access public
	 * @param $Name array|string 变量名称 或 包含所有变量的数组
	 * @return oldValue
	 */
	public function getVar($Name){}

	/**
	 * 模板内容替换
	 *
	 * @access protected
	 * @param string $sContent 模板内容
	 * @return string
	 */
	protected function templateContentReplace($sContent){}

}
