<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	页面模型($)*/

!defined('DYHB_PATH') && exit;

class PageModel extends BlogModel{

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

}
