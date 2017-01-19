<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   用户注册控制器($) */

!defined('DYHB_PATH') && exit;

class RegisterController extends CommonController{

	public function index(){
		define('IS_REGISTER',TRUE);
		define('CURSCRIPT','register');
		if($GLOBALS['___login___']!==false){
			$this->E(G::L('你已经是注册会员，不需要重复注册'));
		}
		$this->assign('the_register_description',Global_Extend::getOption('the_register_description'));
		$this->assign('sLicenceTxt',$this->licence());
		$this->display('register');
	}

	public function check_user(){
		$sUserName=trim(strtolower(G::getGpc('user_name')));
		$oUser=new UserModel();
		if($oUser->isUsernameExists($sUserName)===true){
			echo 'false';
		}
		else{
			echo 'true';
		}
	}

	public function check_email(){
		$sUserEmail=trim(strtolower(G::getGpc('user_email')));
		$oUser=new UserModel();
		if(!empty($sUserEmail)&& $oUser->isUseremailExists($sUserEmail)===true){
			echo 'false';
		}
		else{
			echo 'true';
		}
	}

	public function insert(){
		if($GLOBALS['___login___']===false &&Global_Extend::getOption('allowed_register')==0){
			$this->E(G::L('系统已经关闭了注册功能！','app'));
		}
		if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('registerseccode')){// 验证码
			$this->check_seccode(true);
		}
		$sPassword=trim(G::getGpc('user_password','P'));
		if(!$sPassword || $sPassword !=G::addslashes($sPassword)){
			$this->E(G::L('密码空或包含非法字符','app'));
		}
		if(strpos($sPassword,"\n")!==false || strpos($sPassword,"\r")!==false || strpos($sPassword,"\t")!==false){
			$this->E(G::L('密码包含不可接受字符.','app'));
		}
		$sUsername=trim(G::getGpc('user_name','P'));
		$sCensoruser=Global_Extend::getOption('allowed_register');
		$sCensorexp='/^('.str_replace(array('\\*',"\r\n",' '),array('.*','|',''),preg_quote(($sCensoruser=trim($sCensoruser)),'/')).')$/i';
		if($sCensoruser && @preg_match($sCensorexp,$sUsername)){
			$this->E(G::L('用户名包含被系统屏蔽的字符','app'));
		}
		$arrNameKeys=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
		foreach($arrNameKeys as $sNameKeys){
			if(strpos($sUsername,$sNameKeys)!==false){
				$this->E(G::L('此用户名包含不可接受字符或被管理员屏蔽,请选择其它用户名.','app'));
			}
		}
		$sUseremail=trim(G::getGpc('user_email','P'));
		$sCensoremail=trim(Global_Extend::getOption('censoremail'));
		if($sCensoremail){
			$arrCensoremail=explode("\n",$sCensoremail);
			$arrCensoremail=Dyhb::normalize($arrCensoremail);
			if(in_array($sUseremail,$arrCensoremail)){
				$this->E(G::L('你注册的邮件地址%s已经被官方屏蔽','app',null,$sUseremail));
			}
		}
		$sAccessemail=trim(Global_Extend::getOption('accessemail'));
		if($sAccessemail){
			$arrAccessemail=explode("\n",$sAccessemail);
			$arrAccessemail=Dyhb::normalize($arrAccessemail);
			if(!in_array($sUseremail,$arrAccessemail)){
				$this->E(G::L('你注册的邮件地址%s不在系统允许的邮件之列','app',null,$sUseremail));
			}
		}
		$oUser=new UserModel();
		if(Global_Extend::getOption('audit_register')==0){
			$oUser->user_status=1;
		}
		$oUser->save();
		if($oUser->isError()){
			$this->E($oUser->getErrorMessage());
		}
		else{
			$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
			$oCacheData->countcache_usersnum=$oCacheData->countcache_usersnum +1;
			$oCacheData->countcache_lastuser=G::getGpc('user_name','P');
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->E($oCacheData->getErrorMessage());
			}
			$this->A($oUser->toArray(),G::L('注册成功'),1);
		}
	}

	public function licence(){
		$sLicenceTxt=trim(Global_Extend::getOption('website_licence'));
		if(empty($sLicenceTxt)){
			if(file_exists(DYHB_PATH."/../blog/App/Lang/".LANG_NAME."/licence.txt")){
				$sLicenceTxt=nl2br(file_get_contents(DYHB_PATH."/../blog/App/Lang/".LANG_NAME."/licence.txt"));
			}
			else{
				$sLicenceTxt=nl2br(file_get_contents(DYHB_PATH."/../blog/licence.txt"));
			}
		}
		return $sLicenceTxt;
	}

}
