<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   wap 基本配置文件($) */

!defined('DYHB_PATH') && exit;

$arrAppConfigs=array(
	'URL_MODEL'=>0,
	'TMPL_ACTION_ERROR'=>'message',
	'TMPL_ACTION_SUCCESS'=>'message',
	'URL_HTML_SUFFIX'=>'.html',
	'DEFAULT_CONTROL'=>'blog',
	'PHP_OFF'=>TRUE,
	'START_GZIP'=>FALSE,
);
return array_merge( $arrAppConfigs,require('Common/Config.inc.php'));
