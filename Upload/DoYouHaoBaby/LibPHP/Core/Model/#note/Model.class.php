<?dyhb
class Model implements IModel,IModelCallback,ArrayAccess{

	/**
	 * 系统内置特殊格式（用于自动填充）
	 *
	 * @access const
	 * @var string
	 */
	const AUTOFILL_DATETIME='DATETIME';
	const AUTOFILL_TIMESTAMP='TIMESTAMP';
	const AUTOFILL_DATE='DATE_'; // 与系统函数date()冲突 ，所以我们在后面加了一个‘_’分割
	const AUTOFILL_TIME='TIME_'; // 与系统函数time()冲突

	/**
	 * 系统内置时间状态（用于自动验证和自动填充时间状态）
	 *
	 * @access const
	 * @var int
	 */
	const MODEL_ALL='ALL'; // 任何时间填充
	const MODEL_CREATE='CREATE'; // 创建的时候填充
	const MODEL_UPDATE='UPDATE'; // 更新的时候填充

	/**
	 * 用于系统自动验证的条件
	 *
	 * @access const
	 * @var string
	 */
	const EXISTS_TO_CHECKDATE='EXIST';// 存在字段即验证
	const MUST_TO_CHECKDATE='MUST';// 必须验证
	const VALUE_TO_CHECKDATE='NOTEMPTY';// 不为空即验证

	/**
	 * 用于系统自动填充的字段（时间专用）
	 *
	 * @access const
	 * @var string
	 */
	const FIELD_DATELINE='DATELINE'; // 任何时候用当前Linux时间戳进行填充
	const FIELD_CREATE_DATELINE='CREATE_DATELINE'; // 创建对象的时候使用当前Linux时间戳进行填充
	const FIELD_UPDATE_DATELINE='UPDATE_DATELINE'; // 更新对象的时候使用当前Linux时间戳进行填充

	/**
	 * 模型名字
	 *
	 * @access private
	 * @var string
	 */
	private $_sName;

	/**
	 * 模型是否出错
	 *
	 * @access protected
	 * @static
	 * @var bool
	 */
	protected  $_bIsError=false;

	/**
	 * 模型最后出现的错误消息
	 *
	 * @access protected
	 * @static
	 * @var string
	 */
	protected  $_sErrorMessage;

	/**
	 * 对象所有属性的值
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrProp;

	/**
	 * 当前 ActiveRecord 对象的类名称
	 *
	 * @access protected
	 * @var string
	 */
	protected $_sClassName;

	/**
	 * ActiveRecord 继承类使用的 Meta 对象
	 *
	 * @access public
	 * @static
	 * @var ActiveRecordMeta
	 */
	private static $_arrMeta;

	/**
	 * 指示对象的哪些属性已经做了修改
	 *
	 * @access private
	 * @var array
	 */
	private $_arrChangedProp=array();

	/**
	 * 对象的 ID
	 *
	 * < 如果对象的 ID 是由多个属性组成，则 $_id 是一个由多个属性值组成的名值对。>
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_id=false;

	/**
	 * 构造函数
	 *
	 * @access public
	 * @param string $sName 模型名字
	 * @param array|Iterator $Data包含数据的名值对
	 * @param int $nStruct=self::DYNAMIC 动态或静态结构模型
	 * @param string $sNamesStyle 名值对的键名风格
	 * @param boolean $bFromStorage 是否从存储器载入数据
	 */
	public function __construct($Data=null,$sNamesStyle=Db::PROP,$bFromStorage=FALSE,$sName=''){}

	/**
	 * 获得对象的 ID（既对象在数据库中的主键值）
	 *
	 * < 如果对象的 ID 是由多个属性组成，则 id()方法会返回一个数组。>
	 *
	 * @access public
	 * @param boolean $bCached 默认返回缓存值
	 * @return mixed
	 */
	public function id($bCached=true){ }

	/**
	 * 保存对象到数据库
	 *
	 * @access public
	 * @param int $nRecursion 保存操作递归到多少层
	 * @param string $sSaveMethod 保存对象的方法
	 * @return Model 连贯接口
	 */
	public function save($nRecursion=99,$sSaveMethod='save'){}

	/**
	 * 强制改变一个属性的值，忽略属性的 readonly 设置
	 *
	 * @access  public
	 * @param string $sPropName 要改变的属性名
	 * @param mixed $PropValue 属性的值
	 * @return Model 连贯接口
	 */
	public function changePropForce($sPropName, $PropValue){}

	/**
	 * 批量设置对象的属性值
	 *
	 * < 如果指定了 $AttrAccessible 参数，则会忽略 ActiveRecord 类的 AttrAccessible 和 AttrProtected 设置 >
	 *
	 * @access public
	 * @param array|Iterator $Prop 名值对数组
	 * @param string $sNamesStyle 键名是属性名还是字段名
	 * @param array|string $AttrAccessible 指定哪些属性允许设置
	 * @param boolean $bFromStorage 内部参数
	 * @param boolean $bIignoreReadonly 内部参数
	 * @return Model 连贯接口
	 */
	public function changeProp($Prop,$sNamesStyle=Db::PROP,$AttrAccessible=null,$bFromStorage=false,$bIignoreReadonly=false){}

	/**
	 * 魔法方法，实现对象属性值的读取
	 *
	 * @access public
	 * @param  string $sPropName  属性名字
	 * @return mixed
	 */
	public function &__get($sPropName){}

	/**
	 * 魔法方法，实现对象属性的设置
	 *
	 * @access public
	 * @param string $sPropName  属性名字
	 * @param mixed $Value 属性值
	 * @return void
	 */
	public function __set($sPropName,$Value){}

	/**
	 * 某些特殊情况下需要关闭自动填充
	 *
	 * @access public
	 * @param bool $bAutofill 是否进行自动填充
	 * @return boolean
	 */
	public function setAutofill($bAutofill=true){}

	/**
	 * 魔法方法，实现对 isset()的支持
	 *
	 * @access public
	 * @param string $sPropName 属性值
	 * @return boolean
	 */
	public function __isset($sPropName){}

	/**
	 * 魔法方法，用于调用行为插件为对象添加的方法
	 *
	 * @access public
	 * @param string $sMethod 方法名字
	 * @param array $arrArgs 参数名字
	 * @return mixed
	 */
	public function __call($sMethod,array $arrArgs){}

	/**
	 * ArrayAccess 接口方法
	 *
	 * @access public
	 * @param string $sPropName 属性名字
	 * @return boolean
	 */
	public function offsetExists($sPropName){}

	/**
	 * ArrayAccess 接口方法
	 *
	 * @access public
	 * @param string $sPropName 属性名
	 * @param mixed $Value 值
	 */
	public function offsetSet($sPropName, $Value){}

	/**
	 * ArrayAccess 接口方法
	 *
	 * @access public
	 * @param string $sPropName 属性值
	 * @return boolean
	 */
	public function offsetGet($sPropName){}

	/**
	 * ArrayAccess 接口方法
	 *
	 * @access public
	 * @param string $sPropName 属性值
	 */
	public function offsetUnset($sPropName){}

	/**
	 * 用于 Coll 的回调方法
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	static function collCallback_(){}

	/**
	 * 将多个 Model 对象转换为JSON字符串
	 *
	 * @access public
	 * @param array $arrObjects 对象列表
	 * @param int $nRecursion 递归深度
	 * @param string  $sNamesStyle 属性风格
	 *  - const FIELD='field'; // 字段
	 *  - const PROP ='prop'; // 属性
	 * @return string
	 */
	static function multiToJson(array $arrObjects,$nRecursion=99,$sNamesStyle=Db::PROP){}

	/**
	 * 调用 Model 对象的动态方法
	 *
	 * @access public
	 * @param string $sMethod 方法名字
	 * @return mixed
	 */
	protected function method_($sMethod){}

	/**
	 * 触发事件
	 *
	 * @access protected
	 * @param string $sEventName
	 * @return void
	 */
	protected function event_($sEventName){}

	/**
	 * 通过数据字的字段和映射获取表单的正确post名字
	 *
	 * @access protected
	 * @param string $sField 字段
	 * @return string
	 */
	protected function getPostName($sField){}

	/**
	 * 根据表单字段自动创建数据
	 *
	 * @access protected
	 * @return array
	 */
	protected function makePostData(){}

	/**
	 * 在数据库中创建对象
	 *
	 * @access protected
	 * @param int $nRecursion 递归深度
	 * @return void
	 */
	protected function create_($nRecursion=99){}

	/**
	 * 更新对象到数据库
	 *
	 * @access public
	 * @param int $nRecursion 递归深度
	 * @return void
	 */
	protected function update_($nRecursion=99){}

	/**
	* 替换数据库中的对象，如果不存在则创建新记录
	*
	* @access public
	* @param int $nRecursion 递归层数
	* @return void
	*/
	protected function replace_($nRecursion=99){}

	/**
	 * 对当前对象的属性进行自动填充
	 *
	 * @access public
	 * @param string $sMode 填充时间
	 *  - all 任何时候填充
	 *  - create 创建的时候填充
	 *  - update 更新的时候填充
	 * @return array
	 */
	protected function autofill_($sMode=self::MODEL_ALL){}

	/**
	 * 使用数据库中的字段属性对数据进行过滤（ 增强数据库安全性 ）
	 *
	 * < mark: 如果属性不是数据库中的字段信息，那么直接返回原值,同时是否转化还取决了全局DB_FILEDTYPE_CHECK的值 >
	 *
	 * @access public
	 * @param string $sProp 属性名字
	 * @param mixed $Data 属性数据
	 * @return array
	 */
	protected function dbFieldtypeCheck_($sProp,$Data){}

	/**
	 * 对数据进行验证（ 看是否满足 ）
	 *
	 * @access public
	 * @param string $sMode 验证时间
	 *  - all 任何时候验证
	 *  - create 创建的时候验证
	 *  - update 更新的时候验证
	 * @return array
	 */
	public function check_($sMode){}

	/**
	 * 返回当前对象的元信息对象
	 *
	 * @access public
	 * @return ModelMeta
	 */
	public function getMeta(){}

	/**
	 * 返回当前对象的入口表
	 *
	 * @access public
	 * @return DbTableEnter
	 */
	public function getTableEnter(){}

	/**
	 * 返回当前对象的Db实例
	 *
	 * @access public
	 * @return Db
	 */
	public function getDb(){}

	/**
	 * 返回当前对象的数据库表前缀
	 *
	 * @access public
	 * @return string
	 */
	public function getTablePrefix(){}

	/**
	 * 判断对象是否有特定的属性
	 *
	 * @access public
	 * @return boolean
	 */
	public function hasProp($sPropName){}

	/**
	 * 从数据库重新读取当前对象的属性，不影响关联的对象
	 *
	 * @access public
	 * @return void
	 */
	public function reload(){}

	/**
	 * 销毁对象对应的数据库记录
	 *
	 * @access public
	 * @return void
	 */
	public function destroy(){}

	/**
	 * 返回类型化以后的值
	 *
	 * < 用于数据入库前地安全检测 >
	 *
	 * @access protected
	 * @static
	 * @param mixed $Value 值
	 * @param string $sPtype 类型
	 * @return mixed
	 */
	static protected function typed_($Value,$sPtype){}

	/**
	 * 获得修改过的属性
	 *
	 * @access public
	 * @return array
	 */
	public function changes(){}

	/**
	 * 确认对象或指定的对象属性是否已经被修改
	 *
	 * @access public
	 * @param string|array $sPropsName 属性名
	 * @return boolean
	 */
	public function changed($sPropsName=null){}

	/**
	 * 将指定的属性设置为“脏”状态
	 *
	 * @access public
	 * @param string|array $sPropsName 属性名
	 * @return Model 连贯接口
	 */
	public function willChanged($sPropsName){}

	/**
	 * 清除所有属性或指定的“脏”状态
	 *
	 * @access public
	 * @param string|array $Props 属性名
	 * @return Model 连贯接口
	 */
	public function cleanChanges($Props=null){}

	/**
	 * 获得包含对象所有属性的数组
	 *
	 * @access public
	 * @param int $nRecursion  递归层数
	 * @param string $sNamesStyle 规则
	 *  -  const FIELD='field'; // 字段
	 *  -  const PROP ='prop'; // 属性
	 * @return array
	 */
	public function toArray($nRecursion=99,$sNamesStyle=Db::PROP){}

	/**
	 * 返回对象所有属性的 JSON 字符串
	 *
	 * @access public
	 * @param int $nRecursion 递归层数
	 * @param string $sNamesStyle 规则
	 *  -  const FIELD='field'; // 字段
	 *  -  const PROP ='prop'; // 属性
	 * @return string
	 */
	public function toJson($nRecursion=99, $sNamesStyle=Db::PROP){}

	/**
	 * 表入口SQL 执行是否出错
	 *
	 * access protected
	 * @return bool
	 */
	protected function isTableError(){}

	/**
	 * 返回表入口错误代码或信息
	 *
	 * @access protected
	 * @return string
	 */
	protected function getTableErrorMessage(){}

	/**
	 * __clone魔术方法
	 *
	 * < 返回的复制品没有 ID 值，因此在保存时将会创建一个新记录。
	 *   __clone()操作仅限当前对象的属性，对于关联的对象不会进行克隆。
	 *   同时，返回模型容器和视图容器复制品。
	 * >
	 *
	 * @access public
	 * @return void
	 */
	public function __clone(){ }

	/**
	 * 设置模型是否错误
	 *
	 * @access protected
	 * @param $bIsError  bool  验证是否错误
	 * @static
	 * @return oldValue
	 */
	protected function setIsError($bIsError=false){}

	/**
	 * 设置模型错误消息
	 *
	 * @access public
	 * @static
	 * @param $sErrorMessage string 验证错误消息
	 * @return oldValue
	 */
	protected function setErrorMessage($sErrorMessage=''){}

	/**
	 * 返回是否错误
	 *
	 * @access public
	 * @static
	 * @return bool
	 */
	public function isError(){}

	/**
	 * 返回错误消息
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public  function getErrorMessage(){}

	/**
	 * 获取模型名字
	 *
	 * @access public
	 * @return string
	 */
	public function getName(){}

	/**
	 * 设置模型名字
	 *
	 * @access public
	 * @param $sName string 模型名字
	 * @return oldVar
	 */
	public function setName($sName){}

	/**
	 * 事件回调：开始验证之前
	 */
	protected function beforeCheck_(){}
	protected function beforeCheckPost_(){}

	/**
	 * 事件回调：为创建记录进行的验证开始之前
	 */
	protected function beforeCheckOnCreate_(){}
	protected function beforeCheckOnCreatePost_(){}

	/**
	 * 事件回调：为创建记录进行的验证完成之后
	 */
	protected function afterCheckOnCreate_(){}
	protected function afterCheckOnCreatePost_(){}

	/**
	 * 事件回调：为更新记录进行的验证开始之前
	 */
	protected function beforeCheckOnUpdate_(){}
	protected function beforeCheckOnUpdatePost_(){}

	/**
	 * 事件回调：为更新记录进行的验证完成之后
	 */
	protected function afterCheckOnUpdate_(){}
	protected function afterCheckOnUpdatePost_(){}

	/**
	 * 事件回调：验证完成之后
	 */
	protected function afterCheck_(){}
	protected function afterCheckPost_(){}

	/**
	 * 事件回调：创建记录之后
	 *
	 * @access protected
	 * @return void
	 */
	protected function afterCreate_(){}
	protected function afterCreatePost_(){}

	/**
	 * 事件回调：更新记录之前
	 */
	protected function beforeUpdate_(){}
	protected function beforeUpdatePost_(){}

	/**
	 * 事件回调：更新记录之后
	 */
	protected function afterUpdate_(){}
	protected function afterUpdatePost_(){}

	/**
	 * 事件回调：删除记录之前
	 */
	protected function beforeDestroy_(){}
	protected function beforeDestroyPost_(){}

	/**
	 * 事件回调：删除记录之后
	 */
	protected function afterDestroy_(){}
	protected function afterDestroyPost_(){}

	/**
	 * 事件回调：创建记录之前
	 *
	 * @access protected
	 * @return void
	 */
	protected function beforeCreate_(){}
	protected function beforeCreatePost_(){}

	/**
	 * 事件回调：保存记录之后
	 *
	 * @access protected
	 * @return void
	 */
	protected function afterSave_(){}
	protected function afterSavePost_(){}

	/**
	 * 事件回调：保存记录之前
	 *
	 * @access protected
	 * @return void
	 */
	protected function beforeSave_(){}
	protected function beforeSavePost_(){}

	/**
	 * 事件回调：对象构造之后
	 *
	 * @access protected
	 * @return void
	 */
	protected function afterInit_(){ }
	protected function afterInitPost_(){ }

}
