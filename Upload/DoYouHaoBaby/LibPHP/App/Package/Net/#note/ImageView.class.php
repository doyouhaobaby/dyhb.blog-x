<?dyhb
class ImageView{

	/**
	 * DOMDocment
	 *
	 * @access protected
	 * @var object
	 */
	protected $_oDOMDocment=null;

	/**
	 * 根节点
	 *
	 * @access protected
	 * @var object
	 */
	protected $_oRoot=null;

	/**
	 * 自动播放时间节点
	 *
	 * @access protected
	 * @var object
	 */
	protected $_oAutoPlayTime=null;

	/**
	 * 自动播放时间
	 *
	 * @access protected
	 * @var int
	 */
	protected $_nAutoPlayTime=3;

	/**
	 * 文档数据
	 *
	 * @access protected
	 * @var array
	 */
	 protected $_arrDatas =array();

	/**
	 * 是否直接输出
	 *
	 * @access protected
	 * @var bool
	 */
	 protected $_bEach =false;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $arrData array 节点数据
	 * @param $nAutoPlayTime int 自动播放时间
	 * @param bool $bEach 是否直接输出
	 * @return void
	 */
	public function __construct($arrData=array(),$nAutoPlayTime=3,$bEach=false){}

	/**
	 * 生成XML 文件
	 *
	 * @access public
	 * @return mixed
	 */
	public function RUN(){}

	/**
	 * 创建头文件识别
	 *
	 * @access public
	 * @return string
	 */
	public function createHeader(){}

	/**
	 * 创建根节点
	 *
	 * @access public
	 * @return string
	 */
	public function createRoot(){}

	/**
	 * 创建播放时间节点
	 *
	 * @access public
	 * @return void
	 */
	public function createAutoPlayTime(){}

	/**
	 * 创建文档数据
	 *
	 * @access public
	 * @return void
	 */
	public function createData(){}

	/**
	 * 保存文档
	 *
	 * @access public
	 * @return xml
	 */
	public function saveDoc(){}

	/**
	 * 设置是否输出
	 *
	 * @access public
	 * @param $bEach bool  是否自动输出
	 * @return oldValue
	 */
	public function setEach($bEach=true){}

	/**
	 * 设置节点数据
	 *
	 * @access public
	 * @param array $arrDatas 节点数据
	 * @return oldValue
	 */
	public function setDatas($arrDatas){}

	/**
	 * 设置自动播放时间
	 *
	 * @access public
	 * @param int $nAutoPlayTime 自动播放时间
	 * @return oldValue
	 */
	 public function setAutoPlayTime($nAutoPlayTime){}

	/**
	 * 返回是否输出
	 *
	 * @access public
	 * @return bool
	 */
	public function getEach(){}

	/**
	 * 返回节点数据
	 *
	 * @access public
	 * @return array
	 */
	public function getDatas(){}

	/**
	 * 返回自动播放时间
	 *
	 * @access public
	 * @return int
	 */
	public function getAutoPlayTime(){}

}
