<?php
/**

 //  [Websetup!] 图像界面工具
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | websetup 入口文件
 //  +---------------------------------------------------------------------

*/

define('APP_NAME','websetup'); //项目名字
define('APP_PATH',dirname(dirname(__FILE__)).'/Extension/websetup' );  //项目路径
define('RUNTIME_ALLINONE',true); 

/** 加载框架 */
require('../DoYouHaoBaby.php');

/** 实例化框架并且初始化 */
App::RUN();
