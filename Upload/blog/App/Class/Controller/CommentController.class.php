<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   评论控制器($) */

!defined('DYHB_PATH') && exit;

class CommentController extends CommonController{

	protected $_oBlog=null;
	protected $_oTaotao=null;
	protected $_oUpload=null;
	protected $_nTotalCommentNum;

	public function feed(){
		$this->U('feed/comments');
	}

	public function index(){
		define('IS_COMMENTLIST',TRUE);
		define('CURSCRIPT','commentlist');
		$nSearchid=G::getGpc('searchid','G');
		$sTheOrderby=G::getGpc('orderby','G');
		$sOrderby=G::getGpc('ascdesc','G');
		$arrCommentIds=null;
		if($nSearchid){
			$oSearchindexModel=SearchindexModel::F('searchindex_id=?',$nSearchid)->query();
			if(empty($oSearchindexModel->searchindex_id)){
				$this->page404();
			}
			if($oSearchindexModel->searchindex_searchfrom !='comment'){
				$this->E(G::L('非评论搜索索引，我们无法完成搜索请求'));
			}
			$arrCommentIds=array('exp','IN('.$oSearchindexModel->searchindex_ids.')');
			$arrSearchstring=unserialize($oSearchindexModel->searchindex_searchstring);
			if(!empty($arrSearchstring['theorderby'])){
				$sTheOrderby=$arrSearchstring['theorderby'];
			}
			if(!empty($arrSearchstring['orderby'])){
				$sOrderby=$arrSearchstring['orderby'];
			}
		}
		$oCommentModelSelect=CommentModel::F();
		$arrCommentMap['comment_isshow']=1;
		$arrCommentMap['comment_parentid']=0;
		if($nSearchid){
			$arrCommentMap['comment_id']=&$arrCommentIds;
		}
		$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
		$nEveryCommentnum=Global_Extend::getOption('display_comments_list_num');
		$oPage=Page::RUN($nTotalComment,$nEveryCommentnum, G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		if(!empty($sTheOrderby) && !empty($sOrderby)){
			$sOrder="`{$sTheOrderby}` {$sOrderby}";
		}
		else{
			$sOrder="`comment_id` DESC";
		}
		$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order($sOrder)->limit($oPage->returnPageStart(), $nEveryCommentnum)->query();
		$arrAllCommentMap['comment_isshow']=1;
		$arrAllCommentMap['comment_parentid']=array('neq',0);
		if($nSearchid){
			$arrAllCommentMap['comment_id']=&$arrCommentIds;
		}
		$arrAllComments=CommentModel::F()->reset(DbSelect::WHERE)->where($arrAllCommentMap)->all()->order($sOrder)->query();
		$this->assign('arrAllComments',$arrAllComments);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrCommentLists',$arrCommentLists);
		$this->assign('nTotalComments',$nTotalComment);
		$this->assign('the_comment_description',Global_Extend::getOption('the_comment_description'));
		$this->display('comment');
	}

	public function post(){
		$this->system_allowed_comment();
		$sRelationType=G::getGpc('comment_relationtype','P');
		$nRelationValue=G::getGpc('comment_relationvalue','P');
		$this->the_relationtype_comment($sRelationType,$nRelationValue);
		$nCommentRelationtype=G::getGpc('comment_relationtype','P');
		if(empty($nCommentRelationtype)){
			if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('blog_comment_seccode')){
				$this->check_seccode(true);
			}
		}
		else{
			if(Global_Extend::getOption('seccode')&&Global_Extend::getOption('blog_guestbook_seccode')){
				$this->check_seccode(true);
			}
		}

		$oCommentModel=new CommentModel();
		$_POST['comment_content']=trim(strip_tags($_POST['comment_content']));
		$sCommentBanIp=trim(Global_Extend::getOption("comment_ban_ip"));
		$sOnlineip=E::getIp();
		if(Global_Extend::getOption("comment_banip_enable")&& $sCommentBanIp){
			$sCommentBanIp=str_replace('，', ',', $sCommentBanIp);
			$arrCommentBanIp=Dyhb::normalize(explode(',', $sCommentBanIp));
			if(is_array($arrCommentBanIp)&& count($arrCommentBanIp)){
				foreach($arrCommentBanIp as $sValueCommentBanIp){
					$sValueCommentBanIp=str_replace('\*', '.*', preg_quote($sValueCommentBanIp, "/"));
					if(preg_match("/^{$sValueCommentBanIp}/", $sOnlineip)){
						$this->E(G::L('您的IP%s已经被系统禁止发表评论','app',null,$sOnlineip));
					}
				}
			}
		}
		$sCommentName=trim(G::getGpc('comment_name','P'));
		if(empty($sCommentName)){
			$this->E(G::L('评论名字不能为空','app'));
		}
		$arrNamekeys=array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','$','(',')','%','@','+','?',';','^');
		foreach($arrNamekeys as $sNamekeys){
			if(strpos($sCommentName,$sNamekeys)!==false){
				$this->E(G::L('此评论名字包含不可接受字符或被管理员屏蔽,请选择其它名字','app'));
			}
		}
		$sCommentContent=trim(G::getGpc('comment_content','P'));
		$nCommentMinLen=intval(Global_Extend::getOption("comment_min_len"));
		if($nCommentMinLen&&strlen($sCommentContent)<$nCommentMinLen){
			$this->E(G::L('评论内容最少的字节数为%d','app',null,$nCommentMinLen));
		}
		$nCommentMaxLen=intval(Global_Extend::getOption("comment_max_len"));
		if($nCommentMaxLen&&strlen($sCommentContent)>$nCommentMaxLen){
			$this->E(G::L('评论内容最大的字节数为%d','app',null,$nCommentMaxLen));
		}
		$this->system_audit_comment($oCommentModel);
		$this->the_relationtype_audit_comment($oCommentModel,$sRelationType);
		$bDisallowedSpamWordToDatabase=Global_Extend::getOption('disallowed_spam_word_to_database')?true:false;
		if(Global_Extend::getOption("comment_spam_enable")){
			$nCommentSpamUrlNum=intval(Global_Extend::getOption('comment_spam_url_num'));
			if($nCommentSpamUrlNum){
				if(substr_count($sCommentContent,'http://')>=$nCommentSpamUrlNum){
					if($bDisallowedSpamWordToDatabase){
						$this->E(G::L('评论内容中出现的衔接%d数量超过了系统的限制','app',null,$nCommentSpamUrlNum));
					}
					else{
						$oCommentModel->comment_isshow=0;
					}
				}
			}

			$sSpamWords=trim(Global_Extend::getOption('comment_spam_words'));
			if($sSpamWords){
				$sSpamWords=str_replace('，',',',$sSpamWords);
				$arrSpamWords=Dyhb::normalize(explode(',',$sSpamWords));
				if(is_array($arrSpamWords) && count($arrSpamWords)){
					foreach($arrSpamWords as $sValueSpamWords){
						if($sValueSpamWords){
							if(preg_match("/".preg_quote($sValueSpamWords, '/')."/i", $sCommentContent)){
								if($bDisallowedSpamWordToDatabase){
									$this->E(G::L("你的评论内容包含系统屏蔽的词语%s",'app',null,$sValueSpamWords));
								}
								else{
									$oCommentModel->comment_isshow=0;
								}
								break;
							}
						}
					}
				}
			}
			$nCommentSpamContentSize=intval(Global_Extend::getOption('comment_spam_content_size'));
			if($nCommentSpamContentSize){
				if(strlen($sCommentContent)>=$nCommentSpamContentSize){
					if($bDisallowedSpamWordToDatabase){
						$this->E(G::L('评论内容最大的字节数为%d','app',null,$nCommentSpamContentSize));
					}
					else{
						$oCommentModel->comment_isshow=0;
					}
				}
			}
		}

		$nCommentPostSpace=intval(Global_Extend::getOption('comment_post_space'));
		if($nCommentPostSpace){
			$nLastPostTime=G::cookie('the_comment_time');
			if(CURRENT_TIMESTAMP-$nLastPostTime <=$nCommentPostSpace){
				$this->E(G::L('为防止灌水,发表评论时间间隔为%d秒','app',null,$nCommentPostSpace));
			}
		}
		$nCurrentTimeStamp=CURRENT_TIMESTAMP;
		$oTryComment=CommentModel::F("comment_name=? AND comment_content=? AND {$nCurrentTimeStamp}-create_dateline<86400 AND comment_ip=?",$sCommentName,$sCommentContent,$sOnlineip)->query();
		if(!empty($oTryComment['comment_id'])){
			$this->E(G::L('你提交的评论已经存在','app'));
		}
		if(Global_Extend::getOption('disallowed_all_english_word')){
			$sPattern='/[一-龥]/u';
			if(!preg_match_all($sPattern,$sCommentContent, $arrMatch)){
				if($bDisallowedSpamWordToDatabase){
					$this->E('You should type some Chinese word(like 你好)in your comment to pass the spam-check, thanks for your patience! '.G::L('您的评论中必须包含汉字!','app'));
				}
				else{
					$oCommentModel->comment_isshow=0;
				}
			}
		}
		$oCommentModel->save();
		if(!$oCommentModel->isError()){
			$this->after_send_comment($sRelationType,$nRelationValue);
			$this->send_cookie($oCommentModel);
			$this->comment_sendmail($oCommentModel);// 邮件通知
			Cache_Extend::front_widget_guestbook();
			Cache_Extend::front_widget_comment();
			Cache_Extend::front_widget_static(	);
			if($this->isAjax()){
				if(TEMPLATE_TYPE==='blog'||TEMPLATE_TYPE==='cms'){
					$arrCommentData=$oCommentModel->toArray();
					$sAjaxBackHtml=G::W('treecommentajaxback',array('comment'=>$arrCommentData),true);// 取得ajax html代码
					$arrCommentData['ajaxbackhtml']=$sAjaxBackHtml;
					$arrCommentData['commentnum']=$this->_nTotalCommentNum;// ajax 总评论
				}
				elseif(TEMPLATE_TYPE==='bbs'){
						$arrCommentData['jumpurl']=str_replace('#comment-'.$oCommentModel['comment_id'],($GLOBALS['_commonConfig_']['URL_MODEL']&&$GLOBALS['_commonConfig_']['URL_MODEL']!=3?'?':'&').'extra=new'.$oCommentModel['comment_id'].'#comment-'.$oCommentModel['comment_id'],CommentModel::getACommentUrl($oCommentModel->toArray()));
				}
				else{
					$this->E(G::L('你当前的模板方案不正确'));
				}
				$this->A($arrCommentData,G::L('评论保存成功！'),1);
			}
			else{
				$this->E('Comment need ajax!');
			}
		}
		else{
			$this->E($oCommentModel->getErrorMessage());
		}

	}

	public function system_allowed_comment(){
		if(Global_Extend::getOption("default_comment_status")!=1	)
		$this->E(G::L('系统关闭了评论功能！'));
	}

	public function the_relationtype_comment($sRelationType,$nRelationValue){
		switch($sRelationType){
			case 'blog':
				if(Global_Extend::getOption("blog_comment_status")!=1){
					$this->E(G::L('系统关闭了日志评论功能！'));
				}
				$oBlog=BlogModel::F('blog_id=?',$nRelationValue)->setColumns('blog_islock,blog_id,blog_commentnum')->query();
				if(!empty($oBlog->blog_islock)){
					$this->E(G::L('该篇日志已经被锁定，你无法评论！'));
				}
				$this->_oBlog=$oBlog;
				break;
			case 'taotao':
				if(Global_Extend::getOption("taotao_status")!=1){
					$this->E(G::L('系统关闭了心情评论功能！'));
			}
				$oTaotao=TaotaoModel::F('taotao_id=?',$nRelationValue)->setColumns('taotao_islock,taotao_id,taotao_commentnum')->query();
				if(!empty($oTaotao->taotao_islock)){
					$this->E(G::L('该篇心情已经被锁定，你无法评论！'));
				}
				$this->_oTaotao=$oTaotao;
				break;
			case 'upload':
				if(Global_Extend::getOption("upload_status")!=1){
					$this->E(G::L('系统关闭了附件评论功能！'));
				}
				$oUpload=UploadModel::F('upload_id=?',$nRelationValue)->setColumns('upload_islock,upload_id,upload_commentnum')->query();
				if(!empty($oUpload->upload_islock)){
					$this->E(G::L('该附件已经被锁定，你无法评论！'));
				}
				$this->_oUpload=$oUpload;
				break;
			default:
				if(Global_Extend::getOption("guestbook_status")!=1){
					$this->E(G::L('系统关闭了留言板功能！'));
				}
				break;
		}
	}

	public function system_audit_comment(&$oCommentModel){
		if(Global_Extend::getOption('audit_comment')==1){
			$oCommentModel->comment_isshow=0;
		}
	}

	public function the_relationtype_audit_comment(&$oCommentModel,$sRelationType){
		switch($sRelationType){
			case 'blog':
				if(Global_Extend::getOption('audit_blog_comment')==1){
					$oCommentModel->comment_isshow=0;
				}
				break;
			case 'taotao':
				if(Global_Extend::getOption('audit_taotao_comment')==1){
					$oCommentModel->comment_isshow=0;
				}
				break;
			case 'upload':
				if(Global_Extend::getOption('audit_upload_comment')==1){
					$oCommentModel->comment_isshow=0;
				}
				break;
			default:
				if(Global_Extend::getOption('audit_blog_guestbook')==1){
					$oCommentModel->comment_isshow=0;
				}
				break;
		}
	}

	public function after_send_comment($sRelationType,$nRelationValue){
		switch($sRelationType){
			case 'blog':
				$oBlog=$this->_oBlog;
				$this->_nTotalCommentNum=$oBlog->blog_commentnum=$oBlog->blog_commentnum+1;
				$oBlog->blog_lastpost=serialize(array('comment_id'=>$nRelationValue,'create_dateline'=>CURRENT_TIMESTAMP,'user_name'=>($GLOBALS['___login___']===false ?G::L('跌名'):$GLOBALS['___login___']['user_name']),'user_id'=>($GLOBALS['___login___']===false ?-1:$GLOBALS['___login___']['user_id'])));
				$oBlog->save(0,'update');
				if($oBlog->isError()){
					$this->E($oBlog->getErrorMessage());
				}
				if(G::getGpc('category_id','P')){
					$oCategory=CategoryModel::F('category_id=?', G::getGpc('category_id','P'))->query();
					$oCategory->category_comments=$oCategory->category_comments+1;
					$oCategory->category_todaycomments=$oCategory->category_todaycomments+1;
					$oCategory->save(0,'update');
					if($oCategory->isError()){
						$this->E($oCategory->getErrorMessage());
					}
					Cache_Extend::front_widget_category();
				}
				break;
			case 'taotao':
				$oTaotao=$this->_oTaotao;
				$this->_nTotalCommentNum=$oTaotao->taotao_commentnum=$oTaotao->taotao_commentnum+1;
				$oTaotao->save(0,'update');
				if($oTaotao->isError()){
					$this->E($oTaotao->getErrorMessage());
				}
				break;
			case 'upload':
				$oUpload=$this->_oUpload;
				$this->_nTotalCommentNum=$oUpload->upload_commentnum=$oUpload->upload_commentnum+1;
				$oUpload->save(0,'update');
				if($oUpload->isError()){
					$this->E($oUpload->getErrorMessage());
				}
				break;
			default:
				$arrCommentMap['comment_isshow']=1;
				$arrCommentMap['comment_parentid']=0;
				$arrCommentMap['comment_relationtype']='';
				$arrCommentMap['comment_relationvalue']='';
				$this->_nTotalCommentNum=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
				break;
		}

		if($GLOBALS['___login___']){
			$oUser=UserModel::F('user_id=?',$GLOBALS['___login___']['user_id'])->query();
			$oUser->user_comments=$oUser->user_comments+1;
			$oUser->save(0,'update');
			if($oUser->isError()){
				$this->E($oUser->getErrorMessage());
			}
		}
		$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
		$oCacheData->countcache_todaynum=$oCacheData->countcache_todaynum +1;
		$oCacheData->countcache_postsnum=$oCacheData->countcache_postsnum+1;
		$oCacheData->save(0,'update');
		if($oCacheData->isError()){
			$this->E($oCacheData->getErrorMessage());
		}
	}

	public function send_cookie($oCommentModel){
		G::cookie('the_comment_name',$oCommentModel->comment_name,86400);
		G::cookie('the_comment_url',$oCommentModel->comment_url,86400);
		G::cookie('the_comment_email',$oCommentModel->comment_email,86400);
		G::cookie('the_comment_time',CURRENT_TIMESTAMP,86400);
	}

	public function comment_sendmail($oCommentModel){
		$bSendMail=false;
		$bSendMailAdmin=false;
		$bSendMailAuthor=false;
		$sAdminEmail=Global_Extend::getOption('admin_email');
		if(Global_Extend::getOption('comment_mail_to_admin')==1 && !empty($sAdminEmail)){
			$bSendMailAdmin=true;
		}
		if(Global_Extend::getOption('comment_mail_to_author')==1 && !empty($oCommentModel->comment_parentid)){
			$bSendMailAuthor=true;
		}
		if($bSendMailAdmin || $bSendMailAuthor){
			$bSendMail=true;
		}
		if($bSendMail===false){
			return ;
		}
		$oMailModel=new MailModel();
		$oMailSend=$oMailModel->getMailConnect();
		if($bSendMailAdmin===true){
			$sEmailTo=&$sAdminEmail;
			$sEmailSubject=$this->get_email_to_admin_subject($oCommentModel);
			$sEmailMessage=$this->get_email_to_admin_message($oCommentModel,$oMailSend);
			$this->send_a_email($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage);
		}

		if($bSendMailAuthor===true){
			$oCommentParent=CommentModel::F('comment_id=?',$oCommentModel->comment_parentid)->query();
			if($oCommentParent->comment_isreplymail==1 &&!empty($oCommentParent->comment_email)){
				$sEmailTo=&$oCommentParent->comment_email;
				$sEmailSubject=$this->get_email_to_author_subject($oCommentParent);
				$sEmailMessage=$this->get_email_to_author_message($oCommentParent,$oCommentModel,$oMailSend);
				$this->send_a_email($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage);
			}
		}
		return;
	}

	public function send_a_email($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage){
		$oMail=new MailModel();
		$oMail=$oMail->sendAEmail($oMailSend,$sEmailTo,$sEmailSubject,$sEmailMessage,'blog');
		if($oMail->isError()){
			$this->E($oMail->getErrorMessage());
		}
	}

	public function get_email_to_admin_subject($oCommentModel){
		return G::L('你的朋友【%s】在您的博客（%s）留言了！','app',null,$oCommentModel->comment_name,Global_Extend::getOption('blog_name'));
	}

	public function get_email_to_admin_message($oCommentModel,$oMailSend){
		$sLine=$this->get_mail_line($oMailSend);
		$sMessage=$this->get_email_to_admin_subject($oCommentModel)."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=$oCommentModel->comment_content."{$sLine}{$sLine}";
		$sMessage.=G::L('请进入下面链接查看留言：')."{$sLine}";
		$sMessage.=$this->clear_url($this->get_host_header().CommentModel::getACommentUrl($oCommentModel->toArray()))."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('名字：').$oCommentModel->comment_name."{$sLine}";
		if(!empty($oCommentModel->comment_email)){
			$sMessage.=G::L('E-mail：').$oCommentModel->comment_email."{$sLine}";
		}
		if(!empty($oCommentModel->comment_url)){
			$sMessage.=G::L('主页：').$oCommentModel->comment_url."{$sLine}";
		}
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('消息来源：').Global_Extend::getOption('blog_name')."{$sLine}";
		$sMessage.=G::L('站点网址：').Global_Extend::getOption('blog_url')."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('程序支持：').Global_Extend::getOption('blog_program_name')." Blog " .BLOG_SERVER_VERSION. "	Release " .BLOG_SERVER_RELEASE."{$sLine}";
		$sMessage.=G::L('产品官网：').Global_Extend::getOption('blog_program_url')."{$sLine}";
		return $sMessage;
	}

	public function get_email_to_author_subject($oCommentModel){
		return G::L("我的朋友：【%s】您在博客（%s）发表的评论被回复了！",'app',null,$oCommentModel->comment_name,Global_Extend::getOption('blog_name'));
	}

	public function get_email_to_author_message($oCommentModel,$oCommentNew,$oMailSend){
		$sLine=$this->get_mail_line($oMailSend);
		$sMessage=$this->get_email_to_author_subject($oCommentModel).$sLine;
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('您的评论：')."{$sLine}";
		$sMessage.=$oCommentModel->comment_content."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.="【".$oCommentModel->comment_name."】".G::L('回复说：')."{$sLine}";
		$sMessage.=$oCommentNew->comment_content."{$sLine}{$sLine}";
		$sMessage.=G::L('请进入下面链接查看留言：')."{$sLine}";
		$sMessage.=$this->clear_url($this->get_host_header().CommentModel::getACommentUrl($oCommentModel->toArray()))."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('名字：').$oCommentModel->comment_name."{$sLine}";
		if(!empty($oCommentModel->comment_email)){
			$sMessage.=G::L('E-mail：').$oCommentModel->comment_email."{$sLine}";
		}
		if(!empty($oCommentModel->comment_url)){
			$sMessage.=G::L('主页：').$oCommentModel->comment_url."{$sLine}";
		}
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('消息来源：').Global_Extend::getOption('blog_name')."{$sLine}";
		$sMessage.=G::L('站点网址：').Global_Extend::getOption('blog_url')."{$sLine}";
		$sMessage.="-----------------------------------------------------{$sLine}";
		$sMessage.=G::L('程序支持：').Global_Extend::getOption('blog_program_name')." Blog " .BLOG_SERVER_VERSION. "  Release " .BLOG_SERVER_RELEASE."{$sLine}";
		$sMessage.=G::L('产品官网：').Global_Extend::getOption('blog_program_url')."{$sLine}";
		return $sMessage;
	}

	public function get_mail_line($oMailSend){
		return $oMailSend->getIsHtml()===true?'<br/>':"\r\n";
	}

}
