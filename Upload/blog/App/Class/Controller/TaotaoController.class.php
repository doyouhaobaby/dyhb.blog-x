<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   心情控制器($) */

!defined('DYHB_PATH') && exit;

class TaotaoController extends CommonController{

	static public $_oTaotao=null;

	public function index(){
		define('IS_TAOTAOLIST',TRUE);
		define('CURSCRIPT','taotaolist');
		$arrMap=array();
		$nUserId=G::getGpc('uid','G');
		if(!empty($nUserId)){
			$arrMap['user_id']=$nUserId;
		}
		$nTotalRecord=TaotaoModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_taotao_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrTaotaoList=TaotaoModel::F()->where($arrMap)->all()->order('`taotao_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nTotalTaotao',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTaobaoList',$arrTaotaoList);
		$this->assign('the_taotao_description',Global_Extend::getOption('the_taotao_description'));
		$this->display('taotao');
	}

	public function show(){
		$nId=G::getGpc('id');
		if($nId=='index'){
			$this->index();
			exit();
		}
		$nId=intval($nId);
		if(empty($nId)){
			$this->page404();
		}
		$oTaotao=TaotaoModel::F('taotao_id=?',$nId)->query();
		if(empty($oTaotao->taotao_id)){
			$this->page404();
		}
		define('IS_SINGLETAO',TRUE);
		define('CURSCRIPT','singletao');
		self::$_oTaotao=$oTaotao;
		$this->assign('theData',$oTaotao);
		$this->assign('arrTaobaoList',array($oTaotao));
		$nPage=G::getGpc('page','G');
		$nEveryCommentnum=Global_Extend::getOption('display_taotao_comment_list_num');
		if(TEMPLATE_TYPE==='blog'||TEMPLATE_TYPE==='cms'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_parentid']=0;
			$arrCommentMap['comment_relationtype']='taotao';
			$arrCommentMap['comment_relationvalue']=$nId;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,$nPage);
			$sPageNavbar=$oPage->P('pagination');
			$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$arrAllCommentMap['comment_isshow']=1;
			$arrAllCommentMap['comment_parentid']=array('neq',0);
			$arrAllCommentMap['comment_relationtype']='taotao';
			$arrAllCommentMap['comment_relationvalue']=$nId;
			$arrAllComments=CommentModel::F()->reset(DbSelect::WHERE)->where($arrAllCommentMap)->all()->order('`comment_id` DESC')->query();
			$this->assign('arrAllComments',$arrAllComments);
			$this->assign('arrCommentLists',$arrCommentLists);
		}
		elseif(TEMPLATE_TYPE==='bbs'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_relationtype']='taotao';
			$arrCommentMap['comment_relationvalue']=$nId;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,$nPage);
			$sPageNavbar=$oPage->P('pagination');
			$arrBoardCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$this->assign('arrBoardCommentLists',$arrBoardCommentLists);
		}
		else{
			$this->E(G::L('你当前的模板方案不正确'));
		}
		$this->assign('nPage',$nPage);
		$this->assign('nEveryCommentnum',$nEveryCommentnum);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('nTotalTaotaocomment',$nTotalComment);
		$this->assign('sCommentRelationtype','taotao');
		$this->assign('nCommentRelationvalue',$nId);
		$this->display('singletao');
	}

}
