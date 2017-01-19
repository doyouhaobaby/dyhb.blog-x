<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   登录表单挂件($) */

!defined('DYHB_PATH') && exit;

class LoginformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div id="login-wrap">',
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
		return $this->renderTpl('',$arrData);
	}

}
