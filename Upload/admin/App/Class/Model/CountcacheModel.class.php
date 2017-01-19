<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	计数缓存模型($)*/

!defined('DYHB_PATH') && exit;

class CountcacheModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'countcache',
			'props'=>array(
				'countcache_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'countcache_id',
		);
	}

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

}
