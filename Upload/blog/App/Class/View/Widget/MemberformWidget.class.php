<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   个人中心表单挂件($) */

!defined('DYHB_PATH') && exit;

class MemberformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before'=>'<div id="member-wrap">',
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
		$arrLoginInfo=UserModel::F('user_id=?',$arrData['data']['user_id'])->query();
		$arrData['user']=&$arrLoginInfo;
		$arrData['member']=Global_Extend::getOption('javascript_dir')=='default'?__PUBLIC__."/Images/Js/member.js?v=1.0":__ROOT__."/blog/App/~Runtime/Data/Javascript/member.js?v=1.0";
		$arrData['seccode']=(Global_Extend::getOption('seccode')==1&&Global_Extend::getOption('changeinfoseccode'));
		return $this->renderTpl('',$arrData);
	}

}
