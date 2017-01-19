<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	数据调用索引模型($)*/

!defined('DYHB_PATH') && exit;

class DataindexModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'dataindex',
			'props'=>array(
				'dataindex_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'dataindex_id',
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
