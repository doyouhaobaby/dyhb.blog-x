<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   扩展静态函数库($)*/

!defined('DYHB_PATH')&& exit;

class E{

	static public function changeFileSize($nFileSize){
		if($nFileSize>=1073741824){$nFileSize=round($nFileSize/1073741824,2).'GB';}
		elseif($nFileSize>=1048576){$nFileSize=round($nFileSize/1048576,2).'MB';}
		elseif($nFileSize >= 1024){$nFileSize=round($nFileSize/1024,2).'KB';}
		else{$nFileSize=$nFileSize.G::L('字节','dyhb');}
		return $nFileSize;
	}

	static public function getMicrotime(){
		list($nM1,$nM2)=explode(' ',microtime());
		return((float)$nM1 +(float)$nM2);
	}

	static public function oneImensionArray($arrArray){
		return count($arrArray)==count($arrArray,1);
	}

	public static function arrayHandler($arrVars,$nType=1,$nGo=2){
		$nLen=count($arrVars);
		$sParam='';
		if($nType==1){// 类似$hello['test']['test2']
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.="['{$arrVars[$nI]}']";
			}
		}
		elseif($nType=='2'){// 类似$hello->test1->test2
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.="->{$arrVars[$nI]}";
			}
		}
		elseif($nType=='3'){// 类似$hello.test1.test2
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.=".{$arrVars[$nI]}";
			}
		}
		return $sParam;
	}

	static public function getIp(){
		static $sRealip=NULL;
		if($sRealip !== NULL){
			return $sRealip;
		}
		if(isset($_SERVER)){
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$arrValue=explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
					foreach($arrValue AS $sIp){// 取X-Forwarded-For中第一个非unknown的有效IP字符串
						$sIp=trim($sIp);
						if($sIp!='unknown'){
							$sRealip=$sIp;
							break;
						}
					}
				}
				elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
					$sRealip=$_SERVER['HTTP_CLIENT_IP'];
				}
				else{
					if(isset($_SERVER['REMOTE_ADDR'])){
						$sRealip=$_SERVER['REMOTE_ADDR'];
					}
					else{
						$sRealip='0.0.0.0';
					}
				}

			}
			else{
				if(getenv('HTTP_X_FORWARDED_FOR')){
					$sRealip=getenv('HTTP_X_FORWARDED_FOR');
				}
				elseif(getenv('HTTP_CLIENT_IP')){
					$sRealip=getenv('HTTP_CLIENT_IP');
				}
				else{
					$sRealip=getenv('REMOTE_ADDR');
				}
			}
			preg_match("/[\d\.]{7,15}/",$sRealip,$arrOnlineip);
			$sRealip=!empty($arrOnlineip[0])?$arrOnlineip[0]:'0.0.0.0';
			return $sRealip;
	}

	static public function authcode($string,$operation=TRUE,$key=null,$expiry=3600){
		$ckey_length=4;
		$key=md5($key?$key:$GLOBALS['_commonConfig_']['DYHB_AUTH_KEY']);
		$keya=md5(substr($key, 0, 16));
		$keyb=md5(substr($key, 16, 16));
		$keyc=$ckey_length?($operation===TRUE?substr($string,0,$ckey_length):substr(md5(microtime()),-$ckey_length)):'';
		$cryptkey=$keya.md5($keya.$keyc);
		$key_length=strlen($cryptkey);
		$string=$operation===TRUE?base64_decode(substr($string, $ckey_length)):sprintf('%010d',$expiry?$expiry+time():0).substr(md5($string.$keyb),0,16).$string;
		$string_length=strlen($string);
		$result='';
		$box=range(0,255);
		$rndkey=array();
		for($i=0;$i<=255;$i++){
			$rndkey[$i]=ord($cryptkey[$i%$key_length]);
		}
		for($j=$i=0;$i<256;$i++){
			$j=($j+$box[$i]+$rndkey[$i])%256;
			$tmp=$box[$i];
			$box[$i]=$box[$j];
			$box[$j]=$tmp;
		}
		for($a=$j=$i=0;$i<$string_length;$i++){
			$a=($a+1)%256;
			$j=($j+$box[$a])%256;
			$tmp=$box[$a];
			$box[$a]=$box[$j];
			$box[$j]=$tmp;
			$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
		}
		if($operation===TRUE){
			if((substr($result,0,10)==0 || substr($result,0,10)-time()>0) && substr($result,10,16)==substr(md5(substr($result,26).$keyb),0,16)){
				return substr($result,26);
			}
			else{
				return '';
			}
		}
		else{
			return $keyc.str_replace('=','',base64_encode($result));
		}
	}

	public static function seccodeConvert(&$nSeccode,$bChinesecode=false,$nSeccodeTupe=1){
		$nSeccode=substr($nSeccode,-6);
		if($bChinesecode and $nSeccodeTupe){ // 中文
			$sChineseLang='们以我到他会作时要动国产的一是工就年巨徒私银伊景坦累匀霉杜乐';
			$arrCode=array(substr($nSeccode,0,3),substr($nSeccode,3,3));
			$nSeccode=array();
			for($nI=0;$nI<2;$nI++){
				$nSeccode[$nI]=substr($sChineseLang,$arrCode[$nI]*3,3);
			}
			unset($sChineseLang);
			return;
		}
		else{
			$sS=sprintf('%04s',base_convert($nSeccode,10,24));
			$sSeccodeUnits='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$nSeccode='';
			for($nI=0;$nI<4;$nI++){
				$sUnit=ord($sS{$nI});
				$nSeccode.=($sUnit>=0x30 and $sUnit<=0x39)?$sSeccodeUnits[$sUnit-0x30]:$sSeccodeUnits[$sUnit-0x57];
			}
		}
	}

	static public function returnBytes($sVal){
		$sVal=trim($sVal);
		$sLast=strtolower($sVal{strlen($sVal)-1});
		switch($sLast){
			case 'g':
				$sVal*=1024*1024*1024;
			case 'm':
				$sVal*=1024*1024;
			case 'k':
				$sVal*=1024;
		}
		return $sVal;
	}

	static function includeDirPhpfile($sPath,$bInclude=FALSE){
		A::DDIR($sPath,sprintf('路径（$sPath）%s不是一个有效的目录',$sPath));
		$arrListFuncs=glob($sPath.'/*.php');
		if($bInclude===true){
			 if($arrListFuncs){
				foreach($arrListFuncs as $sValue){include($sValue);}
			}
			unset($arrListFuncs);
		}
		else{
			return $arrListFuncs;
		}
	}

	static public function listDir($sDir,$bFullPath=FALSE){
		if(is_dir($sDir)){
			$hDir=opendir($sDir);
			while(($sFile=readdir($hDir))!== false){
				if((is_dir($sDir."/".$sFile)) && $sFile!="." && $sFile!=".." && $sFile!='_svn'){
				if($bFullPath===TRUE){$arrFiles[]=$sDir."/".$sFile;}
				else{$arrFiles[]=$sFile;}
				}
			}
			closedir($hDir);
			return $arrFiles;
		}
		else{
			return false;
		}
	}

	public static function hasStaticMethod($sClassName,$sMethodName){
		$oRef=new ReflectionClass($sClassName);
		if($oRef->hasMethod($sMethodName) and $oRef->getMethod($sMethodName)->isStatic()){
			return true;
		}
		return false;
	}

	static public function mbUnserialize($sSerial){
		$sSerial=preg_replace('!s:(\d+):"(.*?)";!se',"'s:'.strlen('$2').':\"$2\";'",$sSerial);
		$sSerial=str_replace("\r","",$sSerial);
		return unserialize($sSerial);
	}

	static public function getAvatar($nUid,$sSize='middle'){
		$sSize=in_array($sSize,array('big','middle','small','origin'))?$sSize:'middle';
		$nUid=abs(intval($nUid));
		$nUid=sprintf("%09d",$nUid);
		$nDir1=substr($nUid,0,3);
		$nDir2=substr($nUid,3,2);
		$nDir3=substr($nUid,5,2);
		return $nDir1.'/'.$nDir2.'/'.$nDir3.'/'.substr($nUid, -2)."_avatar_{$sSize}.jpg";
	}

	static public function getExtName($sFileName,$nCase=0){
		if(!preg_match('/\./',$sFileName)){return '';}
		$arr=explode('.',$sFileName);
		$sExtName=end($arr);
		if($nCase==1){return strtoupper($sExtName);}
		elseif($nCase==2){return strtolower($sExtName);}
		else{return $sExtName;}
	}

}
