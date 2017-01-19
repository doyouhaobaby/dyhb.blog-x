<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   用户投稿控制器($) */

!defined('DYHB_PATH') && exit;

class PublishController extends CommonController{

	public function index(){
		define('IS_PUBLISH',TRUE);
		define('CURSCRIPT','publish');
		$this->get_the_category();
		$this->assign('nCurrentTimestamp',CURRENT_TIMESTAMP);
		$this->assign('the_publish_description',Global_Extend::getOption('the_publish_description'));
		$this->get_emot();
		$this->assign('sLicenceTxt',$this->licence());
		$this->display('publish');
	}

	public function get_emot(){
		$arrResult=Dyhb::cache('global_emot');
		if($arrResult===false){
			Cache_Extend::global_emot('admin');
			Cache_Extend::global_emot('blog');
			$arrResult=Dyhb::cache('global_emot');
		}
		$this->assign('arrEmots',$arrResult);
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

	public function licence(){
		$sLicenceTxt=trim(Global_Extend::getOption('website_publish_licence'));
		if(empty($sLicenceTxt)){
			if(file_exists(DYHB_PATH."/../blog/App/Lang/".LANG_NAME."/publish.txt")){
				$sLicenceTxt=nl2br(file_get_contents(DYHB_PATH."/../blog/App/Lang/".LANG_NAME."/publish.txt"));
			}
			else{
				$sLicenceTxt=nl2br(file_get_contents(DYHB_PATH."/../blog/publish.txt"));
			}
		}
		return $sLicenceTxt;
	}

	public function check_urlname(){
		$sUrlname=G::getGpc('blog_urlname');
		$nBlogs=BlogModel::F('blog_urlname=?',$sUrlname)->all()->getCounts();
		if($nBlogs>0){
			echo 'false';
		}
		else{
			echo 'true';
		}
	}

	public function insert(){
		if($GLOBALS['___login___']===false && Global_Extend::getOption('allowed_publish')==0){
			$this->E(G::L('系统已经关闭了投稿功能！','app'));
		}
		if(Global_Extend::getOption('seccode') && Global_Extend::getOption('publishseccode')){// 验证码
			$this->check_seccode(true);
		}
		$nBlogId=intval(G::getGpc('id','G'));
		if($nBlogId>0){
			$oBlog=BlogModel::F('blog_id=? ',$nBlogId)->query();
		}
		else{
			$oBlog=new BlogModel();
			$arrUserData=UserModel::M()->userData();
			$oBlog->user_id=$arrUserData['user_id'];
			$oBlog->blog_dateline=CURRENT_TIMESTAMP;
		}
		$oBlog->blog_isshow=1;
		
		if(preg_match('|<p>(.*?)</p>|is',G::getGpc('blog_content','P'),$arrMatches)){
			$sBlogExcerpt=trim(strip_tags($arrMatches[1]));
		}
		if(empty($sBlogExcerpt)){
			$sBlogExcerpt=String::subString(trim(strip_tags(G::getGpc('blog_content','P'))),0,255);
		}
		$sBlogExcerpt='<p>'.$sBlogExcerpt.'</p>';
		$oBlog->blog_excerpt=$sBlogExcerpt;
		$oBlog->blog_lastpost=serialize(array('comment_id'=>-1,'create_dateline'=>CURRENT_TIMESTAMP,'user_name'=>($GLOBALS['___login___']===false ?G::L('跌名'):$GLOBALS['___login___']['user_name']),'user_id'=>($GLOBALS['___login___']===false ?-1:$GLOBALS['___login___']['user_id'])));
		$oBlog->blog_content=str_replace("\n",'<br/>',str_replace("\r\n",'<br/>',G::getGpc('blog_content','P')));
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
		Cache_Extend::front_widget_static(	);
		$sTag=trim(G::getGpc('blog_tag'));
		if(!empty($sTag)){
			$bResult=TagModel::F()->query()->addTag($sTag,$oBlog->blog_id);
			if($bResult===false){
				$this->E(G::L('标签插入失败!'));
			}
			Cache_Extend::front_widget_hottag();
			Cache_Extend::front_widget_static();
		}
		if($oBlog->isError()){
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
					$this->E($oCategory->getErrorMessage());
				}
				Cache_Extend::front_widget_category();
			}
			if($GLOBALS['___login___']){
				$oUser=UserModel::F('user_id=?',$GLOBALS['___login___']['user_id'])->query();
				$oUser->user_blogs=$oUser->user_blogs+1;
				$oUser->save(0,'update');
				if($oUser->isError()){
					$this->E($oUser->getErrorMessage());
				}
			}
			$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
			$oCacheData->countcache_todaynum=$oCacheData->countcache_todaynum +1;
			$oCacheData->countcache_topicsnum=$oCacheData->countcache_topicsnum +1;
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->E($oCacheData->getErrorMessage());
			}
			$arrData=$oBlog->toArray();
			$arrData['url']=PageType_Extend::getBlogUrl($oBlog);
			$this->A($arrData,G::L('投稿成功'),1);
		}
	}

	public function autosave(){
		if($GLOBALS['___login___']===false && Global_Extend::getOption('allowed_publish')==0){
			echo -1;
			return;
		}
		$nBlogId=intval(G::getGpc('blog_id','P'));
		$sBlogTitle=G::getGpc('blog_title','P');
		$sBlogContent=G::getGpc('blog_content','P');
		if($nBlogId>0){
			$oBlog=BlogModel::F('blog_id=? ',$nBlogId)->query();
		}
		else{
			$oBlog=new BlogModel();
			$arrUserData=UserModel::M()->userData();
			$oBlog->user_id=$arrUserData['user_id'];
			$nBlogId=intval(G::getGpc('blog_id','P'));
			$oBlog->blog_dateline=CURRENT_TIMESTAMP;
		}

		$oBlog->blog_title=$sBlogTitle;
		$oBlog->blog_content=$sBlogContent;
		$oBlog->blog_isshow=0;// 保存到草稿
		$oBlog->blog_type=$oBlog->getBlogType();
		if($nBlogId>0){
			$oBlog->save(0,'update');
		}
		else{
			$oBlog->save();
			$nBlogId=$oBlog->blog_id;
		}

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
		Cache_Extend::front_widget_commentpost();
		Cache_Extend::front_widget_archive();
		Cache_Extend::front_widget_calendar();
		echo $nBlogId;
	}

}
