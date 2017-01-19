<?dyhb
class UploadFile{

	/**
	 * 错误代号常量
	 *
	 * @access const
	 * @var int
	 */
	const ERR_NOUPLOAD=1;// 文件未上传
	const ERR_EXMAXBYTE_BY_PHP=2;// 超过限制字节数（在 php.ini 中设定）
	const ERR_UNALLOWEXT=3;// 不允许的文件类型
	const ERR_EXMAXBYTE=4;// 超过限制字节数
	const ERR_STOREDIR_NOTEXIST=5;// 存储目录不存在，且 UploadFile::setAutoCreateStoreDir(false);
	const ERR_STOREDIR_UNCREATE=6;// 无法自动创建 存储目录
	const ERR_UNCOPY=7;// 无法将上传文件 由 临时路径拷贝至 存储路径
	const ERR_CANCEL=8;// 上传被取消

	/**
	 * 允许上传的 最大字节数， 204800byte=200Kb
	 *
	 * @access public
	 * @var int
	 */
	static public $MAXSIZE=204800;

	/**
	 * 上传文件的最大值
	 *
	 * @access public
	 * @var int
	 */
	public $_nMaxSize=-1;

	/**
	 * 是否支持多文件上传
	 *
	 * @access public
	 * @var bool
	 */
	public $_bSupportMulti=true;

	/**
	 * 允许上传的文件后缀,留空不作后缀检查
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAllowExts=array();

	/**
	 * 不允许上传的文件后缀,留空不作后缀检查
	 *
	 * @access public
	 * @var array
	 */
	public $_arrNotAllowExts=array('php','php5');

	/**
	 * 允许上传的文件类型,留空不做检查
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAllowTypes=array();

	/**
	 * 不允许上传的文件类型,留空不做检查
	 *
	 * @access public
	 * @var array
	 */
	public $_arrNotAllowTypes=array();

	/**
	 * 使用对上传图片进行缩略图处理
	 *
	 * @access public
	 * @var bool
	 */
	public $_bThumb=false;

	/**
	 * 缩略图最大宽度
	 *
	 * @access public
	 * @var int
	 */
	public $_nThumbMaxWidth;

	/**
	 * 缩略图最大高度
	 *
	 * @access public
	 * @var int
	 */
	public $_nThumbMaxHeight;

	/**
	 * 缩略图前缀
	 *
	 * @access public
	 * @var string
	 */
	public $_sThumbPrefix='thumb_';

	/**
	 * 缩略图后缀
	 *
	 * @access public
	 * @var string
	 */
	public $_sThumbSuffix = '';

	/**
	 * 缩略图保存路径
	 *
	 * @access public
	 * @var string
	 */
	public $_sThumbPath='';

	/**
	 * 缩略图文件名
	 *
	 * @access public
	 * @var string
	 */
	public $_sThumbFile='';

	/**
	 * 是否移除原图
	 *
	 * @access public
	 * @var bool
	 */
	public $_bThumbRemoveOrigin=false;

	/**
	 * 压缩图片文件上传
	 *
	 * @access public
	 * @var bool
	 */
	public $_bZipImages=false;

	/**
	 * 启用子目录保存文件
	 *
	 * @access public
	 * @var bool
	 */
	public $_bAutoSub=false;

	/**
	 * 子目录创建方式 可以使用hash date 以及目录层次
	 *
	 * @access public
	 * @var string
	 */
	public $_sSubType='hash';
	public $_sDateFormat='Ymd';
	public $_nHashLevel= 1; // hash的目录层次

	/**
	 * 上传文件保存路径
	 *
	 * @access public
	 * @var string
	 */
	public $_sSavePath='';
	public $_bAutoCheck=true; // 是否自动检查附件

	/**
	 * 存在同名是否覆盖
	 *
	 * @access public
	 * @var bool
	 */
	public $_bUploadReplace=false;

	/**
	 * 上传文件命名规则,例如可以是 md5,必须是一个只有一个参数的函数，可以使用自定义函数
	 *
	 * @access private
	 * @var public
	 */
	public $_sSaveRule='';

	/**
	 * 上传文件Hash规则函数名,例如可以是 md5_file sha1_file 等
	 *
	 * @access public
	 * @var string
	 */
	public $_sHashType='md5_file';

	/**
	 * 错误信息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sError='';

	/**
	 * 上传成功的文件信息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_arrUploadFileInfo;

	/**
	 * 保持原名
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bKeepOriginalName=false;

	/**
	 * 是否自动创建目录
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bAutoCreateStoreDir=false;

	/**
	 * 最后一个input名字
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sLastInput;

	/**
	 * 最后一个input表单文件信息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_arrLastFileinfo=array();

	/**
	 * 是否写入安全文件
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bWriteSafeFile=true;

	/**
	 * 图片加水印
	 *
	 * @access public
	 * @var bool
	 */
	public $_bIsImagesWaterMark=false;

	/**
	 * 图片加水印类型
	 *
	 * @access public
	 * @return int
	 */
	const TEXT='text';
	const IMG='img';
	public $_sImagesWaterMarkType=self::TEXT;

	/**
	 * 图片水印配置
	 *
	 * @access public
	 * @var string
	 */
	public $_arrImagesWaterMarkImg=array('path'=>'','offset'=>'');

	/**
	 * 文字水印配置
	 *
	 * @access public
	 * @var array
	 */
	public $_arrImagesWaterMarkText=array('content'=>'DoYouHaoBaby','textColor'=>'#000000','textFont'=>'','textFile'=>'','offset'=>'');

	/**
	 * 水印位置
	 *
	 * @access protected
	 * @var int
	 */
	 public $_nWaterPos=0;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param int $nMaxSize 文件最大长度
	 * @param array|sting $AllowExts 允许的后缀
	 * @param array|string $AllowTypes 允许的类型
	 * @param string $sSavePath 保存的路径
	 * @param string $sSaveRule 保存规则
	 * @return void
	 */
	public function __construct($nMaxSize='',$AllowExts='',$AllowTypes='',$sSavePath='',$sSaveRule=''){}

	/**
	 * 上传一个文件
	 *
	 * @access public
	 * @param array $arrFile 数据
	 * @return string
	 */
	protected function save($arrFile){}

	/**
	 * 上传文件
	 *
	 * @access public
	 * @param string $sSavePath 上传文件保存路径
	 * @return string
	 */
	public function upload($sSavePath=''){}

	public function cancelUpload($sInputName=null){}

	/**
	 * 转换上传文件数组变量为正确的方式
	 *
	 * @access private
	 * @param array $arrFiles 上传的文件变量
	 * @return array
	 */
	protected function dealFiles($arrFiles){}

	/**
	 * 获取错误代码信息
	 *
	 * @access public
	 * @param string $nErrorNo 错误号码
	 * @return void
	 */
	protected function error($nErrorNo){}

	/**
	 * 根据上传文件命名规则取得保存文件名
	 *
	 * @access protected
	 * @param array $arrFilename 数据
	 * @return string
	 */
	protected function getSaveName($arrFilename){}

	/**
	 * 获取子目录的名称
	 *
	 * @access protected
	 * @param array $arrFile  上传的文件信息
	 * @return string
	 */
	protected function getSubName($arrFile){}

	/**
	 * 检查上传的文件
	 *
	 * @access protected
	 * @param array $arrFile 文件信息
	 * @return boolean
	 */
	protected function check($arrFile){}

	/**
	 * 从默认的存储文件名中 取得原始文件名
	 *
	 * @access public
	 * @param $sStoreName string
	 * @static
	 * @return string
	 */
	static public function getOriginalNameFromStoreName($sStoreName){}

	public function getOriginalName($sInputName=null){}

	public function getOriginalExt($sInputName=null){}

	public function getOriginalType($sInputName=null){}

	public function getTempPath($sInputName=null){}

	public function getLength($sInputName=null){}

	public function setExts($MixedExtName,$bAllow=true){}

	public function removeExt($sExtName,$bAllow=true){}

	public function getExts($bAllow=true){}

	/**
	 * 检查上传的文件类型是否合法
	 *
	 * @access protected
	 * @param string $sType 数据
	 * @return boolean
	 */
	protected function checkType($sType){}

	/**
	 * 检查上传的文件后缀是否合法
	 *
	 * @access protected
	 * @param string $sExt 后缀名
	 * @return boolean
	 */
	protected function checkExt($sExt){}

	/**
	 * 检查文件大小是否合法
	 *
	 * @access protected
	 * @param integer $nSize 数据
	 * @return boolean
	 */
	protected function checkSize($nSize){}

	/**
	 * 检查文件是否非法提交
	 *
	 * @access protected
	 * @param string $sFilename 文件名
	 * @return boolean
	 */
	protected function checkUpload($sFilename){}

	/**
	 * 取得上传文件的后缀
	 *
	 * @access protected
	 * @param string $sFilename 文件名
	 * @return boolean
	 */
	protected function getExt($sFilename){}

	public function getLastInput(){}

	public function getLastFileInfo(){}

	/**
	 * 取得上传文件的信息
	 *
	 * @access public
	 * @return array
	 */
	public function getUploadFileInfo(){}

	/**
	 * 取得最后一次错误信息
	 *
	 * @access public
	 * @return string
	 */
	public function getErrorMessage(){}

	public function setAutoCreateStoreDir($bAutoCreateStoreDir=true){}

	public function setKeepOriginalName($bKeepOriginalName=true,$bUploadReplace=false){}

	public function setUploadReplace($bUploadReplace=false){}

	public function setMaxSize($nMaxSize=null){}

	static function getReadableFileSize($nByte,$nPrecision=2){}

}
