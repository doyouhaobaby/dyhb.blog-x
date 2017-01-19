<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   静态配置文件($) */

!defined('DYHB_PATH') && exit;

return array(
	'*'=>array('{$_SERVER.REQUEST_URI|md5}'),
);
