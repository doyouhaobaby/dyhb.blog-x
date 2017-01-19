<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Flashimage控制器($) */

!defined('DYHB_PATH') && exit;

class FlashimageController extends CommonController{

	public function index(){
		$nCategoryId=G::getGpc('category_id');
		$arrMap['string_']='FIND_IN_SET(\'f\',blog_type)>0';
		$arrMap['blog_isshow']=1;
		if(!empty($nCategoryId)){
			$arrMap['category_id']=$nCategoryId;
		}
		$arrFlashimageDatas=BlogModel::F()->where($arrMap)->asArray()->all()->order('`blog_dateline` DESC')->limit(0,Global_Extend::getOption('widget_flashimage_display_num')?Global_Extend::getOption('widget_flashimage_display_num'):5	)->query();
		$arrXmlData=array();
		foreach($arrFlashimageDatas as $nKey=>$arrFlashimageData){
			$arrXmlData[$nKey]['image']=Blog_Extend::getThumbImage($arrFlashimageData);
			$arrXmlData[$nKey]['url']=PageType_Extend::getBlogUrl($arrFlashimageData);
			$arrXmlData[$nKey]['title']=$arrFlashimageData['blog_title'];
		}
		$nAutoPlayTime=Global_Extend::getOption('widget_flashimage_autoplaytime')? Global_Extend::getOption('widget_flashimage_autoplaytime'):3;
		$oImageView=new ImageView($arrXmlData,$nAutoPlayTime,true);
		$oImageView->RUN();
	}

}
