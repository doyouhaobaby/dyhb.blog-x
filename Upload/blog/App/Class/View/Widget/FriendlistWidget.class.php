<?php 
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   好友列表挂件($) */

!defined('DYHB_PATH') && exit;

class FriendlistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'even'=>'friendlist-even',
			'odd'=>'friendlist-odd',
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
