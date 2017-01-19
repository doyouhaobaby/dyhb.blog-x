<?dyhb
abstract class DbFactory {

	/**
	 * 创建数据库连接对象
	 *
	 * @access public
	 * @return DbConnect
	 */
	abstract public function createConnect();

	/**
	 * 创建一个数据库 记录集
	 *
	 * @access public
	 * @param $oConnect DbConnect 数据库连接
	 * @return DbRecordSet
	 */
	abstract public function createRecordSet(DbConnect $oConnect);

}
