<?dyhb
class ModelMeta{

	/**
	 * 扩展的方法
	 *
	 * @access public
	 * @var array of callbacks
	 */
	public $_arrMethods=array();

	/**
	 * 扩展的静态方法
	 *
	 * @access public
	 * @var array of callbacks
	 */
	public $_arrStaticMethods=array();

	/**
	 * 所有属性的默认值
	 *
	 * @access public
	 * @var array
	 */
	public $_arrDefaultProp=array();

	/**
	 * 事件钩子
	 *
	 * @access public
	 * @var array of callback
	 */
	public $_arrCallback=array();

	/**
	 * 允许使用mass-assignment方式赋值的属性
	 *
	 * < 如果指定了 AttrAccessible，则忽略 AttrProtected 的设置。>
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAttrAccessible=array();

	/**
	 * 拒绝使用mass-assignment方式赋值的属性
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAttrProtected=array();

	/**
	 * 属性到字段名的映射
	 *
	 * @access public
	 * @var array
	 */
	public $_arrPropToField=array();

	/**
	 * 字段名到属性的映射
	 *
	 * @access public
	 * @var array
	 */
	public $_arrFieldToProp=array();

	 /**
	 * 所有属性的元信息
	 *
	 * @access public
	 * @var array of properties meta
	 */
	public $_arrProp=array();

	/**
	 * 所有Model继承类的Meta对象
	 *
	 * @access protected
	 * @var array of ModelMeta
	 */
	protected static $_arrMeta=array();

	/**
	 * BELONGS_TO关联的source_key
	 *
	 * @access public
	 * @var array
	 */
	public $_arrBelongstoProp=array();

	/**
	 * Model之间的关联
	 *
	 * <!--代码示例-->
	 * < array('prop_name' => $relation) >
	 *
	 * < 如果关联已经初始化，则 $relation 是一个 ModelRelation 继承类实例。
	 *   否则 $relation 为 false。>
	 *
	 * @access public
	 * @var array ModelRelation
	 */
	public $_arrRelation=array();

	/**
	 * 用于指定继承类名称的字段名
	 *
	 * @access public
	 * @var string
	 */
	 public $_sInheritTypeField;

	/**
	 * ID 属性名
	 *
	 * @access public
	 * @var array
	 */
	public $_arrIdName;

	/**
	 * ID 属性包含多少个属性
	 *
	 * @access public
	 * @var int
	 */
	public $_nIdNameCount;

	/**
	 * 确定关联关系时，来源方使用哪一个键
	 *
	 * @access public
	 * @var string
	 */
	public $_sSourceKey;

	/**
	 * Model 的基础类
	 *
	 * @access public
	 * @var string
	 */
	public $_sInheritBaseClass;

	/**
	 * 表数据入口
	 *
	 * @access public
	 * @var DbTable
	 */
	public $_oTable;

	/**
	 * 数据表的元信息
	 *
	 * @access public
	 * @var array
	 */
	public $_arrTableMeta;

	/**
	 * 验证规则
	 *
	 * @access public
	 * @var array
	 */
	public $_arrCheck=array();

	/**
	 * 创建时要过滤的属性
	 *
	 * @access public
	 * @var array
	 */
	public $_arrCreateReject=array();

	/**
	 * 更新时要过滤的属性
	 *
	 * @access public
	 * @var array
	 */
	public $_arrUpdateReject=array();

	/**
	 * 自动填充的属性
	 *
	 * @access public
	 * @var array
	 */
	public $_arrAutofill=array();

	/**
	 * 表单映射字段的映射
	 *
	 * @access protected
	 * @var array
	 */
	public $_arrPostMapField=array();

	/**
	 * 行为插件对象
	 *
	 * @access protected
	 * @var array of ModelBehavior objects
	 */
	protected $_arrBehaviors=array();

	/**
	 * 可用的对象聚合类型
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_arrRelationTypes=array();

	/**
	 * 验证策略可用的选项
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_arrCheckOptions=array();

	/**
	 * 模型元最后出错的消息
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sLastErrorMessage='';

	/**
	 * 模型元最后验证是否错误
	 *
	 * @access protected
	 * @var bool
	 */
	protected $_bIsError=false;

	/**
	 * 用于初始化关联对象的参数映射(这样做的目的是满足系统关于属性命名规则)
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_arrMapConfigKeys=array();

	/**
	 * 构造函数
	 *
	 * @access protected
	 * @param string  $sclass 类名
	 */
	protected function __construct($sClass){}

	/**
	 * 获得指定指定 Model 继承类的元对象唯一实例
	 *
	 * @access   public
	 * @param string $sclass 类名字
	 * @return   ModelMeta
	 */
	static public  function instance($sClass){}

	/**
	 * 开启一个查询
	 *
	 * @access public
	 * @return DbSelect
	 */
	public function find(){}

	/**
	 * 开启一个查询，并根据提供的参数设置查询对象
	 *
	 * @access public
	 * @param array $arrArgs
	 * @return DbSelect
	 */
	public function findByArgs(array $arrArgs=array()){}

	/**
	 * 更新符合条件的对象
	 *
	 * @access public
	 * @param array $arrAttribs
	 * @return void
	 */
	public function updateWhere(array $arrAttribs){}

	/**
	 * 更新符合条件的记录
	 *
	 * @acccess public
	 * @return void
	 */
	public function updateDbWhere(){}

	/**
	 * 实例化符合条件的对象，并调用这些对象的 destroy()方法，返回被删除的对象数
	 *
	 * @access public
	 * @return int
	 */
	public function destroyWhere(){}

	/**
	 * 从数据库中直接删除符合条件的对象
	 *
	 * < 与destroyWhere()不同，deleteWhere()会直接从数据库中删除符合条件的记录。
	 *   而不是先把符合条件的对象查询出来再调用对象的destroy()方法进行删除。>
	 *
	 * < 因此，deleteWhere()速度更快，但无法处理对象间的关联关系。>
	 *
	 * @access public
	 * @return bool
	 */
	public function deleteWhere(){}

	/**
	 * 返回一个根据$Data数组构造的Model继承类实例
	 *
	 * @access public
	 * @return Model
	 */
	public function newObj(array $Data=null){}

	/**
	 * 添加一个对象关联
	 *
	 * < $sPropName参数指定使用Model对象的什么属性来引用关联的对象。>
	 * < 例如“文章”对象的comments属性引用了多个关联的“评论”对象。>
	 *
	 * < $sRelationType 指定了关联的类型，可以是 Db::BELONGS_TO、Db::HAS_MANY、Db::HAS_ONE或Db::MANY_TO_MANY。>
	 * < $arrConfig 指定了关联的属性，可用的属性有多项。>
	 *
	 * @access public
	 * @param  string $sPropName
	 * @param  string $sRelationType
	 * @param  array $arrConfig
	 * @return  ModelMeta
	 */
	public function addRelation($sPropName, $sRelationType, array $arrConfig){}

	/**
	 * 移除一个关联
	 *
	 * @access public
	 * @param string $sPropName  属性
	 * @return ModelMeta
	 */
	public function removeRelation($sPropName){}

	/**
	 * 获得对象的关联对象
	 *
	 * @access public
	 * @param Model $oObj  ActivedRecord对象
	 * @param string $sPropName   属性名字
	 * @return Model|ModelRelationColl
	 */
	public function relatedObj(Model $oObj, $sPropName){}

	/**
	 * 访问指定属性对应的关联
	 *
	 * @access public
	 * @param string $sPropName  属性名字
	 * @return ModelRelation
	 */
	public function relation($sPropName){}

	/**
	 * 添加一个属性
	 *
	 * @access public
	 * @param string  $sPropName  属性名
	 * @param array $arrConfig 属性值
	 * @return ModelMeta
	 */
	public function addProp($sPropName, array $arrConfig){}

	/**
	 * 检查是否绑定了指定的行为插件
	 *
	 * @access public
	 * @param $sName  string  插件名
	 * @return void
	 */
	public function hasBindBehavior($sName){}

	/**
	 * 绑定行为插件
	 *
	 * @param string|array $Behaviors  插件名字
	 * @param array $arrConfig 插件配置
	 * @return ModelMeta
	 */
	public function bindBehaviors($Behaviors, array $arrConfig=null){}

	/**
	 * 撤销与指定行为插件的绑定
	 *
	 * @access public
	 * @param string|array $Behaviors
	 * @return ModelMeta
	 */
	public function unbindBehaviors($Behaviors){}

	/**
	 * 添加一个动态方法
	 *
	 * @access public
	 * @param string $sMethodName
	 * @param callback $callback
	 * @param array $arrCustomParameters
	 * @return ModelMeta
	 */
	public function addDynamicMethod($sMethodName, $callback, array $arrCustomParameters=array()){ }

	/**
	 * 删除指定的动态方法
	 *
	 * @access public
	 * @param string $sMethodName
	 * @return ModelMeta
	 */
	public function removeDynamicMethod($sMethodName){}

	/**
	 * 添加一个静态方法
	 *
	 * @access public
	 * @param string $sMethodName
	 * @param callback $callback
	 * @param array $arrCustomParameters
	 * @return ModelMeta
	 */
	public function addStaticMethod($sMethodName, $callback, array $arrCustomParameters=array()){}

	/**
	 * 删除指定的静态方法
	 *
	 * @access public
	 * @param string $sMethodName
	 * @return DbModelMeta
	 */
	public function removeStaticMethod($sMethodName){}

	/**
	 * 设置属性的setter方法
	 *
	 * @access public
	 * @param  string  $sPropName  属性名字
	 * @param  callback $hCallback 回调
	 * @param  array $arrCustomParameters  自定义参数
	 * @return ModelMeta
	 */
	public function setPropSetter($sPropName, $hCallback, array $arrCustomParameters=array()){}

	/**
	 * 取消属性的setter方法
	 *
	 * @access public
	 * @param string $sPropName  属性名字
	 * @return ModelMeta
	 */
	public function unsetPropSetter($sPropName){ }

	/**
	 * 设置属性的getter方法
	 *
	 * @access public
	 * @param string $sName  属性名字
	 * @param callback $hCallback 回调
	 * @param array $arrCustomParameters  自定义参数
	 * @return ModelMeta
	 */
	public function setPropGetter($sName, $hCallback, array $arrCustomParameters=array()){}

	/**
	 * 取消属性的getter方法
	 *
	 * @access public
	 * @param string $sPropName  属性名字
	 * @return ModelMeta
	 */
	public function unsetPropGetter($sPropName){}

	/**
	 * 为指定事件添加处理方法
	 *
	 * @access public
	 * @param int $sEventType
	 * @param callback $Callback
	 * @param array $arrCustomParameters
	 * @return ModelMeta
	 */
	public function addEventHandler($sEventType, $Callback, array $arrCustomParameters=array()){}

	/**
	 * 删除指定事件的一个处理方法
	 *
	 * @access public
	 * @param int $sEventType
	 * @param callback $Callback
	 * @return ModelMeta
	 */
	public function removeEventHandler($sEventType, $Callback){}

	/**
	 * 初始化
	 *
	 * @access public
	 * @param string $sclass 类名
	 * @return void
	 */
	private function init_($sClass){}

	/**
	 * 准备验证策略
	 *
	 * @access protected
	 * @param array $arrPolicies 要解析的策略
	 * @param array $arrRef 用于 include 参考的策略
	 * @param boolean $bSetPolicy 是否指定验证策略
	 * @return void
	 */
	protected function prepareCheckRules_($arrPolicies, array $arrRef=array(), $bSetPolicy=true){}

	/**
	 * 根据数据表名称获得表数据入口对象
	 *
	 * @access protected
	 * @param string $sTableName  表名
	 * @param array $arrTableConfig 表配置
	 * @return DbTable
	 */
	protected function tableByName_($sTableName, array $arrTableConfig=array()){}

	/**
	 * 根据类名称获得表数据入口对象
	 *
	 * @access protected
	 * @param string $sTableClass 表类
	 * @param array $arrTableConfig 表配置
	 * @return DbTable
	 */
	protected function tableByClass_($sTableClass, array $arrTableConfig=array()){}

	/**
	 * 解析数据库连接信息
	 *
	 * @access protected
	 * @param string $sTableName 表名字
	 * @param array $arrTableConfig 表配置
	 * @param bool  $bByClass 是否根据类名字来获取表数据入口对象
	 * @return array
	 */
	protected function parseDsn($arrTableConfig, $sTableName,$bByClass=false){}

	/**
	 * 再次初始化
	 *
	 * < 避免因为关联到自身造成循环引用。>
	 *
	 * @access private
	 * @return void
	 */
	private function initInstance_(){}

	/**
	 * 获得修改过的属性
	 *
	 * @access public
	 * @return array
	 */
	public function changes(){}

	/**
	 * 对数据进行验证，返回所有未通过验证数据的错误信息
	 *
	 * @access public
	 * @param array $arrData 要验证的数据
	 * @param array|string $arrProps 指定仅仅验证哪些属性
	 * @param string $sMode 验证模式(update|create|all)
	 * @return array 所有没有通过验证的属性名称及验证规则
	 */
	public function check(array $arrData, $arrProps=null, $sMode='all'){}

	/**
	 * 根据验证因子验证字段
	 *
	 * @access private
	 * @param array $arrData 创建数据
	 * @param string $arrCheckInfo 验证规则
	 * @return boolean
	 */
	 private function checkField_($arrData,$arrCheckInfo){}

	/**
	 * 为指定属性添加一个验证规则
	 *
	 * @access public
	 * @param string $sPropName
	 * @param mixed $Check
	 * @return ModelMeta
	 */
	public function addCheck($sPropName, $Check){}

	/**
	 * 取得指定属性的所有验证规则
	 *
	 * @access public
	 * @param string $sPropName
	 * @return array
	 */
	public function propCheck($sPropName){}

	/**
	 * 取得所有属性的所有验证规则
	 *
	 * @access public
	 * @return array
	 */
	public function allCheck(){}

	/**
	 * 清除指定属性的所有验证规则
	 *
	 * @access public
	 * @param string $sPropName
	 * @return ModelMeta
	 */
	public function removePropCheck($sPropName){}

	/**
	 * 清除所有属性的所有验证规则
	 *
	 * @access public
	 * @return ModelMeta
	 */
	public function removeAllCheck(){}

	/**
	 * 调用Model继承类定义的自定义静态方法
	 *
	 * @access public
	 * @param string $sMethodName
	 * @param array $arrArgs
	 * @return mixed
	 */
	public function __call($sMethodName, array $arrArgs){}

	/**
	 * 设置验证是否错误
	 *
	 * @access public
	 * @param bool $bIsError
	 * @return mixed
	 */
	public function setIsError($bIsError=false ){}

	/**
	 * 设置当前错误消息
	 *
	 * @access public
	 * @param string $sErrorMessage
	 * @return oldValue
	 */
	public function setErrorMessage($sErrorMessage='' ){}

	/**
	 * 取得验证是否出错
	 *
	 * @access public
	 * @return bool
	 */
	public function isError(){}

	/**
	 * 取得错误消息
	 *
	 * @access public
	 * @return string
	 */
	public  function getErrorMessage(){}

}
