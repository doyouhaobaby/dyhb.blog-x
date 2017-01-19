<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   附件控制器($) */

!defined('DYHB_PATH') && exit;

class AttachmentController extends CommonController{

	public function index(){
		if(empty($_GET['k'])){
			$bEncodeMethod=TRUE;
			@list($_GET['id'],$_GET['k'],$_GET['t'],$_GET['sid'])=explode('|',base64_decode($_GET['id']));
		} else {
			$bEncodeMethod=FALSE;
		}
		$fileId=intval($_GET['id']);
		$nThumb=G::getGpc('thumb','G');
		$oUpload=UploadModel::F('upload_id=?',$fileId)->query();
		if(empty($oUpload['upload_id'])){
			exit(G::L('你请求的附件不存在！'));
		}
		if(Global_Extend::getOption('attach_expire_hour')){
			$k=$_GET['k'];
			$t=$_GET['t'];
			$authk=md5($fileId.md5($GLOBALS['_commonConfig_']['DYHB_AUTH_KEY']).$t);
			$authk=$bEncodeMethod?substr($authk,0,8):$authk;
			if(empty($k) || empty($t) || $k!=$authk || CURRENT_TIMESTAMP-$t>Global_Extend::getOption('attach_expire_hour')*3600){
				if(in_array($oUpload['upload_extension'],array('gif','jpg','png','bmp','jpeg'))){
					header('location: '.Global_Extend::getOption('blog_url').'/Public/Images/Common/none.gif');
				}else{
					exit(G::L('你请求的附件已过期！'));
				}
			}
		}
		$bCheckLimitUploadLeechOk=true;
		if(Global_Extend::getOption('is_limit_upload_leech')==1){
			$arrAllowHosts=unserialize(Global_Extend::getOption('not_limit_leech_domail'));
			$arrBlogUrl=parse_url($_SERVER['HTTP_HOST']);
			$arrReferer=parse_url($_SERVER['HTTP_REFERER']);
			$arrAllowHosts[]=$arrBlogUrl['host'];
			if(!in_array($arrReferer['host'],$arrAllowHosts)){
				$bCheckLimitUploadLeechOk=false;
			}
		}
		if($bCheckLimitUploadLeechOk===false){
			$this->generate_leech_error();
		}
		$bDownload=false;
		if($sAttachmentCookie=G::cookie('attachment')){
			$arrAttachmentIds=explode(',', $sAttachmentCookie);
			if(in_array($fileId, $arrAttachmentIds)){
				$bDownload=true;
			}
		}
		if($bDownload===false){
			$oUpload->upload_download=$oUpload->upload_download +1;
			$oUpload->save(0,'update');
			$sAttachmentCookie.=empty($sAttachmentCookie)? $fileId : ','.$fileId;
			G::cookie('attachmemt', $sAttachmentCookie);
		}
		if(Global_Extend::getOption('is_upload_direct_to_really_path')==1){
			$sUploadPath=__PUBLIC__.'/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
			header("Location: {$sUploadPath}");
			exit();
		}
		if($nThumb==1 || ($oUpload->upload_isthumb && in_array($oUpload->upload_extension,array('gif','jpg','jpeg','bmp','png'))) ){
			$sFilePath=DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_thumbpath.'/'.$oUpload->upload_thumbprefix.$oUpload->upload_savename;
		}
		else{
			$sFilePath=DYHB_PATH.'/../Public/Upload/'.$oUpload->upload_savepath.'/'.$oUpload->upload_savename;
		}
		if(is_readable($sFilePath)){
			$sUploadName=!empty($oUpload->upload_name)?$oUpload->upload_name:$oUpload->upload_savename;
			$sBrowser=$this->get_browser_detection();
			if(in_array($sBrowser, array('Firefox', 'Mozilla', 'Opera'))){
				$sUploadName=urldecode($sUploadName);
			}
			$sFiletype=$oUpload->upload_type ? $oUpload->upload_type : 'application/octet-stream';
			ob_end_clean();
			header('Cache-control: max-age=31536000');
			header('Expires: ' . date('D, d M Y H:i:s',$oUpload->create_dateline). ' GMT');
			header('Last-Modified: ' . date('D, d M Y H:i:s',$oUpload->update_dateline). ' GMT');
			header('Content-Encoding: none');
			header('Content-type: '.$sFiletype);
			header('Content-Disposition:'.(Global_Extend::getOption('is_upload_inline')==0 ? 'inline' : 'attachment').'; filename='.$sUploadName);
			header('Content-Length: '.filesize($sFilePath));
			$hFp=fopen($sFilePath,'rb');
			fpassthru($hFp);
			fclose($hFp);
			exit();
		}
		else{
			exit(G::L('附件不可读'));
		}
	}

	public function generate_leech_error(){
		header('Content-Encoding: none');
		header('Content-Type: image/gif');
		header('Content-Disposition: inline; filename="no_leech.gif"');
		$oFp=fopen((file_exists(TEMPLATE_PATH.'/Public/Images/no_leech.gif')?TEMPLATE_PATH.'/Public/Images/no_leech.gif' :APP_PATH.'/Tp/Default/Public/Images/no_leech.gif'),'rb');
		fpassthru($oFp);
		fclose($oFp);
		exit();
	}

	public function get_browser_detection(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko')!==false){
			if(strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')){
				$sBrowser='Netscape';
			}
			elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Firefox')!==false){
				$sBrowser='Firefox';
			}
			else{
				$sBrowser='Mozilla';
			}
		}
		elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')!==false){
			if(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')!==false){
				$sBrowser='Opera'; //Opera 8.0
			}
			else{
				$sBrowser='IE';
			}
		}
		elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Opera')!==false){
			$sBrowser='Opera';//Opera 9.0
		}
		else{
			$sBrowser='Other';
		}
		return $sBrowser;
	}

}
