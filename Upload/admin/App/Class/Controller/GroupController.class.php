<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	节点分组控制器($)*/

!defined('DYHB_PATH') && exit;

class GroupController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['group_name']=array('like',"%".G::getGpc('group_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('使用节点分组对节点进行管理，同时节点分组也是后台菜单生产的项目。','group').'</p>'.
				'<p>'.G::L('节点分组可以进行批量操作。','group').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以对节点分组进行编辑或者添加。','group').'</p>'.
				'<p>'.G::L('节点分组的节点名和节点显示名都不能为空。','group').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _sort_get_admin_help_description(){
		return '<p>'.G::L('在本窗口你可以对节点分组进行排序操作。','group').'</p>'.
				'<p>'.G::L('排序提供了多个按钮供你操作，选择完毕后，请不要忘记保存一下。','group').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function aInsert($nId){
		$this->clear_menu_cache();
	}

	public function aUpdate($nId){
		$this->clear_menu_cache();
	}

	public function aForeverdelete($sId){
		$this->clear_menu_cache();
	}

	public function afterInputChangeAjax(){
		$this->clear_menu_cache();
	}

	public function clear_menu_cache(){
		Dyhb::deleteCache('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY'])),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'));
		Dyhb::deleteCache('menu_top_cache_'.$GLOBALS['___login___']['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
	}

	public function sort(){
		$nSortId=G::getGpc('sort_id','G');
		if(!empty($nSortId)){
			$arrMap['group_status']=1;
			$arrMap['group_id']=array('in',$nSortId);
			$arrSortList=GroupModel::F()->order('group_sort ASC')->where($arrMap)->all()->query();
		}
		else{
			$arrSortList=GroupModel::F()->order('group_sort ASC')->all()->query();
		}
		$this->assign("arrSortList",$arrSortList);
		$this->display();
	}

}
