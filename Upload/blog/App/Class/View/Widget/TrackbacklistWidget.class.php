<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   引用列表挂件($) */

!defined('DYHB_PATH') && exit;

class TrackbacklistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before_widget'=>'<div id="trackbacks-box" class="trackbacks-box">',
			'after_widget'=>'</div>',
			'date'=>'Y,F j,g:i A',
			'even'=>'trackbac-item-even',
			'odd'=>'trackbac-item-odd',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		return $this->renderTpl('',$arrData);
	}

	static public function getTrackbackBlog($nBlogId){
		$oBlog=BlogModel::F('blog_id=?',$nBlogId)->query();
		if($oBlog->isError()){
			return false;
		}
		else{
			return $oBlog;
		}
	}

}
