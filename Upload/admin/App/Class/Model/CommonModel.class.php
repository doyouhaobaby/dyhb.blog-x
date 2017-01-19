<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	公用模型($)*/

!defined('DYHB_PATH') && exit;

class CommonModel extends Model{

	public function getHostByUrl($sUrl){
		$arrResult=parse_url($sUrl);
		if(!$arrResult['host']){
			return -1;
		}
		if(!preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/",$arrResult['host'])){
			$sIp=@gethostbyname($arrResult['host']);
			if(!$sIp || $sIp==$arrResult['host']){
				return -2;
			}
			return $sIp;
		}
		else{
			return $arrResult['host'];
		}
	}

	public function fopen($sUrl,$nLimit=0,$sPost='',$sCookie='',$bySocket=FALSE ,$sIp='',$nTimeout=15,$bBlock=TRUE,$sEncodeType='URLENCODE'){
		$sReturn='';
		$nErrNo=0;
		$sErrStr='';
		$arrMatches=parse_url($sUrl);
		$sHost=$arrMatches['host'];
		$sPath=$arrMatches['path']?$arrMatches['path'].($arrMatches['query']?'?'.$arrMatches['query']:''):'/';
		$nPort=!empty($arrMatches['port'])? $arrMatches['port']:80;
		if($sPost){
			$sOut="POST{$sPath} HTTP/1.0\r\n";
			$sOut.="Accept: */*\r\n";
			$sOut.="Accept-Language: zh-cn\r\n";
			$sBoundary=$sEncodeType=='URLENCODE'?'':';'.substr($sPost,0,trim(strpos($sPost,"\n")));
			$sOut.=$sEncodeType=='URLENCODE'?"Content-Type: application/x-www-form-urlencoded\r\n":"Content-Type: multipart/form-data{$sBoundary}\r\n";
			$sOut.="User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$sOut.="Host:{$sHost}:{$nPort}\r\n";
			$sOut.='Content-Length: '.strlen($sPost)."\r\n";
			$sOut.="Connection: Close\r\n";
			$sOut.="Cache-Control: no-cache\r\n";
			$sOut.="Cookie: $sCookie\r\n\r\n";
			$sOut.=$sPost;
		}
		else{
			$sOut="GET{$sPath} HTTP/1.0\r\n";
			$sOut.="Accept: */*\r\n";
			$sOut.="Accept-Language: zh-cn\r\n";
			$sOut.="User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
			$sOut.="Host:{$sHost}:{$nPort}\r\n";
			$sOut.="Connection: Close\r\n";
			$sOut.="Cookie:{$sCookie}\r\n\r\n";
		}

		try{
			$hFp=@fsockopen((trim($sIp)? trim($sIp): $sHost),$nPort,$nErrNo,$sErrStr,$nTimeout);
		}
		catch(exception $oE){
			$hFp=false;
		}

		if(!$hFp){
			return '';
		}
		else{
			stream_set_blocking($hFp,$bBlock);
			stream_set_timeout($hFp,$nTimeout);
			@fwrite($hFp,$sOut);
			$arrStatus=stream_get_meta_data($hFp);
			if(!$arrStatus['timed_out']){
				while(!feof($hFp)){
					if(($sHeader=@fgets($hFp)) && ($sHeader=="\r\n" || $sHeader=="\n")){
						break;
					}
				}
				$bStop=false;
				while(!feof($hFp)&& !$bStop){
					$sData=fread($hFp,($nLimit==0 || $nLimit>8192?8192:$nLimit));
					$sReturn.=$sData;
					if($nLimit){
						$nLimit -=strlen($sData);
						$bStop=$nLimit <=0;
					}
				}
			}
			@fclose($hFp);
			return $sReturn;
		}
	}

	public function fopen2($sUrl,$nLimit=0,$sPost='',$sCookie='',$bBySocket=FALSE,$sIp='',$nTimeout=15,$bBlock=TRUE,$sEncodeType='URLENCODE'){
		$nTime=G::getGpc('_times_');
		$nTime=!empty($nTime)? intval($nTime)+1:1;
		if($nTime>2){
			return '';
		}
		$sUrl.=(strpos($sUrl,'?')===FALSE?'?':'&')."_times_={$nTime}";
		return $this->fopen($sUrl,$nLimit,$sPost,$sCookie,$bBySocket,$sIp,$nTimeout,$bBlock,$sEncodeType);
	}

}
