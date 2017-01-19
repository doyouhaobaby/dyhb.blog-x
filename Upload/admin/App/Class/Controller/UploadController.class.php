<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	文件控制器($)*/

!defined('DYHB_PATH') && exit;

class UploadController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['upload_name']=array('like',"%".G::getGpc('upload_name')."%");
	}

	public function init__(){
		parent::init__();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('您上传的所有文件都在“媒体库”中列出，按上传时间先后排列。','upload').'</p>'.
				'<p>'.G::L('通过点击页面上方的过滤器链接（按照文件类型或状态）来缩小列表列出文件的范围。使用列表上方的下拉菜单，您也可以通过指定时间段来过滤列表项。','upload').'</p>'.
				'<p>'.G::L('将鼠标光标移动到某一行上方，将出现几个新的链接：<em>编辑</em>、<em>删除</em>和<em>查看</em>。点击<em>编辑</em>或文件名，会出现一个简单的编辑页面，您可用它进行文件属性的编辑；点击<em>删除</em>将从媒体库中删除该文件（同时，也会从所有包含它的文章中删除）；<em>查看</em>将带您到该文件的显示页面。','upload').'</p>' .
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function get_upload_target_url($oValue){
		return __PUBLIC__.'/Upload/'.$oValue->upload_savepath.'/'.$oValue->upload_savename;
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',trim($sId));
			foreach($arrIds as $nId){
				$oUploadModel=UploadModel::F('upload_id=?',$nId)->query();
				$oUploadModel->reduceUploadNum($oUploadModel->upload_module,$oUploadModel->upload_record);
				if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_savepath.'/'.$oUploadModel->upload_savename)){
					@unlink(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_savepath.'/'.$oUploadModel->upload_savename);
				}
				if(in_array($oUploadModel->upload_extension,array('gif','jpg','jpeg','bmp','png')) && $oUploadModel->upload_isthumb){
					if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_thumbpath.'/'.$oUploadModel->upload_thumbprefix.$oUploadModel->upload_savename)){
						@unlink(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_thumbpath.'/'.$oUploadModel->upload_thumbprefix.$oUploadModel->upload_savename);
					}
				}
			}
		}
	}

	public function get_upload_file_type($oValue){
		$sUploadFileType='';
		if(in_array($oValue->upload_extension,array('gif','jpg','jpeg','bmp','png'))){
			$sUploadFileType=$oValue->upload_isthumb?
					__PUBLIC__.'/Upload/'.$oValue->upload_thumbpath.'/'.$oValue->upload_thumbprefix.$oValue->upload_savename:
					__PUBLIC__.'/Upload/'.$oValue->upload_savepath.'/'.$oValue->upload_savename;
		}
		elseif(in_array($oValue->upload_extension,array('zip','z','gz','gtar','rar'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/archive.png';
		}
		elseif(in_array($oValue->upload_extension,array('aif','aifc','aiff','au','kar','m3u','mid','midi','mp2','mp3','mpga','ra','ram','rm','rpm','snd','wav','wax','wma','aac'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/audio.png';
		}
		elseif(in_array($oValue->upload_extension,array('asf','asx','avi','mov','movie','mpeg','mpe','mpg','mxu','qt','wm','wmv','wmx','wvx','rmvb','flv','mp4'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/video.png';
		}
		elseif(in_array($oValue->upload_extension,array('doc','pdf','ppt'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/document.png';
		}
		elseif(in_array($oValue->upload_extension,array('txt','ascii','mime'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/text.png';
		}
		elseif(in_array($oValue->upload_extension,array('xls','et'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/spreadsheet.png';
		}
		elseif(in_array($oValue->upload_extension,array('as','flash'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/interactive.png';
		}
		elseif(in_array($oValue->upload_extension,array('h','c','h','cpp','dfm','pas','frm','vbs','asp','jsp','java','class','php'))){
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/code.png';
		}
		else{
			$sUploadFileType=__PUBLIC__.'/Images/Crystal/default.png';
		}
		return $sUploadFileType;
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('您可以不创建文章，而上传多媒体文件。这样，您就可以得到一个用于分享的网络链接，当然，也可以在日后将这写媒体文件用在文章中。','upload').'</p>'.
				'<p>'.G::L('有两种上传文件的方式：<em>选择文件</em>将打开一个基于 Flash 的上传工具（可一次上传多个文件），或者可以使用<em>浏览器上传工具</em>。点击<em>选择文件</em>将打开一个导航窗口，显示您操作系统中的文件。选择文件后，请点击<em>打开</em>，这时文件将被自动上传，同时将出现进度条，显示上传的情况。','upload').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_display_sceen_options(){
		return true;
	}

	public function _add_get_sceen_options_value(){
		return "<input type=\"radio\" id=\"upload_file_default\" class=\"field\" name=\"configs[upload_file_default]\" value=\"1\"".($this->_arrOptions['upload_file_default']==1? "checked=\"checked\"":"")."/>".G::L("传统表单").
"<input type=\"radio\" id=\"\" class=\"field\" name=\"configs[upload_file_default]\" value=\"0\" ".($this->_arrOptions['upload_file_default']==0? "checked=\"checked\"":"")." />".G::L("Flash 批量上传").'  '.G::L("修改文件上传方式")."</label>
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function bAdd_(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$arrAllAllowType=unserialize($this->_arrOptions['all_allowed_upload_type']);
		$this->assign('arrAllAllowType',$arrAllAllowType);
		$arrAllAllowTypeTemp=array();
		if($arrAllAllowType){
			foreach($arrAllAllowType as $sValue){
				$arrAllAllowTypeTemp[]="*.".$sValue;
			}
		}
		$sAllAllowType=implode(';',$arrAllAllowTypeTemp);
		unset($arrAllAllowType,$arrAllAllowTypeTemp);
		$this->assign('sAllAllowType',$sAllAllowType);
		$this->getUploadCategory();
		$this->assign('nFileInputNum',$this->_arrOptions['file_input_num']);
		$this->assign('nUploadfileMaxsize',$this->_arrOptions['uploadfile_maxsize']);
		$this->assign('nIsUploadAuto',$this->_arrOptions['is_upload_auto']);
		$this->assign('nSimUploadLimit',$this->_arrOptions['sim_upload_limit']);
	}

	public function _edit_get_admin_help_description(){
		return '<p>'.G::L('在此页面，您可编辑媒体库中文件的 3 项属性。','upload').'</p>'.
				'<p>'.G::L('如果你要更新媒体，可以考虑新上传一个。','upload').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function edit(){
		$nId=G::getGpc('id','G');
		if(!empty($nId)){
			$oUploadModel=UploadModel::F('upload_id=?',$nId)
					->query();
			if(!empty($oUploadModel->upload_id)){
				$this->assign('arrUploadCategorys',$this->getUploadCategory(true));
				$this->assign('oValue',$oUploadModel);
				$this->assign('nId',$nId);
				$this->display();
			}
			else{
				$this->assign('arrUploadCategorys',array());
				$this->E(G::L('数据库中并不存在该项，或许它已经被删除了！'));
			}
		}
		else{
			$this->E(G::L('编辑项不存在！'));
		}
	}

	public function upload_page(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$arrAllAllowType=unserialize($this->_arrOptions['all_allowed_upload_type']);
		$this->assign('arrAllAllowType',$arrAllAllowType);
		$arrAllAllowTypeTemp=array();
		if($arrAllAllowType){
			foreach($arrAllAllowType as $sValue){
				$arrAllAllowTypeTemp[]="*.".$sValue;
			}
		}
		$sAllAllowType=implode(';',$arrAllAllowTypeTemp);
		unset($arrAllAllowType,$arrAllAllowTypeTemp);
		$this->assign('sAllAllowType',$sAllAllowType);
		$this->getUploadCategory();
		$this->assign('nFileInputNum',$this->_arrOptions['file_input_num']);
		$this->assign('nUploadfileMaxsize',$this->_arrOptions['uploadfile_maxsize']);
		$this->assign('nIsUploadAuto',$this->_arrOptions['is_upload_auto']);
		$this->assign('nSimUploadLimit',$this->_arrOptions['sim_upload_limit']);
		$this->display();
	}

	public function getUploadCategory($bReturn=false){
		if($bReturn===true){
			return UploadcategoryModel::F()->all()->asArray()->query();
		}
		$this->assign('arrUploadCategory',UploadcategoryModel::F()->all()->asArray()->query());
	}

	public function upload_file(){
		$nUploadFileDefault=$this->_arrOptions['upload_file_default'];
		if(empty($_FILES)){
			if($nUploadFileDefault==1){$this->E(G::L('你没有选择任何文件！'));}
			else{echo(G::L('你没有选择任何文件！'));}
			return;
		}
		$sUploadStoreWhereDir=$this->getUploadStoreWhereDir();
		$arrAllAllowType=@unserialize($this->_arrOptions['all_allowed_upload_type']);
		$nUploadfileMaxsize=$this->_arrOptions['uploadfile_maxsize'];
		if($nUploadFileDefault==1){
			$oUploadfile=new UploadFile($nUploadfileMaxsize,implode(',',$arrAllAllowType),'','./Public/Upload'.$sUploadStoreWhereDir);
		}
		else{
			$oUploadfile=new UploadFileForUploadify($nUploadfileMaxsize,$arrAllAllowType,'','./Public/Upload'.$sUploadStoreWhereDir);
		}
		if($this->_arrOptions['is_makeimage_thumb']==1){
			$oUploadfile->_bThumb=true;
		}// 开启缩略图
		$arrThumbMax=@unserialize($this->_arrOptions['blogfile_thumb_width_heigth']);
		$oUploadfile->_nThumbMaxHeight=$arrThumbMax[0];// 缩略图大小，像素
		$oUploadfile->_nThumbMaxWidth=$arrThumbMax[1];
		$oUploadfile->_sThumbPath="./Public/Upload{$sUploadStoreWhereDir}/Thumb";// 缩略图文件保存路径
		$oUploadfile->_sSaveRule='uniqid';// 设置上传文件规则
		if($this->_arrOptions['is_images_water_mark']==1){
			$oUploadfile->_bIsImagesWaterMark=true;
		}
		$oUploadfile->_sImagesWaterMarkType=$this->_arrOptions['images_water_type'];
		$oUploadfile->_arrImagesWaterMarkImg=array('path'=>$this->_arrOptions['images_water_mark_img_imgurl'],'offset'=>$this->_arrOptions['images_water_position_offset']);
		$oUploadfile->_arrImagesWaterMarkText=array('content'=>$this->_arrOptions['images_water_mark_text_content'],'textColor'=>$this->_arrOptions['images_water_mark_text_color'],'textFont'=>$this->_arrOptions['images_water_mark_text_fontsize'],'textFile'=>$this->_arrOptions['images_water_mark_text_fonttype'],'textPath'=>$this->_arrOptions['images_water_mark_text_fontpath'],'offset'=>$this->_arrOptions['images_water_position_offset']);
		$oUploadfile->_nWaterPos=$this->_arrOptions['images_water_position'];
		$oUploadfile->setAutoCreateStoreDir(TRUE);
		if(!$oUploadfile->upload()){
			if($nUploadFileDefault==1){$this->E($oUploadfile->getErrorMessage());}
			else{exit($oUploadfile->getErrorMessage());}
		}
		else{
			$arrPhotoInfo=$oUploadfile->getUploadFileInfo();
		}
		$nRecordId=G::getGpc('record_id');
		if($nRecordId===null)$nRecordId=-1;
		$bResult=UploadModel::F()->query()->upload($arrPhotoInfo,$nRecordId);
		if($bResult===FALSE){
			if($nUploadFileDefault==1){$this->E(G::L("保存附件信息失败！"));}
			else{ echo(0);}
		}
		else{
			Cache_Extend::front_widget_recentimage();
			Cache_Extend::front_widget_static();
			if($nUploadFileDefault==1){$this->S(G::L("保存附件信息成功！"));}
			else{ echo(1);}
		}
		return;
	}

	public function lists(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$sType=G::getGpc('type');//附件类型
		$nUploadCategoryId=intval(G::getGpc('cid'));//相册分类
		$arrMap=array();
		if(!empty($nUploadCategoryId)){
			$arrMap['uploadcategory_id']=$nUploadCategoryId;
		}
		if(empty($sType)){ }
		elseif($sType=='current'){
			$nRecordId=intval(G::getGpc('id'));
			$sModule=G::getGpc('module');
			$arrMap['upload_module']=$sModule;
			$arrMap['upload_record']=$nRecordId;
		}
		elseif($sType=='not_photo'){
			$arrMap['upload_extension']=array(array('neq','jpg'),array('neq','jpeg'),array('neq','png'),array('neq','gif'),array('neq','bmp'),'and');
		}
		elseif($sType=='photo'){
			$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		}
		else{
			$arrMap['upload_extension']=$sType;
		}

		$nTotalUploadRecord=UploadModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=$this->_arrOptions['admineverynum'];
		$sParameter='';
		foreach($arrMap as $sKey=>$sVal){
			if(!is_array($sVal)){
				$sParameter.=$sKey.'='.urlencode($sVal).'&';
			}
		}
		$oPage=Page::RUN($nTotalUploadRecord,$nEverynum,G::getGpc('page'));
		$oPage->setParameter($sParameter);
		$sPageNavbar=$oPage->P();
		$oListUpload=UploadModel::F()->where($arrMap)->all()->order("`create_dateline` DESC")->limit($oPage->returnPageStart(),$nEverynum)->query();
		$arrAllAllowType=unserialize($this->_arrOptions['all_allowed_upload_type']);
		$sResultInfo=
"<div id=\"checkList\">
<div class=\"morecontrol\">".G::L("批量操作：")."
<input type=\"checkbox\" onclick=\"checkAll('checkList')\">&nbsp;&nbsp;
<a href=\"#\" onclick=\"javascript:uploadAction('insert');\">".G::L("批量插入")."</a>&nbsp;&nbsp;
<a href=\"#\" onclick=\"javascript:uploadAction('del');\">".G::L("批量删除")."</a>&nbsp;&nbsp;
<select name=\"filesort\" id=\"filesort\" onChange=\"changeUploadCategory(this);\">
<option value=\"\" selected=\"selected\">".G::L("批量移动到分类")."</option>";
		$arrUploadCategorys=$this->getUploadCategory(true);
		foreach($arrUploadCategorys as $arrUploadCategory){
			$sResultInfo.="<option value=\"".$arrUploadCategory['uploadcategory_id']."\">".$arrUploadCategory['uploadcategory_name']."</option>";
		}
		$sResultInfo.="
<option value=\"-1\">".G::L("未分类")."</option>
</select>&nbsp;&nbsp;
<select onchange=\"javascript:location.href=this.value;\">
<option value=\"\">".G::L("按格式浏览附件")."</option>";
		foreach($arrAllAllowType as $sAllowType){
			$sResultInfo.="<option value=\"".G::U('upload/lists?type='.$sAllowType)."\">".$sAllowType."</option>";
		}
		$sResultInfo.="
</select></div>
<ul>";
		foreach($oListUpload as $oUpload){
			if($oUpload->uploadcategory_id=='-1'){
				$arrUploadCategory=array('uploadcategory_id'=>'-1','uploadcategory_name'=>G::L('未分类'));
			}
			else{
				$arrUploadCategory=array('uploadcategory_id'=>$oUpload->uploadcategory_id,'uploadcategory_name'=>$oUpload->uploadcategory->uploadcategory_name);
			}
			$sImgTargetSrc=__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
			$sUploadExtension=&$oUpload->upload_extension;
			$sFilesize=E::changeFileSize($oUpload->upload_size);
			$sUploadDateline=date('Y-m-d h:i:s',$oUpload->create_dateline);
			if(!in_array($sUploadExtension,array('gif','jpg','jpeg','bmp','png'))){
				if(!in_array($sUploadExtension,array('mp3','wma','wav'))){
					$sAction="<a href=\"{$sImgTargetSrc}\" target=\"_blank\" >".G::L("按格式浏览附件")."</a>";
					$sFileDown=$sImgTargetSrc;
				}
				else{
					$sAction="<a href=\"{$sImgTargetSrc}\" target=\"_blank\">".G::L("播放")."</a>";
					$sFileDown=$sImgTargetSrc;
				}
				$nUploadId=&$oUpload->upload_id;
				if($sUploadExtension=="swf"){
					$sInsert="[swf=400,300]{$nUploadId}[/swf]";
				}
				elseif(in_array($sUploadExtension,array('wma','asf','wmv'))){
					$sInsert="[wmp=400,300]{$nUploadId}[/wmp]";
				}
				elseif($sUploadExtension=='mp3'){
					$sInsert="[mp3]{$nUploadId}[/mp3]";
				}
				elseif(in_array($sUploadExtension,array('rm','rmvb','ra','ram'))){
					$sInsert="[real=400,300]{$nUploadId}[/real]";
				}
				elseif(in_array($sUploadExtension,array('html','htm','txt'))){
					$sInsert="[url]{$nUploadId}[/url]";
				}
				elseif(in_array($sUploadExtension,array('flv','mp4','aac'))){
					$sInsert="[flv=400,300]{$nUploadId}[/flv]";
				}
				else{
					$sInsert="[uploadfile]{$nUploadId}[/uploadfile]";
				}
				$sResultInfo.="
<li id=\"filelist\">
	<a href=\"{$sImgTargetSrc}\" target=\"_blank\" title=\"Name:".$oUpload->upload_name.' | Size:'.$sFilesize.' | Upload Time:'.$sUploadDateline."\">
	<img src=\"".__TMPLPUB__."/Images/file.gif\" width=\"80\" height=\"80\" border=\"0\"/></a><br>
	<input name=\"key\" type=\"checkbox\" value=\"{$nUploadId}\" ><a href=\"".G::U('upload/lists?cid='.$arrUploadCategory['uploadcategory_id'])."\" >".$arrUploadCategory['uploadcategory_name']."</a><br>
	{$sAction}
	<a href=\"javascript:parent.newPage('{$sInsert}');\">".G::L("插入")."</a><br>
	<span>{$sUploadExtension}</span>
	<a href=\"".G::U('upload/update_file?id='.$nUploadId)."\">".G::L("更新")."</a><br>
	下载(".$oUpload->upload_download.")
	<a href=\"javascript:parent.dyhbConfirm(D.L('".G::L("你确认删除附件吗？")."'),function(){ parent.delUpload('{$nUploadId}','upload','".__APP__."/upload/lists');art.dialog.open.api.close();});\">".G::L("删除")."</a>
</li>";
			}
			else{
				$sImgSrc=$oUpload->upload_isthumb?
					__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:
					$sImgTargetSrc;
				$nUploadId=&$oUpload->upload_id;
				$sResultInfo.="
<li id=\"filelist\">
	<a href=\"{$sImgTargetSrc}\" class=\"preview\" target=\"_blank\" title=\"Name:".$oUpload->upload_name.' | Size:'.$sFilesize.' | Upload Time:'.$sUploadDateline."\">
	<img src=\"{$sImgSrc}\" width=\"80\" height=\"80\" border=\"0\"/></a><br>
	<input name=\"key\" type=\"checkbox\" value=\"{$nUploadId}\" ><a href=\"".G::U('upload/lists?cid='.$arrUploadCategory['uploadcategory_id'])."\" >".$arrUploadCategory['uploadcategory_name']."</a><br>
	<a href=\"javascript:parent.insertInput('{$nUploadId}','blog_thumb')\">".G::L("缩略图")."</a>
	<a href=\"javascript:parent.newPage('[upload]{$nUploadId}[/upload]');\">".G::L("插入")."</a><br>
	<a href=\"".G::U("upload/cover?id={$nUploadId}&cid=".$arrUploadCategory['uploadcategory_id'])."\">".G::L("设为封面")."</a>
	<a href=\"".G::U('upload/update_file?id='.$nUploadId)."\">".G::L("更新")."</a><br>
	".G::L("下载")."(".$oUpload->upload_download.")
	<a href=\"javascript:parent.dyhbConfirm(D.L('".G::L("你确认删除附件吗？")."'),function(){ parent.delUpload('{$nUploadId}','upload','".__APP__."/upload/lists');art.dialog.open.api.close();});\">".G::L("删除")."</a>
</li>";
			}
		}
		$sResultInfo.="</ul></div>";
		$sResultInfo.="<div class='pagination'>".$oPage->P()."</div>";
		$this->assign('sResultInfo',$sResultInfo);
		$this->assign('sFileType',$sType);
		$this->display('upload+upload_page');
	}

	public function cover(){
		$nUploadId=G::getGpc('id');
		$nUploadCategoryId=G::getGpc('cid');
		if($nUploadCategoryId<0){
			$this->E(G::L('未分类不用设置封面'));
		}
		$oUploadCategory=UploadcategoryModel::F('uploadcategory_id=?',$nUploadCategoryId)->query();
		$oUploadCategory->uploadcategory_cover=$nUploadId;
		$oUploadCategory->save(0,'update');
		if($oUploadCategory->isError()){
			$this->E($oUploadCategory->getErrorMessage());
		}
		else{
			Cache_Extend::front_widget_uploadcategory();
			$this->assign('__JumpUrl__',G::U('upload/lists?cid='.$nUploadCategoryId));
			$this->S(G::L('设置封面成功！'));
		}
	}

	public function update_file(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$nUploadId=G::getGpc('id');
		$oUpload=UploadModel::F('upload_id=?',$nUploadId)->query();
		$arrUploadCategorys=$this->getUploadCategory(true);
		if(!isset($_POST['update'])){
			$sUploadSelect='';
			foreach($arrUploadCategorys as $arrUploadCategory){
				$sUploadSelect.="<option value=\"".$arrUploadCategory['uploadcategory_id']."\" ".($oUpload->uploadcategory_id==$arrUploadCategory['uploadcategory_id']?"selected":'').">".$arrUploadCategory['uploadcategory_name']."</option>";
			}
			$sResultInfo="
<br/><form action=\"".G::U('upload/update_file?id='.$nUploadId)."\" method=\"post\" enctype='multipart/form-data'>
 <label>".G::L("附件名字：")."</label>
<input name=\"upload_name\"  type=\"text\" value=\"".$oUpload->upload_name."\" /><br/><br/>
<label>".G::L("附件归档：")."</label>
<SELECT class=\"field\"  name=\"uploadcategory_id\">".
"<option value=\"-1\" ".($oUpload->uploadcategory_id==-1?"selected":'').">".G::L('未归档')."</option>".$sUploadSelect.
"</select><br/><br/><label>".G::L("附件描述(最多300个字符)：")."</label><br/>
<textarea name=\"upload_description\" />".$oUpload->upload_description."</textarea><br/><br/>
<label>".G::L("上传")."：</label>
<input type=\"file\" class=\"field\" name='newupdatefile'>
<div>
<input type=\"submit\"  value=\"".G::L("更新附件信息")."\" name=\"update\"/>
</div>
</form>";
			$this->assign('sResultInfo',$sResultInfo);
			$this->display('upload+upload_page');
		}
		else{
			$arrPhotoInfo=array();
			if($_FILES['newupdatefile']['error']!=4){
				if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename)){
					@unlink(DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename);
				}
				if(in_array($oUpload->upload_extension,array('gif','jpg','jpeg','bmp','png')) && $oUpload->upload_isthumb){
					if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename)){
						@unlink(DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename);
					}
				}
				$sUploadStoreWhereDir=$this->getUploadStoreWhereDir();
				$arrAllAllowType=unserialize($this->_arrOptions['all_allowed_upload_type']);
				$nUploadfileMaxsize=$this->_arrOptions['uploadfile_maxsize'];
				$oUploadfile=new UploadFileForUploadify($nUploadfileMaxsize,$arrAllAllowType,'','./Public/Upload'.$sUploadStoreWhereDir);
				if($this->_arrOptions['is_makeimage_thumb']==1){$oUploadfile->_bThumb=true;}// 开启缩略图
				$arrThumbMax=unserialize($this->_arrOptions['blogfile_thumb_width_heigth']);
				$oUploadfile->_nThumbMaxHeight=$arrThumbMax[0];// 缩略图大小，像素
				$oUploadfile->_nThumbMaxWidth=$arrThumbMax[1];
				$oUploadfile->_sThumbPath="./Public/Upload{$sUploadStoreWhereDir}/Thumb";// 缩略图文件保存路径
				$oUploadfile->_sSaveRule='uniqid';{// 设置上传文件规则
				if($this->_arrOptions['is_images_water_mark']==1)
					$oUploadfile->_bIsImagesWaterMark=true;
				}
				$oUploadfile->_sImagesWaterMarkType=$this->_arrOptions['images_water_type'];
				$oUploadfile->_arrImagesWaterMarkImg=array('path'=>$this->_arrOptions['images_water_mark_img_imgurl'],'offset'=>$this->_arrOptions['images_water_position_offset']);
				$oImage2Local->_arrImagesWaterMarkText=array('content'=>$this->_arrOptions['images_water_mark_text_content'],'textColor'=>$this->_arrOptions['images_water_mark_text_color'],'textFont'=>$this->_arrOptions['images_water_mark_text_fontsize'],'textFile'=>$this->_arrOptions['images_water_mark_text_fonttype'],'textPath'=>$this->_arrOptions['images_water_mark_text_fontpath'],'offset'=>$this->_arrOptions['images_water_position_offset']);
				$oUploadfile->_nWaterPos=$this->_arrOptions['images_water_position'];
				$oUploadfile->setAutoCreateStoreDir(TRUE);
				$oUploadfile->setUploadifyDataName('newupdatefile');
				if(!$oUploadfile->upload()){
					$this->E($oUploadfile->getErrorMessage());
				}
				else{
					$arrPhotoInfo=$oUploadfile->getUploadFileInfo();
				}
			}
			$arrPhotoInfo=reset($arrPhotoInfo);
			$oUpload->upload_description=G::getGpc('upload_description');
			$oUpload->upload_name=G::getGpc('upload_name');
			$oUpload->uploadcategory_id=intval(G::getGpc('uploadcategory_id'));
			if(!empty($arrPhotoInfo)){
				$oUpload->upload_extension=$arrPhotoInfo['extension'];
				$oUpload->upload_savepath=str_replace('./Public/Upload/','',$arrPhotoInfo['savepath']);
				$oUpload->upload_savename=$arrPhotoInfo['savename'];
				$oUpload->upload_isthumb=$arrPhotoInfo['isthumb'];
				$oUpload->upload_thumbprefix=$arrPhotoInfo['thumbprefix'];
				$oUpload->upload_thumbpath=str_replace('./Public/Upload/','',$arrPhotoInfo['thumbpath']);
			}
			$oUpload->save(0,'update');
			if($oUpload->isError()){
				$this->E($oUpload->getErrorMessage());
			}
			else{
				Cache_Extend::front_widget_recentimage();
				$this->S(G::L('附件更新成功了！'));
			}
		}
	}

	protected function getUploadStoreWhereDir(){
		$sUploadStoreWhereType=$this->_arrOptions['upload_store_where_type'];
		if($sUploadStoreWhereType=='month'){$sUploadStoreWhereDir='/month_'.date('Ym',CURRENT_TIMESTAMP);}
		elseif($sUploadStoreWhereType=='day'){$sUploadStoreWhereDir='/day_'.date('Ymd',CURRENT_TIMESTAMP);}
		else {$sUploadStoreWhereDir='';}
		return $sUploadStoreWhereDir;
	}

	public function category(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$arrUploadCategorys=$this->getUploadCategory(true);
		$sResultInfo="
<ul>
	<li id=\"filelist\">
	<a href=\"".G::U('upload/lists?cid=-1')."\"  title=\"".G::L("未分类")."\" ><img src=\"".__TMPLPUB__."/Images/photosort.gif\" width=\"170\" height=\"170\" border=\"0\"/></a><br>
	".G::L("未分类")."
	</li>";
		foreach($arrUploadCategorys as $arrUploadCategory){
			if(empty($arrUploadCategory['uploadcategory_cover'])){
				$sCoverPhoto=__TMPLPUB__.'/Images/photosort.gif';
			}
			else{
				if(!preg_match("/[^\d-.,]/",$arrUploadCategory['uploadcategory_cover'])){
					$oUpload=UploadModel::F('upload_id=?',$arrUploadCategory['uploadcategory_cover'])->query();
					if(!$oUpload->isError() && in_array($oUpload['upload_extension'],array('gif','jpg','jpeg','bmp','png'))){
						$sCoverPhoto=$oUpload->upload_isthumb?
									__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:
									__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
					}
					else{
						$sCoverPhoto=__TMPLPUB__.'/Images/photosort.gif';
					}
				}
				else{
					$sCoverPhoto=$arrUploadCategory['uploadcategory_cover'];
				}
			}
			$sResultInfo.="
<li id=\"filelist\">
	<a href=\"".G::U('upload/lists?cid='.$arrUploadCategory['uploadcategory_id'])."\"  title=\"".$arrUploadCategory['uploadcategory_name']."\"><img src=\"{$sCoverPhoto}\" width=\"170\" height=\"170\" border=\"0\"/></a>
	<br><a href=\"".G::U('upload/update_category?cid='.$arrUploadCategory['uploadcategory_id'])."\" title=\"".G::L("编辑")."\" >".$arrUploadCategory['uploadcategory_name']."</a>
	<a href=\"javascript:parent.dyhbConfirm(D.L('".G::L("你确认删除附件分类吗？")."'),function(){ parent.delUpload('".$arrUploadCategory['uploadcategory_id']."','uploadcategory','".__APP__."/upload/category');art.dialog.open.api.close();});\">".G::L("删除")."</a>
</li>";
		}
		$sResultInfo.="
</ul>
<div class=\"continuefile\">
	<form name=\"upload_category\" method=\"post\" action=\"".G::U('upload/add_category')."\" id=\"upload_category\">
		<input name=\"uploadcategory_name\" type=\"text\" size=\"6\"><br>
		<input type=\"submit\" value=\"".G::L("添加")."\">
	</form>
</div>";
		$this->assign('sResultInfo',$sResultInfo);
		$this->display('upload+upload_page');
	}

	public function add_category(){
		$nUploadCategoryId=intval(G::getGpc('uploadcategory_id'));
		$sUploadCategoryName=G::getGpc('uploadcategory_name');
		if(empty($sUploadCategoryName))
		$this->E(G::L('相册分类名不能为空！'));
		$sUploadCategoryCover=G::getGpc('uploadcategory_cover');
		$nUpoadCategoryCompositor=intval(G::getGpc('uploadcategory_compositor'));
		if($nUploadCategoryId>0){
			$oUploadCategory=UploadcategoryModel::F('uploadcategory_id=?',$nUploadCategoryId)->query();
		}
		else{
			$oUploadCategory=new UploadcategoryModel();
		}
		$oUploadCategory->uploadcategory_name=$sUploadCategoryName;
		if($nUploadCategoryId>0){
			$oUploadCategory->uploadcategory_cover=$sUploadCategoryCover;
			$oUploadCategory->uploadcategory_compositor=$nUpoadCategoryCompositor;
			$oUploadCategory->save(0,'update');
			if(!$oUploadCategory->isError()){
				Cache_Extend::front_widget_uploadcategory();
				$this->S(G::L('相册分类更新成功了！'));
			}
			else{
				if($oUploadCategory->getErrorMessage()=='zero-effect'){
					$this->S(G::L('你没有更新相册!'));
				}
				else{
					$this->S($oUploadCategory->getErrorMessage());
				}
			}
		}
		else{
			$oUploadCategory->save();
			if(!$oUploadCategory->isError()){
				Cache_Extend::front_widget_uploadcategory();
				$this->S(G::L('相册分类添加成功了！'));
			}
			else{
				$this->S($oUploadCategory->getErrorMessage());
			}
		}
	}

	public function update_category(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$nUploadCategoryId=G::getGpc('cid');
		$oUploadCategory=UploadcategoryModel::F('uploadcategory_id=?',$nUploadCategoryId)->query();
		$sResultInfo="
<br>
<form id=\"form\" action=\"".G::U('upload/add_category')."\" method=\"post\" >
	<input name=\"uploadcategory_id\"  type=\"hidden\" value=\"".$oUploadCategory->uploadcategory_id."\" />
	<label>".G::L("相册分类排序:")." </label>
	<input name=\"uploadcategory_compositor\"  type=\"text\" value=\"".$oUploadCategory->uploadcategory_compositor."\" /><br /><br/>
	<label>".G::L("相册分类名：")."</label>
	<input name=\"uploadcategory_name\"  type=\"text\" value=\"".$oUploadCategory->uploadcategory_name."\" /><br /><br/>
	<label>".G::L("相册封面：（可以为一个图片附件的ID，或者一个完整的图片路径）")." </label><br/>
	<input name=\"uploadcategory_cover\"  type=\"text\" value=\"$oUploadCategory->uploadcategory_cover\" size=\"40\" /><br /><br />
	<div>
	<input id=\"button\" type=\"submit\" value=\"".G::L("更新相册分类")."\" />
	</div>
</form>";
		$this->assign('sResultInfo',$sResultInfo);
		$this->display('upload+upload_page');
	}

	public function operate(){
		$this->assign('uploadFileDefault',$this->_arrOptions['upload_file_default']);
		$sResultInfo="
<p><a href=\"".G::U('upload/clear_file')."\">".G::L("附件清理")."</a><br/>
".G::L("删除那些服务器上实际存在的附件，而在数据库中没有记录的附件")."</p><br/>
<p><b>".G::L("附件修复")."</b><br/>
".G::L("本次操作将会删除在数据库中有附件信息，而服务器上实际上没有附件的记录.")."
".G::L("如果附件较多，过程会比较久，请耐心等候。")."
".G::L("建议定期执行。")."<br/>
<form action=\"".G::U('upload/repaire_file')."\" method=\"post\" >
".G::L("开始ID：")." <input type=\"text\" name=\"start\" size=\"5\" /><br/>
".G::L("结束ID： ")."<input type=\"text\" name=\"end\" size=\"5\" /><br/>
 ".G::L("循环处理数量：")."<input type=\"text\" name=\"pagesize\" value=\"200\" size=\"5\" /><br/>
<input type=\"submit\" value=\"".G::L("确定")."\" />
</form><p>";
		$this->assign('sResultInfo',$sResultInfo);
		$this->display('upload+upload_page');
	}

	public function clear_file(){
		$this->clear_one_file('./Public/Upload');
		$hUploadDir=opendir('./Public/Upload');
		while(($sFile=readdir($hUploadDir))!==false){
			if($sFile !="." && $sFile !=".." && $sFile !=""){
				if(is_dir('./Public/Upload/'.$sFile)){
					$this->clear_one_file('./Public/Upload/'.$sFile.'/');
				}
			}
		}
		$this->S(G::L('清理附件成功了！'));
	}

	protected function clear_one_file($sFileDir){
		$hHandler=opendir($sFileDir);
		while(($sFilename=readdir($hHandler))!==false){
			if($sFilename !='.'&& $sFilename !='..'){
				if(substr($sFilename,0,6)=='thumb_'|| $sFilename=='index.html' ||$sFilename=='index.php'){
					continue;
				}
				$oUpload=UploadModel::F()->getByupload_savename($sFilename);
				if(empty($oUpload->upload_id)){
					@unlink($sFileDir.$sFilename);
					if(file_exists($sFileDir.'Thumb/'.'thumb_'.$sFilename)){
						@unlink($sFileDir.'Thumb/'.'thumb_'.$sFilename);
					}
				}
			}
		}
	}

	public function repaire_file(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		$nBuild=intval(G::getGpc('build'));

		!$nPagesize && $nPagesize=200;
		!$nCount && $nCount=0;
		!$nBuild && $nBuild=0;

		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['upload_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['upload_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['upload_id']=array('elt',$nEnd);
		}
		if(!$nTotal){
			$nTotal=UploadModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrUploads=UploadModel::F()->where($arrMap)->all()->order('`upload_id` ASC')->limit($nCount,$nPagesize)->query();
		foreach($arrUploads as $oUploads){
			if(!file_exists(DYHB_PATH.'/../Public/Upload/'.$oUploads->upload_savepath.'/'.$oUploads->upload_savename)){
				$oUploadMeta=UploadModel::M();
				$oUploadMeta->deleteWhere('upload_id=?',$oUploads->upload_id);
				if($oUploadMeta->isError()){
					$this->E($oUploadMeta->getErrorMessage());
				}
				$nActionNum++;
			}
			$nCount++;
		}

		Cache_Extend::front_widget_recentimage();
		Cache_Extend::front_widget_static();
		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("upload/repaire_file?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&build={$nBuild}");
		$sResultInfo='';
		if($nTotal > $nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',$nBuild==1 ?G::U('upload/build'):G::U('upload/operate'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo);
		}
	}

	public function inserts(){
		$arrIds=explode(',',G::getGpc('id','G'));
		$sResultInfo=G::L('批量插入操作消息如下：').'<br/>';
		foreach($arrIds as $nId){
			$sResultInfo.="<script type=\"text/javascript\">";
			$oUpload=UploadModel::F('upload_id=?',$nId)->query();
			$sResultInfo.="parent.newPage('[upload]".$oUpload->upload_id."[/upload]<br />');";
			$sResultInfo.="</script>";
			$sResultInfo.=G::L("插入(%s)格式文件 [%s]成功",'app',null,$oUpload->upload_extension,$oUpload->upload_name)."<br/>";
		}
		$this->S($sResultInfo);
	}

	public function move_category(){
		$arrIds=explode(',',G::getGpc('id','G'));
		$nUploadCategoryId=G::getGpc('cid','G');
		foreach($arrIds as $nId){
			$oUpload=UploadModel::F('upload_id=?',$nId)->query();
			$oUpload->uploadcategory_id=$nUploadCategoryId;
			$oUpload->save(0,'update');
		}
		Cache_Extend::front_widget_recentimage();
		$this->S(G::L('批量移动附件归档成功！'));
	}

	public function _build_get_admin_help_description(){
		return '<p>'.G::L('附件整理的目地是更好地管理附件，清理垃圾。','upload').'</p>'.
				'<p>'.G::L('附件整理分为两项，一个是附件清理，一个是附件修复。','upload').'</p>'.
				'<p><strong>'.G::L('附件清理:').'</strong>'.G::L('删除那些服务器上实际存在的附件，而在数据库中没有记录的附件。','upload').'</p>'.
				'<p><strong>'.G::L('附件修复:').'</strong>'.G::L("本次操作将会删除在数据库中有附件信息，而服务器上实际上没有附件的记录.",'upload').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function build(){
		$this->display();
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_recentimage();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_recentimage();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_recentimage();
	}

	public function _comment_build_get_admin_help_description(){
		return '<p>'.G::L('附件数据在处理过程中难免会出现错误，这个时候我们需要修复错误。','upload').'</p>'.
				'<p>'.G::L('有些错误为你直接操作数据中的记录，而不是通过我们的程序来做的。','upload').'</p>'.
				'<p>'.G::L('还有些错误是因为网络的原因，造成数据更新错误。','upload').'</p>'.
				'<p>'.G::L('我们在操作过程中评论有可能出现错误统计，这个时候可以修复它。','upload').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function comment_build(){
		$this->display();
	}

	public function repaire_comment(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));

		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['upload_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['upload_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['upload_id']=array('elt',$nEnd);
		}
		if(!$nTotal){
			$nTotal=UploadModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrUploads=UploadModel::F()->where($arrMap)->all()->order('`upload_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrRecords=array();
		foreach($arrUploads as $oUploads){
			$nNum=0;
			$nNum=$this->repaire_blog_comment($oUploads);
			$arrRecords[]=$oUploads->upload_id.'-'.$nNum;
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
		$sUrl=G::U("upload/repaire_comment?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理附件成功记录','upload')."</h3><br/>";
			$sRecords.=implode('<br/>',$arrRecords);
			$sRecords.="</div>";
		}
		if($nTotal > $nCount){
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

	protected function repaire_blog_comment($oUploads){
		$nNum=CommentModel::F('`comment_relationtype`=\'upload\' AND `comment_relationvalue`=?',$oUploads->upload_id)->all()->getCounts();
		if($nNum<0){$nNum=0;}
		$oUploads->upload_commentnum=$nNum;
		$oUploads->save(0,'update');
		return $nNum;
	}

	public  function lock(){
		$nUploadId=intval(G::getGpc('id','G'));
		if($nUploadId){
			$oUpload=UploadModel::F('upload_id=?',$nUploadId)->query();
			$oUpload->upload_islock=1;
			$oUpload->save(0,'update');
			if($oUpload->isError()){
				$this->E($oUpload->getErrorMessage());
			}
			else{
				$this->S(G::L('锁定附件成功了'));
			}
		}
		else{
			$this->E(G::L('你没有指定待锁定的附件'));
		}
	}

	public  function un_lock(){
		$nUploadId=intval(G::getGpc('id','G'));
		if($nUploadId){
			$oUpload=UploadModel::F('upload_id=?',$nUploadId)->query();
			$oUpload->upload_islock=0;
			$oUpload->save(0,'update');
			if($oUpload->isError()){
				$this->E($oUpload->getErrorMessage());
			}
			else{
				$this->S(G::L('解锁附件成功了'));
			}
		}
		else{
			$this->E(G::L('你没有指定待解锁的附件'));
		}
	}

}
