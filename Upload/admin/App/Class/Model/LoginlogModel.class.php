<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	登录状态记录模型($)*/

!defined('DYHB_PATH') && exit;

class LoginlogModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'loginlog',
			'props'=>array(
				'loginlog_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'loginlog_id',
			'autofill'=>array(
				array('loginlog_ip','getIp','create','callback'),
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

	public function getIp(){
		return E::getIp();
	}

}
