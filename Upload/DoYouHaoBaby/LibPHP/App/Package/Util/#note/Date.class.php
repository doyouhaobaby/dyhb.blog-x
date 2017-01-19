<?dyhb
class Date{

	/**
	 * 时间转化函数
	 *
	 * @access public
	 * @static
	 * @param $nDateTemp 需要转换的时间 int
	 * @param $sDateFormat 格式化参数 string
	 * @return string
	 */
	static public function smartDate($nDateTemp,$sDateFormat='Y-m-d H:i'){}

	/**
	 * 时间处理,返回unix中一个月的开头和结尾，一年的开头和结尾
	 *
	 * @access public
	 * @static
	 * @param int $nT 给定的日期，只能是4位或者6位 例如: 201200 或者 20120000
	 * @return array
	 */
	static public function getTheFirstOfYearOrMonth($nT){}

	/**
	 * 返回当天时间的开始和结束的unix时间戳
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	static public function getTheDataOfNowDay(){}

}
