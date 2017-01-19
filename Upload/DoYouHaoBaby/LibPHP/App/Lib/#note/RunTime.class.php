<?dyhb
class RunTime{

   /**
	* 所有注册在PHP运行结束时的函数
	*
	* @access private
	* @static
	* @var array
	*/
	static private $_arrHandles=array();

	/**
	 * PHP运行时错误
	 *
	 * @access public
	 * @access const
	 * @var
	 */
	const HANDLE_ERROR='error';

	/**
	 * PHP运行时终止
	 *
	 * @access public
	 * @access const
	 * @var
	 */
	const HANDLE_SHUTDOWN='shutdown';

	/**
	 * 响应PHP结束时的方法
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function runtimeShutdown(){}

	/**
	 * 注册一个在PHP执行结束被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @static
	 * @return void
	 */
	static public function registerShutdown($Callback){}

	/**
	 * 移除一个在PHP执行结束被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @static
	 * @return void
	 */
	static public function unRegisterShutdown($Callback){}

	/**
	 * 检查是否为PHP执行结束时被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @static
	 * @return bool
	 */
	static public function isShutdown($Callback){}

	/**
	 * 退出钩子函数：打印程序退出前产生的错误信息
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function exitBeforeShutdown(){}

	/**
	 * 响应PHP错误事件的方法
	 *
	 * @access public
	 * @param int $nErrno 错误类型
	 * @param string $sErrstr 错误信息
	 * @param string $sErrfile 错误文件
	 * @param int $sErrline 错误行数
	 * @static
	 * @return void
	 */
	static public function errorHandel($nErrorNo,$sErrStr,$sErrFile,$nErrLine){}

	/**
	 * 注册一个在PHP执行结束被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param int $nErrorType=E_ALL 类型
	 * @static
	 * @return void
	 */
	static public function registerError($Callback,$nErrorType=E_ALL){}

	/**
	 * 移除一个在PHP执行结束被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param int $nErrorType=E_ALL 类型
	 * @static
	 * @return void
	 */
	static public function unRegisterError($Callback,$nErrorType=E_ALL){}

	/**
	 * 检查是否为PHP执行结束时被调用的收尾函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param int $nErrorType=E_ALL 类型
	 * @static
	 * @return bool
	 */
	static public function isError($Callback,$nErrorType=E_ALL){}

	/**
	 * 移除一个PHP运行时事件处理函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param string $sHandle 句柄
	 * @static
	 * @return void
	 */
	static public function unRegisterRuntimeHandle($Callback,$sHandle){}

	/**
	 * 查找一个PHP运行时事件处理函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param string $sHandle 句柄
	 * @static
	 * @return bool
	 */
	public function isRuntimeHandle($Callback,$sHandle){}

	/**
	 * 注册一个PHP运行时事件处理函数
	 *
	 * @access public
	 * @param callback $Callback 回调
	 * @param string $sHandle 句柄
	 * @static
	 * @return void
	 */
	static public function registerRuntimeHandle($Callback,$sHandle){}

	/**
	 * 查找一个PHP运行时事件处理函数
	 *
	 * @access private
	 * @param callback $Callback 回调
	 * @param string $sHandle 句柄
	 * @static
	 * @return int
	 */
	private static function findRuntimeHandle($Callback,$sHandle){}

	/**
	 * 响应PHP运行时事件的方法
	 *
	 * @access public
	 * @param string $sHandle 句柄
	 * @param array $arrArgs 参数
	 * @static
	 * @return void
	 */
	static public function runtimeEvent($sHandle,$arrArgs=array()){}

}
