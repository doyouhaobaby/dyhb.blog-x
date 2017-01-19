<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   好友模型($)*/

!defined('DYHB_PATH') && exit;

class FriendModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'friend',
			'props'=>array(
				'friend_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'friend_id',
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
