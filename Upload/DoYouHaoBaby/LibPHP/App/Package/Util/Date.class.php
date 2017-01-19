<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   日期处理($) */

!defined('DYHB_PATH') && exit;

class Date{

	static public function smartDate($nDateTemp,$sDateFormat='Y-m-d H:i'){
		$sReturn='';
		$nSec=time()-$nDateTemp;
		$nHover=floor($nSec/3600);
		if($nSec==0){
			$nMin=floor($nSec/60);
			if($nMin==0)
				$sReturn=$nSec.' '.' Seconds before';
			else
				$sReturn=$nMin.' '." Minutes ago";
		}
		elseif($nHover<24){
			$sReturn=sprintf("About %d hours ago",$nHover);
		}
		else{
		  $sReturn=date($sDateFormat,$nDateTemp);
		}
		return $sReturn;
	}

	static public function getTheFirstOfYearOrMonth($nT){
		$arrResult=array();
		$nMouth=substr($nT,4,2);
		$nYear=substr($nT,0,4);
		$nDay=substr($nT,6,2);
		$arrResult[0]=mktime(0,0,0,$nMouth,1,$nYear);
		$arrResult[1]=mktime(0,0,0,$nMouth+1,1,$nYear);
		$arrResult[2]=mktime(0,0,0,1,1,$nYear);
		$arrResult[3]=mktime(0,0,0,1,1,$nYear+1);
		$arrResult[4]=mktime(0,0,0,$nMouth,$nDay,$nYear);
		$arrResult[5]=mktime(0,0,0,$nMouth,$nDay+1,$nYear);
		return $arrResult;
	}

	static public function getTheDataOfNowDay(){
		$nYear=date("Y");
		$nMonth=date("m");
		$nDay=date("d");
		$nDayBegin=mktime(0,0,0,$nMonth,$nDay,$nYear);//当天开始时间戳
		$nDayEnd=mktime(23,59,59,$nMonth,$nDay,$nYear);//当天结束时间戳
		return array($nDayBegin,$nDayEnd);
	}

}
