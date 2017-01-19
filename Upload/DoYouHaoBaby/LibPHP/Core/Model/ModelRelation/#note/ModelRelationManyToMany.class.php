<?dyhb
class ModelRelationManyToMany extends ModelRelation{

	/**
	 * 是否为一对一
	 *
	 * @access public
	 * @var bool
	 */
	public $_bOneToOne=false;

	/**
	 * 初始化
	 *
	 * @access public
	 * @return self
	 */
	public function init_(){}

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
	 * 销毁关联源数据
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @param array $nRecursion 递归深度
	 * @return self
	 */
	public function onSourceDestroy(Model $oSource){}

	/**
	 * 添加和一个对象的关联关系
	 *
	 * @access public
	 * @param Model $oSource 源对象
	 * @param Model $oTarget 目标对象
	 * @return ModelRelationManyToMany
	 */
	public function bindRelatedObject(Model $oSource,Model $oTarget){}

	/**
	 * 解除和一个对象的关联关系
	 *
	 * @access public
	 * @param Model $oSource
	 * @param Model $oTarget
	 * @return ModelRelationManyToMany
	 */
	public function unbindRelatedObject(Model $oSource,Model $oTarget){}

	/**
	 * 更新对象间的关联关系
	 *
	 * @access protected
	 * @param Model $oSource
	 * @param int $nRecursion
	 * @return ModelRelation
	 */
	protected function updateRelation_(Model $oSource,$nRecursion=1){}

	/**
	 * 使用 Model 来操作中间表，并更新对象之间的 many_to_many 关系
	 *
	 * @access protected
	 * @param Model $oSource
	 * @param array|Coll $arrTargets
	 * @param int $nRecursion
	 * @return ModelRelationManyToMany
	 */
	protected function updateRelationByMeta_(Model $oSource,$arrTargets,$nRecursion){}

	/**
	 * 使用表数据入口来操作中间表，并更新对象之间的 many_to_many 关系
	 *
	 * @access protected
	 * @param Model $oSource
	 * @param array|Coll $arrTargets
	 * @param int $nRecursion 递归
	 * @return ModelRelationManyToMany
	 */
	 protected function updateRelationByTable_(Model $oSource,$arrTargets,$nRecursion){}

}
