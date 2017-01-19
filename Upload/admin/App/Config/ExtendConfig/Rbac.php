<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   Rbac配置数据($) */

!defined('DYHB_PATH') && exit;

return array(
	'RBAC_ROLE_TABLE'=>'role',
	'RBAC_USERROLE_TABLE'=>'userrole',
	'RBAC_ACCESS_TABLE'=>'access',
	'RBAC_NODE_TABLE'=>'node',
	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'=>1,
	'USER_AUTH_KEY'=>'auth_id',
	'ADMIN_USERID'=>'1,2',
	'ADMIN_AUTH_KEY'=>'administrator',
	'USER_AUTH_MODEL'=>'user',
	'AUTH_PWD_ENCODER'=>'md5',
	'USER_AUTH_GATEWAY'=>'public/login',
	'NOT_AUTH_MODULE'=>'public',
	'REQUIRE_AUTH_MODULE'=>'',
	'NOT_AUTH_ACTION'=>'',
	'REQUIRE_AUTH_ACTION'=>'',
	'GUEST_AUTH_ON'=>false,
	'GUEST_AUTH_ID'=>0,
	'RBAC_ERROR_PAGE'=>'',
);
