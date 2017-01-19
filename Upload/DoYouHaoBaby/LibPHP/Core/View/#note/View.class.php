<?dyhb
abstract class View{

	/**
	 * Template控件
	 *
	 * @access private
	 * @var array
	 */
	private $_oTemplate;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $oPar 控制器
	 * @return void
	 */
	public function __construct($oPar=null){}

	/**
	 * 自动定位模板文件
	 *
	 * @access private
	 * @param string $sTemplateFile 文件名
	 * @return string
	 */
	public function parseTemplateFile($sTemplateFile){}

	/**
	 * 获取Template控件
	 *
	 * @access public
	 * @return Template
	 */
	public function getTemplate(){}

	/**
	 * 设置Template控件
	 *
	 * @access public
	 * @param $oTemplate Template Template对象
	 * @return oldValue
	 */
	public function setTemplate(Template $oTemplate){}

}
