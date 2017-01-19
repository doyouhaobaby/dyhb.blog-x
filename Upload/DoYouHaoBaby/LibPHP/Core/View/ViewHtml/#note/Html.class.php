<?dyhb
class Html{

	/**
	 * 缓存有效期（支持函数）
	 *
	 * @access private
	 * @var int|string
	 * @static
	 */
	static private $_nCacheTime;

	/**
	 * 是否需要缓存
	 *
	 * @access private
	 * @var bool
	 * @static
	 */
	static private $_bRequireCache=false;

	/**
	 * 判断是否需要静态缓存
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	static private function RC(){}

	/**
	 * 读取静态缓存
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function R(){}

	/**
	 * 写入静态缓存
	 *
	 * @static
	 * @access public
	 * @param string $sContent 页面内容
	 * @return void
	 */
	static public function W($sContent ){}

	/**
	 * 检查静态HTML文件是否有效
	 *
	 * < 如果无效需要重新更新 >
	 *
	 * @static
	 * @access public
	 * @param string $sCacheFile 静态文件名
	 * @param mixed $CacheTime 缓存有效期
	 * @return boolen
	 */
	static public function C($sCacheFile='',$CacheTime=''){}

	/**
	* 检查是否是空操作
	*
	* @static
	* @access private
	* @param string $sModule 模块
	* @param string $sAction 方法
	* @return void
	*/
	static private function EC($sModule,$sAction){}

}
