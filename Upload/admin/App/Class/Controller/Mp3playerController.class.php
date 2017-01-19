<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Mp3播放器设置控制器($)*/

!defined('DYHB_PATH') && exit;

class Mp3playerController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function index(){
		$this->assign('arrOptions',$this->_arrOptions);
		$this->display();
	}

	public function update_config(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
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
		return '<p>'.G::L('这里是侧边栏mp3 播放器相关设置，动听音乐从这里开始。','mp3player').'</p>'.
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

}
