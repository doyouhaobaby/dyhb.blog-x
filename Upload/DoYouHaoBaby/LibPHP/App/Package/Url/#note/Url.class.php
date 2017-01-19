<?dyhb
abstract class Url{

	/**
	 * 当前路由名字(最后一次获取的路由名字)
	 *
	 * @access protected
	 * @var array
	 */
	protected $_sLastRouterName=null;

	/**
	 * 当前路由信息(最后一次获取的路由信息)
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrLastRouteInfo=array();

	/**
	 * 当前请求 URL 不包含查询参数的部分
	 *
	 * @access private
	 * @const
	 * @var string
	 */
	static private $_sBaseUrl;

	/**
	 * URL请求中的基础路径（不包含脚本路径）
	 *
	 * @access private
	 * @const
	 * @var string
	 */
	static private $_sBaseDir;

	/**
	 * 当前请求 URL
	 *
	 * @access private
	 * @const
	 * @var string
	 */
	static private $_sRequestUrl;

	/**
	 * 路由请求对象
	 *
	 * @access private
	 * @var Router
	 */
	private $_oRouter=null;

	/**
	 * 请求包含的控制器名称
	 *
	 * @access public
	 * @var string
	 */
	public $_sControllerName;

	/**
	 * 请求包含的动作名
	 *
	 * @access public
	 * @var string
	 */
	public $_sActionName;

	/**
	 * 分析URL，返回结果
	 *
	 * <!-- 测试实例  -->
	 *
	 * < 开始请求URL:
	 *   url:http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/hehe
	 *   http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=haha >
	 *
	 * @access public
	 * @return void
	 */
	public function parseUrl(){}

	/**
	 * 获取路由初始化信息
	 *
	 * @access private
	 * @return array
	 */
	private function getRouterInfo(){}

	/**
	 * 获取最后一次路由解析名字
	 *
	 * @access public
	 * @return string
	 */
	public function getLastRouterName(){}

	/**
	 * 获取最后一次路由解析信息
	 *
	 * @access public
	 * @return array
	 */
	public function getLastRouterInfo(){}

	/**
	 * 获取当前页面完整的URL地址
	 *
	 * <!-- 几个示例 -->
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   返回/doyouhaobaby/branch/TestApp/index.php >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=index
	 *   返回/doyouhaobaby/branch/TestApp/index.php?c=test&a=index >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/index
	 *   返回/doyouhaobaby/branch/TestApp/index.php/c/test/a/index >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/c/test/a/index
	 *   返回/doyouhaobaby/branch/TestApp/c/test/a/index(如果这里重写了URL，index.php用于根目录)>
	 * @return string
	 */
	public public function requestUrl(){}

	/**
	 * 返回请求 URL 中的基础路径（不包含脚本名称）
	 *
	 * 几个示例：
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   返回/doyouhaobaby/branch/TestApp/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=index
	 *   返回/doyouhaobaby/branch/TestApp/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/index
	 *   返回/doyouhaobaby/branch/TestApp/ >
	 *
	 * @return string 请求 URL 中的基础路径
	 */
	public function baseDir(){}

	/**
	 * 返回不包含任何查询参数的 URl（但包含脚本名称）
	 *
	 * <!-- 几个示例 -->
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   返回/doyouhaobaby/branch/TestApp/index.php >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=index
	 *   返回/doyouhaobaby/branch/TestApp/index.php >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/index
	 *   返回/doyouhaobaby/branch/TestApp/index.php >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/c/test/a/index
	 *   返回/doyouhaobaby/branch/TestApp/c/test/a/index(如果这里重写了URL，index.php用于根目录)>
	 *
	 * <!-- $_SERVER[PHP_SELF],[SCRIPT_NAME], ['REQUEST_URI']的区别 -->
	 *
	 * < $_SERVER[’PHP_SELF’]http://www.doyouhaobaby.com/example/ — – — /example/index.php ,
	 *   当我们使用$_SERVER['PHP_SELF']的时候，无论访问的URL地址是否有index.php，它都会自动的返回
	 *   index.php.但是如果在文件名后面再加斜线的话，就会把后面所有的内容都返回在$_SERVER['PHP_SELF']。 >
	 *
	 * < $_SERVER['REQUEST_URI'],http://www.doyouhaobaby.com/example/index.php?a=test — – — /example/index.php?a=test
	 *   $_SERVER['REQUEST_URI']返回的是我们在URL里写的精确的地址，如果URL只写到”/”，就返回 “/ >
	 *
	 * < $_SERVER['SCRIPT_NAME'] http://www.doyouhaobaby.com/example/index.php — – — /example/index.php >
	 *
	 *
	 * @return string
	 */
	public function baseUrl(){}

	/**
	 * 返回 PATHINFO 信息
	 *
	 * <!-- 几个示例 -->
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   返回/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=index
	 *   返回/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/index
	 *   返回/c/test/a/index >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/c/test/a/index
	 *   返回/c/test/a/index(如果这里重写了URL，index.php用于根目录)>
	 *
	 * <!-- $_SERVER[PHP_SELF],[SCRIPT_NAME], ['REQUEST_URI']的区别 -->
	 *
	 * < $_SERVER[’PHP_SELF’]http://www.doyouhaobaby.com/example/ — – — /example/index.php ,
	 *   当我们使用$_SERVER['PHP_SELF']的时候，无论访问的URL地址是否有index.php，它都会自动的返回
	 *   index.php.但是如果在文件名后面再加斜线的话，就会把后面所有的内容都返回在$_SERVER['PHP_SELF']。 >
	 * @access public
	 * @return string
	 */
	public function pathinfo(){}

	/**
	 * 分析pathinfo的参数
	 *
	 * @access public
	 * return array
	 */
	 public function parsePathInfo(){}

	/**
	 * 获得实际的控制器（模块）名称
	 *
	 * @param $sVar string 模块名字
	 * @access private
	 * @return string
	 */
	private function getControl($sVar){}

	/**
	 * 获得实际的操作名称
	 *
	 * @param $sVar string 操作名字
	 * @access private
	 * @return string
	 */
	private function getAction($sVar){}

	/**
	 * 获取group的名字
	 *
	 * @access public
	 * return string
	 */
	public function control(){}

	/**
	 * 获取group的名字
	 *
	 * @access public
	 * return string
	 */
	public function action(){}

	/**
	 * 获取过滤后的适合本系统的pathinfo信息
	 *
	 * <!-- 几个示例 -->
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php
	 *   返回/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php?c=test&a=index
	 *   返回/ >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/?s=test
	 *   返回test(如果当前的pathinfo兼容性变量为s)>
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/index.php/c/test/a/index
	 *   返回/c/test/a/index >
	 *
	 * < 请求 http://localhost/doyouhaobaby/branch/TestApp/c/test/a/index
	 *   返回/c/test/a/index(如果这里重写了URL，index.php用于根目录)>
	 *
	 * @access public
	 * @return string
	 */
	public function filterPathInfo(){}

	/**
	 * 过滤URL中的静态后缀
	 *
	 * @access private
	 * @param string $sVal 待过滤URL数据
	 */
	private function clearHtmlSuffix($sVal){}

}
