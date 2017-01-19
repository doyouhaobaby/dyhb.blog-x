<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   系统默认树状评论挂件($) */

!defined('DYHB_PATH') && exit;

class TreecommentWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'rss_img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/comments.gif')?STYLE_IMG_DIR.'/comments.gif':IMG_DIR.'/comments.gif'),
			'replyemail_img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/comment_replyemail.gif')?STYLE_IMG_DIR.'/comment_replyemail.gif':IMG_DIR.'/comment_replyemail.gif'),
			'mobile_img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/mobile.gif')?STYLE_IMG_DIR.'/mobile.gif':IMG_DIR.'/mobile.gif'),
			'replay'=>true,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['nofollow'])){$arrData['nofollow']=Global_Extend::getOption('comment_url_nofollow');}
		if(!isset($arrData['show_avatar'])){$arrData['show_avatar']=Global_Extend::getOption('show_comment_avatar');}
		if(!isset($arrData['avatar_size'])){$arrData['avatar_size']=Global_Extend::getOption('comment_avatar_size');}
		if(!isset($arrData['thread_comments_depth'])){$arrData['thread_comments_depth']=Global_Extend::getOption('thread_comments_depth');}
		if(!isset($arrData['dateformat'])){$arrData['dateformat']=Global_Extend::getOption('comment_dateformat');}
		return $this->renderTpl('',$arrData);
	}

}
