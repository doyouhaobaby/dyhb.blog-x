<?php
abstract class DbRecordSet{

	/**
	 * 指示返回结果集的形式
	 *
	 * @access public
	 * @var const && int
	 */
	public $_nFetchMode;

	/**
	 * 指示是否将查询结果中的字段名转换为全小写
	 *
	 * @access public
	 * @var boolean
	 */
	public $_bResultFieldNameLower=false;

	/**
	 * 结果内容
	 *
	 * @access private
	 * @var array
	 */
	private $_arrData=array();

	/**
	 * 结果行数
	 *
	 * @access private
	 * @var int
	 */
	protected $_nCount=-1;

	/**
	 * 数据库连接
	 *
	 * @access private
	 * @var DbConnect
	 */
	protected $_oConnect;

	/**
	 * Query 传入的SQL
	 *
	 * @access private
	 * @var DbSqlSelect,string
	 */
	protected $_runSelectSql='';

	/**
	 * 查询结果句柄
	 *
	 * @access private
	 * @var handle,null
	 */
	private $_hResult=null;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param $oConnect DbConnect 数据库连接
	 * @param const $nFetchMode 查询结果方式
	 * @return void
	 */
	public function __construct(DbConnect $oConnect,$nFetchMode=Db::FETCH_MODE_ARRAY){}

	/**
	 * 析构函数
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct(){}

	/**
	 * 设置要使用的数据库访问对象
	 *
	 * @param DbConnect $oConnect 数据库连接对象
	 * @return DbRecordSelect
	 */
	public function setConnect(DbConnect $oConnect){}

	/**
	 * 取回 数据库连接对象
	 *
	 * @access public
	 * @return DbConnect
	 */
	public function getConnect(){}

	/**
	 * 指示句柄是否有效
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid(){}

	/**
	 * 释放句柄
	 *
	 * @access public
	 * @return void
	 */
	abstract public  function free();

	/**
	 * 重置整个查询对象或指定部分
	 *
	 * @access public
	 * @param string $sOption 指定部分
	 * @return DbRecordSet
	 */
	public function reset($sOption=null){}

	/**
	 * 查询
	 *
	 * @access public
	 * @param $oSql DbSqlSelect,string SQL 查询语句
	 * @return
	 */
	public function query($Sql){}

	/**
	 * 设置查询结果句柄
	 *
	 * @access public
	 * @param $hRes handle 查询结果句柄
	 * @return oldValue
	 */
	public function setQueryResultHandle($hRes){}

	/**
	 * 返回查询结果句柄
	 *
	 * @access public
	 * @return handle,null
	 */
	public function getQueryResultHandle(){}

	/**
	 * 取回结果行数
	 *
	 * @access public
	 * @return int
	 */
	abstract public function getCount();

	/**
	 * 返回一行记录
	 *
	 * @access public
	 * @return array,null
	 */
	abstract public function fetch();

	/**
	 * 获取一条记录的某个字段值
	 *
	 * @access public
	 * @param string $sColumn 字段名 user_name,user_password
	 * @param string $sSpea 字段数据间隔符号
	 * @return mixed
	 */
	public function getColumn($sColumn,$sSepa='-'){}

	/**
	 * 返回指定行的内容
	 *
	 * @access public
	 * @param $nRow=-1 int 行数
	 * @return array,null
	 */
	public function getRow($nRow=-1){}

	/**
	 * 取得所有 行
	 *
	 * @access public
	 * @return array
	 */
	public function getAllRows(){}

	/**
	 * 从查询句柄提取记录集，并返回包含每行指定列数据的数组，如果 $col 为 0，则返回第一列
	 *
	 * @access public
	 * @param int $nCol 列
	 * @return array
	 */
	public function fetchCol($nCol=0){}

	/**
	 * 返回记录集和指定字段的值集合，以及以该字段值作为索引的结果集
	 *
	 * < 假设数据表 posts 有字段 post_id 和 title，并且包含下列数据：>
	 *
	 * <!-- 例子 -->
	 *
	 * < +---------+--------------------------+
	 *   | post_id | title                    |
	 *   +---------+--------------------------+
	 *   |     1 | It's live                  |
	 *   +---------+--------------------------+
	 *   |      2 | DoYouHaoBaby Recipes      |
	 *   +---------+--------------------------+
	 *   |     7 | DoYouHaoBaby User manual   |
	 *   +---------+--------------------------+
	 *   |    15 | DoYouHaoBaby Quickstart    |
	 *   +---------+--------------------------+ >
	 *
	 * < 现在我们查询 posts 表的数据，并以 post_id 的值为结果集的索引值：>
	 *
	 * < 用法:
	 *
	 *   $sSql="SELECT * FROM posts";
	 *   $hHandle=$dbo->execute($sSql);
	 *
	 *   $arrFieldsValue=array();
	 *   $arrRef=array();
	 *   $rowset=$hHandle->fetchAllRefby(array('post_id'), $arrFieldsValue, $arrRef); >
	 *
	 * < 上述代码执行后，$rowset 包含 posts 表中的全部 4 条记录。
	 *   最后，$fields_value 和 $ref 是如下形式的数组：>
	 *
	 * <
	 *   $arrFieldsValue=array(
	 *	 'post_id'=> array(1, 2, 7, 15),
	 *);
	 * >
	 *
	 * <
	 *   $arrRef=array(
	 *   'post_id'=> array(
	 *    1=> & array(array(...)),
	 *    2=> & array(array(...), array(...)),
	 *    7=> & array(array(...), array(...)),
	 *    15=> & array(array(...), array(...), array(...))
	 *  ),
	 *);
	 * >
	 *
	 * < $arrRef 用 post_id 字段值作为索引值，并且指向 $rowset 中 post_id 值相同的记录。>
	 * < 由于是以引用方式构造的 $ref 数组，因此并不会占用双倍内存。>
	 *
	 * @access public
	 * @param array $arrFields 字段
	 * @param array $arrFieldsValue 字段值
	 * @param array $arrRef 字段为引用的结果值
	 * @param boolean $bCleanUp 是否释放结果
	 * @return array
	 */
	public function fetchAllRefby(array $arrFields,&$arrFieldsValue,&$arrRef,$bCleanUp){}

	/**
	 * 以对象方式返回数据
	 *
	 * < 如果设置了return_first为true，直接返回单个对象，否则返回对象集合 >
	 *
	 * @access public
	 * @param string $sClassName 类名字
	 * @param boolean $bReturnFirst 是否返回单个对象
	 * @return mixed
	 */
	public function fetchObject($sClassName,$bReturnFirst=false){}

}
