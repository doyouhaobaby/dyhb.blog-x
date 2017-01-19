<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   日志记录控制器($) */

!defined('DYHB_PATH') && exit;

class RecordController extends CommonController{

	public function index(){
		define('IS_RECORD',TRUE);
		define('CURSCRIPT','record');
		$arrBlogLists=BlogModel::F('blog_isshow=? AND blog_ispage=?',1,0)->setColumns('blog_dateline')->order('blog_dateline DESC')->all()->query();
		$arrTemp=$arrBlogDate=array();
		foreach($arrBlogLists as $oBlogList){
			$arrBlogDate[]=date('Y-m',$oBlogList->blog_dateline);
		}
		$arrTemp=array_count_values($arrBlogDate);
		unset($arrBlogDate);
		foreach($arrTemp as $sKey=>$nVal){
			list($nY,$nM)=explode('-',$sKey);
			$arrBlogDate[$nY][$nM]=$nVal;
		}
		$this->assign('arrBlogRecords',$arrBlogDate);
		$this->assign('the_record_description',Global_Extend::getOption('the_record_description'));
		$this->display('record');
	}

}
