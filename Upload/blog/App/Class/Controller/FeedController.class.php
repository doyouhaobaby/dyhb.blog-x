<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Feed控制器($) */

!defined('DYHB_PATH') && exit;

class FeedController extends CommonController{

	public function rss(){
		$arrMap['blog_isshow']=1;
		$arrMap['blog_ispage']=0;
		$nRssNums=intval(Global_Extend::getOption('blog_rss1_num'));
		if(empty($nRssNums)){
			$nRssNums=1;
		}
		$arrBlogs=BlogModel::F($arrMap)->all()->order('blog_id DESC')->limit(0,$nRssNums)->query();
		$oRss=new Rss(Global_Extend::getOption('blog_name'),Global_Extend::getOption('blog_url'),Global_Extend::getOption('blog_description'));
		foreach($arrBlogs as $oBlog){
			$sTitle=$this->clear_title($oBlog->blog_title);
			$sBlogUrl=$this->get_really_url($oBlog);
			$sContent=$this->get_rss_body($oBlog);
			$oRss->addItem($sTitle,$sBlogUrl,$sContent);
		}
		$oRss->display();
	}

	public function rss2($nBlogId=0,$bReturn=false){
		$arrMap['blog_isshow']=1;
		$arrMap['blog_ispage']=0;
		if(empty($nBlogId)){
			$nBlogId=intval(G::getGpc('blog_id','G'));
		}
		if(empty($nBlogId)){
			$nCategoryId=intval(G::getGpc('cid','G'));
			if(!empty($nCategoryId)){
				$arrMap['category_id']=$nCategoryId;
			}
			$nRssNums=intval(Global_Extend::getOption('blog_rss2_num'));
			if(empty($nRssNums)){
				$nRssNums=1;
			}
			$arrBlogs=BlogModel::F($arrMap)->all()->order('blog_id DESC')->limit(0,$nRssNums)->query();
		}
		else{
			$arrMap['blog_id']=$nBlogId;
			$arrBlogs[]=BlogModel::F($arrMap)->query();
		}
		$oRss=new Rss2(Global_Extend::getOption('blog_name'),Global_Extend::getOption('blog_url'),Global_Extend::getOption('blog_description'));
		$oRss->setOption('_sAtomLink',$this->get_atom_link());
		foreach($arrBlogs as $oBlog){
			$sTitle=$this->clear_title($oBlog->blog_title);
			$sBlogUrl=$this->get_really_url($oBlog);
			$sContentEncoded=$this->get_rss_body($oBlog);
			$sComments=$sBlogUrl.'#comments';
			$sPubdate=$this->clear_pubtime($oBlog->blog_dateline);
			$sCreator=$this->get_creator($oBlog);
			$sCategory=$this->get_category($oBlog);
			$sGuid=$sBlogUrl;
			$sCommentRss=$this->get_comments_url($oBlog);
			$nCommentNum=$oBlog->blog_commentnum;
			$oRss->addItem2($sTitle,$sBlogUrl,$sComments,$sPubdate,$sCreator,$sCategory,$sGuid,$sContentEncoded ,$sContentEncoded,$sCommentRss,$nCommentNum);
		}
		if($bReturn===false){
			$oRss->display();
		}
		else{
			return $oRss;
		}
	}

	public function comments($sRelationtype='',$nRelationvalue='',$bReturn=false){
		$arrMap['comment_isshow']=1;
		$nCommentId=intval(G::getGpc('comment_id','G'));
		if(empty($nCommentId)){
			if(!empty($sRelationtype)){
				$arrMap['comment_relationtype']=$sRelationtype;
			}
			if(!empty($nRelationvalue)){
				$arrMap['comment_relationvalue']=$nRelationvalue;
			}
			$nRssNums=intval(Global_Extend::getOption('blog_rss_comment_num'));
			if(empty($nRssNums)){
				$nRssNums=1;
			}
			$arrComments=CommentModel::F($arrMap)->all()->asArray()->order('comment_id DESC')->limit(0,$nRssNums)->query();
		}
		else{
			$arrMap['comment_id']=$nCommentId;
			$arrComments[]=CommentModel::F($arrMap)->asArray()->query();
		}
		$oRss=new Rss2(Global_Extend::getOption('blog_name'),Global_Extend::getOption('blog_url'),Global_Extend::getOption('blog_description'));
		$oRss->setOption('_sAtomLink',$this->get_comment_atom_link());
		foreach($arrComments as $arrComment){
			$nCommentEverynum=CommentModel::getCommentsEverynum($arrComment);
			$sTitle=$arrComment['comment_name'];
			$sCommentUrl=$this->clear_url(self::get_host_header().CommentModel::getCommentsUrl($arrComment,$nCommentEverynum,$arrComment['comment_relationtype'],$arrComment['comment_relationvalue']));
			$sContentEncoded=$arrComment['comment_content'];
			$sComments=$sCommentUrl;
			$sPubdate=$this->clear_pubtime($arrComment['create_dateline']);
			$sCreator=$this->get_comment_creator($arrComment);
			$sCategory=G::L('评论');
			$sGuid=$sCommentUrl;
			$sCommentRss=$this->get_comments_comment_url($arrComment);
			$nCommentNum=1;
			$oRss->addItem2($sTitle,$sCommentUrl,$sComments,$sPubdate,$sCreator,$sCategory,$sGuid,$sContentEncoded ,$sContentEncoded,$sCommentRss,$nCommentNum);
		}
		if($bReturn===false){
			$oRss->display();
		}
		else{
			return $oRss;
		}
	}

	public function blog(){
		$nBlogId=intval(G::getGpc('id','G'));
		if(empty($nBlogId)){
			$this->page404();
		}
		$oRssBlog=$this->rss2($nBlogId,true);
		$sRssBody=$oRssBlog->getRssBody();
		$oRssComment=$this->comments('blog',$nBlogId,true);
		$sRssBody.=$oRssComment->getRssBody();
		$oRssBlog->display($sRssBody);
	}

	public function get_comment_creator($arrComment){
		return !empty($arrComment['comment_email'])? $arrComment['comment_email'] : 'user@domain.com';
	}

	public function get_atom_link(){
		return $this->clear_url($this->get_host_header().G::U('feed/rss2'));
	}

	public function get_comment_atom_link(){
		return $this->clear_url($this->get_host_header().G::U('feed/comments'));
	}

	public function get_category($oBlog){
		return !empty($oBlog->category->category_name)? $oBlog->category->category_name :G::L('未分类');
	}

	public function get_creator($oBlog){
		return !empty($oBlog->user->user_name)?(!empty($oBlog->user->user_nikename)? $oBlog->user->user_nikename : $oBlog->user->user_name):G::L('跌名');
	}

	public function clear_title($sTitle){
		return preg_replace("/&(.+?);/is","",$sTitle);
	}

	public function clear_pubtime($nTime){
		$sTime=gmdate('r',$nTime);
		$sTime=str_replace('  ',' ',$sTime);//PHP outputs two spaces between weekday and time
		return $sTime;
	}

	public function get_really_url($oBlog){
		return $this->clear_url($this->get_host_header().PageType_Extend::getBlogUrl($oBlog));
	}

	public function get_comments_comment_url($arrComment){
		return $this->clear_url($this->get_host_header().G::U('feed/comments?comment_id='.$arrComment['comment_id']));
	}

	public function get_comments_url($oBlog){
		return $this->clear_url($this->get_host_header().G::U('feed/comments?id='.$oBlog->blog_id));
	}

	public function get_rss_body($oBlog,$sExcertp='blog_rss1_excerpt'){
		if(Global_Extend::getOption($sExcertp)==1){
			$sContent=$oBlog->blog_excerpt;
		}
		else{
			$sContent=$oBlog->blog_content;
		}
		if(!empty($oBlog->blog_password)){
			$sContent=G::L('该文章已经加密，请访问后，输入密码可以访问');
		}
		else{
			$sContent=Ubb_Extend::convertUbb($sContent,0,1);
			$sContent=str_replace('[newpage]','',$sContent);
			$sContent=preg_replace("/\[emot\]([^ ]+?)\[\/emot\]/is",'',$sContent);
		}
		return $sContent;
	}

}
