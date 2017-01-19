<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   个人中心控制器($) */

!defined('DYHB_PATH') && exit;

class MemberController extends CommonController{

	public function index(){
		define('IS_MEMBER',TRUE);
		define('CURSCRIPT','member');
		$arrUser=$this->get_login_info();
		$this->assign('arrUser',$arrUser);
		$this->display('member');
	}

	public function password(){
		define('IS_PASSWORD',TRUE);
		define('CURSCRIPT','password');
		$arrUser=$this->get_login_info();
		$this->assign('arrUser',$arrUser);
		$this->display('password');
	}

	public function change_pass(){
		if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('changepasswordseccode')){// 验证码
			$this->check_seccode(true);
		}
		$arrUserData=UserModel::M()->userData();
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
		UserModel::M()->changePassword(	$arrUserData['user_name'],$sPassword,$sOldPassword);
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
			$this->assign('__JumpUrl__',__ROOT__.'/admin.php?c=public&a=login');
			$this->S(G::L('密码修改成功，你需要重新登录！'));
		}

	}

	public function check_email(){
		$sUserEmail=trim(strtolower(G::getGpc('user_email')));
		$nUserId=intval(G::getGpc('uid'));
			$oUser=new UserModel();
			if(!empty($sUserEmail)&& $oUser->isUseremailExists($sUserEmail,$nUserId)===true){
				echo 'false';
			}
			else{
				echo 'true';
			}
	}

	public function update(){
		if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('changeinfoseccode')){// 验证码
			$this->check_seccode(true);
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
			if(!in_array($sUseremail,$arrAccessemail))
			$this->E(G::L('你注册的邮件地址%s不在系统允许的邮件之列','app',null,$sUseremail));
		}

		$nUserId=intval(G::getGpc('user_id'));
		$oUser=UserModel::F('user_id=?',$nUserId)->query();
		$oUser->save(0,'update');
		if($oUser->isError()){
			$this->E($oUser->getErrorMessage());
		}
		else{
			$this->A($oUser->toArray(),G::L('修改资料成功'),1);
		}
	}

	public function avatar(){
		define('IS_AVATAR',TRUE);
		define('CURSCRIPT','avatar');
		$arrUser=$this->get_login_info();
		$this->assign('arrUser',$arrUser);
		$this->display('avatar');
	}

	public function get_login_info(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('login/index'));
			$this->E(G::L('你没有登录!'));
		}
		return $GLOBALS['___login___'];
	}

	public function upload(){
		define('IS_AVATARCROP',TRUE);
		define('CURSCRIPT','avatarcrop');
		if($_FILES['image']['error']==4){
			$this->E(G::L('你没有选择任何文件！'));
			return;
		}
		$oUploadfile=new UploadFileForUploadify('',array('gif','jpg','jpeg','png'),'','./Public/Avatar');
		$oUploadfile->_sSaveRule=array('MemberController','get_temp_avatar');// 设置上传文件规则
		$oUploadfile->setUploadifyDataName('image');
		$oUploadfile->setUploadReplace(TRUE);
		$oUploadfile->setAutoCreateStoreDir(TRUE);
		if(!$oUploadfile->upload()){
			$this->E($oUploadfile->getErrorMessage());
		}
		else{
			$arrPhotoInfo=$oUploadfile->getUploadFileInfo();
		}
		$this->assign('arrPhotoInfo',reset($arrPhotoInfo));
		$this->display('avatarcrop');
	}

	static public function get_temp_avatar($arrData){
		return 'temp/temp_'. $GLOBALS['___login___']['user_id'].'.'.$arrData['extension'];
	}

	public function save_crop(){
		$nTargW=$nTargH=200;
		$nJpegQuality=intval(Global_Extend::getOption('avatar_crop_jpeg_quality'));
		$sSrc=G::getGpc('temp_image','P');
		$sSrc=DYHB_PATH.'/../Public/Avatar/'.$sSrc;
		$arrPhotoInfo=@getimagesize($sSrc);
		if(function_exists('imagecreatetruecolor')&& function_exists('imagecopyresampled')){
			switch($arrPhotoInfo['mime']){
				case 'image/jpeg':
					$sImageCreateFromFunc=function_exists('imagecreatefromjpeg')? 'imagecreatefromjpeg' : '';
					break;
				case 'image/gif':
					$sImageCreateFromFunc=function_exists('imagecreatefromgif')? 'imagecreatefromgif' : '';
					break;
				case 'image/png':
					$sImageCreateFromFunc=function_exists('imagecreatefrompng')? 'imagecreatefrompng' : '';
					break;
			}
			if(empty($sImageCreateFromFunc)){
				$this->E(G::L("请不要重复提交图像，因为已经上传过图像，临时文件已经被删除！"));
			}
			$oImgR=$sImageCreateFromFunc($sSrc);
			$oDstR=ImageCreateTrueColor($nTargW,$nTargH);
			imagecopyresampled($oDstR,$oImgR,0,0,intval(G::getGpc('x','P')),intval(G::getGpc('y','P')),$nTargW,$nTargH,intval(G::getGpc('w','P')),intval(G::getGpc('h','P')));
			$sOutfile=DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('big');
			$this->create_file_dir($sOutfile	);
			imagejpeg($oDstR,$sOutfile,$nJpegQuality);
			imagedestroy($oDstR);
			$this->create_thumb($sOutfile,$sImageCreateFromFunc);
		}
		else{
			$this->E(G::L("你的PHP 版本或者配置中不支持如下的函数 “imagecreatetruecolor”、“imagecopyresampled”等图像函数，所以创建不了头像。"));
		}
		$this->create_high_jpg($sSrc,$sImageCreateFromFunc);
		$this->assign('__JumpUrl__',G::U('member/avatar'));
		$this->S(G::L("头像上传成功了！"));
	}

	public function create_high_jpg($sSrc,$sImageCreateFromFunc){
		$oImgR=$sImageCreateFromFunc($sSrc);
		imagejpeg($oImgR,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar(),intval(Global_Extend::getOption('avatar_origin_jpeg_quality')));
		imagedestroy($oImgR);
		unlink($sSrc);
	}

	public function create_file_dir($sDir){
		if(!is_dir(dirname($sDir)) && !G::makeDir(dirname($sDir))){
			$this->E(G::L('上传目录%s不可写',null,null,dirname($sDir)));
		}
	}

	public function create_thumb($sOutfile){
		$arrThumbs=array(
			array($sOutfile,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('middle'),array(120,120)),
			array($sOutfile,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('small'),array(48,48)),
		);
		foreach($arrThumbs as $arrThumb){
			$this->create_one_thumb($arrThumb[0],$arrThumb[1],$arrThumb[2]);
		}
	}

	public function create_one_thumb($sFilename,$sThumbPath,$arrSize=array()){
		$this->create_file_dir($sThumbPath);
		Image::thumb($sFilename,$sThumbPath,'',$arrSize[0],$arrSize[1],true);
	}

	public function get_avatar($sType='origin',$nUid=''){
		if(empty($nUid)){
			$arrUserData=$GLOBALS['___login___'];
			$nUid=$arrUserData['user_id'];
		}
		return E::getAvatar($nUid,$sType);
	}

}
