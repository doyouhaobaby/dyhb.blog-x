<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   短消息列表挂件($) */

!defined('DYHB_PATH') && exit;

class PmlistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'even'=>'pmlist-even',
			'odd'=>'pmlist-odd',
			'img_dir'=>1,//1表示附属主题，否则为样式主题
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
