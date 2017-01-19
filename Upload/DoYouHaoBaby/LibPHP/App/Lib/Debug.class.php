<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   调式类($) */

!defined('DYHB_PATH') && exit;

class Debug{

	static private $_arrDebug= array();

	static public function mark($sName){
		self::$_arrDebug['time'][$sName]=microtime(TRUE);
		if(MEMORY_LIMIT_ON){
			self::$_arrDebug['mem'][$sName]=memory_get_usage();
			self::$_arrDebug['peak'][$sName]=function_exists('memory_get_peak_usage')?memory_get_peak_usage():self::$_arrDebug['mem'][$sName];
		}
	}

	static public function useTime($sStart,$sEnd,$nDecimals=6){
		if(!isset(self::$_arrDebug['time'][$sStart])){return '';}
		if(!isset(self::$_arrDebug['time'][$sEnd])){self::$_arrDebug['time'][$sEnd]=microtime(TRUE);}
		return number_format(self::$_arrDebug['time'][$sEnd]-self::$_arrDebug['time'][$sStart],$nDecimals);
	}

	static public function useMemory($sStart,$sEnd){
		if(!MEMORY_LIMIT_ON){return '';}
		if(!isset(self::$_arrDebug['mem'][$sStart])){return '';}
		if(!isset(self::$_arrDebug['mem'][$sEnd])){self::$_arrDebug['mem'][$sEnd]=memory_get_usage();}
		return number_format((self::$_arrDebug['mem'][$sEnd]-self::$_arrDebug['mem'][$sStart])/1024);
	}

	static function getMemPeak($sStart,$sEnd){
		if(!MEMORY_LIMIT_ON){return '';}
		if(!isset(self::$_arrDebug['peak'][$sStart])){return '';}
		if(!isset(self::$_arrDebug['peak'][$sEnd])){self::$_arrDebug['peak'][$sEnd]=function_exists('memory_get_peak_usage')?memory_get_peak_usage():memory_get_usage();}
		return number_format(max(self::$_arrDebug['peak'][$sStart],self::$_arrDebug['peak'][$sEnd])/1024);
	}

	static function debugStart($sLabel=''){
		$GLOBALS[$sLabel]['_beginTime_']=microtime(TRUE);
		if(MEMORY_LIMIT_ON){$GLOBALS[$sLabel]['_beginMem_']=memory_get_usage();}
	}

	static function debugEnd($sLabel=''){
		$GLOBALS[$sLabel]['_endTime_']=microtime(TRUE);
		$sMessage='<div style="text-align:center;width:100%">Process '.$sLabel.': Times '.number_format($GLOBALS[$sLabel]['_endTime_']-$GLOBALS[$sLabel]['_beginTime_'],6).'s ';
		if(MEMORY_LIMIT_ON){
			$GLOBALS[$sLabel]['_endMem_']=memory_get_usage();
			$sMessage.=' Memories '.number_format(($GLOBALS[$sLabel]['_endMem_']-$GLOBALS[$sLabel]['_beginMem_'])/1024).' k';
		}
		$sMessage.='</div>';
		echo $sMessage;
	}

}
