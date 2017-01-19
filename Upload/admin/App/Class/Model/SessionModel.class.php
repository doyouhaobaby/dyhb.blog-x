<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	会话模型($)*/

!defined('DYHB_PATH') && exit;

class SessionModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'session',
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
