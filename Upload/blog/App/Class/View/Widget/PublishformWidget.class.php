<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   投稿表单挂件($) */

!defined('DYHB_PATH') && exit;

class PublishformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div id="publish-wrap">',
			'after'=>'</div>',
			'before_table'=>'<div id="formwrapbox">',
			'after_table'=>'</div>',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['publish']=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/publish.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/publish.js?v=1.0";
		$arrData['cid']=G::getGpc('cid','G');
		$arrData['seccode']=(Global_Extend::getOption('seccode')==1&&Global_Extend::getOption('publishseccode'));
		return $this->renderTpl('',$arrData);
	}

}
