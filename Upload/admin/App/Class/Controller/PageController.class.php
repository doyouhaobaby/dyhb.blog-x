<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	页面控制器($)*/

!defined('DYHB_PATH') && exit;

class PageController extends BlogController{

	public function filter_(&$arrMap){
		$arrMap['blog_title']=array('like',"%".G::getGpc('blog_title')."%");
		$nUserId=intval(G::getGpc('uid'));// 用户ID
		if($nUserId){
			$arrMap['user_id']=$nUserId;
		}
		$arrMap['blog_ispage']=1;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('页面和文章类似 —— 它们都有标题、正文，以及附属的相关信息。但是它们类似永久的文章，往往并不按照一般博客文章那样，随着时间的流逝逐渐淡出人们的视线。页面并不能被分类、亦不能拥有标签。','blog').'</p>'.
				'<p>'.G::L('管理页面的方法和管理文章的方法类似，本页面也可以以相同的方式自定义。','blog').'</p>'.
				'<p>'.G::L('您可以进行同样的操作，比如使用过滤器筛选列表项、使用鼠标光标悬停的方式进行管理，或进行批量操作。','blog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function edit(){
		$nId=G::getGpc('id','G');
		if(!empty($nId)){
			$oModel=BlogModel::F('blog_id=?',$nId)->query();
			if(!empty($oModel->blog_id)){
				$this->assign('oValue',$oModel);
				$this->assign('nId',$nId);
				$this->display('page+add');
			}
			else{
				$this->E(G::L('数据库中并不存在该页面，或许它已经被删除了！'));
			}
		}
		else{
			$this->E(G::L('编辑项不存在！'));
		}
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('页面和文章类似 —— 它们都有标题、正文，以及附属的相关信息。但是它们类似永久的文章，往往并不按照一般博客文章那样，随着时间的流逝逐渐淡出人们的视线。页面并不能被分类、亦不能拥有标签。','blog').'</p>'.
				'<p>'.G::L('创建页面的方法也和文章类似。','blog').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrMap=$this->map();
		if(method_exists($this,'filter_')){
			$this->filter_($arrMap);
		}
		$this->get_list($arrMap);
		$this->display();
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',trim($sId));
			foreach($arrIds as $nId){
				$this->delete_blog_comment($nId);
				$this->update_blog_tag($nId);
				$this->delete_blog_upload($nId);
				$this->delete_widget_page($nId);
			}
		}
	}

	public function delete_widget_page($nId){
		$sPageType='system_page_'.$nId;
		$arrNormalMenus=E::mbUnserialize($this->_arrOptions['normal_menu']);
			if(is_array($arrNormalMenus)&& !empty($arrNormalMenus)){
			foreach($arrNormalMenus as $nKey=>$sNormalMenu){
				if($sNormalMenu==$sPageType){
					unset($arrNormalMenus[$nKey]);
				}
			}
			$bResult=OptionModel::uploadOption('normal_menu',serialize($arrNormalMenus));
			if($bResult===false)
				$this->E(G::L('从排序数据删除页面失败，请重试！'));
		}
	}

	protected function get_list($arrMap){
		$sParameter='';
		$arrSortUrl=array();
		$nTotalRecord=BlogModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=$this->_arrOptions['admineverynum'];
		foreach($arrMap as $sKey=>$sVal){
			if(!is_array($sVal)){
				$sParameter.=$sKey.'='.urlencode($sVal).'&';
				$arrSortUrl[]='"'.$sKey.'='.urlencode($sVal).'"';
			}
		}

		$sSortBy=strtoupper(G::getGpc('sort_'))=='ASC'?'ASC':'DESC' ;
		$sOrder=G::getGpc('order_')? G::getGpc('order_'): 'blog_id';
		$this->assign('sSortByUrl',strtolower($sSortBy)=='desc'? 'asc':'desc');
		$this->assign('sSortByDescription',strtolower($sSortBy)=='desc'?G::L('倒序'): G::L('升序'));
		$this->assign('sOrder',$sOrder);
		$this->assign('sSortUrl','new Array('.implode(',',$arrSortUrl).')');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page'));
		$oPage->setParameter($sParameter);
		$sPageNavbar=$oPage->P();
		$oList=BlogModel::F()->where($arrMap)->all()->order($sOrder.' '.$sSortBy)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('oList',$oList);
	}

	public function bUpdate_(){
		$this->bInsert_();
	}

	public function bInsert_(){
		$nBlogId=G::getGpc('id');
		$sBlogContent=G::getGpc('blog_content');
		$sBlogUrlname=G::getGpc('blog_urlname');
		if(!empty($sBlogUrlname)){
			if($nBlogId>0){
				$oBlog=BlogModel::F('blog_id <>?',$nBlogId)->getByblog_urlname($sBlogUrlname);
				if($oBlog->blog_id){
					$sBlogUrlname=$sBlogUrlname.'-'.$nBlogId;
				}
			}
			else{
				$oBlog=BlogModel::F()->getByblog_urlname($sBlogUrlname);
				if($oBlog->blog_id){
					$arrMaxid=$oDb->getOne("SELECT MAX(blog_id)AS max_blog_id FROM ".BlogModel::F()->query()->getTablePrefix()."blog");
					$sBlogUrlname=$sBlogUrlname.'-'.($arrMaxid['max_blog_id']+1);
				}
			}
		}
		$_POST['blog_urlname']=$sBlogUrlname;
	}

	public function img2local(){
		if(!isset($_POST['send_img2local'])){
			$sBlogContent=htmlspecialchars(G::cookie('blog_content'));
			G::cookie('blog_content',null);
			$nBlogId=G::cookie('blog_id');
			G::cookie('blog_id',null);
			$this->assign('__MessageTitle__',G::L('确认图片本地化处理'));
			$this->assign('__WaitSecond__',60);
			$this->assign('__JumpUrl__',G::U('page/index'));
			$sMessage.=G::L("您的页面或者草稿已经成功保存。")."<br/>".G::L("你选择了图片本地化处理，如果你并不希望进行图片本地化处理，请点击下面的取消按钮。否则请点击开始图片本地化。")."<br><br>";
			$sMessage.="
<div align=center><form action='".G::U('page/img2local')."' method='post'>
	<input type='hidden' name='blog_id' value=\"{$nBlogId}\">
	<input type='hidden' name='blog_content' value=\"{$sBlogContent}\">
  <input type='submit' value='".G::L("开始图片本地化处理")."' class='button' name=\"send_img2local\">
  <input type='button' value='".G::L("取消处理")."' onclick='window.location=(\"".G::U('page/index')."\");' class='button'>
  </form></div>
  <ul>
 <li><a href=\"".G::U('page/index')."\">".G::L("前往页面列表")."</a></li>
 </ul>";
			$this->S($sMessage);
		}
		else{
			$sBlogContent=G::stripslashes(G::getGpc('blog_content','P'));
			$nBlogId=intval(G::getGpc('blog_id','P'));
			$this->assign('__MessageTitle__',G::L('图片本地化处理消息'));
			$this->assign('__WaitSecond__',3);
			$this->assign('__JumpUrl__',G::U('page/index'));
			$sUploadStoreWhereDir=$this->getUploadStoreWhereDir();
			$nUploadfileMaxsize=$this->_arrOptions['uploadfile_maxsize'];
			$oImage2Local=new Image2Local($sBlogContent,$nUploadfileMaxsize,'./Public/Upload'.$sUploadStoreWhereDir);
			$oImage2Local->_bThumb=true;// 开启缩略图
			$oImage2Local->_nThumbMaxHeight=100;// 缩略图大小，像素
			$oImage2Local->_nThumbMaxWidth=100;
			$oImage2Local->_sThumbPath="./Public/Upload{$sUploadStoreWhereDir}/Thumb";// 缩略图文件保存路径
			$oImage2Local->_sSaveRule=uniqid;// 设置上传文件规则
			$oImage2Local->setAutoCreateStoreDir(TRUE);
			if($this->_arrOptions['is_images_water_mark']==1){$oImage2Local->_bIsImagesWaterMark=true;}
			$oImage2Local->_sImagesWaterMarkType=$this->_arrOptions['images_water_type'];
			$oImage2Local->_arrImagesWaterMarkImg=array('path'=>$this->_arrOptions['images_water_mark_img_imgurl'],'offset'=>$this->_arrOptions['images_water_position_offset']);
			$oImage2Local->_arrImagesWaterMarkText=array('content'=>$this->_arrOptions['images_water_mark_text_content'],'textColor'=>$this->_arrOptions['images_water_mark_text_color'],'textFont'=>$this->_arrOptions['images_water_mark_text_fontsize'],'textFile'=>$this->_arrOptions['images_water_mark_text_fonttype'],'textPath'=>$this->_arrOptions['images_water_mark_text_fontpath'],'offset'=>$this->_arrOptions['images_water_position_offset']);
			$oImage2Local->_nWaterPos=$this->_arrOptions['images_water_position'];
			$sBlogContent=$oImage2Local->local();
			$arrPhotoInfo=$oImage2Local->getUploadFileInfo();
			$bResult=UploadModel::F()->query()->upload($arrPhotoInfo,$nBlogId);
			if($bResult===FALSE){
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

}
