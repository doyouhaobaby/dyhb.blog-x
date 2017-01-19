<?dyhb
class Debug{

	static private $_arrDebug=array();

	/**
	 * 标记调试位
	 *
	 * @access public
	 * @static
	 * @param string $sName 要标记的位置名称
	 * @return void
	 */
	static public function mark($sName){}

	/**
	 * 区间使用时间查看
	 *
	 * @access public
	 * @static
	 * @param string $sStart 开始标记的名称
	 * @param string $sEnd 结束标记的名称
	 * @param integer $nDecimals 时间的小数位
	 * @return integer
	 */
	static public function useTime($sStart,$sEnd,$nDecimals=6){}

	/**
	 * 区间使用内存查看
	 *
	 * @access public
	 * @static
	 * @param string $sStart 开始标记的名称
	 * @param string $sEnd 结束标记的名称
	 * @return integer
	 */
	static public function useMemory($sStart,$sEnd){}

	/**
	 * 区间使用内存峰值查看
	 *
	 * @access public
	 * @static
	 * @param string $sStart 开始标记的名称
	 * @param string $sEnd 结束标记的名称
	 * @return integer
	 */
	static function getMemPeak($sStart,$sEnd){}

	/**
	 * 调式开始
	 *
	 * @access public
	 * @static
	 * @param string $sLabel 标记label 名字
	 * @return void
	 */
	static function debugStart($sLabel=''){}

	/**
	 * 调试结束
	 *
	 * @access public
	 * @static
	 * @param string $sLabel 标记label名字
	 * @return void
	 */
	static function debugEnd($sLabel=''){}

}
