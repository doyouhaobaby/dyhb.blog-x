<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	分组模型($)*/

!defined('DYHB_PATH') && exit;

class GroupModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'group',
			'props'=>array(
				'group_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'group_id',
			'check'=>array(
				'group_name'=>array(
					array('require',G::L('组名不能为空！')),
					array('number_underline_english',G::L('组名只能是由数字，下划线，字母组成')),
					array('groupName',G::L('组名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
				'group_title'=>array(
					array('require',G::L('组显示名不能为空！')),
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

	public function groupName(){
		$nId=G::getGpc('id','P');
		$sGroupName=G::getGpc('group_name','P');
		$sGroupInfo='';
		if($nId){
			$arrGroup=self::F('group_id=?',$nId)->asArray()->getOne();
			$sGroupInfo=trim($arrGroup['group_name']);
		}

		if($sGroupName !=$sGroupInfo){
			$arrResult=self::F()->getBygroup_name($sGroupName)->toArray();
			if(!empty($arrResult['group_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

}
