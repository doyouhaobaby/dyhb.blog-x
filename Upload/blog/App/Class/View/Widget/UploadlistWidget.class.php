<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   附件列表挂件($) */

!defined('DYHB_PATH') && exit;

class UploadlistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array('before_widget'=>'<div id="upload-box" class="upload-box" >','after_widget'=>'</div>','clear'=>'<div class="clearer"></div>','cut_num'=>16);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		return $this->renderTpl('',$arrData);
	}

}
