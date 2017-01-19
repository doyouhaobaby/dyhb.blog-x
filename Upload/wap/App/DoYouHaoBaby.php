<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   公用初始化文件($) */

!defined('DYHB_PATH') && exit;

/** 导入博客版本 */
require('Common/Version.inc.php');

/** 导入后台模型 */
Package::import('admin/App/Class/Model');

/** 导入公用类 */
Package::import('Common/ClassExtend');
