<?dyhb
interface IModelCallback{

	/**
	 * 查询前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_FIND='beforeFind';

	/**
	 * 查询后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_FIND='afterFind';

	/**
	 * 初始化后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_INIT='afterInit';

	/**
	 * 保存之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_SAVE='beforeSave';

	/**
	 * 保存之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_SAVE='afterSave';

	/**
	 * 创建之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_CREATE='beforeCreate';

	/**
	 * 创建之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_CREATE='afterCreate';

	/**
	 * 更新之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_UPDATE='beforeUpdate';

	/**
	 * 更新之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_UPDATE='afterUpdate';

	/**
	 * 验证之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_CHECK='beforeCheck';

	/**
	 * 验证之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_CHECK='afterCheck';

	/**
	 * 创建记录验证之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_CHECK_ON_CREATE='beforeCheckOnCreate';

	/**
	 * 创建记录验证之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_CHECK_ON_CREATE='afterCheckOnCreate';

	/**
	 * 更新记录验证之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_CHECK_ON_UPDATE='beforeCheckOnUpdate';

	/**
	 * 更新记录验证之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_CHECK_ON_UPDATE='afterCheckOnUpdate';

	/**
	 * 销毁之前
	 *
	 * @const
	 * @var string
	 */
	const BEFORE_DESTROY='beforeDestroy';

	/**
	 * 销毁之后
	 *
	 * @const
	 * @var string
	 */
	const AFTER_DESTROY='afterDestroy';

}
