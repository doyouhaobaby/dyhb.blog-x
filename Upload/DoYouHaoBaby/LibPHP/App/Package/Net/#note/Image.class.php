<?dyhb
class Image{

	/**
	 * 取得图像属性
	 *
	 * @access public
	 * @static
	 * @param $sImagesPath  string  文件地址
	 * @return array | bool
	 */
	static function getImageInfo($sImagesPath){}

	/**
	 * 生成缩略图
	 *
	 * @access pubic
	 * @static
	 * @param string $sImage 原图
	 * @param string $sType 图像格式
	 * @param string $sThumbName 缩略图文件名
	 * @param string $nMaxWidth 宽度
	 * @param string $nMaxHeight 高度
	 * @param boolean $bInterlace 启用隔行扫描
	 * @return image
	 */
	static function thumb($sImage,$sThumbName,$sType='',$nMaxWidth=200,$nMaxHeight=50,$bInterlace=true){}

	/**
	 * GD缩略图
	 *
	 * @access public
	 * @static
	 * @param string $sTargetFile 图片URL路径
	 * @param string $nThumbWidth 图片宽
	 * @param string $nThumbHeight 图片高
	 * @return image
	 */
	static function thumbGd($sTargetFile, $nThumbWidth, $nThumbHeight){}

	/**
	 * 获得服务器上的 GD 版本
	 *
	 * @access public
	 * @return int 可能的值为0,1,2
	 */
	static function gdVersion(){}

	/**
	 * PHP图片水印 (水印支持图片或文字)支持中文
	 *
	 * <!-- 用法 -->
	 * < imageWaterMark('./apntc.gif', 1,  array('type' => 'img', 'path'=>'','offset'=>0)); 添加水印图片
	 *   imageWaterMark('./apntc.gif', 1, array('type' => 'text', 'content' => '', 'textColor' => '', 'textFont' => '','textPath'='','offset'=>0));  添加水印文字 >
	 *
	 * < 图片水印位置：有10种状态，1-9以外为随机位置；
	 *   1为顶端居左，2为顶端居中，3为顶端居右；
	 *   4为中部居左，5为中部居中，6为中部居右；
	 *   7为底端居左，8为底端居中，9为底端居右；>
	 *
	 * < 水印的参数(图片和文字)：
	 *
	 *   图片：array('type' => 'img', 'path'=>'')
	 *      type: 水印类型为图像
	 *      path: 图像地址
	 *      offset: 开始位置
	 *
	 *   文字：array('type' => 'text', 'content' => '', 'textColor' => '', 'textFont' => '')
	 *      type: 水印类型为文字
	 *      content： 文字内容
	 *      textColor: 文字颜色
	 *      textFont：文字字体
	 *      textPath: 字体路径
	 *      offset: 开始位置 >
	 *
	 * @access pubic
	 * @static
	 * @param $sBackgroundPath string 背景图像类型
	 * @param $nWaterPos int 图片位置
	 * @param $arrWaterArgs array  水印参数
	 * @return mixed 返回TRUE或错误信息，只有当返回TRUE时，操作才是成功的
	 */
	static function imageWaterMark($sBackgroundPath,$nWaterPos=0,$arrWaterArgs,$bDeleteBackgroupPath=false){}

	/**
	 * 按照比例改变图片大小
	 *
	 * @access public
	 * @static
	 * @param string $sImgPath 图片路径
	 * @param int $nMaxWidth 最大缩放宽
	 * @param int $nMaxHeight 最大缩放高
	 * @return array
	 */
	static function changeImgSize ($sImgPath,$nMaxWidth,$nMaxHeight){}

	/**
	 * 输出图像
	 *
	 * @access public
	 * @static
	 * @param image $oImage 图像
	 * @param string $sType 文件类型
	 * @param string $sFilename  文件名
	 * @return image
	 */
	static function output($oImage,$sType='png',$sFilename=''){}

	/**
	 * 图像裁剪
	 *
	 * @access pubic
	 * @static
	 * @param string $sImageFile 原图地址
	 * @param string $sTargetFile 目标地址
	 * @param string $nWidth 裁剪后的图像宽度
	 * @param string $nHeight 裁剪后的图像高度
	 * @param string $bCut 是否裁剪
	 * @param string $sCorner 圆角图片地址
	 * @param int $nCornerRadius 圆角度数
	 * @param int $nAngle 倾斜度
	 * @return 返回TRUE 才正确，否则返回错误消息
	 */
	static public function resizeImage($sImageFile,$sTargetFile='',$nWidth=80,$nHeight=80,$bCut=true,$sCorner='',$nCornerRadius=6,$nAngle=0){}

}
