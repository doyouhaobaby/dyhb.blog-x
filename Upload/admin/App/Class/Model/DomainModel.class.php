<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	域名模型($)*/

!defined('DYHB_PATH') && exit;

class DomainModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'domain',
			'props'=>array(
				'domain_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'domain_id',
			'check'=>array(
				'domain_name'=>array(
					array('require',G::L('域名不能为空')),
				),
				'domain_ip'=>array(
					array('require', G::L('域名Ip不能为空')),
					array('ip', G::L('域名Ip格式不正确')),
				),
			),
		);
	}

	static function F(){
		$arrArgs = func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

}
