<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   标签控制器($) */

!defined('DYHB_PATH') && exit;

class TagController extends CommonController{

	public function index(){
		define('IS_TAGLIST',TRUE);
		define('CURSCRIPT','taglist');
		$nTotalRecord=TagModel::F()->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_tag_list_num');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrTagList=TagModel::F()->all()->order('`tag_id` DESC')->asArray()->limit($oPage->returnPageStart(),$nEverynum)->query();
		if(!isset($arrData['tag_color'])){
			$arrColor=unserialize(Global_Extend::getOption('widget_hottag_color'));
		}
		foreach($arrTagList as $nTagKey=>$arrTag){
			$arrTagList[$nTagKey]['color']=$arrColor[rand(0,count($arrColor))%count($arrColor)];
			if(empty($arrTagList[$nTagKey]['color'])){
				$arrTagList[$nTagKey]['color']='#000000';
			}
			$arrTagList[$nTagKey]['fontsize']=12 + $arrTag['tag_usenum'] / 2;
		}
		$this->assign('nTotalTag',$nTotalRecord);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTagLists',$arrTagList);
		$this->assign('the_tag_description',Global_Extend::getOption('the_tag_description'));
		$this->display('tag');
	}

}
