<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   用户修改密码挂件($) */

!defined('DYHB_PATH') && exit;

class MemberpasswordWidget extends Widget{


	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div id="password-wrap">',
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
		$arrData['seccode']=(Global_Extend::getOption('seccode')==1&&Global_Extend::getOption('changepasswordseccode'));
		return $this->renderTpl('',$arrData);
	}
}
