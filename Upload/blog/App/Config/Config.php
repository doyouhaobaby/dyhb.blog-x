<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   基本配置文件($) */

!defined('DYHB_PATH') && exit;

$arrAppConfigs=array(
	//'START_ROUTER'=>TRUE,// 是否开启URL路由
	//'URL_MODEL'=>0, // URLMODE,
	//'TEMPLATE_DEBUG'=>TRUE,// 模板引擎调式控制台
	'TMPL_ACTION_ERROR'=>'message',
	'TMPL_ACTION_SUCCESS'=>'message',
	'START_ROUTER'=>TRUE,
	'DEFAULT_CONTROL'=>'blog',
	'URL_HTML_SUFFIX'=>'.html',
	'HTML_CACHE_ON'=>false,
	'HTML_CACHE_TIME'=>700,
	'HTML_READ_TYPE'=>0,
	'PHP_OFF'=>TRUE,
	'CACHE_LIFE_TIME'=>-1,
);

return array_merge($arrAppConfigs,require('Common/Config.inc.php'));
