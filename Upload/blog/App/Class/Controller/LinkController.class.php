<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   衔接控制器($) */

!defined('DYHB_PATH') && exit;

class LinkController extends CommonController{

	public function index(){
		define('IS_LINK',TRUE);
		define('CURSCRIPT','link');
		$this->assign('the_link_description',Global_Extend::getOption('the_link_description'));
		$this->display('link');
	}

}
