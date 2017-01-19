<?php 
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   判断页面类型函数($) */

!defined('DYHB_PATH') && exit;

class PageType_Extend{

	static public function isSingle(){
		return defined('IS_SINGLE');
	}

	static public function isHome(){
		return defined('IS_HOME');
	}
	static public function isGuestbook(){
		return defined('IS_GUESTBOOK');
	}

	static public function isTaotao(){
		return defined('IS_TAOTAOLIST');
	}

	static public function isRecord(){
		return defined('IS_RECORD');
	}

	static public function isLink(){
		return defined('IS_LINK');
	}

	static public function isSingleTao(){
		return defined('IS_SINGLETAO');
	}

	static public function isPage(){
		return defined('IS_PAGE');
	}

	static public function isCategory(){
		return defined('IS_CATEGORY');
	}

	static public function isTag(){
		return defined('IS_TAG');
	}

	static public function isTagList(){
		return defined('IS_TAGLIST');
	}

	static public function isNewBlogs(){
		return defined('IS_NEWBLOGS');
	}

	static public function isAuthor(){
		return defined('IS_SINGLEUSER');
	}

	static public function isSingleUser(){
		return defined('IS_SINGLEUSER');
	}

	static public function isSinglePm(){
		return defined('IS_SINGLEPM');
	}

	static public function isPmNew(){
		return defined('IS_PMNEW');
	}

	static public function isPublish(){
		return defined('IS_PUBLISH');
	}

	static public function isRegister(){
		return defined('IS_REGISTER');
	}

	static public function isSearchForm(){
		return defined('IS_SEARCHFORM');
	}

	static public function isMember(){
		return defined('IS_MEMBER');
	}

	static public function isPassword(){
		return defined('IS_PASSWORD');
	}

	static public function isAvatar(){
		return defined('IS_AVATAR');
	}

	static public function isAvatarCrop(){
		return defined('IS_AVATARCROP');
	}

	static public function isArchive(){
		return defined('IS_ARCHIVE');
	}

	static public function isSearch(){
		return defined('IS_SEARCH');
	}

	static public function isBloglist(){
		return defined('IS_BLOGLIST');
	}

	static public function isUser(){
		return defined('IS_USER');
	}

	static public function isUploadList(){
		return defined('IS_UPLOADLIST');
	}

	static public function isPmList(){
		return defined('IS_PMLIST');
	}

	static public function isFriendList(){
		return defined('IS_FRIENDLIST');
	}

	static public function isCommentList(){
		return defined('IS_COMMENTLIST');
	}

	static public function isUserList(){
		return defined('IS_USERLIST');
	}

	static public function isTrackbackList(){
		return defined('IS_TRACKBACKLIST');
	}

	static public function isUploadcategoryList(){
		return defined('IS_UPLOAPCATEGOYR');
	}

	static public function isSingleUpload(){
		return defined('IS_SINGLEUPLOAD');
	}

	static public function is404(){
		return defined('IS_404');
	}

	static public function getBlogUrl($arrBlog,$arrExtend=array()){
		if(!empty($arrBlog['blog_gotourl'])){
			return $arrBlog['blog_gotourl'];
		}
		if(empty($arrBlog['blog_urlname'])){
			return G::U('blog/'.$arrBlog['blog_id'],$arrExtend);
		}
		else{
			return G::U($arrBlog['blog_urlname'].'/index',$arrExtend);
		}
	}

	static public function getBlogName($oBlog){
		if($oBlog->user_id==-1){
			$sUserName=G::L('跌名');
		}
		else{
			if(!empty($oBlog->user->user_nikename)){
				$sUserName=$oBlog->user->user_nikename;
			}
			else{
				$sUserName=$oBlog->user->user_name;
			}
		}
		return $sUserName;
	}

	static public function getPageUrl($arrBlog){
		if(!empty($arrBlog['blog_gotourl'])){
			return $arrBlog['blog_gotourl'];
		}
		if(empty($arrBlog['blog_urlname'])){
			return G::U('page/'.$arrBlog['blog_id']);
		}
		else{
			return __APP__."/page/".$arrBlog['blog_urlname'].$GLOBALS['_commonConfig_']["HTML_FILE_SUFFIX"];
		}
	}

	static public function getTaotaoUrl($arrTaotao,$arrExtend=array()){
		return G::U('taotao@?id='.$arrTaotao['taotao_id'],$arrExtend);
	}

	static public function getCategoryUrl($arrCategory){
		return G::U('category@?value='.(!empty($arrCategory['category_urlname'])? $arrCategory['category_urlname']:$arrCategory['category_id']));
	}

	public function getCategoryidByUrlname($sUrlName){
		if(empty($sUrlName)){return false;}
		$oCategory=CategoryModel::F()->getBycategory_urlname($sUrlName);
		if(!empty($oCategory['category_id'])){
			return $oCategory->category_id;
		}
		else{
			return false;
		}
	}

	static public function getTagUrl($arrTag){
		return G::U('tag@?value='.(!empty($arrTag['tag_urlname'])? $arrTag['tag_urlname']:$arrTag['tag_id']));
	}

	static public function getUploadUrl($arrUpload,$arrExtend=array()){
		return G::U('upload@?id='.$arrUpload['upload_id'],$arrExtend);
	}

	static public function getArchiveUrl($nTime){
		$nTime=intval($nTime);
		return G::U('archive@?value='.$nTime);
	}

	static public function getUserUrl($arrBlog){
		return G::U('user@?value='.$arrBlog['user_id']);
	}

	static public function getAuthorUrl($arrUser){
		return G::U('author@?id='.$arrUser['user_id']);
	}

	static public function getUploadcategoryUrl($arrUploadcategory){
		return G::U('upload/index?cid='.$arrUploadcategory['uploadcategory_id']);
	}

}
