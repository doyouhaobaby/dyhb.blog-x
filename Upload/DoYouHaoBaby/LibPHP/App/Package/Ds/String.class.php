<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   PHP字符串处理类($) */

!defined('DYHB_PATH') && exit;

class String{

	public static function isUtf8($sString){
		$nLength=strlen($sString);
		for($nI=0;$nI<$nLength;$nI++){
			if(ord($sString[$nI])< 0x80){$nN=0;}
			elseif((ord($sString[$nI])&0xE0)==0xC0){$nN=1;}
			elseif((ord($sString[$nI])&0xF0)==0xE0){$nN=2;}
			elseif((ord($sString[$nI])&0xF0)==0xF0){$nN=3;}
			else{return FALSE;}
			for($nJ=0;$nJ<$nN;$nJ++){
				if((++$nI==$nLength) ||((ord($sString[$nI])&0xC0)!=0x80)){return FALSE;}
			}
		}
		return TRUE;
	}

	static public function subString($sStr,$nStart=0,$nLength=255,$sCharset="utf-8",$bSuffix=true){
		if(function_exists("mb_substr")){// 对系统的字符串函数进行判断
			return mb_substr($sStr,$nStart,$nLength,$sCharset);
		}
		elseif(function_exists('iconv_substr')){
			return iconv_substr($sStr,$nStart,$nLength,$sCharset);
		}
		$arrRe['utf-8']="/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";// 常用几种字符串正则表达式
		$arrRe['gb2312']="/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$arrRe['gbk']="/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$arrRe['big5']="/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($arrRe[$sCharset],$sStr,$arrMatch);// 匹配
		$sSlice=join("",array_slice($arrMatch[0],$nStart,$nLength));
		if($bSuffix){
			return $sSlice."…";
		}
		return $sSlice;
	}

}
