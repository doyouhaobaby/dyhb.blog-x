<?dyhb
class App{

	/**
	 * 控制器实例
	 *
	 * @access private
	 * @var obj
	 */
	private static $_oControl;

	/**
	 * 应用程序初始化
	 *
	 * @access private
	 * @static
	 */
	static private function init_(){}

	/**
	 * 全局入口
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	static public function RUN(){}

	/**
	 * 执行应用程序
	 *
	 * @access public
	 * @return void
	 */
	static public function execute(){}

	/**
	 * 空模块数据处理
	 *
	 * @access private
	 * @return mixed obj|bool
	 */
	private static function emptyModule(){}

	/**
	 * 空方法数据处理
	 *
	 * @access private
	 * @return mixed obj|bool
	 */
	private static function emptyAction(){}

	/**
	 * 分析扩展配置，返回模块扩展配置信息
	 *
	 * @access private
	 * @param mixed(string|array) $ExtendConfig 扩展模块的配置信息
	 * @return array|null 返回扩展配置信息数组，如果数组为空的话，那么返回NULL
	 */
	static private function parseExtendModule($ExtendConfig){}

	/**
	 * 直接加载视图文件
	 *
	 * @access public
	 * @return void
	 */
	static private function display(){}

	/**
	 * 模板检查，如果不存在使用默认
	 *
	 * @access private
	 * @return void
	 */
	static private function checkTemplate(){}

	/**
	 * 语言自动检测
	 *
	 * @access private
	 * @return void
	 */
	static private function checkLanguage(){}

	/**
	 * 初始化系统设置
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function initSystem_(){}

	/**
	 * 系统运行过程中的相关路径定义
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	static private function constantDefine(){}

	/**
	 * 初始化Javascript的U方法
	 * 
	 * < 本函数主要配合Dyhb.U函数的使用 >
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	static public function U(){}

	/**
	 * 初始化Javascript的U2方法
	 * 
	 * < 本函数主要配合Dyhb.U2函数的使用 >
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	static public function U2(){}

}
