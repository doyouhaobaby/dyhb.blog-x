<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   心情挂件($) */

!defined('DYHB_PATH') && exit;

class ArchivelistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array('before_widget'=>'<div id="archive-box" class="archive-box" >','after_widget'=>'</div>','ul_class'=>'archive-list','archive_group'=>'archive-group','clear'=>'<div class="clearer"></div>');
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		return $this->renderTpl('',$arrData);
	}

}
