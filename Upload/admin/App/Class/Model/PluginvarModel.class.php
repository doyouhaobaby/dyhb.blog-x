<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	插件变量模型($)*/

!defined('DYHB_PATH') && exit;

class PluginvarModel extends CommonModel{

	static public function init__(){

		return array(
			'table_name'=>'pluginvar',
			'props'=>array(
				'pluginvar_id'=>array('readonly'=>true),
				'plugin'=>array(Db::BELONGS_TO=>'PluginModel','target_key'=>'plugin_id'),
			),
			'attr_protected'=>'pluginvar_id',
			'check'=>array(
				'pluginvar_title'=>array(
					array('require',G::L('插件变量标题不能为空')),
					array('max_length',100,G::L('插件变量标题不能超过100个字符'))
				),
				'pluginvar_description'=>array(
					array('max_length',255,G::L('插件变量描述不能超过255个字符'))
				),
				'pluginvar_variable'=>array(
					array('require',G::L('插件变量名不能为空')),
					array('max_length',40,G::L('插件变量名不能超过40个字符')),
					array('number_underline_english',G::L('插件变量名只能是字母，数字和下划线')),
					array('pluginvarVariable',G::L('插件变量名已经存在'),'condition'=>'must','extend'=>'callback')
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

	public function pluginvarVariable(){
		$nId=G::getGpc('id','P');
		$nVarId=G::getGpc('var_id','P');
		$sPluginvarVariable=G::getGpc('pluginvar_variable','P');
		$sPluginvarInfo='';
		if($nVarId){
			$arrPluginvar=self::F('pluginvar_id=? AND plugin_id=?',$nVarId,$nId)->asArray()->getOne();
			$sPluginvarInfo=trim($arrPluginvar['pluginvar_variable']);
		}
		if($sPluginvarVariable!=$sPluginvarInfo){
			$arrResult=self::F('pluginvar_variable=? AND plugin_id=?',$sPluginvarVariable,$nId)->asArray()->getOne();
			if(!empty($arrResult['pluginvar_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

}
