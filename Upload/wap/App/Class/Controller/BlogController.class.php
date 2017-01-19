<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	wap Blog控制器($) */

!defined('DYHB_PATH') && exit;

class BlogController extends CommonController{

	public function show(){
		$nId=intval(G::getGpc('id','G'));
		if(empty($nId)){
			$this->page404();
		}
		$arrMap=array();
		$arrMap['blog_password']='';
		$arrMap['blog_id']=$nId;
		$arrMap['blog_ispage']=0;
		$oBlog=BlogModel::F($arrMap)->query();
		if(empty($oBlog->blog_id)){
			$this->page404();
		}
		$oBlog->blog_viewnum=$oBlog->blog_viewnum+1;
		$oBlog->save(0,'update');
		$nNewpage=G::getGpc('newpage','G');
		$nNewpage=empty($nNewpage)?1: intval($nNewpage);
		if(strpos($oBlog['blog_content'],'[newpage]')!==false){
			$arrContentPieces=Blog_Extend::blogContentNewpage($oBlog,'page');
			$sNewpageNavbar=count($arrContentPieces)==2?'':$arrContentPieces['newpagenav'];
			if($nNewpage>1){
				$oBlog->blog_content=$arrContentPieces[$nNewpage-1];
			}
			else{
				$oBlog->blog_content=$arrContentPieces[0];
			}
		}
		else{
			$sNewpageNavbar='';
		}
		$this->assign('sNewpageNavbar',$sNewpageNavbar);
		$oBlog->blog_content=strip_tags(Ubb_Extend::convertUbb($oBlog->blog_content,1));
		$oBlog->blog_content =Global_Extend::backword($oBlog->blog_content);
		$this->assign('nNewpage',$nNewpage);
		$this->assign('oBlog',$oBlog);
		$this->display('single');
	}

	public function index(){
		$nTagId=intval(G::getGpc('tag','G'));
		$nCategoryId=intval(G::getGpc('cid','G'));
		$nRecordId=intval(G::getGpc('record','G'));
		$nUserId=intval(G::getGpc('uid','G'));
		$nPage=intval(G::getGpc('page','G'));
		$arrMap=array();
		$arrMap['blog_ispage']=0; // 去除页面
		$arrMap['blog_isshow']=1; // 只允许公开的日志
		$arrMap['blog_password']='';//除掉密码
		if(!empty($nTagId)){
			$sBlogIds=TagModel::F()->query()->getBlogIdStrByTagId($nTagId);
			if(empty($sBlogIds)) {$this->page404();}
			$arrMap['blog_id']=array('in',$sBlogIds);
			$this->assign('oTag',TagModel::F('tag_id=?',$nTagId)->query());
			define('IS_TAG',TRUE);
		}
		elseif(!empty($nCategoryId)){
			$arrMap['category_id']=$nCategoryId;
			if($arrMap['category_id']==-1){
				$oCategory=-1;
			}
			else{
				$oCategory=CategoryModel::F('category_id=?',$arrMap['category_id'])->query();
				if(!empty($oCategory['category_gotourl']))
					$this->page404();
			}
			$this->assign('oCategory',$oCategory);
			define('IS_CATEGORY',TRUE);
		}
		elseif(!empty($nRecordId)){
			$arrSqlTime=Date::getTheFirstOfYearOrMonth($nRecordId);
			$arrMap['blog_dateline']=array('between',array($arrSqlTime[0],$arrSqlTime[1]));
			$this->assign('sRecord',$nRecordId);
			define('IS_RECORD',TRUE);
		}
		elseif(!empty($nUserId)){
			$arrMap['user_id']=intval($nUserId);
			if($arrMap['user_id']==-1){
				$oUser=-1;
			}
			else{
				$oUser=UserModel::F('user_id=?',$arrMap['user_id'])->query();
			}
			$this->assign('oUser',$oUser);
			define('IS_USER',TRUE);
		}
		else{
			if($nPage<2){
				Global_Extend::updateCountCacheData();
				define('IS_HOME',TRUE);
			}
		}
		$nTotalRecord=BlogModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('wap_display_blog_list_num');
		$oPage=WapPage::RUN($nTotalRecord,$nEverynum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$sOrder="`blog_istop` DESC,`blog_id` DESC";
		$arrBlogLists=BlogModel::F()->where($arrMap)->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrBlogLists',$arrBlogLists);
		$this->assign('nBlogTitleCutnum',Global_Extend::getOption('wap_blog_cutnum'));
		$this->display('list');
	}

	public function login(){
		if($GLOBALS['___login___']!==false){
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('你已经登录过了。'));
		}
		$this->display('login');
	}

	public function checklogin(){
		if($GLOBALS['___login___']!==false){
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('你已经登录过了。'));
		}
		$sUserName=G::getGpc('user_name','P');
		$sPassword=G::getGpc('user_password','P');
		if(empty($sUserName)){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('帐号或者E-mail不能为空！'));
		}
		elseif(empty($sPassword)){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('密码不能为空！'));
		}
		Check::RUN();
		if(Check::C($sUserName,'email')){
			$bEmail=true;
			unset($_POST['user_name']);
		}
		else{
			$bEmail=false;
		}
		$oUser=UserModel::M()->checkLogin($sUserName,$sPassword,$bEmail);
		$oLoginlog=new LoginlogModel();
		$oLoginlog->loginlog_user=$sUserName;
		$oLoginlog->login_application='blog';
		if(G::isImplementedTo($oUser,'IModel')){
			$oLoginlog->user_id=$oUser->user_id;
		}
		if(UserModel::M()->isBehaviorError()){
			$oLoginlog->loginlog_status=0;
			$oLoginlog->save();
			if($oLoginlog->isError()){
				$this->assign('__JumpUrl__',G::U('blog/login'));
				$this->E($oLoginlog->getErrorMessage());
			}
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		else{
			if($oUser->isError()){
				$oLoginlog->loginlog_status=0;
				$oLoginlog->save();
				if($oLoginlog->isError()){
					$this->assign('__JumpUrl__',G::U('blog/login'));
					$this->E($oLoginlog->getErrorMessage());
				}
				$this->assign('__JumpUrl__',G::U('blog/login'));
				$this->E($oUser->getErrorMessage());
			}
			$oLoginlog->loginlog_status=1;
			$oLoginlog->save();
			if($oLoginlog->isError()){
				$this->assign('__JumpUrl__',G::U('blog/login'));
				$this->E($oLoginlog->getErrorMessage());
			}
			$this->assign('__JumpUrl__',G::U('blog/admin'));
			$this->S(G::L('Hello %s,你成功登录！','app',null,$sUserName));
		}
	}

	public function the_top($arrBlog,$arrData=array()){
		return Blog_Extend::theTop($arrBlog,$arrData);
	}

	public function the_mobile($arrBlog,$arrData=array()){
		return Blog_Extend::theMobile($arrBlog,$arrData);
	}

	public function the_comment_mobile($arrComment,$arrData=array()){
		return Blog_Extend::theCommentMobile($arrComment,$arrData);
	}

	public function the_taotao_mobile($arrTaotao,$arrData=array()){
		return Blog_Extend::theTaotaoMobile($arrTaotao,$arrData);
	}

	public function comment(){
		$nBlogId=intval(G::getGpc('blog_id','G'));
		$nTaotaoId=intval(G::getGpc('taotao_id','G'));
		$nUploadId=intval(G::getGpc('upload_id','G'));
		$nPage=intval(G::getGpc('page','G'));
		$arrCommentMap['comment_isshow']=1;
		$nEveryCommentnum=Global_Extend::getOption('wap_display_comment_list_num');
		if(!empty($nBlogId)){
			$arrCommentMap['comment_relationtype']='blog';
			$arrCommentMap['comment_relationvalue']=$nBlogId;
			define('IS_BLOG',TRUE);
			$this->assign('nBlogId',$nBlogId);
		}
		elseif(!empty($nTaotaoId)){
			$arrCommentMap['comment_relationtype']='taotao';
			$arrCommentMap['comment_relationvalue']=$nTaotaoId;
			define('IS_TAOTAO',TRUE);
			$this->assign('nTaotaoId',$nTaotaoId);
		}
		elseif(!empty($nUploadId)){
			$arrCommentMap['comment_relationtype']='upload';
			$arrCommentMap['comment_relationvalue']=$nUploadId;
			define('IS_UPLOAD',TRUE);
			$this->assign('nUploadId',$nUploadId);
		}
		$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
		$oPage=WapPage::RUN($nTotalComment,$nEveryCommentnum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrCommentLists',$arrCommentLists);
		$this->display('comment');
	}

	public function add_comment(){
		$this->system_allowed_comment();
		$sRelationType=G::getGpc('comment_relationtype','P');
		$nRelationValue=G::getGpc('comment_relationvalue','P');
		$this->the_relationtype_comment($sRelationType,$nRelationValue);
		$oCommentModel=new CommentModel();
		$sCommentContent=$_POST['comment_content'] =	trim(strip_tags($_POST['comment_content']));
		$sCommentName=trim($_POST['comment_name']);
		if(empty($sCommentName)){
			$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
			$this->E(G::L('评论名字不能为空'));
		}
		if(strlen($sCommentName)>25){
			$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
			$this->E(G::L('评论名字的最大字符数为25'));
		}
		if(empty($sCommentContent)){
			$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
			$this->E(G::L('评论的内容不能为空！'));
		}
		$this->system_audit_comment($oCommentModel);
		$this->the_relationtype_audit_comment($oCommentModel,$sRelationType);
		$oCommentModel->comment_ismobile =1;
		$oCommentModel->save();
		if(!$oCommentModel->isError()){
			$this->after_send_comment($sRelationType,$nRelationValue);
			Cache_Extend::front_widget_guestbook();
			Cache_Extend::front_widget_comment();
			Cache_Extend::front_widget_static();
			$sJumpUrl=$this->get_comment_url($oCommentModel->toArray());
			$this->assign('__JumpUrl__',$sJumpUrl);
			$this->S(G::L('评论成功'));
		}
		else{
			$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
			$this->E($oCommentModel->getErrorMessage());
		}
	}

	public function get_comment_url($arrComment=array(),$bPost=false){
		if($bPost===true){
			$arrComment['comment_relationtype']=G::getGpc('comment_relationtype','P');
			$arrComment['comment_relationvalue']=G::getGpc('comment_relationvalue','P');
		}
		if($arrComment['comment_relationtype']=='blog'){//日志
			return G::U('blog/show?id='.$arrComment['comment_relationvalue']);
		}
		elseif($arrComment['comment_relationtype']=='upload'){//附件
			return G::U('blog/singleupload?id='.$arrComment['comment_relationvalue']);
		}
		elseif($arrComment['comment_relationtype']=='taotao'){//心情
			return G::U('blog/singletaotao?id='.$arrComment['comment_relationvalue']);
		}
		else{//心情
			return G::U('blog/comment');
		}
	}

	public function system_allowed_comment(){
		if(Global_Extend::getOption("default_comment_status")!=1){
			$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
			$this->E(G::L('系统关闭了评论功能！'));
		}
	}

	public function the_relationtype_comment($sRelationType,$nRelationValue){
		switch($sRelationType){
			case 'blog':
				if(Global_Extend::getOption("blog_comment_status")!=1){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('系统关闭了日志评论功能！'));
				}
				$oBlog=BlogModel::F('blog_id=?',$nRelationValue)->setColumns('blog_islock,blog_id,blog_commentnum')->query();
				if(!empty($oBlog->blog_islock)){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('该篇日志已经被锁定，你无法评论！'));
				}
				$this->_oBlog=$oBlog;
				break;
			case 'taotao':
				if(Global_Extend::getOption("taotao_status")!=1){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('系统关闭了心情评论功能！'));
				}
				$oTaotao=TaotaoModel::F('taotao_id=?',$nRelationValue)->setColumns('taotao_islock,taotao_id,taotao_commentnum')->query();
				if(!empty($oTaotao->taotao_islock)){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('该篇心情已经被锁定，你无法评论！'));
				}
				$this->_oTaotao=$oTaotao;
				break;
			case 'upload':
				if(Global_Extend::getOption("upload_status")!=1){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('系统关闭了附件评论功能！'));
				}
				$oUpload=UploadModel::F('upload_id=?',$nRelationValue)->setColumns('upload_islock,upload_id,upload_commentnum')->query();
				if(!empty($oUpload->upload_islock)){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('该附件已经被锁定，你无法评论！'));
				}
				$this->_oUpload=$oUpload;
				break;
			default:
				if(Global_Extend::getOption("guestbook_status")!=1){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E(G::L('系统关闭了留言板功能！'));
				}
				break;
		}
	}

	public function system_audit_comment(&$oCommentModel){
		if(Global_Extend::getOption('audit_comment')==1)
		$oCommentModel->comment_isshow=0;
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
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E($oBlog->getErrorMessage());
				}
				if(G::getGpc('category_id','P')){
					$oCategory=CategoryModel::F('category_id=?',G::getGpc('category_id','P'))->query();
					$oCategory->category_comments=$oCategory->category_comments+1;
					$oCategory->category_todaycomments=$oCategory->category_todaycomments+1;
					$oCategory->save(0,'update');
					if($oCategory->isError()){
						$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
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
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E($oTaotao->getErrorMessage());
				}
			break;
			case 'upload':
				$oUpload=$this->_oUpload;
				$this->_nTotalCommentNum=$oUpload->upload_commentnum=$oUpload->upload_commentnum+1;
				$oUpload->save(0,'update');
				if($oUpload->isError()){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
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
				$oUser->user_comments=$oUser->user_comments=$oUser->user_comments+1;
				$oUser->save(0,'update');
				if($oUser->isError()){
					$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
					$this->E($oUser->getErrorMessage());
				}
			}
			$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
			$oCacheData->countcache_todaynum=$oCacheData->countcache_todaynum+1;
			$oCacheData->countcache_postsnum=$oCacheData->countcache_postsnum+1;
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->assign('__JumpUrl__',$this->get_comment_url(array(),true));
				$this->E($oCacheData->getErrorMessage());
			}
	}

	public function the_category($oCategory){
		return Blog_Extend::theCategory($oCategory);
	}

	public function the_author($oUser){
		return Blog_Extend::theAuthor($oUser);
	}

	public function the_tag($oBlog,$bReturn=true){
		$oTags=$this->getTagsByBlogId($oBlog->blog_id);
		if(!is_array($oTags)){
			return false;
		}
		else{
			return Blog_Extend::theTag($oTags,$bReturn);
		}
	}

	public function getTagsByBlogId($nBlogId){
		return TagModel::F()->query()->getTagsByBlogId($nBlogId);
	}

	public function the_previous_post($oBlog,$arrData){
		return Blog_Extend::thePreviousPost($oBlog,$arrData);
	}

	public function the_next_post($oBlog,$arrData){
		return Blog_Extend::theNextPost($oBlog,$arrData);
	}

	public function admin(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		$this->display('admin');
	}

	public function option(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		if($GLOBALS['___login___']['user_id']!=1){
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('你不是超级管理员,不能修改博客配置'));
		}
		$this->display('option');
	}

	public function change_option(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		if($GLOBALS['___login___']['user_id']!=1){
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('你不是超级管理员,不能修改博客配置'));
		}

		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
				$bResult=OptionModel::uploadOption($sKey,$val);
				if($bResult===false){
					$this->assign('__JumpUrl__',__APP__);
					$this->E(G::L('配置数据更新失败'));
				}
			}
			$this->S(G::L('配置数据更新成功'));
	}

	public function add_blog(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		$this->get_the_category();
		$this->display('addblog');
	}

	public function get_the_category(){
		$oCategoryTree=new TreeCategory();
		foreach($this->get_blog_category() as $oCategory){
			$oCategoryTree->setNode($oCategory->category_id,$oCategory->category_parentid,$oCategory->category_name);
		}
		$this->assign('oCategoryTree',$oCategoryTree);
	}

	public function get_blog_category(){
		return CategoryModel::F()->order('category_id ASC,category_compositor DESC')->all()->query();
	}

	public function saveblog(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		if($GLOBALS['___login___']===false &&Global_Extend::getOption('allowed_publish')==0){
			$this->assign('__JumpUrl__',G::U('blog/admin'));
			$this->E(G::L('系统已经关闭了投稿功能！'));
		}
		$sBlogTitle=trim($_POST['blog_title']);
		$sBlogContent=trim($_POST['blog_content']);
		if(empty($sBlogTitle)){
			$this->assign('__JumpUrl__',G::U('blog/add_blog'));
			$this->E(G::L('日志标题不能为空'));
		}
		if(empty($sBlogContent)){
			$this->assign('__JumpUrl__',G::U('blog/add_blog'));
			$this->E(G::L('日志内容不能为空'));
		}
		if(strlen($sBlogContent)>300){
			$this->assign('__JumpUrl__',G::U('blog/add_blog'));
			$this->E(G::L('日志最大长度为300'));
		}
		$oBlog=new BlogModel();
		$oBlog->blog_dateline=CURRENT_TIMESTAMP;
		$oBlog->blog_ismobile=1;
		if(preg_match('|<p>(.*?)</p>|is',G::getGpc('blog_content'),$arrMatches)){
			$sBlogExcerpt=trim(strip_tags($arrMatches[1]));
		}
		if(empty($sBlogExcerpt))
		$sBlogExcerpt=String::subString(trim(strip_tags(G::getGpc('blog_content'))),0,255);
		$sBlogExcerpt='<p>'.$sBlogExcerpt.'</p>';
		$oBlog->blog_excerpt=$sBlogExcerpt;
		$oBlog->blog_lastpost=serialize(array('comment_id'=>-1,'create_dateline'=>CURRENT_TIMESTAMP,'user_name'=>($GLOBALS['___login___']===false ?G::L('跌名'):$GLOBALS['___login___']['user_name']),'user_id'=>($GLOBALS['___login___']===false ?-1:$GLOBALS['___login___']['user_id'])));
		$oBlog->save();
		Cache_Extend::front_widget_yearhotpost();
		Cache_Extend::front_widget_yearcommentpost();
		Cache_Extend::front_widget_recentpost();
		Cache_Extend::front_widget_randpost();
		Cache_Extend::front_widget_pagepost();
		Cache_Extend::front_widget_monthhotpost();
		Cache_Extend::front_widget_monthcommentpost();
		Cache_Extend::front_widget_hotpost();
		Cache_Extend::front_widget_dayhotpost();
		Cache_Extend::front_widget_daycommentpost();
		Cache_Extend::front_widget_archive();
		Cache_Extend::front_widget_static();
		$sTag=trim(G::getGpc('tag'));
		if(!empty($sTag)){
			$bResult=TagModel::F()->query()->addTag($sTag,$oBlog->blog_id);
			if($bResult===false){
				$this->assign('__JumpUrl__',G::U('blog/add_blog'));
				$this->E(G::L('标签插入失败!'));
			}
			Cache_Extend::front_widget_hottag();
			Cache_Extend::front_widget_static();
		}
		if($oBlog->isError()){
			$this->assign('__JumpUrl__',G::U('blog/add_blog'));
			$this->E($oBlog->getErrorMessage());
		}
		else{
			if(G::getGpc('category_id','P')!=-1){
				$oCategory=CategoryModel::F('category_id=?',G::getGpc('category_id','P'))->query();
				$oCategory->category_blogs=$oCategory->category_blogs+1;
				$oCategory->category_todaycomments=$oCategory->category_todaycomments+1;
				$oCategory->category_lastpost=serialize(array('blog_id'=>$oBlog['blog_id'],'blog_title'=>$oBlog['blog_title'],'blog_dateline'=>$oBlog['blog_dateline'],'user_name'=>($GLOBALS['___login___']===false ?G::L('跌名'):$GLOBALS['___login___']['user_name']),'user_id'=>($GLOBALS['___login___']===false ?-1:$GLOBALS['___login___']['user_id'])));
				$oCategory->save(0,'update');
				if($oCategory->isError()){
					$this->assign('__JumpUrl__',G::U('blog/add_blog'));
					$this->E($oCategory->getErrorMessage());
				}
				Cache_Extend::front_widget_category();
			}

			if($GLOBALS['___login___']){
				$oUser=UserModel::F('user_id=?',$GLOBALS['___login___']['user_id'])->query();
				$oUser->user_blogs=$oUser->user_blogs+1;
				$oUser->save(0,'update');
				if($oUser->isError()){
					$this->assign('__JumpUrl__',G::U('blog/add_blog'));
					$this->E($oUser->getErrorMessage());
				}
			}
			$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
			$oCacheData->countcache_todaynum=$oCacheData->countcache_todaynum+1;
			$oCacheData->countcache_topicsnum=$oCacheData->countcache_topicsnum+1;
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->assign('__JumpUrl__',G::U('blog/add_blog'));
				$this->E($oCacheData->getErrorMessage());
			}
			$this->assign('__JumpUrl__',Url_Extend::getBlogUrl($oBlog));
			$this->S(G::L('发布日志成功'));
		}
	}

	public function user(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		$nUserId=intval(G::getGpc('id','G'));
		if(!empty($nUserId)){
			if($GLOBALS['___login___']['user_id']!=$nUserId){
				$this->assign('__JumpUrl__',__APP__);
				$this->E(G::L('你不能修改的别人的用户资料'));
			}
			$oUser=UserModel::F('user_id=?',$nUserId)->query();
			if(empty($oUser['user_id'])){
				$this->assign('__JumpUrl__',__APP__);
				$this->E(G::L('你访问的用户不存在'));
			}
			else{
				$this->assign('oUser',$oUser);
				$this->display('user');
			}
		}
		else{
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('你没有选择待修改的用户资料'));
		}
	}

	public function changeuser(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		$nUserId=intval(G::getGpc('user_id','P'));
		$oUser=UserModel::F('user_id=?',$nUserId)->query();
		if(!empty($oUser['user_id'])){
			$oUser->save(0,'update');
			if($oUser->isError()){
				$this->assign('__JumpUrl__',G::U('blog/user?id='.$GLOBALS['___login___']['user_id']));
				$this->E($oUser->getErrorMessage());
			}
			else
				$this->S(G::L('用户资料更新成功'));
		}
		else{
			$this->assign('__JumpUrl__',G::U('blog/user?id='.$GLOBALS['___login___']['user_id']));
			$this->E(G::L('你访问的用户不存在'));
		}
	}

	public function logout(){
		if(UserModel::M()->isLogin()){
			$arrUserData=$GLOBALS['___login___'];
			UserModel::M()->replaceSession($arrUserData['session_hash'],$arrUserData['user_id'],$arrUserData['session_auth_key'],$arrUserData['session_seccode']);
			UserModel::M()->logout();
			$this->assign("__JumpUrl__",G::U('blog/login'));
			$this->S(G::L('登出成功！'));
		}
		else{
			$this->assign('__JumpUrl__',__APP__);
			$this->E(G::L('已经登出！'));
		}
	}

	public function clear(){
		UserModel::M()->clearThisCookie();
		$this->S(G::L('清理登录痕迹成功'));
	}

	public function category(){
		$nPage=intval(G::getGpc('page','G'));
		$nTotalRecord=CategoryModel::F()->all()->getCounts();
		$nEverynum=Global_Extend::getOption('wap_display_category_list_num');
		$oPage=WapPage::RUN($nTotalRecord,$nEverynum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$sOrder="`category_compositor` DESC";
		$arrCategoryLists=CategoryModel::F()->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrCategoryLists',$arrCategoryLists);
		$this->display('category');
	}

	public function record(){
		$arrBlogLists=BlogModel::F('blog_isshow=? AND blog_ispage=?',1,0)->setColumns('blog_dateline')->order('blog_dateline DESC')->all()->query();
		$arrTemp=$arrBlogDate=array();
		foreach($arrBlogLists as $oBlogList){
			$arrBlogDate[]=date('Y-m',$oBlogList->blog_dateline);
		}
		$arrTemp=array_count_values($arrBlogDate);
		unset($arrBlogDate);
		foreach($arrTemp as $sKey=>$nVal){
			list($nY,$nM)=explode('-',$sKey);
			$arrBlogDate[$nY][$nM]=$nVal;
		}
		$this->assign('arrBlogRecords',$arrBlogDate);
		$this->display('record');
	}

	public function tag(){
		$nPage=intval(G::getGpc('page','G'));
		$nTotalRecord=TagModel::F()->all()->getCounts();
		$nEverynum=Global_Extend::getOption('wap_display_tag_list_num');
		$oPage=WapPage::RUN($nTotalRecord,$nEverynum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$sOrder="`tag_id` DESC";
		$arrTagLists=TagModel::F()->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTagLists',$arrTagLists);
		$this->display('tag');
	}

	public function taotao(){
		$nPage=intval(G::getGpc('page','G'));
		$nTotalRecord=TaotaoModel::F()->all()->getCounts();
		$nEverynum=Global_Extend::getOption('wap_display_taotao_list_num');
		$oPage=WapPage::RUN($nTotalRecord,$nEverynum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$sOrder="`taotao_id` DESC";
		$arrTaotaoLists=TaotaoModel::F()->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTaotaoLists',$arrTaotaoLists);
		$this->display('taotao');
	}

	public function singletaotao(){
		$nId=intval(G::getGpc('id','G'));
		if(empty($nId)){
			$this->page404();
		}
		$oTaotao=TaotaoModel::F('taotao_id=?',$nId)->query();
		if(empty($oTaotao->taotao_id)){
			$this->page404();
		}
		$this->assign('oTaotao',$oTaotao);
		$this->display('singletaotao');
	}

	public function addtaotao(){
		if($GLOBALS['___login___']===false){
			$this->assign('__JumpUrl__',G::U('blog/login'));
			$this->E(G::L('你没有登录。'));
		}
		$sContent=trim(G::getGpc('taotao_content'),'P');
		if(empty($sContent)){
			$this->E(G::L('微博内容不能为空'));
			$this->assign('__JumpUrl__',G::U('blog/admin'));
		}
		if(strlen($sContent)>400){
			$this->E(G::L('微博最大长度为400'));
			$this->assign('__JumpUrl__',G::U('blog/admin'));
		}
		$oTaotao=new TaotaoModel();
		$oTaotao->taotao_ismobile=1;
		$oTaotao->save();
		if($oTaotao->isError()){
			$this->assign('__JumpUrl__',G::U('blog/admin'));
			$this->E($oTaotao->getErrorMessage());
		}
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_taotao();
		$this->assign('__JumpUrl__',G::U('blog/taotao'));
		$this->S(G::L('添加心情成功了'));
	}

	public function upload(){
		$nPage=intval(G::getGpc('page','G'));
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$nTotalRecord=UploadModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('wap_display_upload_list_num');
		$oPage=WapPage::RUN($nTotalRecord,$nEverynum,$nPage);
		$sPageNavbar=$oPage->P('page');
		$sOrder="`upload_id` DESC";
		$arrUploadLists=UploadModel::F()->where($arrMap)->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrUploadLists',$arrUploadLists);
		$this->display('upload');
	}

	public function thumb(){
		$nUploadId=G::getGpc('id');
		$nWidth=intval(G::getGpc('w'));
		$nHeigth=intval(G::getGpc('h'));
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$arrMap['upload_id']=$nUploadId;
		if(is_numeric($nUploadId)){
			$oUpload=UploadModel::F($arrMap)->query();
			if(empty($oUpload['upload_id'])){
				return FALSE;
			}
			$sFilePath=DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
			$sFilePath=$oUpload->upload_isthumb?DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename :$sFilePath;
		}
		else{
			$sFilePath=$nUploadId;
		}
		if($sFilePath){
			Image::thumbGd($sFilePath,$nWidth,$nHeigth);
		}
	}

	public function singleupload(){
		$nId=intval(G::getGpc('id','G'));
		if(empty($nId)){
			$this->page404();
		}
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$arrMap['upload_id']=$nId;
		$oUpload=UploadModel::F($arrMap)->query();
		if(empty($oUpload['upload_id'])){
			$this->page404();
		}
		$this->assign('oUpload',$oUpload);
		$this->display('singleupload');
	}

	static public function the_next_upload($nId){
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$arrMap['upload_id']=array('lt',$nId);
		$oNextUpload=UploadModel::F($arrMap)->order('upload_id DESC')->query();
		if(empty($oNextUpload->upload_id)){
			return false;
		}
		else{
			return $oNextUpload->upload_id;
		}
	}

	static public function the_previous_upload($nId){
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$arrMap['upload_id']=array('gt',$nId);
		$oPreviousUpload=UploadModel::F($arrMap)->order('upload_id ASC')->query();
		if(empty($oPreviousUpload->upload_id)){
			return false;
		}
		else{
			return $oPreviousUpload->upload_id;
		}
	}

}
