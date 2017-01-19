<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   install 入口文件(安装程序)($) */

define('APP_NAME','install');
define('APP_PATH',dirname(__FILE__).'/install');
define('TMPL_STRIP_SPACE',true);
define('RUNTIME_ALLINONE',true);
require('DoYouHaoBaby/DoYouHaoBaby.php');
App::RUN();
