<?dyhb
abstract class Template{

	/**
	 * Template对象
	 *
	 * @access private
	 * @var array
	 */
	private $TEMPLATE_OBJS=array() ;

	/**
	 * 分析器管理器程序
	 *
	 * @access private
	 * @var
	 */
	private $_oParserManager ;

	/**
	 * 模板编译路径
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sCompiledFilePath;

	/**
	 * 模板主题
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sThemeName='';

	/**
	 * 是否为子模板
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsChildTemplate=FALSE;

	/**
	 * 编译缓存文件是否保存在系统内部
	 *
	 * <!--说明-->
	 * < 系统有些编译文件，不需要暴露在外面，那么我们可以通过这个设置，保存在系统内部。
	 *   因为系统保存的一般不会改变，除非系统内核升级 >
	 *
	 * @access protected
	 * @var bool
	 */
	static protected $_bWithInTheSystem=FALSE;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $oParserManager ITemplateObjProcessorManager 分析
	 * @return void
	 */
	public function __construct( ITemplateObjProcessorManager $oParserManager=null){}

	/**
	 * 设置一个 处理器管理器
	 *
	 * @access public
	 * @param $oParserManager ITemplateObjProcessorManager 处理器管理器
	 * @return oldValue
	 */
	public function setParserManager(ITemplateObjProcessorManager $oParserManager){}

	/**
	 * 取回 处理器管理器
	 *
	 * @access public
	 * @return ITemplateObjProcessorManager
	 */
	public function getParserManager(){}

	/**
	 * 加入一个Template对象
	 *
	 * @access public
	 * @param $oTemplateObj TemplateObj Template对象
	 * @return void
	 */
	public function putInTemplateObj(TemplateObj $oTemplateObj){}

	/**
	 * 清除已有的Template对象
	 *
	 * @access public
	 * @return int
	 */
	public function clearTemplateObj(){}

	/**
	 * 编译模版, 返回编译文件的路径
	 *
	 * @access public
	 * @param $sTemplatePath string 模版文件路径
	 * @param $sCompiledPath='' string 输出路径
	 * @return string
	 */
	public function compile($sTemplatePath,$sCompiledPath=''){}

	/**
	 * 分析模版前处理
	 *
	 * @access public
	 * @param $sCompiled string 分析原文
	 * @return void
	 */
	protected function bParseTemplate_(&$sCompiled){}

	/**
	 * 编译模板对象
	 *
	 * @access protected
	 * @reuturn string
	 */
	protected function compileTemplateObj(){}

	/**
	 * 通过缺省的方式 指定 编译输出路径
	 *
	 * @access public
	 * @param $sTemplatePath string 模版文件路径
	 * @return string
	 */
	public function getCompiledPath($sTemplatePath){}

	/**
	 * 设置缓存文件是否保存在系统，即在模板文件同级新建一个Compiled 文件
	 *
	 * @access public
	 * @param $sTemplatePath string 模版文件路径
	 * @return string
	 */
	static public function in($bWithInTheSystem=false){}

	/**
	 * 返回模板编译路径
	 *
	 * @access public
	 * @return string
	 */
	public function returnCompiledPath(){}

	/**
	 * 编译文件 是否过期（模版文件有改动）
	 *
	 * @access protected
	 * @param $sTemplatePath string 模版文件路径
	 * @param $sCompiledPath string 编译文件
	 * @return bool
	 */
	protected function isCompiledFileExpired($sTemplatePath,$sCompiledPath){}

	/**
	 * 生成编译文件
	 *
	 * @access protected
	 * @param $sTemplatePath string 模版路径
	 * @param $sCompiledPath string 编译文件路径
	 * @param &$sCompiled string 各个 TemplateObj编译内容
	 * @return void
	 */
	protected function makeCompiledFile($sTemplatePath,$sCompiledPath,&$sCompiled){}

}
