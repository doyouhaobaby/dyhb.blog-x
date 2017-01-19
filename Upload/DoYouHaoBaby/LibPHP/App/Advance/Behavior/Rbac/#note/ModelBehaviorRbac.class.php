<?dyhb
/**
 * 使用方法：
 +--------------------------
// 配置文件增加设置
 USER_AUTH_ON 是否需要认证
 USER_AUTH_TYPE 认证类型
 USER_AUTH_KEY 认证识别号
 REQUIRE_AUTH_MODULE  需要认证模块
 NOT_AUTH_MODULE 无需认证模块
 USER_AUTH_GATEWAY 认证网关

// 数据库表
DROP TABLE IF EXISTS `doyouhaobaby_access`;
CREATE TABLE `doyouhaobaby_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `access_level` tinyint(1) NOT NULL,
  `access_parentid` smallint(6) NOT NULL,
  `access_module` varchar(50) default NULL,
  `access_status` tinyint(1) unsigned NOT NULL default '1',
  KEY `group_id` (`role_id`),
  KEY `node_id` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------
DROP TABLE IF EXISTS `doyouhaobaby_group`;
CREATE TABLE `doyouhaobaby_group` (
  `group_id` smallint(3) unsigned NOT NULL auto_increment,
  `group_name` varchar(25) NOT NULL,
  `group_title` varchar(50) NOT NULL,
  `create_dateline` int(11) unsigned NOT NULL,
  `update_dateline` int(11) unsigned NOT NULL default '0',
  `group_status` tinyint(1) unsigned NOT NULL default '0',
  `group_sort` smallint(3) unsigned NOT NULL default '0',
  `group_show` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------
DROP TABLE IF EXISTS `doyouhaobaby_node`;
CREATE TABLE `doyouhaobaby_node` (
  `node_id` smallint(6) unsigned NOT NULL auto_increment,
  `node_name` varchar(20) NOT NULL,
  `node_title` varchar(50) default NULL,
  `node_status` tinyint(1) default '0',
  `node_remark` varchar(255) default NULL,
  `node_sort` smallint(6) unsigned default NULL,
  `node_parentid` smallint(6) unsigned NOT NULL default '0',
  `node_level` tinyint(1) unsigned NOT NULL default '1',
  `node_type` tinyint(1) NOT NULL default '0',
  `group_id` tinyint(3) unsigned default '0',
  PRIMARY KEY  (`node_id`),
  KEY `node_level` (`node_level`),
  KEY `node_parentid` (`node_parentid`),
  KEY `node_status` (`node_status`),
  KEY `node_name` (`node_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------
DROP TABLE IF EXISTS `doyouhaobaby_role`;
CREATE TABLE `doyouhaobaby_role` (
  `role_id` smallint(6) unsigned NOT NULL auto_increment,
  `role_name` varchar(20) NOT NULL,
  `role_parentid` smallint(6) default NULL,
  `role_status` tinyint(1) unsigned default NULL,
  `role_remark` varchar(255) default NULL,
  `role_ename` varchar(5) default NULL,
  `create_dateline` int(11) unsigned NOT NULL,
  `update_dateline` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`role_id`),
  KEY `role_parentid` (`role_parentid`),
  KEY `role_ename` (`role_ename`),
  KEY `role_status` (`role_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------
DROP TABLE IF EXISTS `doyouhaobaby_roleuser`;
CREATE TABLE `doyouhaobaby_roleuser` (
  `role_id` mediumint(9) unsigned default NULL,
  `user_id` char(32) default NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------
DROP TABLE IF EXISTS `doyouhaobaby_user`;
CREATE TABLE `doyouhaobaby_user` (
  `user_id` int(10) NOT NULL auto_increment,
  `user_name` varchar(50) character set ucs2 NOT NULL,
  `user_nikename` varchar(50) default NULL,
  `user_password` char(32) NOT NULL,
  `user_lastlogintime` int(11) default NULL,
  `user_lastloginip` varchar(40) default NULL,
  `user_logincount` mediumint(8) default NULL,
  `user_email` varchar(150) default NULL,
  `user_remark` varchar(255) default NULL,
  `create_dateline` int(11) default NULL,
  `update_dateline` int(11) default NULL,
  `user_status` tinyint(1) default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
+--------------------------
*/
class ModelBehaviorRbac extends ModelBehavior{

	/**
	 * 插件的设置信息
	 *
	 * @access protected
	 * @var array
	 */
	protected $_arrSettings=array();

	/**
	 * 保存状态
	 *
	 * @access private
	 * @var array
	 */
	private $_arrSavedState=array();

	/**
	 * 认证器
	 *
	 * @access protected
	 * @var CommonIdentity
	 */
	private $_oCommonIdentity=null;

	/**
	 * 绑定行为插件
	 *
	 * @access public
	 * @return void
	 */
	public function bind(){}

	/**
	 * 验证用户登录并返回用户对象
	 *
	 * < 如果 update_login_auto 设置为 true，验证通过时会同时更新用户的登录信息。>
	 *
	 * < 用法：
	 *   $oUser=UserModel::M( )->checkLogin( $sUsername, $sPassword )
	 *   if( UserModel::M()->isBehaviorError() ){
	 *       UserModel::M()->getBehaviorErrorMessage()
	 *   }
	 *   else{
	 *       dump( $oUser );
	 *   }
	 *
	 *   如果用户名不存在，将获取我们无法找到XX这个用户的消息。
	 *   密码不正确，用户XX的密码错误。>
	 *
	 * @access public
	 * @param string $sUsername 用户名
	 * @param string $sPassword 密码
	 * @param bool $bUpdateLogin 是否自动更新登录信息
	 * @param boolean $bEmail 是否为E-mail验证
	 * @return Model 用户对象
	 */
	public function checkLogin($sUsername,$sPassword,$bEmail=false,$bUpdateLogin=true ){}

	/**
	 * 清理老的登录数据，可能为多个浏览器登录后的遗留问题
	 *
	 * @access public
	 * @param $sUserId int 登录用户名
	 * @return void
	 */
	public function tryToDeleteOldSession($nUserId){}

	/**
	 * 从数据库中获取用户登录信息
	 *
	 * @access public
	 * @param $sUserModel string 储存用户信息的数据库表
	 * @return void
	 */
	public function getAuthData($sUserModel=null){}

	/**
	 * 权限验证
	 *
	 * @access public
	 * @return void
	 */
	public function checkRbac(){}

	/**
	 * 判断是否已经登录
	 *
	 * @access public
	 * @return void
	 */
	public function isLogin(){}

	/**
	 * 判断是否已经登录
	 *
	 * @access public
	 * @return void
	 */
	public function alreadyLogout(){}

	/**
	 * 清理浏览器所有COOKIE
	 *
	 * @access public
	 * @return void
	 */
	public function clearAllCookie(){}

	/**
	 * 登出
	 *
	 * @access public
	 * @return void
	 */
	public function logout(){}

	/**
	 * 验证码校验
	 *
	 * @access public
	 * @param $sSeccode string 验证码
	 * @return void
	 */
	public function checkSeccode($sSeccode){}

	/**
	 * 清理DoYouHaoBaby Framework RBAC所有COOKIE
	 *
	 * @access public
	 * @return void
	 */
	protected function clearThisCookie(){}

	/**
	 * 验证用户名
	 *
	 * < 用法：
	 *  $bResult=UserModel::M( )->checkUsername(  $sUserName );
	 *  if( UserModel::M()->isBehaviorError() ){// 这里可以直接$bResult===false判断
	 *      G::dump( UserModel::M()->getBehaviorErrorMessage());
	 *  }
	 *  else{
	 *      echo '验证通过';
	 *  } >
	 *
	 * @access public
	 * @param string $sUsername 用户名
	 * @return boolean
	 */
	public function checkUsername($sUsername){}

	/**
	 * 验证用户名和密码
	 *
	 * < 用法：
	 * if(Member::meta()->checkPassword($username,$password)){
	 *     echo '用户名和密码验证通过';
	 * } >
	 *
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return boolean
	 */
	public function checkPassword($sUsername,$sPassword){}

	/**
	 * 修改指定用户的密码
	 *
	 * < 用法：
	 *   UserModel::changePassword( $sUsername, $sNewPassword, $sOldPassword, $bIgnoreOldPassword );
	 *
	 *   如果用户名不存在，将获取我们无法找到XX这个用户的消息。
	 *   密码不正确，用户XX的密码错误。
	 *   捕获错误消息，参考$this->checkUsername( $sUsername ) >
	 *
	 *   可以通过指定 $bIgnoreOldPassword 为 true 来忽略对旧密码的检查：
	 *   UserModel::changePassword( $sUsername, $sNewPassword, null, true); >
	 *
	 * @access public
	 * @param string $sUsername 用户名
	 * @param string $sNewPassword 新密码
	 * @param string $sOldPassword 旧密码
	 * @param boolean $bIgnoreOldPassword 是否忽略对旧密码的检查，默认为 false
	 */
	public function changePassword($sUsername,$sNewPassword,$sOldPassword,$bIgnoreOldPassword=false){}

	/**
	 * 检查指定的密码是否与当前用户的密码相符
	 *
	 * < 用法：
	 *   if ($oUser->checkPassword( $sPassword ){
	 *       echo '指定的密码与用户现有的密码相符';
	 *   } >
	 *
	 * @access public
	 * @param Model $oMember 要检查的用户对象
	 * @param string $sPassword 检查密码
	 * @return boolean 检查结果
	 */
	public function checkPasswordDyn(Model $oMember,$sPassword){}

	/**
	 * 修改当前用户的密码
	 *
	 * @access public
	 * @param Model $oMember 要更新密码的用户对象
	 * @param string $sNewPassword 新密码
	 * @param string $sOldPassword 旧密码
	 * @param boolean $bIgnoreOldPassword 是否忽略对旧密码的检查，默认为 false
	 * @return void
	 */
	public function changePasswordDyn(Model $oMember,$sNewPassword,$sOldPassword,$bIgnoreOldPassword=false){}

	/**
	 * 更新用户的登录信息
	 *
	 * < 要更新的属性由 update_login_count_prop、update_login_at_prop 和 update_login_ip_prop设置指定。>
	 *
	 * < 用法：
	 *   $oUser->updateLogin( );
	 *   updateLogin() 会尝试自行获取登录时间和 IP 信息。如果有必要也可自行指定：
	 *   $oUser->updateLogin( array(
	 *     'login_at' => $time,
	 *     'login_ip' => $ip,
	 *  )); >
	 *
	 * @access public
	 * @param Model $oMember 要更新登录信息的用户对象
	 * @param array $arrData 自行指定的属性值
	 * @return void
	 */
	public function updateLoginDyn(Model $oMember,array $arrData=null){}

	/**
	 * 获得用户的 用户登录信息 数据
	 *
	 * < RBAC 数据一般包含用户 ID 和用户名，用于实现基于角色的访问控制。>
	 *
	 * < 用法：
	 *   $arrData=$oUser->rbacData();
	 *   dump($arrData); >
	 *
	 * < 要返回的数据由 rbac_data_props 设置来指定。不过不管指定了哪些属性. >
	 *
	 * @access public
	 * @param Model $oMember 用户对象
	 * @return array 包含指定属性值的数组
	 */
	public function userDataDyn(){}

	/**
	 * 在新建的 Model 保存到数据库前调用的事件
	 *
	 * @access public
	 * @param Model $oMember 用户对象
	 * @return void
	 */
	public function afterCheckOnCreate_(Model $oMember){}

	/**
	 * 在更新 Model 前调用的事件
	 *
	 * @access public
	 * @param Model $oMember 用户对象
	 */
	public function afterCheckOnUpdate_(Model $oMember){}

	/**
	 * 在保存新建用户对象失败时，还原用户的密码属性
	 *
	 * @access public
	 * @param Model $oMember 保存出错的用户对象
	 * @return void
	 */
	public function saveExceptionHandler_(Model $oMember){}

	/**
	 * 检查明文和加密后的密码是否相符
	 *
	 * @access private
	 * @param string $cleartext 明文
	 * @param string $cryptograph 加密后的密码
	 * @param string $sRanDom 加密后的生成的随机码
	 * @return boolean
	 */
	private function checkPassword_($sCleartext,$sCryptograph,$sRanDom){}

	/**
	 * 获得加密后的密码
	 *
	 * @access public
	 * @param string $sPassword 要加密的密码明文
	 * @return string 加密后的密码
	 */
	private function encodePassword_($sPassword){}

	/**
	 * 发送登录信息COOKIE
	 *
	 * @access public
	 * @param $nUserId int 用户Id
	 * @param $sPassword string 用户密码
	 * @param $nLoginCount int 用户登录次数
	 * @param $nSecondCode int Second验证码
	 * @return void
	 */
	public function sendCookie($nUserId,$sPassword,$nLoginCount,$nSecondCode){}

	/**
	 * 更新登陆session
	 *
	 * @access public
	 * @param $sHash string hash验证
	 * @param $nUserId int 用户Id
	 * @param $sAuthKey string 认证键值
	 * @param $nSeccode int second验证
	 * @return void
	 */
	public function updateSession($sHash,$nUserId,$sAuthKey,$nSeccode){}

	/**
	 * 重置session
	 *
	 * @access public
	 * @param $sHash string hash
	 * @param $nUserId int 用户ID
	 * @param $sAuthKey string authkey
	 * @param $nSeccode int second码
	 * @param $bInsert=false bool 是否插入新数据
	 * @return void
	 */
	public function replaceSession($sHash,$nUserId,$sAuthKey,$nSeccode,$bInsert=false){}

	/**
	 * 取得顶部菜单
	 *
	 * @access public
	 * @return void
	 */
	public function getTopMenuList(){}

	/**
	 * 读取菜单列表
	 *
	 * @access public
	 * @return void
	 */
	public function getMenuList(){}

	/**
	 * 用于检测用户权限的方法,并保存到Cookie中
	 *
	 * @access public
	 * @param $authId
	 * @return void
	 */
	public function saveAccessList($nAuthId=null){}

	/**
	 * 检查当前操作是否需要认证
	 *
	 * @access public
	 * @return miexed
	 */
	public function checkAccess(){}

	/**
	 * 权限认证的过滤器方法
	 *
	 * @access public
	 * @param $sAppName
	 * @return mixed
	 */
	public function accessDecision($sAppName=APP_NAME){}

	/**
	 * 取得当前认证号的所有权限列表
	 *
	 * @access public
	 * @param $nAuthId
	 * @return miexed
	 */
	public function getAccessList($nAuthId){}

	/**
	 * 读取模板所属记录访问权限
	 *
	 * @access public
	 * @param $auth_id
	 * @param $module
	 * @return array
	 */
	static public function getModuleAccessList($nAuthId,$sModule){}

}
