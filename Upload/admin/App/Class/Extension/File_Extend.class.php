<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	文件扩展类($)*/

!defined('DYHB_PATH') && exit;

class File_Extend{

	static function cleanupHeaderComment($sValue){
		return trim(preg_replace("/\s*(?:\*\/|\?>).*/",'',$sValue));
	}

	static public function getFileData($sFile,$arrDefaultHeaders){
		$hFp=fopen($sFile,'r');
		$sFileData=fread($hFp,8192);
		fclose($hFp);
		$arrHeaders=&$arrDefaultHeaders;
		foreach($arrHeaders as $sField=>$sRegex){
			preg_match('/^[ \t\/*#@]*' . preg_quote($sRegex,'/'). ':(.*)$/mi',$sFileData,${$sField});
			if(!empty(${$sField})){
				${$sField}=self::cleanupHeaderComment(${$sField}[1]);
			}
			else{
				${$sField}='';
			}
		}
		return compact(array_keys($arrHeaders));
	}

}
