<?dyhb
class HasMap implements IDs,IteratorAggregate{

	/**
	 * 映射对
	 *
	 * @access private
	 * @var array
	 */
	private $_arrMappings=array();

	/**
	 * 严格模式
	 *
	 * @access private
	 * @var array
	 */
	private $_bStrictly=true;

	/**
	 * 若要获得迭代因子，通过getIterator方法实现
	 *
	 * @access public
	 * @return ArrayObject
	 */
	public function getIterator(){}

	/**
	 * 是否严格模式
	 *
	 * @access public
	 * @param  bool $bStrictly
	 * @return void
	 */
	public function __construct($bStrictly){}

	/**
	 * 设置一个图映射对
	 *
	 * @access public
	 * @param $Source mixed 源变量
	 * @param $Mapping mixed 映射变量
	 * @param $sMappingName='default' string 映射名称
	 * @return oldValue
	 */
	public function setItem($Source,$Mapping,$sMappingName='default'){}

	/**
	 * 获取一个图值
	 *
	 * @access public
	 * @param $Source mixed 源变量
	 * @param $sMappingName='default' string 映射名称
	 * @return mixed
	 */
	public function getItem($Source,$sMappingName='default'){}

	/**
	 * 设置列表元素
	 *
	 * < 返回修改之前的值 >
	 *
	 * @access public
	 * @param mixed $Index 索引
	 * @param mixed $Element 元素
	 * @return mixed
	 */
	public function set($Index,$Element){}

	/**
	 * 根据索引取得元素
	 *
	 * @access public
	 * @param mixed $Index 索引
	 * @return mixed
	 */
	public function get($Index){}

	/**
	 * 是否包含某个元素
	 *
	 * @access public
	 * @param mixed $Element  查找元素
	 * @return string
	 */
	public function hasOne($Element){}

	/**
	 * 判断元素是否为空
	 *
	 * @access public
	 * @return boolen
	 */
	public function isEmpty(){}

	/**
	 * 清除所有元素
	 *
	 * @access public
	 */
	public function clear(){}

	/**
	 * 从当前图映射表中查找源变量
	 *
	 * @access private
	 * @param $Source mixed 源变量
	 * @return int
	 */
	private function findSource($Source){}

}
