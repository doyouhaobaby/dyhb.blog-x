<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   系统版本文件($) */

!defined('DYHB_PATH') && exit;

/** 定义BLOG版本 */
define('BLOG_SERVER_VERSION','2.0.1');
define('BLOG_SERVER_RELEASE','20120325');

/** 检查系统是否安装过 */
if(!file_exists(DYHB_PATH.'/../Common/Install.lock.php')){
	@unlink(APP_PATH.'/App/~Runtime/Config.php');
	G::urlGoTo(__ROOT__.'/install.php');
}
