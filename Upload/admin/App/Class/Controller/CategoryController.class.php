<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	分类控制器($)*/

!defined('DYHB_PATH') && exit;

class CategoryController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['category_name']=array('like',"%".G::getGpc('category_name')."%");
	}

	public function bEdit_(){
		$this->bAdd_();
	}

	public function bAdd_(){
		$oCategoryTree=new TreeCategory();
		foreach($this->getBlogCategory()as $oCategory){
			$oCategoryTree->setNode($oCategory->category_id,$oCategory->category_parentid,$oCategory->category_name);
		}
		$this->assign('oCategoryTree',$oCategoryTree);
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('您可以使用分类目录来给站点分区、按照主题组织相关的文章。默认的目录叫做“未分类”。','category').'</p>'.
				'<p>'.G::L('分类目录和标签的区别是什么呢？通常，标签是临时安排的一些关键词，用来标记文章中的关键信息（名字，题目等），也许其它文章也会拥有这个标签。而分类则是事先决定了的。若将您的站点比做一本书，那么分类目录就是书的目录，标签则是书前所列出的术语。','category').'</p>'.
				'<p>'.G::L('当您创建一个新分类目录时，您须填写下列栏目：','category').'</p><ul>'.
				'<li><strong>'.G::L('分类名').'</strong> -'.G::L('分类在网站上的显示名称。','category').'</li>'.
				'<li><strong>'.G::L('分类别名').'</strong> -'.G::L('“别名“是 URL 友好的另外一个叫法。它通常为小写并且只能包含字母，数字和连字符。','category').'</li>'.
				'<li><strong>'.G::L('父级').'</strong> -'.G::L('分类目录，和标签不同，它可以有层级关系。您可以有一个音乐的分类目录，在这个目录下面您可以创建流行或者古典的子目录。完全自由。','category').'</li>'.
				'<li><strong>'.G::L('描述').'</strong> -'.G::L('“描述”不一定会被显示，但有的主题会显示它。','category').'</li>'.
				'<li><strong>'.G::L('SEO 描述').'</strong> -'.G::L('提供分类的SEO 描述，方便搜索引擎的搜录。','category').'</li>'.
				'<li><strong>'.G::L('SEO 关键字').'</strong> -'.G::L('提供分类的关键字，可以提高搜索引擎的搜录率。','category').'</li></ul>'.
				'<p>'.G::L('分类是一个非常重要的信息，请注意多多关注。','category').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _tree_get_admin_help_description(){
		return $this->_index_get_admin_help_description();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('你可以对分类进行批量操作，批量操作可以大大提高操作效率。','category').'</p>'.
				'<p>'.G::L('你可以进行如下的操作：','category').'</p><ul>'.
				'<li><strong>'.G::L('删除').'</strong> -'.G::L('你可以选中分类数据，然后点击上下的按钮删除。同时，也可以划过每条数据的“操作条”进行操作。','category').'</li>'.
				'<li><strong>'.G::L('排序浏览').'</strong> -'.G::L('你可以点击表格的头部排序数据浏览。','category').'</li>'.
				'<p>'.G::L('分类是一个非常重要的信息，请注意多多关注。','category').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		$arrIds=explode(',',$sId);
		foreach($arrIds  as $nId){
			$nCategorys=CategoryModel::F('category_parentid=?',$nId)->all()->getCounts();
			$oCategory=CategoryModel::F('category_id=?',$nId)->query();
			if($nCategorys>0){
				$this->E(G::L("分类%s存在子分类，你无法删除。",'app',null,$oCategory->category_name));
			}
			$oBlog=new BlogModel();
			$oBlog->getDb()->query("update `". $oBlog->getTablePrefix()."blog` set `category_id`=-1 where `category_id`={$nId}");
		}
	}

	public function getBlogCategory(){
		return CategoryModel::F()->order('category_id ASC,category_compositor DESC')->all()->query();
	}

	public function blog_ajax_insert(){
		$nCategoryCompositor=intval(G::getGpc('category_compositor','G'));
		$nCategoryParentid=intval(G::getGpc('category_parentid','G'));
		$sCategoryName=G::getGpc('category_name','G');
		$sCategoryUrlname=G::getGpc('category_urlname','G');
		$sCategoryLogo=G::getGpc('category_logo','G');
		$sCategoryKeyword=G::getGpc('category_keyword','G');
		$sCategoryDescription=G::getGpc('category_description','G');
		$sCategoryIntroduce=G::getGpc('category_introduce','G');
		$oCategory=new CategoryModel();
		$oCategory->category_compositor=$nCategoryCompositor;
		$oCategory->category_urlname=$sCategoryUrlname;
		$oCategory->category_name=$sCategoryName;
		$oCategory->category_logo=$sCategoryLogo;
		$oCategory->category_keyword=$sCategoryKeyword;
		$oCategory->category_description=$sCategoryDescription;
		$oCategory->category_introduce=$sCategoryIntroduce;
		$oCategory->category_parentid=$nCategoryParentid;
		$oCategory->save();
		if(!$oCategory->isError()){
			Cache_Extend::front_widget_category();
			$this->A($oCategory->toArray(),G::L('分类数据保存成功！'),1);
		}
		else{
			$this->E($oCategory->getErrorMessage());
		}
	}

	public function get_parent_category($nParentCategoryId){
		if($nParentCategoryId==0){
			return G::L('顶级分类');
		}
		else{
			$oCategory=CategoryModel::F('category_id=?',$nParentCategoryId)->query();
			if(!empty($oCategory->category_id)){
				return $oCategory->category_name;
			}
			else{
				return G::L('父级分类已经损坏，你可以编辑分类进行修复。');
			}
		}
	}

	public function tree(){
		$oCategorys=CategoryModel::F()->order('category_compositor ASC,category_id DESC,')->all()->query();
		$oCategoryTree=new TreeCategory();
		foreach($oCategorys as $oCategory){
			$oCategoryTree->setNode($oCategory->category_id,$oCategory->category_parentid,$oCategory->category_name);
		}
		$this->assign('oCategoryTree',$oCategoryTree);
		$this->display();
	}

	public function get_category($nCategoryId){
		return CategoryModel::F('category_id=?',$nCategoryId)->query();
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_category();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_category();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_category();
	}

}
