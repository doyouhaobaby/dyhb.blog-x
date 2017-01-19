<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	用户控制器($)*/

!defined('DYHB_PATH') && exit;

class UserController extends InitController{

	public function filter_(&$arrMap){
		$arrMap ['user_id']=array('gt',1);
		$arrMap ['user_name']=array('like','%'.G::getGpc('user_name').'%');
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('本页面列出了当前您站点的所有用户。根据站点管理员的意愿，每位用户都可以分配权限（基于角色的权限认证体系 RBAC）。','user').'</p>'.
				'<p>'.G::L('要添加一位新用户到站点中，点击屏幕上方的“添加”按钮。','user').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('要为您的站点添加用户，请填写本页面的表单。','user').'</p>'.
				'<p>'.G::L('请不要忘记在完成后点击“添加”按钮。','user').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function checkusername(){
		$sUserName=G::getGpc('user_name','R');
		if(!empty($sUserName)){
			Check::RUN();
			if(!Check::C($sUserName,'number_underline_english')){
				$this->E(G::L('用户名只能由数字，下划线，字母组成'));
			}
			$arrUser=UserModel::F()->getByuser_name($sUserName)->toArray();
			if(!empty($arrUser['user_id'])){
				$this->E(G::L('用户名已经存在'));
			}
			else{
				$this->S(G::L('用户名可以使用!'));
			}
		}
		else{
			$this->E(G::L('用户名必须'));
		}
	}

	public function resetpassword(){
		$nId=intval(G::getGpc('id'));
		$sPassword=G::getGpc('password');
		if(!empty($sPassword)){
			UserModel::M()->changePassword($nId,$sPassword,null,true);
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
			else{
				$oUser=UserModel::F('user_id=?',$nId)->query();
				if($oUser->isError()){
					$this->E($oUser->getErrorMessage());
				}
				$this->S(G::L('密码修改成功！'));
			}
		}
		else{
			$this->E(G::L('密码不能为空！'));
		}
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_static();
		$oUser=UserModel::F('user_id=?',$nId)->query();
		$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
		$oCacheData->countcache_usersnum=$oCacheData->countcache_usersnum+1;
		$oCacheData->countcache_lastuser=$oUser['user_name'];
		$oCacheData->save(0,'update');
		if($oCacheData->isError()){
			$this->E($oCacheData->getErrorMessage());
		}
	}

	public function aForeverdelete($sId){
		$sId=G::getGpc('id','G');
		$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
		$oCacheData->countcache_usersnum=$oCacheData->countcache_usersnum -count(explode(',',trim($sId)));
		if($oCacheData->countcache_usersnum<0){
			$oCacheData->countcache_usersnum=0;
		}
		$oCacheData->save(0,'update');
		if($oCacheData->isError()){
			$this->E($oCacheData->getErrorMessage());
		}
		Cache_Extend::front_widget_static();
	}

}
