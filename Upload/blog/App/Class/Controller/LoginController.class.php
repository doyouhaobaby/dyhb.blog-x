<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   登录控制器($) */

!defined('DYHB_PATH') && exit;

class LoginController extends CommonController{

	public function index(){
		if($GLOBALS['___login___']!==false){
			$this->E(G::L('你已经登录过了。'));
		}
		define('IS_LOGIN',TRUE);
		define('CURSCRIPT','login');
		$this->display('login');
	}

	public function check(){
		if($GLOBALS['___login___']!==false){
			$this->E(G::L('你已经登录过了。'));
		}
		$sUserName=G::getGpc('user_name','P');
		$sPassword=G::getGpc('user_password','P');
		if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('loginseccode')){
			$sSeccode=G::getGpc('seccode');
			UserModel::M()->checkSeccode($sSeccode);
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
		}
		if(empty($sUserName)){
			$this->E(G::L('帐号或者E-mail不能为空！'));
		}
		elseif(empty($sPassword)){
			$this->E(G::L('密码不能为空！'));
		}
		Check::RUN();
		if(Check::C($sUserName,'email')){
			$bEmail=true;
			unset($_POST['user_name']);
		}
		else{
			$bEmail=false;
		}

		$oUser=UserModel::M()->checkLogin($sUserName,$sPassword,$bEmail);
		$oLoginlog=new LoginlogModel();
		$oLoginlog->loginlog_user=$sUserName;
		$oLoginlog->login_application='blog';
		if(G::isImplementedTo($oUser,'IModel')){
			$oLoginlog->user_id=$oUser->user_id;
		}
		if(UserModel::M()->isBehaviorError()){
			$oLoginlog->loginlog_status=0;
			$oLoginlog->save();
			if($oLoginlog->isError()){
				$this->E($oLoginlog->getErrorMessage());
			}
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		else{
			if($oUser->isError()){
				$oLoginlog->loginlog_status=0;
				$oLoginlog->save();
				if($oLoginlog->isError()){
					$this->E($oLoginlog->getErrorMessage());
				}
				$this->E($oUser->getErrorMessage());
			}
			$oLoginlog->loginlog_status=1;
			$oLoginlog->save();
			if($oLoginlog->isError()){
				$this->E($oLoginlog->getErrorMessage());
			}
			$this->A('',G::L('Hello %s,你成功登录！','app',null,$sUserName),1);
		}
	}

	public function logout(){
		if(UserModel::M()->isLogin()){
			$arrUserData=$GLOBALS['___login___'];
			UserModel::M()->replaceSession($arrUserData['session_hash'],$arrUserData['user_id'],$arrUserData['session_auth_key'],$arrUserData['session_seccode']);
			UserModel::M()->logout();
			$this->assign("__JumpUrl__",G::U('login/index'));
			$this->S(G::L('登出成功！'));
		}
		else{
			$this->E(G::L('已经登出！'));
		}
	}

	public function clear(){
		UserModel::M()->clearThisCookie();
		$this->S(G::L('清理登录痕迹成功'));
	}

}
