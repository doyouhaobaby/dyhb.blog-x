<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   论坛友情衔接挂件($) */

!defined('DYHB_PATH') && exit;

class ForumlinkWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'collapsed_image'=>'collapsed_no.gif',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['data'])){
			$arrLinks=Blog_Extend::getWidgetCache('link');
		}
		else{
			$arrLinks=$arrData['data'];
		}
		$sTightlinkContent=$sTightlinkText=$sTightlinkLogo='';
		foreach($arrLinks as $arrLink){
			if($arrLink['link_description']){
				if($arrLink['link_logo']){
					$sTightlinkContent.='<li><div class="forumlogo"><img src="'.$arrLink['link_logo'].'" border="0" alt="'.$arrLink['link_name'].'" /></div><div class="forumcontent"><h5><a href="'.$arrLink['link_url'].'" target="_blank">'.$arrLink['link_name'].'</a></h5><p>'.$arrLink['link_description'].'</p></div>';
				}
				else{
					$sTightlinkContent.='<li><div class="forumcontent"><h5><a href="'.$arrLink['link_url'].'" target="_blank">'.$arrLink['link_name'].'</a></h5><p>'.$arrLink['link_description'].'</p></div>';
				}
			}
			else{
				if($arrLink['logo']){
					$sTightlinkLogo.='<a href="'.$arrLink['link_url'].'" target="_blank"><img src="'.$arrLink['link_logo'].'" border="0" alt="'.$arrLink['link_name'].'" /></a> ';
				}
				else{
					$sTightlinkText.='<li><a href="'.$arrLink['link_url'].'" target="_blank" title="'.$arrLink['link_name'].'">'.$arrLink['link_name'].'</a></li>';
				}
			}
		}
		$arrData['link_content']=$sTightlinkContent;
		$arrData['link_text']=$sTightlinkText;
		$arrData['link_logo']=$sTightlinkLogo;
		return $this->renderTpl('',$arrData);
	}

}
