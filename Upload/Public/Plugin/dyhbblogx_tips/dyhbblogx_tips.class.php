<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	消息提示插件主类管理程序($)*/

!defined('DYHB_PATH') && exit;

class plugin_dyhbblogx_tips{

	public $_arrValue=array();

	public function __construct(){
		$arrTipsConfig=array();
		$arrPluginValues=Model::C('global_plugin','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		if($arrPluginValues==false){
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$arrPluginValues=Model::C('global_plugin','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}
		$arrTipsConfig=$arrPluginValues['dyhbblogx_tips'];//缓存插件变量值
		$this->_arrValue['global_header']=$arrTipsConfig['testvar'];
		$this->_arrValue['global_footer']=$arrTipsConfig['testvar2'];
	}

	public function global_header(){
		return $this->_arrValue['global_header'];
	}

	public function global_footer(){
		return $this->_arrValue['global_footer'];
	}

	public function common(){
		return '123456';
	}

}

class plugin_dyhbblogx_tips_guestbook extends plugin_dyhbblogx_tips{

	public function index_dyhbblogx_tips_output(){
		return 'Guestbook output';
	}

	public function index_hello(){
		return 'hello';
	}

	public function index_hello2(){
		return array('key1'=>'瓜西西的','key2'=>'瓜西西2');
	}

}
