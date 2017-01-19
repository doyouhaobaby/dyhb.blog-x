<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   初始化基本配置执行程序($)*/

!defined('DYHB_PATH') && exit;

$arrConfig =(array)(include DYHB_PATH.'/LibPHP/Common/DefaultConfig.inc.php');// 读取系统默认配置，并写入默认配置项
if(is_file(APP_PATH.'/App/Config/Config.php')){// 合并数据，项目配置优先于系统惯性配置
	$arrConfig=array_merge($arrConfig,(array)(include APP_PATH.'/App/Config/Config.php'));
	unset($arrAppConfig);
}
if(is_file(APP_PATH.'/App/Config/ExtendConfig.php' )){// 读取扩展配置文件，扩展配置优先于项目配置
	foreach((array)(include APP_PATH.'/App/Config/ExtendConfig.php') as $sVal){
		if(is_file(APP_PATH.'/App/Config/ExtendConfig/'.ucfirst($sVal).'.php')){
			$arrConfig=array_merge($arrConfig,(array)(include APP_PATH.'/App/Config/ExtendConfig/'.ucfirst($sVal).'.php'));
			unset($arrExtendConfig);
		}else{
			E('Extend config file :'.APP_PATH.'/App/Config/ExtendConfig/'.ucwords($sVal).'.php'.' not exists !','#fff');
		}
	}
}
if(!is_dir(APP_PATH.'/App/~Runtime')){
	G::makeDir(APP_PATH.'/App/~Runtime');
}
$oConfig=new Config(APP_PATH.'/App/~Runtime');
foreach($arrConfig as $sK=>$v){
	$oConfig->setItem($sK,$v);
}
unset($oConfig);
unset($arrConfig);
