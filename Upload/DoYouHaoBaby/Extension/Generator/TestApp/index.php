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
 //  | %APP_NAME% 入口文件
 //  +---------------------------------------------------------------------

*/

/** 
 * 当前项目配置 < 用于配置系统运行环境 >
 *
 * <!-- 虚拟主机方式部署 -->
 *
 * < 如果采用虚拟主机方式来部署，即入口文件位于项目目录的根目录 
 *   这种情况可以不设置APP_NAME 和APP_PATH >
 *
 * <!-- 独立服务器部署方式 --> 
 *
 * < 如果是独立服务器部署方式，因为入口文件和项目不一致。
 *   这个时候，我们需要指定项目目录，以便我们利用项目中资源。
 *   同时，这个时候我们也需要指定项目目录，因为如果我们指定项目名字，
 *   那么项目就由入口文件上层目录代替，显然，这是错误的。 >
 *
 * <!-- 注意事项 -->
 *
 * < 项目名字只能有字母、数字、下划线组成 >
 * < 项目路径只能使用物理路径，因为系统很多底层数据处理，因为目录深度的原因，
 *   如果不使用物理路径，将会造成系统找不到路径。
 *   所以系统中不允许出现 ‘./test’，‘../hello/test’之类的相对路径 。
 *   另外，项目路径不云溪后面添加'/','\'，这是系统一个习惯用法。
 *   所有常量定义的路径，后面都没有在添加一个斜杠后者反斜杠 >
 *
 */
%APP_NOTES%define('APP_NAME','%APP_NAME%'); //项目名字
%APP_PATH% //项目路径

/** 加载框架 */
require('%DYHB_PATH%');

/** 实例化框架并且初始化 */
App::RUN();