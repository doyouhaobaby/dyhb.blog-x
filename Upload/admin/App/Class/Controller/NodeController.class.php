<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	节点控制器($)*/

!defined('DYHB_PATH') && exit;

class NodeController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['node_name']=array('like',"%".G::getGpc('node_name')."%");
		$nGroupId=G::getGpc('group_id');
		$sSearch=G::getGpc('search');
		$nNodeParentid=G::getGpc('node_parentid');
		if($nGroupId===null)$nGroupId=0;
		if(!empty($nGroupId)){
			$arrMap['group_id']=&$nGroupId;
			$this->assign('sNodeName',G::L('分组'));
		}
		else if(empty($sSearch)&& !isset($arrMap['node_parentid'])){
			$arrMap['node_parentid']=0;
		}

		if(!empty($nNodeParentid)){
			$arrMap['node_parentid']=&$nNodeParentid;
		}
		$this->assign('nGroupId',$nGroupId);
		G::cookie('current_node_id',$nNodeParentid);
		if(isset($arrMap['node_parentid'])){
			if($oNode=NodeModel::F()->getBynode_id($arrMap['node_parentid'])){
				$this->assign('nNodeLevel',$oNode->node_level+1);
				$this->assign('sNodeName',$oNode->node_name);
			}
			else{
				$this->assign('nNodeLevel',1);
			}
		}
		else{
			$this->assign('nNodeLevel',1);
		}
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('本博客软件采用基于角色的权限认证体系(RBAC)来管理用户访问权限。','node').'</p>'.
				'<p>'.G::L('节点在RBAC系统中属于资源容器，里面储存了资源的层级关系。','node').'</p>'.
				'<p>'.G::L('节点的最高级别为应用，比如本软件的admin和blog这两个不同的应用。','node').'</p>'.
				'<p>'.G::L('应用的下面有模块，比如说BlogController 的blog，模块下面有操作方法。','node').'</p>'.
				'<p>'.G::L('（应用+模板+方法）构成一个完整的资源链，我们可以通过比对来进行访问控制。','node').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('选择“授权”，系统将会使用Ajax 提取数据供你选择。','node').'</p>'.
				'<p>'.G::L('节点的应用名不能为空，显示名不能为空。','node').'</p>'.
				'<p>'.G::L('填写完毕，请不要忘记点击保存数据。','node').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _sort_get_admin_help_description(){
		return '<p>'.G::L('在本窗口你可以对节点进行排序操作。','node').'</p>'.
				'<p>'.G::L('排序提供了多个按钮供你操作，选择完毕后，请不要忘记保存一下。','node').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		$arrIds=explode(',',$sId);
		foreach($arrIds  as $nId){
			$nNodes=NodeModel::F('node_parentid=?',$nId)->all()->getCounts();
			$oNode=NodeModel::F('node_id=?',$nId)->query();
			if($nNodes>0){
				$this->E(G::L("节点%s存在子分类，你无法删除。",'app',null,$oNode->node_name));
			}
		}
	}

	public function get_access(){
		$sAccess=G::getGpc('access','P');
		$nNodeLevel=1;
		if($sAccess=='app'){
			$arrAccessList=NodeModel::F()->where(array('node_level'=>1,'node_parentid'=>0))->asArray()->all()->query();
			$nNodeLevel=2;
		}
		elseif($sAccess=='module'){
			$arrAccessList=NodeModel::F()->where(array('node_level'=>2))->asArray()->all()->query();
			$nNodeLevel=3;
		}
		$this->assign('arrAccessList',$arrAccessList);
		$this->assign('nNodeLevel',$nNodeLevel);
		$this->display();
	}

	public function getGroup(){
		$arrGroup=array_merge(array(array('group_id'=>0,'group_title'=>G::L('未分组'))),GroupModel::F()->setColumns('group_id,group_title')->asArray()->all()->query()
		);
		$this->assign('arrGroup',$arrGroup);
	}

	public function bIndex_(){
		$this->getGroup();
	}

	public function bAdd_(){
		$this->getGroup();
	}

	public function bEdit_(){
		$this->getGroup();
	}

	public function clear_menu_cache(){
		Dyhb::deleteCache('access_list_'.G::cookie(md5($GLOBALS['_commonConfig_']['USER_AUTH_KEY' ])),array('cache_path'=>APP_RUNTIME_PATH.'/Data/AccessList'));
		Dyhb::deleteCache('_Menu_'.$GLOBALS['_commonConfig_']['USER_AUTH_KEY'].'_'.$GLOBALS['___login___']['user_id'],array('cache_path'=>APP_RUNTIME_PATH.'/Data/MenuList'));
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

	public function sort(){
		$nSortId=G::getGpc('sort_id');
		if(!empty($nSortId)){
			$arrMap['node_status']=1;
			$arrMap['node_id']=array('in',$nSortId);
			$arrSortList=NodeModel::F()->order('node_sort ASC')->all()->where($arrMap)->query();
		}
		else{
			$nNodeParentid=G::getGpc('node_parentid');
			if(!empty($nNodeParentid)){
				$nPid=&$nNodeParentid;
			}
			else{
				$nPid=G::cookie('current_node_id',$nNodeParentid);;
			}
			if($nPid===null)$nPid=0;
			$arrNode=NodeModel::F()->getBynode_id($nPid)->toArray();
			if(isset($arrNode['node_id'])){
				$nLevel=$arrNode['node_level']+1;
			}
			else{
				$nLevel=1;
			}
			$this->assign('nLevel',$nLevel);
			$arrSortList=NodeModel::F()->where('node_status=1 and node_parentid=? and node_level=?',$nPid,$nLevel)->order('node_sort ASC')->all()->query();
		}
		$this->assign("arrSortList",$arrSortList);
		$this->display();
	}

}
