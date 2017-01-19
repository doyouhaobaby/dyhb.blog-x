<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	wap 公用控制器($) */

!defined('DYHB_PATH') && exit;

class CommonController extends Controller{

	public $_arrOptions=array();

	public function init__(){
		parent::init__();
		if(preg_match('/(mozilla|m3gate|winwap|openwave)/i',$_SERVER['HTTP_USER_AGENT'])){
			G::urlGoTo(__ROOT__.'/index.php');
		}
		header("Content-type: text/vnd.wap.wml; charset=utf-8");
		if(empty($this->_arrOptions)){
			$this->_arrOptions=OptionModel::optionData();
		}
		Global_Extend::visitor();
		UserModel::M()->authData();
		if(UserModel::M()->isBehaviorError()){// 捕获错误
			$this->assign('__JumpUrl__',__APP__);
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		$arrUserData=UserModel::M()->userData();
		if(empty($arrUserData['user_id'])){
			$GLOBALS['___login___']=false;
		}
		else{
			$GLOBALS['___login___']=$arrUserData;
		}
		unset($arrUserData);
		$this->init();
	}

	public function init(){
		if(Global_Extend::getOption('close_blog')==1){
			$this->assign('__JumpUrl__',__APP__);
			$this->wap_mes(Global_Extend::getOption('close_blog_why'));
		}
		if(Global_Extend::getOption('close_wap')==1){
			$this->assign('__JumpUrl__',__APP__);
			$this->wap_mes(G::L('博客wap已关闭'));
		}
		$arrConfigData=array('LANG'=>ucfirst(Global_Extend::getOption('blog_lang_set')),'TIME_ZONE'=>Global_Extend::getOption('timeoffset'));
		foreach($arrConfigData as $sKey=>$sConfigData){
			if($GLOBALS['_commonConfig_'][$sKey]!==$sConfigData){
				G::C($sKey,$sConfigData);
			}
		}
		unset($arrConfigData);
	}

	public function page404(){
		header("HTTP/1.0 404 Not Found");
		$this->assign('__JumpUrl__',__APP__);
		$this->wap_mes(G::L('404 未找到'));
		exit();
	}

	public function wap_mes($sMsg,$sLink=''){
		if(empty($sLink)){
			$sLink=__APP__;
		}
		$this->assign('__JumpUrl__',$sLink);
		$this->assign('__Message__',$sMsg);
		$this->display('message');
		exit();
	}

}
