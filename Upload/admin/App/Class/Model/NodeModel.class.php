<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	节点模型($)*/

!defined('DYHB_PATH') && exit;

class NodeModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'node',
			'props'=>array(
				'node_id'=>array('readonly'=>true),
				'group'=>array(Db::BELONGS_TO=>'GroupModel','target_key'=>'group_id','skip_empty'=>true),
			),
			'attr_protected'=>'node_id',
			'check'=>array(
				'node_name'=>array(
					array('require',G::L('应用名不能为空')),
					array('nodeName',G::L('应用名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
				'node_title'=>array(
					array('require',G::L('显示名不能为空')),
				),
				'node_parentid'=>array(
					array('nodeParentId',G::L('节点不能为自己'),'condition'=>'must','extend'=>'callback'),
				),
				'node_sort'=>array(
					array('number',G::L('序号只能是数字'),'condition'=>'notempty','extend'=>'regex'),
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

	public function nodeParentId(){
		$nNodeId=G::getGpc('id');
		$nNodeParentid=G::getGpc('node_parentid');
		if(($nNodeId==$nNodeParentid)
				and !empty($nNodeId)
				and !empty($nNodeParentid)){
			return false;
		}
		return true;
	}

	public function nodeName(){
		$nId=G::getGpc('id','P');
		$sNodeName=G::getGpc('node_name','P');
		$sNodeInfo='';
		if($nId){
			$arrNode=self::F('node_id=?',$nId)->asArray()->getOne();
			$sNodeInfo=trim($arrNode['node_name']);
		}
		if($sNodeName !=$sNodeInfo){
			$arrResult=self::F()->getBynode_name($sNodeName)->toArray();
			if(!empty($arrResult['node_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

}
