<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Blog 控制器($)*/

!defined('DYHB_PATH') && exit;

class BlogController extends InitController{

	public function filter_(&$arrMap){
		$nCategoryId=intval(G::getGpc('cid'));// 文章分类
		$nTagId=intval(G::getGpc('tid'));// 标签ID
		$nUserId=intval(G::getGpc('uid'));// 用户ID
		$nStatus=intval(G::getGpc('status'));// 状态
		$sKeyword=G::getGpc('keyword');// 关键字
		$sType=G::getGpc('type');// 类型
		if($nTagId){
			$sBlogIds=TagModel::F()->query()->getBlogIdStrByTagId($nTagId);
			if(empty($sBlogIds))$this->E(G::L('标签不存在！'));
			$arrMap['blog_id']=array('in',$sBlogIds);
		}
		if(!empty($nCategoryId)){
			$arrMap['category_id']=$nCategoryId;
		}

		if($nUserId){
			$arrMap['user_id']=$nUserId;
		}
		if($sKeyword){
			$arrMap['blog_title']=array('like',"%".$sKeyword."%");
		}
		else{
			$arrMap['blog_title']=array('like',"%".G::getGpc('blog_title')."%");
		}
		$arrMap['blog_ispage']=0;
		if($sType=='ishide' || $nStatus==2){
			$arrMap['blog_isshow']=0;
		}
		elseif($nStatus==1){
			$arrMap['blog_isshow']=1;
		}
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',trim($sId));
			foreach($arrIds as $nId){
				$this->delete_blog_comment($nId);
				$this->delete_blog_trackback($nId);
				$this->update_blog_tag($nId);
				$this->delete_blog_upload($nId);
				$this->update_the_blogs($nId);
			}
		}
	}

	protected function delete_blog_comment($nId){
		$oCommentMeta=CommentModel::M();
		$oCommentMeta->deleteWhere('comment_relationtype=\'blog\' AND comment_relationvalue=?',$nId);
		Cache_Extend::front_widget_comment();
		Cache_Extend::front_widget_static();
	}

	protected function update_the_blogs($nId){
		$oBlog=BlogModel::F('blog_id=?',$nId)->query();
		if(!empty($oBlog['blog_id'])){
			if($oBlog['category_id']!=-1){
				$oCategory=CategoryModel::F('category_id=?',$oBlog['category_id'])->query();
				$oCategory->category_blogs=$oCategory->category_blogs-1;
				$oCategory->save(0,'update');
				if($oCategory->isError()){
					$this->E($oCategory->getErrorMessage());
				}
				Cache_Extend::front_widget_category();
			}
			if($oBlog['user_id']){
				$oUser=UserModel::F('user_id=?',$oBlog['user_id'])->query();
				$oUser->user_blogs=$oUser->user_blogs-1;
				$oUser->save(0,'update');
				if($oUser->isError()){
					$this->E($oUser->getErrorMessage());
				}
			}
			$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
			$oCacheData->countcache_topicsnum=$oCacheData->countcache_topicsnum  -1;
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->E($oCacheData->getErrorMessage());
			}
		}
	}

	protected function delete_blog_trackback($nId){
		$oTrackbackMeta=TrackbackModel::M();
		$oTrackbackMeta->deleteWhere('blog_id=?',$nId);
	}

	protected function update_blog_tag($nId){
		$oTagModel=new TagModel();
		$oTagModel->getDb()->query("UPDATE ".$oTagModel->getTablePrefix(). "tag SET blog_id=REPLACE(blog_id,',$nId,',','),tag_usenum=tag_usenum-1 WHERE blog_id LIKE '%".$nId."%' ");
		$oTagModelMeta=TagModel::M();
		$oTagModelMeta->deleteWhere("blog_id=?",',');
		Cache_Extend::front_widget_hottag();
		Cache_Extend::front_widget_static();
	}

	protected function delete_blog_upload($nId){
		$oUploadModel=new UploadModel();
		$oUploadModel->getDb()->query("UPDATE ".$oUploadModel->getTablePrefix(). "upload SET upload_module='upload',upload_record=0 WHERE upload_record=".$nId);
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_recentimage();
	}

	public function bEdit_(){
		$nBlogId=G::getGpc('id','G');
		$arrTags=TagModel::F()->query()->getTagsByBlogId($nBlogId);
		$arrTagsString=array();
		foreach($arrTags as $arrValue){
			$arrTagsString[]=$arrValue['tag_name'];
		}
		$this->assign('sBlogTag',implode(",",$arrTagsString));
		$this->assign('sEditor',$this->_arrOptions['write_blog_editor']);
		$this->get_emot();
		$this->bIndex_();
	}

	public function AEditObject_($oBlog){
		$sContent=$oBlog->blog_content;
		if($this->_arrOptions['write_blog_editor']!='kindeditor'){
			$sBrCode=IS_WIN?"\r\n":"\n";
			$sContent=str_replace('<br/>',$sBrCode,$sContent);
		}
		$oBlog->blog_content=$sContent;
	}

	public function get_emot(){
		$arrResult= Dyhb::cache('global_emot');
		if($arrResult===false){// 没有缓存，则更新一遍缓存
			Cache_Extend::global_emot('admin');
			Cache_Extend::global_emot('blog');
			$arrResult=Dyhb::cache('global_emot');
		}
		$this->assign('arrEmots',$arrResult);
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _edit_get_display_sceen_options(){
		return true;
	}

	public function _add_get_display_sceen_options(){
		return true;
	}

	public function _edit_get_sceen_options_value(){
		return $this->_add_get_sceen_options_value();
	}

	public function _add_get_sceen_options_value(){
		return "<select name='configs[write_blog_editor]' id='write_blog_editor' class='field' >
<option value='quicktags' ".($this->_arrOptions['write_blog_editor']=='quicktags'?'selected':'')." >".G::L("QuickTags")."</option>
<option value='ubb' ".($this->_arrOptions['write_blog_editor']=='ubb'?'selected':'')." >".G::L("UBB编辑器")."</option>
<option value='kindeditor' ".($this->_arrOptions['write_blog_editor']=='kindeditor'?'selected':'').">".G::L("Kindeditor 可视化编辑器")."</option>
<option value='no' ".($this->_arrOptions['write_blog_editor']=='no' || empty($this->_arrOptions['write_blog_editor'])? 'selected':'').">".G::L("空白")."</option>
</select>&nbsp;&nbsp;
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function _add_get_sceen_options_title(){
		return G::L('切换编辑器');
	}

	public function _edit_get_sceen_options_title(){
		return G::L('切换编辑器');
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('默认显示的是几个常用的日志选项，你可以点击图标“More” 来获取更多的选项。','blog').'</p><ul>'.
				'<p><strong>'.G::L('日志标题').'</strong> -'.G::L('为您的文章键入一个标题。','blog').'</p>'.
				'<p><strong>'.G::L('编辑器').'</strong> -'.G::L('在这里键入您的文章内容。文章编辑器提供了两种模式：“可视化”和“HTML”。点选您需要的模式即可进行切换。“可视化模式”提供一个所见即所得编辑器。我们使用的是KindEditor，你可以访问其官方网站了解详情。','blog').'</p>'.
				'<p><strong>'.G::L('多媒体').'</strong> -'.G::L('在日志上面有一个“文件管理器”，在里面可以上传多媒体，插入多媒体到日志等等操作。','blog').'</p>'.
				'<p><strong>'.G::L('分页符').'</strong> -'.G::L('点击分页符按钮，可以插入分页号。分页符可以分割你的日志，让长日志不再让你烦恼。','blog').'</p>'.
				'<p><strong>'.G::L('发布').'</strong> -'.G::L('保存文章有两个按钮，一个“保存草稿”和“发布”，保存草稿将会保存日志为草稿，发布会发布该文章。','blog').'</p>'.
				'<p><strong>'.G::L('缩略图').'</strong> -'.G::L('为文章配上一张特色图片。若您的主题具有“缩略图片”功能，这张图片可能会显示在首页、页面顶端等位置。','blog').'</p>'.
				'<p><strong>'.G::L('发送 trackback').'</strong> -'.G::L('Trackback 是通知其它博客系统您已链接至它们的一种方式。请输入希望发送 trackback 至哪个（哪些） URL。','blog').'</p>'.
				'<p><strong>'.G::L('讨论').'</strong> -'.G::L('您可以设置评论和引用通告的开关。若该篇文章有评论，您可以在这里浏览、审核评论。','blog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bAdd_(){
		$this->assign('nCurrentTimestamp',CURRENT_TIMESTAMP);
		$this->assign('sEditor',$this->_arrOptions['write_blog_editor']);
		$this->get_emot();
		$this->bIndex_();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('您可以通过以下方法来自定义本页面：','blog').'</p><ul>'.
				'<li>'.G::L('您可以通过点击列表左上方的文字链接来过滤列表显示的项目 —— 全部、页面、日志、草稿。默认视图中，显示所有文章。','blog').'</li>'.
				'<li>'.G::L('您可在“页面选项”中依据您的需要隐藏或显示每页显示的文章数量。','blog').'</li></ul>'.
				'<p>'.G::L('将鼠标光标悬停在文章列表中的某一行，操作链接将会显示出来，您可以通过它们快速管理文章。您可进行下列操作：','blog').'</p><ul>'.
				'<li>'.G::L('点击“编辑”可在编辑器中编辑该文章。当然，直接点击文章标题也是可以的。','blog').'</li>'.
				'<li>'.G::L('点击“删除”，该文章将会从列表中删除。','blog').'</li>'.
				'<li>'.G::L('“访问”将把您带到站点前台，访问已经发布的这篇文章。','blog').'</li>'.
				'<li>'.G::L('你可以选择“批量自动摘要”来对日志批量生成摘要。','blog').'</li></ul>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bIndex_(){
		$oCategoryTree=new TreeCategory();
		foreach($this->getBlogCategory()as $oCategory){
			$oCategoryTree->setNode($oCategory->category_id,$oCategory->category_parentid,$oCategory->category_name);
		}
		$this->assign('oCategoryTree',$oCategoryTree);
	}

	public function getBlogCategory(){
		return CategoryModel::F()->order('category_id ASC,category_compositor DESC')->all()->query();
	}

	public function getTagsByBlogId($nBlogId){
		return TagModel::F()->query()->getTagsByBlogId($nBlogId);
	}

	public function draft(){
		$this->action('draft',G::L('转入草稿箱成功！'));
	}

	public function undraft(){
		$this->action('unDraft',G::L('发布草稿箱日志成功！'));
	}

	public function top(){
		$this->action('top',G::L('置顶文章成功！'));
	}

	public function untop(){
		$this->action('unTop',G::L('取消日志置顶成功！'));
	}

	public function lock(){
		$this->action('lock',G::L('锁定文章成功！'));
	}

	public function unlock(){
		$this->action('unLock',G::L('日志解锁成功！'));
	}

	public function page(){
		$this->action('page',G::L('日志转为页面成功！'));
	}

	public function blog(){
		$this->action('blog',G::L('页面转为日志成功！'));
	}

	public function category(){
		$arrBlogIds=explode(',',G::getGpc('id','G'));
		$nCategoryId=intval(G::getGpc('cid','G'));
		foreach($arrBlogIds as $nValue){
			$oBlog=BlogModel::F('blog_id=?',$nValue)->query();
			$oBlog->category_id=$nCategoryId;
			$oBlog->save(0,'update');
		}
		$this->S(G::L('移动分类成功！'));
	}

	public function action($sAction,$sTitle){
		$sId=G::getGpc('id','G');
		BlogModel::F()->query()->logAction(explode(',',$sId),$sAction);
		$this->S($sTitle);
	}

	public function autosave(){
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
			$oBlog->blog_dateline=CURRENT_TIMESTAMP;
		}

		$oBlog->blog_title=$sBlogTitle;
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

	public function bUpdate_(){
		$this->bInsert_();
	}

	public function bInsert_(){
		$nBlogId=G::getGpc('id');
		$sBlogExcerpt=G::getGpc('blog_excerpt');
		$sBlogContent=G::getGpc('blog_content');
		$sBlogUrlname=G::getGpc('blog_urlname');
		if($this->_arrOptions['auto_excerpt'] && empty($sBlogExcerpt)){
			if(preg_match('|<p>(.*?)</p>|is',$sBlogContent,$arrMatches)){
				$sBlogExcerpt=trim(strip_tags($arrMatches[1]));
			}
			if(empty($sBlogExcerpt)){
				$sBlogExcerpt=String::subString(trim(strip_tags($sBlogContent)),0,255);
			}
			$sBlogExcerpt='<p>'.$sBlogExcerpt.'</p>';
		}
		$_POST['blog_excerpt']=$this->clear_excerpt_upload_ubb($sBlogExcerpt);

		if(!empty($sBlogUrlname)){
			if($nBlogId >0){
				$nBlogs=BlogModel::F('blog_id <>? AND blog_urlname=?',$nBlogId,$sBlogUrlname)->all()->getCounts();
				if($nBlogs>0)
					$sBlogUrlname=$sBlogUrlname.'-'.$nBlogId;
			}
			else{
				$nBlogs=BlogModel::F('blog_urlname=?',$sBlogUrlname)->all()->getCounts();
				if($nBlogs>0){
					$oDb=Db::RUN();
					$arrMaxid=$oDb->getOne("SELECT MAX(blog_id)AS max_blog_id FROM ".BlogModel::F()->query()->getTablePrefix()."blog");
					$sBlogUrlname=$sBlogUrlname.'-'.($arrMaxid['max_blog_id']+1);
				}
			}
		}
		$_POST['blog_urlname']=$sBlogUrlname;
		$_POST['blog_content']=$this->convertContent($_POST['blog_content']);
	}

	public function convertContent($sContent){
		if($this->_arrOptions['write_blog_editor']!='kindeditor'){
			$sContent=str_replace("\n",'<br/>',str_replace("\r\n",'<br/>',$sContent));
		}
		return $sContent;
	}

	public function clear_excerpt_upload_ubb($sExcerpt){
		return preg_replace(array("/\[url=([^\[]*)\]\[img(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/img\]\[\/url\]/ise",
									"/\[img(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/img\]/ise",
									"/\[url=([^\[]*)\]\[upload(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/upload\]\[\/url\]/ise",
									"/\[upload(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/upload\]/ise",
									"/\[uploadfile\]\s*(\S+?)\s*\[\/uploadfile\]/ise",
									"/\[file\]\s*(\S+?)\s*\[\/file\]/ise",
									"/\[newpage\]/ise"),
								array('','','','','','',''),$sExcerpt);
	}

	public function AInsertObject_($oBlogModel){
		$oBlogModel->blog_dateline=$oBlogModel->getDateline();
		$oBlogModel->blog_type=$oBlogModel->getBlogType();
		$oBlogModel->blog_lastpost=serialize(array('comment_id'=>-1,'create_dateline'=>$oBlogModel->getDateline(),'user_name'=>($GLOBALS['___login___']===false ?G::L('跌名'):$GLOBALS['___login___']['user_name']),'user_id'=>($GLOBALS['___login___']===false ?-1:$GLOBALS['___login___']['user_id'])));
	}

	public function AUpdateObject_($oBlogModel){
		$oBlogModel->blog_dateline=$oBlogModel->getDateline();
		$oBlogModel->blog_type=$oBlogModel->getBlogType();
		if($oBlogModel['category_id']!=G::getGpc('category_id')){
			$oDb=Db::RUN();
			if($oBlogModel['category_id']!=-1){
				$sSql="UPDATE `".AccessModel::F()->query()->getTablePrefix()."category` SET category_blogs=category_blogs-1 WHERE `category_id`=".$oBlogModel['category_id'];
				$oDb->query($sSql);
			}
			if(G::getGpc('category_id')!=-1){
				$sSql="UPDATE `".AccessModel::F()->query()->getTablePrefix()."category` SET category_blogs=category_blogs+1 WHERE `category_id`=".G::getGpc('category_id');
				$oDb->query($sSql);
			}
			Cache_Extend::front_widget_category();
		}
	}

	protected function aInsert($nId,$bUpdate=false){
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
		Cache_Extend::front_widget_static();
		$nBlogId=G::getGpc('id','P');
		$sTag=trim(G::getGpc('blog_tag','P'));
		if($nBlogId>0){
			$bResult=TagModel::F()->query()->updateTag($sTag,$nId);
			if($bResult===false){
				$this->E(G::L('标签更新失败!'));
			}
		}
		else{
			$bResult=TagModel::F()->query()->addTag($sTag,$nId);
			if($bResult===false){
				$this->E(G::L('标签插入失败!'));
			}
		}
		Cache_Extend::front_widget_hottag();
		Cache_Extend::front_widget_static();
		if($bUpdate===false){
			if(G::getGpc('category_id')!=-1){
				$oBlog=BlogModel::F('blog_id=?',$nId)->query();
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
			$oCacheData->countcache_todaynum=$oCacheData->countcache_todaynum+1;
			$oCacheData->countcache_topicsnum=$oCacheData->countcache_topicsnum+1;
			$oCacheData->save(0,'update');
			if($oCacheData->isError()){
				$this->E($oCacheData->getErrorMessage());
			}
		}
		else{}
		$nImg2local=G::getGpc('img2local','P');
		if($nImg2local){
			G::cookie('blog_img2local',$nImg2local);
			G::cookie('blog_content',G::stripslashes(G::getGpc('blog_content')));
		}
		G::cookie('blog_id',$nId);
		$sBlogTrackback=G::getGpc('blog_trackback');
		if(empty($sBlogTrackback)||$sBlogTrackback==G::L('每行输入一个引用地址')){
			return;
		}
		$sBlogExcerpt=G::getGpc('blog_excerpt');
		$sBlogContent=G::getGpc('blog_content');
		if(empty($sBlogExcerpt)){
			if(preg_match('|<p>(.*?)</p>|is',G::getGpc('blog_content'),$arrMatches)){
				$sBlogExcerpt=trim(strip_tags($arrMatches[1]));
			}
			if(empty($sBlogExcerpt)){
				$sBlogExcerpt=String::subString(trim(strip_tags($sBlogContent)),0,255);
			}
			$sBlogExcerpt='<p>'.$sBlogExcerpt.'</p>';
		}
		$sBlogExcerpt=$this->clear_excerpt_upload_ubb($sBlogExcerpt);
		G::cookie('blog_title',G::stripslashes(G::getGpc('blog_title')));
		G::cookie('blog_excerpt',G::stripslashes($sBlogExcerpt));
		G::cookie('blog_trackback',$sBlogTrackback);
	}

	public function trackback(){
		if(!isset($_POST['send_trackback'])){
			$sBlogUrl=$this->_arrOptions['blog_url'];
			$sBlogName=G::stripslashes($this->_arrOptions['blog_name']);
			$sBlogTitle=G::stripslashes(G::cookie('blog_title'));
			$sBlogExcerpt=G::stripslashes(htmlspecialchars(G::cookie('blog_excerpt')));
			$sBlogTrackback=G::stripslashes(G::cookie('blog_trackback'));
			$nId=intval(G::cookie('blog_id'));
			$this->assign('__MessageTitle__',G::L('确认发送引用'));
			$this->assign('__WaitSecond__',60);
			$arrBlogTrackbacks=explode("\n",$sBlogTrackback);
			$sMessage=G::L("您的日志或者草稿已经成功保存。接下来，将向以下地址发送引用通告：")."<br/>";
			foreach($arrBlogTrackbacks as $sTrackback){
				$sMessage.=$sTrackback.'<br />';
			}
			$sMessage.=G::L("如果您保存了一篇草稿，并不希望现在就发送引用，请点击下面的取消按钮。否则请点击开始按钮发送引用。")."<br><br>";
			$sMessage.="
<div align=center><form action='".G::U('blog/trackback')."' method='post'>
	<input type='hidden' name='blog_title' value=\"{$sBlogTitle}\">
	<input type='hidden' name='blog_excerpt' value=\"{$sBlogExcerpt}\">
	<input type='hidden' name='blog_name' value=\"{$sBlogName}\">
	<input type='hidden' name='blog_url' value='{$sBlogUrl}/index.php?p={$nId}'>
	<input type='hidden' name='blog_trackback' value='{$sBlogTrackback}'>
  <input type='submit' value='".G::L("开始发送")."' class='button' name=\"send_trackback\">
  <input type='button' value='".G::L("取消发送")."' class='button' onclick='window.location=(\"".G::U('blog/index')."\");' class='field'>
  </form></div>
  <ul>
 <li><a href=\"index.php?p={$nId}\">".G::L("前往此日志")."</a></li>
 </ul>";
			$this->S($sMessage);
			G::cookie('blog_title',null);
			G::cookie('blog_excerpt',null);
			G::cookie('blog_trackback',null);
		}
		else{
			$sBlogUrl=G::stripslashes(G::getGpc('blog_url','P'));
			$sBlogName=G::stripslashes(G::getGpc('blog_name','P'));
			$sBlogTitle=G::stripslashes(G::getGpc('blog_title','P'));
			$sBlogExcerpt=G::stripslashes(G::getGpc('blog_excerpt','P'));
			$sBlogTrackback=G::stripslashes(G::getGpc('blog_trackback','P'));
			$this->assign('__MessageTitle__',G::L('引用发送反馈消息'));
			$this->assign('__WaitSecond__',10);
			$nImg2local=G::cookie('blog_img2local');
			$this->assign('__JumpUrl__',$nImg2local==1?G::U('blog/img2local'): G::U('blog/index'));
			G::cookie('blog_img2local',null);
			if(!$nImg2local) G::cookie('blog_id',null);
			$oTrackback=new Trackback($sBlogUrl,$sBlogTitle,$sBlogName,$sBlogExcerpt);
			$sTrackbackMessage=$oTrackback->sendTrackback($sBlogTrackback);
			$sTrackbackMessage=G::L("程序已经成功发送了trackback，但即使成功发送了，对方仍然可以选择是否接受：")."<br/>".$sTrackbackMessage;
			if($nImg2local==1){
				$sTrackbackMessage.=G::L("程序还需要进行远程图片本地化处理，即将跳转请求！");
			}
			$this->S($sTrackbackMessage);
		}
	}

	protected function aUpdate($nId){
		$this->aInsert($nId,true);
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_yearhotpost();
		Cache_Extend::front_widget_yearcommentpost();
		Cache_Extend::front_widget_recentpost();
		Cache_Extend::front_widget_randpost();
		Cache_Extend::front_widget_pagepost();
		Cache_Extend::front_widget_monthhotpost();
		Cache_Extend::front_widget_monthcommentpost();
		Cache_Extend::front_widget_commentpost();
		Cache_Extend::front_widget_hotpost();
		Cache_Extend::front_widget_dayhotpost();
		Cache_Extend::front_widget_daycommentpost();
		Cache_Extend::front_widget_archive();
		Cache_Extend::front_widget_calendar();
		Cache_Extend::front_widget_hottag();
		Cache_Extend::front_widget_comment();
		Cache_Extend::front_widget_static();
	}

	public function img2local(){
		if(!isset($_POST['send_img2local'])){
			$sBlogContent=htmlspecialchars(G::cookie('blog_content'));
			G::cookie('blog_content',null);
			$nBlogId=G::cookie('blog_id');
			G::cookie('blog_id',null);
			$this->assign('__MessageTitle__',G::L('确认图片本地化处理'));
			$this->assign('__WaitSecond__',60);
			$this->assign('__JumpUrl__',G::U('blog/index'));
			$sMessage=G::L("您的日志或者草稿已经成功保存。")."<br/>".G::L("你选择了图片本地化处理，如果你并不希望进行图片本地化处理，请点击下面的取消按钮。否则请点击开始图片本地化。")."<br><br>";
			$sMessage.="
<div align=center><form action='".G::U('blog/img2local')."' method='post'>
	<input type='hidden' name='blog_id' value=\"{$nBlogId}\">
	<input type='hidden' name='blog_content' value=\"{$sBlogContent}\">
  <input type='submit' value='".G::L("开始图片本地化处理")."' class='button' name=\"send_img2local\">
  <input type='button' value='".G::L("取消处理")."' onclick='window.location=(\"".G::U('blog/index')."\");' class='button'>
  </form></div>
  <ul>
 <li><a href=\"".G::U('blog/index')."\">".G::L("前往日志列表")."</a></li>
 </ul>";
			$this->S($sMessage);
		}
		else{
			$sBlogContent=G::stripslashes(G::getGpc('blog_content','P'));
			$nBlogId=intval(G::getGpc('blog_id','P'));
			$this->assign('__MessageTitle__',G::L('图片本地化处理消息'));
			$this->assign('__WaitSecond__',3);
			$this->assign('__JumpUrl__',G::U('blog/index'));
			$sUploadStoreWhereDir=$this->getUploadStoreWhereDir();
			$nUploadfileMaxsize=$this->_arrOptions['uploadfile_maxsize'];
			$oImage2Local=new Image2Local($sBlogContent,$nUploadfileMaxsize,'./Public/Upload'.$sUploadStoreWhereDir);
			$oImage2Local->_bThumb=true;// 开启缩略图
			$oImage2Local->_nThumbMaxHeight=100;// 缩略图大小，像素
			$oImage2Local->_nThumbMaxWidth=100;
			$oImage2Local->_sThumbPath="./Public/Upload{$sUploadStoreWhereDir}/Thumb";// 缩略图文件保存路径
			$oImage2Local->_sSaveRule='uniqid';// 设置上传文件规则
			$oImage2Local->setAutoCreateStoreDir(TRUE);
			if($this->_arrOptions['is_images_water_mark']==1){
				$oImage2Local->_bIsImagesWaterMark=true;
			}
			$oImage2Local->_sImagesWaterMarkType=$this->_arrOptions['images_water_type'];
			$oImage2Local->_arrImagesWaterMarkImg=array('path'=>$this->_arrOptions['images_water_mark_img_imgurl'],'offset'=>$this->_arrOptions['images_water_position_offset']);
			$oImage2Local->_arrImagesWaterMarkText=array('content'=>$this->_arrOptions['images_water_mark_text_content'],'textColor'=>$this->_arrOptions['images_water_mark_text_color'],'textFont'=>$this->_arrOptions['images_water_mark_text_fontsize'],'textFile'=>$this->_arrOptions['images_water_mark_text_fonttype'],'textPath'=>$this->_arrOptions['images_water_mark_text_fontpath'],'offset'=>$this->_arrOptions['images_water_position_offset']);
			$oImage2Local->_nWaterPos=$this->_arrOptions['images_water_position'];
			$sBlogContent=$oImage2Local->local();
			$arrPhotoInfo=$oImage2Local->getUploadFileInfo();
			$bResult=UploadModel::F()->query()->upload($arrPhotoInfo,$nBlogId);
			if($bResult===FALSE || trim($sBlogContent)==''){
				$this->E(G::L("保存远程图片信息失败！"));
			}
			else{
				Cache_Extend::front_widget_recentimage();
				$oBlog=BlogModel::F('blog_id=?',$nBlogId)->query();
				$oBlog->blog_content=$sBlogContent;
				$oBlog->save(0,'update');
				if($oBlog->isError()){
					$this->E($oBlog->getErrorMessage());
				}
				$this->S(G::L("保存远程图片信息成功！").G::L("系统共处理远程图片%d个。",'app',null,$oImage2Local->getCounts()));
			}
		}
	}

	protected function getUploadStoreWhereDir(){
		$sUploadStoreWhereType=$this->_arrOptions['upload_store_where_type'];
		if($sUploadStoreWhereType=='month')$sUploadStoreWhereDir='/month_'.date('Ym',CURRENT_TIMESTAMP);
		elseif($sUploadStoreWhereType=='day')$sUploadStoreWhereDir='/day_'.date('Ymd',CURRENT_TIMESTAMP);
		else $sUploadStoreWhereDir='';
		return $sUploadStoreWhereDir;
	}

	public function _excerpt_get_admin_help_description(){
		return '<p>'.G::L('批量自动摘要可以为你批量创建文章摘要。','blog').'</p><ul>'.
				'<p>'.G::L('有时候,我们关闭了系统的自动摘要,而且我们又没有书写摘要,那么这个时候,文章的摘要就是空的。','blog').'</p>'.
				'<p>'.G::L('有一天,你的站点需要摘要来填充,那么空白的摘要让你的站点看起来空洞,这个时候可以通过这里来自动生成。','blog').'</p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function excerpt(){
		$this->bIndex_();
		$this->display();
	}

	public function excerpt_blog(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nBlogCategory=intval(G::getGpc('blog_category'));
		$nRebuild=intval(G::getGpc('rebuild'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['blog_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['blog_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['blog_id']=array('elt',$nEnd);
		}

		if(!empty($nBlogCategory)){
			$arrMap['category_id']=$nBlogCategory;
		}

		if(!$nTotal){
			$nTotal=BlogModel::F()->where($arrMap)->all()->getCounts('blog_id');
		}
		$arrBlogs=BlogModel::F()->where($arrMap)->all()->order('`blog_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrFailds=array();
		$arrSucceeds=array();
		foreach($arrBlogs as $oBlogs){
			$sBlogExcerpt=$oBlogs->blog_excerpt;
			if($nRebuild==1 || empty($sBlogExcerpt)){
				if(preg_match('|<p>(.*?)</p>|is',stripslashes($oBlogs->blog_content),$arrMatches)){
					$sBlogExcerpt=trim(strip_tags($arrMatches[1]));
				}
				if(empty($sBlogExcerpt)){
					$sBlogExcerpt=String::subString(trim(strip_tags(stripslashes($oBlogs->blog_content))),0,255);
				}
				$sBlogExcerpt='<p>'.$sBlogExcerpt.'</p>';
				$oBlogs->blog_excerpt=$this->clear_excerpt_upload_ubb($sBlogExcerpt);
				$oBlogs->save(0,'update');
				if($oBlogs->isError()){
					$arrFailds[]=$oBlogs->blog_id.' '.$oBlogs->getErrorMessage();
				}
				else{
					$arrSuccedds[]=$oBlogs->blog_id;
				}
				$nActionNum++;
			}
			$nCount++;
		}

		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("blog/excerpt_blog?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&blog_category={$nBlogCategory}&rebuild={$nRebuild}&action_num={$nActionNum}");
		$sResultInfo='';
		$sFaild='';
		if(!empty($arrFailds)){
			$sFaild="<br/><div style=\"color:red;\"><h3>".G::L('自动摘要失败日志ID','blog')."</h3><br/>";
			$sFaild.=implode('<br/>',$arrFailds);
			$sFaild.="</div>";
		}
		$sSucceed='';
		if(!empty($arrSuccedds)){
			$sSucceed="<br/><div style=\"color:green;\"><h3>".G::L('自动摘要成功日志ID','blog')."</h3><br/>";
			$sSucceed.=implode('<br/>',$arrSuccedds);
			$sSucceed.="</div>";
		}
		if($nTotal>$nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo.$sFaild.$sSucceed);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',G::U('blog/excerpt'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sFaild.$sSucceed);
		}
	}

	public function _build_get_admin_help_description(){
		return '<p>'.G::L('由于操作过程中难免会出错，本次操作将会重新统计日志的一些数据。','blog').'</p>'.
				'<p>'.G::L('我们将会对附件数量、引用数量、评论数量进行重新统计。','blog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}
	public function build(){
		$this->display();
	}

	public function repaire_blog(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$sType=G::getGpc('type');
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['blog_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['blog_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['blog_id']=array('elt',$nEnd);
		}

		if(!$nTotal){
			$nTotal=BlogModel::F()->where($arrMap)->all()->getCounts('blog_id');
		}
		$arrBlogs=BlogModel::F()->where($arrMap)->all()->order('`blog_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrRecords=array();
		foreach($arrBlogs as $oBlogs){
			$nNum=0;
			switch($sType){
				case 'comment':
					$nNum=$this->repaire_blog_comment($oBlogs);
					break;
				case 'trackback':
					$nNum=$this->repaire_blog_trackback($oBlogs);
					break;
				case 'upload':
					$nNum=$this->repaire_blog_upload($oBlogs);
					break;
			}
			$arrRecords[]=$oBlogs->blog_id.'-'.$nNum;
			$nActionNum++;
			$nCount++;
		}

		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("blog/repaire_blog?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}&type={$sType}&repaire=1");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理日志成功记录','blog')."</h3><br/>";
			$sRecords.=implode('<br/>',$arrRecords);
			$sRecords.="</div>";
		}
		if($nTotal>$nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo.$sRecords);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',G::U('blog/build'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sRecords);
		}
	}

	protected function repaire_blog_comment($oBlogs){
		$nNum=CommentModel::F('`comment_relationtype`=\'blog\' AND `comment_relationvalue`=?',$oBlogs->blog_id)->all()->getCounts();
		if($nNum<0){$nNum=0;}
		$oBlogs->blog_commentnum=$nNum;
		$oBlogs->save(0,'update');
		return $nNum;
	}

	protected function repaire_blog_upload($oBlogs){
		$nNum=UploadModel::F('`upload_module`=\'blog\' AND `upload_record`=?',$oBlogs->blog_id)->all()->getCounts();
		if($nNum<0){$nNum=0;}
		$oBlogs->blog_uploadnum=$nNum;
		$oBlogs->save(0,'update');
		return $nNum;
	}

	protected function repaire_blog_trackback($oBlogs){
		$nNum=TrackbackModel::F('`blog_id`=?',$oBlogs->blog_id)->all()->getCounts();
		if($nNum<0){$nNum=0;}
		$oBlogs->blog_trackbacknum=$nNum;
		$oBlogs->save(0,'update');
		return $nNum;
	}

}
