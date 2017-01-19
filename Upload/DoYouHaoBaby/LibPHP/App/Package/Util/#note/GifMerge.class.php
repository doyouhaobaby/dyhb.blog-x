<?dyhb
/**
 * 本类可以被继承
 */
class GifMerge{

	/**
	 * GIF动画处理使用文件还是内存来存取临时数据
	 *
	 * @access protected
	 * @var string
	 */
	const C_FILE='C_FILE';
	const C_MEMORY='C_MEMORY';

	/**
	 * 版本
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sVer='1.1';

	/**
	 * 延迟
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nDly=50;

	/**
	 * GIF动画处理使用文件还是内存来存取临时数据
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sMod=self::C_FILE;

	/**
	 * bFirst
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bFirst=true;

	/**
	 * UseLoop
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bUseLoop=false;

	/**
	 * TransParent
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bTransParent=false;

	/**
	 * UseGlobalIn
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bUseGlobalIn=false;

	/**
	 * 横轴坐标
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nX=0;

	/**
	 * 纵轴坐标
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nY=0;

	/**
	 * 字节指针
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nCh=0;

	/**
	 * 文件数据写入指针
	 *
	 * @access protected
	 * @var int
	 */
	protected $_hFin=0;

	/**
	 * 保留新创建的动画的数据
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sFout='';

	/**
	 * 循环次数
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nLoop=0;

	/**
	 * 延迟时间
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nDelay=0;

	/**
	 * 宽度
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nWidth=0;

	/**
	 * 高度
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nHeight=0;

	/**
	 * Srans1
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_nSrans1=255;

	/**
	 * Srans2
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_nSrans2=255;

	/**
	 * Srans3
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_nSrans3=255;

	/**
	 * Disposal
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nDisposal=2;

	/**
	 * 输出颜色表大小
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nOutColorTableSize=0;

	/**
	 * 本地颜色表
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nLocalColorTableFlag=0;

	/**
	 * 全局颜色表的大小
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_nGlobalColorTableSize=0;

	/**
	 * 输出颜色表
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nOutColorTableSizecode=0;

	/**
	 * 全局颜色表sizecode
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nGlobalColorTableSizecode=0;

	/**
	 * Gif格式数据
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrGif=array(0x47,0x49,0x46);

	/**
	 * 缓存数据
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_arrBuffer=array();

	/**
	 * 本地全局写入
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrLocalIn=array();

	/**
	 * 全局数据写入
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrGlobalIn=array();

	/**
	 * 全局输出
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrGlobalOut=array();

	/**
	 * 逻辑屏幕描述
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrLogicalScreenDescriptor=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param array $arrCodeSet
	 * @return void
	 */
	public function __construct( $arrImages,$nT1,$nT2,$nT3,$nLoop,$arrDl,$arrXpos,$arrYpos,$sModel){}

	/**
	 * 开始Gif动画合并进程
	 *
	 * @access protected
	 * @param string $sFp 图像数据
	 * @return void
	 */
	protected function startGifmergeProcess($sFp){}

	/**
	 * 读取图像信息
	 *
	 * @access protected
	 * @return void
	 */
	protected function readImageDescriptor(){}

	/**
	 * 读取扩展信息
	 *
	 * @access protected
	 * @return void
	 */
	protected function readExtension(){}

	/**
	 * 比较数组前几个数据是否相同
	 *
	 * @access protected
	 * @param $arrB array 一个数据
	 * @param $arrS array 另一个数据
	 * @param $nL int 长度
	 * @reutrn bool
	 */
	protected function arrCmp($arrB,$arrS,$nL){}

	/**
	 * 取得指定长度的数据字节内容
	 *
	 * @access protected
	 * @param $nL int 字节指定位置
	 * @reutrn void
	 */
	protected function getBytes($nL){}

	/**
	 * 写入数据
	 *
	 * @access protected
	 * @param $arrS array 字节内容
	 * @param $nL int 字节指定位置
	 * @reutrn images
	 */
	protected function putBytes($arrS,$nL){}

	/**
	 * 取回动画内容
	 *
	 * @access protected
	 * @reutrn images
	 */
	public function getAnimation(){}

}
