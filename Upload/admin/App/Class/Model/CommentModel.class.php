<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	评论模型($)*/

!defined('DYHB_PATH') && exit;

class CommentModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'comment',
			'props'=>array(
				'comment_id'=>array('readonly'=>true),
				'user'=>array(Db::BELONGS_TO=>'UserModel','target_key'=>'user_id'),
			),
			'attr_protected'=>'comment_id',
			'autofill'=>array(
				array('user_id','getUserid','create','callback'),
				array('comment_ip','getIp','create','callback'),
			),
			'check'=>array(
				'comment_name'=>array(
					array('require',G::L('评论名字不能为空')),
					array('max_length',25,G::L('评论名字的最大字符数为25'))
				),
				'comment_email'=>array(
					array('empty'),
					array('max_length',300,G::L('评论Email 最大字符数为300')),
					array('email',G::L('评论的邮件必须为正确的Email 格式'))
				),
				'comment_url'=>array(
					array('empty'),
					array('max_length',300,G::L('评论URL 最大字符数为300')),
					array('url',G::L('评论的邮件必须为正确的URL 格式'))
				),
				'comment_content'=>array(
					array('require',G::L('评论的内容不能为空！'))
				),
			),
		);
	}

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

	static public function getLevelByComment($arrComment){
		if(is_object($arrComment)){
			$arrComment=$arrComment->toArray();
		}
		if($arrComment['comment_parentid']==0){
			$nLevel=1;
		}
		else{
			$nLevel=self::theCommentParent($arrComment);
		}
		return $nLevel;
	}

	static public function theCommentParent($arrComment){
		$nLevel=1;
		if($arrComment['comment_parentid']==0){
			return $nLevel;
		}
		$arrParentComment=CommentModel::F('comment_id=?',$arrComment['comment_parentid'])->asArray()->query();
		$nLevel+=self::theCommentParent($arrParentComment);
		return $nLevel;
	}

	static public function getCommentKeyByComment($arrComment){
		if($arrComment['comment_parentid']==0)
			return OptionModel::getOption('display_blog_comment_list_num');
		else{
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_parentid']=$arrComment['comment_parentid'];
			$arrCommentMap['comment_relationtype']=$arrComment['comment_relationtype'];
			$arrCommentMap['comment_relationvalue']=$arrComment['comment_relationvalue'];
			return self::F()->where($arrCommentMap)->all()->getCounts();
		}
	}

	public function getUserid(){
		$arrUserData=$GLOBALS['___login___'];
		return $arrUserData['user_id']?$arrUserData['user_id']:-1;
	}

	public function getIp(){
		return E::getIp();
	}

	static public function getTopByComment($arrComment){
		if($arrComment['comment_parentid']==0){
			return $arrComment;
		}
		else{
			return self::theTopParent($arrComment);
		}
	}

	static public function theTopParent($arrComment){
		if($arrComment['comment_parentid']==0){
			return $arrComment;
		}
		$arrParentComment=CommentModel::F('comment_id=?',$arrComment['comment_parentid'])->asArray()->query();
		return $arrParentComment;
	}

	static public function getTopCommentsPage($nTheSearchId,$nEveryCommentNum=1,$sCommentRelationtype='',$nCommentRelationvalue=''){
		$arrCommentMap['comment_isshow']=1;
		$arrCommentMap['comment_parentid']=0;
		$arrCommentMap['comment_relationtype']=$sCommentRelationtype;
		$arrCommentMap['comment_relationvalue']=$nCommentRelationvalue;
		$arrCommentLists=self::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->query();
		$arrCommentIds=array();
		foreach($arrCommentLists as $nKey=>$oCommentList){
			$arrCommentIds[$nKey]=$oCommentList->comment_id;
		}
		$nTheSearchKey=false;
		foreach($arrCommentIds as $nKey=>$nCommentId){
			if($nCommentId==$nTheSearchId)$nTheSearchKey=$nKey;
		}
		if($nTheSearchKey===false){
			return false;
		}
		$nPage=ceil($nTheSearchKey/$nEveryCommentNum);
		if($nPage<1)$nPage=1;
		return $nPage;
	}

	static public function getCommentsUrl($arrComment,$nEveryCommentNum=1,$sCommentRelationtype='',$nCommentRelationvalue=''){
		$arrComment=self::getTopByComment($arrComment);
		$nPage=self::getTopCommentsPage($arrComment['comment_id'],$nEveryCommentNum,$sCommentRelationtype,$nCommentRelationvalue);
		if($nPage===false)
			return false;

		switch($arrComment['comment_relationtype']){
			case 'blog':
				return self::getBlogCommentUrl($arrComment,$nPage);
				break;
			case 'taotao':
				return self::getTaotaoCommentUrl($arrComment,$nPage);
				break;
			case 'upload':
				return self::getUploadCommentUrl($arrComment,$nPage);
				break;
			default:
				return self::getGuestbookCommentUrl($arrComment,$nPage);
				break;
		}
	}

	static public function getBlogCommentUrl($arrComment,$nPage){
		$oBlog=BlogModel::F('blog_id=?',$arrComment['comment_relationvalue'])->asArray()->query();
		if(empty($oBlog['blog_id'])){
			return false;
		}
		$arrExtend=array();
		$nNewpage=intval(G::getGpc('newpage','G'));
		if($nPage>1){
			$arrExtend=array('page'=>$nPage);
		}
		if($nNewpage>1){
			$arrExtend=array('page'=>$nNewpage);
		}
		$oBlogUrl=PageType_Extend::getBlogUrl($oBlog,$arrExtend);
		return $oBlogUrl."#comment-".$arrComment['comment_id'];
	}

	static public function getTaotaoCommentUrl($arrComment,$nPage){
		$oTaotao=TaotaoModel::F('taotao_id=?',$arrComment['comment_relationvalue'])->asArray()->query();
		if(empty($oTaotao['taotao_id'])){
			return false;
		}
		$arrExtend=array();
		if($nPage>1){
			$arrExtend=array('page'=>$nPage);
		}
		$oTaotaoUrl=PageType_Extend::getTaotaoUrl($oTaotao,$arrExtend);
		return $oTaotaoUrl."#comment-".$arrComment['comment_id'];
	}

	static public function getUploadCommentUrl($arrComment,$nPage){
		$oUpload=UploadModel::F('upload_id=?',$arrComment['comment_relationvalue'])->asArray()->query();
		if(empty($oUpload['upload_id'])){
			return false;
		}
		$arrExtend=array();
		if($nPage>1){
			$arrExtend=array('page'=>$nPage);
		}
		$oUploadUrl=PageType_Extend::getUploadUrl($oUpload,$arrExtend);
		return $oUploadUrl."#comment-".$arrComment['comment_id'];
	}

	static public function getGuestbookCommentUrl($arrComment,$nPage){
		$sExtend='';
		if($nPage>1){
			$sExtend='?page='.$nPage;
		}
		$oGuestbookUrl=G::U('guestbook/index'.$sExtend);
		return $oGuestbookUrl."#comment-".$arrComment['comment_id'];
	}

	static public function getCommentsEverynum($arrComment){
		switch($arrComment['comment_relationtype']){
			case 'blog':
				return Global_Extend::getOption('display_blog_comment_list_num');
				break;
			case 'upload':
				return Global_Extend::getOption('display_blog_upload_list_num');
				break;
			case 'taotao':
				return Global_Extend::getOption('display_blog_taotao_list_num');
				break;
			default:
				return Global_Extend::getOption('display_blog_guestbook_list_num');
				break;
		}
	}

	static public function getACommentUrl($arrComment){
		$nCommentEverynum=self::getCommentsEverynum($arrComment);
		return CommentModel::getCommentsUrl($arrComment,$nCommentEverynum,$arrComment['comment_relationtype'],$arrComment['comment_relationvalue']);
	}

}
