<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   相册控制器($) */

!defined('DYHB_PATH') && exit;

class UploadController extends CommonController{

	static public $_oUpload=null;

	public function init__(){
		parent::init__();
		if(Global_Extend::getOption('only_login_can_view_upload')==1){
			$this->E(G::L('这个文件只能在登入之后下载。'));
		}
	}
	
	public function index(){
		define('IS_UPLOADLIST',TRUE);
		define('CURSCRIPT','uploadlist');
		$nUploadcategoryId=G::getGpc('cid','G');
		$arrMap=array();
		if(!empty($nUploadcategoryId)){
			$arrMap['uploadcategory_id']=$nUploadcategoryId;
		}
		$nUpload=UploadModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_upload_list_num');
		$oPage=Page::RUN($nUpload,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrUploadList=UploadModel::F()->where($arrMap)->all()->order('`upload_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nUpload',$nUpload);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrUploadLists',$arrUploadList);
		$this->assign('the_upload_description',Global_Extend::getOption('the_upload_description'));
		$this->display('upload');
	}

	static public function get_the_upload_url($arrUpload,$bImage=false){
		$sType=self::get_upload_type($arrUpload);
		if($sType=='img'){
			if(Global_Extend::getOption('is_hide_upload_really_path')==1){
				return $arrUpload['upload_isthumb'] && $bImage===true?
				G::U('attachment/index?id='.Global_Extend::aidencode($arrUpload['upload_id']).'&thumb=1'):
				G::U('attachment/index?id='.Global_Extend::aidencode($arrUpload['upload_id']));
			}else{
				return $arrUpload['upload_isthumb'] && $bImage===true?
						__PUBLIC__.'/Upload/'.$arrUpload['upload_thumbpath'].'/'.$arrUpload['upload_thumbprefix'].$arrUpload['upload_savename']:
						__PUBLIC__.'/Upload/'.$arrUpload['upload_savepath'].'/'.$arrUpload['upload_savename'];
			}
		}
		else{
			if($sType=='mp3'){
				$sType='wmp';
			}
			return __PUBLIC__.'/Images/Blog/Media/'.$sType.'.jpg';
		}
	}

	static public function get_all_upload_url($arrUpload,$bImage=false){
		if(Global_Extend::getOption('is_hide_upload_really_path')==1){
			return $arrUpload['upload_isthumb'] && $bImage===true?
				G::U('attachment/index?id='.Global_Extend::aidencode($arrUpload['upload_id']).'&thumb=1'):
				G::U('attachment/index?id='.Global_Extend::aidencode($arrUpload['upload_id']));
		}else{
			return $arrUpload['upload_isthumb'] && $bImage===true?
				__PUBLIC__.'/Upload/'.$arrUpload['upload_thumbpath'].'/'.$arrUpload['upload_thumbprefix'].$arrUpload['upload_savename']:
				__PUBLIC__.'/Upload/'.$arrUpload['upload_savepath'].'/'.$arrUpload['upload_savename'];
		}
	}

	public function show(){
		$nUploadId=G::getGpc('id');
		if($nUploadId===null || $nUploadId=='index'){
			$this->index();
			exit();
		}
		if($nUploadId=='json'){
			$this->json();
			exit();
		}
		$oUpload=UploadModel::F('upload_id=?',$nUploadId)->query();
		if(empty($oUpload->upload_id)){
			$this->page404();
		}
		self::$_oUpload=$oUpload;
		define('IS_SINGLEUPLOAD',TRUE);
		define('CURSCRIPT','singleupload');
		$nPage=G::getGpc('page','G');
		$nEveryCommentnum=Global_Extend::getOption('display_upload_comment_list_num');
		if(TEMPLATE_TYPE==='blog'||TEMPLATE_TYPE==='cms'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_parentid']=0;
			$arrCommentMap['comment_relationtype']='upload';
			$arrCommentMap['comment_relationvalue']=$oUpload->upload_id;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,$nPage);
			$sPageNavbar=$oPage->P('pagination');
			$arrCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$arrAllCommentMap['comment_isshow']=1;
			$arrAllCommentMap['comment_parentid']=array('neq',0);
			$arrAllCommentMap['comment_relationtype']='upload';
			$arrAllCommentMap['comment_relationvalue']=$oUpload->upload_id;
			$arrAllComments=CommentModel::F()->reset(DbSelect::WHERE)->where($arrAllCommentMap)->all()->order('`comment_id` DESC')->query();
			$this->assign('arrAllComments',$arrAllComments);
			$this->assign('arrCommentLists',$arrCommentLists);
		}
		elseif(TEMPLATE_TYPE==='bbs'){
			$arrCommentMap['comment_isshow']=1;
			$arrCommentMap['comment_relationtype']='upload';
			$arrCommentMap['comment_relationvalue']=$oUpload->upload_id;
			$nTotalComment=CommentModel::F()->where($arrCommentMap)->all()->getCounts();
			$oPage=Page::RUN($nTotalComment,$nEveryCommentnum,$nPage);
			$sPageNavbar=$oPage->P('pagination');
			$arrBoardCommentLists=CommentModel::F()->where($arrCommentMap)->all()->order('`comment_id` DESC')->limit($oPage->returnPageStart(),$nEveryCommentnum)->query();
			$this->assign('arrBoardCommentLists',$arrBoardCommentLists);
		}
		else{
			$this->E(G::L('你当前的模板方案不正确'));
		}
		$this->assign('nPage',$nPage);
		$this->assign('nEveryCommentnum',$nEveryCommentnum);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('sCommentRelationtype','upload');
		$this->assign('nCommentRelationvalue',$oUpload->upload_id);
		$this->assign('oUpload',$oUpload);
		$this->assign('theData',$oUpload);
		$this->assign('sUploadType',self::get_upload_type($oUpload));
		$this->display('uploadshow');
	}

	public function json(){
		$arrMap['upload_extension']=array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or');
		$arrListUploads=UploadModel::F()->where($arrMap)->all()->asArray()->order("`create_dateline` DESC")->query();
		$nTotals=count($arrListUploads);
		echo '[';
		foreach($arrListUploads as $nKey=>$arrListUpload){
			echo"{\"photoid\":\"".$arrListUpload['upload_id']."\",\"imgurl\":\"".self::get_the_upload_url($arrListUpload)."\",\"imgurl_thumb\":\"".self::get_the_upload_url($arrListUpload,true)."\",\"imgurl_view\":\"".self::get_the_upload_url($arrListUpload)."\",\"author\":\"admin\",\"intro\":\"".($arrListUpload['upload_description']?$arrListUpload['upload_description']:G::L('暂无相关图片描述'))."\",\"title\":\"".$arrListUpload['upload_name']."\"}".($nKey<$nTotals-1?',':'');
		}
		echo ']';
	}

	static public function get_upload_type($arrUpload){
		$sUploadExtension=$arrUpload['upload_extension'];
		if(in_array($sUploadExtension,array('jpg','jpeg','gif','png','bmp'))){
			return 'img';
		}
		elseif($sUploadExtension=="swf"){
			return 'swf';
		}
		elseif(in_array($sUploadExtension,array('wma','asf','wmv'))){
			return 'wmp';
		}
		elseif($sUploadExtension=='mp3'){
			return 'mp3';
		}
		elseif(in_array($sUploadExtension,array('rm','rmvb','ra','ram'))){
			return 'real';
		}
		elseif(in_array($sUploadExtension,array('flv','mp4','aac'))){
			return 'flv';
		}
		elseif(in_array($sUploadExtension,array('html','htm','txt'))){
			return 'url';
		}
		else{
			return 'download';
		}
	}

	public function get_the_upload_username($oUpload){
		if($oUpload['user_id']==-1){
			return G::L('跌名');
		}
		else{
			return $oUpload->user->user_nikename?$oUpload->user->user_nikename:$oUpload->user->user_name;
		}
	}

	public function show_the_upload($oUpload,$sUploadType){
		if(in_array($sUploadType,array('img','swf','wmp','mp3','real','flv','url'))){
			call_user_func(array('UploadController','show'.$sUploadType),array('data'=>$oUpload));
		}
		else{
			$this->showdownload(array('data'=>$oUpload));
		}
	}

	public function showwmp($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote mediabox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/wmp.gif');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=urlencode(self::get_all_upload_url($arrData['data']));
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("Windows Media Player文件")."
</div>
<div class=\"quote-content\">
	<a href=\"javascript: playmedia('player_{$arrData['id']}','wmp','{$arrData['url']}','400','300');\">".G::L('点击打开/折叠播放器')."</a>
	<div id=\"player_{$arrData['id']}\" style=\"display: none;\"></div>
</div>{$arrData['after_widget']}";
	}

	public function showurl($arrData=array()){
		$arrData['url']=self::get_all_upload_url($arrData['data']);
		echo "<a href=\"{$arrData['url']}\" title=\"".$arrData['data']['upload_name']."\" target=\"_blank\" >".$arrData['data']['upload_name']."</a>";
	}

	public static function showswf($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote mediabox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/swf.gif',);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=urlencode(self::get_all_upload_url($arrData['data']));
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("Flash Player文件")."
</div>
<div class=\"quote-content\">
	<a href=\"javascript: playmedia('player_{$arrData['id']}','swf','{$arrData['url']}','400','300');\">".G::L('点击打开/折叠播放器')."</a>
	<div id=\"player_{$arrData['id']}\" style=\"display: none;\"></div>
</div>{$arrData['after_widget']}";
	}

	public function showflv($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote mediabox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/swf.gif');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=urlencode(self::get_all_upload_url($arrData['data']));
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("Flash Video Player文件")."
</div>
<div class=\"quote-content\">
	<a href=\"javascript: playmedia('player_{$arrData['id']}','flv','{$arrData['url']}','400','300');\">".G::L('点击打开/折叠播放器')."</a>
	<div id=\"player_{$arrData['id']}\" style=\"display: none;\"></div>
</div>{$arrData['after_widget']}";
	}

	public function showmp3($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote mediabox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/swf.gif',);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['width'])){$arrData['width']=intval(Global_Extend::getOption('widget_single_mp3player_width'));}
		if(!isset($arrData['height'])){$arrData['height']=intval(Global_Extend::getOption('widget_single_mp3player_height'));}
		if(!isset($arrData['bgcolor'])){$arrData['bgcolor']=Global_Extend::getOption('widget_single_mp3player_bgcolor');}
		$arrData['id']=rand(1000,99999);
		$sUrl=urlencode(self::get_all_upload_url($arrData['data']));
		$arrData['url']=Blog_Extend::singleMp3play($sUrl,true);
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("Flash Player文件")."
</div>
<div class=\"quote-content\">
	<a href=\"javascript: playmedia('player_{$arrData['id']}','swf','{$arrData['url']}','{$arrData['width']}','{$arrData['height']}','{$arrData['bgcolor']}');\">".G::L('点击打开/折叠播放器')."</a>
	<div id=\"player_{$arrData['id']}\" style=\"display: none;\"></div>
</div>{$arrData['after_widget']}";
	}

	public function showdownload($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote downloadbox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/download.gif');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=self::get_all_upload_url($arrData['data']);
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("下载文件")."
</div>
<div class=\"quote-content\">
	<a href=\"{$arrData['url']}\">".G::L("点击这里下载文件")."</a>
</div>{$arrData['after_widget']}";
	}

	public function showreal($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div class="quote mediabox">','after_widget'=>'</div>','image'=>__PUBLIC__.'/Images/Media/real.gif');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=urlencode(self::get_all_upload_url($arrData['data']));
		echo "{$arrData['before_widget']}
<div class=\"quote-title\">
	<img src=\"{$arrData['image']}\" alt=\"\">".G::L("Real Player文件")."
</div>
<div class=\"quote-content\">
	<a href=\"javascript: playmedia('player_{$arrData['id']}','real','{$arrData['url']}','{$arrData['height']}','{$arrData['width']}');\">".G::L('点击打开/折叠播放器')."</a>
	<div id=\"player_{$arrData['id']}\" style=\"display: none;\"></div>
</div>{$arrData['after_widget']}";
	}

	public function showimg($arrData=array()){
		$arrDefaultOption=array('before_widget'=>'<div id="albumlist-container-box">','after_widget'=>'</div>');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);}
		else{
			$arrData=$arrDefaultOption;
		}
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".__PUBLIC__."/Images/AjaxAlbum/imgcss/style.css?v=1.2\" />
<script type=\"text/javascript\" src=\"".__PUBLIC__."/Images/AjaxAlbum/js/jquery.ajaxAlbum-1.2.js?v=1.2\"></script>
<script language=\"javascript\">
//<![CDATA[
\$(function(){
\$(\".albumlist_container\").ajaxAlbum({
	ajaxUrl:\"".G::U('upload/json')."\",
		onloadId:{$arrData['data']['upload_id']},
		imgDir:\"\"
	});
});
//]]>
</script>
{$arrData['before_widget']}
<div class=\"albumlist_container\">
	<div class=\"albumlist_tt\">
			<p class=\"prev_next prev\" title=\"".G::L("切换至前一排")."\"><a href=\"javascript:;\" title=\"".G::L("切换至前一排")."\">&nbsp;</a></p>
			<div class=\"items_list_container\">
				<ul class=\"items_list ajaxAlbum_thumbList\">
					<a><img src=\"\" /></a>
				</ul>
			</div>
			<p class=\"prev_next next\" title=\"".G::L("切换至后一排")."\"><a href=\"javascript:;\" title=\"".G::L("切换至后一排")."\">&nbsp;</a></p>
	</div>
	<div class=\"albumlist_ct ajaxAlbum_viewInfo\"></div>
</div>
{$arrData['after_widget']}";
	}

	public function uploadpage(){
		$arrAllAllowType=unserialize(Global_Extend::getOption('all_allowed_upload_type'));
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
		$this->assign('arrUploadCategory',UploadcategoryModel::F()->all()->asArray()->query());
		$this->assign('nFileInputNum',Global_Extend::getOption('file_input_num'));
		$this->assign('nUploadfileMaxsize',Global_Extend::getOption('uploadfile_maxsize'));
		$this->assign('nIsUploadAuto',Global_Extend::getOption('is_upload_auto'));
		$this->assign('nSimUploadLimit',Global_Extend::getOption('sim_upload_limit'));
		$this->display('uploadpage');
	}

	public function lists(){
		if($GLOBALS['___login___']===false){
			$sResultInfo=G::L('你没有登录，你不能使用文件管理器！');
			$this->assign('sResultInfo',$sResultInfo);
			$this->display('uploadlists');
			exit();
		}
		$nRecordId=intval(G::getGpc('id'));
		$sModule=G::getGpc('module');
		$arrMap['upload_module']=$sModule;
		$arrMap['upload_record']=$nRecordId;
		$oListUpload=UploadModel::F()->where($arrMap)->all()->order("`create_dateline` DESC")->query();
		$sResultInfo='<ul>';
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
				$sAction="<a href=\"{$sImgTargetSrc}\" target=\"_blank\">".G::L("播放")."</a>";
				$sFileDown=$sImgTargetSrc;
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
<li class=\"filelist\" id=\"filelist-id-{$nUploadId}\">
	<a href=\"{$sImgTargetSrc}\" target=\"_blank\" title=\"Name:".$oUpload->upload_name.' | Size:'.$sFilesize.' | Upload Time:'.$sUploadDateline."\">
	<img src=\"".__PUBLIC__."/Images/Blog/file.gif\" width=\"80\" height=\"80\" border=\"0\"/></a><br>
	{$sAction}
	<a href=\"javascript:AddText('{$sInsert}');\">".G::L("插入")."</a><br>
	<a target=\"_blank\" href=\"".PageType_Extend::getUploadUrl($oUpload)."\">".G::L("浏览")."</a>
	<a href=\"javascript:void(0);\" onclick=\"showDialog('".G::L("确认要删除所选附件吗？")."','confirm','',function(){deleteUploadFile('{$nUploadId}');});return false;\">".G::L("删除")."</a>
</li>";
			}
			else{
				$sImgSrc=$oUpload->upload_isthumb?
					__PUBLIC__.'/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename:
					$sImgTargetSrc;
				$nUploadId=&$oUpload->upload_id;
				$sResultInfo.="
<li class=\"filelist\" id=\"filelist-id-{$nUploadId}\">
	<a href=\"{$sImgTargetSrc}\" class=\"preview\" target=\"_blank\" title=\"Name:".$oUpload->upload_name.' | Size:'.$sFilesize.' | Upload Time:'.$sUploadDateline."\">
	<img src=\"{$sImgSrc}\" width=\"80\" height=\"80\" border=\"0\"/></a><br>
	<a href=\"javascript:insertInput('{$nUploadId}','blog_thumb')\">".G::L("缩略图")."</a>
	<a href=\"javascript:AddText('[upload]{$nUploadId}[/upload]');\">".G::L("插入")."</a><br>
	<a target=\"_blank\" href=\"".PageType_Extend::getUploadUrl($oUpload)."\">".G::L("浏览")."</a>
	<a href=\"javascript:void(0);\" onclick=\"showDialog('".G::L("确认要删除所选附件吗？")."','confirm','',function(){deleteUploadFile('{$nUploadId}');});return false;\">".G::L("删除")."</a>
</li>";
			}
		}

		if(!isset($oListUpload[0])){
			$sResultInfo.="
<li>
	<a href=\"javascript:void(0);\" onclick=\"hideWindow('uploadlist',1,1);showWindow('upload','".G::U('upload/uploadpage')."');\" target=\"_blank\" title=\"".G::L("上传文件")."\">
	<img src=\"".__PUBLIC__."/Images/Blog/upload.gif\" border=\"0\"/></a></li>";
		}

		$sResultInfo.='</ul></div>';
		$this->assign('sResultInfo',$sResultInfo);
		$this->display('uploadlists');
	}

	public function delete_file(){
		if($GLOBALS['___login___']===false){
			$this->E(G::L( '你没有登录，无法删除附件！' ));
		}
		$nId=intval(G::getGpc('id'));
		$oUploadModel=UploadModel::F('upload_id=?',$nId)->query();
		$oUploadModel->reduceUploadNum($oUploadModel->upload_module,$oUploadModel->upload_record);
		if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_savepath.'/'.$oUploadModel->upload_savename)){
			@unlink(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_savepath.'/'.$oUploadModel->upload_savename);
		}
		if($oUploadModel->upload_isthumb){
			if(file_exists(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_thumbpath.'/'.$oUploadModel->upload_thumbprefix.$oUploadModel->upload_savename)){
				@unlink(DYHB_PATH.'/../Public/Upload/'.$oUploadModel->upload_thumbpath.'/'.$oUploadModel->upload_thumbprefix.$oUploadModel->upload_savename);
			}
		}
		$oUploadModel->destroy();
		$this->S(G::L( '删除附件成功！' ));
	}

	protected function getUploadStoreWhereDir(){
		$sUploadStoreWhereType=$this->_arrOptions['upload_store_where_type'];
		if($sUploadStoreWhereType=='month'){$sUploadStoreWhereDir='/month_'.date('Ym',CURRENT_TIMESTAMP);}
		elseif($sUploadStoreWhereType=='day'){$sUploadStoreWhereDir='/day_'.date('Ymd',CURRENT_TIMESTAMP);}
		else {$sUploadStoreWhereDir='';}
		return $sUploadStoreWhereDir;
	}

	public function upload_file(){
		if(empty($_FILES)){
			echo(G::L('你没有选择任何文件！'));
			return;
		}
		$sUploadStoreWhereDir=$this->getUploadStoreWhereDir();
		$arrAllAllowType=unserialize($this->_arrOptions['all_allowed_upload_type']);
		$nUploadfileMaxsize=$this->_arrOptions['uploadfile_maxsize'];
		$oUploadfile=new UploadFileForUploadify($nUploadfileMaxsize,$arrAllAllowType,'','./Public/Upload'.$sUploadStoreWhereDir);
		if($this->_arrOptions['is_makeimage_thumb']==1){
			$oUploadfile->_bThumb=true;
		}// 开启缩略图
		$arrThumbMax=unserialize($this->_arrOptions['blogfile_thumb_width_heigth']);
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
			exit($oUploadfile->getErrorMessage());
		}
		else{
			$arrPhotoInfo=$oUploadfile->getUploadFileInfo();
		}
		$nRecordId=G::getGpc('record_id');
		if($nRecordId===null){$nRecordId=-1;}
		$bResult=UploadModel::F()->query()->upload($arrPhotoInfo,$nRecordId);
		if($bResult===FALSE){
			echo(0);
		}
		else{
			Cache_Extend::front_widget_recentimage();
			Cache_Extend::front_widget_static();
			echo(1);
		}
		return;
	}

}
