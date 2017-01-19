<?dyhb
class Config{

	/**
	 * 根键
	 *
	 * @access private
	 * @var ConfigKey
	 */
	private $_oKey;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sRootStoreDirectory string 根存储目录
	 * @return void
	 */
	public function __construct($sStoreDirectory){}

	/**
	 * 设置一个数据项
	 *
	 * @access public
	 * @param $sItemName string 数据项名称
	 * @param $Data mixed 数据
	 * @return oldValue
	 */
	public function setItem($sItemName,$Data){}

	/**
	 * 取得数据项数据
	 *
	 * @access public
	 * @param $sItemName string 数据项名称
	 * @return mixed
	 */
	public function getItem($sItemName){}

	/**
	 * 删除数据项
	 *
	 * @access public
	 * @param $sItemName string 数据项名称
	 * @return bool
	 */
	public function deleteItem($sItemName){}

	/**
	 * 返回一个数据项是否存在
	 *
	 * @access public
	 * @param $sItemName string 数据项名称
	 * @return bool
	 */
	public function hasItem($sItemName){}

	/**
	 * 返回数据项的值（数组）
	 *
	 * @access public
	 * @return Iterator
	 */
	public function getItems(){}

	/**
	 * 清空数据项的值
	 *
	 * @access public
	 * @return Iterator
	 */
	public function cleanItems(){}

}
