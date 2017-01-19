<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	表情模型($)*/

!defined('DYHB_PATH') && exit;

class EmotModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'emot',
			'props'=>array(
				'emot_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'emot_id',
			'autofill'=>array(
				array('emot_admin','admin','create','callback'),
				array('user_id','getUserid','create','callback'),
			),
			'check'=>array(
				'empt_compositor'=>array(
					array('number',G::L('序号只能是数字'),'condition'=>'notempty','extend'=>'regex'),
				),
				'emot_name'=>array(
					array('require',G::L('表情代号不能为空')),
					array('number_underline_english',G::L('表情代号只能为英文字母、数字和下划线')),
					array('max_length',25,G::L('表情代码的最大字符数为25'))
				),
				'emot_image'=>array(
					array('require',G::L('表情路径不能为空')),
					array('max_length',100,G::L('表情路径的最大字符数为100'))
				),
				'emot_thumb'=>array(
					array('require',G::L('表情缩略图路径不能为空')),
					array('max_length',150,G::L('表情缩略图路径的最大字符数为150'))
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

	protected function admin(){
		$arrUserData = UserModel::M()->userData();
		return $arrUserData['user_name'];
	}

	public function getUserid(){
		$arrUserData = $GLOBALS['___login___'];
		return $arrUserData['user_id']?$arrUserData['user_id']:-1;
	}

}
