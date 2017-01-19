<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	日志分类模型($)*/

!defined('DYHB_PATH') && exit;

class CategoryModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'category',
			'props'=>array(
				'category_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'category_id',
			'check'=>array(
				'category_compositor'=>array(
					array('number',G::L('排序名只能是数字')),
				),
				'category_name'=>array(
					array('require',G::L('分类名不能为空')),
				),
				'category_urlname'=>array(
					array('empty'),
					array('number_underline_english',G::L('URL 别名只能是字母，数字和下划线')),
					array('max_length',50,G::L('URL 别名最大长度为50')),
					array('urlName',G::L('分类别名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
				'category_gotourl'=>array(
					array('empty'),
					array('url',G::L('分类外部衔接必须为正确的URL 格式')),
				),
				'category_parentid'=>array(
					array('categoryParentId',G::L('分类不能为自己'),'condition'=>'must','extend'=>'callback'),
				),
				'category_template'=>array(
					array('empty'),
					array('number_underline_english',G::L('分类主题只能是字母，数字和下划线')),
					array('max_length',20,G::L('分类主题最大长度为20')),
				),
			),
		);
	}

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

	public function categoryParentId(){
		$nCategoryId=G::getGpc('id');
		$nCategoryParentid=G::getGpc('category_parentid');
		if(($nCategoryId==$nCategoryParentid)
				and !empty($nCategoryId)
				and !empty($nCategoryParentid)){
			return false;
		}
		return true;
	}

	public function urlName(){
		$nId=G::getGpc('id','P');
		$sCategoryUrlName=G::getGpc('category_urlname','P');
		$sCategoryInfo='';
		if($nId){
			$arrCategory=self::F('category_id=?',$nId)->asArray()->getOne();
			$sCategoryInfo=trim($arrCategory['category_urlname']);
		}
		if($sCategoryUrlName !=$sCategoryInfo){
			$arrResult=self::F()->getBycategory_urlname($sCategoryUrlName)->toArray();
			if(!empty($arrResult['category_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

	static public function getCategoryData($arrCategory){
		if(is_int($arrCategory)){
			$arrCategory=CategoryModel::F('category_id=?',$arrCategory)->query();
		}
		$nBlogs=isset($nBlogs)?$nBlogs+$arrCategory['category_blogs']:$arrCategory['category_blogs'];
		$nComments=isset($nComments)?$nComments+$arrCategory['category_comments']:$arrCategory['category_comments'];
		$nTodaycomments=isset($nTodaycomments)?$nTodaycomments+$arrCategory['category_todaycomments']:$arrCategory['category_todaycomments'];
		$arrChilds=CategoryModel::F('category_parentid=?',$arrCategory['category_id'])->all()->asArray()->query();
		if(empty($arrChilds[0]['category_id'])){
			return array('blogs'=>$nBlogs,'comments'=>$nComments,'todaycomments'=>$nTodaycomments);
		}
		else{
			foreach($arrChilds as $arrChild){
				$arrResult=self::getCategoryData($arrChild);
				$nBlogs+=$arrResult['blogs'];
				$nComments+=$arrResult['comments'];
				$nTodaycomments+=$arrResult['todaycomments'];
			}
			return array('blogs'=>$nBlogs,'comments'=>$nComments,'todaycomments'=>$nTodaycomments);
		}
	}

}
