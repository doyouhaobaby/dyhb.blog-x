<?dyhb
class Controller{

	/**
	 * 构造函数
	 *
	 * @access public
	 * @return void
	 */
	public function __construct(){}

	/**
	 * 模板变量赋值
	 *
	 * @access protected
	 * @param mixed $Name 要显示的模板变量
	 * @param mixed $Value 变量的值
	 * @return void
	 */
	 public function assign($Name,$Value=''){}

	/**
	 * 模板变量赋值 < __set模式方法 >
	 *
	 * @access protected
	 * @param mixed $Name 要显示的模板变量
	 * @param mixed $Value 变量的值
	 * @return void
	 */
	 public function __set($Name,$Value){}

	/**
	 * 取得模板显示变量的值
	 *
	 * @access protected
	 * @param string $sName 模板显示变量
	 * @return mixed
	 */
	public function get($sName){}

	/**
	 * 取得模板显示变量的值 < __get模式方法 >
	 *
	 * @access protected
	 * @param string $name 模板显示变量
	 * @return mixed
	 */
	public function &__get($sName){}

	/**
	 * 模板显示
	 *
	 * @access public
	 * @param string $sTemplateFile 指定要调用的模板文件 < 默认为空由系统自动定位模板文件 >
	 * @param string $sCharset 输出编码
	 * @param string $sContentType 输出类型
	 * @return void
	 */
	public function display($sTemplateFile='',$sCharset='',$sContentType='text/html'){}

	/**
	 * Trace变量赋值
	 *
	 * @access protected
	 * @param mixed $Name 要显示的模板变量
	 * @param mixed $Value 变量的值
	 * @return void
	 */
	protected function T($Name,$Value='',$sViewName=null){}

	/**
	 * 取得模板显示变量的值
	 *
	 * @access protected
	 * @param string $sName 模板显示变量
	 * @param string $sViewName 视图名字
	 * @return mixed
	 */
	protected function G($sName,$sViewName=null ){}

	/**
	 * 是否AJAX请求
	 *
	 * @access protected
	 * @return bool
	 */
	protected function isAjax(){}

	/**
	 * 操作成功跳转的快捷方法
	 *
	 * @access protected
	 * @param string $sMessage 提示信息
	 * @param Boolean $bAjax 是否为Ajax方式
	 * @return void
	 */
	protected function S($sMessage,$bAjax=FALSE ){}

	/**
	 * Ajax方式返回数据到客户端
	 *
	 * @access protected
	 * @param mixed $Data 要返回的数据
	 * @param string $sInfo 提示信息
	 * @param int $nStatus 返回状态
	 * @param String $sType ajax返回类型 JSON XML
	 * @return void
	 */
	protected function A($Data,$sInfo='',$nStatus=1,$sType=''){}

	/**
	 * Action跳转(URL重定向） 支持指定模块和延时跳转
	 *
	 * @access protected
	 * @param string $sUrl 跳转的URL表达式
	 * @param array $arrParams 其它URL参数
	 * @param integer $nDelay 延时跳转的时间 单位为秒
	 * @param string $sMsg 跳转提示信息
	 * @return void
	 */
	protected function U($sUrl,$arrParams=array(),$nDelay=0,$sMsg='') {}

	/**
	 * 默认跳转操作 支持错误导向和正确跳转
	 *
	 * < 调用模板显示 默认为public分隔符success.html页面
	 *   提示页面为可配置 支持模板标签 >
	 *
	 * @access private
	 * @param string $sMessage 提示信息
	 * @param int $nStatus 状态
	 * @param bool $bAjax 是否为Ajax方式
	 * @return void
	 */
	private function J($sMessage, $nStatus=1, $bAjax=FALSE){}

	/**
	 * 返回供模板跳转用的JavaScript代码
	 *
	 * @access private
	 * @param  array $arrInit 调转配置信息
	 * @return string->html
	 */
	private function javascriptR($arrInit){}

}
