<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Trackback 通告引用实现类($)*/

!defined('DYHB_PATH') && exit;

class Trackback {

	protected $_sErrorMessageBox="<font color='red'>***</font>";
	protected $_sSucessedMessageBox="<font color='green'>***</font>";
	protected $_sBlogUrl;
	protected $_sTitle;
	protected $_sBlogName;
	protected $_sExcerpt;
	protected $_arrTrackbacks=array();
	protected $_arrSucessedTrackbacks=array();
	protected $_arrFailedTrackbacks=array();
	protected $_bIsError=false;
	protected $_arrSucessedTrackbackMessages=array();
	protected $_arrFailedTrackbackMessages=array();

	public function __construct($sBlogUrl,$sTitle='',$sBlogName='',$sExcerpt=''){
		if($sTitle==''){$sTitle=$sBlogUrl;}
		if($sBlogName ==''){$sBlogName='Unknowed blog name';}
		$sExcerpt=String::subString($sExcerpt,0,252);
		$this->_sBlogUrl=$sBlogUrl;// 初始化相关数据
		$this->_sTitle=$sTitle;
		$this->_sBlogName=$sBlogName;
		$this->_sExcerpt=$sExcerpt;
	}

	public function sendTrackback($sPingUrl){
		$this->_arrTrackbacks=$arrHosts=explode("\n",$sPingUrl);
		$sTrackbackMessage='';
		foreach ($arrHosts as $sHost){
			$sHost=trim($sHost);
			if(strstr(strtolower($sHost),"http://")|| strstr(strtolower($sHost),"https://")){
				$sData="url=".rawurlencode($this->_sBlogUrl)."&title=".rawurlencode($this->_sTitle)."&blog_name=".rawurlencode($this->_sBlogName)."&excerpt=".rawurlencode($this->_sExcerpt);
				$sResult=strtolower($this->sendPacket($sHost,$sData));
				if(empty($sResult)){// 主机无法连接
					$this->_bIsError =true;
					$this->_arrFailedTrackbacks[]=$sHost;// 记录发送失败的 trackback URL 地址
					$sTrackbackMessage.="{$sHost}:status<br/>";
					$sTrackbackMessage.="<font color='red'>".G::L('主机连接失败','NetDyhb')."</font>"."{$sHost} faild<br>";
					$sTrackbackMessage.=str_replace('***',G::L("请求 Trackback URL %s 失败，对方返回的消息为(%s)，可能为错误消息。",'NetDyhb',null,$sHost,G::L('主机连接失败','NetDyhb')),$this->_sErrorMessageBox).'<br/>';
					$this->_arrFailedTrackbackMessages[]=G::L("请求 Trackback URL %s 失败，对方返回的消息为(%s)，可能为错误消息。",'NetDyhb',null,$sHost,G::L('主机连接失败','NetDyhb'));
				}
				else{
					if(strstr($sResult,"<error>0</error>")=== false){// 处理反馈消息
						$this->_bIsError =true;
						$this->_arrFailedTrackbacks[]=$sHost;// 记录发送失败的 trackback URL 地址
						preg_match("/<message>(.*)<\/message>/Uis",$sResult,$arrMatch);
						$sTrackbackMessage.="{$sHost}:status<br/>";
						$sTrackbackMessage.="<font color='red'>$arrMatch[0]</font>"."{$sHost} faild<br>";
						$sTrackbackMessage.=str_replace('***',G::L("请求 Trackback URL %s 失败，对方返回的消息为(%s)，可能为错误消息。",'NetDyhb',null,$sHost,$arrMatch[0]),$this->_sErrorMessageBox).'<br/>';
						$this->_arrFailedTrackbackMessages[]=G::L("请求 Trackback URL %s 失败，对方返回的消息为(%s)，可能为错误消息。",'NetDyhb',null,$sHost,$arrMatch[0]);
					}
					else{
						$this->_arrSucessedTrackbacks[]=$sHost;// 记录发送失败的 trackback URL 地址
						$sTrackbackMessage.="<font color='green'>{$sHost} sucessed</font><br>";
						$sTrackbackMessage.=str_replace('***',G::L("发送 Trackback URL %s 成功!",'NetDyhb',null,$sHost),$this->_sSucessedMessageBox).'<br/>';
						$this->_arrSucessedTrackbackMessages[]=G::L("发送 Trackback URL %s 成功!",'NetDyhb',null,$sHost);
					}
				}
			}
		}
		return $sTrackbackMessage;
	}

	protected function sendPacket($sUrl,$sData){
		$arrUrlInformation=parse_url($sUrl);
		if(isset($arrUrlInformation['query'])){
			$sData.="&".$arrUrlInformation['query'];
		}
		if($arrUrlInformation['host']=='localhost'){// 处理localhost信息
			$arrUrlInformation['host']='127.0.0.1';
		}
		if(!$hFp=@fsockopen($arrUrlInformation['host'],(($arrUrlInformation['port'])?$arrUrlInformation['port'] : "80"),$nErrno,$sError,3)){// 通过socket发送数据
			return false;
		}
		$sOut="POST ".$arrUrlInformation['path']." HTTP/1.1\r\n";
		$sOut.="Host: ".$arrUrlInformation['host']."\r\n";
		$sOut.="Content-type: application/x-www-form-urlencoded\r\n";
		$sOut.="Content-length: ".strlen($sData)."\r\n";
		$sOut.="Connection: close\r\n\r\n";
		$sOut.=$sData;
		fwrite($hFp,$sOut);
		$sHttpResponse='';
		while(!feof($hFp)){
			$sHttpResponse.= fgets($hFp,128);
		}
		@fclose($hFp);
		return $sHttpResponse;
	}

	public function clearTrackbacks(){
		$nCount=$this->_arrTrackbacks;
		$this->_arrTrackbacks=array();
		return $nCount;
	}

	public function clearSucessedTrackbacks(){
		$nCount=$this->_arrSucessedTrackbacks;
		$this->_arrSucessedTrackbacks=array();
		return $nCount;
	}

	public function clearFailedTrackbacks(){
		$nCount=$this->_arrFailedTrackbacks;
		$this->_arrFailedTrackbacks=array();
		return $nCount;
	}

	public function clearSucessedTrackbackMessages(){
		$nCount=$this->_arrSucessedTrackbackMessages;
		$this->_arrSucessedTrackbackMessages=array();
		return $nCount;
	}

	public function clearFailedTrackbackMessages(){
		$nCount=$this->_arrFailedTrackbackMessages;
		$this->_arrFailedTrackbackMessages=array();
		return $nCount;
	}

	public function setIsError($bIsError=true){
		$sOldValue=$this->_bIsError;
		$this->_bIsError=$bIsError;
		return $sOldValue;
	}

	public function setErrorMessageBox($sErrorMessageBox){
		$sOldValue=$this->_sErrorMessageBox;
		$this->_sErrorMessageBox=$sErrorMessageBox;
		return $sOldValue;
	}

	public function setSucessedMessageBox($sSucessedMessageBox){
		$sOldValue=$this->_sSucessedMessageBox;
		$this->_sSucessedMessageBox=$sSucessedMessageBox;
		return $sOldValue;
	}

	public function setBlogUrl($sBlogUrl){
		$sOldValue=$this->_sBlogUrl;
		$this->_sBlogUrl=$sBlogUrl;
		return $sOldValue;
	}

	public function setTitle($sTitle){
		$sOldValue=$this->_sTitle;
		$this->_sTitle=$sTitle;
		return $sOldValue;
	}

	public function setBlogName($sBlogName){
		$sOldValue=$this->_sBlogName;
		$this->_sBlogName=$sBlogName;
		return $sOldValue;
	}

	public function setExcerpt($sExcerpt){
		$sOldValue=$this->_sExcerpt;
		$this->_sExcerpt=$sExcerpt;
		return $sOldValue;
	}

	static public function xmlSuccess(){
		header("Content-type:application/xml");
		echo "<?xml version=\"1.0\" ?>";
		echo "<response>";
		echo "<error>0</error>";
		echo "</response>";
		exit;
	}

	static public function xmlError($sError){
		header("Content-type:application/xml");
		echo "<?xml version=\"1.0\" ?>";
		echo "<response>";
		echo "<error>1</error>";
		echo "<message>{$sError}</message>";
		echo "</response>";
		exit;
	}

	public function getBlogUrl(){
		return $this->_sBlogUrl;
	}

	public function getTitle(){
		return $this->_sTitle;
	}

	public function getBlogName(){
		return $this->_sBlogName;
	}

	public function getExcerpt(){
		return $this->_sExcerpt;
	}

	public function getTrackbacks(){
		return $this->_arrTrackbacks;
	}

	public function getSucessedTrackbacks(){
		return $this->_arrSucessedTrackbacks;
	}

	public function getFailedTrackbacks(){
		return $this->_arrFailedTrackbacks;
	}

	public function isError(){
		return $this->_bIsError;
	}

	public function getSucessedTrackbackMessages(){
		return $this->_arrSucessedTrackbackMessages;
	}

	public function getFailedTrackbackMessages(){
		return $this->_arrFailedTrackbackMessages;
	}

}
