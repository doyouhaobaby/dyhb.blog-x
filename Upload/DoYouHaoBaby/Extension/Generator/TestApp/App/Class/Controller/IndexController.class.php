<?php
/**

 //  [DoYouHaoBaby!] Init APP - %APP_NAME%
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2099 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | %APP_NAME% Index控制器
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

class IndexController extends Controller{

	public function index(){
		Template::in(true);// 演示缓存保持到系统中
		$this->display(DYHB_PATH.'/Resource/Template/AutoIndex/index_hello.html');
		Template::in(false);// 还原状态
	}

	public function check_probe(){
		include( DYHB_PATH.'/Resource/Template/AutoIndex/Probe.inc.php' );// 载入探针文件
		return;
	}

}
