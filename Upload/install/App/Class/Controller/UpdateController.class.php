<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   install Update控制器($)*/

!defined('DYHB_PATH')&& exit;

class UpdateController extends Controller{

	protected $_sLockfile='';

	public function init__(){
		parent::init__();
		define('PROGRAM_INSTALL_PATH',dirname(APP_PATH));
		$this->_sUpdatefile=DYHB_PATH.'/../Common/Update.lock.php';
		if(file_exists($this->_sLockfile) && !in_array(ACTION_NAME,array('index','step2'))){
			exit(G::L(" 程序已运行升级，如果你确定要重新升级（可能出现错误），请先从FTP中删除 %s",'update',null,$this->_sUpdatefile));
		}
		$this->assign('sVersion','2.0.1');
		$this->assign('sVersionTime','20120325');
		$this->assign('sDyhbBlogXDatabase','dyhbblogxv201');
	}

	public function index(){
		$this->display('updatestep1');
	}

	public function step2(){
		$arrInfo=array();
		$arrInfo['phpv']=phpversion();
		$arrInfo['sp_os']=PHP_OS;
		$arrInfo['sp_gd']=gdVersion();
		$arrInfo['sp_server']=$_SERVER['SERVER_SOFTWARE'];
		$arrInfo['sp_host']=(empty($_SERVER['REMOTE_ADDR'])? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
		$arrInfo['sp_name']=$_SERVER['SERVER_NAME'];
		$arrInfo['sp_max_execution_time']=ini_get('max_execution_time');
		$arrInfo['sp_allow_reference']=(ini_get('allow_call_time_pass_reference')? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
		$arrInfo['sp_allow_url_fopen']=(ini_get('allow_url_fopen')? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
		$arrInfo['sp_safe_mode']=(ini_get('safe_mode')? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
		$arrInfo['sp_gd']=($arrInfo['sp_gd']>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
		$arrInfo['sp_mysql']=(function_exists('mysql_connect')? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
		if($arrInfo['sp_mysql']=='<font color=red>[×]Off</font>'){
			$bSpMysqlErr=TRUE;
		}
		else{
			$bSpMysqlErr=FALSE;
		}
		$arrSpTestDirs=array(
			'/Common/*',
			'/Common/Config.inc.php',
			'/Public/Upload/*',
			'/Public/Avatar/*',
			'/Public/Avatar/data/*',
			'/Public/Avatar/temp/*',
			'/Public/Images/Emot/Custom/*',
			'/Public/Images/Mp3/mp3player.xml',
			'/blog/Theme/*',
			'/blog/Theme/Default/*',
			'/blog/Theme/Default/dyhb-x-blog-style-default.xml',
			'/blog/Theme/Default/Public/*',
			'/blog/Theme/Default/Public/Php/*',
			'/blog/Theme/Default/Public/Php/Lang/*',
			'/blog/Theme/Default/Public/Php/Lang/Zh-cn/*',
			'/blog/Theme/Default/Public/Php/Lang/Zh-tw/*',
			'/blog/Theme/Default/Public/Php/Lang/En-us/*',
			'/blog/Theme/Cms/*',
			'/blog/Theme/Cms/dyhb-x-blog-style-cms.xml',
			'/blog/Theme/Cms/Public/*',
			'/blog/Theme/Cms/Public/Php/*',
			'/blog/Theme/Cms/Public/Php/Lang/*',
			'/blog/Theme/Cms/Public/Php/Lang/Zh-cn/*',
			'/blog/Theme/Cms/Public/Php/Lang/Zh-tw/*',
			'/blog/Theme/Cms/Public/Php/Lang/En-us/*',
			'/blog/Theme/Board/*',
			'/blog/Theme/Board/dyhb-x-blog-style-board.xml',
			'/blog/Theme/Board/Public/*',
			'/blog/Theme/Board/Public/Php/*',
			'/blog/Theme/Board/Public/Php/Lang/*',
			'/blog/Theme/Board/Public/Php/Lang/Zh-cn/*',
			'/blog/Theme/Board/Public/Php/Lang/Zh-tw/*',
			'/blog/Theme/Board/Public/Php/Lang/En-us/*',
			'/blog/Theme/Blue/*',
			'/blog/Theme/Blue/dyhb-x-blog-style-blue.xml',
			'/blog/Theme/Brown/*',
			'/blog/Theme/Brown/dyhb-x-blog-style-brown.xml',
			'/blog/Theme/Cmsblue/*',
			'/blog/Theme/Cmsblue/dyhb-x-blog-style-cmsblue.xml',
			'/blog/Theme/Fashion/*',
			'/blog/Theme/Fashion/dyhb-x-blog-style-fashion.xml',
			'/blog/Theme/Gray/*',
			'/blog/Theme/Gray/dyhb-x-blog-style-gray.xml',
			'/blog/Theme/Green/*',
			'/blog/Theme/Green/dyhb-x-blog-style-green.xml',
			'/blog/Theme/Greenwall/*',
			'/blog/Theme/Greenwall/dyhb-x-blog-style-greenwall.xml',
			'/blog/Theme/Greyishgreen/*',
			'/blog/Theme/Greyishgreen/dyhb-x-blog-style-greyishgreen.xml',
			'/blog/Theme/Jeans/*',
			'/blog/Theme/Jeans/dyhb-x-blog-style-jeans.xml',
			'/blog/Theme/Orange/*',
			'/blog/Theme/Orange/dyhb-x-blog-style-orange.xml',
			'/blog/Theme/Pink/*',
			'/blog/Theme/Pink/dyhb-x-blog-style-pink.xml',
			'/blog/Theme/Purple/*',
			'/blog/Theme/Purple/dyhb-x-blog-style-purple.xml',
			'/blog/Theme/Red/*',
			'/blog/Theme/Red/dyhb-x-blog-style-red.xml',
			'/blog/Theme/Uchome/*',
			'/blog/Theme/Uchome/dyhb-x-blog-style-uchome.xml',
			'/blog/Theme/Violet/*',
			'/blog/Theme/Violet/dyhb-x-blog-style-violet.xml',
			'/blog/App/Class/*',
			'/blog/App/~Runtime/*',
			'/blog/App/~Runtime/Cache/*',
			'/blog/App/~Runtime/Data/*',
			'/blog/App/~Runtime/Data/AccessList/*',
			'/blog/App/~Runtime/Data/Css/*',
			'/blog/App/~Runtime/Data/DbCache/*',
			'/blog/App/~Runtime/Data/DbMeta/*',
			'/blog/App/~Runtime/Data/Javascript/*',
			'/blog/App/~Runtime/Log/*',
			'/blog/App/~Runtime/Temp/*',
			'/blog/App/Lang/*',
			'/blog/App/Lang/Zh-cn/*',
			'/blog/App/Lang/Zh-tw/*',
			'/blog/App/Lang/En-us/*',
			'/blog/App/Html/*',
			'/wap/Theme/*',
			'/wap/Theme/Default/*',
			'/wap/App/Class/*',
			'/wap/App/~Runtime/*',
			'/wap/App/~Runtime/Cache/*',
			'/wap/App/~Runtime/Data/*',
			'/wap/App/~Runtime/Data/AccessList/*',
			'/wap/App/~Runtime/Data/DbCache/*',
			'/wap/App/~Runtime/Data/DbMeta/*',
			'/wap/App/~Runtime/Log/*',
			'/wap/App/~Runtime/Temp/*',
			'/wap/App/Lang/*',
			'/wap/App/Lang/Zh-cn/*',
			'/wap/App/Lang/Zh-tw/*',
			'/wap/App/Lang/En-us/*',
			'/admin/Theme/*',
			'/admin/Theme/Default/*',
			'/admin/App/Class/*',
			'/admin/App/~Runtime/*',
			'/admin/App/~Runtime/Cache/*',
			'/admin/App/~Runtime/Data/*',
			'/admin/App/~Runtime/Data/AccessList/*',
			'/admin/App/~Runtime/Data/DbCache/*',
			'/admin/App/~Runtime/Data/DbMeta/*',
			'/admin/App/~Runtime/Data/MenuList/*',
			'/admin/App/Data/*',
			'/admin/App/Data/Backup/*',
			'/admin/App/Data/Backup/run.log',
			'/admin/App/Data/Search/*',
			'/admin/App/Class/Model/*',
			'/admin/App/Lang/Zh-cn/*',
			'/admin/App/Lang/Zh-tw/*',
			'/admin/App/Lang/En-us/*',
		);
		$this->assign('arrInfo',$arrInfo);
		$this->assign('bSpMysqlErr',$bSpMysqlErr);
		$this->assign('arrSpTestDirs',$arrSpTestDirs);
		$this->display('step4');
	}

	public function step3(){
		$arrConfig=(array)(include DYHB_PATH.'/../Common/Config.inc.php');
		$this->assign('arrConfig',$arrConfig);
		$this->display('updatestep3');
	}

	public function first(){
		$arrConfig=(array)(include DYHB_PATH.'/../Common/Config.inc.php');
		if(empty($arrConfig['RBAC_DATA_PREFIX'])){
			$this->E(G::L('Rbac前缀不能为空'));
		}
		if(empty($arrConfig['COOKIE_PREFIX'])){
			$this->E(G::L('Cookie前缀不能为空'));
		}
		if(!$hConn=mysql_connect($arrConfig['DB_HOST'],$arrConfig['DB_USER'],$arrConfig['DB_PASSWORD'])){
			$this->E(G::L('数据库服务器或登录密码无效').",".G::L('无法连接数据库，请重新设定！'));
		}
		if(!mysql_select_db($arrConfig['DB_NAME'])){
			$this->E(G::L('选择数据库失败，可能是你没权限，请预先创建一个数据库！'));
		}
		$sRs=mysql_query("SELECT VERSION();",$hConn);
		$arrRow=mysql_fetch_array($sRs);
		$arrMysqlVersions=explode('.',trim($arrRow[0]));
		$nMysqlVersion=$arrMysqlVersions[0].".".$arrMysqlVersions[1];
		mysql_query("SET NAMES 'UTF8',character_set_client=binary,sql_mode='';",$hConn);

		$sDbPrefix=$arrConfig['DB_PREFIX'];
		$sQuery=<<<EOT

DROP TABLE IF EXISTS `{$sDbPrefix}pluginvar`;

CREATE TABLE `{$sDbPrefix}pluginvar` (
`pluginvar_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '变量ID',
`plugin_id` smallint(6) unsigned NOT NULL default '0' COMMENT '插件ID',
`pluginvar_displayorder` tinyint(3) NOT NULL default '0' COMMENT '变量显示顺序',
`pluginvar_title` varchar(100) NOT NULL default '' COMMENT '变量的标题',
`pluginvar_description` varchar(255) NOT NULL default '' COMMENT '配置变量的描述',
`pluginvar_variable` varchar(40) NOT NULL default '' COMMENT '配置变量名',
`pluginvar_value` text NOT NULL COMMENT '配置变量的值',
`pluginvar_type` varchar(20) NOT NULL COMMENT '变量配置类型',
`pluginvar_extra` text NOT NULL COMMENT '变量配置扩展，用于select',
PRIMARY KEY (`pluginvar_id`),
KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

EOT;

		$sMessage='<br/>';
		foreach(explode(";\n",trim($sQuery)) as $sQuery2){
			$sQuery2=trim($sQuery2);
			mysql_query($sQuery2,$hConn);
		}
		$sMessage.=G::L('添加数据表%s完毕','update',null,"{$sDbPrefix}pluginvar").' ... '.G::L('成功').'<br/>';
		$sMessage.=G::L('添加新的数据表完毕','update').' ... '.G::L('成功').'<br/>';
		$this->assign('__MessageTitle__',G::L('添加新表成功，请勿关闭窗口，程序自动继续','update'));
		$this->assign('__JumpUrl__',G::U('update/second'));
		$this->assign('__WaitSecond__',3);
		$this->S($sMessage);
	}

	public function second(){
		$arrConfig=(array)(include DYHB_PATH.'/../Common/Config.inc.php');
		if(empty($arrConfig['RBAC_DATA_PREFIX'])){
			$this->E(G::L('Rbac前缀不能为空'));
		}
		if(empty($arrConfig['COOKIE_PREFIX'])){
			$this->E(G::L('Cookie前缀不能为空'));
		}
		if(!$hConn=mysql_connect($arrConfig['DB_HOST'],$arrConfig['DB_USER'],$arrConfig['DB_PASSWORD'])){
			$this->E(G::L('数据库服务器或登录密码无效').",".G::L('无法连接数据库，请重新设定！'));
		}
		if(!mysql_select_db($arrConfig['DB_NAME'])){
			$this->E(G::L('选择数据库失败，可能是你没权限，请预先创建一个数据库！'));
		}
		$sRs=mysql_query("SELECT VERSION();",$hConn);
		$arrRow=mysql_fetch_array($sRs);
		$arrMysqlVersions=explode('.',trim($arrRow[0]));
		$nMysqlVersion=$arrMysqlVersions[0].".".$arrMysqlVersions[1];
		mysql_query("SET NAMES 'UTF8',character_set_client=binary,sql_mode='';",$hConn);

		$sDbPrefix=$arrConfig['DB_PREFIX'];
		$sQuery=<<<EOT
ALTER TABLE `{$sDbPrefix}plugin`
	ADD `plugin_identifier` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '插件唯一识别' AFTER `plugin_name`,
	ADD `plugin_module` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块' AFTER `plugin_dir`,
	ADD `plugin_copyright` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版权信息' AFTER `plugin_module`,
	CHANGE `plugin_author` `plugin_author` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '作者',
	CHANGE `plugin_version` `plugin_version` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本',
	ADD INDEX(`plugin_identifier`);

ALTER TABLE `{$sDbPrefix}category`
	ADD `category_template` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分类主题' AFTER `category_columns`;
EOT;
		$sMessage='<br/>';
		foreach(explode(";\n",trim($sQuery)) as $sQuery2){
			$sQuery2=trim($sQuery2);
			mysql_query($sQuery2,$hConn);
		}
		$sMessage.=G::L('更新数据库结构','update').' ... '.G::L('成功').'<br/>';
		$this->assign('__MessageTitle__',G::L('更新数据库结构成功，请勿关闭窗口，程序自动继续','update'));
		$this->assign('__JumpUrl__',G::U('update/three'));
		$this->assign('__WaitSecond__',3);
		$this->S($sMessage);
	}

	public function three(){
		$arrConfig=(array)(include DYHB_PATH.'/../Common/Config.inc.php');
		if(empty($arrConfig['RBAC_DATA_PREFIX'])){
			$this->E(G::L('Rbac前缀不能为空'));
		}
		if(empty($arrConfig['COOKIE_PREFIX'])){
			$this->E(G::L('Cookie前缀不能为空'));
		}
		if(!$hConn=mysql_connect($arrConfig['DB_HOST'],$arrConfig['DB_USER'],$arrConfig['DB_PASSWORD'])){
			$this->E(G::L('数据库服务器或登录密码无效').",".G::L('无法连接数据库，请重新设定！'));
		}
		if(!mysql_select_db($arrConfig['DB_NAME'])){
			$this->E(G::L('选择数据库失败，可能是你没权限，请预先创建一个数据库！'));
		}
		$sRs=mysql_query("SELECT VERSION();",$hConn);
		$arrRow=mysql_fetch_array($sRs);
		$arrMysqlVersions=explode('.',trim($arrRow[0]));
		$nMysqlVersion=$arrMysqlVersions[0].".".$arrMysqlVersions[1];
		mysql_query("SET NAMES 'UTF8',character_set_client=binary,sql_mode='';",$hConn);

		$sDbPrefix=$arrConfig['DB_PREFIX'];
		$sQuery=<<<EOT
TRUNCATE `{$sDbPrefix}group`;
EOT;

		$sLang=strtolower(G::cookie(APP_NAME.'_language'));
		if($sLang=='en-us'){
			$sQuery=<<<EOT
INSERT INTO `{$sDbPrefix}node` (`node_id`, `node_name`, `node_title`, `node_status`, `node_remark`, `node_sort`, `node_parentid`, `node_level`, `node_type`, `group_id`, `create_dateline`, `update_dateline`) VALUES
(43, 'admin@resource', 'Extend', 1, '', 0, 1, 2, 0, 10, 1331134585, 1331134674),
(44, 'admin@plugin', 'Plugin', 1, '', 1, 1, 2, 0, 10, 1331135507, 0);

INSERT INTO `{$sDbPrefix}group` (`group_id`, `group_name`, `group_title`, `create_dateline`, `update_dateline`, `group_status`, `group_sort`, `group_show`) VALUES
(1, 'rbac', 'Right', 1296454621, 1323676504, 1, 5, 1),
(2, 'blog', 'Con', 1309436022, 1323676504, 1, 2, 1),
(3, 'option', 'Set', 1312119640, 1323676504, 1, 1, 1),
(4, 'admin', 'Web', 1312119691, 1323676504, 1, 8, 1),
(6, 'upload', 'Media', 1312119780, 1323676504, 1, 4, 1),
(7, 'run', 'Ope', 1312119811, 1323676504, 1, 7, 1),
(8, 'theme', 'Theme', 1312119847, 1323676504, 1, 3, 1),
(9, 'adv', 'Adv', 1312121353, 1323676504, 1, 6, 1),
(10, 'plugin', 'Plu', 1331134399, 1331134427, 1, 9, 1);
EOT;
		}elseif($sLang=='zh-tw'){
			$sQuery=<<<EOT
INSERT INTO `{$sDbPrefix}node` (`node_id`, `node_name`, `node_title`, `node_status`, `node_remark`, `node_sort`, `node_parentid`, `node_level`, `node_type`, `group_id`, `create_dateline`, `update_dateline`) VALUES
(43, 'admin@resource', '擴展中心', 1, '', 0, 1, 2, 0, 10, 1331134585, 1331134674),
(44, 'admin@plugin', '插件列表', 1, '', 1, 1, 2, 0, 10, 1331135507, 0);

INSERT INTO `{$sDbPrefix}group` (`group_id`, `group_name`, `group_title`, `create_dateline`, `update_dateline`, `group_status`, `group_sort`, `group_show`) VALUES
(1, 'rbac', '權限', 1296454621, 1323676504, 1, 5, 1),
(2, 'blog', '內容', 1309436022, 1323676504, 1, 2, 1),
(3, 'option', '設置', 1312119640, 1323676504, 1, 1, 1),
(4, 'admin', '站長', 1312119691, 1323676504, 1, 8, 1),
(6, 'upload', '媒體', 1312119780, 1323676504, 1, 4, 1),
(7, 'run', '運營', 1312119811, 1323676504, 1, 7, 1),
(8, 'theme', '界面', 1312119847, 1323676504, 1, 3, 1),
(9, 'adv', '高級', 1312121353, 1323676504, 1, 6, 1),
(10, 'plugin', '插件', 1331134399, 1331134427, 1, 9, 1);
EOT;
		}else{
			$sQuery=<<<EOT
INSERT INTO `{$sDbPrefix}node` (`node_id`, `node_name`, `node_title`, `node_status`, `node_remark`, `node_sort`, `node_parentid`, `node_level`, `node_type`, `group_id`, `create_dateline`, `update_dateline`) VALUES
(43, 'admin@resource', '扩展中心', 1, '', 0, 1, 2, 0, 10, 1331134585, 1331134674),
(44, 'admin@plugin', '插件列表', 1, '', 1, 1, 2, 0, 10, 1331135507, 0);

INSERT INTO `{$sDbPrefix}group` (`group_id`, `group_name`, `group_title`, `create_dateline`, `update_dateline`, `group_status`, `group_sort`, `group_show`) VALUES
(1, 'rbac', '权限', 1296454621, 1323676504, 1, 5, 1),
(2, 'blog', '内容', 1309436022, 1323676504, 1, 2, 1),
(3, 'option', '设置', 1312119640, 1323676504, 1, 1, 1),
(4, 'admin', '站长', 1312119691, 1323676504, 1, 8, 1),
(6, 'upload', '媒体', 1312119780, 1323676504, 1, 4, 1),
(7, 'run', '运营', 1312119811, 1323676504, 1, 7, 1),
(8, 'theme', '界面', 1312119847, 1323676504, 1, 3, 1),
(9, 'adv', '高级', 1312121353, 1323676504, 1, 6, 1),
(10, 'plugin', '插件', 1331134399, 1331134427, 1, 9, 1);
EOT;
		}

		$sQuery=<<<EOT
INSERT INTO `{$sDbPrefix}option` (`option_name`,`option_value`) VALUES
('attach_expire_hour', ''),
('footer_extend_message', '');
EOT;

		$sMessage='<br/>';
		foreach(explode(";\n",trim($sQuery)) as $sQuery2){
			$sQuery2=trim($sQuery2);
			mysql_query($sQuery2,$hConn);
		}
		$sMessage.=G::L('写入新的数据信息','update').' ... '.G::L('成功').'<br/>';
		$this->assign('__MessageTitle__',G::L('写入新的数据信息，请勿关闭窗口，程序自动继续','update'));
		$this->assign('__JumpUrl__',G::U('update/four'));
		$this->assign('__WaitSecond__',3);
		$this->S($sMessage);
	}

	public function four(){
		$hFp=fopen($this->_sUpdatefile,'w');
		fwrite($hFp,'ok');
		fclose($hFp);
		$this->display('four');
	}

}
