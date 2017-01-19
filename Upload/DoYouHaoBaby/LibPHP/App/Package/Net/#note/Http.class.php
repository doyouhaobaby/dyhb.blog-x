<?dyhb
class Http{
	
	/**
	 * 采集远程文件
	 *
	 * @access public
	 * @param string $sRemote 远程文件名
	 * @param string $sLocal 本地保存文件名
	 * @return mixed
	 */
	static public function curlDownload($sRemote,$sLocal){}

	/** 
	 * 下载文件
	 *
	 * < 可以指定下载显示的文件名，并自动发送相应的Header信息
	 *   如果指定了$sContent参数，则下载该参数的内容 >
	 *
	 * @static
	 * @access public
	 * @param string $sFilename 下载文件名
	 * @param string $sShowname 下载显示的文件名
	 * @param string $sContent 下载的内容
	 * @param integer $nExpire 下载内容浏览器缓存时间
	 * @return void
	 */
	static public function download($sFilename,$sShowname='',$sContent='',$nExpire=180){}

	/**
	 * 显示HTTP Header 信息
	 *
	 * @static
	 * @access public
	 * @param string $sHeader 头部信息
	 * @param bool $bEcho 是否输出
	 * @return string
	 */
	static function getHeaderInfo($sHeader='',$bEcho=true){}

	/**
	 * HTTP Protocol defined status codes
	 *
	 * @static
	 * @access public
	 * @param int $nCode Http状态
	 * @return void 
	 */
	static function sendHttpStatus($nCode){}

}
