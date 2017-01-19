<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   留言板控制器($) */

!defined('DYHB_PATH') && exit;

class GuestbookController extends CommonController{

	public function index(){
		define('IS_GUESTBOOK',TRUE);
		define('CURSCRIPT','guestbook');
		$nPage=G::getGpc('page','G');
		$nEveryCommentnum=Global_Extend::getOption('display_blog_guestbook_list_num');
		if(TEMPLATE_TYPE==='blog'||TEMPLATE_TYPE==='cms'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_parentid']=0;
			$arrCommentMap['comment_relationtype']='';
			$arrCommentMap['comment_relationvalue']='';
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,$nPage);
			$sPageNavbar=$oPage->P('pagination');
			$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$arrAllCommentMap['comment_isshow']=1;
			$arrAllCommentMap['comment_parentid']=array('neq',0);
			$arrAllCommentMap['comment_relationtype']='';
			$arrAllCommentMap['comment_relationvalue']='';
			$arrAllComments=CommentModel::F()->reset(DbSelect::WHERE)->where($arrAllCommentMap)->all()->order('`comment_id` DESC')->query();
			$this->assign('arrAllComments',$arrAllComments);
			$this->assign('arrCommentLists',$arrCommentLists);
		}
		elseif(TEMPLATE_TYPE==='bbs'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_relationtype']='';
			$arrCommentMap['comment_relationvalue']='';
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,G::getGpc('page','G'));
			$sPageNavbar=$oPage->P('pagination');
			$arrBoardCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$this->assign('arrBoardCommentLists',$arrBoardCommentLists);
		}
		else{
			$this->E(G::L('你当前的模板方案不正确'));
		}
		$this->assign('nPage',$nPage);
		$this->assign('nEveryCommentnum',$nEveryCommentnum);
		$this->assign('theData','');
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('nTotalGuestbook',$nTotalComment);
		$this->assign('the_guestbook_description',Global_Extend::getOption('the_guestbook_description'));
		$this->assign('sCommentRelationtype','');
		$this->assign('nCommentRelationvalue','');
		$this->display('guestbook');
	}

}
