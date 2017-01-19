<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	基本配置文件($)*/

!defined('DYHB_PATH') && exit;

$arrAppConfigs=array(
	//'START_ROUTER'=>TRUE,// 是否开启URL路由
	'URL_MODEL'=>0, // URLMODE,
	//'TEMPLATE_DEBUG'=>TRUE,// 模板引擎调式控制台
	'TMPL_MODULE_ACTION_DEPR'=>'/',
);
return array_merge($arrAppConfigs,require('Common/Config.inc.php'));
