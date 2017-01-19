<?dyhb
class TemplateObj{

	/**
	 * 所在模版
	 *
	 * @access private
	 * @var Template
	 */
	private $_oTemplate;

	/**
	 * 模板文件程序分析器
	 *
	 * @access private
	 * @var ITemplateObjParser
	 */
	private $_oParser;

	/**
	 * 模板分析器
	 *
	 * @access private
	 * @var ITemplateObjCompiler
	 */
	private $_oCompiler;

	/**
	 * 原文
	 *
	 * @access private
	 * @var string
	 */
	private $_sSourceStream;

	/**
	 * 分析后的数据
	 *
	 * @access private
	 * @var string
	 */
	private $_sCompiledStream;

	/**
	 * 下级Template对象
	 *
	 * @access private
	 * @var string
	 */
	protected $_arrChildTemplateObj=array();

	/**
	 * 所在模版文件
	 *
	 * @access private
	 * @var string
	 */
	private $_sTemplateFile='';

	/**
	 * 开始行
	 *
	 * @access private
	 * @var int
	 */
	private $_nStartLine=0;

	/**
	 * 结束行
	 *
	 * @access private
	 * @var int
	 */
	private $_nEndLine=0;

	/**
	 * 开始字节数
	 *
	 * @access private
	 * @var int
	 */
	private $_nStartByte=0;

	/**
	 * 结束字节数
	 *
	 * @access private
	 * @var int
	 */
	private $_nEndByte=0;

	/**
	 * 在开始行中的字节位置
	 *
	 * @access private
	 * @var int
	 */
	private $_nStartByteInLine=0;

	/**
	 * 在结束行中的字节位置
	 *
	 * @access private
	 * @var int
	 */
	private $_nEndByteInLine=0;

	/**
	 * 获取的模板值
	 *
	 * @access private
	 * @var array
	 */
	protected $_arrGet=array();

	/**
	 * 设置的值
	 *
	 * @access private
	 * @var array
	 */
	protected $_arrSet=array();

	/**
	 * 最小位置值
	 *
	 * @access private
	 * @var int
	 */
	const LOCAL_MIN=1;

	/**
	 * 位置内
	 *
	 * @access const
	 * @var int
	 */
	const LOCAL_IN=1;

	/**
	 * 位置外
	 *
	 * @access const
	 * @var int
	 */
	const LOCAL_OUT=2;

	/**
	 * 位置前
	 *
	 * @access const
	 * @var int
	 */
	const LOCAL_FRONT=3;

	/**
	 * 位置后
	 *
	 * @access const
	 * @var int
	 */
	const LOCAL_BEHIND=4;

	/**
	 * 位置最大值
	 *
	 * @access const
	 * @var int
	 */
	const LOCAL_MAX=1;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sSourceStream string 在模版中的原文
	 * @return void
	 */
	public function __construct($sSourceStream){}

	/**
	 * 设置所在模版
	 *
	 * @access public
	 * @param $oTempate Template 模板对象
	 * @return oldVar
	 */
	public function setTemplate(Template $oTempate){}

	/**
	 * 取得所在模版
	 *
	 * @access public
	 * @return Template
	 */
	public function getTemplate(){}

	/**
	 * 设置 分析器
	 *
	 * @access public
	 * @param $oParser TemplateObjParser 分析器
	 * @return oldVar
	 */
	public function setParser( ITemplateObjParser $oParser){}

	/**
	 * 取回 分析器
	 *
	 * @access public
	 * @return ITemplateObjParser
	 */
	public function getParser(){}

	/**
	 * 设置 编译此模版的编译器，可以是null
	 *
	 * @access public
	 * @param $Compiler ITemplateObjCompiler|null 编译器
	 * @return oldVar
	 */
	public function setCompiler($Compiler){}

	/**
	 * 取得 编译器
	 *
	 * @access public
	 * @return ITemplateObjCompiler|null
	 */
	public function getCompiler(){}

	/**
	 * 取得原文
	 *
	 * @access public
	 * @return string
	 */
	public function getSource( ){}

	/**
	 * 设置原文
	 *
	 * @access public
	 * @return oldValue
	 */
	protected function setSource($sSource){}

	/**
	 * 取得编译后的内容
	 *
	 * @access public
	 * @return string
	 */
	public function getCompiled(){}

	/**
	 * 设置 编译后的内容
	 *
	 * @access public
	 * @param $sStream string 原文内容
	 * @return oldValue
	 */
	public function setCompiled($sStream){}

	/**
	 * call特殊魔术方法
	 *
	 * @access public
	 * @return mixed
	 */
	public function __call($sMethod,$arrArgs){}
	/**
	 * 比较Template对象的位置
	 *
	 * @access public
	 * @param $oTemplateObj TemplateObject 用于比较的另外一个对象
	 * @return self::LOCAL_IN|self::LOCAL_OUT|self::LOCAL_FRONT|self::LOCAL_BEHIND
	 */
	public function compareLocal(TemplateObj $oTemplateObj){}

	/**
	 * 插入一个 TemplateObj
	 *
	 * @access public
	 * @param $oInTemplateObj TemplateObj TemplateObj对象
	 * @return void
	 */
	public function addTemplateObj(TemplateObj $oTemplateObj){}

	/**
	 * 删除一个Template对象
	 *
	 * @access public
	 * @param $nIdx int 对象索引
	 * @return TemplateObj|null
	 */
	public function remvoeTemplateObj($nIdx){}

	/**
	 * 获取一个Template对象
	 *
	 * @access public
	 * @param $nIdx int Templage对象的索引
	 * @return TemplateObj|null
	 */
	public function getTemplateObj($nIdx){}

	/**
	 * 定位 < 定位模板 >
	 *
	 * @access public
	 * @param $sTemplateStream string 模版原文
	 * @param $nStart int 开始位置
	 * @return void
	 */
	public function locate($sTemplateStream,$nStart){}

	/**
	 * 编译
	 *
	 * @access public
	 * @return void
	 */
	public function compile(){}

	/**
	 * 取得对象位置的描述
	 *
	 * @access public
	 * @return string
	 */
	public function getLocationDescription(){}

}
