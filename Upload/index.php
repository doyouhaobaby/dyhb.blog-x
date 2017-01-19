<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   blog 入口文件($) */

define('APP_NAME','blog');
define('APP_PATH',dirname(__FILE__).'/blog');
define('TMPL_STRIP_SPACE',true);
define('RUNTIME_ALLINONE',true);
require('DoYouHaoBaby/DoYouHaoBaby.php');
App::RUN();
