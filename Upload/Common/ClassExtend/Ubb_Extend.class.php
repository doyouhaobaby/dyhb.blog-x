<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Ubb 扩展函数($) */

!defined('DYHB_PATH') && exit;

class Ubb_Extend{

	static public function convertUbb($sContent,$nAdvanced=0,$nInRss=0){
		if($GLOBALS['___login___']===false){
			$sContent=preg_replace("/\[hide\](.+?)\[\/hide\]/is","<div class=\"quote hidebox\"><div class=\"quote-title\">".G::L('隐藏内容')."</div><div class=\"quote-content\">".G::L("这部分内容只能在登入之后看到。").G::L('请先')."	<a href=\"".self::clearUrl(self::getHostHeader().G::U('register/index'))."\">".G::L('注册')."</a> ".G::L('或者')." <a href=\"".self::clearUrl(self::getHostHeader().G::U('login/index'))."\">".G::L('登录')."</a> </div></div>",$sContent);
		}
		else{
			$sContent=str_replace(array('[hide]','[/hide]'),'',$sContent);
		}
		$sContent=str_replace(array('{','}'),array('&#123;','&#125;'),$sContent);
		$arrBasicUbbSearch=array('[hr]','<br>');
		$arrBasicUbbReplace=array('<hr/>','<br/>');
		$sContent=str_replace($arrBasicUbbSearch,$arrBasicUbbReplace,$sContent);
		if($nAdvanced){
			$sContent=preg_replace("/\[url=([^\[]*)\]\[img(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/img\]\[\/url\]/ise","Ubb_Extend::makeImgWithUrl('\\1','\\2','\\3','\\4','\\5',{$nInRss})",$sContent);
			$sContent=preg_replace("/\[img(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/img\]/ise","Ubb_Extend::makeImg('\\1','\\2','\\3','\\4',{$nInRss})",$sContent);
			$sContent=preg_replace("/\[url=([^\[]*)\]\[upload(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/upload\]\[\/url\]/ise","Ubb_Extend::makeImgWithUrl('\\1','\\2','\\3','\\4','\\5',{$nInRss},1)",$sContent);
			$sContent=preg_replace("/\[upload(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/upload\]/ise","Ubb_Extend::makeImg('\\1','\\2','\\3','\\4',{$nInRss},1)",$sContent);
		}
		else{
			$sContent=preg_replace("/\[img(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/img\]/ise","Ubb_Extend::makeImgInRss('\\4')",$sContent);
			$sContent=preg_replace("/\[upload(align=L| align=M| align=R)?(width=[0-9]+)?(height=[0-9]+)?\]\s*(\S+?)\s*\[\/upload\]/ise","Ubb_Extend::makeImgInRss('\\4')",$sContent);
		}
		$sContent=preg_replace("/\[file\]\s*(\S+?)\s*\[\/file\]/ise","Ubb_Extend::makeDownload('\\1',0,$nInRss,false)",$sContent);
		if(Global_Extend::getOption('content_count_download')==1){
			$sContent=preg_replace("/\[uploadfile\]\s*(\S+?)\s*\[\/uploadfile\]/ise","Ubb_Extend::makeDownload('\\1',1,$nInRss,1)",$sContent);
		}
		else{
			$sContent=preg_replace("/\[uploadfile\]\s*(\S+?)\s*\[\/uploadfile\]/ise","Ubb_Extend::makeDownload('\\1',1,$nInRss,0)",$sContent);
		}
		if(Global_Extend::getOption('content_auto_add_link')==1){
			$sContent=preg_replace("/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp|gopher|news|telnet|rtsp|mms|callto|ed2k):\/\/|www\.)([a-z0-9\/\-_+=.~!%@?#%&;:$\\()|]+)/i","[autourl]\\1\\3[/autourl]",$sContent);
		}
		$arrRegUbbSearch=array(
			"/\[size=([^\[\<]+?)\](.+?)\[\/size\]/ie",
			"/\[tbl(width=[0-9]+)?(%)?(bgcolor=[^]*)?(border=[^]*)?\](.+?)\[\/tbl\]/ise",
			"/\s*\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
			"/\s*\[quote=(.+?)\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s*/is",
			"/\s*\[code\][\n\r]*(.+?)[\n\r]*\[\/code\]\s*/ie",
			"/\[autourl\]([^\[]*)\[\/autourl\]/ie",
			"/\[url\]([^\[]*)\[\/url\]/ie",
			"/\[url=([^\[]*)\](.+?)\[\/url\]/ie",
			"/\[email\]([^\[]*)\[\/email\]/is",
			"/\[acronym=([^\[]*)\](.+?)\[\/acronym\]/is",
			"/\[color=([a-zA-Z0-9#]+?)\](.+?)\[\/color\]/i",
			"/\[font=([^\[\<:;\(\)=&#\.\+\*\/]+?)\](.+?)\[\/font\]/i",
			"/\[p align=([^\[\<]+?)\](.+?)\[\/p\]/i",
			"/\[b\](.+?)\[\/b\]/i",
			"/\[i\](.+?)\[\/i\]/i",
			"/\[u\](.+?)\[\/u\]/i",
			"/\[blockquote\](.+?)\[\/blockquote\]/i",
			"/\[strong\](.+?)\[\/strong\]/i",
			"/\[strike\](.+?)\[\/strike\]/i",
			"/\[sup\](.+?)\[\/sup\]/i",
			"/\[sub\](.+?)\[\/sub\]/i",
			"/\s*\[php\][\n\r]*(.+?)[\n\r]*\[\/php\]\s*/ie"
		);
		$arrRegUbbReplace=array(
			"Ubb_Extend::makeFontSize('\\1','\\2')",
			"Ubb_Extend::makeTable('\\5','\\1','\\2','\\3','\\4')",
			"<div class=\"quote\"><div class=\"quote-title\">".G::L('引用')."</div><div class=\"quote-content\">\\1</div></div>",
			"<div class=\"quote\"><div class=\"quote-title\">".G::L('引用自')." \\1</div><div class=\"quote-content\">\\2</div></div>",
			"Ubb_Extend::makeCode('\\1')",
			"Ubb_Extend::makeUrl('\\1',{$nAdvanced})",
			"Ubb_Extend::makeUrl('\\1',{$nAdvanced})",
			"Ubb_Extend::makeUrl('\\1',{$nAdvanced},'\\2')",
			"<a href=\"mailto:\\1\">\\1</a>",
			"<acronym title=\"\\1\">\\2</acronym>",
			"<span style=\"color: \\1;\">\\2</span>",
			"<span style=\"font-family: \\1;\">\\2</span>",
			"<p align=\"\\1\">\\2</p>",
			"<strong>\\1</strong>",
			"<em>\\1</em>",
			"<u>\\1</u>",
			"<blockquote>\\1</blockquote>",
			"<strong>\\1</strong>",
			"<del>\\1</del>",
			"<sup>\\1</sup>",
			"<sub>\\1</sub>",
			"Ubb_Extend::xhtmlHighlightString('\\1')"
		);
		$sContent=preg_replace($arrRegUbbSearch,$arrRegUbbReplace,$sContent);
		if($nAdvanced==1){
			$sContent=$nInRss==0?preg_replace("/\[(wmp|swf|real|flv)=([^\[\<]+?),([^\[\<]+?)\]\s*([^\[\<\r\n]+?)\s*\[\/(wmp|swf|real|flv)\]/ies","Ubb_Extend::makeMedia('\\1','\\4','\\2','\\3')",$sContent):preg_replace("/\[(wmp|swf|real|flv)=([^\[\<]+?),([^\[\<]+?)\]\s*([^\[\<\r\n]+?)\s*\[\/(wmp|swf|real|flv)\]/is","<br/>".G::L('此处包含一个多媒体文件，请用网页方式查看。')."<br/>",$sContent);
		}
		if($nAdvanced==1){
			$sContent=$nInRss==0?preg_replace("/\[mp3\]\s*(\S+?)\s*\[\/mp3\]/ise","Ubb_Extend::makeMedia('mp3','\\1')",$sContent):preg_replace("/\[mp3\]\s*(\S+?)\s*\[\/mp3\]/ise","<br/>".G::L('此处包含一个多媒体文件，请用网页方式查看。')."<br/>",$sContent);
		}
		return $sContent;
	}

	static public function makeUrl($sUrl,$nAdvanced=0,$sLinkText=''){
		if($nAdvanced){
			$sGoToRealLink=Global_Extend::getOption('blog_url').'/urlredirect.php?go='.(substr(strtolower($sUrl),0,4)=='www.'?urlencode("http://{$sUrl}"):urlencode($sUrl));
		}else{
			$sGoToRealLink=substr(strtolower($sUrl),0,4)=='www.'?"http://{$sUrl}":$sUrl;
		}
		$sUrlLink="<a href=\"{$sGoToRealLink}\" target=\"_blank\">";
		if(!empty($sLinkText)){
			$sUrl=$sLinkText;
		}
		else{
			if(Global_Extend::getOption('content_short_en_url') && strlen($sUrl)>Global_Extend::getOption('content_url_max_len')){
				$nHalfMax=floor(Global_Extend::getOption('content_url_max_len')/2);
				$sUrl=substr($sUrl,0,$nHalfMax).'...'.substr($sUrl,0-$nHalfMax);
			}
		}
		$sUrlLink.=$sUrl.'</a>';
		return $sUrlLink;
	}

	static public function makeFontSize($nSize,$sWord){
		$sWord=stripslashes($sWord);
		$nSizeItem=array(0,8,10,12,14,18,24,36);
		$nSize=$nSizeItem[$nSize];
		return "<span style=\"font-size:{$nSize}px;\">{$sWord}</span>";
	}

	static public function makeMedia($sMediaType,$sUrl,$nWidth='',$nHeight=''){
		$arrUrl=self::getUrlPath($sUrl);
		if(is_array($arrUrl)){
			$sUrl=$arrUrl[1];
		}
		$sMediaType=strtolower($sMediaType);
		if($sMediaType=='mp3'){
			$sMediaType='swf';
			$nWidth=intval(Global_Extend::getOption('widget_single_mp3player_width'));
			$nHeight=intval(Global_Extend::getOption('widget_mp3player_height'));
			$sBg=',\''.Global_Extend::getOption('widget_mp3player_bgcolor').'\'';
			$sUrl=Blog_Extend::singleMp3play($sUrl,true);
		}
		else{
			$sBg='';
		}
		$nId=rand(1000,99999);
		$arrTypeDescription=array('wmp'=>'Windows Media Player','swf'=>'Flash Player','real'=>'Real Player','flv'=>'Flash Video Player');
		$arrMediaPictrue=array('wmp'=>'wmp.gif','swf'=>'swf.gif','real'=>'real.gif','flv'=>'swf.gif');
		if($arrUrl===false){
			$sStr="<div class=\"quote mediabox\"><div class=\"quote-title\"><img src=\"".__PUBLIC__."/Images/Media/".$arrMediaPictrue[$sMediaType]."\" alt=\"\"/>".$arrTypeDescription[$sMediaType].G::L('文件')."</div><div class=\"quote-content\">".G::L('该文件已经损坏')."</div></div>";
		}
		else{
			$sUrl=urlencode($sUrl);
			$sStr="<div class=\"quote mediabox\"><div class=\"quote-title\"><img src=\"".__PUBLIC__."/Images/Media/".$arrMediaPictrue[$sMediaType]."\" alt=\"\"/>".$arrTypeDescription[$sMediaType].G::L('文件')."</div><div class=\"quote-content\"><a href=\"javascript: playmedia('player_{$nId}','{$sMediaType}','{$sUrl}','{$nWidth}','{$nHeight}'{$sBg});\">".G::L('点击打开/折叠播放器')."</a><div id='player_{$nId}' style='display:none;'></div></div></div>";
		}
		return $sStr;
	}

	static public function makeCode($sStr){
		$sStr=str_replace('[autourl]','',$sStr);
		$sStr=str_replace('[/autourl]','',$sStr);
		return "<div class=\"code\">{$sStr}</div>";
	}

	static public function attachwidth($nWidth){
		if(IMAGE_MAX_WIDTH<$nWidth) {
			return 'width="'.IMAGE_MAX_WIDTH.'"';
		}else{
			return '';
		}
	}

	static public function makeImg($sAlignCode,$sWidthCode,$sHeightCode,$sSrc,$nInRss=0,$bIsAttached=false){
		$arrUrl=self::getUrlPath($sSrc,true);
		if($arrUrl===false || (G::isImplementedTo($arrUrl[0],'IModel') && !is_file(DYHB_PATH.'/../Public/Upload/'.$arrUrl[0]['upload_savepath'].'/'.$arrUrl[0]['upload_savename']))){
			return "<div class=\"quote mediabox\"><div class=\"quote-title\"><img src=\"".($sPublicHeader=$nInRss==1?Global_Extend::getOption('blog_url'):'')."/Images/Public/Images/Media/viewimage.gif"."\" alt=\"\"/>".G::L('损坏图片')."</div><div class=\"quote-content\">".G::L('该文件已经损坏')."</div></div>";
		}
		if(is_array($arrUrl)){
			$sSrc=$arrUrl[2];
			$sTargetSrc=$arrUrl[1];
		}
		else{
			$sTargetSrc=$sSrc;
		}
		$sAlign=str_replace(' align=','',strtolower($sAlignCode));
		if($sAlign=='l') {$sShow=' align="left"';}
		elseif($sAlign=='r') {$sShow=' align="right"';}
		else {$sShow='';}
		$nWidth=str_replace(' width=','',strtolower($sWidthCode));
		if(!empty($nWidth)) {$sShow.=" width=\"{$nWidth}\"";}
		else{
			if(G::isImplementedTo($arrUrl[0],'IModel')){
				$arrImageInfo=getimagesize(DYHB_PATH.'/../Public/Upload/'.$arrUrl[0]->upload_savepath.'/'.$arrUrl[0]->upload_savename);
				$sShow.=self::attachwidth($arrImageInfo[0]);
			}
		}
		$nHeight=str_replace(' height=','',strtolower($sHeightCode));
		if(!empty($nHeight)) {$sShow.=" height=\"{$nHeight}\"";}
		if($nInRss==1 &&(substr(strtolower($sSrc),0,4)!='http')){
			$sHeader=self::getHostHeader();
		}
		else{
			$sHeader='';
		}
		$nContentAutoResizeImg=Global_Extend::getOption('content_auto_resize_img');
		$sOnloadAct=($nInRss==0 && !empty($nContentAutoResizeImg))?" onload=\"if(this.width>{$nContentAutoResizeImg}){this.resized=true;this.width={$nContentAutoResizeImg};}\"":'';
		if($bIsAttached==1){
			if(G::isImplementedTo($arrUrl[0],'IModel')){
				$sDownloadTime=" | ".G::L('已下载').'('.$arrUrl[0]->upload_download.')'.G::L('次');
				$sFileSize=' | '.E::changeFileSize($arrUrl[0]->upload_size);
				$sTitle="Filename:{$oUpload->upload_name} | Upload Time:".date('Y-m-d H:i:s',$arrUrl[0]->create_dateline);
				$sComment=' | '.G::L('评论').'('.$arrUrl[0]['upload_commentnum'].')';
				$sUploadUser=' | '.G::L('上传用户').':'.($arrUrl[0]->user->user_id?$arrUrl[0]->user->user_name:G::L('跌名'));
			}else{
				$sDownloadTime=$sFileSize=$sTitle=$sComment=$sUploadUser='';
			}
			if(Global_Extend::getOption('only_login_can_view_upload')==0 || $GLOBALS['___login___']!==false){
				$sCode="<a href=\"{$sHeader}{$sTargetSrc}\" target=\"_blank\"><img src=\"{$sHeader}{$sSrc}\" class=\"content-insert-image\" alt=\"".G::L('在新窗口浏览此图片')."\" title=\"".G::L('在新窗口浏览此图片')." {$sTitle} {$sUploadUser} {$sComment} {$sFileSize} {$sDownloadTime}\" border=\"0\"{$sOnloadAct}{$sShow}/></a>";
			}
			else{
				$sCode='<div class="locked">'.G::L('这个图片只能在登入之后查看。').G::L('请先')."<a href=\"".self::clearUrl($sHeader.G::U('register/index'))."\">".G::L('注册')."</a> ".G::L('或者')." <a href=\"".self::clearUrl($sHeader.G::U('login/index'))."\">".G::L('登录')."</a></div>";
			}
		}
		else{
			$sCode="<a href=\"{$sHeader}{$sTargetSrc}\" target=\"_blank\"><img src=\"{$sHeader}{$sSrc}\" class=\"content-insert-image\" alt=\"".G::L('在新窗口浏览此图片')."\" title=\"".G::L('在新窗口浏览此图片')."\" border=\"0\"{$sOnloadAct}{$sShow}/></a>";
		}
		return $sCode;
	}

	static public function makeImgWithUrl($sUrl,$sAlignCode,$sWidthCode,$sHeightCode,$sSrc,$nInRss=0,$bIsAttached=false){
		$arrUrl=self::getUrlPath($sSrc,true);
		if($arrUrl===false || (G::isImplementedTo($arrUrl[0],'IModel') && !is_file(DYHB_PATH.'/../Public/Upload/'.$arrUrl[0]['upload_savepath'].'/'.$arrUrl[0]['upload_savename']))){
			return "<div class=\"quote mediabox\"><div class=\"quote-title\"><img src=\"".($sPublicHeader=$nInRss==1?Global_Extend::getOption('blog_url'):'')."/Images/Public/Images/Media/viewimage.gif"."\" alt=\"\"/>".G::L('损坏图片')."</div><div class=\"quote-content\">".G::L('该文件已经损坏')."</div></div>";
		}

		if(is_array($arrUrl)){
			$sSrc=$arrUrl[2];
			$sTargetSrc=$arrUrl[1];
		}
		else{
			$sTargetSrc=$sSrc;
		}

		$sAlign=str_replace(' align=','',strtolower($sAlignCode));
		if($sAlign=='l') {$sShow=' align="left"';}
		elseif($sAlign=='r'){ $sShow=' align="right"';}
		else {$sShow='';}
		$nWidth=str_replace(' width=','',strtolower($sWidthCode));
		if(!empty($nWidth)) {$sShow.=" width=\"{$nWidth}\"";}
		else{
			if(G::isImplementedTo($arrUrl[0],'IModel')){
				$arrImageInfo=getimagesize(DYHB_PATH.'/../Public/Upload/'.$arrUrl[0]->upload_savepath.'/'.$arrUrl[0]->upload_savename);
				$sShow.=self::attachwidth($arrImageInfo[0]);
			}
		}
		$nHeight=str_replace(' height=','',strtolower($sHeightCode));
		if(!empty($nHeight)) {$sShow.=" height=\"{$nHeight}\"";}
		if($nInRss==1 &&(substr(strtolower($sSrc),0,4)!='http')){
			$sHeader=self::getHostHeader();
		}
		else{
			$sHeader='';
		}
		$nContentAutoResizeImg=Global_Extend::getOption('content_auto_resize_img');
		$sOnloadAct=($nInRss==1 && !empty($nContentAutoResizeImg))?" onload=\"if(this.width>{$nContentAutoResizeImg}){this.resized=true;this.width={$nContentAutoResizeImg};}\"":'';
		if($bIsAttached==1){
			if(G::isImplementedTo($arrUrl[0],'IModel')){
				$sDownloadTime=" | ".G::L('已下载').'('.$arrUrl[0]->upload_download.')'.G::L('次');
				$sFileSize=' | '.E::changeFileSize($arrUrl[0]->upload_size);
				$sTitle="Filename:{$oUpload->upload_name} | Upload Time:".date('Y-m-d H:i:s',$arrUrl[0]->create_dateline);
				$sComment=' | '.G::L('评论').'('.$arrUrl[0]['upload_commentnum'].')';
				$sUploadUser=' | '.G::L('上传用户').':'.($arrUrl[0]->user->user_id?$arrUrl[0]->user->user_name:G::L('跌名'));
			}else{
				$sDownloadTime=$sFileSize=$sTitle=$sComment=$sUploadUser='';
			}
			if(Global_Extend::getOption('only_login_can_view_upload')==0 || $GLOBALS['___login___']!==false){
				$sCode="<a href=\"{$sHeader}{$sTargetSrc}\" target=\"_blank\"><img src=\"{$sHeader}{$sSrc}\" class=\"content-insert-image\" alt=\"".G::L('在新窗口浏览此图片')."\" title=\"".G::L('在新窗口浏览此图片')." {$sTitle} {$sUploadUser} {$sComment} {$sFileSize} {$sDownloadTime}\" border=\"0\"{$sOnloadAct}{$sShow}/></a>";
			}
			else{
				$sCode='<div class="locked">'.G::L('这个图片只能在登入之后查看。').G::L('请先')."<a href=\"".self::clearUrl($sHeader.G::U('register/index'))."\">".G::L('注册')."</a> ".G::L('或者')." <a href=\"".self::clearUrl($sHeader.G::U('login/index'))."\">".G::L('登录')."</a></div>";
			}
		}
		else{
			$sCode="<a href=\"{$sHeader}{$sTargetSrc}\" target=\"_blank\"><img src=\"{$sHeader}{$sSrc}\" class=\"content-insert-image\" alt=\"".G::L('在新窗口浏览此图片')."\" title=\"".G::L('在新窗口浏览此图片')."\" border=\"0\"{$sOnloadAct}{$sShow}/></a>";
		}
		return $sCode;
	}

	static public function makeImgInRss($sSrc){
		$arrUrl=self::getUrlPath($sSrc,true);
		if($arrUrl===false || (G::isImplementedTo($arrUrl[0],'IModel') && !is_file(DYHB_PATH.'/../Public/Upload/'.$arrUrl[0]['upload_savepath'].'/'.$arrUrl[0]['upload_savename']))){
			return "<div class=\"quote mediabox\"><div class=\"quote-title\"><img src=\"".Global_Extend::getOption('blog_url')."/Images/Public/Images/Media/viewimage.gif"."\" alt=\"\"/>".G::L('损坏图片')."</div><div class=\"quote-content\">".G::L('该文件已经损坏')."</div></div>";
		}
		if(is_array($arrUrl)){
			$sSrc=$arrUrl[2];
			$sTargetSrc=$arrUrl[1];
		}
		else{
			$sTargetSrc=$sSrc;
		}
		if(G::isImplementedTo($arrUrl[0],'IModel')){
			$sDownloadTime="(".G::L('已下载').'('.$arrUrl[0]->upload_download.')'.G::L('次').")";
			$sFileSize='('.E::changeFileSize($arrUrl[0]->upload_size).')';
			$sTitle="title=\"Filename:{$oUpload->upload_name} | Upload Time:".date('Y-m-d H:i:s',$arrUrl[0]->create_dateline).'"';
		}else{
			$sDownloadTime=$sFileSize=$sTitle='';
		}
		$sSrc=(substr(strtolower($sSrc),0,4)=='http')?$sSrc:self::getHostHeader().$sSrc;
		$sStr="<br/><img src=\"".Global_Extend::getOption('blog_url')."/Public/Images/Media/viewimage.gif\" alt=\"\" {$sTitle}/>".G::L('在新窗口浏览此图片').'('.$sFileSize." | ".$sDownloadTime.")<br/>[url]{$sSrc}[/url]<br/>";
		return $sStr;
	}

	static public function xhtmlHighlightString($sStr){
		$sStr=base64_decode($sStr);
		$sHlt=highlight_string($sStr,true);
		if(PHP_VERSION>'5') {return "<div class=\"code\" style=\"overflow: auto;\">$sHlt</div>";}
		$sFon=str_replace(array('<font ','</font>'),array('<span ','</span>'),$sHlt);
		$sRet=preg_replace('#color="(.*?)"#','style="color: \\1"',$sFon);
		return "<div class=\"code\" style=\"overflow: auto;\">{$sRet}</div>";
	}

	static public function makeDownload($sUrl,$nFile,$nInRss,$bIsAttached=false){
		$arrUrl=self::getUrlPath($sUrl);
		$sPublicHeader=$nInRss==1?Global_Extend::getOption('blog_url'):'';
		if($nInRss==1 &&(substr(strtolower($sUrl),0,4)!='http')){
			$sHeader=self::getHostHeader();
		}
		else{
			$sHeader='';
		}
		if($arrUrl===false){
			return "<div class=\"quote\"><div class=\"quote-title\"><img src=\"".$sPublicHeader.__PUBLIC__."/Images/Media/download.gif\" alt=\"\"/>".G::L('下载文件')."{$sDownloadTime}</div><div class=\"quote-content\">".G::L('该文件已经损坏')."\">"." </div></div>";
		}
		if(is_array($arrUrl)){
			$sUrl=$arrUrl[1];
		}
		if($bIsAttached && G::isImplementedTo($arrUrl[0],'IModel')){
			$sDownloadTime="(".G::L('已下载').'('.$arrUrl[0]->upload_download.')'.G::L('次').")";
			$sFileSize='('.E::changeFileSize($arrUrl[0]->upload_size).')';
			$sTitle="title=\"Filename:{$arrUrl[0]->upload_name} | Upload Time:".date('Y-m-d H:i:s',$arrUrl[0]->create_dateline).'"';
			$sComment=' | <a href="'.PageType_Extend::getUploadUrl($arrUrl[0]).'#comments">'.G::L('评论').'('.$arrUrl[0]['upload_commentnum'].')</a>';
			$sUploadUser=' | '.G::L('上传用户').':'.($arrUrl[0]->user['user_id']?'<a href="'.PageType_Extend::getAuthorUrl($arrUrl[0]->user).'">'.$arrUrl[0]->user->user_name.'</a>':G::L('跌名'));
		}else{
			$sDownloadTime=$sFileSize=$sTitle=$sComment=$sUploadUser='';
		}
		if($nInRss==0){
			if(Global_Extend::getOption('only_login_can_view_upload')==0 || $GLOBALS['___login___']!==false || $nFile!=1){
				$sStr="<div class=\"quote downloadbox\"><div class=\"quote-title\"><img src=\"".$sPublicHeader.__PUBLIC__."/Images/Media/download.gif\" alt=\"\"/>".G::L('下载文件')."{$sDownloadTime}</div><div class=\"quote-content\"><a href=\"{$sHeader}{$sUrl}\" {$sTitle}>".G::L('点击这里下载文件')."{$sFileSize}</a>{$sComment}{$sUploadUser}</div></div>";
			}
			else{
				$sStr="<div class=\"quote\"><div class=\"quote-title\"><img src=\"".$sPublicHeader.__PUBLIC__."/Images/Media/download.gif\" alt=\"\"/>".G::L('下载文件')."{$sDownloadTime}</div><div class=\"quote-content\">".G::L('这个文件只能在登入之后下载。').G::L('请先')." <a href=\"".self::clearUrl($sHeader.G::U('register/index'))."\">".G::L('注册')."</a> ".G::L('或者')." <a href=\"".self::clearUrl($sHeader.G::U('login/index'))."\">".G::L('登录')."</a> </div></div>";
			}
		}
		else{
			if(Global_Extend::getOption('only_login_can_view_upload')==1 && $nFile==1){
				$sStr='<div class="locked">'.G::L('这个文件只能在登入之后下载。').G::L('请先')."<a href=\"".self::clearUrl($sHeader.G::U('register/index'))."\">".G::L('注册')."</a> ".G::L('或者')." <a href=\"".self::clearUrl($sHeader.G::U('login/index'))."\">".G::L('登录')."</a></div>";
			}
			else{
				$sStr="<a href=\"{$sHeader}{$sUrl}\" {$sTitle}>".G::L('点击这里下载文件')."{$sFileSize}{$sComment}{$sUploadUser}</a>";
			}
		}
		return $sStr;
	}

	static public function makeTable($sTableBody,$sWidthCode,$bIfPercentAge,$sBgColorCode,$sBorderColorCode){
		$sTableBody=stripslashes($sTableBody);
		$sShow="<table";
		$nWidth=str_replace(' width=','',strtolower($sWidthCode));
		if($bIfPercentAge=='%'){ $nWidth.='%';}
		if(!empty($nWidth)) {$sShow.=" width=\"{$nWidth}\"";}
		$sShow.=" cellpadding=\"0\" cellspacing=\"0\">\n<tr>\n";
		$sBgColor=str_replace(' bgcolor=','',strtolower($sBgColorCode));
		$sBorderColor=str_replace(' border=','',strtolower($sBorderColorCode));
		if(!$sBorderColor) {$sBorderColor="#000000";}
		if(!$sBgColor) {$sBgColor="#ffffff";}
		$sShow.="<td bgcolor=\"{$sBorderColor}\">\n";
		$sShow.="<table width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">\n<tr><td bgcolor=\"{$sBgColor}\">";
		$sTableBody=str_replace(',',"</td>\n<td bgcolor=\"{$sBgColor}\">",$sTableBody);
		$sTableBody=str_replace('<br/>',"</td></tr>\n<tr><td bgcolor=\"{$sBgColor}\">",$sTableBody);
		$sTableBody=str_replace('<br />',"</td></tr>\n<tr><td bgcolor=\"{$sBgColor}\">",$sTableBody);
		$sShow.=$sTableBody;
		$sShow.="</td></tr>\n</table>\n</td></tr>\n</table>";
		return $sShow;
	}

	public static function getUrlPath($Url,$bImg=false){
		if(!preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',$Url)){
			if(Global_Extend::getOption('is_hide_upload_really_path')==1){
				if(!preg_match("/[^\d-.,]/",$Url)){
					$oUpload=UploadModel::F('upload_id=?',$Url)->query();
				}
				else{
					$oUpload=UploadModel::F('upload_savename=?',$Url)->query();
				}
				if(!($oUpload instanceof UploadModel)){
					return false;
				}
				$sSrcPath=G::U('attachment/index?id='.Global_Extend::aidencode($oUpload['upload_id']));
				if($bImg===true){
					return array($oUpload,$sSrcPath,$oUpload['upload_isthumb']?G::U('attachment/index?id='.Global_Extend::aidencode($oUpload['upload_id']).'&thumb=1'):$sSrcPath);
				}
				else{
					return array($oUpload,$sSrcPath);
				}
			}
			$Url=Blog_Extend::blogContentUpload($Url,$bImg);
		}
		return $Url;
	}

	static public function getHostHeader(){
		return 'http://'.$_SERVER['HTTP_HOST'];
	}

	static public function clearUrl($sUrl){
		return str_replace('&','&amp;',$sUrl);
	}

}
