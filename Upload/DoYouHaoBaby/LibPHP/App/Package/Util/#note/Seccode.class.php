<?dyhb
/**
 * 本类可以被继承
 *
 * < 部分GD函数：
 *   1：imagecreatetruecolor imagecreatetruecolor( int x_size,int y_size ) // x就是宽，y就是高。
 *   2：imagecolorallocate( resource image, int red,int green,int,int blue ) // 调色
 *   3：imagestring( resource image,font,int x,int y ,内容,颜色) // 绘图函数
 *   4：imagesetpixel(int im, int x, int y, int col)// 画像素
 *   5：imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color ) // 画线条 >
 */
class Seccode{

	/**
	 * 100000-999999 范围内随机
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nCode;

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
	 * 是否创建背景图像
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bBackground=TRUE;

	/**
	 * 是否使用系统提供的图像创建背景图像
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bImageBackground=TRUE;

	/**
	 * 随机背景图形
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bAdulterate=TRUE;

	/**
	 * 随机 TTF 字体
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bTtf=TRUE;

	/**
	 * 随机倾斜度
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bTilt=TRUE;

	/**
	 * 随机颜色
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bColor=TRUE;

	/**
	 * 随机大小
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bSize=TRUE;

	/**
	 * 文字阴影
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bShadow=TRUE;

	/**
	 * TTF 字库目录
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sFontPath;

	/**
	 * 验证码相关数据目录
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sDataPath='';

	/**
	 * 文字颜色
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrFontColor;

	/**
	 * 创建的图像
	 *
	 * @access protected
	 * @var image
	 */
	protected $_oIm;

	/**
	 * gif验证码路径（ D:\phpcondition\htdocs\doyouhaobaby\DoYouHaoBaby\Resource\Seccode\Gif ）中一个图片地址
	 *
	 * @access protected
	 * @var image
	 */
	protected $_sImCodeFile='';

	/**
	 * 创建的验证码图像
	 *
	 * @access protected
	 * @var image
	 */
	protected $_oImCode=null;

	/**
	 * 创建的验证码图像的阴影< 内部使用 >
	 *
	 * @access protected
	 * @var image
	 */
	protected $_oImCodeShadow=null;

	/**
	 * 是否创建中文验证码
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bChineseCode=false;

	/**
	 * 字体背景颜色< 内部使用 >
	 *
	 * @access protected
	 * @var color
	 */
	protected $_oColor=null;

	/**
	 * 是否开启动画验证码
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bAnimator=false;

	/**
	 * 基于字体还是基于图片创建动画
	 *
	 * < 1：基于字体 2：基于图片 >
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nType=1;

	/**
	 * 是否添加背景干扰
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bNoise=false;

	/**
	 * 是否使用弧线干扰
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bCurve=false;

	/**
	 * 干扰字母
	 *
	 * @access protected
	 * @var string
	 */
	protected $_arrCodeSet='346789ABCDEFGHJKLMNPQRTUVWXY';

	/**
	 * 验证码干扰字体大小(px)
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nFontSize=25;

	/**
	 * 设置Seccode
	 *
	 * @access public
	 * @param int $nCode seccode代码
	 * @return void
	 */
	public function setCode($nCode){}

	/**
	 * 设置width
	 *
	 * @access public
	 * @param int $nWidth 宽度
	 * @return void
	 */
	public function setWidth($nWidth){}

	/**
	 * 设置height
	 *
	 * @access public
	 * @param int $nHeight  验证码高度
	 * @return void
	 */
	public function setHeight($nHeight){}

	/**
	 * 设置随机背景图形
	 *
	 * @access public
	 * @param bool $bValue
	 * @return void
	 */
	public function setAdulterate($bValue){}

	/**
	 * 设置TTF字体
	 *
	 * @access public
	 * @param bool $bValue
	 * @return void
	 */
	public function setTtf($bValue){}

	/**
	 * 设置随机倾斜度
	 *
	 * @access public
	 * @param bool $bValue
	 * @return void
	 */
	public function setTilt($bValue){}

	/**
	 * 设置随机颜色
	 *
	 * @access public
	 * @param bool $bValue
	 * @return void
	 */
	public function setColor($bValue){}

	/**
	 * 设置文字阴影
	 *
	 * @access public
	 * @param bool $bValue
	 * @return void
	 */
	public function setShadow($bValue){}

	/**
	 * 设置字体路径
	 *
	 * @access public
	 * @param string $sPath 字体路径
	 * @return void
	 */
	public function setFontPath($sPath){}

   /**
	 * 设置随机大小
	 *
	 * @access public
	 * @param bool $bSize 随机大小
	 * @return.void
	 */
	public function setSize($bSize){}

	/**
	 * 设置验证码相关数据目录
	 *
	 * @access public
	 * @param string $sDataPath 路径地址
	 * @return void
	 */
	public function setDataPath($sDataPath){}

	/**
	 * 设置验证码相关数据目录
	 *
	 * @access public
	 * @param bool $bChineseCode 是否为中文路径
	 * @return void
	 */
	public function setChineseCode($bChineseCode){}

	/**
	 * 设置是否开启动画验证码
	 *
	 * @access public
	 * @param bool $bAnimator
	 * @return void
	 */
	public function setAnimator($bAnimator){}

	/**
	 * 设置使用基于文字的验证码还是基于gif图片生成文字的验证码
	 *
	 * @access public
	 * @param int $nType 类型
	 * @return void
	 */
	public function setType($nType){}

	/**
	 * 设置添加背景干扰
	 *
	 * @access public
	 * @param bool $bNorise
	 * @return void
	 */
	public function setNorise($bNorise){}

	/**
	 * 设置添加弧线干扰
	 *
	 * @access public
	 * @param bool $bCurve 类型
	 * @return void
	 */
	public function setCurve($bCurve){}

	/**
	 * 设置验证码字体大小
	 *
	 * @access public
	 * @param int $nFontSize
	 * @return void
	 */
	public function setFontSize($nFontSize){}

	/**
	 * 设置干扰字母
	 *
	 * @access public
	 * @param array $arrCodeSet
	 * @return void
	 */
	public function setCodeSet($arrCodeSet){}

	/**
	 * 设置是否创建背景图像
	 *
	 * @access public
	 * @param bool $bBackground
	 * @return void
	 */
	public function setBackground($bBackground){}

	/**
	 * 设置是否从系统图片中创建背景图像
	 *
	 * @access public
	 * @param bool $bImageBackground
	 * @return void
	 */
	public function setImageBackground($bImageBackground){}

	/**
	 * 输出验证码
	 *
	 * @access public
	 * @return img
	 */
	public function display(){}

	/**
	 * 取得文件的扩展名
	 *
	 * @access protected
	 * @param $sFileName string 文件名
	 * @return string
	 */
	protected function fileExt($sFileName){}

	/**
	 * 生成图像
	 *
	 * @access protected
	 * @return images
	 */
	protected function image(){}

	/**
	 * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
	 *
	 * < @author 流水孟春 <cmpan(at)qq.com>
	 *   高中的数学公式:
	 *   正弦型函数解析式：y=Asin(ωx+φ)+b
	 *   各常数值对函数图像的影响：
	 *   - A：决定峰值（即纵向拉伸压缩的倍数）
	 *   - b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
	 *   - φ：决定波形与X轴位置关系或横向移动距离（左加右减）
	 *   - ω：决定周期（最小正周期T=2π/∣ω∣）>
	 *
	 * @access protected
	 * @return void
	 */
	protected function writeCurve_(){}

	/**
	 * 画杂点
	 *
	 * < 往图片上写不同颜色的字母或数字
	 *   @author 流水孟春 <cmpan(at)qq.com> >
	 *
	 * @access protected
	 * @return void
	 */
	protected function writeNoise_(){}

	/**
	 * 创建验证码的背景图像
	 *
	 * @access protected
	 * @return string
	 */
	protected function background(){}

	/**
	 * 创建随机背景
	 *
	 * @access protected
	 * @return void
	 */
	protected function adulterate(){}

	/**
	 * 基于字体创建验证码的文字
	 *
	 * @access protected
	 * @return void
	 */
	protected function ttfFont(){}

	/**
	 * 添加随机文字背景
	 *
	 * @access protected
	 * @return void
	 */
	protected function adulterateFont(){}

	/**
	 * 创建文字
	 *
	 * @access protected
	 * @return void
	 */
	protected function textFont(){}

	/**
	 * 创建Gif动画字体
	 *
	 * @access protected
	 * @return void
	 */
	protected function gifFont(){}

	/**
	 * 创建位图
	 *
	 * @access protectet
	 * @return images
	 */
	protected function bitmap(){}

}
