<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Trackback控制器($) */

!defined('DYHB_PATH') && exit;

class TrackbackController extends CommonController{

	public function index(){
		define('IS_TRACKBACKLIST',TRUE);
		define('CURSCRIPT','trackbacklist');
		$arrMap['trackback_status']=1;
		$nTotalRecord=TrackbackModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_trackback_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrTrackbackList=TrackbackModel::F()->where($arrMap)->all()->order('`trackback_id` DESC')->asArray()->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nTotalTrackback',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTrackbackLists',$arrTrackbackList);
		$this->assign('the_trackback_description',Global_Extend::getOption('the_trackback_description'));
		$this->display('trackback');
	}

	public function url(){
		if(Global_Extend::getOption('allow_trackback')!=1){
			Trackback::xmlError(G::L('引用功能被关闭了。'));
		}
		$nId=intval(G::getGpc('id','G'));
		$sExtra=trim(G::getGpc('extra','G'));
		if($nId<1){
			Trackback::xmlError(G::L('不正确的日志引用ID','app'));
		}
		$charset_convert=0;
		$sCharset=strtolower($_SERVER['HTTP_ACCEPT_CHARSET']);
		if($sCharset && !strstr($sCharset,'utf-8')){
			if(strstr($sCharset,'gb')|| strstr($sCharset,'big5')){
				Trackback::xmlError(G::L('系统只接受UTF-8格式的引用数据','app'));
			}
		}
		$oBlog=BlogModel::F('blog_id=? AND blog_isshow=1',$nId)->query();
		if(empty($oBlog['blog_id'])){
			Trackback::xmlError(G::L('错误的日志ID，系统已经删除或或者隐藏了该日志','app'));
		}
		if($oBlog['blog_allowedtrackback']==0){
			Trackback::xmlError(G::L('本日志不接受引用','app'));
		}
		$sTbAuthEntic=Blog_Extend::trackbackCertificate($oBlog['blog_id'],$oBlog['blog_dateline']);
		if($sTbAuthEntic!=$sExtra){
			Trackback::xmlError(G::L('引用验证失败，可能是引用已经过期','app'));
		}
		$sTitle=trim(G::getGpc('title'));
		$sExcerpt=trim(G::getGpc('excerpt'));
		$sUrl=trim(G::getGpc('url'));
		$sBlogName=trim(G::getGpc('blog_name'));
		$arrSourceForCheck=array('title'=>$sTitle,'excerpt'=>$sExcerpt,'url'=>$sUrl,'blog_name'=>$sBlogName);
		if(empty($sUrl)){
			Trackback::xmlError(G::L('引用的URL 不能为空','app'));
		}
		if(empty($sExcerpt)){
			Trackback::xmlError(G::L('引用的摘要不能为空','app'));
		}
		$sTitle=String::subString($sTitle,0,Global_Extend::getOption('max_trackback_length'));
		$sBlogName=String::subString($sBlogName,0,Global_Extend::getOption('max_trackback_length'));
		$sExcerpt=String::subString($sExcerpt,0,Global_Extend::getOption('max_trackback_length'));
		if(empty($sTitle)){
			$sTitle=G::L('暂无标题','app');
		}
		else{
			$sTitle=$this->tb_convert($sTitle);
		}
		if(empty($sBlogName)){
			$sBlogName=G::L('暂无博客名','app');
		}
		else{
			$sBlogName=$this->tb_convert($sBlogName);
		}
		// Trackback评分式的防御机制
		// Base from 4ngel + bo-blog
		$nSetSpam=0;
		$nPoint=0;
		if(Global_Extend::getOption('audit_trackback')==3){ // 如果人工审核
			$nSetSpam=1;
		}
		elseif(Global_Extend::getOption('audit_trackback')!=0){
			if(Global_Extend::getOption('audit_trackback')==2){
				$sSourceContent='';
				if(!empty($arrSourceForCheck['url'])){
					$sSourceContent=$this->fopen_url($arrSourceForCheck['url'],true);
				}
				if(empty($sSourceContent)){
					$nPoint -=1;
				}
				else{
					if(strpos(strtolower($sSourceContent),strtolower($_SERVER['HTTP_HOST']))!==FALSE){
						$nPoint+=1;// 对比链接，如果原代码中包含本站的hostname就+1分，这个未必成立
					}
					if(strpos(strtolower($sSourceContent),strtolower($arrSourceForCheck['title']))!==FALSE){
						$nPoint+=1;// 对比标题，如果原代码中包含发送来的title就+1分，这个基本可以成立
					}
					if(strpos(strtolower($sSourceContent),strtolower($arrSourceForCheck['excerpt']))!==FALSE){
						$nPoint+=1;// 对比内容，如果原代码中包含发送来的excerpt就+1分，这个由于标签或者其他原因，未必成立
					}
				}
			}
			$nTbInterval=(Global_Extend::getOption('audit_trackback')==1)? '30': '60';// 根据防范强度设置时间间隔，强的话在30内发现有同一IP发送。弱的话就是60秒内发现有同一IP发送.
			$nTyrTrackbackNum=TrackbackModel::F('trackback_ip=? AND `create_dateline`+{$nTbInterval}>='.CURRENT_TIMESTAMP,E::getIp())->all()->getCounts();;
			if($nTyrTrackbackNum>0){// 在单位时间内发送的次数
				$nPoint-=1;// 如果发现在单位时间内同一IP发送次数大于0就扣一分，人工有这么快发送trackback的吗？
			}
			if(Global_Extend::getOption('audit_trackback')==2){
				$nSetSpam=(($nPoint<1)?1:0);// 防范强:最终分数少于1分就CUT！
			}
			else{
				$nSetSpam=(($nPoint<0)?1:0);// 防范弱:最终分数少于0分才CUT！
			}
		}

		$oTrackback=new TrackbackModel();// 引用数据入库
		if($nSetSpam==1){// 垃圾，加入审核
			$oTrackback->trackback_status=0;
		}
		$oTrackback->trackback_title=$sTitle;
		$oTrackback->trackback_excerpt=$sExcerpt;
		$oTrackback->trackback_blogname=$sBlogName;
		$oTrackback->trackback_url=$sUrl;
		$oTrackback->blog_id=$nId;
		$oTrackback->trackback_points=$nPoint;
		$oTrackback->save();
		if($oTrackback->isError()){
			Trackback::xmlError(G::L('引用数据入库失败','app'));
		}
		$oBlog->blog_trackbacknum=$oBlog->blog_trackbacknum+1;// 更新博客引用数据
		$oBlog->save(0,'update');
		if($oTrackback->isError()){
			Trackback::xmlError(G::L('更新引用数量失败','app'));
		}
		Cache_Extend::front_widget_static();
		Trackback::xmlSuccess();
	}

	public function tb_convert($sStr){
		$sStr=trim($sStr);
		$sStr=preg_replace("/&(.+?);/is","",$sStr);
		$sStr=preg_replace("/\[(.+?)\]/is","",$sStr);
		$sStr=str_replace("\\","",$sStr);
		return $sStr;
	}

	public function fopen_url($sUrl,$bConvertCase=false){
		$sFileContent='';
		$sUrl=parse_url($sUrl);
		if($sUrl['port']==''){
			$sUrl['port']=80;
		}
		if($sUrl['host']=='localhost'){
			$sUrl['host']='127.0.0.1';
		}
		$hFp=fsockopen($sUrl['host'],$sUrl['port'],$nErrno,$sErrstr,8);
		if($hFp){
			$sOut="GET {$sUrl['path']}".($sUrl['query'] ? '?'.$sUrl['query']: '')." HTTP/1.1\r\n";
			$sOut.="Host: {$sUrl['host']}\r\n";
			$sOut.="Connection: Close\r\n\r\n";
			fwrite($hFp,$sOut);
			while(!feof($hFp)){
				$sFileContent.=fgets($hFp,128);
			}
			fclose($hFp);
		}
		return $sFileContent;
	}

}
