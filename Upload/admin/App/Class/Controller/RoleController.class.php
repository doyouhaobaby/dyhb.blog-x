<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	角色控制器($)*/

!defined('DYHB_PATH') && exit;

class RoleController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['role_name']=array('like',"%".G::getGpc('role_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('“角色”一词的源于戏剧，自1934年米德（G.H.Mead）首先运用角色的概念来说明个体在社会舞台上的身份及其行以后，角色的概念被广泛应用于社会学与心理学的研究中。','role').'</p>'.
				'<p>'.G::L('社会学对角色的定义是“与社会地位相一致的社会限度的特征和期望的集合体”。','role').'</p>'.
				'<p>'.G::L('在企业管理中，组织对不同的员工有不同的期待和要求，就是企业中员工的角色。','role').'</p>'.
				'<p>'.G::L('社会学对角色的定义是“与社会地位相一致的社会限度的特征和期望的集合体”。','role').'</p>'.
				'<p>'.G::L('这种角色不是固定的，会随着企业的发展和企业管理的需要而不断变化，比如在项目管理中，某些项目成员可能是原职能部门的领导者，在项目团队中可能角色会变为服务者。','role').'</p>'.
				'<p>'.G::L('角色是一个抽象的概念，不是具体的个人，它本质上反映一种社会关系，具体的个人是一定角色的扮演者。','role').'</p>'.
				'<p>'.G::L('角色可以由不同的职位和岗位担任，职位、岗位和角色的综合表现形式就是相应的职位说明书、岗位说明书和角色说明书。','role').'</p>'.
				'<p>'.G::L('角色是一个抽象的概念，不是具体的个人，它本质上反映一种社会关系，具体的个人是一定角色的扮演者。','role').'</p>'.
				'<p>'.G::L('角色与其“扮演者”既有的岗位不存在冲突，可以理解为：既有岗位职责没有描述（或者不可能描述）的内容可因为其角色的分配而充实。','role').'</p>'.
				'<p>'.G::L('本软件的角色也是这个意思，我们将具有相似的用户放在一起组成角色。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('在这里我们可以添加一个角色，角色具有层级关系，通过Ajax 选择。','role').'</p>'.
				'<p>'.G::L('角色名不能为空。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _app_get_admin_help_description(){
		return '<p>'.G::L('应用授权给整个应用进行授权。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _module_get_admin_help_description(){
		return '<p>'.G::L('模块授权给各个模块进行授权。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _action_get_admin_help_description(){
		return '<p>'.G::L('操作授权给各个操作进行授权。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _user_get_admin_help_description(){
		return '<p>'.G::L('用户授权给用户进行授权。','role').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function select(){
		$arrMap=array();
		$nParentid=G::getGpc('role_parentid');
		if($nParentid!=''){
			$arrMap['role_parentid']=$nParentid;
		}
		$arrList=RoleModel::F($arrMap)->setColumns('role_id,role_name')->all()->asArray()->query();
		$this->assign('arrList',$arrList);
		$this->display();
	}

	public function bSet_app_(){
		$nGroupId=G::getGpc('group_id');
		if(empty($nGroupId)){
			$this->E(G::L('你没有选择分组!'));
		}
	}

	public function set_app(){
		$nId=G::getGpc('groupAppId');
		$nGroupId=G::getGpc('group_id');
		$oRoleModel=RoleModel::F()->query();
		$oRoleModel->delGroupApp($nGroupId);
		$bResult=$oRoleModel->setGroupApps($nGroupId,$nId);
		if($bResult===false){
			$this->E(G::L('项目授权失败！'));
		}
		else{
			$this->S(G::L('项目授权成功！'));
		}
	}

	public function app(){
		$arrAppList=array();
		$arrList=NodeModel::F('node_level=?',1)->setColumns('node_id,node_title')->asArray()->all()->query();
		foreach($arrList as $arrVo){
			$arrAppList[$arrVo['node_id']]=$arrVo['node_title'];
		}
		$arrGroupList=array();
		$arrList=RoleModel::F()->setColumns('role_id,role_name')->asArray()->all()->query();
		foreach($arrList as $arrVo){
			$arrGroupList[$arrVo['role_id']]=	$arrVo['role_name'];
		}
		$this->assign("arrGroupList",$arrGroupList);
		$nGroupId=G::getGpc('group_id');
		if($nGroupId===null) $nGroupId=0;
		$this->assign('nGroupId',$nGroupId);
		$arrGroupAppList=array();
		$this->assign("nSelectGroupId",$nGroupId);
		if(!empty($nGroupId)){
			$arrList=RoleModel::F()->query()->getGroupAppList($nGroupId);
			foreach($arrList as $arrVo){
				$arrGroupAppList[]=	$arrVo['node_id'];
			}
		}
		$this->assign('arrGroupAppList',$arrGroupAppList);
		$this->assign('arrAppList',$arrAppList);
		$this->display();
	}

	public function module(){
		$nGroupId=G::getGpc('group_id');
		$nAppId=G::getGpc('app_id');
		if($nGroupId===null)$nGroupId=0;
		$this->assign('nGroupId',$nGroupId);
		$arrGroupList=array();
		$arrList=RoleModel::F()->setColumns('role_id,role_name')->all()->asArray()->query();
		foreach($arrList as $arrVo){
			$arrGroupList[$arrVo['role_id']]=$arrVo['role_name'];
		}
		$this->assign("arrGroupList",$arrGroupList);
		$arrAppList=array();
		$this->assign("nSelectGroupId",$nGroupId);
		if(!empty($nGroupId)){
			$arrList=RoleModel::F()->query()->getGroupAppList($nGroupId);
			foreach($arrList as $arrVo){
				$arrAppList[$arrVo['node_id']]=	$arrVo['node_title'];
			}
		}
		$this->assign("arrAppList",$arrAppList);
		$arrModuleList=array();
		$this->assign("nSelectAppId",$nAppId);
		if(!empty($nAppId)){
			$arrWhere['node_level']=2;
			$arrWhere['node_parentid']=$nAppId;
			$arrNodelist=NodeModel::F()->setColumns('node_id,node_title')->where($arrWhere)->asArray()->all()->query();
			foreach($arrNodelist as $arrVo){
				$arrModuleList[$arrVo['node_id']]=$arrVo['node_title'];
			}
		}

		$arrGroupModuleList=array();
		if(!empty($nGroupId)&& !empty($nAppId)){
			$arrGrouplist=RoleModel::F()->query()->getGroupModuleList($nGroupId,$nAppId);
			foreach($arrGrouplist as $arrVo){
				$arrGroupModuleList[]=$arrVo['node_id'];
			}
		}
		$this->assign('arrGroupModuleList',$arrGroupModuleList);
		$this->assign('arrModuleList',$arrModuleList);
		$this->display();
	}

	public function bSet_module_(){
		$nGroupId=G::getGpc('group_id');
		$nAppId=G::getGpc('appId');
		if(empty($nGroupId)){
			$this->E(G::L('你没有选择分组!'));
		}
		if(empty($nAppId)){
			$this->E(G::L('你没有选择APP'));
		}
	}

	public function set_module(){
		$nId=G::getGpc('groupModuleId');
		$nGroupId=G::getGpc('group_id');
		$nAppId=G::getGpc('appId');
		RoleModel::F()->query()->delGroupModule($nGroupId,$nAppId);
		$bResult=RoleModel::F()->query()->setGroupModules($nGroupId,$nId);
		if($bResult===false){
			$this->E(G::L('模块授权失败！'));
		}
		else{
			$this->S(G::L('模块授权成功！'));
		}
	}

	public function action(){
		$nGroupId=G::getGpc('group_id','G');
		$nAppId=G::getGpc('app_id','G');
		$nModuleId=G::getGpc('module_id','G');
		if($nGroupId===null)$nGroupId=0;
		$this->assign('nGroupId',$nGroupId);
		if($nAppId===null)$nAppId=0;
		$this->assign('nAppId',$nAppId);
		$arrGrouplist=RoleModel::F()->setColumns('role_id,role_name')->asArray()->all()->query();
		foreach($arrGrouplist as $arrVo){
			$arrGroupList[$arrVo['role_id']]=$arrVo['role_name'];
		}
		$this->assign("arrGroupList",$arrGroupList);
		$this->assign("nSelectGroupId",$nGroupId);
		$arrAppList=array();
		if(!empty($nGroupId)){
			$arrList=RoleModel::F()->query()->getGroupAppList($nGroupId);
			foreach($arrList as $arrVo){
				$arrAppList[$arrVo['node_id']]=	$arrVo['node_title'];
			}
		}

		$this->assign("arrAppList",$arrAppList);
		$this->assign("nSelectAppId",$nAppId);
		$arrModuleList=array();
		if(!empty($nAppId)){
			$arrList=RoleModel::F()->query()->getGroupModuleList($nGroupId,$nAppId);
			foreach($arrList as $arrVo){
				$arrModuleList[$arrVo['node_id']]=$arrVo['node_title'];
			}
		}

		$this->assign("arrModuleList",$arrModuleList);
		$this->assign("nSelectModuleId",$nModuleId);
		$arrActionList=array();
		if(!empty($nModuleId)){
			$arrMap['node_level']=3;
			$arrMap['node_parentid']=$nModuleId;
			$arrList=NodeModel::F()->setColumns('node_id,node_title')->where($arrMap)->asArray()->all()->query();
			if($arrList){
				foreach($arrList as $arrVo){
					$arrActionList[$arrVo['node_id']]=	$arrVo['node_title'];
				}
			}
		}
		$this->assign('arrActionList',$arrActionList);
		$arrGroupActionList=array();
		if(!empty($nModuleId) && !empty($nGroupId)){
			$arrGroupAction=RoleModel::F()->query()->getGroupActionList($nGroupId,$nModuleId);
			if($arrGroupAction){
				foreach($arrGroupAction as $arrVo){
					$arrGroupActionList[]=$arrVo['node_id'];
				}
			}
		}
		$this->assign('arrGroupActionList',$arrGroupActionList);
		$this->display();
	}

	public function bSet_action_(){
		$nGroupId=G::getGpc('group_id','P');
		$nAppId=G::getGpc('appId','P');
		$nGroupActionId=G::getGpc('groupActionId','P');
		if(empty($nGroupId)){
			$this->E(G::L('你没有选择分组!'));
		}
		if(empty($nAppId)){
			$this->E(G::L('你没有选择APP！'));
		}
		if(empty($nGroupActionId)){
			$this->E(G::L('你没有选择方法！'));
		}
	}

	public function set_action(){
		$nId=G::getGpc('groupActionId','P');
		$nModuleId=G::getGpc('moduleId','P');
		$nGroupId=G::getGpc('group_id','P');
		RoleModel::F()->query()->delGroupAction($nGroupId,$nModuleId);
		$bResult=RoleModel::F()->query()->setGroupActions($nGroupId,$nId);
		if($bResult===false){
			$this->E(G::L('操作授权失败！'));
		}
		else{
			$this->S(G::L('操作授权成功！'));
		}
	}

	public function user(){
		$arrList=UserModel::F()->setColumns('user_id,user_name,user_nikename')->asArray()->all()->query();
		foreach($arrList as $arrVo){
			$arrUserList[$arrVo['user_id']]=$arrVo['user_name'].' '.$arrVo['user_nikename'];
		}
		$arrList=RoleModel::F()->setColumns('role_id,role_name')->asArray()->all()->query();
		foreach($arrList as $arrVo){
			$arrGroupList[$arrVo['role_id']]=$arrVo['role_name'];
		}
		$this->assign("arrGroupList",$arrGroupList);
		$nGroupId=G::getGpc('id');
		$this->assign('nId',$nGroupId);
		$arrGroupUserList=array();
		$this->assign("nSelectGroupId",$nGroupId);
		$arrGroupUserList=array();
		if(!empty($nGroupId)){
			$arrList=RoleModel::F()->query()->getGroupUserList($nGroupId);
			foreach($arrList as $arrVo){
				$arrGroupUserList[]=$arrVo['user_id'];
			}
		}
		$this->assign('arrGroupUserList',$arrGroupUserList);
		$this->assign('arrUserList',$arrUserList);
		$this->display();
	}

	public function bSet_user_(){
		$nGroupId=G::getGpc('group_id','P');
		if(empty($nGroupId)){
			$this->E(G::L('授权失败！'));
		}
	}

	public function set_user(){
		$nId=G::getGpc('groupUserId','P');
		$nGroupId=G::getGpc('group_id','P');
		RoleModel::F()->query()->delGroupUser($nGroupId);
		$bResult=RoleModel::F()->query()->setGroupUsers($nGroupId,$nId);
		if($bResult===false){
			$this->E(G::L('授权失败！'));
		}
		else{
			$this->S(G::L('授权成功！'));
		}
	}

	public function get_parent_role($nParentRoleId){
		if($nParentRoleId==0){
			return G::L('顶级分类');
		}
		else{
			$oRole=RoleModel::F('role_id=?',$nParentRoleId)->query();
			if(!empty($oRole->role_id)){
				return $oRole->role_name;
			}
			else{
				return G::L('父级分类已经损坏，你可以编辑分类进行修复。');
			}
		}
	}

}
