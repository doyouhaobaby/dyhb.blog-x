<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	衔接模型($)*/

!defined('DYHB_PATH') && exit;

class LinkModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'link',
			'props'=>array(
				'link_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'link_id',
			'check'=>array(
				'link_compositor'=>array(
					array('number',G::L('衔接序号只能是数字')),
				),
				'link_name'=>array(
					array('require',G::L('衔接名字不能为空')),
					array('max_length',50,G::L('衔接名字最大长度为60'))
				),
				'link_url'=>array(
					array('require',G::L('衔接URL 不能为空')),
					array('max_length',250,G::L('衔接Url 最大长度为250')),
					array('url',G::L('衔接Url 格式必须为正确的URL 格式')),
				),
				'link_logo'=>array(
					array('empty'),
					array('max_length',360,G::L('衔接Logo 最大长度为360')),
					array('url',G::L('衔接Logo 格式必须为正确的URL 格式')),
				),
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
