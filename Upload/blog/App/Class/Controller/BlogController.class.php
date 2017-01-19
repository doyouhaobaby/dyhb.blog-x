<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Blog控制器($) */

!defined('DYHB_PATH') && exit;

class BlogController extends CommonController{

	static public $_oBlog=null;
	static public $_oCategory=null;
	static public $_oTag=null;
	static public $_oUser=null;

	public function empty_(){
		$arrMap['blog_isshow']=1;
		if(is_numeric(ACTION_NAME)){
			$arrMap['blog_id']=ACTION_NAME;
		}
		else if(ACTION_NAME=='blog'){
			$arrMap['blog_id']=intval(G::getGpc('id','G'));
		}
		else{
			$arrMap['blog_urlname']=ACTION_NAME;
		}
		$this->show($arrMap);
	}

	public function show($arrMap){
		$oBlog=BlogModel::F($arrMap)->query();
		if(empty($oBlog['blog_id'])){
			$this->page404();
		}
		if($oBlog['blog_ispage']==1){
			define('IS_PAGE',TRUE);
			define('CURSCRIPT','page');
		}
		else{
			define('CURSCRIPT','single');
			define('IS_SINGLE',TRUE);
		}
		if(!empty($oBlog['blog_password'])){
			$sPostPassword=G::getGpc('password','P');
			$sPostPassword=$sPostPassword ?G::addslashes(trim($sPostPassword)): '';
			$sCookiePassword=G::cookie('dyhbblogx_blogid_'.$oBlog['blog_id']);
			$sCookiePassword=$sCookiePassword ?E::authcode(G::addslashes(trim($sCookiePassword))): '';
			$this->auth_password($sPostPassword,$sCookiePassword,$oBlog['blog_password'],$oBlog['blog_id']);
		}
		$oBlog->blog_viewnum=$oBlog->blog_viewnum+1;
		$oBlog->save(0,'update');
		self::$_oBlog=$oBlog;
		$nNewpage=G::getGpc('newpage','G');
		$nNewpage=empty($nNewpage)?1: intval($nNewpage);
		if(strpos($oBlog['blog_content'],'[newpage]')!==false){
			$arrContentPieces=Blog_Extend::blogContentNewpage($oBlog,'pagination');
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
		$oBlog->blog_content=Ubb_Extend::convertUbb($oBlog->blog_content,1);
		if(Global_Extend::getOption('show_content_emot')==1){
			$oBlog->blog_content=preg_replace_callback("/\[emot\]([^ ]+?)\[\/emot\]/is", array('Global_Extend','getEmot'), $oBlog->blog_content);
			$oBlog->blog_excerpt=preg_replace_callback("/\[emot\]([^ ]+?)\[\/emot\]/is", array('Global_Extend','getEmot'), $oBlog->blog_excerpt);
		}
		$oBlog->blog_content=Global_Extend::backword($oBlog->blog_content);
		$oBlog->blog_excerpt=Global_Extend::backword($oBlog->blog_excerpt);
		$arrTrackbackData=$this->get_trackback_data($oBlog);
		$this->assign('arrTrackbackData',$arrTrackbackData);
		$arrCache=Model::C('widget_category');
		if($arrCache===false){
			Cache_Extend::front_widget_category();
			$arrCache=Model::C('widget_category');
		}
		$oCategoryTree=new TreeCategory();
		$arrSaveCatlists=array();
		foreach($arrCache as $oCategory){
			$oCategoryTree->setNode($oCategory['category_id'], $oCategory['category_parentid'], $oCategory['category_name']);
			$arrSaveCatlists[$oCategory['category_id']]=$oCategory;
		}
		$this->assign('arrAllCategory',$arrSaveCatlists);
		$this->assign('oCategoryTree',$oCategoryTree);
		$nPage=G::getGpc('page','G');
		$nEveryCommentnum=Global_Extend::getOption('display_blog_comment_list_num');
		if(TEMPLATE_TYPE==='blog'||TEMPLATE_TYPE==='cms'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_parentid']=0;
			$arrCommentMap['comment_relationtype']='blog';
			$arrCommentMap['comment_relationvalue']=$oBlog->blog_id;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum, $nPage);
			$oPage->setParameter('newpage='.$nNewpage.'&');
			$sPageNavbar=$oPage->P('pagination');
			$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(), $nEveryCommentnum)->query();
			$arrAllCommentMap['comment_isshow']=1;
			$arrAllCommentMap['comment_parentid']=array('neq',0);
			$arrAllCommentMap['comment_relationtype']='blog';
			$arrAllCommentMap['comment_relationvalue']=$oBlog->blog_id;
			$arrAllComments=CommentModel::F()->reset(DbSelect::WHERE)->where($arrAllCommentMap)->all()->order('`comment_id` DESC')->query();
			$this->assign('arrAllComments',$arrAllComments);
			$this->assign('arrCommentLists',$arrCommentLists);
		}
		elseif(TEMPLATE_TYPE==='bbs'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_relationtype']='blog';
			$arrCommentMap['comment_relationvalue']=$oBlog->blog_id;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum, G::getGpc('page','G'));
			$oPage->setParameter('newpage='.$nNewpage.'&');
			$sPageNavbar=$oPage->P('pagination');
			$arrBoardCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(), $nEveryCommentnum)->query();
			$this->assign('arrBoardCommentLists',$arrBoardCommentLists);
		}
		else{
			$this->E(G::L('你当前的模板方案不正确'));
		}
		if(TEMPLATE_TYPE==='cms'){
			if($oBlog['category_id']!=-1){
				$arrBrotherCategorys=CategoryModel::F('category_parentid=?',$oBlog->category->category_parentid)->all()->query();
			}
			else{
				$arrBrotherCategorys=array();
			}
			$this->assign('arrBrotherCategorys',$arrBrotherCategorys);
		}
		$this->assign('nPage',$nPage);
		$this->assign('nEveryCommentnum',$nEveryCommentnum);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('nNewpage',$nNewpage);// 只有newpage等于1才显示摘要和缩略图
		$this->assign('oBlog',$oBlog);
		$this->assign('theData',$oBlog);
		$this->assign('sCommentRelationtype','blog');
		$this->assign('nCommentRelationvalue',$oBlog->blog_id);
		$this->assign('nTotalComment',$nTotalComment);
		if($oBlog['blog_ispage']==1){
			$this->display('page');
		}
		else{
			$this->display('single');
		}
	}

	public function auth_password($sPostPassword, $sCookiePassword, $sBlogPassword, $nBlogId){
		$sPassword=$sCookiePassword ?$sCookiePassword : $sPostPassword;
		if($sPassword !==G::addslashes($sBlogPassword)){
			$sUrl=__APP__;
			$sMessage='<div class="auth_password">
<form action="" method="post">
'.G::L('请输入该日志的访问密码','app').'<br>
<input type="password" name="password" class="field" />
<input type="submit" value="'.G::L('进入','app').'.." />
<br /><br /><a href="'.$sUrl.'" title="'.G::L('返回首页','app').'">&laquo;'.G::L('返回首页','app').'</a>
</form>
</div>';
			if($sCookiePassword){
				G::cookie('dyhbblogx_blogid_'.$nBlogId,' ',CURRENT_TIMESTAMP-31536000);
			}
			$this->assign('__MessageTitle__',G::L('访问加密日志','app'));
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',60);
			$this->E($sMessage);
			exit;
		}
		else{
			G::cookie('dyhbblogx_blogid_'. $nBlogId, E::authcode($sBlogPassword,false,null,3600));
		}
	}

	public function index(){
		$sCategoryTemplate='';
		$arrMap=array();
		$arrMap[ 'blog_ispage' ]=0; // 去除页面
		$arrMap[ 'blog_isshow' ]=1; // 只允许公开的日志
		$sFilter=G::getGpc('filter','G');
		$sFilter2=G::getGpc('filter2','G');
		if(empty($sFilter2)){
			$sFilter2='all';
		}
		$value=G::getGpc('value','G');
		$nSearchid=G::getGpc('searchid','G');
		$nTotalRecord=null;
		$sTheOrderby=G::getGpc('orderby','G');
		$sOrderby=G::getGpc('ascdesc','G');
		if(empty($sOrderby)){
			$sOrderby='DESC';
		}
		$nPage=G::getGpc('page','G');
		$this->assign('nPage',$nPage);
		if($sFilter2=='top'){
			$arrMap['blog_istop']=1;
		}
		elseif(preg_match("/^\d+$/", $sFilter2)){
			$arrMap['blog_dateline']=array('egt',CURRENT_TIMESTAMP-$sFilter2);
		}
		if($nSearchid){
			define('IS_SEARCH',TRUE);
			define('CURSCRIPT','search');
			$oSearchindexModel=SearchindexModel::F('searchindex_id=?',$nSearchid)->query();
			if(empty($oSearchindexModel->searchindex_id)){
				$this->page404();
			}
			if($oSearchindexModel->searchindex_searchfrom !='blog'){
				$this->E(G::L('非日志搜索索引，我们无法完成搜索请求'));
			}
			$nTotalRecord=$oSearchindexModel->searchindex_totals;
			$arrMap['blog_id']=array('exp','IN('.$oSearchindexModel->searchindex_ids.')');
			$arrSearchstring=unserialize($oSearchindexModel->searchindex_searchstring);
			if(!empty($arrSearchstring['theorderby'])){
				$sTheOrderby=$arrSearchstring['theorderby'];
			}
			if(!empty($arrSearchstring['orderby'])){
				$sOrderby=$arrSearchstring['orderby'];
			}
		}
		if(!empty($sFilter)){
			switch($sFilter){
				case "user":
					if($value=='index'){
						$oUserController=new UserController();
						$oUserController->index();
						exit();
					}
					$arrMap['user_id']=intval($value);
					define('IS_USER',TRUE);
					define('CURSCRIPT','user');
					if($arrMap['user_id']==-1){
						self::$_oUser=-1;
					}
					else{
						self::$_oUser=UserModel::F('user_id=?',$arrMap['user_id'])->query();
					}
					$this->assign('oUser',self::$_oUser);
					break;
				case "category":
					if(!empty($value)){
							if(!preg_match("/[^\d-., ]/",$value)){
								$arrMap['category_id']=$value;
							}
							else{
								$value=$this->get_categoryid_by_urlname($value);
								if($value===false)	$this->page404();
								$arrMap['category_id']=$value;
							}
					}
					if($arrMap['category_id']==-1){
						$oCategory=-1;
					}
					else{
						$oCategory=CategoryModel::F('category_id=?',$arrMap['category_id'])->query();
					}
					if(!empty($oCategory['category_gotourl'])){
						G::urlGoTo($oCategory['category_gotourl']);
					}
					if(!empty($oCategory['category_template'])){
						$sCategoryTemplate.=$oCategory['category_template'];
					}
					if(G::isImplementedTo($oCategory,'IModel')){
							$arrChildrens=CategoryModel::F('category_parentid=?',$oCategory['category_id'])->asArray()->all()->query();
							if(empty($arrChildrens[0]['category_id'])){
								$arrChildrens=false;
							}
					}
					else{
						$arrChildrens=false;
					}
					if(TEMPLATE_TYPE==='cms'){
						if(G::isImplementedTo($oCategory,'IModel')){
							$arrBrotherCategorys=CategoryModel::F('category_parentid=?',$oCategory->category_parentid)->all()->query();
						}
						else{
							$arrBrotherCategorys=array();
						}
						$this->assign('arrBrotherCategorys',$arrBrotherCategorys);
					}
					self::$_oCategory=$oCategory;
					$this->assign('oCategory',$oCategory);
					$this->assign('arrChildrens',$arrChildrens);
					define('IS_CATEGORY',TRUE);
					define('CURSCRIPT','category');
					break;
				case "archive":
					$arrSqlTime=Date::getTheFirstOfYearOrMonth($value);
					$arrArchive=strlen($value)==8?array($arrSqlTime[4],$arrSqlTime[5]):array($arrSqlTime[0],$arrSqlTime[1]);
					$arrMap['blog_dateline']=array('between',$arrArchive);
					define('IS_ARCHIVE',TRUE);
					define('CURSCRIPT','archive');
					break;
				case "tag":
					if($value){
							if($value=='index'){
							$oTagController=new TagController();
							$oTagController->index();
							exit();
						}
						$sBlogIds=TagModel::F()->query()->getBlogIdStrByTagId($value);
						if(empty($sBlogIds))$this->page404();
						$arrMap['blog_id']=array('in',$sBlogIds);
						self::$_oTag=TagModel::F('tag_id=?',$value)->query();
						$this->assign('oTag',self::$_oTag);
					}
					define('IS_TAG',TRUE);
					define('CURSCRIPT','tag');
					break;
			}
		}
		else{
			$sOp=G::getGpc('op','G');
			if(TEMPLATE_TYPE==='bbs' && $sOp=='newblogs'){
				define('IS_NEWBLOGS',TRUE);// 定义最新日志
				define('CURSCRIPT','newblogs');
			}
			else if($nPage===null || $nPage<=1){
				if(!$nSearchid){
					define('IS_HOME',TRUE);// 定义主页
					define('CURSCRIPT','index');
				}
			}
		}

		if($nSearchid || $sFilter=='category'){
			$arrCache=Model::C('widget_category');
			if($arrCache===false){
				Cache_Extend::front_widget_category();
				$arrCache=Model::C('widget_category');
			}
			$oCategoryTree=new TreeCategory();
			$arrSaveCatlists=array();
			foreach($arrCache as $oCategory){
				$oCategoryTree->setNode($oCategory['category_id'],$oCategory['category_parentid'],$oCategory['category_name']);
				$arrSaveCatlists[$oCategory['category_id']]=$oCategory;
			}
			$this->assign('arrAllCategory',$arrSaveCatlists);
			$this->assign('oCategoryTree',$oCategoryTree);
		}
		if($nTotalRecord===null){
			$nTotalRecord=BlogModel::F()->where($arrMap)->all()->getCount('blog_id');
		}
		$nEverynum=Global_Extend::getOption('display_blog_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		if(!empty($sTheOrderby)){
			$sOrder="`{$sTheOrderby}` {$sOrderby},`blog_id` DESC";
		}
		else{
			$sOrder="`blog_istop` DESC,`blog_id` DESC";
		}
		$this->assign('sTheOrderby',$sTheOrderby);
		$this->assign('sFilter2',$sFilter2);
		$arrBlogLists=BlogModel::F()->where($arrMap)->all()->order($sOrder)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrBlogLists',$arrBlogLists);
		if(defined('IS_HOME')|| defined('IS_NEWBLOGS')){
			$this->assign('oCachedata',Global_Extend::updateCountCacheData());
		}
		if(defined('IS_HOME')){
			$arrCatlists=CategoryModel::F('category_parentid=?',0)->all()->asArray()->order('category_compositor')->query();
			$this->assign('arrCategoryLists',$arrCatlists);
			$arrCache=Model::C('widget_category');
			if($arrCache===false){
				Cache_Extend::front_widget_category();
				$arrCache=Model::C('widget_category');
			}
			$oCategoryTree=new TreeCategory();
			$arrSaveCatlists=array();
			if(!empty($arrCache)){
				foreach($arrCache as $oCategory){
					$oCategoryTree->setNode($oCategory['category_id'],$oCategory['category_parentid'],$oCategory['category_name']);
					$arrSaveCatlists[$oCategory['category_id']]=$oCategory;
				}
			}
			$this->assign('arrAllCategory',$arrSaveCatlists);
			$this->assign('oCategoryTree',$oCategoryTree);
			$this->display('index');
		}
		elseif(defined('IS_NEWBLOGS')){
			$this->display('newblogs');
		}
		else{
			define('IS_BLOGLIST',TRUE);
			if(!defined('CURSCRIPT')){
				define('CURSCRIPT','bloglist');
			}
			$this->display('archive'.$sCategoryTemplate);
		}
	}

	public function get_child_category($arrCategory){
		$nChilds=CategoryModel::F('category_parentid=?',$arrCategory['category_parentid'])->all()->getCounts();
		if($nChilds>0){
			return true;
		}
		else{
			return false;
		}
	}

	public function the_title_url($oBlog){
		return PageType_Extend::getBlogUrl($oBlog);
	}

	public function get_user_url($oBlog){
		return PageType_Extend::getUserUrl($oBlog);
	}

	public function get_user_name($oBlog){
		return PageType_Extend::getBlogName($oBlog);
	}

	public function get_categoryid_by_urlname($sUrlName){
		return	PageType_Extend::getCategoryidByUrlname($sUrlName);
	}

	public function get_thumb_image($oBlog){
		return Blog_Extend::getThumbImage($oBlog);
	}

	public function the_category($oCategory,$arrData=array()){
		return Blog_Extend::theCategory($oCategory,$arrData);
	}

	public function the_title($arrBlog,$arrData=array()){
		return Blog_Extend::theTitle($arrBlog,$arrData);
	}

	public function the_from($arrBlog,$arrData=array()){
		return Blog_Extend::theFrom($arrBlog,$arrData);
	}

	public function the_comment($arrBlog,$arrData=array()){
		return Blog_Extend::theComment($arrBlog,$arrData);
	}

	public function the_view($arrBlog,$arrData=array()){
		return Blog_Extend::theView($arrBlog,$arrData);
	}

	public function the_trackback($arrBlog,$arrData=array()){
		return Blog_Extend::theTrackback($arrBlog,$arrData);
	}

	public function the_author($oUser,$arrData=array()){
		return Blog_Extend::theAuthor($oUser,$arrData);
	}

	public function the_author_space($oUser,$arrData=array()){
		return Blog_Extend::theAuthorSpace($oUser,$arrData);
	}

	public function the_top($arrBlog,$arrData=array()){
		return Blog_Extend::theTop($arrBlog,$arrData);
	}

	public function the_mobile($arrBlog,$arrData=array()){
		return Blog_Extend::theMobile($arrBlog,$arrData);
	}

	public function the_upload($arrBlog,$arrData=array()){
		return Blog_Extend::theUpload($arrBlog,$arrData);
	}

	public function the_digg($arrBlog){
		return Blog_Extend::theDigg($arrBlog);
	}

	public function digg(){
		$nBlogId=intval(G::getGpc('id','G'));
		$oBlog=BlogModel::F('blog_id=?',$nBlogId)->query();
		$nGoodPost=$oBlog->blog_good;
		$nBadPost=$oBlog->blog_bad;
		$nTotalPostNum=$nGoodPost+$nBadPost;
		if($nTotalPostNum>0){
			$nGoodPer=number_format($nGoodPost/$nTotalPostNum,3)*100;
			$nBadPer=100-$nGoodPer;
		}
		else{
			$nGoodPer=$nBadPer=0;
		}
		$sAction=G::getGpc('action');
		$sUrl=G::U('blog/digg?id='.$nBlogId);
		if($sAction=='good'){
			$oBlog->blog_good=$oBlog->blog_good+1;
			$oBlog->save('0','update');
			header("Location: {$sUrl}");
		}
		else if($sAction=='bad'){
			$oBlog->blog_bad=$oBlog->blog_bad+1;
			$oBlog->save('0','update');
			header("Location: {$sUrl}");
		}
		$sDigg='<div class="diggbox digg_good"
onmousemove="this.style.backgroundPosition=\'left bottom\';"
onmouseout="this.style.backgroundPosition=\'left top\';"
onclick="postDigg(\'good\','.$nBlogId.')">
<div class="digg_act">'.G::L('顶一下').'</div><div class="digg_num">('.$nGoodPost.')</div>
<div class="digg_percent"><div class="digg_percent_bar"><span style="width:'.$nGoodPer.'%"></span></div>
<div class="digg_percent_num">'.$nGoodPer.'%</div></div></div>
<div class="diggbox digg_bad"
onmousemove="this.style.backgroundPosition=\'right bottom\';"
onmouseout="this.style.backgroundPosition=\'right top\';"
onclick="postDigg(\'bad\','.$nBlogId.')">
<div class="digg_act">'.G::L('踩一下').'</div>
<div class="digg_num">('.$nBadPost.')</div>
<div class="digg_percent">
<div class="digg_percent_bar">
<span style="width:'.$nBadPer.'%"></span></div>
<div class="digg_percent_num">'.$nBadPer.'%</div></div></div>';
		$this->assign('sDigg',$sDigg);
		Template::in(true);
		$this->display(DYHB_PATH.'/../Public/Images/Blog/digg/digg.html');
		Template::in(false);
	}

	public function getTagsByBlogId($nBlogId){
		return TagModel::F()->query()->getTagsByBlogId($nBlogId);
	}

	public function the_tag($oBlog,$arrData){
		$oTags=$this->getTagsByBlogId($oBlog->blog_id);
		if(!is_array($oTags)){
			return false;
		}
		else{
			return Blog_Extend::theTag($oTags,$arrData);
		}
	}

	public function the_previous_post($oBlog,$arrData){
		return Blog_Extend::thePreviousPost($oBlog,$arrData);
	}

	public function the_next_post($oBlog,$arrData){
		return Blog_Extend::theNextPost($oBlog,$arrData);
	}

	public function save_a_text(){
		$nBlogId=G::getGpc('id','G');
		$arrMap['blog_isshow']=1;
		$arrMap['blog_id']=$nBlogId;
		$oBlog=BlogModel::F($arrMap)->query();
		if(empty($oBlog->blog_id)){
			$this->page404();
		}
		if(!empty($oBlog->blog_password)){
			$this->page404();
		}
		$sBlogContent=&$oBlog->blog_content;
		$sBlogContent=str_replace('[newpage]', '', $sBlogContent);
		if(Global_Extend::isLogin()===false){
			$sBlogContent=preg_replace("/\[hide\](.+?)\[\/hide\]/is", "<br/>".G::L("这部分内容只能在登入之后看到。").G::L('请先').G::L('注册').G::L('或者').G::L('登录')."<br/>", $sBlogContent);
		}
		else{
			$sBlogContent=str_replace(array('[hide]','[/hide]'), '', $sBlogContent);
		}
		$sBlogContent=str_replace(array('<br/>', '</p>', '</div>', '&#123;', '&#125;', '&nbsp;'), array("\r\n", "\r\n", "\r\n", '{', '}', ' '), $sBlogContent);
		$sBlogContent=strip_tags($sBlogContent);
		$sBlogContent=html_entity_decode($sBlogContent , ENT_QUOTES);
		$sBlogUrl=Global_Extend::getOption('blog_url').PageType_Extend::getBlogUrl($oBlog);
		$oUserName=!empty($oBlog->user->user_name)?(!empty($oBlog->user->user_nikename)?$oBlog->user->user_nikename : $oBlog->user->user_name):G::L('跌名');
		header('Content-type: text/plain');
		header('Content-Disposition: attachment; filename="'.date('Ymd-His').'-blogid'.$oBlog->blog_id.'.txt"');
		$sUtf8Bom=chr(239).chr(187).chr(191);
		echo $sUtf8Bom;
		echo G::L('标题：').$oBlog->blog_title."\r\n";
		echo G::L('出处：').Global_Extend::getOption('blog_program_name')."\r\n";
		echo G::L('时间：').date('r' ,$oBlog->blog_dateline)."\r\n";
		if(!empty($oBlog->update_dateline)){
			echo G::L('最后更新时间：').date('r' ,$oBlog->update_dateline)."\r\n";
		}
		echo G::L('作者：').$oUserName."\r\n";
		echo G::L('地址：').$sBlogUrl."\r\n\r\n";
		echo G::L('内容：\r\n').$sBlogContent."\r\n\r\n\r\n";
		echo "Generated by ".Global_Extend::getOption('blog_program_name')." Blog ".BLOG_SERVER_VERSION. "	Release ".BLOG_SERVER_RELEASE;
		exit();
	}

	public function get_trackback_data($oBlog){
		$sTrackbackAuthentic=Blog_Extend::trackbackCertificate($oBlog->blog_id, $oBlog->blog_dateline);
		if(Global_Extend::getOption('allow_trackback')==1){
			$sEntryTrackbackUrl=Global_Extend::getOption('blog_url')."/index.php?c=trackback&a=url&id={$oBlog->blog_id}&amp;extra={$sTrackbackAuthentic}";
			$sEntryTrackbackUrl2=Global_Extend::getOption('trackback_url_javascript')==1 ?"<span id='tbb{$oBlog->blog_id}'></span>" : $sEntryTrackbackUrl;
			$sTbb=($oBlog->blog_isshow==1)?"<strong>".G::L('引用地址：')."</strong>	{$sEntryTrackbackUrl2}" : "<strong>".G::L('本日志被隐藏，不接受引用')."</strong>";
			if($oBlog->blog_allowedtrackback==0){
				$sTbb.="<strong>".G::L('本日志不接受引用','app')."</strong>";
			}
			$sTbb=($oBlog->blog_isshow==1)?"<strong>".G::L('引用地址：')."</strong>	{$sEntryTrackbackUrl2}" : "<strong>".G::L('本日志被隐藏，不接受引用')."</strong>";
			if(Global_Extend::getOption('trackback_url_expire')==1){
				$sTbb.="<br/>".G::L('<strong>注意：</strong> 该地址仅在今日23:59:59之前有效');
			}
			$sTbBar="<div id=\"tb{$oBlog->blog_id}\" style=\"display: none;\" class=\"textbox-tburl\">{$sTbb}</div>";
			$sTbOnclick=Global_Extend::getOption('trackback_url_javascript')==1	?"\$(\"#tb{$oBlog->blog_id}\").toggle(); if(document.getElementById(\"tbb{$oBlog->blog_id}\"))document.getElementById(\"tbb{$oBlog->blog_id}\").innerHTML=decodeTrackbackUrl(\"".Blog_Extend::encodeTrackbackUrl($sEntryTrackbackUrl)."\", ".Global_Extend::getOption('trackback_url_math').", {$oBlog->blog_id});" : "\$(\"#tb{$oBlog->blog_id}\").toggle();";
		}
		else{
			$sTbBar="<div id=\"tb{$oBlog->blog_id}\" style=\"display: none;\" class=\"textbox-tburl\"><strong>".G::L('引用功能被关闭了。')."</strong></div>";
			$sEntryTrackbackUrl=G::L('引用功能被关闭了。');
			$sTbOnclick="\$(\"#tb{$oBlog->blog_id}\").toggle();";
		}
		$sImgDir=file_exists(TEMPLATE_PATH.'/Public/Images/ajax/loading.gif')?STYLE_IMG_DIR:IMG_DIR;
		$sEntryTb="<a href='javascript: void(0);' title=\"".G::L('查看引用地址')."\" onclick='{$sTbOnclick}' >".G::L('引用')."({$oBlog->blog_trackbacknum})</a>";
		$sEntryTb.="&nbsp;<img src=\"{$sImgDir}/utf8.jpg\" /><br/>\n\r";
		$sEntryTb.=G::L('<strong>注意：</strong>本程序使用utf8进行编码，你点击上面的“引用”即可获取引用地址，非utf8 的程序请注意发送utf8 格式的数据，我们才能够接受。')."\r\n";
		return array('enter_tb'=>$sEntryTb,'tb_bar'=>$sTbBar);
	}

	public function the_toolbar($oBlog,$arrData=array()){
		return Blog_Extend::theToobar($oBlog,$arrData);
	}

}
