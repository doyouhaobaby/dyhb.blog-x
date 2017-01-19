<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Memberavatarcrop 用户头像裁剪挂件($) */

!defined('DYHB_PATH') && exit;

class MemberavatarcropWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before_title'=>'<h2>',// 用户头像裁剪前html 标签
			'after_title'=>'</h2>',// 用户头像裁剪后html 标签
			'before_widget'=>'<div id="memberavatarcrop-box" class="memberavatarcrop-box">',
			'after_widget'=>'</div>',
			'class'=>'button',
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
