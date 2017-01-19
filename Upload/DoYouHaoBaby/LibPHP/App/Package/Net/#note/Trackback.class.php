<?dyhb
class Trackback {

	/**
	 * 错误消息框
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sErrorMessageBox="<font color='red'>***</font>";

	/**
	 * 正确消息框
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sSucessedMessageBox="<font color='green'>***</font>";

	/**
	 * 待发送的博客地址
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sBlogUrl;

	/**
	 * 待发送的博文标题
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sTitle;

	/**
	 * 待发送的博客名字
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sBlogName;

	/**
	 * 待发送的博文摘要
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sExcerpt;

	/**
	 * 所有trackback地址
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrTrackbacks=array();

	/**
	 * 发送成功的trackback地址
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrSucessedTrackbacks=array();

	/**
	 * 发送失败的trackback地址
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrFailedTrackbacks=array();

	/**
	 * 发送引用是否出错
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsError=false;

	/**
	 * 发送成功的trackback反馈消息
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrSucessedTrackbackMessages=array();

	/**
	 * 发送失败的trackback消息
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrFailedTrackbackMessages=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sBlogUrl string 待发送的博客地址
	 * @param $sTitle string 待发送的博文标题
	 * @param $sBlogUrl string 待发送的博客名字
	 * @param $sBlogUrl string 待发送的博文摘要
	 * @return void
	 */
	public function __construct($sBlogUrl,$sTitle='',$sBlogName='',$sExcerpt=''){}

	/**
	 * 发送trackback
	 *
	 * @access public
	 * @param $sPingUrl string 发送引用的地址列表
	 * @return string(trackback 反馈消息)
	 */
	public function sendTrackback($sPingUrl){}

	/**
	 * 发送一个引用
	 *
	 * @access protected
	 * @param $sUrl string Trackback 地址
	 * @param $sData string 发送的数据
	 * @return string(http 返回消息)
	 */
	protected function sendPacket($sUrl,$sData){}

	/**
	 * 清理所有trackback引用
	 *
	 * @access public
	 * @return int
	 */
	public function clearTrackbacks(){}

	/**
	 * 清理所有成功的trackback引用
	 *
	 * @access public
	 * @return int
	 */
	public function clearSucessedTrackbacks(){}

	/**
	 * 清理所有失败的trackback引用
	 *
	 * @access public
	 * @return int
	 */
	public function clearFailedTrackbacks(){}

	/**
	 * 清理所有trackback引用成功反馈消息
	 *
	 * @access public
	 * @return int
	 */
	public function clearSucessedTrackbackMessages(){}

	/**
	 * 清理所有trackback引用失败反馈消息
	 *
	 * @access public
	 * @return int
	 */
	public function clearFailedTrackbackMessages(){}

	/**
	 * 设置发送引用是否出错
	 *
	 * @access public
	 * @param $bIsError string 发送引用是否出错
	 * @return oldValue
	 */
	public function setIsError($bIsError=true){}

	/**
	 * 设置错误消息框
	 *
	 * @access public
	 * @param $sErrorMessageBox string 错误消息框
	 * @return oldValue
	 */
	public function setErrorMessageBox($sErrorMessageBox){}

	/**
	 * 设置正确消息框
	 *
	 * @access public
	 * @param $sSucessedMessageBox string 正确消息框
	 * @return oldValue
	 */
	public function setSucessedMessageBox($sSucessedMessageBox){}

	/**
	 * 设置博客的地址
	 *
	 * @access public
	 * @param $sBlogUrl string 待发送的博客地址
	 * @return oldValue
	 */
	public function setBlogUrl($sBlogUrl){}

	/**
	 * 设置博文标题
	 *
	 * @access public
	 * @param $sTitle string 待发送的博文标题
	 * @return oldValue
	 */
	public function setTitle($sTitle){}

	/**
	 * 设置博客名字
	 *
	 * @access public
	 * @param $sBlogName string 待发送的博客名字
	 * @return oldValue
	 */
	public function setBlogName($sBlogName){}

	/**
	 * 设置博客的地址
	 *
	 * @access public
	 * @param $sExcerpt string 待发送的博客摘要
	 * @return oldValue
	 */
	public function setExcerpt($sExcerpt){}

	/**
	 * 引用反馈正确
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static public function xmlSuccess(){}

	/**
	 * 引用反馈错误
	 *
	 * @access public
	 * @static
	 * @param  string $sError 错误消息
	 * @return void
	 */
	static public function xmlError($sError){}

	/**
	 * 取回博客的地址
	 *
	 * @access public
	 * @return string
	 */
	public function getBlogUrl(){}

	/**
	 * 取回博文标题
	 *
	 * @access public
	 * @return string
	 */
	public function getTitle(){}

	/**
	 * 取回博客名字
	 *
	 * @access public
	 * @return string
	 */
	public function getBlogName(){}

	/**
	 * 取回博客的地址
	 *
	 * @access public
	 * @return string
	 */
	public function getExcerpt(){}

	/**
	 * 取回所有引用地址
	 *
	 * @access public
	 * @return array
	 */
	public function getTrackbacks(){}

	/**
	 * 取回所有发送成功的引用
	 *
	 * @access public
	 * @return array
	 */
	public function getSucessedTrackbacks(){}

	/**
	 * 取回所有发送失败的引用
	 *
	 * @access public
	 * @return array
	 */
	public function getFailedTrackbacks(){}

	/**
	 * 发送引用是否出错
	 *
	 * @access public
	 * @return bool
	 */
	public function isError(){}

	/**
	 * 取回所有发送成功的返回消息
	 *
	 * @access public
	 * @return array
	 */
	public function getSucessedTrackbackMessages(){}

	/**
	 * 取回所有发送失败的返回消息
	 *
	 * @access public
	 * @return array
	 */
	public function getFailedTrackbackMessages(){}

}
