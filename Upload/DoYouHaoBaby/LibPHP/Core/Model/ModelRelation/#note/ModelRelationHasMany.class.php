<?dyhb
class ModelRelationHasMany extends ModelRelation{

	/**
	 * 是否为一对一
	 *
	 * @access public
	 * @var bool
	 */
	public $_bOneToOne=false;

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
	public $_onDelete='cascade';

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
	public $_sOnSave  ='save';

	/**
	 * 初始化
	 *
	 * @access public
	 * @return  self
	 */
	public function init_(){}

	/**
	 * 注册关联信息
	 *
	 * @access public
	 * @param array $arrAssocInfo 关联信息
	 * @return self
	 */
	public function regCallback( array $arrAssocInfo){}

	/**
	 * 添加目标对象
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @param Model $oTarget 目标对象
	 * @return void
	 */
	public function addTarget(Model $oSource,Model $oTarget){}

	/**
	 * 保存关联源数据
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @param array $nRecursion 递归深度
	 * @return self
	 */
	public function onSourceSave(Model $oSource,$nRecursion){}

	/**
	 * 销毁源数据
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @return self
	 */
	public function onSourceDestroy(Model $oSource){}

	/**
	 * 直接添加一个关联对象
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @param Model $oTarget 目标对象
	 * @return self
	 */
	public function addRelatedObject(Model $oSource,Model $oTarget){}

}
