<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	搜索索引模型($)*/

!defined('DYHB_PATH') && exit;

class SearchindexModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'searchindex',
			'props'=>array(
				'searchindex_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'searchindex_id',
			'autofill'=>array(
				array('user_id','getUserid','create','callback'),
				array('searchindex_ip','getIp','create','callback'),
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

	public function getUserid(){
		$arrUserData=$GLOBALS['___login___'];
		return $arrUserData['user_id']?$arrUserData['user_id']:-1;
	}

	public function getIp(){
		return E::getIp();
	}

}
