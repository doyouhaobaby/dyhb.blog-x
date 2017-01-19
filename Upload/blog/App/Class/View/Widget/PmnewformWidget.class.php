<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   发送短消息挂件($) */

!defined('DYHB_PATH') && exit;

class PmnewformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div class="pmsend-float" id="pmsend-float">',
			'after'=>'</div>',
			'before_table'=>'<div id="formwrapbox">',
			'after_table'=>'</div>',
			'style'=>'width: 450px; height: 160px;',
			'hide'=>0,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['seccode']=(Global_Extend::getOption('seccode')==1 && Global_Extend::getOption('sendpmseccode'));
		return $this->renderTpl('',$arrData);
	}

}
