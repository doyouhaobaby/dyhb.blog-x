<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模型RBAC 行为扩展($)*/

!defined('DYHB_PATH') && exit;

class ModelBehaviorRbac extends ModelBehavior{

	protected $_arrSettings=array(
	'encode_type'=>'authcode',
	'authcode_random'=>6,
	'auth_thin'=>false,
	'userid_prop'=>'user_id',
	'username_prop'=>'user_name',
	'useremail_prop'=>'user_email',
	'status_prop'=>'user_status',
	'password_prop'=>'user_password',
	'authcode_random_prop'=>'user_random',
	'rbac_data_props'=>'user_name,user_id,user_email',
	'check_login_field'=>'',
	'update_login_auto'=>true,
	'update_login_count_prop'=>'user_logincount',
	'update_login_at_prop'=>'user_lastlogintime',
	'update_login_ip_prop'=>'user_lastloginip',
	'register_save_auto'=>true,
	'register_ip_prop'=>'user_registerip',
	'register_at_prop'=>'create_dateline',
	'unique_username'=>true,
	'rbac_login_life'=>86400
	);
	private $_arrSavedState=array();

	public function bind(){
		parent::bind();
		// 添加静态方法
		$this->addStaticMethod_('checkLogin',array($this,'checkLogin'));// 登录验证
		$this->addStaticMethod_('checkUsername',array($this,'checkUsername'));// 验证用户名是否存在
		$this->addStaticMethod_('checkPassword',array($this,'checkPassword'));// 验证密码
		$this->addStaticMethod_('changePassword',array($this,'changePassword'));// 修改密码
		$this->addStaticMethod_('authData',array($this,'getAuthData'));// 获取当前的加密登录信息
		$this->addStaticMethod_('userData',array($this,'userDataDyn'));// 获取当前用户登录信息
		$this->addStaticMethod_('checkRbac',array($this,'checkRbac'));// 启动权限检查
		$this->addStaticMethod_('isLogin',array($this,'isLogin'));// 判断是否登录
		$this->addStaticMethod_('alreadyLogout',array($this,'alreadyLogout'));// 检测是否已经登出
		$this->addStaticMethod_('checkRbacLogin',array($this,'checkRbacLogin'));// 检测是否已经登出
		$this->addStaticMethod_('logout',array($this,'logout'));// 登出
		$this->addStaticMethod_('replaceSession',array($this,'replaceSession'));
		$this->addStaticMethod_('updateSession',array($this,'updateSession'));
		$this->addStaticMethod_('checkSeccode',array($this,'checkSeccode'));
		$this->addStaticMethod_('getMenuList',array($this,'getMenuList'));
		$this->addStaticMethod_('getTopMenuList',array($this,'getTopMenuList'));
		$this->addStaticMethod_('clearThisCookie',array($this,'clearThisCookie'));
		// 添加动态方法
		$this->addDynamicMethod_('checkPassword',array($this,'checkPasswordDyn'));
		$this->addDynamicMethod_('changePassword',array($this,'changePasswordDyn'));
		$this->addDynamicMethod_('updateLogin',array($this,'updateLoginDyn'));
		$this->addDynamicMethod_('userData',array($this,'userDataDyn'));
		// 响应事件，用于更新认证相关数据
		$this->addEventHandler_(self::AFTER_CHECK_ON_CREATE,array($this,'afterCheckOnCreate_'));
		$this->addEventHandler_(self::AFTER_CHECK_ON_UPDATE,array($this,'afterCheckOnUpdate_'));
	}

	public function checkLogin($sUsername, $sPassword,$bEmail=false, $bUpdateLogin=true){
		if(!empty($this->_arrSettings['check_login_field'])){
			$sPn=trim($this->_arrSettings['check_login_field']);
		}
		else{
			if($bEmail===true){// E-mail
				$sPn=$this->_arrSettings['useremail_prop'];
			}
			else{
				$sPn=$this->_arrSettings['username_prop'];
			}
		}

		$oMember=$this->_oMeta->find(array($sPn=> $sUsername))->query();
		if(!$oMember->id()){
			$this->setErrorMessage(G::L('我们无法找到%s这个用户' ,'AdvanceDyhb',null,$sUsername));
			return false;
		}
		if($oMember[$this->_arrSettings['status_prop']]<=0){// 查看用户是否禁用
			$this->setErrorMessage(G::L('用户%s的账户还没有解锁，你暂时无法登录' ,'AdvanceDyhb',null,$sUsername));
			return false;
		}
		if(!$this->checkPasswordDyn($oMember,$sPassword)){// 验证密码
			$this->setErrorMessage(G::L('用户%s的密码错误' ,'AdvanceDyhb',null,$sUsername));
			return false;
		}
		if($this->_arrSettings['encode_type']=='authcode'){
			$this->tryToDeleteOldSession($oMember['user_id']);
		}
		if($this->_arrSettings['auth_thin']===TRUE){// 如果为简化版的验证，直接返回
			return $oMember;
		}

		G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']),$oMember->id(),array('expire'=>$this->_arrSettings['rbac_login_life']));
		$arrAdmins=$GLOBALS['_commonConfig_']['ADMIN_USERID']?explode(',',$GLOBALS['_commonConfig_']['ADMIN_USERID']):array(1);
		if(in_array($oMember->id(),$arrAdmins)){
			G::cookie(md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']),true,array('expire'=>$this->_arrSettings['rbac_login_life']));
		}
		if($this->_arrSettings['update_login_auto']){
			$this->updateLoginDyn($oMember);
		}

		$nSecondCode=intval(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'seccode_code'));
		$this->sendCookie($oMember->id(),$oMember[$this->_arrSettings['password_prop']],$oMember[$this->_arrSettings['update_login_count_prop']],$nSecondCode);
		$sHash=G::addslashes(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash'));
		$this->updateSession($sHash,$oMember->id(),$GLOBALS['_authkey_'],$nSecondCode);// 更新数据库中的登陆会话
		$this->clearLoginMenu();
		$this->saveAccessList($oMember->id());// 最后缓存rbac权限
		return $oMember;
	}

	public function tryToDeleteOldSession($nUserId){
		SessionModel::M()->deleteWhere("`user_id`=$nUserId OR(`user_id`=0)");
	}

	public function getAuthData($sUserModel=null){
		if($sUserModel===null || $sUserModel=='') $sUserModel=$GLOBALS['_commonConfig_']['USER_AUTH_MODEL'];
		$sAuthKey=md5($GLOBALS['_commonConfig_']['DYHB_AUTH_KEY'].$_SERVER['HTTP_USER_AGENT']);
		$sAuthData=G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'auth' );
		list($nUserId,$sPassword,$nLoginCount,$nSecondCode)=$sAuthData ? explode("\t", E::authCode($sAuthData,true,NULL,$this->_arrSettings['rbac_login_life'])): array('', '', 0,'');
		$sHash=G::addslashes(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash'));
		$nUserId=intval($nUserId);
		$sPassword=G::addslashes($sPassword);
		$nLoginCount=intval($nLoginCount);
		$nSeccodeCode=intval($nSecondCode);
		$arrUserInformation=array();// 用户信息容器
		$bSessionExists=false;
		if($sHash){
			if($nUserId){
				$oSessionDbExpression=new DbExpression('['.$GLOBALS['_commonConfig_']['DB_PREFIX'].$sUserModel.'.user_id]');
				$arrUserInformation=UserModel::F()
					->setColumns($this->_arrSettings['rbac_data_props'])
					->joinLeft(array($GLOBALS['_commonConfig_']['DB_PREFIX'].'session'),'user_id,session_hash,session_auth_key,session_seccode',array('user_id'=>$oSessionDbExpression))
					->where('['.$GLOBALS['_commonConfig_']['DB_PREFIX'].'session.session_hash]=? AND ['.$GLOBALS['_commonConfig_']['DB_PREFIX'].$sUserModel.'.user_id]=? AND ['.$GLOBALS['_commonConfig_']['DB_PREFIX'].$sUserModel.'.user_password]=? AND ['.$GLOBALS['_commonConfig_']['DB_PREFIX'].'session.session_auth_key]=? AND user_status > 0',array($sHash, $nUserId,$sPassword,$sAuthKey))
					->asArray()
					->query();
			}
			else{
				$oSelect=SessionModel::F('session_hash=?',$sHash);
				$arrUserInformation=$oSelect->asArray()->query();
			}
			if($arrUserInformation){
				$bSessionExists=TRUE;
				if(!empty($arrUserInformation['user_id'])){
					$oSessionDbExpression=new DbExpression('['.$GLOBALS['_commonConfig_']['DB_PREFIX'].$sUserModel.'.user_id]');
					$arrUserInformation=array_merge( $arrUserInformation,UserModel::F()
						->setColumns($this->_arrSettings['rbac_data_props'])
						->joinLeft($GLOBALS['_commonConfig_']['DB_PREFIX'].'session','user_id,session_hash,session_auth_key,session_seccode',array('user_id'=>$oSessionDbExpression))
						->where('['.$GLOBALS['_commonConfig_']['DB_PREFIX'].$sUserModel.'.user_id]=? AND user_status > 0', intval($arrUserInformation['user_id']))
						->asArray()
						->query());
					$nUserId=$arrUserInformation['user_id'];
				}
			}
			else{
				if($arrUserInformation=SessionModel::F('session_hash=?',$sHash)->asArray()->query()){
					$this->clearThisCookie();
					$bSessionExists=TRUE;
				}
			}
		}

		if($bSessionExists===FALSE){
			if($nUserId){
				if(!($arrUserInformation=UserModel::F('user_id=? AND user_password=? AND user_status > 0',$nUserId,$sPassword)->asArray()->query())){
					$this->clearThisCookie();
				}
			}
			$arrUserInformation['session_hash']=G::randString(6);
			$arrUserInformation['session_seccode']=G::randString(6,null,true);
			UserModel::M()->updateSession($arrUserInformation['session_hash'],$nUserId,$sAuthKey,$arrUserInformation['session_seccode']);
		}
		$sHash=isset($arrUserInformation['session_hash'])?$arrUserInformation['session_hash']:$sHash;// hash and seccodecode
		$nSeccodeCode=isset($arrUserInformation['session_seccode'])?$arrUserInformation['session_seccode']:$nSeccodeCode;
		$nUserId=isset($arrUserInformation['user_id'])? $arrUserInformation['user_id']:$nUserId;// 用户名和用户密码
		$sUserName=isset($arrUserInformation['user_name'])? $arrUserInformation['user_name'] :'';
		$GLOBALS['_sessionExists_']=$bSessionExists;// 将Session 存在状态保存在全局变量中
		if(!$sHash || $sHash!=G::addslashes(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash'))){// 设置hash值
			G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash',$sHash,array('expire'=>$this->_arrSettings['rbac_login_life']));
		}
		$nTempSeccodeCode=intval(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'seccode_code'));// 设置seccode值
		if($nTempSeccodeCode===null || $nSeccodeCode!=$nTempSeccodeCode){
			G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'seccode_code',$nSeccodeCode,array('expire'=>$this->_arrSettings['rbac_login_life']));
		}
		$GLOBALS[$sAuthKey]=$arrUserInformation ;
		$GLOBALS['_authkey_']=$sAuthKey;
	}

	public function checkRbac(){
		$bAdminAuthKey=G::cookie(md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']));
		if($bAdminAuthKey){return true;}
		if($GLOBALS['_commonConfig_']['USER_AUTH_ON'] && !in_array(MODULE_NAME,Dyhb::normalize($GLOBALS['_commonConfig_']['NOT_AUTH_MODULE']))){// 用户权限检查
			if(!$this->accessDecision()){
				if(!G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']))){// 检查认证识别号
					G::urlGoTo(G::U($GLOBALS['_commonConfig_']['USER_AUTH_GATEWAY']));// 跳转到认证网关
				}
				if($GLOBALS['_commonConfig_']['RBAC_ERROR_PAGE']){// 没有权限 抛出错误
					G::urlGoTo($GLOBALS['_commonConfig_']['RBAC_ERROR_PAGE']);
				}
				else{
					if($GLOBALS['_commonConfig_']['GUEST_AUTH_ON']){
						G::urlGoTo(G::U($GLOBALS['_commonConfig_']['USER_AUTH_GATEWAY']));
					}
					$this->setErrorMessage(G::L('你没有访问权限','AdvanceDyhb'));
					return false;
				}
			}
		}
	}

	public function isLogin(){
		$arrUser=$GLOBALS[$GLOBALS['_authkey_']];
		return !empty($arrUser['user_id']);
	}

	public function alreadyLogout(){
		$arrUser=$GLOBALS[$GLOBALS['_authkey_']];
		return empty($arrUser['user_id']);
	}

	public function checkRbacLogin(){
		if($this->checkAccess()){// 检查当前操作是否需要认证
			if(!G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']))){// 检查认证识别号
				if($GLOBALS['_commonConfig_']['GUEST_AUTH_ON']){
					$arrAccessList=unserialize(E::authcode(Dyhb::cache('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'))));
					if($arrAccessList!==false){// 开启游客授权访问
						$this->saveAccessList($GLOBALS['_commonConfig_']['GUEST_AUTH_ID']);// 保存游客权限
					}
				}
				else{// 禁止游客访问跳转到认证网关
					G::urlGoTo(PHP_FILE.$GLOBALS['_commonConfig_']['USER_AUTH_GATEWAY']);
				}
			}
		}
		return true;
	}

	public function clearAllCookie(){
		G::cookie(null,null,null,false);
	}

	public function logout(){
		if($this->isLogin()){
			$this->clearThisCookie();
		}
	}

	public function checkSeccode($sSeccode){
		$arrUserData=UserModel::M()->userData();
		$nOldSeccode=isset($arrUserData['session_seccode'])?trim($arrUserData['session_seccode']):'';
		$nOldSeccode=trim($nOldSeccode);
		if(empty($nOldSeccode)){
			$this->setErrorMessage(G::L('我们无法从数据库获取验证码，也许没有写入，或者无法读取？','AdvanceDyhb'));
			return false;
		}
		E::seccodeConvert($nOldSeccode);
		if(trim($nOldSeccode)!=$sSeccode){
			$this->setErrorMessage(G::L('数据库验证码错误，你提交的验证码%s 不等于 %s','AdvanceDyhb',null,$sSeccode,$nOldSeccode));
			return false;
		}
	}

	public function clearThisCookie(){
		G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']),null);
		G::cookie(md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']),null);
		G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'seccode_code',null);
		G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash',null);
		G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'auth',null);
		G::cookie(md5(APP_NAME.MODULE_NAME.ACTION_NAME),null);
	}

	public function clearLoginMenu(){
		$nAuthId=G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']));
		$arrUserData=$this->userDataDyn();
		Dyhb::deleteCache('access_list_'.$nAuthId,array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'));
		if(!empty($arrUserData['user_id'])){
			Dyhb::deleteCache('_Menu_'.$GLOBALS['_commonConfig_']['USER_AUTH_KEY'].'_'.$arrUserData['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
			Dyhb::deleteCache('menu_top_cache_'.$arrUserData['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
		}
	}

	public function checkUsername($sUsername){
		$sPn=$this->_arrSettings['username_prop'];
		if(!$this->_oMeta->find(array($sPn=>$sUsername))->getCounts()>0){
			$this->setErrorMessage(G::L('用户%s的不存在' ,'AdvanceDyhb',null,$sUsername));
			return false;
		}
		return true;
	}

	public function checkPassword($sUsername,$sPassword){
		$sPn=$this->_arrSettings['username_prop'];
		$oMember=$this->_oMeta->find(array($sPn=>$sUsername))->query();
		return($oMember->id() and $this->checkPasswordDyn($oMember,$sPassword));
	}

	public function changePassword($sUsername,$sNewPassword,$sOldPassword,$bIgnoreOldPassword=false){
		$sPn=is_int($sUsername)?$this->_arrSettings['userid_prop']:$this->_arrSettings['username_prop'];
		$oMember=$this->_oMeta->find(array($sPn=>$sUsername))->query();
		if(!$oMember->id()){
			$this->setErrorMessage(G::L('我们无法找到%s这个用户' ,'AdvanceDyhb',null,$sUsername));
			return false;
		}
		$this->changePasswordDyn($oMember,$sNewPassword,$sOldPassword,$bIgnoreOldPassword);
	}

	public function checkPasswordDyn(Model $oMember, $sPassword){
		if($this->_arrSettings['auth_thin']===TRUE)
			return $this->checkPassword_($sPassword,$oMember[ $this->_arrSettings['password_prop'] ]);
		else
			return $this->checkPassword_($sPassword,$oMember[$this->_arrSettings['password_prop']],$oMember[$this->_arrSettings['authcode_random_prop']]);
	}

	public function changePasswordDyn(Model $oMember,$sNewPassword,$sOldPassword,$bIgnoreOldPassword=false){
		if(!$bIgnoreOldPassword){
			if(!$this->checkPasswordDyn($oMember, $sOldPassword)){
				$this->setErrorMessage(G::L('用户输入的旧密码错误','AdvanceDyhb'));
				return false;
			}
		}
		$oMember->changePropForce($this->_arrSettings['password_prop'],$sNewPassword);
		$oMember->save(0,'update');
	}

	public function updateLoginDyn(Model $oMember,array $arrData=null){
		$sPn=$this->_arrSettings['update_login_count_prop'];// 更新登录次数
		if($sPn){
			$oMember->changePropForce($sPn, $oMember[ $sPn ] + 1);
		}
		$sPn=$this->_arrSettings['update_login_at_prop'];// 登录时间
		if($sPn){
			$nTime=isset($arrData['login_at'])?$arrData['login_at']:CURRENT_TIMESTAMP;
			if(substr($this->_oMeta->_arrProp[ $sPn ]['ptype'], 0, 3)!='int'){
				$nTime=date('Y-m-d H:i:s',$nTime);
			}
			$oMember->changePropForce($sPn,$nTime);
		}
		$sPn=$this->_arrSettings['update_login_ip_prop'];// 登录当前登录IP
		if($sPn){
			$sIp=isset($arrData['login_ip'])?$arrData['login_ip']:isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:E::getIp();
			if(substr($this->_oMeta->_arrProp[$sPn]['ptype'], 0, 3)=='int'){
				$sIp=ip2long($sIp);
			}
			$oMember->changePropForce($sPn, $sIp);
		}
		$oMember->save(0,'update');
	}

	public function userDataDyn(){
		return $GLOBALS[$GLOBALS['_authkey_']];
	}

	public function afterCheckOnCreate_(Model $oMember){
		if($this->_arrSettings['unique_username']){// 验证用户是否唯一
			$sPn=$this->_arrSettings['username_prop'];
			if($this->_oMeta->find(array($sPn=> $oMember[$sPn]))->getCounts()> 0){
				$this->setErrorMessage(G::L('用户名%s只能够唯一','AdvanceDyhb',null,$oMember[$sPn]));
				return false;
			}
		}
		$sPn=$this->_arrSettings['password_prop'];// 加密密码
		$sPasswordCleartext=$oMember[$sPn];
		$oMember->changePropForce($sPn,$this->encodePassword_($sPasswordCleartext)); // 加密密码
		if(!$this->_arrSettings['auth_thin'])
			$oMember->changePropForce($this->_arrSettings['authcode_random_prop'], $this->_arrSavedState['authcode_random']); // 发送验证random
		$this->_arrSavedState['password']=$sPasswordCleartext;
		if($this->_arrSettings['register_save_auto']){// 如果设置了自动创建注册数据
			$sPn=$this->_arrSettings['register_at_prop'];// 创建注册时间数据
			if($sPn){
				$nTime=CURRENT_TIMESTAMP;
				if(substr($this->_oMeta->_arrProp[$sPn]['ptype'], 0, 3)!='int'){
					$nTime=date('Y-m-d H:i:s', $nTime);
				}
				$oMember->changePropForce($sPn, $nTime);
			}
			$sPn=$this->_arrSettings['register_ip_prop'];// 创建注册IP数据
			if($sPn){
				$sIp=isset($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_ADDR']:E::getIp();
				if(substr($this->_oMeta->_arrProp[$sPn]['ptype'],0,3)=='int'){
					$sIp=ip2long($sIp);
				}
				$oMember->changePropForce($sPn, $sIp);
			}
		}
	}

	public function afterCheckOnUpdate_(Model $oMember){
		$sPn=$this->_arrSettings['password_prop'];// 获取密码属性
		if($oMember->changed($sPn)){
			$sPasswordCleartext=$oMember[$sPn];
			$oMember[$sPn]=$this->encodePassword_($sPasswordCleartext);
			$this->_arrSavedState['password']=$sPasswordCleartext;
			if(!$this->_arrSettings['auth_thin'])
				$oMember->changePropForce($this->_arrSettings['authcode_random_prop'],$this->_arrSavedState['authcode_random']);
		}
	}

	public function saveExceptionHandler_(Model $oMember){
		if(isset($this->_arrSavedState['password'])){// 还原密码
			$oMember->changePropForce($this->_arrSettings['password_prop'],$this->_arrSavedState['password']);
			unset($this->_arrSavedState['password']);
		}
	}

	private function checkPassword_($sCleartext,$sCryptograph,$sRanDom=''){
		$et=$this->_arrSettings['encode_type'];
		if(is_array($et)){
			return call_user_func($et,$sCleartext)==$sCryptograph;
		}
		if($et=='cleartext')return $sCleartext==$sCryptograph;
		switch($et){
			case 'authcode':
				return md5(md5($sCleartext).$sRanDom)==$sCryptograph;
			case 'md5':
				return md5($sCleartext)==$sCryptograph;
			case 'crypt':
				return crypt($sCleartext,$sCryptograph)==$sCryptograph;
			 case 'sha1':
				return sha1($sCleartext)==$sCryptograph;
			 case 'sha2':
				return hash('sha512', $sCleartext)==$sCryptograph;
			default:
				return $et($sCleartext)==$sCryptograph;
		}
	}

	private function encodePassword_($sPassword){
		$et=$this->_arrSettings['encode_type'];
		if(is_array($et)){
			return call_user_func($et,$sPassword);
		}
		if($et=='cleartext')return $sPassword;
		if($et=='authcode'){
			$sRandom=G::randString($this->_arrSettings['authcode_random']);
			$this->_arrSavedState['authcode_random']=$sRandom;
			return md5(md5(trim($sPassword)).trim($sRandom));
		}
		return $et($sPassword);
	}

	public function sendCookie($nUserId,$sPassword,$nLoginCount,$nSecondCode){
		G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'auth',E::authcode("{$nUserId}\t{$sPassword}\t{$nLoginCount}\t{$nSecondCode}",FALSE ,NULL,$this->_arrSettings['rbac_login_life']),array('expire'=>$this->_arrSettings['rbac_login_life']));
	}

	public function updateSession($sHash,$nUserId,$sAuthKey,$nSeccode){
		$bSessionExists=isset($GLOBALS['_sessionExists_'])?$GLOBALS['_sessionExists_']:false;
		if($bSessionExists===true){// 如果Session存在的话，那么更新Session数据库表
			$oSession=SessionModel::F('user_id=?',$nUserId)->getOne();
			$oSession->session_seccode=$nSeccode;
			$oSession->session_hash=$sHash;
			if($nUserId){
				$oSession->user_id=$nUserId;
			}
			$oSession->save(0,'update');
		}
		else{
			$this->replaceSession($sHash,$nUserId,$sAuthKey,$nSeccode,TRUE);// 写入Session数据
		}
	}

	public function replaceSession($sHash,$nUserId,$sAuthKey,$nSeccode,$bInsert=false){
		if($nUserId===''){return false;}
		$oSession=SessionModel::M()->deleteWhere("`session_hash`='$sHash' OR($nUserId<>0 AND `user_id`=$nUserId OR(`user_id`=0))");// 删除SESSION
		if($bInsert){// 新插入Session数据
			$oSession=new SessionModel();
			$oSession->session_hash=$sHash;
			$oSession->session_auth_key=$sAuthKey;
			$oSession->user_id=$nUserId;
			$oSession->session_seccode=$nSeccode;
			$oSession->save();
		 }
	}

	public function getTopMenuList(){
		$arrUserData=$this->userDataDyn();
		$sMenuCacheData=Dyhb::cache('menu_top_cache_'.$arrUserData['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
		$arrTemp=unserialize(E::authcode($sMenuCacheData));
		if($sMenuCacheData && $arrTemp!==false){
			$arrMenuList=$arrTemp;
		}
		else{
			$arrMenuList=GroupModel::F('group_status=?',1)
				->order('`group_sort` ASC')
				->setColumns('group_id,group_title')
				->all()
				->asArray()
				->query();
			Dyhb::writeCache('menu_top_cache_'.$arrUserData['user_id'],E::authcode(serialize($arrMenuList),FALSE ,NULL,$this->_arrSettings['rbac_login_life']),array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
		}
		unset($arrTemp);
		return $arrMenuList;
	}

	public function getMenuList(){
		$arrMenuList=array();
		$arrUserData=$this->userDataDyn();
		$sMenuCacheData=Dyhb::cache('_Menu_'.$GLOBALS['_commonConfig_']['USER_AUTH_KEY'].'_'.$arrUserData['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
		$arrTemp=unserialize(E::authcode($sMenuCacheData));
		if($sMenuCacheData && $arrTemp!==false){
			$arrMenuList=unserialize(E::authcode($sMenuCacheData));
		}
		else{
			$nId=NodeModel::F()->getColumn('node_id');
			$arrWhere['node_level']=2;
			$arrWhere['node_status']=1;
			$arrWhere['node_parentid']=$nId ;
			$arrMenuList=NodeModel::F()
					->setColumns('node_id,node_name,group_id,node_title')
					->order('`node_sort` ASC')
					->all()
					->where($arrWhere)
					->asArray()
					->query();
			$arrAccessList=unserialize(E::authcode(Dyhb::cache('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'))));
			foreach($arrMenuList as $sKey=>$arrModule){
				if(isset($arrAccessList[strtolower(APP_NAME)][strtolower($arrModule['node_name'])]) OR G::cookie(md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']))){
					$arrModule['node_access']=1;
					$arrMenuList[$sKey]=$arrModule;
				}
			}
			Dyhb::writeCache('_Menu_'.$GLOBALS['_commonConfig_']['USER_AUTH_KEY'].'_'.$arrUserData['user_id'],E::authcode(serialize($arrMenuList),FALSE ,NULL,$this->_arrSettings['rbac_login_life']),array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
		}
		return $arrMenuList;
	}

	public function saveAccessList($nAuthId=null){
		if(null===$nAuthId ){
			$nAuthId=G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY']));
		}
		if($GLOBALS['_commonConfig_']['USER_AUTH_TYPE']!=2 and !G::cookie( md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']))){
			Dyhb::writeCache('access_list_'.$nAuthId,E::authcode(serialize($this->getAccessList($nAuthId)),false,null,$this->_arrSettings['rbac_login_life']),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'));
		}
		return ;
	}

	static function getRecordAcessList($nAuthId=null,$sModule=''){
		if(null===$nAuthId){ $nAuthId=G::cookie( md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])); }
		if(empty($sModule)){ $sModule=MODULE_NAME; }
		$arrAccessList=$this->getModuleAccessList($nAuthId,$sModule);
		return $arrAccessList;
	}

	public function checkAccess(){
		if($GLOBALS['_commonConfig_']['USER_AUTH_ON']){// 如果项目要求认证，并且当前模块需要认证，则进行权限认证
			$arrModule=array();
			$arrAction=array();
			if('' !=$GLOBALS['_commonConfig_']['REQUIRE_AUTH_MODULE']){
				$arrModule['yes']=Dyhb::normalize(strtoupper($GLOBALS['_commonConfig_']['REQUIRE_AUTH_MODULE']));
			}
			else{
				$arrModule['no']=Dyhb::normalize(strtoupper($GLOBALS['_commonConfig_']['NOT_AUTH_MODULE']));
			}
			// 检查当前模块是否需要认证
			if((!empty($arrModule['no']) and !in_array(strtoupper(MODULE_NAME),$arrModule['no'])) || (!empty($arrModule['yes']) and in_array(strtoupper(MODULE_NAME),$arrModule['yes'])) || empty($arrModuele['yes'])){
				if('' !=$GLOBALS['_commonConfig_']['REQUIRE_AUTH_ACTION']){
					$arrAction['yes']=Dyhb::normalize(strtoupper($GLOBALS['_commonConfig_']['REQUIRE_AUTH_ACTION']));// 需要认证的操作
				}
				else{
					$arrAction['no']=Dyhb::normalize(strtoupper($GLOBALS['_commonConfig_']['NOT_AUTH_ACTION']));// 无需认证的操作
				}
				// 检查当前操作是否需要认证
				if((!empty($arrAction['no']) and !in_array(strtoupper(ACTION_NAME),$arrAction['no'])) || (!empty($arrAction['yes']) and in_array(strtoupper(ACTION_NAME),$arrAction['yes'])) || empty($arrAction['yes'])){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		return false;
	}

	public function accessDecision($sAppName=APP_NAME){
		if( $this->checkAccess()){
			$sAccessGuid=md5($sAppName.MODULE_NAME.ACTION_NAME);
			$bAdminAuthKey=G::cookie(md5($GLOBALS['_commonConfig_']['ADMIN_AUTH_KEY']));
			if(empty($bAdminAuthKey)){
				if($GLOBALS['_commonConfig_']['USER_AUTH_TYPE']==2){
					$arrAccessList=$this->getAccessList(G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])));
				}
				else{
					if($this->getAccessList(G::cookie($sAccessGuid))){
						return true;
					}
					$arrAccessList=unserialize(E::authcode(Dyhb::cache('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'))));
				}
				$sLowerAppName=strtolower($sAppName);
				$sLowerModule=MODULE_NAME;
				$sLowerAction=ACTION_NAME;
				if(!isset($arrAccessList[$sLowerAppName ][$sLowerAppName.'@'.$sLowerModule][$sLowerAppName.'@'.$sLowerModule.'@'.$sLowerAction])){
					G::cookie($sAccessGuid,false,$this->_arrSettings['rbac_login_life']);
					return false;
				}
				else{
					G::cookie($sAccessGuid,true,$this->_arrSettings['rbac_login_life']);
				}
			}
			else{
				return true;
			}
		}
		return true;
	}

	public function getAccessList( $nAuthId){
		$oDb=Db::RUN();
		$arrTable=array(
			'role'=>RoleModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_ROLE_TABLE'],
			'userrole'=>UserroleModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_USERROLE_TABLE'],
			'access'=>AccessModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_ACCESS_TABLE'],
			'node'=>NodeModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_NODE_TABLE']
		);
		$sSql="SELECT DISTINCT node.node_id,node.node_name FROM ".
			$arrTable['role']." AS role,".
			$arrTable['userrole']." AS userrole,".
			$arrTable['access']." AS access ,".
			$arrTable['node']." AS node ".
			"WHERE userrole.user_id='{$nAuthId}' and userrole.role_id=role.role_id AND(access.role_id=role.role_id  OR(access.role_id=role.role_parentid AND role.role_parentid!=0))AND role.role_status=1 AND access.node_id=node.node_id AND node.node_level=1 AND node.node_status=1";

		$arrApps=$oDb->getAllRows($sSql);
		$arrAccess=array();// 项目权限列表

		foreach($arrApps as $sKey=>$arrApp){
			$nAppId=$arrApp['node_id'];
			$sAppName=$arrApp['node_name'];
			$arrAccess[ strtolower($sAppName)]=array();// 读取项目的模块权限
			$sSql="SELECT DISTINCT node.node_id,node.node_name FROM ".
				$arrTable['role']." AS role,".
				$arrTable['userrole']." AS userrole,".
				$arrTable['access']." AS access ,".
				$arrTable['node']." AS node ".
				"WHERE userrole.user_id='{$nAuthId}' and userrole.role_id=role.role_id AND(access.role_id=role.role_id  OR(access.role_id=role.role_parentid AND role.role_parentid!=0))AND role.role_status=1 AND access.node_id=node.node_id AND node.node_level=2 AND node.node_parentid={$nAppId} AND node.node_status=1";
			$arrModules=$oDb->getAllRows($sSql);
			$arrPublicAction=array();// 判断是否存在公共模块的权限
			foreach($arrModules as $sKey=>$arrModule){
				$nModuleId=$arrModule['node_id'];
				$sModuleName=$arrModule['node_name'];
				if('PUBLIC'==strtoupper($sModuleName)){
					$sSql="SELECT DISTINCT node.node_id,node.node_name FROM ".
					$arrTable['role']." AS role,".
					$arrTable['userrole']." AS userrole,".
					$arrTable['access']." AS access ,".
					$arrTable['node']." AS node ".
					"WHERE userrole.user_id='{$nAuthId}' AND userrole.role_id=role.role_id AND(access.role_id=role.role_id  OR(access.role_id=role.role_parentid AND role.role_parentid!=0))AND role.role_status=1 AND access.node_id=node.node_id AND node.node_level=3 AND node.node_parentid={$nModuleId} AND node.node_status=1";
					$arrRs=$oDb->getAllRows($sSql);
					foreach($arrRs as $arrA){
						$arrPublicAction[$arrA['node_name']]=$arrA['node_id'];
					}
					unset($arrModules[$sKey]);
					break;
				}
			}
			foreach($arrModules as $sKey=>$arrModule){// 依次读取模块的操作权限
				$nModuleId=$arrModule['node_id'];
				$sModuleName=$arrModule['node_name'];
				$sSql="SELECT DISTINCT node.node_id,node.node_name FROM ".
					$arrTable['role']." AS role,".
					$arrTable['userrole']." AS userrole,".
					$arrTable['access']." AS access ,".
					$arrTable['node']." AS node ".
					"WHERE userrole.user_id='{$nAuthId}' AND userrole.role_id=role.role_id AND(access.role_id=role.role_id  OR(access.role_id=role.role_parentid AND role.role_parentid!=0))AND role.role_status=1 AND access.node_id=node.node_id AND node.node_level=3 and node.node_parentid={$nModuleId} AND node.node_status=1";
				$arrRs=$oDb->getAllRows($sSql);
				$arrAction=array();
				foreach($arrRs as $arrA){
					$arrAction[$arrA['node_name']]=$arrA['node_id'];
				}
				$arrAction+=$arrPublicAction;// 和公共模块的操作权限合并
				$arrAccess[strtolower($sAppName)][strtolower($sModuleName)]=array_change_key_case($arrAction,CASE_LOWER);
			}
		}
		return $arrAccess;
	}

	public function getModuleAccessList($nAuthId,$sModule){
		$oDb=Db::RUN();
		$arrTable=array(
			'role'=>RoleModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_ROLE_TABLE'],
			'userrole'=>UserroleModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_USERROLE_TABLE'],
			'access'=>AccessModel::F()->query()->getTablePrefix().$GLOBALS['_commonConfig_']['RBAC_ACCESS_TABLE'],
		);
		$sSql="SELECT DISTINCT access.node_id FROM ".
			$arrTable['role']." AS role,".
			$arrTable['userrole']." AS userrole,".
			$arrTable['access']." AS access ".
			"WHERE userrole.user_id='{$nAuthId}' AND userrole.role_id=role.role_id AND(access.role_id=role.role_id  OR(access.role_id=role.role_parentid AND role.role_parentid!=0))AND role.role_status=1 AND  access.access_module='{$sModule}' AND access.access_status=1";
		$arrNodes=$oDb->getAllRows($sSql);
		$arrAccess=array();
		foreach($arrNodes as $arrNode){
			$arrAccess[]=$arrNodes['node_id'];
		}
		return $arrAccess;
	}

}
