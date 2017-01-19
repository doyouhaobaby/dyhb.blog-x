<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	会话模型($)*/

!defined('DYHB_PATH') && exit;

class UserModel extends CommonModel{

	static public function init__(){

		return array(
			'behaviors'=>'rbac',
			'behaviors_settings'=>array(
				'rbac'=>array(
					'password_prop'=>'user_password',
					'rbac_data_props'=>'user_id,user_name,user_lastlogintime,user_lastloginip,user_logincount,user_email,user_remark,create_dateline,user_registerip,update_dateline,user_status,user_homepage,user_nikename'
				),
			),
			'table_name'=>'user',
			'props'=>array(
				'user_id'=>array('readonly'=>true),
				'user_name'=>array('readonly'=>true),
			),

			'attr_protected'=>'user_id',
			'check'=>array(
				'user_email'=>array(
					array('require',G::L('E-mail不能为空')),
					array('email',G::L('E-mail格式错误')),
					array('max_length',150,G::L('E-mail不能超过150个字符'))
				),
				'user_name'=>array(
					array('require',G::L('用户名不能为空')),
				),
				'user_password'=>array(
					array('require',G::L('用户密码不能为空')),
					array('min_length',6,G::L('用户密码最小长度为6个字符！')),
					array('max_length',25,G::L('用户密码最大长度为25个字符！')),
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

	public function isUsernameExists($sUsername,$nUserId=0){
		$oResult=self::F()->getByuser_name($sUsername);
		if(!empty($oResult['user_id'])){
			if($nUserId==0){
				return true;
			}
			else{
				if($oResult['user_id']==$nUserId){
					return false;
				}
				else{
					return true;
				}
			}
		}
		else{
			return false;
		}
	}

	public function isUseremailExists($sUseremail,$nUserId=0){
		$oResult=self::F()->getByuser_email($sUseremail);
		if(!empty($oResult['user_id'])){
			if($nUserId==0){
				return true;
			}
			else{
				if($oResult['user_id']==$nUserId){
					return false;
				}
				else{
					return true;
				}
			}
		}
		else{
			return false;
		}
	}

	static public function getUserNameById($nUserId){
		$oUser=UserModel::F('user_id=?',$nUserId)->query();
		return $oUser['user_name'];
	}

}
