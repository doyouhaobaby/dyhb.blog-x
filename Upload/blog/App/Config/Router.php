<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   路由配置文件($) */

!defined('DYHB_PATH') && exit;

return array(
	'Category'=>array('blog/index','value','filter=category'),
	'Archive'=>array('blog/index','value','filter=archive'),
	'Tag'=>array('blog/index','value','filter=tag'),
	'User'=>array('blog/index','value','filter=user'),
	'Taotao'=>array('taotao/show','id'),
	'Upload'=>array('upload/show','id'),
	'Author'=>array('user/show','id'),
);
