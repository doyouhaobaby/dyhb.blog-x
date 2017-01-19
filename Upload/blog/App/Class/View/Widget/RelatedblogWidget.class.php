<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   相关日志挂件($) */

!defined('DYHB_PATH') && exit;

class RelatedblogWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'widget_title'=>1,
			'title'=>1,// 是否显示相关日志标题，1为显示 0为不显示 默认显示
			'before_title'=>'<h2>',// 相关日志名字前html 标签
			'after_title'=>'</h2>',// 相关日志名字后html 标签
			'before_widget'=>'<div class="widget widget_relatedblog">',
			'after_widget'=>'</div>',
			'block_title'=>G::L('相关日志：'),
			'in_rss'=>0,
			'ul_class'=>'',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['relatedblog_type'])){$arrData['relatedblog_type']=Global_Extend::getOption('relatedblog_type');}
		if(!isset($arrData['relatedblog_sort'])){$arrData['relatedblog_sort']=Global_Extend::getOption('relatedblog_sort');}
		if(!isset($arrData['relatedblog_num'])){$arrData['relatedblog_num']=Global_Extend::getOption('relatedblog_num');}
		if(!isset($arrData['relatedblog_inrss'])){$arrData['relatedblog_inrss']=Global_Extend::getOption('relatedblog_inrss');}
		if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=Global_Extend::getOption('relatedblog_title_cutnum');}
		if(empty($arrData['relatedblog_num'])){$arrData['relatedblog_num']=5;}
		$arrBlog=&$arrData['blog'];
		if(empty($arrBlog['blog_id'])){
			return G::L('错误的相关日志日志数据参数，请在widget中的第二个参数传入array(\'blog\'=>$日志数组或者对象)');
		}
		if($arrData['in_rss']==1 && $arrData['relatedblog_inrss']==0){
			return '';
		}
		$nBlogId=$arrBlog['blog_id'];
		$nCategoryId=$arrBlog['category_id'];
		$arrMap=array();
		$arrMap['blog_ispage']=0;
		$arrMap['blog_isshow']=1;
		if($arrData['relatedblog_type']=='tag'){
			$arrRelatedBlogId=array();
			$oTag=new TagModel();
			$arrTags=$oTag->getTagsByBlogId($nBlogId);
			foreach($arrTags as $nKey=>$arrVal){
				$arrRelatedBlogId[]=$oTag->getBlogIdStrByTagId($arrVal['tag_id']);
			}
			$arrMap['blog_id']=array(array('neq',$nBlogId),array('exp','in('.implode(',',$arrRelatedBlogId).')'),'and');
		}
		else{
			$arrMap['blog_id']=array('neq',$nBlogId);
			$arrMap['category_id']=$nCategoryId;
		}

		switch($arrData['relatedblog_sort']){
			case 'views_desc':{
				$sOrderSql="blog_viewnum DESC";
				break;
			}
			case 'views_asc':{
				$sOrderSql="blog_viewnum ASC";
				break;
			}
			case 'comnum_desc':{
				$sOrderSql="blog_commentnum DESC";
				break;
			}
			case 'comnum_asc':{
				$sOrderSql="blog_commentnum ASC";
				break;
			}
			case 'rand':{
				$sOrderSql="rand()";
				break;
			}
		}
		$arrBlogs=BlogModel::F()->where($arrMap)->order($sOrderSql)->all()->limit(0,$arrData['relatedblog_num'])->query();
		$arrData['data']=&$arrBlogs;
		return $this->renderTpl('',$arrData);
	}

}
