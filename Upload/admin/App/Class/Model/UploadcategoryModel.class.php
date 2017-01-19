<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	文件归类模型($)*/

!defined('DYHB_PATH') && exit;

class UploadcategoryModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'uploadcategory',
			'props'=>array(
				'uploadcategory_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'uploadcategory_id',
			'check'=>array(
			),
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
