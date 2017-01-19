<?dyhb
/**
 * < 使用代码 >
 * < $oTree = new TreeCategory();
 *   $oTree->setNode(1, 0, '值1');
 *   $oTree->setNode(2, 1, '值2');
 *   $oTree->setNode(5, 3, '值5');
 *   $oTree->setNode(3, 0, '值3');
 *   $oTree->setNode(4, 2, '值4');
 *   $oTree->setNode(9, 4, '值9');
 *   $oTree->setNode(6, 2, '值6');
 *   $oTree->setNode(7, 2, '值7');
 *   $oTree->setNode(8, 3, '值8');
 *
 *   $arrCategory = $oTree->getChilds();
 *   foreach ($arrCategory as $sKey=>$nId){
 *
 *   echo $nId.$oTree->getLayer( $nId, '|-' ).$oTree->getValue( $nId )."<br/>";
 *   } >
 */
class TreeCategory {

	/**
	 * 节点label数据
	 *
	 * @var array
	 */
	public $_arrData=array();

	/**
	 * 节点父级对应
	 *
	 * @var array
	 */
	public $_arrCate=array();

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param array $arrNodes 节点数据
	 * @return void
	 */
	public function __construct($arrNodes=array()){}

	/**
	 * 设置节点
	 *
	 * @access public
	 * @param int $nId 当前节点键值
	 * @param int $nParent 父级节点的键值
	 * @param string $sValue 当前节点的label
	 * @return void
	 */
	public function setNode ($nId,$nParent,$sValue){}

	/**
	 * 递归取得节点的子树
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @return array
	 */
	public function getChildsTree($nId=0){}

	/**
	 * 递归取得节点的子树
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @return array
	 */
	public function getChilds($nId=0){}

	/**
	 * 取得所有子节点
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @return array
	 */
	public function getChild($nId){}

	/**
	 * 单线获取父节点
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @return array
	 */
	public function getNodeLever($nId){}

	/**
	 * 取得无限分类前缀Label
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @param string $sPreStr 无限分类前缀
	 * @return array
	 */
	public function getLayer($nId,$sPreStr='|-'){}

	/**
	 * 取得节点的Label值
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @return array
	 */
	public function getValue($nId){}

	/**
	 * 设置节点的Label值
	 *
	 * @access public
	 * @param int $nId 节点键值
	 * @param string $sValue 节点的Label 值
	 * @return oldValue
	 */
	public function setValue($nId,$sValue){}

}
