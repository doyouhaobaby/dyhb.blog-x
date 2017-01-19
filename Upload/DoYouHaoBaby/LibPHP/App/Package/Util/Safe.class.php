<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   PHP 安全相关($)*/

!defined('DYHB_PATH') && exit;

class Safe{

	public static function deepReplace($Search,$sSubject){
		$bFound=true;
		$sSubject =(string)$sSubject;
		while($bFound){
			$bFound=false;
			foreach((array)$Search as $sVal){
				while(strpos($sSubject,$sVal)!== false){
					$bFound=true;
					$sSubject=str_replace($sVal,'',$sSubject);
				}
			}
		}
		return $sSubject;
	}

	public static function escUrl($sUrl,$arrProtocols=null,$sContext='display'){
		$sOriginalUrl=$sUrl;
		if(''==trim($sUrl)){return $sUrl;}
		$sUrl=preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i','',$sUrl);
		$arrStrip=array('%0d','%0a','%0D','%0A');
		$sUrl=self::deepReplace($arrStrip,$sUrl);
		$sUrl=str_replace(';//','://',$sUrl); // 防止拼写错误
		// 加上http:// ，防止导入一个脚本如php，从而引发安全问题
		if(strpos($sUrl,':')=== false &&
			substr($sUrl,0,1)!= '/' && substr($sUrl,0,1)!= '#' && !preg_match('/^[a-z0-9-]+?\.php/i',$sUrl)){$sUrl='http://' . $sUrl;}
		if('display' == $sContext){
			$sUrl=str_replace('&amp;','&#038;',$sUrl);
			$sUrl=str_replace("'",'&#039;',$sUrl);
		}
		if(!is_array($arrProtocols)){$arrProtocols=array('http','https','ftp','ftps','mailto','news','irc','gopher','nntp','feed','telnet','mms','rtsp','svn');}// 协议检查
		return $sUrl;
	}

	public static function longCheck($sPost){
		$nMaxSlen=3000; // 限制较长输入项最多3000个字符
		if(!G::getMagicQuotesGpc()){// 判断magic_quotes_gpc是否为打开
			$sPost=G::addslashes($sPost); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
		}
		$sPost=self::lenLimit($sPost,$nMaxSlen);
		$sPost=str_replace("\'","’",$sPost);
		$sPost= Input::htmlspecialchars($sPost); // 将html标记转换为可以显示在网页上的html
		$sPost=nl2br($sPost); // 回车
		return $sPost;
	}

	 public static function bigCheck( $sPost){
		$nMaxSlen=20000; //限制大输入项最多20000个字符
		if(!G::getMagicQuotesGpc()){// 判断magic_quotes_gpc是否为打开
			$sPost=Input::htmlspecialchars($sPost); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
		}
		$sPost=self::lenLimit($sPost,$nMaxSlen);
		$sPost=str_replace("\'","’",$sPost);
		$sPost=str_replace("<script ","",$sPost);
		$sPost=str_replace("</script ","",$sPost);
		return $sPost;
	}

	public static function shortCheck($sStr){
		$nMaxSlen=500;//限制短输入项最多300个字符
		if(!G::getMagicQuotesGpc()){// 判断magic_quotes_gpc是否打开
			$sStr=Input::htmlspecialchars($sStr); // 进行过滤
		}
		$sStr=self::lenLimit($sStr,$nMaxSlen);
		$sStr=str_replace(array("\'","\\","#"),"",$sStr);
		if($sStr!=''){
			$sStr=Input::htmlspecialchars($sStr);
		}
		return preg_replace("/　+/","",trim($sStr));
	}

	public static function filterScript($sStr){
		return preg_replace(array('/<\s*script/','/<\s*\/\s*script\s*>/',"/<\?/","/\?>/"),array("&lt;script","&lt;/script&gt;","&lt;?","?&gt;"),$sStr);
	}

	public static function cleanHex($sInput){
		$sClean=preg_replace("![\][xX]([A-Fa-f0-9]{1,3})!","",$sInput);
		return $sClean;
	}

	public static function lenLimit($sStr,$nMaxSlen){
		if(isset($sStr{$nMaxSlen})){
			return " ";
		}

		else{
			return $sStr;
		}
	}

	public static function filtNumArray($IdStr){
		if($IdStr !=''){
			if(!is_array($IdStr)){
				$IdStr=explode(',',$IdStr);
			}
			$arrIdArray=array_map("intval",$IdStr);
			$IdStr=join(',',$arrIdArray);
			return $IdStr;
		}
		else{
			return 0;
		}
	}

	public static function filtStrArray( $StrOrArray){
		$sGstr="";
		if(! is_array($StrOrArray)){
			$StrOrArray=explode(',',$StrOrArray);
		}
		$StrOrArray=array_map('sqlFilter',$StrOrArray);
		foreach($StrOrArray as $sVal){
			if($sVal != ''){
				$sGstr.="'".$sVal."',";
			}
		}
		$sGstr=preg_replace("/,$/","",$sGstr);
		return $sGstr;
	}

	public static function sqlFilter($sStr){
		return str_replace(array("/","\\","'","#"," ","  ","%","&","\(","\)"),"",$sStr);
	}

	public static function filtFields($Fields){
		if(!is_array($Fields)){

			$Fields=explode(',',$Fields);
		}
		$arrFields=array_map('sqlFilter',$Fields);
		$sFields=join(',',$arrFields);
		$sFields=preg_replace('/^,|,$/','',$sFields);
		return $sFields;
	}

	public static function checkItem($Check,$arrMatchArray){
		$sResultStr='';
		if(!is_array($Check)){
			$Check=explode(',',$Check);
		}
		foreach($Check as $sRs){
			if(in_array($sRs,$arrMatchArray)){
				$sResultStr.=$sRs.',';
			}
		}
		return preg_replace("/,$/","",$sResultStr);
	}

	public function strFilter($StrOrArray,$nMaxNum=20000){
		if(is_array($StrOrArray)){
			foreach($StrOrArray as $sKey=>$val){
				$StrOrArray[self::strFilter($sKey)]=self::strFilter($val,$nMaxNum);
			}
		}
		else{
			$StrOrArray=self::lenLimit($StrOrArray,$nMaxNum);
			$StrOrArray=trim($StrOrArray);
			$StrOrArray=preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/','&\\1',Input::htmlspecialchars($StrOrArray));
			$StrOrArray=str_replace("　","",$StrOrArray);
		}
		return G::addslashes($StrOrArray);
	}

	public function htmlFilter($StrOrArray,$nMaxNum=20000){
		if(is_array($StrOrArray)){
			foreach($StrOrArray as $sKey=>$val){
				$StrOrArray[self::htmlFilter($sKey)]=self::htmlFilter($val);
			}
		}
		else{
			$StrOrArray=self::lenLimit($StrOrArray,$nMaxNum);
			$StrOrArray=trim($StrOrArray);
			$arrTranBefore=array('/<\s*a[^>]*href\s*=\s*[\'\"]?(javascript|vbscript)[^>]*>/i','/<([^>]*)on(\w)+=[^>]*>/i','/<\s*\/?\s*(script|i?frame)[^>]*\s*>/i');
			$arrTranAfter=array('<a href="#">','<$1>','&lt;$1&gt;');
			$StrOrArray=preg_replace($arrTranBefore,$arrTranAfter,$StrOrArray);
			$StrOrArray=str_replace("　","",$StrOrArray);
		}
		return G::addslashes($StrOrArray);
	}

	public static function limitTime($LimitTime){
		if(!empty($LimitTime)){
			if(is_string($LimitTime)){
				$LimitTime=array($LimitTime);
			}
			foreach($LimitTime as $sTimeRs){
				$arrTime=explode("-",$sTimeRs);
				$nLimitMinTime=strtotime($arrTime[0]);
				$nLimitMaxTime=strtotime((isset($arrTime[1])?$arrTime[1]:''));
				if($nLimitMaxTime<$nLimitMinTime){
					$nLimitMaxTime=$nLimitMaxTime+60*60*24;
				}
				if(CURRENT_TIMESTAMP>=$nLimitMinTime && CURRENT_TIMESTAMP<=$nLimitMaxTime){
					E(sprintf('You can only %s and %s time to access this site!',date('Y-m-d H:i:s',$nLimitMinTime),date('Y-m-d H:i:s',$nLimitMaxTime)));
				}
			}
		}
	}

	public static function limitIp($LimitIpList){
		$sVisitorIp=isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR']?$_SERVER['REMOTE_ADDR']:E::getIp();
		if(!empty($LimitIpList)){
			if(is_string($LimitIpList)){
				$LimitIpList=array($LimitIpList);
			}
			foreach($LimitIpList as $sIpRs){
				if(preg_match("/$sIpRs/",$sVisitorIp)){
					E(sprintf('You IP %s are banned, you can not access the site.',$sVisitorIp));
				}
			}
		}
	}

	public function checkAttackEvasive($nAttackEvasive=1){
		if(in_array($nAttackEvasive,array(1,3))){// 防止刷新
			if(G::cookie('last_request')){
				list($nLastRequest,$sLastPath)=explode("\t",G::cookie('last_request'));
				$nOnlineTime=CURRENT_TIMESTAMP-$nLastRequest;
			}
			else{
				$nLastRequest=$sLastPath='';
			}
			$sRequestUri=$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
			if($sRequestUri==$sLastPath && $nOnlineTime<2){
				E('Refresh Limitation Enabled.The time between your two requests is smaller than 2 seconds,please do NOT refresh and wait for automatical forwarding ...');
				exit();
			}
			G::cookie('last_request',CURRENT_TIMESTAMP."\t".$sRequestUri);
		}

		/** 检测代理 */
		if(in_array($nAttackEvasive,array(2,3)) and ($_SERVER['HTTP_X_FORWARDED_FOR'] or $_SERVER['HTTP_VIA'] or $_SERVER['HTTP_PROXY_CONNECTION'] or $_SERVER['HTTP_USER_AGENT_VIA'])){
			E('Proxy Connection Denied.Your request was forbidden due to the administrator has set to deny all proxy connection.');
		}
	}

}

function sqlFilter($sStr){
	return Safe::sqlFilter($sStr);
}
