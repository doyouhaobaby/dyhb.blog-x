<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	角色模型($)*/

!defined('DYHB_PATH') && exit;

class RoleModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'role',
			'props'=>array(
				'role_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'role_id',
			'check'=>array(
				'role_name'=>array(
					array('require',G::L('应用名不能为空')),
					array('uniqueRoleName',G::L('组名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
				'role_parentid'=>array(
					array('uniqueRoleParentId',G::L('上级组不能为自己'),'condition'=>'must','extend'=>'callback'),
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

	public function getParentRoleName($nRoleId){
		if($nRoleId==0)return;
		$oRole=self::F($nRoleId)->getOne();
		if(!$oRole->isError()){
			return $oRole->role_name;
		}
	}

	public function uniqueRoleName(){
		$nId=G::getGpc('id');
		$sRoleName=trim(G::getGpc('role_name'));
		$sRoleInfo='';
		if($nId){
			$arrRole=self::F('role_id=?',$nId)->asArray()->getOne();
			$sRoleInfo=trim($arrRole['role_name']);
		}
		if($sRoleName !=$sRoleInfo){
			$arrResult=self::F()->getByrole_name($sRoleName)->toArray();
			if(!empty($arrResult['role_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

	public function uniqueRoleParentId(){
		$nRoleId=G::getGpc('id');
		$nRoleParentId=G::getGpc('role_parentid');
		if(($nRoleId==$nRoleParentId)&& $nRoleId !='' && $nRoleParentId !=''){
			return false;
		}
		return true;
	}

	public function getGroupAppList($nGroupId){
		$oDb=$this->getDb();
		return $oDb->getAllRows('select b.node_id,b.node_title,b.node_name from '.
				AccessModel::F()->query()->getTablePrefix().'access as a ,'.
				NodeModel::F()->query()->getTablePrefix().'node as b where a.node_id=b.node_id and  b.node_parentid=0 and a.role_id='.$nGroupId);
	}

	public function delGroupApp($nGroupId){
		$oDb=$this->getDb();
		$bResult=$oDb->query('delete from '.AccessModel::F()->query()->getTablePrefix().'access where `access_level`=1 and role_id='.$nGroupId);
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function setGroupApps($nGroupId,$arrAppIdList){
		$oDb=$this->getDb();
		if(empty($arrAppIdList)){
			return true;
		}
		$sId=implode(',',$arrAppIdList);
		$bResult=$oDb->query('INSERT INTO '.AccessModel::F()->query()->getTablePrefix().
				'access(role_id,node_id,access_parentid,access_level)SELECT a.role_id,b.node_id,b.node_parentid,b.node_level FROM '.
				RoleModel::F()->query()->getTablePrefix().'role a,'.
				NodeModel::F()->query()->getTablePrefix().
				'node b WHERE '.'a.role_id='.$nGroupId.' AND b.node_id in('.$sId.')');
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function getGroupModuleList($nGroupId,$nAppId){
		$oDb=$this->getDb();
		return $oDb->getAllRows('select b.node_id,b.node_title,b.node_name from '.
				AccessModel::F()->query()->getTablePrefix().'access as a ,'.
				NodeModel::F()->query()->getTablePrefix().
				'node as b where a.node_id=b.node_id and  b.node_parentid='.$nAppId.' and a.role_id='.$nGroupId);
	}

	public function delGroupModule($nGroupId,$nAppId){
		$oDb=$this->getDb();
		$nResult=$oDb->query('delete from '.AccessModel::F()->query()->getTablePrefix().'access where access_level=2 and access_parentid='.$nAppId.' and role_id='.$nGroupId);
		if($nResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function setGroupModules($nGroupId,$arrModuleIdList){
		$oDb=$this->getDb();
		if(empty($arrModuleIdList)){
			return true;
		}
		if(is_array($arrModuleIdList)){
			$arrModuleIdList=implode(',',$arrModuleIdList);
		}
		$bResult=$oDb->query('INSERT INTO '.AccessModel::F()->query()->getTablePrefix().
				'access(role_id,node_id,access_parentid,access_level)SELECT a.role_id,b.node_id,b.node_parentid,b.node_level FROM '.
				RoleModel::F()->query()->getTablePrefix().'role a,'.NodeModel::F()->query()->getTablePrefix().
				'node b WHERE '.'a.role_id='.$nGroupId.' AND b.node_id in('.$arrModuleIdList.')');
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function getGroupActionList($nGroupId,$nModuleId){
		$oDb=$this->getDb();
		return $oDb->getAllRows('select b.node_id,b.node_title,b.node_name from '.
			AccessModel::F()->query()->getTablePrefix().'access as a ,'.
			NodeModel::F()->query()->getTablePrefix().'node as b where a.node_id=b.node_id and  b.node_parentid='.$nModuleId.' and  a.role_id='.$nGroupId);

	}

	public function delGroupAction($nGroupId,$nModuleId){
		$oDb=$this->getDb();
		$bResult=$oDb->query('delete from '.AccessModel::F()->query()->getTablePrefix().
				'access where access_level=3 and access_parentid='.$nModuleId.' and role_id='.$nGroupId);
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function setGroupActions($nGroupId,$arrActionIdList){
		$oDb=$this->getDb();
		if(empty($arrActionIdList)){
			return true;
		}
		if(is_array($arrActionIdList)){
			$arrActionIdList=implode(',',$arrActionIdList);
		}

		$bResult=$oDb->query('INSERT INTO '.AccessModel::F()->query()->getTablePrefix().
				'access(role_id,node_id,access_parentid,access_level)SELECT a.role_id,b.node_id,b.node_parentid,b.node_level FROM '.
				RoleModel::F()->query()->getTablePrefix().'role a,'.
				NodeModel::F()->query()->getTablePrefix().'node b WHERE '.'a.role_id='.$nGroupId.' AND b.node_id in('.$arrActionIdList.')');
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function getGroupUserList($nGroupId){
		$oDb=$this->getDb();
		return $oDb->getAllRows('select b.user_id,b.user_nikename,b.user_email from '.
				UserroleModel::F()->query()->getTablePrefix().'Userrole as a ,'.
				UserModel::F()->query()->getTablePrefix().'user as b where a.user_id=b.user_id and  a.role_id='.$nGroupId);
	}

	public function delGroupUser($nGroupId){
		$oDb=$this->getDb();
		$bResult=$oDb->query('delete from '.UserroleModel::F()->query()->getTablePrefix().'Userrole where role_id='.$nGroupId);
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	public function setGroupUsers($nGroupId,$arrUserIdList){
		$oDb=$this->getDb();
		if( empty($arrUserIdList)){
			return true;
		}
		if(is_string($arrUserIdList)){
			$arrUserIdList=explode(',',$arrUserIdList);
		}
		array_walk($arrUserIdList,array($this,'fieldFormat'));
		$arrUserIdList=implode(',',$arrUserIdList);
		$bResult=$oDb->query('INSERT INTO '.UserroleModel::F()->query()->getTablePrefix().
					'Userrole(role_id,user_id)SELECT a.role_id,b.user_id FROM '.
					RoleModel::F()->query()->getTablePrefix().'role a,'.
					UserModel::F()->query()->getTablePrefix().'user b WHERE '.'a.role_id='.$nGroupId.' AND b.user_id in('.$arrUserIdList.')');
		if($bResult===false){
			return false;
		}
		else{
			return true;
		}
	}

	protected function fieldFormat(&$Value){
		if(is_int($Value)){
			$Value=intval($Value);
		}
		else if(is_float($Value)){
			$Value=floatval($Value);
		}
		else if(is_string($Value)){
			$Value='"'.addslashes($Value).'"';
		}
		return $Value;
	}

}
