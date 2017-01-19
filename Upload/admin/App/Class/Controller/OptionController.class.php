<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	配置处理控制器($)*/

!defined('DYHB_PATH') && exit;

class OptionController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function index(){
		$arrOptionData=&$this->_arrOptions;
		$arrItems=array('maildefault','mailsend','mailserver','mailport','mailauth','mailfrom','mailauth_username','mailauth_password','maildelimiter');
		foreach($arrItems as $sItem){
			$arrOptionData[$sItem]=htmlspecialchars($arrOptionData[$sItem]);
		}

		$arrTimezones=array('CET','CST6CDT','Cuba','EET','Egypt','Eire','EST','EST5EDT','Etc/GMT','Etc/GMT+0',
							'Etc/GMT+1','Etc/GMT+10','Etc/GMT+11','Etc/GMT+12','Etc/GMT+2','Etc/GMT+3','Etc/GMT+4',
							'Etc/GMT+5','Etc/GMT+6','Etc/GMT+7','Etc/GMT+8','Etc/GMT+9','Etc/GMT-0','Etc/GMT-1',
							'Etc/GMT-10','Etc/GMT-11','Etc/GMT-12','Etc/GMT-13','Etc/GMT-14','Etc/GMT-2','Etc/GMT-3',
							'Etc/GMT-4','Etc/GMT-5','Etc/GMT-6','Etc/GMT-7','Etc/GMT-8','Etc/GMT-9','Etc/GMT0','Etc/Greenwich',
							'Etc/UCT','Etc/Universal','Etc/UTC','Etc/Zulu','Factory','GB','GB-Eire','GMT','GMT+0','GMT-0','GMT0',
							'Greenwich','Hongkong','HST','Iceland','Iran','Israel','Jamaica','Japan','Kwajalein','Libya','MET',
							'MST','MST7MDT','Navajo','NZ','NZ-CHAT','Poland','Portugal','PRC','PST8PDT','ROC','ROK',
							'Singapore','Turkey','UCT','Universal','UTC','W-SU','WET','Zulu','Asia/Chongqing','Asia/Shanghai',
							'Asia/Urumqi,Asia/Macao','Asia/Hong_Kong','Asia/Taipei','Asia/Singapore');
		$this->assign('arrTimezones',$arrTimezones);
		$this->assign('arrOptions',$arrOptionData);
		$this->display();
	}

	public function update_config(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			if($sKey=='pmlimit1day'
				or $sKey=='pmfloodctrl'
				or $sKey=='pmsendregdays'
				or $sKey=='admineverynum'){
				$val=intval($val);
			}

			if($sKey=='dateformat'){
				$val=str_replace(array('yyyy','mm','dd'),array('y','n','j'),strtolower($val));
			}
			if($sKey=='timeformat'){
				$val=$val==1?'H:i':'h:i A';
			}

			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->S(G::L('配置文件更新成功了！'));
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('系统的所有配置信息，你在这里都可以修改。系统预留了大量的开关，你可以更具实际需要开启相应的功能。','option').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function mail_check(){
		$sEmailFrom=G::getGpc('from','G');
		$sEmailTo=G::getGpc('to','G');
		if(empty($sEmailTo)){
			$this->E(G::L('邮件接收者不能为空！'));
		}
		$oMailModel=new MailModel();
		$oMailSend=$oMailModel->getMailConnect();
		if(!empty($sEmailFrom)&& $sEmailFrom!='no'){
			$oMailSend->setEmailFrom($sEmailFrom);
		}
		$sEmailSubject=$this->get_email_to_test_subject();
		$sEmailMessage=$this->get_email_to_test_message($oMailSend);
		$this->send_a_email($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage);
	}

	public function send_a_email($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage){
		$oMailSend->setEmailTo($sEmailTo );// 邮件去向
		$oMailSend->setEmailSubject($sEmailSubject );// 邮件主题
		$oMailSend->setEmailMessage($sEmailMessage,$oMailSend);// 邮件消息
		$oMailSend->send();
		if($oMailSend->isError()){
			$this->E($oMailSend->getErrorMessage());
		}
		$this->S(G::L('邮件成功发送！注意：使用PHP 函数 Mail函数或者PHP 函数 SMTP发送，虽然显示发送成功，但不保证能够收得到邮件。'));
	}

	public function get_email_to_test_message($oMailSend){
		$sLine=$this->get_mail_line($oMailSend);
		$sMessage=$this->get_email_to_test_subject()."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('这是系统发出的一封用于测试邮件是否设置成功的测试邮件。')."{$sLine}{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('消息来源：').Global_Extend::getOption('blog_name')."{$sLine}";
		$sMessage.=G::L('站点网址：').Global_Extend::getOption('blog_url')."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('程序支持：').Global_Extend::getOption('blog_program_name')." Blog " .BLOG_SERVER_VERSION. "  Release " .BLOG_SERVER_RELEASE."{$sLine}";
		$sMessage.=G::L('产品官网：').Global_Extend::getOption('blog_program_url')."{$sLine}";
		return $sMessage;
	}

	public function get_email_to_test_subject(){
		return G::L("我的朋友：【%s】您在博客（%s）测试邮件发送成功了！",'app',null,G::L('风随我动'),Global_Extend::getOption('blog_name'));
	}

	public function get_mail_line($oMailSend){
		return $oMailSend->getIsHtml()===true?'<br/>':"\r\n";
	}

}
