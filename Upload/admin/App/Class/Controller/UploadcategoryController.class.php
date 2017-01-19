<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	文件分类控制器($)*/

!defined('DYHB_PATH') && exit;

class UploadcategoryController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['uploadcategory_name']=array('like',"%".G::getGpc('uploadcategory_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('附件归档的目的是为了更好的组织附件，方便管理附件。','uploadcategory').'</p>'.
				'<p>'.G::L('附件归档被删除后，它的附件会被移除到未分类。','uploadcategory').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('您可以在表单中添加数据，然后保存即可。','uploadcategory').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_uploadcategory();
		Cache_Extend::front_widget_recentimage();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_uploadcategory();
		Cache_Extend::front_widget_recentimage();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_uploadcategory();
		Cache_Extend::front_widget_recentimage();
	}

}
