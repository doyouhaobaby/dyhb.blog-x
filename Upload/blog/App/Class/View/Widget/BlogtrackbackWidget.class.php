<?php 
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   日志引用挂件($) */

!defined('DYHB_PATH') && exit;

class BlogtrackbackWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array('widget_title'=>1,'title'=>1,'before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_blogtrackback">','after_widget'=>'</div>','block_title'=>G::L('日志引用：'),'in_rss'=>0,'ul_class'=>'');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['blogtrackback_sort'])){$arrData['blogtrackback_sort']=Global_Extend::getOption('blogtrackback_sort');}
		if(!isset($arrData['blogtrackback_num'])){$arrData['blogtrackback_num']=Global_Extend::getOption('blogtrackback_num');}
		if(!isset($arrData['blogtrackback_inrss'])){$arrData['blogtrackback_inrss']=Global_Extend::getOption('blogtrackback_inrss');}
		if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=Global_Extend::getOption('blogtrackback_title_cutnum');}
		if(!isset($arrData['blogtrackback_nofollow'])){$arrData['blogtrackback_nofollow']=Global_Extend::getOption('blogtrackback_nofollow');}
		if(!isset($arrData['blogtrackback_dateline'])){$arrData['blogtrackback_dateline']=Global_Extend::getOption('blogtrackback_dateline');}
		if(!isset($arrData['blogtrackback_dateline_type'])){$arrData['blogtrackback_dateline_type']=Global_Extend::getOption('blogtrackback_dateline_type');}
		$arrBlog=&$arrData['blog'];
		if(is_object($arrBlog)){
			$arrBlog=$arrBlog->toArray();
		}
		if(empty($arrBlog['blog_id'])){
			return G::L('错误的相关日志日志数据参数，请在widget中的第二个参数传入array(\'blog\'=>$日志数组或者对象)');
		}
		if($arrData['in_rss']==1 && $arrData['blogtrackback_inrss']==0){
			return '';
		}
		$nBlogId=$arrBlog['blog_id'];
		switch($arrData['blogtrackback_sort']){
			case 'dateline_desc':{
				$sOrderSql="create_dateline DESC";
				break;
			}
			case 'dateline_asc':{
				$sOrderSql="create_dateline ASC";
				break;
			}
		}
		if(!empty($arrData['blogtrackback_num'])){
			$arrTrackbacks=TrackbackModel::F('blog_id=?AND trackback_status=1 ',$nBlogId)->order($sOrderSql)->all()->limit(0,$arrData['blogtrackback_num'])->query();
		}
		else{
			$arrTrackbacks=TrackbackModel::F('blog_id=?AND trackback_status=1',$nBlogId)->order($sOrderSql)->all()->query();
		}
		if(!is_array($arrTrackbacks)){
			$arrTrackbacks=array();
		}
		$arrData['data']=&$arrTrackbacks;
		return $this->renderTpl('',$arrData);
	}

}
