<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   用户列表挂件($) */

!defined('DYHB_PATH') && exit;

class UserlistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array('before_widget'=>'<div id="users-box" class="users-box">','after_widget'=>'</div>','date'=>'Y-m-d H:i:s','even'=>'even','odd'=>'odd','img_dir'=>1,'thead_class'=>'','table_class'=>'datalist-table full');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$arrData['img_path']=$arrData['img_dir']==1?IMG_DIR:STYLE_IMG_DIR;
		return $this->renderTpl('',$arrData);
	}

}
