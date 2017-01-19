<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	短消息模型($)*/

!defined('DYHB_PATH') && exit;

class PmModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'pm',
			'props'=>array(
				'pm_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'pm_id',
			'check'=>array(
				'pm_message'=>array(
					array('require',G::L('短消息内容不能为空')),
				)
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

	public function sendAPm($sMessageto,$nUserId,$sUserName,$sSubject='',$sApp=''){
		$oUser=UserModel::F()->getByuser_name($sMessageto);
		$oPm=new self();
		$oPm->pm_msgfrom=$sUserName;
		$oPm->pm_msgfromid=$nUserId;
		$oPm->pm_msgtoid=$oUser['user_id'];
		if(!empty($sSubject)){
			$oPm->pm_subject=$sSubject;
		}
		if(!empty($sApp)){
			$oPm->pm_fromapp=$sApp;
		}
		$oPm->save();
		return $oPm;
	}

}
