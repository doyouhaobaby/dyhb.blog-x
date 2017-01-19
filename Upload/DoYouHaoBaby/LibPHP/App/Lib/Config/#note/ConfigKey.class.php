<?dyhb
class ConfigKey{

	/**
	 * 配置储存路径
	 *
	 * @access private
	 * @var
	 */
	private $_sStoreDirectory;

	/**
	 * 数据项
	 *
	 * @access private
	 * @var
	 */
	private $_arrItems=array();

	/**
	 * 使用更新配置
	 *
	 * @access private
	 * @var
	 */
	private $_bUpdate=false;

	/**
	 * 配置文件名字
	 *
	 * @access private
	 * @var
	 */
	const ITEM_DATA_FILENAME='Config.php';

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $sStoreDirectory string 存储目录
	 * @return void
	 */
	public function __construct($sStoreDirectory){}

	/**
	 * 析构函数
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct(){}

	/**
	 * 返回键名称
	 *
	 * @access public
	 * @return string
	 */
	public function getName(){}

	/**
	 * 保存键
	 *
	 * @access public
	 * @return bool
	 */
	public function save(){}

	/**
	 * 删除键
	 *
	 * @access public
	 * @return bool
	 */
	public function delete(){}

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
	 * 以数组形式返回所有Item
	 *
	 * @access public
	 * @return array
	 */
	public function getItems(){}

	/**
	 * 将所有Item 设置为 以数组形式传入的参数
	 *
	 * @access public
	 * @param $arrItems array
	 * @return void
	 */
	public function setItems(array $arrItems){}

	/**
	 * 清空所有数据库项的值
	 *
	 * @access public
	 * @param $arrItems array
	 * @return void
	 */
	public function clearItems(){}

}
