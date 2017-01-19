<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   wap 入口文件($) */

define('APP_NAME','wap');
define('APP_PATH',dirname(__FILE__).'/wap');
define('TMPL_STRIP_SPACE',true);
define('RUNTIME_ALLINONE',true);
require('DoYouHaoBaby/DoYouHaoBaby.php');
App::RUN();
