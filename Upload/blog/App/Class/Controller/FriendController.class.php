<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   好友控制器($) */

!defined('DYHB_PATH') && exit;

class FriendController extends CommonController{

	public $_arrUserInfo=array();

	public function init__(){
		parent::init__();
		$this->_arrUserInfo=Global_Extend::isLogin(true);
		if(empty($this->_arrUserInfo['user_id'])){
			$this->E(G::L('你没有登录，无法访问本页！'));
		}
	}

	public function add(){
		$nUserId=intval(G::getGpc('uid'));
		if(empty($nUserId)){
			$this->E(G::L('你没有指定添加的好友!'));
		}
		$oFriendTempModel=FriendModel::F('user_id=? AND friend_friendid=? AND friend_delstatus=0',$this->_arrUserInfo['user_id'],$nUserId)->query();
		if(!empty($oFriendTempModel['user_id'])){
			$this->E(G::L('此用户已经在你的好友列表中了!'));
		}
		else{
			unset($oFriendTempModel);
		}
		if($nUserId==$this->_arrUserInfo['user_id']){
			$this->E(G::L('你不能添加自己为好友!'));
		}
		$oDb=Db::RUN();
		$oFriendTempModel=FriendModel::F('user_id=? AND friend_friendid=?',$this->_arrUserInfo['user_id'],$nUserId)->query();
		if(!empty($oFriendTempModel['user_id'])){
			$sSql="UPDATE ". PmModel::F()->query()->getTablePrefix()."friend SET friend_delstatus=0 WHERE `friend_friendid`={$nUserId} AND `user_id`=".$this->_arrUserInfo['user_id'];
			$oDb->query($sSql);
		}
		else{
			$oFriendModel=new FriendModel();
			$oFriendModel->user_id=$this->_arrUserInfo['user_id'];
			$oFriendModel->friend_friendid=$nUserId;
			$oFriendModel->save();
		}
		if(empty($oFriendTempModel['user_id'])&& $oFriendModel->isError()){
			$this->E($oFirendModel->getErrorMessage());
		}
		else{
			$oFriendTempModel=FriendModel::F()->where(array('user_id'=>$nUserId,'friend_friendid'=>$this->_arrUserInfo['user_id']))->query();
			if(!empty($oFriendTempModel['user_id'])){
				$sSql="UPDATE ". PmModel::F()->query()->getTablePrefix()."friend SET friend_direction=3 WHERE `user_id`={$nUserId} AND `friend_friendid`=".$this->_arrUserInfo['user_id'];
				$oDb->query($sSql);
				$sSql="UPDATE ". PmModel::F()->query()->getTablePrefix()."friend SET friend_direction=3 WHERE `friend_friendid`={$nUserId} AND `user_id`=".$this->_arrUserInfo['user_id'];
				$oDb->query($sSql);
			}
			$this->S(G::L('添加好友成功'));
		}
	}

}
