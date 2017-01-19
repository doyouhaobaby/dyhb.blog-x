<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Pm控制器($) */

!defined('DYHB_PATH') && exit;

class PmController extends CommonController{

	public $_arrUserInfo=array();

	public function init__(){
		parent::init__();
		if($GLOBALS['___login___']===false){
			$this->E(G::L('你没有登录，无法访问本页！'));
		}
		$this->_arrUserInfo=$GLOBALS['___login___'];
	}

	public function index(){
		define('IS_PMLIST',TRUE);
		define('CURSCRIPT','pmlist');
		$sType=trim(G::getGpc('type','G'));
		if($sType=='new'){
			$arrMap['pm_isread']=0;
			$arrMap['pm_type']='user';
		}
		elseif($sType=='system'){
			$arrMap['pm_type']='system';
		}
		else{
			$arrMap['pm_type']='user';
		}
		if($sType!='system'){
			$arrMap['pm_delstatus']=0;
			$arrMap['pm_msgtoid']=$this->_arrUserInfo['user_id'];
			$arrReadPms=array();
		}
		else{
			$oSystemMessage=SystempmModel::F('user_id=?',$this->_arrUserInfo['user_id'])->query();
			if(!empty($oSystemMessage['user_id'])){
				$arrReadPms=unserialize($oSystemMessage['systempm_readids']);
			}
			else{
				$arrReadPms=array();
			}
		}
		$nTotalRecord=PmModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_pm_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrPmList=PmModel::F()->where($arrMap)->all()->order('`pm_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nTotalPm',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrPmLists',$arrPmList);
		$this->assign('sPmType',$sType);
		$this->assign('arrReadPms',$arrReadPms);
		$this->display('pm');
	}

	public function show(){
		$nPmId=G::getGpc('id');
		if($nPmId===null || $nPmId=='index'){
			$this->index();
			exit();
		}
		$oOnePm=PmModel::F('pm_id=? AND pm_delstatus=0',$nPmId)->query();
		if(empty($oOnePm['pm_id'])){
			$this->page404();
		}
		define('IS_SINGLEPM',TRUE);
		define('CURSCRIPT','singlepm');
		if($oOnePm['pm_type']=='system'){
			$oSystemMessage=SystempmModel::F('user_id=?',$this->_arrUserInfo['user_id'])->query();
			if(!empty($oSystemMessage['user_id'])){
				$arrReadPms=unserialize($oSystemMessage['systempm_readids']);
				if(!in_array($oOnePm['pm_id'],$arrReadPms)){
					$arrReadPms[]=$oOnePm['pm_id'];
				}
				$oSystemMessage->systempm_readids=serialize($arrReadPms);
				$oSystemMessage->save(0,'update');
			}
			else{
				$arrReadPms[]=$oOnePm['pm_id'];
				unset($oSystemMessage);
				$oSystemMessage=new SystempmModel();
				$oSystemMessage->user_id=$this->_arrUserInfo['user_id'];
				$oSystemMessage->systempm_readids=serialize($arrReadPms);
				$oSystemMessage->save();
			}
			$this->assign('arrReadPms',$arrReadPms);
			$this->assign('sPmType','singlepm');
			$this->assign('oOnePm',$oOnePm);
			$this->display('singlepm');
			exit();
		}
		$nUserId=G::getGpc('uid');
		$sDate=G::getGpc('date','G');
		if(empty($sDate)){
			$sDate=3;
		}
		if($sDate!='all'){
			$arrMap['create_dateline']=array('egt',(CURRENT_TIMESTAMP-$sDate*86400));
		}
		$arrMap['pm_delstatus']=0;
		$arrMap['pm_msgfromid']=array('exp',"in(".$oOnePm['pm_msgtoid'].",$nUserId)");
		$arrMap['pm_id']=array('egt',$nPmId);
		$nTotalRecord=PmModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_pm_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrPms=PmModel::F()->where($arrMap)->all()->order('`pm_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$oDb=Db::RUN();
		$sSql="UPDATE ".PmModel::F()->query()->getTablePrefix()."pm SET pm_isread=1 WHERE `pm_msgfromid`={$nUserId} AND `pm_delstatus`=0".(!empty($arrMap['create_dateline'])?" AND `create_dateline`>=".$arrMap['create_dateline'][1]:'');
		$oDb->query($sSql);
		$oToUser=UserModel::F('user_id=?',$nUserId)->query();
		$this->assign('arrPms',$arrPms);
		$this->assign('sPmType','singlepm');
		$this->assign('nUserId',$nUserId);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('nTotalPm',$nTotalRecord);
		$this->assign('sDate',$sDate);
		$this->assign('oOnePm',$oOnePm);
		$this->assign('sCurrentTimestamp',date('Y-m-d H:i:s',CURRENT_TIMESTAMP));
		$this->assign('sVersion',"Blog " .BLOG_SERVER_VERSION. "	Release " .BLOG_SERVER_RELEASE);
		$this->assign('oToUser',$oToUser);
		if(G::getGpc('export')=='yes'){
			ob_end_clean();
			header('Content-Encoding: none');
			header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')? 'application/octetstream' : 'application/octet-stream'));
			header('Content-Disposition: attachment; filename="PM_'.$oOnePm['pm_msgfrom'].'_TO_'.$oToUser['user_name'].'_'.date('Y_m_d_H_i_s',CURRENT_TIMESTAMP).'.html"');
			header('Pragma: no-cache');
			header('Expires: 0');
			$this->assign('sBlogName',Global_Extend::getOption('blog_name'));
			$this->assign('sBlogUrl',Global_Extend::getOption('blog_url'));
			$this->display('pmarchive');
			exit;
		}
		$this->display('singlepm');
	}

	public function del(){
		$nUserId=G::getGpc('uid');
		$sDate=G::getGpc('date','G');
		if(empty($sDate)){
			$sDate=3;
		}
		$oDb=Db::RUN();
		$sSql="UPDATE ".PmModel::F()->query()->getTablePrefix()."pm SET pm_delstatus=1 WHERE `pm_msgfromid`={$nUserId} AND `pm_delstatus`=0 AND `pm_msgtoid`=".$this->_arrUserInfo['user_id'].($sDate!='all'?" AND `create_dateline`>=".(CURRENT_TIMESTAMP-$sDate*86400):'');
		$oDb->query($sSql);
		$this->assign('__JumpUrl__',G::U('pm/index?type=user'));
		$this->S(G::L('短消息已经清空'));
	}

	public function del_one_pm($nId='',$nUserId='',$nFromId=''){
		$nPmId=$nId;
		if(empty($nPmId)){
			$nPmId=G::getGpc('id');
		}
		if(empty($nUserId)){
			$nUserId=$this->_arrUserInfo['user_id'];
		}
		if(empty($nFromId)&&$nFromId!='-1'){
			$nFromId=G::getGpc('uid');;
		}
		if(empty($nPmId)){
			$this->E(G::L('你没有指定要删除的短消息'));
		}
		$oPmModel=PmModel::F('pm_id=? AND pm_type=\'user\' AND pm_msgtoid=? '.($nFromId!=-1?'AND pm_msgfromid='.$nFromId:''),$nPmId,$nUserId,$nFromId)->query();
		if(empty($oPmModel['pm_id'])){
			$this->E(G::L('待删除的短消息不存在'));
		}
		$oPmModel->pm_delstatus=1;
		$oPmModel->save(0,'update');
		if($oPmModel->isError()){
			$this->E($oPmModel->getErrorMessage());
		}
		else{
			if(empty($nId)){
				$this->S(G::L('删除短消息成功！'));
			}
		}
	}

	public function del_the_select(	){
		$arrPmIds=G::getGpc('pmid','P');
		$arrUserId=G::getGpc('uid','P');
		if(empty($arrPmIds)){
			$this->E(G::L('你没有指定要删除的短消息'));
		}
		foreach($arrPmIds as $nKey=>$nPmId){
			$this->del_one_pm($nPmId,$this->_arrUserInfo['user_id'],-1);
		}
		$this->S(G::L('删除短消息成功！'));
	}

	public function check_pm(){
		if(Global_Extend::getOption('pmcenter')==0){// 判断短消息状态
			$this->E(G::L('短消息功能已经被关闭了','app'));
		}
		if(Global_Extend::getOption('pmsendregdays')>0){
			if(CURRENT_TIMESTAMP-$GLOBALS['___login___']['create_dateline']<86400*Global_Extend::getOption('pmsendregdays')){
				$this->E(G::L('只有注册时间超过%d天的用户才能够发送短消息','app',null,Global_Extend::getOption('pmsendregdays')));
			}
		}
		if(Global_Extend::getOption('pmfloodctrl')>0){
			$nCurrentTimeStamp=CURRENT_TIMESTAMP;
			$nPmSpace=intval(Global_Extend::getOption('pmfloodctrl'));
			$oPm=PmModel::F("pm_msgfromid=? AND {$nCurrentTimeStamp}-create_dateline<{$nPmSpace}",$GLOBALS['___login___']['user_id'])->query();
			if(!empty($oPm['pm_id'])){
				$this->E(G::L('每%d秒你才能发送一次短消息','app',null,$nPmSpace));
			}
		}
		if(Global_Extend::getOption('pmlimit1day')>0){
			$arrNowDate=Date::getTheDataOfNowDay();
			$nPms=PmModel::F("create_dateline<{$arrNowDate[1]} AND create_dateline>{$arrNowDate[0]} AND pm_msgfromid=?",$GLOBALS['___login___']['user_id'])->all()->getCounts();
			if($nPms>Global_Extend::getOption('pmlimit1day')){
				$this->E(G::L('一个用户每天最多只能发送%d条消息','app',null,Global_Extend::getOption('pmlimit1day')));
			}
		}
	}

	public function pm_new(){
		define('IS_PMNEW',TRUE);
		define('CURSCRIPT','pmnew');
		$this->check_pm();
		$nUserId=intval(G::getGpc('uid'));
		$nPmId=intval(G::getGpc('pmid'));
		if(!empty($nUserId)){
			$oUser=UserModel::F('user_id=?',$nUserId)->query();
			$sUser=$oUser['user_name'];
		}
		else{
			$sUser='';
		}
		if(!empty($nPmId)){
			$oPm=PmModel::F('pm_id=?',$nPmId)->query();
			if(!empty($oPm['pm_id'])){
				$oToUser=UserModel::F('user_id=?',$oPm['pm_msgtoid'])->query();
				$sContent=G::L("发件人").":".$oPm['pm_msgfrom']."\r\n";
				$sContent.=G::L("收件人").":".$oToUser['user_name']."\r\n";
				$sContent.=G::L("日期").":".date('Y-m-d H:i:s',$oPm['create_dateline'])."\r\n\r\n";
				$sContent.="[quote]".$oPm['pm_message']."[/quote]";
			}
			else{
				$sContent='';
			}
		}
		else{
			$sContent='';
		}
		$this->assign('sUser',$sUser);
		$this->assign('sContent',$sContent);
		$this->display('pmnew');
	}

	public function send_a_pm(){
		$this->check_pm();
		if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('sendpmseccode')){// 验证码
			$this->check_seccode(true);
		}
		$sMessageto=trim(G::getGpc('messageto'));
		$sPmMessage=trim(G::getGpc('pm_message'));
		if(empty($sMessageto)){
			$this->E(G::L('收件人不能为空'));
		}
		if(empty($sPmMessage)){
			$this->E(G::L('短消息内容不能为空'));
		}
		if($GLOBALS['___login___']['user_name']==$sMessageto){
			$this->E(G::L('收件人不能为自己','app'));
		}
		$oUserModel=new UserModel();
		if($oUserModel->isUsernameExists($sMessageto,$GLOBALS['___login___']['user_id'])===false){
			$this->E(G::L('收件人不存在'));
		}
		$arrUserInfo=Global_Extend::isLogin(true);
		$oPmModel=new PmModel();
		$oPm=$oPmModel->sendAPm($sMessageto,$arrUserInfo['user_id'],$arrUserInfo['user_name'],'','blog');
		if($oPm->isError()){
			$this->E($oPm->getErrorMessage());
		}
		else{
			$this->S(G::L('发送短消息成功'));
		}
	}

}
