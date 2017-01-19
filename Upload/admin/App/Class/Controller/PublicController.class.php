<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Public控制器($)*/

!defined('DYHB_PATH') && exit;

class PublicController extends InitController{

	public function is_login(){
		if($GLOBALS['___login___']===false){
			UserModel::M()->clearThisCookie();// 清理COOKIE
			$this->assign('__JumpUrl__',G::U('public/login'));
			$this->E(G::L('你没有登录！'));
		}
	}

	public function _fmain_get_admin_help_description(){
		return '<p>'.G::L('欢迎使用本软件来进行创作。','public').'</p>'.
				'<p>'.G::L('本页共提供了两个大的信息块，你可以查看当前博客统计信息和服务器配置信息。','public').'</p>'.
				'<p>'.G::L('本博客软件基于DoYouHaoBaby 框架开发，让应用程序易于创造是我们不断追求的目标。','public').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function fmain(){
		$this->is_login();
		$oDb=Db::RUN();
		$arrStaticInfo=array(
			array(G::L('日志数量'),$this->get_blog_blogs(),G::U('blog/index')),
			array(G::L('评论留言'),$this->get_blog_comments(),G::U('comment/index')),
			array(G::L('标签数量'),$this->get_blog_tags(),G::U('tag/index')),
			array(G::L('引用数量'),$this->get_blog_trackbacks(),G::U('trackback/index')),
			array(G::L('附件数量'),$this->get_blog_uploads(),G::U('upload/index')),
			array(G::L('用户数量'),$this->get_blog_users(),G::U('user/index')),
			array(G::L('好友记录数'),$this->get_blog_friends()),
			array(G::L('今日访问'),$this->_arrOptions[ 'today_visited_num' ]),
			array(G::L('总访问量'),$this->_arrOptions[ 'all_visited_num' ]),
		);

		$this->assign('arrStaticInfo',$arrStaticInfo);
		$arrInfo=array(
			G::L('操作系统')=>PHP_OS,
			G::L('运行环境')=>$_SERVER["SERVER_SOFTWARE"],
			G::L('PHP运行方式')=>php_sapi_name(),
			G::L('数据库类型')=>$GLOBALS['_commonConfig_']['DB_TYPE'],
			G::L('数据库版本')=>$oDb->getConnect()->getVersion(),
			G::L('上传附件限制')=>ini_get('upload_max_filesize'),
			G::L('执行时间限制')=>ini_get('max_execution_time').G::L('秒'),
			G::L('服务器时间')=>date(G::L('Y年n月j日 H:i:s')),
			G::L('北京时间')=>gmdate(G::L('Y年n月j日 H:i:s'),time()+8*3600),
			G::L('服务器域名/IP')=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
			G::L('剩余空间')=>round((@disk_free_space(".")/(1024*1024)),2).'M',
			G::L('register_globals')=>get_cfg_var("register_globals")=="1"?"ON":"OFF",
			G::L('magic_quotes_gpc')=>(1===get_magic_quotes_gpc())?'YES':'NO',
			G::L('magic_quotes_runtime')=>(1===get_magic_quotes_runtime())?'YES':'NO',
		);

		$this->assign('arrInfo',$arrInfo);
		$arrVersionInfo=array(
			G::L('Blog 程序版本')=>"Blog " .BLOG_SERVER_VERSION. "  Release " .BLOG_SERVER_RELEASE." <a href=\"http://doyouhaobaby.net\" target=\"_blank\">".G::L('查看最新版本')."</a>&nbsp;"."<a href=\"http://doyouhaobaby.net\" target=\"_blank\">".G::L('专业支持与服务')."</a>",
			G::L('DoYouHaoBaby版本')=>DYHB_VERSION.' [ <a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('查看最新版本').'</a> ] &nbsp;'.G::L('DoYouHaoBaby 是一款性能卓越的PHP 开发框架'),
		);
		$this->assign('arrVersionInfo',$arrVersionInfo);
		if(file_exists(DYHB_PATH."/../admin/App/Lang/".LANG_NAME."/licence.txt")){
			$sCopyTxt=nl2br(file_get_contents(DYHB_PATH."/../admin/App/Lang/".LANG_NAME."/licence.txt"));
		}
		else{
			$sCopyTxt=nl2br(file_get_contents(DYHB_PATH."/../admin/licence.txt"));
		}
		$this->assign('sCopyTxt',$sCopyTxt);
		$this->display();
	}

	public function get_blog_blogs(){
		return BlogModel::F()->all()->getCounts();
	}

	public function get_blog_users(){
		return UserModel::F()->all()->getCounts();
	}

	public function get_blog_friends(){
		return FriendModel::F()->all()->getCounts();
	}

	public function get_blog_comments(){
		return CommentModel::F()->all()->getCounts();
	}

	public function get_blog_trackbacks(){
		return TrackbackModel::F()->all()->getCounts();
	}

	public function get_blog_tags(){
		return TagModel::F()->all()->getCounts();
	}

	public function get_blog_uploads(){
		return UploadModel::F()->all()->getCounts();
	}

	public function login(){
		$arrUserData=$GLOBALS['___login___'];
		if($arrUserData!==false){
			UserModel::M()->replaceSession($arrUserData['session_hash'],$arrUserData['user_id'],$arrUserData['session_auth_key'],$arrUserData['session_seccode']);
			UserModel::M()->logout();
		}
		UserModel::M()->clearThisCookie();
		$this->assign('bLoginSeccode',$this->_arrOptions['loginseccode']);
		$this->display();
	}

	public function fheader(){
		$this->is_login();
		$arrUserData=$GLOBALS['___login___'];
		$sUserName=isset($arrUserData['user_nikename'])&& $arrUserData['user_nikename']?$arrUserData['user_nikename']:$arrUserData['user_name'];
		$arrMenuList=UserModel::M()->getTopMenuList();
		$this->assign('sUserName',$sUserName);
		$this->assign('arrListMenu',$arrMenuList);
		$arrMap['pm_delstatus']=0;
		$arrMap['pm_isread']=0 ;
		$arrMap['pm_msgtoid']=$arrUserData['user_id'];
		$arrMap['pm_type']='user';
		$nUser=PmModel::F()->where($arrMap)->all()->getCounts();
		$arrMap['pm_type']='system';
		unset($arrMap['pm_msgtoid']);
		$nSystem=PmModel::F()->where($arrMap)->all()->getCounts();
		$oSystemMessage=SystempmModel::F('user_id=?',$arrUserData['user_id'])->query();
		if(!empty($oSystemMessage['user_id'])){
			$arrReadPms=unserialize($oSystemMessage['systempm_readids']);
		}
		else{
			$arrReadPms=array();
		}
		$nSystem-=count($arrReadPms);
		if($nSystem<0){
			$nSystem=0;
		}
		if($nUser<0){
			$nUser=0;
		}
		$nTotal=$nSystem+$nUser;
		$this->assign('nUser',$nUser);
		$this->assign('nSystem',$nSystem);
		$this->assign('nTotal',$nTotal);
		$this->display();
	}

	public function index(){
		$this->is_login();
		G::urlGoto(__APP__);
	}

	public function fdrag(){
		$this->display();
	}

	public function ffooter(){
		$arrMenuList=UserModel::M()->getTopMenuList();
		$this->assign('arrListMenu',$arrMenuList);
		$this->display();
	}

	public function check_seccode($bSubmit=false){
		if($this->_arrOptions['seccode'] && $this->_arrOptions['loginseccode']){
			$sSeccode=G::getGpc('seccode');
			UserModel::M()->checkSeccode($sSeccode);
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
		}
		if($bSubmit===false){
			$this->S(G::L('验证码正确'));
		}
	}

	public function check_login(){
		if($GLOBALS['___login___']){
			$this->E(G::L('你已经登录过了。'));
		}
		$this->check_seccode(true);
		$sUserName=G::getGpc('user_name','P');
		$sPassword=G::getGpc('user_password','P');
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
		$oLoginlog->login_application='admin';
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
			$sUrl=G::U('index/index');
			$this->A(array('url'=>$sUrl),G::L('Hello %s,你成功登录！','app',null,$sUserName),1);
		}
	}

	public function _password_get_admin_help_description(){
		return '<p>'.G::L('你可以在本页对登录密码进行修改。','public').'</p>'.
				'<p>'.G::L('在表单中填好正确的信息，如果有验证码，点击验证码表单，验证码就会出现。','public').'</p>'.
				'<p>'.G::L('验证码没有出现怎么办？因为验证码需要GD 库支持，这个时候请查看你的PHP 版本是否支持GD 库。','public').'</p>'.
				'<p>'.G::L('密码是你使用本软件的重要凭证，请注意一定不要忘记。','public').'</p>'.
				'<p>'.G::L('请勿将密码设置太简单，比如123456或者你的生日号，这样容易被猜中。','public').'</p>'.
				'<p>'.G::L('一段时间修改一次密码是一个比较靠谱的事情。','public').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function password(){
		$this->is_login();
		$arrUserData=$GLOBALS['___login___'];
		$this->assign('nUserId',$arrUserData['user_id']);
		$this->assign('changepasswordseccode',$this->_arrOptions['seccode'] &&$this->_arrOptions['changepasswordseccode']);
		$this->display();
	}

	public function change_pass(){
		$this->is_login();
		$arrUserData=$GLOBALS['___login___'];
		$sPassword=G::getGpc('user_password','P');
		$sNewPassword=G::getGpc('new_password','P');
		$sOldPassword=G::getGpc('old_password','P');
		if($sOldPassword==''){
			$this->E(G::L('旧密码不能为空'));
		}

		if($sPassword==''){
			$this->E(G::L('新密码不能为空'));
		}

		if($sPassword!=$sNewPassword){
			$this->E(G::L('两次输入的密码不一致'));
		}

		if($this->_arrOptions['seccode'] &&$this->_arrOptions['changepasswordseccode']){
			$sSeccode=G::getGpc('seccode','G');
			UserModel::M()->checkSeccode($sSeccode);
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
		}

		UserModel::M()->changePassword($arrUserData['user_name'],$sPassword,$sOldPassword);
		if(UserModel::M()->isBehaviorError()){
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		else{
			$oUser=UserModel::F('user_id=?',$arrUserData['user_id'])->query();
			if($oUser->isError()){
				$this->E($oUser->getErrorMessage());
			}
			$arrUserData=$GLOBALS['___login___'];
			UserModel::M()->replaceSession($arrUserData['session_hash'],$arrUserData['user_id'],$arrUserData['session_auth_key'],$arrUserData['session_seccode']);
			UserModel::M()->logout();
			UserModel::M()->clearThisCookie();
			$this->S(G::L('密码修改成功，你需要重新登录！'));
		}
	}

	public function _information_get_admin_help_description(){
		return '<p>'.G::L('你可以在本页对个人资料进行修改。','public').'</p>'.
				'<p>'.G::L('在表单中填好正确的信息，如果有验证码，点击验证码表单，验证码就会出现。','public').'</p>'.
				'<p>'.G::L('验证码没有出现怎么办？因为验证码需要GD 库支持，这个时候请查看你的PHP 版本是否支持GD 库。','public').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function information(){
		$this->is_login();
		$arrUserData=$GLOBALS['___login___'];
		$oUserInfo=UserModel::F()->getByuser_id($arrUserData['user_id']);
		$this->assign('oUserInfo',$oUserInfo);
		$this->assign('changeinfoseccode',$this->_arrOptions['seccode'] &&$this->_arrOptions['changeinfoseccode']);
		$this->display();
	}

	public function change_info(){
		$this->is_login();
		if($this->_arrOptions['seccode'] &&$this->_arrOptions['changeinfoseccode']){
			$sSeccode=G::getGpc('seccode','P');
			UserModel::M()->checkSeccode($sSeccode);
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
		}

		$nUserId=G::getGpc('user_id','P');
		$oUser=UserModel::F('user_id=?',$nUserId)->query();
		$oUser->save(0,'update');
		if($oUser->isError()){
			$this->E($oUser->getErrorMessage());
		}
		else{
			$this->S(G::L('修改用户资料成功了！'));
		}
	}

	public function logout(){
		if(UserModel::M()->isLogin()){
			$arrUserData=$GLOBALS['___login___'];
			UserModel::M()->replaceSession($arrUserData['session_hash'],$arrUserData['user_id'],$arrUserData['session_auth_key'],$arrUserData['session_seccode']);
			UserModel::M()->logout();
			$this->assign("__JumpUrl__",G::U('public/login'));
			$this->S(G::L('登出成功！'));
		}
		else{
			$this->E(G::L('已经登出！'));
		}
	}

	public function fmenu(){
		$this->is_login();
		$sTag=G::getGpc('tag');
		$arrMenuList=UserModel::M()->getMenuList();
		if($sTag===null)$sTag='';
		$this->assign('sMenuTag',$sTag);
		$this->assign('arrMenuList',$arrMenuList);
		$this->display();
	}

}
