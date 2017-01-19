<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   User控制器($) */

!defined('DYHB_PATH') && exit;

class UserController extends CommonController{

	public function show($arrMap=array()){
		$nUserId=G::getGpc('id','G');
		$oUser=UserModel::F('user_id=? OR user_name=?',$nUserId,$nUserId)->query();
		if(empty($oUser['user_id'])){
			$this->page404();
		}
		define('IS_SINGLEUSER',TRUE);
		define('CURSCRIPT','singleuser');
		$this->assign('oUser',$oUser);
		$this->display('singleuser');
	}

	public function index(){
		define('IS_USERLIST',TRUE);
		define('CURSCRIPT','userlist');
		$sKey=trim(G::getGpc('key'));
		$arrMap=array();
		if(!empty($sKey)){
			$arrMap['user_name']=array('like',"%".$sKey."%");
		}
		$nTotalRecord=UserModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_user_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrUserList=UserModel::F()->where($arrMap)->all()->order('`user_id`DESC')->asArray()->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nTotalUser',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrUserLists',$arrUserList);
		$this->assign('the_user_description',Global_Extend::getOption('the_user_description'));
		$this->display('user');
	}

}
