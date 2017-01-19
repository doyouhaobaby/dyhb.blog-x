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
 //  | %APP_NAME% 路由配置文件
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

return array(

	/**
	 * 普通路由 ( 1：带正则匹配的 2：不带正则的  )
	 *
	 * < 注意：如果不带正则，1，2个参数必须，第三个可以不填 ，
	 *   带上正则，那么在前面加上一个键值为'regex'的正则值  >
	 *
	 * < 简单路由定义：array('模块/操作名', '路由对应变量','额外参数')
	 *   或者 array(array('模块','操作名'),'路由对应变量','额外参数') >
	 *
	 * <!-- 当前URL：-->
	 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/category/121/1/2/3/4/
	 *   路由到模块 Category 的blog 操作 >
	 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/121/1/hello/232
	 *   路由到模块Blog的category操作
	 *
	 * <!-- $_GET参数: -->
	 * < [a] => blog
	 *   [c] => category
	 *   [id] => 121
	 *   [test1] => 1
	 *   [test2] => 2
	 *   [test3] => 3
	 *   [par1] => 1
	 *   [par2] => 2 >
	 * < [a] => category
	 *   [c] => blog
	 *   [id1] => 121
	 *   [id2] => 1
	 *   [hello] => 232
	 *   [par1] => 1
	 *   [par2] => 2 >
	 */
	 //'Category'=>array('category/blog','id,test1,test2,test3','par1=1&par2=2'),
	 //'Blog'=>array('regex'=>'/^(\d+)\/(\d+)/','blog/category','id1,id2','par1=1&par2=2'),

	/**
	 * 泛路由
	 *
	 * <!-- 当前URL：-->
	 * < http://localhost/doyouhaobaby/branch/TestApp/blog/index.php/12
	 *   路由到模块 blog 的read 操作  >
	 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/12/34
	 *   路由到模块 blog 的archive 操作  >
	 * < http://localhost/doyouhaobaby/branch/TestApp/index.php/blog/hello
	 *   路由到模块 blog 的category 操作  >
	 *
	 * <!-- $_GET参数: -->
	 * < [a] => read
	 *   [c] => blog
	 *   [id] => 12
	 *   [par1] => 1
	 *   [par2] => 2 >
	 * < [a] => archive
	 *   [c] => blog
	 *   [year] => 12
	 *   [month] => 34
	 *   [par1] => 1
	 *   [par2] => 2 >
	 * < [a] => category
	 *   [c] => blog
	 *   [test] => hello
	 *   [par1] => 1
	 *   [par2] => 2 >
	 */
	//'Blog@'=>array(
		//array('regex'=>'/^(\d+)$/','blog/read','id','par1=1&par2=2'),
		//array('regex'=>'/^(\d+)\/(\d+)/','blog/archive','year,month','par1=1&par2=2'),
		//array('blog/category','test','par1=1&par2=2'),
	//)
);
