<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   标签列表挂件($) */

!defined('DYHB_PATH') && exit;

class TaglistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before_widget'=>'<div id="tags-box" class="tags-box">',
			'after_widget'=>'</div>',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['show_color'])){
			$arrData['show_color']=Global_Extend::getOption('tag_list_show_color');
		}
		if(!isset($arrData['show_fontsize'])){
			$arrData['show_fontsize']=Global_Extend::getOption('tag_list_show_fontsize');
		}
		return $this->renderTpl('',$arrData);
	}

}
