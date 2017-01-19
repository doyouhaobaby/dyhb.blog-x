<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   友情衔接挂件($) */

!defined('DYHB_PATH') && exit;

class LinklistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'before_widget'=>'<div id="link-box" class="link-box" >',
			'after_widget'=>'</div>',
			'ul_class'=>'link-list',
			'clear'=>'<div class="clearer"></div>',
			'image_text'=>G::L('图片链接'),
			'text_text'=>G::L('文字链接'),
			'before_header'=>'<div class=\'link-header-url\' id=\'link-header-url\'>',
			'after_header'=>'</div>',
			'before_link_logo_box'=>'<div class=\'link-logo-box\'>',
			'after_link_logo_box'=>'</div>',
			'before_link_text_box'=>'<div class=\'link-text-box\'>',
			'after_link_text_box'=>'</div>',
			'link_header_url_image'=>'link-header-url-image',
			'link_header_url_text'=>'link-header-url-text',
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if(!isset($arrData['ul_image_class'])){$arrData['ul_image_class']=$arrData['ul_class'];}
		if(!isset($arrData['ul_text_class'])){$arrData['ul_text_class']=$arrData['ul_class'];}
		$sImgLink="<a href='".Global_Extend::getOption('blog_url')."' target='_blank' title='".Global_Extend::getOption('blog_name').' | '.Global_Extend::getOption('blog_description')."'>
					<img src='".Global_Extend::getOption('blog_logo')."' alt='".Global_Extend::getOption('blog_name').' | '.Global_Extend::getOption('blog_description')."'>
					</a>";
		$arrData['img_link']=htmlspecialchars($sImgLink);
		$sTextLink="<a href='".Global_Extend::getOption('blog_url')."' target='_blank' title='".Global_Extend::getOption('blog_name').' | '.Global_Extend::getOption('blog_description')."'>
					".Global_Extend::getOption('blog_name')."
					</a>";
		$arrData['text_link']=htmlspecialchars($sTextLink);
		if(!isset($arrData['data'])){
			$arrLinks=Blog_Extend::getWidgetCache('link');
		}
		else{
			$arrLinks=$arrData['data'];
		}
		$arrTextLinks=$arrImgLinks=array();
		foreach($arrLinks as $arrLink){
			if(!empty($arrLink['link_logo'])){
				$arrImgLinks[]=$arrLink;
			}
			else{
				$arrTextLinks[]=$arrLink;
			}
		}
		$arrData['text']=$arrTextLinks;
		$arrData['img']=$arrImgLinks;
		return $this->renderTpl('',$arrData);
	}

}
