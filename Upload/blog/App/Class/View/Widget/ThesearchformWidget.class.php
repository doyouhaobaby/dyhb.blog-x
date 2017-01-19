<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   搜索表单挂件($) */

!defined('DYHB_PATH') && exit;

class ThesearchformWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'blog_name'=>G::L('文章'),
			'comment_name'=>G::L('评论'),
			'button_class'=>'button',
			'input_class'=>'field',
			'hr_line_class'=>'shadowline',
			'form_class'=>'searchform',
			'search_label_class'=>'searchlabel',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$sType=G::getGpc('type','G');
		$arrData['search_type']=$sType;
		$arrCategorys=Blog_Extend::getWidgetCache('category');
		$oCategoryTree=new TreeCategory();
		foreach($arrCategorys as $arrCategory){
			$oCategoryTree->setNode($arrCategory['category_id'],$arrCategory['category_parentid'],$arrCategory['category_name']);
		}
		$arrData['oCategoryTree']=$oCategoryTree;
		return $this->renderTpl('',$arrData);
	}

}
