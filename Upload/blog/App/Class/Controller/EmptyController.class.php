<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Empty控制器($) */

!defined('DYHB_PATH') && exit;

class EmptyController extends CommonController{

	public function empty_(){
		$arrMap['blog_isshow']=1;
		$arrMap['blog_urlname']=MODULE_NAME;
		$oBlogController=new BlogController();
		$oBlogController->show($arrMap);
	}

}
