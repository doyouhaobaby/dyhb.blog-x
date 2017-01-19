<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	系统短消息阅读情况模型($)*/

!defined('DYHB_PATH') && exit;

class SystempmModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'systempm',
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
