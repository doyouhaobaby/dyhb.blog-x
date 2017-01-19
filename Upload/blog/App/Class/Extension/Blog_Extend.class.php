<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Blog日志函数($) */

!defined('DYHB_PATH') && exit;

class Blog_Extend{

	static public function getCommentReply($arrComment){
		if($arrComment['comment_url']){
			return "[url=".htmlspecialchars($arrComment['comment_url'])."]@".htmlspecialchars($arrComment['comment_name']).":[/url]";
		}
		else{
			return "[b]".htmlspecialchars($arrComment['comment_name']).":[/b]";
		}
	}

	static public function getCommentQuote($arrComment){
		if($arrComment['comment_url']){
			$sBack="[url=".htmlspecialchars($arrComment['comment_url'])."]".htmlspecialchars($arrComment['comment_name'])."[/url]";
		}
		else{
			$sBack=htmlspecialchars($arrComment['comment_name']);
		}
		return G::L('引用').htmlspecialchars($arrComment['comment_name']).G::L('的话').":[blockquote][b]{$sBack}:[/b]".htmlspecialchars(trim(strip_tags($arrComment['comment_content'])))."[/blockquote]";
	}

	static public function getGravatar($nUid='',$sType='middle',$arrGravatarCom=array()){
		$arrDefault=array('email'=>'','size'=>'','default'=>'','rate'=>'');
		if(!empty($arrGravatarCom)){
			$arrGravatarCom=array_merge($arrDefault,$arrGravatarCom);
		}
		else{
			$arrGravatarCom=$arrDefault;
		}
		if(Global_Extend::getOption('use_blog_system_avatar')==1 && $sType!='email'){
			return self::getAvatarUrl($nUid,$sType);
		}
		else{
			return self::getGravatarCom($arrGravatarCom['email'],$arrGravatarCom['size'],$arrGravatarCom['default'],$arrGravatarCom['rate']);
		}
	}

	public static function getGravatarCom($sEmail='',$nSize='' ,$sDefault='',$sRating=''){
		if(empty($nSize)){
			$nSize=Global_Extend::getOption('comment_avatar_size');
		}
		if(empty($sDefault)){
			$sDefault=Global_Extend::getOption('avatar_default');
		}
		if(empty($sRating)){
			$sRating=Global_Extend::getOption('avatars_rating');
		}
		if(Global_Extend::getOption('avatar_cache')){
			$sEmail=md5($sEmail);//通过MD5加密邮箱
			$sCachePath=DYHB_PATH."/../Public/Avatar/cache"; //缓存文件夹路径
			if(!is_dir($sCachePath)){
				if(!G::makeDir($sCachePath)){
					exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,$sCachePath,$sCachePath));
				}
			}
			$sAvatarUrl=__PUBLIC__.'/Avatar/cache/'.$sEmail.'.jpg'; //头像相对路径
			$sAvatarAbsUrl=$sCachePath."/".$sEmail.'.jpg'; //头像绝对路径
			$sCacheTime=24*3600*(Global_Extend::getOption('avatar_cache_time')?Global_Extend::getOption('avatar_cache_time'):7); //缓存时间为7天
			if(!file_exists($sAvatarAbsUrl)||(time()-filemtime($sAvatarAbsUrl))> $sCacheTime){
				$sNewAvatar="http://www.gravatar.com/avatar/".$sEmail."?s=".$nSize."&d=".strtolower($sDefault)."&r=".strtolower($sRating);
				copy($sNewAvatar,$sAvatarAbsUrl);
			}
			return $sAvatarUrl;
		}
		else{
			return "http://www.gravatar.com/avatar/".md5($sEmail)."?s=".$nSize."&d=".strtolower($sDefault)."&r=".strtolower($sRating);
		}
	}

	static public function getSystemAvatar($nUid='',$sType='middle'){
		return E::getAvatar($nUid,$sType);
	}

	public static function getAvatarUrl($nUid,$sType=''){
		$bExistAvatar=file_exists(DYHB_PATH.'/../Public/Avatar/data/'.self::getSystemAvatar($nUid,'origin'))?true:false;
		if(empty($sType)){
			$sType='origin';
		}
		return $bExistAvatar?__PUBLIC__.'/Avatar/data/'.self::getSystemAvatar($nUid,$sType):__PUBLIC__.'/Avatar/images/noavatar_'.($sType=='origin'?'big':$sType).'.gif';
	}

	static public function getWidgetCache($sWidgetName){
		if(in_array($sWidgetName,array('admin','blog','rss','search'))){
			return false;
		}
		$arrResult=Model::C('widget_'.$sWidgetName);
		if($arrResult===false){// 没有缓存，则使用widget调入缓存
			call_user_func(array('Template_Extend',$sWidgetName.'W'));
			return Model::C('widget_'.$sWidgetName);
		}
		else{
			return $arrResult;
		}
	}

	static public function getThumbImage($arrBlog){
		if(empty($arrBlog['blog_thumb'])){
			$sThumbImg=(file_exists(TEMPLATE_PATH.'/Public/Images/nopic.gif')?STYLE_IMG_DIR.'/nopic.gif':IMG_DIR.'/nopic.gif');
		}
		else{
			if(!preg_match("/[^\d-.,]/",$arrBlog['blog_thumb'])){
				$oUpload=UploadModel::F('upload_id=?',$arrBlog['blog_thumb'])->query();
				if(!empty($oUpload['upload_id']) && in_array($oUpload->upload_extension,array('gif','jpg','jpeg','bmp','png'))){
					$sThumbImg=$oUpload->upload_isthumb?
							__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:
							__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
				}
				else{
					$sThumbImg=(file_exists(TEMPLATE_PATH.'/Public/Images/nopic.gif')?STYLE_IMG_DIR.'/nopic.gif':IMG_DIR.'/nopic.gif');
				}
			}
			else{
				$sThumbImg=$arrBlog['blog_thumb'];
			}
		}
		return $sThumbImg;
	}

	public function getUploadcategoryCover($arrUploadCategory=array()){
		if(empty($arrUploadCategory['uploadcategory_cover'])){
			$sCoverPhoto=(file_exists(TEMPLATE_PATH.'/Public/Images/photosort.gif')?STYLE_IMG_DIR.'/photosort.gif':IMG_DIR.'/photosort.gif');
		}
		else{
			if(!preg_match("/[^\d-.,]/",$arrUploadCategory['uploadcategory_cover'])){
				$oUpload=UploadModel::F('upload_id=?',$arrUploadCategory['uploadcategory_cover'])->query();
				if(!empty($oUpload['upload_id']) && in_array($oUpload->upload_extension,array('gif','jpg','jpeg','bmp','png'))){
					$sCoverPhoto=$oUpload->upload_isthumb?__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
				}
				else{
					$sCoverPhoto=(file_exists(TEMPLATE_PATH.'/Public/Images/photosort.gif')?STYLE_IMG_DIR.'/photosort.gif':IMG_DIR.'/photosort.gif');
				}
			}
			else{
				$sCoverPhoto=$arrUploadCategory['uploadcategory_cover'];
			}
		}
		return $sCoverPhoto;
	}

	static public function encodeTrackbackUrl($sStr){
		$nRand=rand(0,9);
		$arrNewStr[0]=$nRand;
		for($nI=0; $nI<strlen($sStr); $nI++){
			$arrNewStr[]=ord($sStr{$nI})+$nRand;
		}
		return implode('%',$arrNewStr);
	}

	static public function trackbackCertificate($nBlogId,$nPublishTime){ // Prevent Trackback spam
		if(Global_Extend::getOption('trackback_url_expire')==1){
			$nBlogId.='+'.date('Ymd',CURRENT_TIMESTAMP);
		}
		return substr(md5($nBlogId.$nPublishTime),0,5);
	}

	static public function mp3player($nWidth,$nHeight,$sColor='#ffffff'){
		return "<embed src=\"".__PUBLIC__."/Images/Mp3/mp3player.swf\"
				quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"
				type=\"application/x-shockwave-flash\" width=\"{$nWidth}\" height=\"{$nHeight}\" bgcolor=\"{$sColor}\">
				</embed>";
	}

	static public function singleMp3play($sMp3Path,$bReturnMp3path=false,$nWidth=400,$nHeight=40,$sColor="#ffffff"){
		$sMp3Path=__PUBLIC__."/Images/Mp3/mp3.swf?soundFile={$sMp3Path}";
		$sMp3Extend="&bg=".Global_Extend::getOption('singlemp3player_bg')."&leftbg=".Global_Extend::getOption('singlemp3player_leftbg')."&lefticon=".Global_Extend::getOption('singlemp3player_lefticon')."&rightbg=".Global_Extend::getOption('singlemp3player_rightbg')."&rightbghover=".Global_Extend::getOption('singlemp3player_righticonhover')."&righticon=".Global_Extend::getOption('singlemp3player_righticon')."&righticonhover=".Global_Extend::getOption('singlemp3player_righticonhover')."&text=".Global_Extend::getOption('singlemp3player_text')."&slider=".Global_Extend::getOption('singlemp3player_slider')."&track=".Global_Extend::getOption('singlemp3player_track')."&border=".Global_Extend::getOption('singlemp3player_border')."&loader=".Global_Extend::getOption('singlemp3player_loader')."&autostart=".Global_Extend::getOption('singlemp3player_auto')."&loop=".Global_Extend::getOption('singlemp3player_loop');
		$Mp3Extend=str_replace('#','0x',$sMp3Extend);
		$sMp3Path=$sMp3Path.$Mp3Extend;
		if($bReturnMp3path===true){
			return $sMp3Path;
		}
		return "<embed src=\"{$sMp3Path}\"
				quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\"
				type=\"application/x-shockwave-flash\" width=\"{$nWidth}\" height=\"{$nHeight}\" bgcolor=\"{$sColor}\"></embed>";
	}

	static public function blogContentNewpage($oBlog,$sId='pagenav'){
		$nNewpage=G::getGpc('newpage','G');
		$nNewpage=empty($nNewpage)? 1: $nNewpage;
		$nPage=G::getGpc('page','G');
		$nPage=empty($nPage)? 1: $nPage;
		$sNewpageNav="<div id=\"{$sId}\" class=\"{$sId}\">";
		$arrContentPieces=explode('[newpage]',stripslashes($oBlog->blog_content));
		for($nI=0;$nI<=count($arrContentPieces)+1;$nI++){
			if(!empty($arrContentPieces[$nI])){
				if($nNewpage!=$nI+1){
					$sUrl=PageType_Extend::getBlogUrl($oBlog,array('newpage'=>$nI+1,'page'=>$nPage));
					$sNewpageNav.="<a href=\"{$sUrl}\">".($nI+1)."</a>&nbsp;";
				}
				else{
					$sNewpageNav.="<span class=\"current\">".($nI+1)."</span>&nbsp";
				}
			}
		}
		$sNewpageNav.="</div>";
		$arrContentPieces['newpagenav']=$sNewpageNav;
		return $arrContentPieces;
	}

	static public function blogContentUpload($Upload,$bImg=false){
		if(!preg_match("/[^\d-.,]/",$Upload)){
			$oUpload=UploadModel::F('upload_id=?',$Upload)->query();
		}
		else{
			$oUpload=UploadModel::F('upload_savename=?',$Upload)->query();
		}
		if(!empty($oUpload['upload_id'])){
			$sSrcImg=__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
			if($bImg===true){
				$sThumbImg=$oUpload->upload_isthumb?__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:$sSrcImg;
			}
			if($bImg===true){
				return array($oUpload,$sSrcImg,$sThumbImg);
			}
			else{
				return array($oUpload,$sSrcImg);
			}
		}
		else{
			return false;
		}
	}

	static public function theTitle($arrBlog,$arrData=array()){
		$arrDefault=array('before'=>'<h3>','after'=>'</h3>','display'=>1,'cut'=>20,'rel'=>'bookmark');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sUrl=$arrData['before']."<a rel=\"".$arrData['rel']."\" ".($arrBlog['blog_color']?'style="color:'.$arrBlog['blog_color'].';"':'')." href=\"".PageType_Extend::getBlogUrl($arrBlog)."\" ".($arrBlog['blog_isblank']==1 ? 'target="_blank"':'')." title=\"{$arrBlog['blog_title']}\">".String::subString(strip_tags($arrBlog['blog_title']),0,$arrData['cut'])."</a>".$arrData['after'];
		if($arrData['display']==1){
			echo $sUrl;
		}
		else{
			return $sUrl;
		}
	}

	static public function theCategory($oCategory,$arrData=array()){
		$arrDefault=array('separator'=>' ','parent'=>1,'display'=>1,'no'=>G::L('未分类'),'class'=>'');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if(empty($oCategory['category_id'])){
			$sUrl="<a href=\"".PageType_Extend::getCategoryUrl(array('category_id'=>-1))."\" class=\"{$arrData['class']}\" title=\"{$arrData['no']}\">{$arrData['no']}</a>";
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
		else{
			$sUrl=self::theCategoryParent($oCategory,$arrData);
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
	}

	static public function theCategoryParent($oCategory,$arrData){
		$sReturn="<a class=\"".$arrData['class']."\" href=\"".PageType_Extend::getCategoryUrl($oCategory)."\" title=\"{$oCategory['category_name']}\">{$oCategory['category_name']}</a>";
		if($oCategory->category_parentid==0 || $arrData['parent']==0){
			return $sReturn;
		}
		$oParentCategory=CategoryModel::F('category_id=?',$oCategory->category_parentid)->query();
		$sReturn=self::theCategoryParent($oParentCategory,$arrData).$arrData['separator'].$sReturn;
		return $sReturn;
	}

	static public function theFrom($arrBlog,$arrData=array()){
		$arrDefault=array('display'=>1,'original'=>G::L('本站原创'),'site'=>Global_Extend::getOption('blog_url'));
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if(empty($arrBlog['blog_from'])){
			$arrBlog['blog_from']=$arrData['original'];
		}
		if(empty($arrBlog['blog_fromurl'])){
			$arrBlog['blog_fromurl']=$arrData['site'];
		}
		$sUrl="<a href=\"{$arrBlog['blog_fromurl']}\" title=\"{$arrBlog['blog_from']}\">{$arrBlog['blog_from']}</a>";
		if($arrData['display']==1){
			echo $sUrl;
		}
		else{
			return $sUrl;
		}
	}

	static public function theComment($arrBlog,$arrData=array()){
		$arrDefault=array('display'=>1,'name'=>G::L('评论'),'before'=>'(','after'=>')');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sUrl="<a href=\"".PageType_Extend::getBlogUrl($arrBlog)."#comments\" title=\"{$arrData['name']}\">".$arrData['name'].$arrData['before'].$arrBlog['blog_commentnum'].$arrData['after']."</a>";
		if($arrData['display']==1){
			echo $sUrl;
		}
		else{
			return $sUrl;
		}
	}

	static public function theView($arrBlog,$arrData=array()){
		$arrDefault=array('display'=>1,'name'=>G::L('浏览'),'before'=>'(','after'=>')',);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sUrl="<a href=\"".PageType_Extend::getBlogUrl($arrBlog)."\" title=\"{$arrData['name']}\">".$arrData['name'].$arrData['before'].$arrBlog['blog_viewnum'].$arrData['after']."</a>";
		if($arrData['display']==1){
			echo $sUrl;
		}
		else{
			return $sUrl;
		}
	}

	static public function theTrackback($arrBlog,$arrData=array()){
		$arrDefault=array('display'=>1,'name'=>G::L('引用'),'before'=>'(','after'=>')');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sUrl="<a href=\"".PageType_Extend::getBlogUrl($arrBlog)."#trackbacks\" title=\"{$arrData['name']}\">".$arrData['name'].$arrData['before'].$arrBlog['blog_trackbacknum'].$arrData['after']."</a>";
		if($arrData['display']==1){
			echo $sUrl;
		}
		else{
			return $sUrl;
		}
	}

	static public function theAuthor($oUser,$arrData=array()){
		$arrDefault=array('no'=>G::L('跌名'),'display'=>0);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if($oUser['user_id']<1){
			$sUrl="<a href=\"".PageType_Extend::getUserUrl(array('user_id'=>-1))."\">".$arrData['no']."</a>";
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
		else{
			$sUsername=$oUser->user_nikename ? $oUser->user_nikename: $oUser->user_name;
			$sUrl="<a href=\"".PageType_Extend::getUserUrl($oUser)."\" title=\"{$sUsername}\">{$sUsername}</a>";
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
	}

	static public function theAuthorSpace($oUser,$arrData=array()){
		$arrDefault=array('no'=>G::L('跌名'),'display'=>1);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if($oUser['user_id']<1){
			$sUrl=$arrData['no'];
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
		else{
			$sUsername=$oUser->user_nikename ? $oUser->user_nikename: $oUser->user_name;
			$sUrl="<a href=\"".PageType_Extend::getAuthorUrl($oUser)."\" title=\"{$sUsername}\">{$sUsername}</a>";
			if($arrData['display']==1){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
	}

	static public function theTop($arrBlog,$arrData=array()){
		if($arrBlog['blog_istop']==0){
			return '';
		}
		$arrDefault=array('text'=>G::L('[置顶]'),'img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/import.gif')?STYLE_IMG_DIR.'/import.gif':IMG_DIR.'/import.gif'),'type'=>1,'display'=>1,'style'=>"margin:0;padding:0;",);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if($arrData['type']==0){
			$sText=$arrData['text'];
		}
		else{
			$sText="<img title=\"{$arrData['text']}\" style=\"{$arrData['style']}\" src=\"{$arrData['img']}\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theMobile($arrBlog,$arrData=array()){
		if($arrBlog['blog_ismobile']==0){
			return '';
		}
		$arrDefault=array('text'=>G::L('[手机]'),'img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/mobile.gif')?STYLE_IMG_DIR.'/mobile.gif':IMG_DIR.'/mobile.gif'),'type'=>1,'display'=>1,'style'=>"margin:0;padding:0;",);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if($arrData['type']==0){
			$sText=$arrData['text'];
		}
		else{
			$sText="<img title=\"{$arrData['text']}\" style=\"{$arrData['style']}\" src=\"{$arrData['img']}\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theUpload($arrBlog,$arrData=array()){
		if($arrBlog['blog_uploadnum']<=0){
			return '';
		}
		$arrDefault=array('text'=>G::L('[附件]'),'img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/image.gif')?STYLE_IMG_DIR.'/image.gif':IMG_DIR.'/image.gif'),'type'=>1,'display'=>1,'style'=>"margin:0;padding:0;");
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		if($arrData['type']==0){
			$sText=$arrData['text'];
		}
		else{
			$sText="<img title=\"{$arrData['text']} {$arrBlog['blog_uploadnum']}\" style=\"{$arrData['style']}\" src=\"{$arrData['img']}\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theDigg($arrBlog){
		return "<iframe id=\"digg_content\"
name=\"digg_content\"
src=\"".G::U('blog/digg?id='.$arrBlog['blog_id'])."\"
width=\"100%\" height=\"85px\" scrolling=\"auto\"	frameborder=\"0\" allowTransparency=\"true\" >
</iframe>";
	}

	static public function theTag($arrTags,$arrData=array()){
		$arrDefault=array('text'=>G::L('标签'),'display'=>1,);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sText='';
		foreach($arrTags as $oTag){
			$sText.="<a href=\"".PageType_Extend::getTagUrl($oTag)."\" title=\"{$arrData['text']} {$oTag['tag_name']}\">{$oTag->tag_name}</a>";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function thePreviousPost($arrBlog,$arrData=array()){
		$arrDefault=array('text'=>G::L('上一篇'),'display'=>1,'meta_nav'=>'<span class="meta-nav">&larr;</span>','cut'=>20);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$oPreviousBlog=BlogModel::F('blog_id>? AND blog_ispage=0 AND blog_isshow=1',$arrBlog['blog_id'])->order('blog_id ASC')->query();
		if(empty($oPreviousBlog->blog_id)){
			return G::L('没有了','app');
		}
		$sText="<a href=\"".PageType_Extend::getBlogUrl($oPreviousBlog)."\" ".($oPreviousBlog['blog_color']?'style="color:'.$oPreviousBlog['blog_color'].';"':'')." rel=\"prev\" title=\"{$arrData['text']} {$oPreviousBlog->blog_title}\">".$arrData['meta_nav'].String::subString(strip_tags($oPreviousBlog->blog_title),0,$arrData['cut'])."</a>";
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theNextPost($arrBlog,$arrData=array()){
		$arrDefault=array('text'=>G::L('下一篇'),'display'=>1,'meta_nav'=>'<span class="meta-nav">&rarr;</span>','cut'=>20);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$oNextBlog=BlogModel::F('blog_id<? AND blog_ispage=0 AND blog_isshow=1',$arrBlog['blog_id'])->order('blog_id DESC')->query();
		if(empty($oNextBlog->blog_id)){
			return G::L('没有了','app');
		}
		$sText="<a href=\"".PageType_Extend::getBlogUrl($oNextBlog)."\" ".($oNextBlog['blog_color']?'style="color:'.$oNextBlog['blog_color'].';"':'')." rel=\"next\" title=\"{$arrData['text']}  {$oNextBlog->blog_title}\">".String::subString(strip_tags($oNextBlog->blog_title),0,$arrData['cut']).$arrData['meta_nav']."</a>";;
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theToobar($arrBlog,$arrData=array()){
		$arrDefault=array(
			'img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/image.gif')?STYLE_IMG_DIR.'/image.gif':IMG_DIR.'/image.gif'),
			'display'=>1,
			'class'=>'text-label-indented',
			'fontsize_image'=>(file_exists(TEMPLATE_PATH.'/Public/Images/toolbar_fontsize.gif')?STYLE_IMG_DIR.'/toolbar_fontsize.gif':IMG_DIR.'/toolbar_fontsize.gif'),
			'rss_image'=>(file_exists(TEMPLATE_PATH.'/Public/Images/toolbar_rss.gif')?STYLE_IMG_DIR.'/toolbar_rss.gif':IMG_DIR.'/toolbar_rss.gif'),
			'save_image'=>(file_exists(TEMPLATE_PATH.'/Public/Images/toolbar_save.gif')?STYLE_IMG_DIR.'/toolbar_save.gif':IMG_DIR.'/toolbar_save.gif'),
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$sText="<span class=\"".$arrData['class']."\">";
		$sText.="<img src=\"".$arrData['fontsize_image']."\" alt='' title=\"".G::L('字体大小')."\" border='0'/>";
		$sText.="<a href=\"javascript: doZoom(20);\">".G::L('超大')."</a> |<a href=\"javascript: doZoom(16);\">".G::L('大')."</a> | <a href=\"javascript: doZoom(14);\">".G::L('中')."</a> | <a href=\"javascript: doZoom(12);\">".G::L('小')."</a>";
		$sText.="<a href=\"".G::U('feed/blog?id='.$arrBlog['blog_id'])."\">";
		$sText.="<img src=\"".$arrData['rss_image']."\" alt='' title=\"".G::L('订阅本文')."\" border='0'/></a>";
		$sText.="<a href=\"".G::U('blog/save_a_text?id='.$arrBlog['blog_id'])."\" ><img src=\"".$arrData['save_image']."\" alt='' title=\"".G::L('保存本文为文本文档')."\" border='0'/></a>";
		$sText.="</span>";
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

}
