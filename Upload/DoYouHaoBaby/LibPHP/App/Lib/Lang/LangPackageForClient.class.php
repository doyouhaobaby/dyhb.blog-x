<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   语言包($) */

!defined('DYHB_PATH') && exit;

class LangPackageForClient{

	static public function getLangPackage($sLangName,$sPackageName,$bRefresh=false){
		$oThePackage=LangPackage::getPackage($sLangName,$sPackageName);
		if(!$oThePackage){return;}
		header('Content-type: text/html; charset=utf-8');
		if(!$bRefresh){// 检查客户端缓存是否可用
			$nThisClassMTime=filemtime(__FILE__);
			$nPackageFileMTime=$oThePackage->getUpdateTime();
			$nMTime =($nPackageFileMTime>$nThisClassMTime)?$nPackageFileMTime:$nThisClassMTime;
			if(self::checkClientCache($nMTime)){
				return;
			}
		}
		echo '{';// 向客户端传送语言包内容
		$nLine=0;
		foreach($oThePackage->LANGS as $key=>$sLang){
			if($nLine++){echo ',';}
			echo '"'.$key.'"'.':'.'"'.$sLang.'"';
		}
		echo '}';
	}

	static public function setNewSentence($sSentenceKey,$sSentence,$sLangName,$sPackageName){
		$thePackage=LangPackage::getPackage($sLangName,$sPackageName);
		if(!$thePackage){
			echo(sprintf('can not find lang:%s,package:%s.','LibDyhb',$sLangName,$sPackageName));
			return;
		}
		$thePackage->set($sSentenceKey,$sSentence);
		print '1';
	}

	static public function checkClientCache($nResourceTime,$bSaveCacheOnClient=true){
		if($bSaveCacheOnClient){
			header('Cache-Control: Public');
			header('Last-Modified: '.gmdate("D, d M Y H:i:s",$nResourceTime).' GMT');
		}
		if(function_exists('apache_request_headers')){// 缓存仅在apache中支持以下功能
			$arrheaders=apache_request_headers();
			if(isset($arrheaders['If-Modified-Since'])){
				$nClientCacheTime=strtotime($arrheaders['If-Modified-Since']);// 返回304
				if($nResourceTime<=$nClientCacheTime){
					$_COOKIE=array();
					header('Expires: '.gmdate("D, d M Y H:i:s",time()+3600).' GMT');
					self::sendHttpStatus(304,'Not Modified');
					return true;
				}
			}
		}
		return false;
	}

	static public function sendHttpStatus($nCode,$sMsg){
		if(substr(php_sapi_name(),0,3)=='cgi'){
			header("Status: {$nCode} {$sMsg}",true,$nCode);
		}else{
			header("{$_SERVER['SERVER_PROTOCOL']} {$nCode} {$sMsg}",true,$nCode);
		}
	}

}
