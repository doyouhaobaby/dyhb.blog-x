<?dyhb
class ModelRelationHasOne extends ModelRelationHasMany{

	/**
	 * 是否为一对一
	 *
	 * @access public
	 * @var bool
	 */
	public $_bOneToOne=true;

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
	public $_sOnSave='replace';

	/**
	 * 保存关联源数据
	 *
	 * @access public
	 * @param Model $oSource  源对象
	 * @param array $nRecursion 递归深度
	 * @return self
	 */
	public function onSourceSave(Model $oSource,$nRecursion){}

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
