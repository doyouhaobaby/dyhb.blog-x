<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   我的信息控制器($) */

!defined('DYHB_PATH') && exit;

class MyController extends CommonController{

	public $_arrUserInfo=array();

	public function init__(){
		parent::init__();
		if($GLOBALS['___login___']===false){
			$this->E(G::L('你没有登录，无法访问本页！'));
		}
		$this->_arrUserInfo=$GLOBALS['___login___'];
	}

	public function friend(){
		define('IS_FRIENDLIST',TRUE);
		define('CURSCRIPT','friendlist');
		$sType=trim(G::getGpc('type','G'));
		if($sType=='fan'){
			$arrMap['friend_friendid']=$this->_arrUserInfo['user_id'];
		}
		else{
			$arrMap['user_id']=$this->_arrUserInfo['user_id'];
		}
		$arrMap['friend_delstatus']=0;
		$nTotalRecord=FriendModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_friend_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrFriendList=FriendModel::F()->where($arrMap)->all()->order('`create_dateline` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nTotalFriend',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrFriendLists',$arrFriendList);
		$this->assign('sFriendType',$sType);
		$this->display('friend');
	}

	public function friend_edit(){
		$nFriendId=G::getGpc('friendid');
		$sComment=trim(G::getGpc('comment'));
		if(empty($nFriendId)){
			$this->E(G::L('你没有选择好友ID'));
		}
		$oDb=Db::RUN();
		$sSql="UPDATE ". FriendModel::F()->query()->getTablePrefix()."friend SET friend_comment='{$sComment}' WHERE `friend_delstatus`=0 AND `friend_friendid`={$nFriendId} AND `user_id`=".$GLOBALS['___login___']['user_id'];
		$oDb->query($sSql);
		$this->S(G::L('更新备注成功'));
	}

	public function friend_del(){
		$nFriendId=intval(G::getGpc('friendid'));
		if(empty($nFriendId)){
			$this->E(G::L('你没有指定删除的好友'));
		}
		$oDb=Db::RUN();
		$sSql="UPDATE ". FriendModel::F()->query()->getTablePrefix()."friend SET friend_delstatus=1,friend_direction=1 WHERE `friend_friendid`={$nFriendId} AND `user_id`=".$this->_arrUserInfo['user_id'];
		$oDb->query($sSql);
		$this->E($sSql);
		$sSql="UPDATE ". FriendModel::F()->query()->getTablePrefix()."friend SET friend_direction=1 WHERE `friend_friendid`={$nFriendId} AND `user_id`=".$this->_arrUserInfo['user_id'];
		$oDb->query($sSql);
		$this->S(G::L('好友删除成功'));
	}

}
