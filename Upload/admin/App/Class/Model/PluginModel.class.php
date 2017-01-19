<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	插件模型($)*/

!defined('DYHB_PATH') && exit;

class PluginModel extends CommonModel{

	static public function init__(){

		return array(
			'table_name'=>'plugin',
			'props'=>array(
				'plugin_id'=>array('readonly'=>true),
				'pluginvar'=>array(Db::HAS_MANY=>'PluginvarModel','source_key'=>'plugin_id','target_key'=>'plugin_id'),
			),
			'attr_protected'=>'plugin_id',
			'check'=>array(
				'plugin_name'=>array(
					array('require',G::L('插件名不能为空')),
					array('max_length',50,G::L('插件名不能超过50个字符'))
				),
				'plugin_identifier'=>array(
					array('require',G::L('插件唯一标识不能为空')),
					array('max_length',50,G::L('插件唯一标识不能超过50个字符')),
					array('number_underline_english',G::L('插件唯一标识只能是字母，数字和下划线')),
					array('pluginIdentifier',G::L('插件唯一标识已经存在'),'condition'=>'must','extend'=>'callback')
				),
				'plugin_version'=>array(
					array('require',G::L('插件版本不能为空'))
				),
				'plugin_description'=>array(
					array('max_length',300,G::L('插件描述不能超过300个字符')),
				),
				'plugin_authorurl'=>array(
					array('empty'),
					array('max_length',300,G::L('插件作者URL 最大长度为300')),
					array('url',G::L('插件作者URL 格式必须为正确的URL 格式')),
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

	public function pluginIdentifier(){
		$nId=G::getGpc('id','P');
		$sPluginIdentifier=G::getGpc('plugin_identifier','P');
		$sPluginInfo='';
		if($nId){
			$arrPlugin=self::F('plugin_id=?',$nId)->asArray()->getOne();
			$sPluginInfo=trim($arrPlugin['plugin_identifier']);
		}
		if($sPluginIdentifier!=$sPluginInfo){
			$arrResult=self::F()->getByplugin_identifier($sPluginIdentifier)->toArray();
			if(!empty($arrResult['plugin_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

	public function getPlugindir(){
		$sPluginDir=G::getGpc('plugin_dir','P');
		if(empty($sPluginDir)){
			$sPluginDir=G::getGpc('plugin_identifier','P');
		}
		return $sPluginDir;
	}

}
