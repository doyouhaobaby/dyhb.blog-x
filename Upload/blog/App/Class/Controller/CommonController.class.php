<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   公用控制器($) */

!defined('DYHB_PATH') && exit;

class CommonController extends Controller{

	public $_arrOptions=array();
	public $_sCurentCss='';

	public function init_first(){
		if(empty($this->_arrOptions)){
			$this->_arrOptions=OptionModel::optionData();
		}
		Global_Extend::visitor();
		$sTemplateVarPath=APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME.'/style.php';
		if(!is_file($sTemplateVarPath)){
			$sTemplateVarPath=APP_PATH.'/App/~Runtime/Data/Css/Default/style.php';
		}
		$__style_icons__=array();
		if(is_file($sTemplateVarPath)){
			require($sTemplateVarPath);
			$this->assign('__style_icons__',$__style_icons__);
		}
		else{
			Cache_Extend::front_css();
		}
	}

	public function init__(){
		parent::init__();
		UserModel::M()->authData();
		if(UserModel::M()->isBehaviorError()){// 捕获错误
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		$this->init_first();
		$arrUserData=UserModel::M()->userData();
		if(empty($arrUserData['user_id'])){
			$GLOBALS['___login___']=false;
		}
		else{
			$GLOBALS['___login___']=$arrUserData;
		}
		unset($arrUserData);
		$this->init2();
	}

	public function init2(){
		if(Global_Extend::getOption('close_blog')==1){
			$this->assign('__MessageTitle__',G::L('系统已经关闭站点！','app'));
			$this->E(Global_Extend::getOption('close_blog_why'));
		}
		$arrConfigData=array('TPL_DIR'=>ucfirst(Global_Extend::getOption('front_theme_name')),
							'URL_MODEL'=>intval(Global_Extend::getOption('url_model')),
							'LANG'=>ucfirst(Global_Extend::getOption('blog_lang_set')),
							'HTML_CACHE_ON'=>Global_Extend::getOption('html_cache_on')?true:false,
							'HTML_CACHE_TIME'=>Global_Extend::getOption('html_cache_time'),
							'HTML_READ_TYPE'=>Global_Extend::getOption('html_read_type'),
							'START_GZIP'=>Global_Extend::getOption('start_gzip')?true:false,
							'TIME_ZONE'=>Global_Extend::getOption('timeoffset'));
		foreach($arrConfigData as $sKey=>$sConfigData){
			if($GLOBALS['_commonConfig_'][$sKey]!==$sConfigData){
				G::C($sKey,$sConfigData);
			}
		}
		unset($arrConfigData);
		if(isset($_GET['refresh'])){
			@unlink(DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache/~@menu.php');
			G::urlGoTo(__ROOT__.'/index.php',5,'System initialization is complete, 5 seconds automatically jump!');
		}
		define('CURMODULE',ACTION_NAME);
		define('BASESCRIPT',MODULE_NAME);
		Global_Extend::runHooks();
		Global_Extend::hookScriptOutput(ACTION_NAME);
	}

	public function menu($sCurrentCss,$sNormalCss,$sHome=''){
		$arrMenus=Model::C('menu');
		if($arrMenus===false){
			if(empty($sHome)){
				$sHome=G::L('首页');
			}
			$arrMenus=Cache_Extend::front_menu($sHome);
		}
		foreach($arrMenus as $sKey=>&$arrMenu){
			$arrMenu['css']=$this->isCurrentCss($sKey)?$sCurrentCss:$sNormalCss;
		}
		return $arrMenus;
	}

	public function isCurrentCss($sValue='home'){
		if($sValue=='home'){
			if(PageType_Extend::isHome()===true){
				return TRUE;
			}
		}
		elseif(strpos($sValue,'custom_menu_')===0){}
		elseif(strpos($sValue,'system_page_')===0){}
		else{
			if($sValue=='guestbook' && PageType_Extend::isGuestbook()===true){
				return TRUE;
			}
			if($sValue=='taotao' &&(PageType_Extend::isTaotao()===true || PageType_Extend::isSingleTao()===true)){
				return TRUE;
			}
			if($sValue=='record' && PageType_Extend::isRecord()===true){
				return TRUE;
			}
			if($sValue=='link' && PageType_Extend::isLink()===true){
				return TRUE;
			}
			if($sValue=='tag' && PageType_Extend::isTagList()===true){
				return TRUE;
			}
			if($sValue=='search' && PageType_Extend::isSearchForm()===true){
				return TRUE;
			}
			if(($sValue=='upload' &&(PageType_Extend::isUploadList()===true ||
				PageType_Extend::isUploadCategoryList()===true ||
				PageType_Extend::isSingleUpload()===true))){
				return TRUE;
			}
		}
		return FALSE;
	}

	public function get_title($bDisplay=true){
		$sTitle=$this->title('|',false,'right');
		$sTitle.=Global_Extend::getOption('blog_name').' '.Global_Extend::getOption('seo_title');
		if(PageType_Extend::isHome()){
			$sSiteDescription=Global_Extend::getOption('blog_description');
			if(!empty($sSiteDescription)){
				$sTitle.=' | '.$sSiteDescription;
			}
		}
		$sTitle.=(G::getGpc('page','G')>1?' | '.G::L("第%d页",'app',null,G::getGpc('page','G')):'')." - Powered by ".Global_Extend::getOption('blog_program_name')."!";
		if($bDisplay===true){echo $sTitle;}
		else{return $sTitle;}
	}

	public function title($sSep='&raquo;',$bDisplay=true,$sSeplocation=''){
		$sTitle='';
		$sTSep='%DYHB_TITILE_SEP%';
		if(PageType_Extend::isHome()){$sTitle='';}
		elseif(PageType_Extend::isSingle()|| PageType_Extend::isPage()){
			$oBlog=BlogController::$_oBlog;
			$sCategory=$oBlog->category_id==-1 ? G::L('未分类'):$oBlog->category->category_name;
			$sTitle=$oBlog->blog_title.($oBlog['blog_ispage']==1?'':' - '.$sCategory);
		}
		if(PageType_Extend::isCategory()){
			$oCategory=BlogController::$_oCategory;
			$sTitle=(!G::isImplementedTo($oCategory,'IModel')?G::L('未分类'):$oCategory['category_name']).' - '.G::L('分类');
		}
		if(PageType_Extend::isTag()){
			$oTag=BlogController::$_oTag;
			$sTitle=$oTag['tag_name'].' - '.G::L('标签');
		}
		if(PageType_Extend::isUser()){
			$oUser=BlogController::$_oUser;
			$sTitle=(!G::isImplementedTo($oUser,'IModel')? G::L('跌名'):$oUser['user_name']).' - '.G::L('用户归档');
		}
		if(PageType_Extend::isAuthor()){$sTitle=G::L('作者页面');}
		if(PageType_Extend::isArchive() && G::getGpc('m','G')){
			$nM=intval(G::getGpc('m','G'));
			$nMyYear=substr($nM,0,4);
			$nMyMonth=substr($nM,4,2);
			$nMyDay=intval(substr($nM,6,2));
			$sTitle=$nMyYear.($nMyMonth?$sTSep.$nMyMonth:"").($nMyDay?$sTSep.$nMyDay:"");
		}
		if(PageType_Extend::isArchive()&& G::getGpc('year','G')){
			$sTitle=G::getGpc('year','G');
			if(G::getGpc('monthnum','G')){$sTitle.=$sTSep.intval(G::getGpc('monthnum','G'));}
			if(G::getGpc('day','G')){$sTitle.=$sTSep.intval(G::getGpc('day','G'));}
		}
		if(PageType_Extend::isSearch()){$sTitle=G::L('搜索结果');}
		if(PageType_Extend::isTagList()){$sTitle=G::L('标签列表');}
		if(PageType_Extend::isLink()){$sTitle=G::L('友情衔接');}
		if(PageType_Extend::isSearchForm()){$sTitle=G::L('高级搜索页面');}
		if(PageType_Extend::isRecord()){$sTitle=G::L('日志归档');}
		if(PageType_Extend::isUploadList()){$sTitle=G::L('附件列表');}
		if(PageType_Extend::isUploadCategoryList()){$sTitle=G::L('归档列表');}
		if(PageType_Extend::isSingleUpload()){
			$oUpload=UploadController::$_oUpload;
			$sUploadCategory=$oUpload->uploadcategory_id==-1?G::L('未分类'):$oUpload->uploadcategory->uploadcategory_name;
			$sTitle=$oUpload->upload_name.' - '.$sUploadCategory;
		}
		if(PageType_Extend::isSingleTao()){
			$oTaotao=TaotaoController::$_oTaotao;
			$sTaotao=$oTaotao->user_id==-1?G::L('跌名'):($oTaotao->user->user_nikename?$oTaotao->user->user_nikename:$oTaotao->user->user_name);
			$sTaotaoContent=String::subString(strip_tags($oTaotao->taotao_content),0,16);
			$sTitle=($sTaotaoContent ? $sTaotaoContent:G::L('心情撇')).' - '.$sTaotao;
		}
		if(PageType_Extend::isTaotao()){$sTitle=G::L('滔滔心情');}
		if(PageType_Extend::isGuestbook()){$sTitle=G::L('留言板');}
		if(PageType_Extend::is404()){$sTitle=G::L('页面没有找到404');}
		$sPrefix='';
		if(!empty($sTitle)){$sPrefix=" {$sSep} ";}
		if('right'==$sSeplocation){
			$arrTitle=explode($sTSep,$sTitle);
			$arrTitle=array_reverse($arrTitle);
			$sTitle=implode(" {$sSep} ",$arrTitle). $sPrefix;
		}
		else{
			$arrTitle=explode($sTSep,$sTitle);
			$sTitle=$sPrefix . implode(" {$sSep} ",$arrTitle);
		}
		if($bDisplay){
			echo $sTitle;
		}
		else{
			return $sTitle;
		}
	}

	public function loadCss(){
		$sScriptCss="<link rel=\"stylesheet\" type=\"text/css\" href=\"<?php echo __ROOT__;?>/blog/App/~Runtime/Data/Css/<?php echo TEMPLATE_NAME;?>/common.css?<?php echo VERHASH;?>\" />\n\t";
		$sScriptCss.="<link rel=\"stylesheet\" type=\"text/css\" href=\"<?php echo __ROOT__;?>/blog/App/~Runtime/Data/Css/<?php echo TEMPLATE_NAME;?>/widget.css?<?php echo VERHASH;?>\" />\n\t";
		$sContent='';
		$sContent=@implode('',file(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME.'/style.css'));
		$sContent=preg_replace("/([\n\r\t]*)\[CURSCRIPT\s+=\s+(.+?)\]([\n\r]*)(.*?)([\n\r]*)\[\/CURSCRIPT\]([\n\r\t]*)/ies","CommonController::cssVTags('\\2','\\4')",$sContent);
		if(!is_dir(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME)){
			if(!G::makeDir(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME)){
				exit(G::L('创建缓存目录%s失败','app',null,APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME));
			}
		}
		if(defined('CURSCRIPT')){
			$sCssCurScripts=$this->_sCurentCss;
			$sCssCurScripts=preg_replace(array('/\s*([,;:\{\}])\s*/','/[\t\n\r]/','/\/\*.+?\*\//'),array('\\1','',''),$sCssCurScripts);
			if(@$hFp=fopen(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME."/scriptstyle_".CURSCRIPT.".css",'w')){
				fwrite($hFp,stripslashes($sCssCurScripts));
				fclose($hFp);
			}
			else{
				exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,'#__TEMPLATE__#',APP_RUNTIME_PATH."/Data/Css/".TEMPLATE_NAME."/"));
			}
			$sScriptCss.="<?php if(defined('CURSCRIPT')):?><link rel=\"stylesheet\" type=\"text/css\" href=\"<?php echo __ROOT__;?>/blog/App/~Runtime/Data/Css/<?php echo TEMPLATE_NAME;?>/scriptstyle_<?php echo
			CURSCRIPT;?>.css?<?php echo VERHASH;?>\"/><?php endif;?>\n\t";
		}
		$sContent=preg_replace(array('/\s*([,;:\{\}])\s*/','/[\t\n\r]/','/\/\*.+?\*\//'),array('\\1','',''),$sContent);
		$sContent=str_replace('<script type="text/javascript"></script>','',$sContent);
		$sContent=str_replace('[SCRIPTCSS]',$sScriptCss,$sContent);
		if(!file_put_contents(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME."/css.php" ,$sContent)){
			exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,'#__TEMPLATE__#',APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME."/"));
		}
	}

	public function cssVTags($sCurScript,$sContent){
		if(!defined('CURSCRIPT')){$this->_sCurentCss.='';}
		else{$this->_sCurentCss.=in_array(CURSCRIPT,explode(',',$sCurScript))? $sContent: '';}
	}

	public function header(){
		$sRoot='http://'.$_SERVER['HTTP_HOST'];
		if(!is_file(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME.'/css.php')||(defined('CURSCRIPT')&&!is_file(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME.'/scriptstyle_'.CURSCRIPT.'.css'))){
			$this->loadCss();
		}
		require(APP_PATH.'/App/~Runtime/Data/Css/'.TEMPLATE_NAME.'/css.php');
		echo "<link rel=\"profile\" href=\"http://gmpg.org/xfn/11\" />\n\t";
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".Global_Extend::getOption('blog_name')." &raquo; feed\" href=\"".$sRoot.G::U('feed/rss2')."\" />\n\t";
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".Global_Extend::getOption('blog_name')." &raquo; comment feed\" href=\"".$sRoot.G::U('comment/feed')."\" />\n\t";
		echo "<link rel='index' title='".Global_Extend::getOption('blog_name')."' href='".Global_Extend::getOption('blog_url')."' />\n\t";
		echo "<meta name=\"generator\" content=\"".Global_Extend::getOption('blog_program_name').BLOG_SERVER_VERSION." \" />\n\t";
		$sKeyWords=$sDescription='';
		if(PageType_Extend::isCategory()){
			$oCategory=BlogController::$_oCategory;
			$sTitle=!G::isImplementedTo($oCategory,'IModel')?G::L('未分类'):$oCategory['category_name'];
			$sKeyWords.=$sTitle.' '.$oCategory['category_keyword'].' ';
			$sDescription.=$sTitle.' '.$oCategory['category_description'].' ';
			unset($sTitle,$oCategory);
		}
		if(PageType_Extend::isTag()){
			$oTag=BlogController::$_oTag;
			$sKeyWords.=$oTag['tag_name'].' '.$oTag['tag_keyword'].' ';
			$sDescription.=$oTag['tag_name'].' '.$oTag['tag_description'].' ';
			unset($oTag);
		}
		if(PageType_Extend::isUser()){
			$oUser=BlogController::$_oUser;
			$sTitle=G::isImplementedTo($oUser,'IModel')?G::L('跌名'):(!empty($oUser['user_nikename'])?$oUser['user_nikename']:$oUser['user_name']);
			$sKeyWords.=$sTitle.' ';
			$sDescription.=$sTitle.' ';
			unset($sTitle,$oUser);
		}
		$sDescription.=Global_Extend::getOption('blog_name');
		if(PageType_Extend::isSingle()|| PageType_Extend::isPage()){
			$oBlog=BlogController::$_oBlog;
			$sKeyWords.=$oBlog['blog_title'].','.(!empty($oBlog['blog_keyword'])?$oBlog['blog_keyword'].',':'');
			$sDescription.=$oBlog['blog_title'].' '.(!empty($oBlog['blog_description'])?$oBlog['blog_description'].' ':'');
			unset($oBlog);
		}
		$sKeyWords.=Global_Extend::getOption('blog_seo_keywords');
		$sDescription.=' - '.Global_Extend::getOption('blog_program_name');
		echo "<meta name=\"Description\" content=\"{$sDescription}\" />\n\t";
		echo "<meta name=\"Keywords\" content=\"{$sKeyWords}\" />\n\t";
		echo "<meta content=\"".Global_Extend::getOption('blog_seo_robots')."\" name=\"robots\" />\n\t";
		echo "<meta name=\"author\" content=\"".Global_Extend::getOption('blog_program_name')."\" />\n\t";
		echo "<meta name=\"Copyright\" content=\""."©2010 ".Global_Extend::getOption('blog_name').'('.Global_Extend::getOption('blog_url').')All Rights Reserved'."\" />\n\t";
		echo "<meta name=\"MSSmartTagsPreventParsing\" content=\"True\" />
		<meta http-equiv=\"MSThemeCompatible\" content=\"Yes\" />
		<meta http-equiv=\"X-UA-Compatible\" content=\"IE=7\" />\n\t";
		echo "<script src=\"".__LIBCOM__."/Js/Vendor/Jquery.js?v=1.6\" type=\"text/javascript\"></script>\n\t";
		if(Global_Extend::getOption('jquery_lazyload')==1){
			echo "<script src=\"".__PUBLIC__."/Images/Jquery/Lazyload/jquery.lazyload.mini.js?v=1.5.0\" type=\"text/javascript\"></script>\n\t";
			echo "<script type=\"text/javascript\">
//<![CDATA[
jQuery(function(\$){
	\$(\"img\").lazyload({
		effect: \"fadeIn\"
	});
});
//]]>
</script>\n\t";
		}
		echo "<script src=\"".__LIBCOM__."/Js/Dyhb.package.js?v=1.0\" type=\"text/javascript\"></script>\n\t";
		$sImgDir=file_exists(TEMPLATE_PATH.'/Public/Images/ajax/loading.gif')?STYLE_IMG_DIR:IMG_DIR;
		$arrUserinfo=$GLOBALS['___login___'];
		$sJsPath=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/":__ROOT__."/blog/App/~Runtime/Data/Javascript/";
		echo "<script type=\"text/javascript\">
//<![CDATA[
var _PUBLIC_='".__PUBLIC__."';
Dyhb.Lang.SetCurrentLang('__LANG_NAME__');
Dyhb.Ajax.Dyhb.Image=['{$sImgDir}/ajax/loading.gif','{$sImgDir}/ajax/ok.gif','{$sImgDir}/ajax/update.gif'];
var URL='__URL__',doyouhaobaby_uid=".($arrUserinfo['user_id']?$arrUserinfo['user_id']:0).",IMG_DIR='".IMG_DIR."',VERHASH='".VERSION."',cookiedomain='',cookiepath='/',cookiepre='',charset='utf-8',disallowfloat='sendpm',template_type='".TEMPLATE_TYPE."',SITEURL='',JSPATH='{$sJsPath}';
".App::U()."
//]]>
</script>\n\t";
		$sGlobalJs=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/global.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/global.js?v=1.0";
		$sBlogCommonJs=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/blog_common.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/blog_common.js?v=1.0";
		echo "<script src=\"".$sGlobalJs."\" type=\"text/javascript\"></script>\n\t";
		echo "<script src=\"".$sBlogCommonJs."\" type=\"text/javascript\"></script>\n\t";
		if(TEMPLATE_TYPE==='cms'){
			$sPicScrollJs=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/pic_scroll.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/pic_scroll.js?v=1.0";
			echo "<script src=\"".$sPicScrollJs."\" type=\"text/javascript\"></script>\n\t";
		}
		if(Global_Extend::getOption('jquery_slimbox')==1){
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".__PUBLIC__."/Images/Slimbox/slimbox.css?v=2.0.3\" />\n\t";
			echo "<script src=\"".__PUBLIC__."/Images/Slimbox/slimbox.js?v=2.0.3\" type=\"text/javascript\"></script>\n\t";
			echo "<script type=\"text/javascript\">
//<![CDATA[
jQuery(function(\$){
	\$(\"a[href\$=jpg],a[href\$=gif],a[href\$=png],a[href\$=jpeg],a[href\$=bmp]\").slimbox({});
});
//]]>
</script>\n\t";
		}
		echo "
<script language=\"javascript\" src=\"".__PUBLIC__."/Images/Prettify/prettify.js\"></script>
<script language=\"javascript\">
jQuery(function(\$){
prettyPrint();
});
</script>";
	}

	public function usermenu(){
		if(empty($GLOBALS['___login___']['user_id'])){
			return array('system'=>0,'user'=>0,'total'=>0);
		}
		$arrMap['pm_delstatus']=0;
		$arrMap['pm_isread']=0 ;
		$arrMap['pm_msgtoid']=$GLOBALS['___login___']['user_id'];
		$arrMap['pm_type']='user';
		$arrData['user']=PmModel::F()->where($arrMap)->all()->getCounts();
		$arrMap['pm_type']='system';
		unset($arrMap['pm_msgtoid']);
		$arrData['system']=PmModel::F()->where($arrMap)->all()->getCounts();
		$oSystemMessage=SystempmModel::F('user_id=?',$GLOBALS['___login___']['user_id'])->query();
		if(!empty($oSystemMessage['user_id'])){$arrReadPms=unserialize($oSystemMessage['systempm_readids']);}
		else{$arrReadPms=array();}
		$arrData['system']-=count($arrReadPms);// 减去已经阅读的短消息
		if($arrData['system']<0){$arrData['system']=0;}
		if($arrData['user']<0){$arrData['user']=0;}
		$arrData['total']=$arrData['system']+$arrData['user'];
		return $arrData;
	}

	public function seccode(){
		G::seccode();
	}

	public function check_seccode($bSubmit=false){
		if(Global_Extend::getOption('seccode')==0){
			return;
		}
		$sType=isset($_GET['type'])?G::getGpc('type','G'):G::getGpc('comment_relationtype','P');
		if(!$sType){
			$sType=G::getGpc('seccode_type','P');
		}
		if($sType){
			switch($sType){
				case 'blog' :
					if(Global_Extend::getOption('blog_comment_seccode')==0){return;}
					break;
				case 'pm' :
					if(Global_Extend::getOption('sendpmseccode')==0){return;}
					break;
				case 'member' :
					if(Global_Extend::getOption('changeinfoseccode')==0){return;}
					break;
				case 'login' :
					if(Global_Extend::getOption('loginseccode')==0){return;}
					break;
				case 'password' :
					if(Global_Extend::getOption('changepasswordseccode')==0){return;}
					break;
				case 'register' :
					if(Global_Extend::getOption('registerseccode')==0){return;}
					break;
				case 'publish' :
					if(Global_Extend::getOption('publishseccode')==0){return;}
					break;
				default:
					if(Global_Extend::getOption('blog_comment_seccode')==0){return;}
					break;
			}
		}
		else{
			if(Global_Extend::getOption('blog_guestbook_seccode')==0){return;}
		}

		$sSeccode=G::getGpc('seccode');
		UserModel::M()->checkSeccode($sSeccode);
		if(UserModel::M()->isBehaviorError()){
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		if($bSubmit===false){
			$this->S(G::L('验证码正确'));
		}
	}

	public function page404(){
		header("HTTP/1.0 404 Not Found");
		define('IS_404',TRUE);
		define('CURSCRIPT','404');
		$this->assign('the_404_description',Global_Extend::getOption('the_404_description'));
		$this->display('404');
		exit();
	}

	public function get_host_header(){
		return 'http://'.$_SERVER['HTTP_HOST'];
	}

	public function clear_url($sUrl){
		return str_replace('&','&amp;',$sUrl);
	}

}
