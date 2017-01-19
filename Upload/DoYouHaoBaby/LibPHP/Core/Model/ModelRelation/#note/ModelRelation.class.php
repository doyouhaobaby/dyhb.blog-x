<?dyhb
class ModelRelation {

	/**
	 * 确定关联关系时，来源方使用哪一个键
	 *
	 * @access public
	 * @var string
	 */
	public $_sSourceKey;

	/**
	 * 确定关联关系时，目标方使用哪一个键
	 *
	 * @access public
	 * @var string
	 */
	public $_sTargetKey;

	/**
	 * 指示是否读取目标数据
	 *
	 * < skip|false - 不读取
	 *   all|true - 读取所有关联数据
	 *   整数 - 仅读取指定个数的目标数据
	 *   数组 - 由 count 和 offset 组成的数组，指定读取目标数据的起始位置和个数 >
	 *
	 * < 对于所有类型的关联，on_find 的默认值都是 all >
	 *
	 * @access public
	 * @var string|int|array
	 */
	public $_onFind='all';

	/**
	 * 查询目标数据时要使用的查询条件
	 *
	 * @access public
	 * @var array|string
	 */
	public $_onFindWhere=null;

	/**
	 * 查询目标数据时的排序
	 *
	 * @access public
	 * @var string
	 */
	public $_sOnFindOrder=null;

	/**
	 * 查询目标数据时要查询哪些属性
	 *
	 * @access public
	 * @var array|string
	 */
	public $_onFindKeys='*';

	/**
	 * 指示在来源数据时，如何处理相关的目标数据
	 *
	 * < cascade|true - 删除关联的目标数据
	 *   set_null - 将目标数据的 target_key 键设置为 NULL
	 *   set_value - 将目标数据的 target_key 键设置为指定的值
	 *   skip|false - 不处理关联记录
	 *   reject - 拒绝对来源数据的删除 >
	 *
	 * < 对于 has many 和 has one 关联，默认值则是 cascade
	 *   对于 belongs to 和 many to many 关联，on_delete 设置固定为 skip >
	 *
	 * @access public
	 * @var string|boolean
	 */
	public $_onDelete='skip';

	/**
	 * 如果 on_delete 为 set_value，则通过 on_delete_set_value 指定要填充的值
	 *
	 * @access public
	 * @var mixed
	 */
	public $_onDeleteSetValue=null;

	/**
	 * 指示保存来源数据时，是否保存关联的目标数据
	 *
	 * < save|true - 根据目标数据是否有 ID 或主键值来决定是创建新的目标数据还是更新已有的目标数据
	 *   create - 强制创建新的目标数据
	 *   update - 强制更新已有的目标数据
	 *   replace - 尝试替换已有的目标数据，如果不存在则新建
	 *   skip|false - 保存来源数据时，不保存目标数据
	 *   only_create - 仅仅保存需要新建的目标数据
	 *   only_update - 仅仅保存需要更新的目标数据 >
	 *
	 * < 对于 many to many 关联，on_save 的默认值是 skip >
	 * < 对于 has many 关联，on_save 的默认值是 save >
	 * < 对于 has on 关联，on_save 的默认值是 replace >
	 * < 对于 belongs to 关联，on_save 设置固定为 skip >
	 *
	 * @access public
	 * @var string
	 */
	public $_sOnSave='skip';

	/**
	 * 查询多对多关联时，中间数据使用哪一个键关联到来源方
	 *
	 * @access public
	 * @var string
	 */
	public $_sMidSourceKey;

	/**
	 * 查询多对多关联时，中间数据使用哪一个键关联到目标方
	 *
	 * @access public
	 * @var string
	 */
	public $_sMidTargetKey;

	/**
	 * 查询多对多关联时，是否也要把中间数据放到结果中
	 *
	 * < 如果 mid_on_find_keys 为 null，则不查询。如果为特定属性名，
	 *   则会根据 mid_mapping_to 将中间数据指定为目标数据的一个键。>
	 *
	 * @access public
	 * @var array|string
	 */
	public $_midOnFindKeys=null;

	/**
	 * 查询多对多关联时，中间数据要指定到目标数据的哪一个键
	 *
	 * @access public
	 * @var string
	 */
	public $_sMidMappingTo;

	 /**
	 * 当 enabled 为 false 时，表数据入口的任何操作都不会处理该关联
	 *
	 * < enabled 的优先级高于 linkRead、linkCreate、linkUpdate 和 linkRemove。>
	 *
	 * @access public
	 * @var boolean
	 */
	public $_bEnabled=true;

	/**
	 * 关联的类型
	 *
	 * @access public
	 * @var string
	 */
	public $_sType;

	/**
	 * 指示关联是否已经初始化
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_bInited=false;

	/**
	 * 目标数据映射到来源数据的哪一个键，同时 mapping_name 也是关联的名字
	 *
	 * @access public
	 * @var string
	 */
	public $_sMappingName;

	/**
	 * 源Key别名
	 *
	 * @access public
	 * @var string
	 */
	public $_sSourceKeyAlias;

	/**
	 * 目标key别名
	 *
	 * @access public
	 * @var string
	 */
	public $_sTargetKeyAlias;

	/**
	 * 关联到哪一个 Model 类
	 *
	 * @access public
	 * @var ModelMeta
	 */
	public $_oTargetMeta;

	/**
	 * 关联中的来源对象
	 *
	 * @access public
	 * @var ModelMeta
	 */
	public $_oSourceMeta;

	/**
	 * 封装中间表数据的 Model 元信息对象
	 *
	 * @access public
	 * @var ModelMeta
	 */
	public $_oMidMeta;

	/**
	 * 封装中间表的表数据入口对象
	 *
	 * @access public
	 * @var DbTable
	 */
	public $_oMidTable;

	/**
	 * 目标对象
	 *
	 * @access public
	 * @var string
	 */
	public $_sTargetClass;

	/**
	 * 指示关联两个数据时，是一对一关联还是一对多关联
	 *
	 * @access public
	 * @var boolean
	 */
	public $_bOneToOne=false;

	/**
	 * 初始化参数
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrInitConfig;

	/**
	 * 用于初始化关联对象的参数
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_arrInitConfigKeys=array();

	/**
	 * 构造函数
	 *
	 * @access protected
	 * @param string $sType 格式
	 * @param array $arrConfig 配置数据
	 * @param ModelMeta $oSourceMeta 元对象
	 * @return ModelRelation
	 */
	protected function __construct($sType,array $arrConfig,ModelMeta $oSourceMeta){}

	/**
	 * 创建一个关联对象
	 *
	 * @access public
	 * @param   string   $sType
	 * @param	array   $arrConfig
	 * @param	ModelMeta $oSourceMeta
	 * @return  ModelRelation
	 */
	static function create($sType,array $arrConfig,ModelMeta $oSourceMeta){}

	/**
	 * 初始化关联
	 *
	 * @access public
	 * @return ModelRelation
	 */
	public function init_(){}

	/**
	 * 注册回调方法
	 *
	 * @access public
	 * @param array $arrAssocInfo 回调
	 */
	public function regCallback( array $arrAssocInfo){}

	/**
	 * 为写入数据库Mysql前的数据添加转义字符
	 *
	 * @access protected
	 * @param string $sValue 要处理的数据
	 * @param DbConnect $oConnect 数据库连接
	 * @return string
	 */
	protected function qualifyStr(DbConnect $oConnect ,$sValue){}

	/**
	 * 获得完全限定名 < 字段 >
	 *
	 * @access protected
	 * @param string $sValue 要处理的字段
	 * @param DbConnect $oConnect 数据库连接
	 * @return string
	 */
	protected function qualifyId(DbConnect $oConnect ,$sValue){}

}
