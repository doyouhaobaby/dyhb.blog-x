<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   相册归档控制器($) */

!defined('DYHB_PATH') && exit;

class UploadcategoryController extends CommonController{

	public function init__(){
		parent::init__();
		if(Global_Extend::getOption('only_login_can_view_upload')==1){
			$this->E(G::L('这个文件只能在登入之后下载。'));
		}
	}

	public function index(){
		define('IS_UPLOAPCATEGOYR',TRUE);
		define('CURSCRIPT','uploadcategory');
		$nUploadcateogory=UploadcategoryModel::F()->all()->getCounts();
		$nEverynum=Global_Extend::getOption('display_blog_ucategory_list_num');
		$oPage=Page::RUN($nUploadcateogory,$nEverynum,G::getGpc('page','G'));
		$sPageNavbar=$oPage->P('pagination');
		$arrUploadcateogoryList=UploadcategoryModel::F()->all()->order('`uploadcategory_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('nUploadcateogory',$nUploadcateogory);
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrUploadcateogoryLists',$arrUploadcateogoryList);
		$this->assign('the_uploadcateogory_description',Global_Extend::getOption('the_uploadcateogory_description'));
		$this->display('uploadcategory');
	}

	static public function get_the_uploadphoto_cover($arrUploadCategory){
		if(empty($arrUploadCategory['uploadcategory_cover'])){
			$sCoverPhoto=(file_exists(TEMPLATE_PATH.'/Public/Images/photosort.gif')?STYLE_IMG_DIR.'/photosort.gif':IMG_DIR.'/photosort.gif');
		}
		else{
			if(!preg_match("/[^\d-.,]/",$arrUploadCategory['uploadcategory_cover'])){
				$oUpload=UploadModel::F('upload_id=?',$arrUploadCategory['uploadcategory_cover'])->query();
				if(!empty($oUpload['upload_id'])){
					if(Global_Extend::getOption('is_hide_upload_really_path')==1){
						return in_array($oUpload['upload_extension'],array('gif','jpg','jpeg','bmp','png')) && $oUpload['upload_isthumb']?
							G::U('attachment/index?id='.Global_Extend::aidencode($oUpload['upload_id']).'&thumb=1'):
							G::U('attachment/index?id='.Global_Extend::aidencode($oUpload['upload_id']));
					}else{
						return in_array($oUpload['upload_extension'],array('gif','jpg','jpeg','bmp','png')) && $oUpload['upload_isthumb']?
								__PUBLIC__.'/Upload/'.$oUpload['upload_thumbpath'].'/'.$oUpload['upload_thumbprefix'].$oUpload['upload_savename']:
								__PUBLIC__.'/Upload/'.$oUpload['upload_savepath'].'/'.$oUpload['upload_savename'];
					}
				}
				else{
					return (file_exists(TEMPLATE_PATH.'/Public/Images/photosort.gif')?STYLE_IMG_DIR.'/photosort.gif':IMG_DIR.'/photosort.gif');
				}
			}
			else{
				return $arrUploadCategory['uploadcategory_cover'];
			}
		}
	}

}
