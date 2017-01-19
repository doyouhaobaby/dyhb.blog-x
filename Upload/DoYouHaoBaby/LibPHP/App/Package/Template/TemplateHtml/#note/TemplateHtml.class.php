<?dyhb
class TemplateHtml extends Template{

	/**
	 * 模板目录
	 *
	 * @access private
	 * @static
	 * @var string
	 */
	static private $_sTemplateDir;

	/**
	 * 当前模板变量
	 *
	 * @access private
	 * @var
	 */
	private $_arrVariable;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $oParserManager ITemplateObjProcessorManager=null 处理器管理器
	 * @return void
	 */
	public function __construct(ITemplateObjProcessorManager $oParserManager=null){}

	/**
	 * 设置一个 分析器目录
	 *
	 * @access public
	 * @param $sDir string 分析器目录
	 * @static
	 * @return void
	 */
	static public function addParserDir($sDir){}

	/**
	 * 清空已设置的分析器目录，并返回原有的数量
	 *
	 * @access public
	 * @static
	 * @return int
	 */
	static public function clearParserDir(){}

	/**
	 * 创建一个默认的 分析器管理器
	 *
	 * @access public
	 * @static
	 * @return TemplateObjProcessorManager
	 */
	static public function getDefaultParserManager(){}

	/**
	 * 设置一个模版文件存放目录
	 *
	 * @access public
	 * @param $sDir string 模版文件存放目录
	 * @static
	 * @return void
	 */
	static public function setTemplateDir($sDir){}

	/**
	 * 根据模版 文件名查找 文件
	 *
	 * @access public
	 * @param $arrTemplateFile array 模版文件名称
	 * @static
	 * @return string
	 */
	static public function findTemplate($arrTemplateFile){}

	/**
	 * 加入一个Template对象
	 *
	 * @access public
	 * @param $oTemplateObject TempateObj Template对象
	 * @return void
	 */
	public function putInTemplateObj(TemplateObj $oTemplateObj){}

	/**
	 * 分析模版前处理
	 *
	 * @access public
	 * @param $sCompiled string 分析原文
	 * @return void
	 */
	protected function bParseTemplate_(&$sCompiled){}

	/**
	 * 加载子模板
	 *
	 * @access public
	 * @param $TemplateFile string 模版文件的路径 或 名称
	 * @param $sCharset string 文件编码
	 * @param $sContentType string 文件字符编码
	 * @param $nVarFlags string 变量类型
	 * @return void
	 */
	public function includeChildTemplate($sTemplateFile,$sCharset='',$sContentType='',$nVarFlags=''){}

	/**
	 * 显示模版
	 *
	 * @access public
	 * @param $sTemplateFile string 模版文件的路径 或 名称
	 * @param $sCharset string 网页编码
	 * @param $sContentType string 网页类型
	 * @param $bDisplayAtOnce=true bool 是否立即显示
	 * @param $nVarFlags=self::VAR_DEFAULT int 变量规则
	 * @return string
	 */
	public function display($TemplateFile,$sCharset='utf-8',$sContentType='text/html',$bDisplayAtOnce=true,$nVarFlags=self::VAR_ALL){}

	/**
	 * 设置用户界面变量
	 *
	 * @access public
	 * @param $Name array|string 变量名称 或 包含所有变量的数组
	 * @param $Value mixed 变量值
	 * @return oldVar|null
	 */
	public function setVar($Name,$Value=null){}

	/**
	 * 获取变量值
	 *
	 * @access public
	 * @param $sName string 变量名字
	 * @return mixed
	 */
	public function getVar($sName){}

}
