<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   日志引用地址挂件($) */

!defined('DYHB_PATH') && exit;

class TrackbackurlWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'widget_title'=>1,
			'title'=>1,// 是否显示日志引用标题，1为显示 0为不显示 默认显示
			'before_title'=>'<h2>',// 日志引用名字前html 标签
			'after_title'=>'</h2>',// 日志引用名字后html 标签
			'before_widget'=>'<div class="widget widget_trackbackurl">',
			'after_widget'=>'</div>',
			'block_title'=>G::L('引用地址：'),
			'in_rss'=>0,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['blogtrackback_inrss'])){
			$arrData['blogtrackback_inrss']=Global_Extend::getOption('blogtrackback_inrss');
		}
		if($arrData['in_rss']==1 && $arrData['blogtrackback_inrss']==0){
			return '';
		}
		return $this->renderTpl('',$arrData);
	}

}
