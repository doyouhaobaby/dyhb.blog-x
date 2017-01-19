<?php
/**

 //  [DoYouHaoBaby!] Init APP - %APP_NAME%
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2099 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | %APP_NAME% 基本配置文件
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

return array(

	/**
	 * 分布式服务器数据库部署连接
	 *
	 * < 说明：可以只设置一个主库
	 *   分布式部署的值均为小写，请注意。 >
	 *
	 * < 'DB_GLOBAL_DSN' = array(
	 *
	 *   //第一个为主库
	 *   array(
	 *    'db_host'=>'localhost',
	 *    'db_schema'=>'',
	 *    'db_port'=>3360,
	 *    'db_type'=>'mysql',
	 *    'db_user'=>'root',
	 *    'db_password'=>'',
	 *    'db_name'=>'',
	 *    'db_dsn'=>'',
	 *    'db_params'=>'',
	 *    'db_prefix'=>'',
	 *   ),
	 *    
	 *   //剩下的都是从库（可以多个）
	 *   array(
	 *    'db_host'=>'localhost',
	 *    'db_schema'=>'',
	 *    'db_port'=>3360,
	 *    'db_type'=>'mysql',
	 *    'db_user'=>'root',
	 *    'db_password'=>'',
	 *    'db_name'=>'',
	 *    'db_dsn'=>'',
	 *    'db_params'=>'',
	 *    'db_prefix'=>'',
	 *   ), >
	 */
	//'DB_TYPE'=>'mysql',// 数据库类型
	//'DB_HOST'=>'localhost',// 数据库地址
	//'DB_USER'=>'root',// 数据库用户名
	'DB_PASSWORD'=>'123456',// 数据库密码
	'DB_PREFIX'=>'dyhb_',// 数据库表前缀
	'DB_NAME'=>'',// 数据库名字
	//'DB_SCHEMA'=>'', // SCHEMA
	//'DB_PROT'=>'3360', // 数据库端口
	//'DB_DSN'=>'',// DSN方式连接数据库 < mysql://username:password@localhost:3306/dbname >
	//'DB_PARAMS'=>'',// 数据库连接参数
	//'START_ROUTER'=>TRUE,// 是否开启URL路由
	//'URL_MODEL'=>3,// URLMODE,
	//'APP_DEBUG'=>TRUE,// 系统异常调式，是否输出额外调试信息
	//'DEBUG'=>TRUE,// 模板引擎调式控制台
);
