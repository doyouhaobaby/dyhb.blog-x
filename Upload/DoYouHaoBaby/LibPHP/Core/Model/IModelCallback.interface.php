<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模型回调($)*/

!defined('DYHB_PATH') && exit;

interface IModelCallback{

	const BEFORE_FIND='beforeFind';
	const AFTER_FIND='afterFind';
	const AFTER_INIT='afterInit';
	const BEFORE_SAVE='beforeSave';
	const AFTER_SAVE='afterSave';
	const BEFORE_CREATE='beforeCreate';
	const AFTER_CREATE='afterCreate';
	const BEFORE_UPDATE='beforeUpdate';
	const AFTER_UPDATE='afterUpdate';
	const BEFORE_CHECK='beforeCheck';
	const AFTER_CHECK='afterCheck';
	const BEFORE_CHECK_ON_CREATE='beforeCheckOnCreate';
	const AFTER_CHECK_ON_CREATE='afterCheckOnCreate';
	const BEFORE_CHECK_ON_UPDATE='beforeCheckOnUpdate';
	const AFTER_CHECK_ON_UPDATE='afterCheckOnUpdate';
	const BEFORE_DESTROY='beforeDestroy';
	const AFTER_DESTROY='afterDestroy';

}
