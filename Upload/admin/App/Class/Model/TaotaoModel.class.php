<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	微博模型($)*/

!defined('DYHB_PATH') && exit;

class TaotaoModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'taotao',
			'props'=>array(
				'taotao_id'=>array('readonly'=>true),
				'user'=>array(Db::BELONGS_TO=>'UserModel','target_key'=>'user_id'),
			),
			'attr_protected'=>'taotao_id',
			'autofill'=>array(
				array('user_id','userId','create','callback'),
			),
			'check'=>array(
				'taotao_content'=>array(
					array('require',G::L('微博内容不能为空')),
					array('max_length',400,G::L('微博最大长度为400'))
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

	protected function userId(){
		$arrUserData=$GLOBALS['___login___'];
		return $arrUserData['user_id']?$arrUserData['user_id']:-1;
	}

	public function getATaotaoComments($nTaotaoId){
		if(empty($nTaotaoId)){
			return 0;
		}
		return CommentModel::F(array('comment_relationtype'=>'taotao','comment_relationvalue'=>$nTaotaoId))->all()->getCounts();
	}

}
