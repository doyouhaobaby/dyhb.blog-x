<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	邮件控制器($)*/

!defined('DYHB_PATH') && exit;

class MailController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['mail_subject']=array('like',"%".G::getGpc('mail_subject')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以管理使用本软件发送的邮件信息。','mail').'</p>'.
				'<p>'.G::L('邮件只能被删除，但不能被修改。','mail').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function send(){
		$nMailId=intval(G::getGpc('mail_id'));
		if(empty($nMailId)){
			$this->E(G::L('发送邮件的ID不能为空'));
		}
		$oMail=MailModel::F('mail_id=?',$nMailId)->query();
		if(empty($oMail['mail_id'])){
			$this->E(G::L('你选择的邮件不存在'));
		}
		$oMailSend=$oMail->getMailConnect();
		$oMail->sendAEmail($oMailSend,$oMail['mail_tomail'],$oMail['mail_subject'],($oMail['mail_htmlon']==0?strip_tags($oMail['mail_message']): $oMail['mail_message']),'admin',false);
		if($oMail->isError()){
			$this->E($oMail->getErrorMessage());
		}
		$this->S(G::L('发送邮件成功'));
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('在这里添加一封邮件，然后返回邮件列表，点击即可发送一封邮件。','mail').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

}
