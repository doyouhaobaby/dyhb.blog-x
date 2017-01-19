<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   注册表单挂件($) */

!defined('DYHB_PATH') && exit;

class RegisterformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div id="signup-wrap">',
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
		$arrData['register']=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/register.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/register.js?v=1.0";
		$arrData['seccode']=(Global_Extend::getOption('seccode')==1&&Global_Extend::getOption('registerseccode'));
		return $this->renderTpl('',$arrData);
	}

}
