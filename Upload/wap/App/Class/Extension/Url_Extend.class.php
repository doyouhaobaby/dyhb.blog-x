<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	blog URL创建函数($) */

!defined('DYHB_PATH') && exit;

class Url_Extend{

	static public function getBlogUrl($arrBlog,$arrExtend=array()){
		if(!empty($arrBlog['blog_gotourl'])){
			return $arrBlog['blog_gotourl'];
		}
		return G::U('blog/show?id='.$arrBlog['blog_id'],$arrExtend);
	}

	static public function getTaotaoUrl($arrTaotao,$arrExtend=array()){
		return G::U('blog/singletaotao?id='.$arrTaotao['taotao_id'],$arrExtend);
	}

	static public function getCategoryUrl($arrCategory){
		return G::U('blog/index?cid='.$arrCategory['category_id']);
	}

	static public function getTagUrl($arrTag){
		return G::U('blog/index?tag='.$arrTag['tag_id']);
	}

	static public function getUploadUrl($arrUpload,$arrExtend=array()){
		return G::U('blog/singleupload?id='.$arrUpload['upload_id'],$arrExtend);
	}

	static public function getArchiveUrl($nTime){
		$nTime=intval($nTime);
		return G::U('blog/index?record='.$nTime);
	}

	static public function getUserUrl($arrBlog){
		return G::U('blog/index?uid='.$arrBlog['user_id']);
	}

}
