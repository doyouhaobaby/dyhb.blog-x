<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Blog日志函数($) */

!defined('DYHB_PATH') && exit;

class Blog_Extend{

	static public function blogContentNewpage($oBlog,$sId='pagenav'){
		$nNewpage=G::getGpc('newpage','G');
		$nNewpage=empty($nNewpage)?1: $nNewpage;
		$sNewpageNav="<div id=\"{$sId}\" class=\"{$sId}\">";
		$arrContentPieces=explode('[newpage]',stripslashes($oBlog->blog_content));
		for($nI=0;$nI<=count($arrContentPieces)+1;$nI++){
			if(!empty($arrContentPieces[$nI])){
				if($nNewpage!=$nI+1){
					$sUrl=Url_Extend::getBlogUrl($oBlog,array('newpage'=>$nI+1));
					$sNewpageNav.="<a href=\"{$sUrl}\">".($nI+1)."</a>";
				}
				else{
					$sNewpageNav.="<span class=\"current\">".($nI+1)."</span>";
				}
			}
		}
		$sNewpageNav.="</div>";
		$arrContentPieces['newpagenav']=$sNewpageNav;
		return $arrContentPieces;
	}

	static public function theCategory($oCategory,$bReturn=false){
		if(empty($oCategory['category_id'])){
			$sUrl="<a href=\"".Url_Extend::getCategoryUrl(array('category_id'=>-1))."\" title=\"".G::L('未分类')."\">".G::L('未分类')."</a>";
			if($bReturn===false){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
		else{
			$sUrl="<a href=\"".Url_Extend::getCategoryUrl($oCategory)."\" title=\"".$oCategory['category_name']."\">".$oCategory['category_name']."</a>";
			if($bReturn===false){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
	}

	static public function theAuthor($oUser,$bReturn=false){
		if(empty($oUser['user_id'])){
			$sUrl="<a href=\"".Url_Extend::getUserUrl(array('user_id'=>-1))."\">".G::L('跌名')."</a>";
			if($bReturn=false){
				echo $sUrl;
			}
			else{
				return $sUrl;
			}
		}
		else{
			$sUsername =$oUser->user_nikename?$oUser->user_nikename:$oUser->user_name;
			$sUrl="<a href=\"".Url_Extend::getUserUrl($oUser)."\" title=\"{$sUsername}\">".$sUsername."</a>";
			if($bReturn===false){
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
		$arrDefault =array(
			'text'=>G::L('[置顶]'),
			'img'=>__TMPLPUB__.'/Images/import.gif',
			'type'=>1,//0=text,1=img
			'display'=>1,
			'style'=>"margin:0;padding:0;",
		);
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
			$sText="<img title=\"".$arrData['text']."\" style=\"".$arrData['style']."\" src=\"".$arrData['img']."\">";
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
		$arrDefault =array(
			'text'=>G::L('[手机]'),
			'img'=>__TMPLPUB__.'/Images/mobile.gif',
			'type'=>1,//0=text,1=img
			'display'=>1,
			'style'=>"margin:0;padding:0;",
		);
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
			$sText="<img title=\"".$arrData['text']."\" style=\"".$arrData['style']."\" src=\"".$arrData['img']."\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theCommentMobile($arrComment,$arrData=array()){
		if($arrComment['comment_ismobile']==0){
			return '';
		}
		$arrDefault =array(
			'text'=>G::L('[手机]'),
			'img'=>__TMPLPUB__.'/Images/mobile.gif',
			'type'=>1,//0=text,1=img
			'display'=>1,
			'style'=>"margin:0;padding:0;",
		);
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
			$sText="<img title=\"".$arrData['text']."\" style=\"".$arrData['style']."\" src=\"".$arrData['img']."\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theTaotaoMobile($arrTaotao,$arrData=array()){
		if($arrTaotao['taotao_ismobile']==0){
			return '';
		}
		$arrDefault =array(
			'text'=>G::L('[手机]'),
			'img'=>__TMPLPUB__.'/Images/mobile.gif',
			'type'=>1,//0=text,1=img
			'display'=>1,
			'style'=>"margin:0;padding:0;",
		);
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
			$sText="<img title=\"".$arrData['text']."\" style=\"".$arrData['style']."\" src=\"".$arrData['img']."\">";
		}
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theTag($arrTags,$bReturn=false){
		$sText='';
		foreach($arrTags as $oTag){
			$sText.="<a href=\"".Url_Extend::getTagUrl($oTag)."\" title=\"".G::L('标签').' '.$oTag->tag_name."\">".$oTag->tag_name."</a>";;
		}
		if($bReturn===false){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function thePreviousPost($arrBlog,$arrData=array()){
		$arrDefault =array(
			'text'=>G::L('上一篇'),
			'display'=>1,
			'meta_nav'=>'<span class="meta-nav">&larr;</span>',
			'cut'=>0,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$oPreviousBlog=BlogModel::F('blog_id>? AND blog_ispage=0 AND blog_isshow=1',$arrBlog['blog_id'])->order('blog_id ASC')->query();
		if(empty($oPreviousBlog->blog_id)){
			return G::L('没有了');
		}
		$sText="<a href=\"".Url_Extend::getBlogUrl($oPreviousBlog)."\" ".($oPreviousBlog['blog_color']?'style="color:'.$oPreviousBlog['blog_color'].';"':'')." rel=\"prev\" title=\"".$arrData['text'].' '.$oPreviousBlog->blog_title."\">".$arrData['meta_nav'].(!empty($arrData['cut'])?String::subString(strip_tags($oPreviousBlog->blog_title),0,$arrData['cut']):$oPreviousBlog->blog_title)."</a>";;
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

	static public function theNextPost($arrBlog,$arrData=array()){
		$arrDefault =array(
			'text'=>G::L('下一篇'),
			'display'=>1,
			'meta_nav'=>'<span class="meta-nav">&rarr;</span>',
			'cut'=>0,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefault,$arrData);
		}
		else{
			$arrData=$arrDefault;
		}
		$oNextBlog=BlogModel::F('blog_id<? AND blog_ispage=0 AND blog_isshow=1',$arrBlog['blog_id'])->order('blog_id DESC')->query();
		if(empty($oNextBlog->blog_id)){
			return G::L('没有了');
		}
		$sText="<a href=\"".Url_Extend::getBlogUrl($oNextBlog)."\" ".($oNextBlog['blog_color']?'style="color:'.$oNextBlog['blog_color'].';"':'')." rel=\"next\" title=\"".$arrData['text'].' '.$oNextBlog->blog_title."\">".(!empty($arrData['cut'])?String::subString(strip_tags($oNextBlog->blog_title),0,$arrData['cut']):$oNextBlog->blog_title).$arrData['meta_nav']."</a>";;
		if($arrData['display']==1){
			echo $sText;
		}
		else{
			return $sText;
		}
	}

}
