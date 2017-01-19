<?dyhb
class Mail{

	/**
	 * SMTP 邮件地址或IP 如：smtp.126.com
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sServer='';

	/**
	 * SMTP 邮件端口
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nPort=25;

	/**
	 * SMTP 邮件服务器是否要求身份验证
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bAuth=TRUE;

	/**
	 * SMTP 邮件用户名
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sAuthUsername='';

	/**
	 * SMTP 邮件密码
	 *
	 * @access protected
	 * @var string
	 */
	protected  $_sAuthPassword='';

	/**
	 * SMTP 邮件发送人
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sEmailFrom='';

	/**
	 * SMTP 邮件接收人
	 *
	 * < 多个用逗号隔开 如：635750556@qq.com,doyouhaobaby2009@gmail.com  >
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sEmailTo='';

	/**
	 * 邮件头的分隔符
	 *
	 * < 1使用 CRLF 作为分隔符(通常为 Windows 主机)
	 *   0使用 LF 作为分隔符(通常为 Unix/Linux 主机)
	 *   2使用 CR 作为分隔符(通常为 Mac 主机)  >
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nEmailLimiter=1;

	/**
	 * 邮件标题
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sEmailSubject='';

	/**
	 * 邮件内容
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sEmailMessage='';

	/**
	 * 收件人地址中包含用户名
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bEmailUsername=TRUE;

	/**
	 * 邮件发送字符集
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sCharset='UTF-8';

	/**
	 * 网站名称
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sSiteName='DoYouHaoBaby Mail';

	/**
	 * 是否使用html
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIshtml=TRUE;

	/**
	 * 内容字符
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sContentType='text/plain';

	/**
	 * 邮件错误
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sErrorMessage;

	/**
	 * 邮件是否错误
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsError;

	/**
	 * 邮件发送方式
	 *
	 * @access protected
	 * @const
	 * @var string
	 */
	CONST PHP_MAIL='mail';
	CONST SOCKET_SMTP='socket_smtp';
	CONST PHP_SMTP='php_smtp';

	protected $_sEmailSendType=self::SOCKET_SMTP;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @var bool
	 */
	public function __construct($sServer='',$sAuthUsername='',$sAuthPassword='',$nPort=25,$sEmailSendType=self::SOCKET_SMTP){}

	/**
	 * 发送邮件
	 *
	 * @access public
	 * @var bool
	 */
	public function send(){}

	public function setServer($sServer=''){}

	public function setPort($nPort=25){}

	public function setAuth($bAuth=TRUE){}

	public function setAuthUsername($sAuthUsername=''){}

	public function setAuthPassword($sAuthPassword=''){}

	public function setEmailFrom($sEmailFrom=''){}

	public function setEmailTo($sEmailTo=''){}

	public function setEmailLimiter($nEmailLimiter=1){}

	public function setEmailSubject($sEmailSubject=''){}

	public function setEmailMessage($sEmailMessage=''){}

	public function setEmailUsername($bEmailUsername=TRUE){}

	public function setCharset($sCharset='UTF-8'){}

	public function setSiteName($sSiteName=''){}

	public function setIsHtml($bIsHtml=TRUE){}

	public function setContentType($sContentType='text/plain'){}

	public function getServer(){}

	public function getPort(){}

	public function getAuth(){}

	public function getAuthUsername(){}

	public function getAuthPassword(){}

	public function getEmailFrom(){}

	public function getEmailTo(){}

	public function getEmailLimiter(){}

	public function getEmailSubject(){}

	public function getEmailMessage(){}

	public function getEmailUsername(){}

	public function getCharset(){}

	public function getSiteName(){}

	public function getIsHtml(){}

	public function getContentType(){}

	protected function setIsError($bIsError=false){}

	protected function setErrorMessage($sErrorMessage=''){}

	public function isError(){}

	public function getErrorMessage(){}

}
