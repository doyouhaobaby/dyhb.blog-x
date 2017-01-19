<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	配置模型($)*/

!defined('DYHB_PATH') && exit;

class OptionModel extends CommonModel{

	public static $_arrOption=array();

	static public function init__(){
		return array(
			'table_name'=>'option',
			'props'=>array(
				'option_name'=>array('readonly'=>true),
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

	static public function optionData( ){
		$arrOptionData=Model::C('global_option');
		if(empty($arrOptionData)){
			$arrOptionData=self::F()->asArray()->all()->query();
			if($arrOptionData===false)return false;
			foreach($arrOptionData as $nKey=>$arrValue){
				$arrOptionData[ $arrValue['option_name'] ]=$arrValue['option_value'];
				unset($arrOptionData[ $nKey ] );
			}
			Model::C('global_option',$arrOptionData);
		}
		return self::$_arrOption=$arrOptionData;
	}

	public static function getOption($sOptionName){
		return self::$_arrOption[$sOptionName];
	}

	public static function uploadOption($sOptionName,$sOptionValue){
		$oOptionModel=self::F('option_name=?',$sOptionName)->getOne();
		$oOptionModel->option_value=$sOptionValue;
		$oOptionModel->save(0,'update');
		if($oOptionModel->isError()){
			return false;
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		return true;
	}

}
