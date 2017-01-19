<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   install 初始化文件($)*/

!defined('DYHB_PATH')&& exit;

function gdVersion(){
	if(!function_exists('phpinfo')){
		if(function_exists('imagecreate'))return '2.0';
		else return 0;
	}
	else{
		ob_start();
		phpinfo(8);
		$arrModuleInfo=ob_get_contents();
		ob_end_clean();
		if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i",$arrModuleInfo,$arrMatches))
			$nGdversion=$arrMatches[1];
		else 
			$nGdversion=0; 
		return $nGdversion;
	}
}

function testWrite($sPath){
	$sFile='Dyhb.test.txt';
	$sPath=preg_replace("#\/$#",'',$sPath);
	$hFp=@fopen($sPath.'/'.$sFile,'w');
	if(!$hFp)
		return false;
	else{
		fclose($hFp);
		$bRs=@unlink($sPath.'/'.$sFile);
		if($bRs)return true;
		else return false;
	}
}
