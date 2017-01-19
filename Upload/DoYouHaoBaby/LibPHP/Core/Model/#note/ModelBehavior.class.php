<?dyhb
abstract class ModelBehavior implements IModelCallback{

	/**
	 * Model 继承类的元信息对象
	 *
	 * @access protected
	 * @var ModelMeta
	 */
	protected $_oMeta;

	/**
	 * 插件的设置信息
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrSettings=array();

	/**
	 * 插件添加的动态方法
	 *
	 * @access private
	 * @var array
	 */
	private $_arrDynamicMethods=array();

	/**
	 * 插件添加的静态方法
	 *
	 * @access private
	 * @var array
	 */
	private $_arrStaticMethods=array();

	/**
	 * 插件添加的事件处理函数
	 *
	 * @access private
	 * @var array
	 */
	private $_arrEventHandlers=array();

	/**
	 * 插件添加的 getter 方法
	 *
	 * @access private
	 * @var array
	 */
	private $_arrGetters=array();

	/**
	 * 插件添加的 setter 方法
	 *
	 * @access private
	 * @var array
	 */
	private $_arrSetters=array();

	/**
	 * 行为扩展是否出错
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsError=false;

	/**
	 * 行为扩展最后错误消息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sLastErrorMessage;

	/**
	 * 构造函数
	 *
	 * @access protected
	 * @param ModelMeta $oMeta 元对象
	 * @param array $arrSettings 插件setting方法
	 */
	public function __construct(ModelMeta $oMeta,array $arrSettings){}

	/**
	 * 格式化配置
	 *
	 * @access public
	 * @static
	 * @param $arrConfig array 配置
	 * @return array
	 */
	static public function normalizeConfig($arrConfig){}

	/**
	 * 绑定行为插件
	 *
	 * @access public
	 * @return void
	 */
	public function bind(){}

	/**
	 * 撤销行为插件绑定
	 *
	 * @access public
	 * @return void
	 */
	public function unbind(){}

	/**
	 * 为Model对象添加一个动态方法
	 *
	 * @access protected
	 * @param string $sMethodName 方法名
	 * @param callback $Callback 回调
	 * @param array $arrCustomParameters 客户的参数
	 * @return void
	 */
	protected function addDynamicMethod_($sMethodName,$Callback,$arrCustomParameters=array()){}

	/**
	 * 为 Model 类添加一个静态方法
	 *
	 * @access protected
	 * @param string $sMethodName 方法
	 * @param callback $Callback 回调
	 * @param array $arrCustomParameters 客户的参数
	 * @return void
	 */
	protected function addStaticMethod_($sMethodName,$Callback,$arrCustomParameters=array()){}

	/**
	 * 为Model对象添加一个事件处理函数
	 *
	 * @access protected
	 * @param int $nEventType 事件类型
	 * @param callback $Callback 回调方法
	 * @param array $arrCustomParameters 自定义参数
	 */
	protected function addEventHandler_($nEventType,$Callback,$arrCustomParameters=array()){}

	/**
	 * 设置一个属性的getter方法
	 *
	 * @access protected
	 * @param string $sPropName 属性名字
	 * @param callback $Callback 回调
	 * @param array $arrCustomParameters 自定义参数
	 */
	protected function setPropGetter_($sPropName,$Callback,$arrCustomParameters=array()){}

	/**
	 * 设置一个属性的 setter 方法
	 *
	 * @access protected
	 * @param string $sPropName 属性名
	 * @param callback $Callback 回调方法
	 * @param array $arrCustomParameters 自定义参数
	 */
	protected function setPropSetter_($sPropName,$Callback,$arrCustomParameters=array()){}

	public function setIsError($bIsError=false){}

	public function setErrorMessage($sErrorMessage=''){}

	public function isError(){}

	public function getErrorMessage(){}

}
