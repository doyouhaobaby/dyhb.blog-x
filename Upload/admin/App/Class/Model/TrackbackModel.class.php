<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	引用模型($)*/

!defined('DYHB_PATH') && exit;

class TrackbackModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'trackback',
			'props'=>array(
				'trackback_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'trackback_id',
			'autofill'=>array(
				array('trackback_ip','getIp','create','callback'),
			),
			'check'=>array(
				'trackback_title'=>array(
					array('require',G::L('引用标题不能为空！')),
				),
				'trackback_blogname'=>array(
					array('require',G::L('引用博客名字不能为空')),
				),
				'trackback_excerpt'=>array(
					array('require',G::L('引用摘要不能为空')),
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

	public function getIp(){
		return E::getIp();
	}

}
