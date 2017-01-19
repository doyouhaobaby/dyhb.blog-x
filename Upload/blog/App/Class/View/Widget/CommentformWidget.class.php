<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   评论表单挂件($) */

!defined('DYHB_PATH') && exit;

class CommentformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'comment_header_name'=>G::L('发表评论：'),
			'comment_cancel_reply_name'=>G::L('取消回复'),
			'comment_name_name'=>G::L('昵称'),
			'comment_email_name'=>G::L('邮件地址(选填)'),
			'comment_url_name'=>G::L('个人主页(选填)'),
			'comment_submit_name'=>G::L('提交评论'),
			'comment_information_welcome'=>G::L('欢迎你！'),
			'comment_information_change'=>G::L('更改用户'),
			'comment_information_login'=>G::L('登录用户'),
			'logout_information_change'=>G::L('登出'),
			'coment_isshow'=>G::L('发送悄悄话'),
			'coment_isreplymail'=>G::L('回复时邮件通知'),
			'comment_relation_type'=>'',
			'comment_relation_value'=>'',
			'is_ajax'=>1,
			'show_comment_header'=>1,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['block_title'])){
			$arrData['block_title']=Global_Extend::getOption('widget_admin_name')?Global_Extend::getOption('widget_admin_name'):G::L('管理操作');
		}
		if(!isset($arrData['widget_title'])){
			$arrData['widget_title']=Global_Extend::getOption('widget_admin_titleshow');
		}
		$arrData['the_comment_name']=G::cookie('the_comment_name');
		$arrData['the_comment_url']=G::cookie('the_comment_url');
		$arrData['the_comment_email']=G::cookie('the_comment_email');
		$arrUserData=UserModel::M()->userData();
		if(!empty($arrUserData['user_id'])){
			$bLogin=true;
		}
		else{
			$bLogin=false;
		}
		$arrData['login']=$bLogin;
		if($bLogin===true){
			$arrData['the_comment_name']=!empty($arrUserData['user_nikename'])?$arrUserData['user_nikename']: $arrUserData['user_name'];
			$arrData['the_comment_url']=!empty($arrUserData['user_homepage'])?$arrUserData['user_homepage']: $arrData['the_comment_url'];
			$arrData['the_comment_email']=!empty($arrUserData['user_email'])?$arrUserData['user_email']: $arrData['the_comment_email'];
		}
		$arrData['seccode']=$this->getSeccode($arrData['comment_relation_type']);
		$arrData['emots']=$this->getEmot();
		return $this->renderTpl('',$arrData);
	}

	public function getSeccode($sCommentRelationType){
		if(Global_Extend::getOption('seccode')==0){
			return false;
		}
		if($sCommentRelationType){
			switch($sCommentRelationType){
				case 'blog':
					if(Global_Extend::getOption('blog_comment_seccode')==1){
						return true;
					}
					break;
				default:
					if(Global_Extend::getOption('blog_comment_seccode')==1){
						return true;
					}
					break;
			}
		}
		else{
			if(Global_Extend::getOption('blog_guestbook_seccode')==1){
				return true;
			}
		}
		return false;
	}

	public function getEmot(){
		$arrResult=Dyhb::cache('global_emot');
		if($arrResult===false){// 没有缓存，则更新一遍缓存
			Cache_Extend::global_emot('admin');
			Cache_Extend::global_emot('blog');
			$arrResult=Dyhb::cache('global_emot');
		}
		return $arrResult;
	}

}
