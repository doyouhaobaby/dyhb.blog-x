<?dyhb
/**
 * 相关知识解析
 *
 * # xml_parser_create
 * < 本函数用来初始化一个新的 XML 解析器。参数 encoding 可省略，为 XML 使用的字符集，默认值为 ISO-8859-1，
 *   其它尚有 US-ASCII、UTF-8 二种。成功则返回 parser 代码供其它函数使用，失败则返回 false 值。>
 *
 * # xml_parser_set_option
 * < xml_parser_set_option() 函数为 XML 解析器进行选项设置。
 *   如果成功，则返回 true。如果失败，则返回 false。>
 *
 * # xml_set_object
 * < xml_set_object() 函数允许在对象中使用 XML 解析器。>
 *
 * #　xml_set_element_handler
 * < xml_set_element_handler() 函数建立起始和终止元素处理器。
 *　  如果处理器被成功的建立，该函数将返回 true；否则返回 false。>
 *
 * # xml_set_character_data_handler
 * < xml_set_character_data_handler() 函数建立字符数据处理器。
 *   该函数规定当解析器在 XML 文件中找到字符数据时所调用的函数。
 *   如果处理器被成功的建立，该函数将返回 true；否则返回 false。>
 *
 * # xml_parse
 * < xml_parse() 解析 XML 文档。已配置事件的处理器根据需要被无限次调用。>
 *
 * # xml_parser_free
 * < 释放xml 解析器 >
 */
class Xml{

	/**
	* XML 解析器实例
	*
	* var XML parser
	*/
	public $_oParser=NULL;
	public $_arrDocument;
	public  $_arrParent;
	public  $_arrStack;
	public  $_sLastOpenedTag;
	public $_sData;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){}

	/**
	 * 析构函数
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct(){}

	/**
	 * 将XML 数据转化为数组
	 *
	 * @access public
	 * @param string $sXml Xml 数据
	 * @return XML 数组
	 */
	static public function xmlUnserialize($sXml){}

	/**
	 * 将数组数据转化为XML 数据
	 *
	 * @access public
	 * @param string $arrData 数组数据
	 * @param string $bHtmlOn 是否为HTML数据
	 * @param string $nLevel
	 * @param string $sPriorKey
	 * @param string $sCharset  Xml 编码
	 * @return XML 数据
	 */
	static public function xmlSerialize(&$arrData,$bHtmlOn=false,$nLevel=0,$sPriorKey=NULL,$sCharset='UTF-8'){}

	/**
	 * 分析数据
	 *
	 * @access public
	 * @param  string $sData 数据
	 * @return XML 数组数据
	 */
	public function parse(&$sData){}

	/**
	 * 分析数据
	 *
	 * @access public
	 * @param object $oParser XML 分析器
	 * @param string $sTag Xml 标签
	 * @param array $arrAttributes 属性
	 * @return void
	 */
	public function open(&$oParser, $sTag, $arrAttributes){}

	/**
	 * 数据
	 *
	 * @access public
	 * @param object $oParser XML 分析器
	 * @param string $sData Xml 数据
	 * @return void
	 */
	public function data(&$oParser,$sData){}

	/**
	 * 数据
	 *
	 * @access public
	 * @param object $oParser XML 分析器
	 * @param string $sTag Xml 标签
	 * @return void
	 */
	public function close(&$oParser,$sTag){}
}

/**
 * 数据
 *
 * @access public
 * @param array $array 数据
 * @return void
 */
function countNumericItems(&$array){}
