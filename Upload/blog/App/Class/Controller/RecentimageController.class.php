<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Recentimage控制器($) */

!defined('DYHB_PATH') && exit;

class RecentimageController extends CommonController{

	public function index(){
		$arrRecentimageData=Model::C('widget_recentimage');
		if(empty($arrRecentimageData)){
			Cache_Extend::front_widget_recentimage();
			$arrRecentimageData=Model::C('widget_recentimage');
		}
		$arrXmlData=array();
		foreach($arrRecentimageData as $nKey=>$arrRecentImage){
			$sImgTargetSrc=__PUBLIC__.'/Upload/'.$arrRecentImage['upload_savepath'].'/'.$arrRecentImage['upload_savename'];
			$sImgSrc=$arrRecentImage['upload_isthumb']?__PUBLIC__.'/Upload/'.$arrRecentImage['upload_thumbpath'].'/'.$arrRecentImage['upload_thumbprefix'].$arrRecentImage['upload_savename']:$sImgTargetSrc;
			$arrXmlData[$nKey]['image']=$sImgSrc;
			$arrXmlData[$nKey]['url']=PageType_Extend::getUploadUrl($arrRecentImage);
			$arrXmlData[$nKey]['title']=$arrRecentImage['upload_name'];
		}
		$nAutoPlayTime=Global_Extend::getOption('widget_recentimage_autoplaytime')? Global_Extend::getOption('widget_recentimage_autoplaytime'):3;
		$oImageView=new ImageView($arrXmlData,$nAutoPlayTime,true);
		$oImageView->RUN();
	}

}
