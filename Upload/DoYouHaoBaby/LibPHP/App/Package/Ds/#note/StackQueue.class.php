<?dyhb
abstract class StackQueue{

	/**
	 * 链表中的元素
	 *
	 * @access private
	 * @var array
	 */
	protected $_arrElements=array();

	/**
	 * 允许的类型
	 *
	 * @access private
	 * @var array
	 */
	private $_arrTypes=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sType1 string 可用类型
	 * @return void
	 */
	public function __construct() {}

	/**
	 * 按索引删除元素
	 *
	 * @access public
	 * @param $nIdx int 元素索引
	 * @return void
	 */
	public function remove($nIdx){}

	/**
	 * 返回当前链表中元素的数量
	 *
	 * @access public
	 * @return int
	 */
	public function getLength(){}

	/**
	 * 链表是否为空
	 *
	 * @access public
	 * @return bool
	 */
	public function isEmpty(){}

	/**
	 * 检查 是否 符合 链表对类型的限制
	 *
	 * @access public
	 * @param $Item mixed 待检测的 元素
	 * @return bool
	 */
	public function isValidType($Item){}

	/**
	 * 加入
	 *
	 * @access public
	 * @param $Item mixed 元素
	 * @return void
	 */
	abstract public function in($Item);

	/**
	 * 取出
	 *
	 * @access public
	 * @return mixed
	 */
	abstract public functionout();

}
