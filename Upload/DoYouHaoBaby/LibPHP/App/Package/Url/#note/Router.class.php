<?dyhb
class Router{

/**
 * 普通路由(1：带正则匹配的 2：不带正则的 )
 *
 * < 注意：如果不带正则，1，2个参数必须，第三个可以不填 ，
 *   带上正则，那么在前面加上一个键值为'regex'的正则值  >
 *
 * < 简单路由定义：array('模块/操作名', '路由对应变量','额外参数')
 *   或者 array(array('模块','操作名'),'路由对应变量','额外参数')>
 *
 * <!-- 当前URL：-->
 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/category/121/1/2/3/4/
 *   路由到模块 Category 的blog 操作 >
 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/121/1/hello/232
 *   路由到模块Blog的category操作
 *
 * <!-- $_GET参数: -->
 * < [a] => blog
 *   [c] => category
 *   [id] => 121
 *   [test1] => 1
 *   [test2] => 2
 *   [test3] => 3
 *   [par1] => 1
 *   [par2] => 2 >
 * < [a] => category
 *   [c] => blog
 *   [id1] => 121
 *   [id2] => 1
 *   [hello] => 232
 *   [par1] => 1
 *   [par2] => 2 >
 */
 //'Category'=>array('category/blog','id,test1,test2,test3','par1=1&par2=2'),
 //'Blog'=>array('regex'=>'/^(\d+)\/(\d+)/','blog/category','id1,id2','par1=1&par2=2'),

/**
 * 泛路由
 *
 * <!-- 当前URL：-->
 * < http://localhost/doyouhaobaby/branch/TestApp/blog/index.php/12
 *   路由到模块 blog 的read 操作  >
 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/12/34
 *   路由到模块 blog 的archive 操作  >
 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/hello
 *   路由到模块 blog 的category 操作  >
 *
 * <!-- $_GET参数: -->
 * < [a] => read
 *   [c] => blog
 *   [id] => 12
 *   [par1] => 1
 *   [par2] => 2 >
 * < [a] => archive
 *   [c] => blog
 *   [year] => 12
 *   [month] => 34
 *   [par1] => 1
 *   [par2] => 2 >
 * < [a] => category
 *   [c] => blog
 *   [test] => hello
 *   [par1] => 1
 *   [par2] => 2 >
 */
 //'Blog@'=>array(
 //array('regex'=>'/^(\d+)$/','blog/read','id','par1=1&par2=2'),
      //array('regex'=>'/^(\d+)\/(\d+)/','blog/archive','year,month','par1=1&par2=2'),
      //array('blog/category','test','par1=1&par2=2'),
 //)

	/**
	 * 当前路由名字(最后一次获取的路由名字)
	 *
	 * @var array
	 */
	protected $_sLastRouterName=null;

	/**
	 * 当前路由信息(最后一次获取的路由信息)
	 *
	 * @var array
	 */
	protected $_arrLastRouteInfo=array();

	/**
	 * 所有路由
	 *
	 * @var array
	 */
	protected $_arrRouters=array();

	/**
	 * URL分析对象
	 *
	 * @var UrlParse
	 */
	protected $_oUrlParseObj=null;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param object $oUrlParseObj
	 */
	public function __construct($oUrlParseObj=null){}

	/**
	 * 返回指定路由信息 < 没有指定则返回默认信息  >
	 *
	 * @access public
	 * @param string,null $sRouterName 路由名字
	 * @return array
	 */
	public function G($sRouterName=null){}

	/**
	 * 导入路由规则
	 *
	 * < 如果指定了 $sCacheId 参数，则首先尝试从缓存载入的路由规则！  >
	 *
	 * @param array $arrRouters 路由规则
	 * @param string $sCacheId 缓存ID
	 *
	 * @return Router
	 */
	public function import(array $arrRouters=null, $sCacheId=null){}

	/**
	 * 添加一条路由规则
	 *
	 * @param string $route_name
	 * @param array $rule
	 * @return Router
	 */
	public function add($sRouteName,array $arrRule){}

	/**
	 * 移除指定的路由规则
	 *
	 * @access public
	 * @param string $sRouteName 路由名字
	 * @return Router
	 */
	public function remove($sRouteName){}

	/**
	 * 取得指定名称的路由规则
	 *
	 * @access public
	 * @param string $sRouteName 路由名字
	 * @return array
	 */
	public function get($sRouteName){}

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
	 * 分析路由组/模块/方法参数
	 *
	 * <!-- 说明 -->
	 *
	 * < 分组可以不指定，模块和方法必须指定 >
	 * < 字符串1:group/module/action  >
	 * < 数组2：array('group','module','action'),注意这里顺序不能颠倒!>
	 * < 字符串1:module/action  >
	 * < 数组2：array('module','action'),注意这里顺序不能颠倒!>
	 *
	 * <!-- 返回结果 -->
	 * < Array([a] => action [c] => module [g] => group)>
	 * < Array([a] => action [c] => module [g] => group)>
	 * < Array([a] => action [c] => module)>
	 * < Array([a] => action [c] => module)>
	 *
	 * @param mixed $Route 路由参数
	 */
	private function parseUrl($Route){}

	/**
	 * 获取路由名字
	 *
	 * @access public
	 * @return string
	 */
	private function getRouterName(){}

	/**
	 * 获取指定的普通路由
	 *
	 * @acces public
	 * @param string $sRouteName 路由名字
	 * @param array $arrRule 路由规则
	 * @return array
	 */
	private function getNormalRoute($sRouteName, array $arrRule){}

	/**
	 * 获取指定的泛路由
	 *
	 * @acces public
	 * @param string $sRouteName 路由名字
	 * @param array $arrRule 路由规则
	 * @return array
	 */
	private function getFlowRoute($sRouteName,array $arrRule){}

	/**
	 * 分析简单的路由
	 *
	 * @access public
	 * @param string $sRouteName 路由名称
	 * @param array $arrRule 路由规则
	 * @return array
	 */
	private function getSimpleRoute_($sRouteName,$arrRule){}

	/**
	 * 分析简单正则的路由
	 *
	 * @access public
	 * @param string $sRouteName 路由名称
	 * @param array $arrRule 路由规则
	 * @return array
	 */
	private function getRegexRoute_($sRouteName,$arrRule){}

}
