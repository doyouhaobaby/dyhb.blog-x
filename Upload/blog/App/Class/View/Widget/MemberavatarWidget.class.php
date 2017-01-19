<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Memberavatar 用户头像挂件($) */

!defined('DYHB_PATH') && exit;

class MemberavatarWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before_title'=>'<h2>',// 用户头像前html 标签
			'after_title'=>'</h2>',// 用户头像后html 标签
			'before_widget'=>'<div id="memberavatar-box" class="memberavatar-box">',
			'after_widget'=>'</div>',
			'a_button'=>'a-button',	
			'class'=>'field',
			'size'=>30,
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
