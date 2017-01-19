<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	项目初始化文件($)*/

!defined('DYHB_PATH') && exit;

/** 导入博客版本 */
require('Common/Version.inc.php');

/** 导入公用类 */
Package::import('Common/ClassExtend');

function numBitunit($nNum){
	$arrBitunit=array(' B',' KB',' MB',' GB');
	$nCount=count($arrBitunit);
	for($nKey=0;$nKey<$nCount;$nKey++){
		if($nNum>=pow(2,10*$nKey)-1)// 1024B 会显示为 1KB
		{
			$sNumBitunitStr=(ceil($nNum/pow(2,10*$nKey)*100)/100)." {$arrBitunit[$nKey]}";
		}
	}
	return $sNumBitunitStr;
}
