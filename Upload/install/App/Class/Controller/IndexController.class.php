<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   install Index控制器($)*/

!defined('DYHB_PATH')&& exit;

class IndexController extends Controller{

	protected $_sLockfile='';

	public function init__(){
		parent::init__();
		define('PROGRAM_INSTALL_PATH',dirname(APP_PATH));
		$this->_sLockfile=DYHB_PATH.'/../Common/Install.lock.php';
		if(file_exists($this->_sLockfile)){
			exit(G::L(" 程序已运行安装，如果你确定要重新安装，请先从FTP中删除 %s",'app',null,$this->_sLockfile));
		}
		$this->assign('sVersion','2.0.1');
		$this->assign('sVersionTime','20120325');
		$this->assign('sDyhbBlogXDatabase','dyhbblogxv201');
	}

	public function index(){
		$this->assign('arrInstallLangs',E::listDir(APP_PATH.'/App/Lang'));
		$this->display('step1');
	}

	public function step2(){
		$this->display('step2');
	}

	public function step3(){
		$this->display('step3');
	}

	public function step4(){
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

	public function step5(){
		if(!empty($_SERVER['HTTP_HOST'])){
			$sBaseurl='http://'.$_SERVER['HTTP_HOST'];
		}
		else{
			$sBaseurl="http://".$_SERVER['SERVER_NAME'];
		}
		$sBaseurl=$sBaseurl.__ROOT__;
		$this->assign('sBasepath',$sBaseurl);
		$this->assign('sBaseurl',$sBaseurl);
		$this->display('step5');
	}

	public function step6(){
		$sDbhost=trim(G::getGpc('dbhost'));
		$sDbuser=trim(G::getGpc('dbuser'));
		$sDbpwd=trim(G::getGpc('dbpwd'));
		$sDbname=trim(G::getGpc('dbname'));
		$sDbprefix=trim(G::getGpc('dbprefix'));
		$sAdminuser=trim(G::getGpc('adminuser'));
		$sAdminpwd=trim(G::getGpc('adminpwd'));
		$sCookieprefix=trim(G::getGpc('cookieprefix'));
		$sRbacprefix=trim(G::getGpc('rbacprefix'));
		if(empty($sAdminuser)){
			$this->E(G::L('管理员帐号不能为空'));
		}
		if(!preg_match('/^[a-z0-9\-\_]*[a-z\-_]+[a-z0-9\-\_]*$/i',$sAdminuser)){
			$this->E(G::L('管理员帐号只能是由英文,字母和下划线组成'));
		}
		if(empty($sAdminpwd)){
			$this->E(G::L('管理员密码不能为空'));
		}
		if(empty($sRbacprefix)){
			$this->E(G::L('Rbac前缀不能为空'));
		}
		if(empty($sCookieprefix)){
			$this->E(G::L('Cookie前缀不能为空'));
		}
		if(!$hConn=mysql_connect($sDbhost,$sDbuser,$sDbpwd)){
			$this->E(G::L('数据库服务器或登录密码无效').",".G::L('无法连接数据库，请重新设定！'));
		}
		mysql_query("CREATE DATABASE IF NOT EXISTS `".$sDbname."`;",$hConn);
		if(!mysql_select_db($sDbname)){
			$this->E(G::L('选择数据库失败，可能是你没权限，请预先创建一个数据库！'));
		}
		$sRs=mysql_query("SELECT VERSION();",$hConn);
		$arrRow=mysql_fetch_array($sRs);
		$arrMysqlVersions=explode('.',trim($arrRow[0]));
		$nMysqlVersion=$arrMysqlVersions[0].".".$arrMysqlVersions[1];
		mysql_query("SET NAMES 'UTF8',character_set_client=binary,sql_mode='';",$hConn);
		$arrConfig=(array)(include APP_PATH.'/App/Data/Images/Config.inc.bak.php');
		$arrConfig['DB_HOST']=$sDbhost;
		$arrConfig['DB_USER']=$sDbuser;
		$arrConfig['DB_PASSWORD']=$sDbpwd;
		$arrConfig['DB_NAME']=$sDbname;
		$arrConfig['DB_PREFIX']=$sDbprefix;
		$arrConfig['RBAC_DATA_PREFIX']=$sRbacprefix;
		$arrConfig['COOKIE_PREFIX']=$sCookieprefix;
		$sConfigData='<'."?php\r\n" ;
		$sConfigData.='/** DYHB.BLOG X Config.INT File,Do not to modify this file! */'."\r\n" ;
		$sConfigData.='return array('."\r\n";
		foreach($arrConfig as $sConfigKey=>$sConfigValue){
			$sConfigValue=$this->filter_option_value($sConfigValue);
			$sConfigData.="'{$sConfigKey}'=>{$sConfigValue},\r\n";
		}
		$sConfigData.=")\r\n";
		$sConfigData.="\r\n?".'>' ;
		if(!file_put_contents(PROGRAM_INSTALL_PATH.'/Common/Config.inc.php',$sConfigData)){
			$this->E((G::L('写入配置失败，请检查%s目录是否可写入！','app',null,PROGRAM_INSTALL_PATH.'/Common')));
		}
		$sSql4Tmp='';
		if($nMysqlVersion >=4.1){
			$sSql4Tmp="ENGINE=MyISAM DEFAULT CHARSET=UTF8";
		}
		$sMessage='<br/>';
		$sQuery='';
		$hFp=fopen(APP_PATH.'/App/Data/Images/Dyhbxblog.table.sql','r');
		while(!feof($hFp)){
			$sLine=rtrim(fgets($hFp,1024));
			if(preg_match("#;$#",$sLine)){
				$sQuery.=$sLine."\n";
				$sQuery=str_replace('#@__',$sDbprefix,$sQuery);
				if(substr($sQuery,0,12)=='CREATE TABLE'){
					$sTableName=preg_replace("/CREATE TABLE `([a-z0-9_]+)` .*/is","\\1",$sQuery);
					$sMessage.=G::L('创建数据库表').' '.$sTableName.' ... '.G::L('成功').'<br/>';
				}
				if($nMysqlVersion<4.1){
					$hRs=mysql_query($sQuery,$hConn);
				}
				else{
					if(preg_match('#CREATE#i',$sQuery)){
						$hRs=mysql_query(preg_replace("#TYPE=MyISAM#i",$sSql4Tmp,$sQuery),$hConn);
					}
					else{
						$hRs=mysql_query($sQuery,$hConn);
					}
				}
				$sQuery='';
			}
			else if(!preg_match("#^(\/\/|--)#",$sLine)){
				$sQuery.=$sLine;
			}
		}
		fclose($hFp);
		$sQuery='';
		$sDyhbxblogDataPath=APP_PATH.'/App/Data/Images/'.ucfirst(G::cookie(APP_NAME.'_language')).'/Dyhbxblog.data.sql';
		if(!is_file($sDyhbxblogDataPath)){
			$sDyhbxblogDataPath=APP_PATH.'/Public/App/Data/Zh-cn/Dyhbxblog.data.sql';
		}
		$hFp=fopen($sDyhbxblogDataPath,'r');
		while(!feof($hFp)){
			$sLine=rtrim(fgets($hFp,1024));
			if(preg_match("#;$#",$sLine)){
				$sQuery.=$sLine;
				$sQuery=str_replace('#@__',$sDbprefix,$sQuery);
				$hRs=mysql_query($sQuery,$hConn);
				$sQuery='';
			}
			else if(!preg_match("#^(\/\/|--)#",$sLine)){
				$sQuery.=$sLine;
			}
		}
		fclose($hFp);
		$sMessage.=G::L('导入系统初始化数据').' ... '.G::L('成功').'<br/>';
		mysql_query("Update `{$sDbprefix}option` set option_value='".trim(G::getGpc('baseurl'))."' where option_name='blog_url';",$hConn);
		$sMessage.=G::L('写入博客地址').' '.trim(G::getGpc('baseurl')).' ... '.G::L('成功').'<br/>';
		mysql_query("Update `{$sDbprefix}option` set option_value='".trim(G::getGpc('webname'))."' where option_name='blog_name';",$hConn);
		$sMessage.=G::L('写入博客名字').' '.trim(G::getGpc('webname')).' ... '.G::L('成功').'<br/>';
		mysql_query("Update `{$sDbprefix}option` set option_value='".trim(G::getGpc('adminmail'))."' where option_name='adminmail';",$hConn);
		$sMessage.=G::L('写入管理员邮件').' '.trim(G::getGpc('adminmail')).' ... '.G::L('成功').'<br/>';
		$sRandom=G::randString(6);
		$sPassword=md5(md5($sAdminpwd).trim($sRandom));
		mysql_query("Update `{$sDbprefix}user` set user_name='".$sAdminuser."',user_password='".$sPassword."',user_random='".$sRandom."',user_password='".$sPassword."',user_registerip='".E::getIp()."',user_email='".trim(G::getGpc('adminmail'))."',user_lastloginip='".E::getIp()."' where user_id=1;",$hConn);
		$sMessage.=G::L('初始化超级管理员帐号').'	... '.G::L('成功').'<br/>';
		mysql_query("Update `{$sDbprefix}countcache` set countcache_lastuser='".$sAdminuser."' where countcache_id=1;",$hConn);
		$sMessage.=G::L('更新新会员').'	... '.G::L('成功').'<br/>';
		$this->assign('sMessage',$sMessage);
		$this->display('step6');
	}

	private function filter_option_value($sValue){
		if($sValue===false){return 'FALSE';}
		if($sValue===true){return 'TRUE';}
		if($sValue==''){return '""';}
		$sValue=str_replace('"','\\"',$sValue);
		$sValue=str_replace("\n","\\n",$sValue);
		$sValue=str_replace("\r","\\r",$sValue);
		$sValue=str_replace('$','\\$',$sValue);
		return '"'.$sValue.'"';
	}

	public function step7(){
		$hFp=fopen($this->_sLockfile,'w');
		fwrite($hFp,'ok');
		fclose($hFp);
		$this->display('step7');
	}

	public function step10(){
		header("Pragma:no-cache\r\n");
		header("Cache-Control:no-cache\r\n");
		header("Expires:0\r\n");
		$sDbhost=G::getGpc('dbhost');
		$sDbuser=G::getGpc('dbuser');
		$sDbpwd=G::getGpc('dbpwd');
		$sDbname=G::getGpc('dbname');
		$hConn=@mysql_connect($sDbhost,$sDbuser,$sDbpwd);
		if($hConn){
			if(empty($sDbname)){
				$this->S("<font color='green'>".G::L('数据库连接成功')."</font>");
			}
			else{
				if(mysql_select_db($sDbname,$hConn)){
					$this->E("<font color='red'>".G::L('数据库已经存在，系统将覆盖数据库')."</font>");
				}
				else{
					$this->S("<font color='green'>".G::L('数据库不存在,系统将自动创建')."</font>");
				}
			}
		}
		else{
			$this->E("<font color='red'>".G::L('数据库连接失败！')."</font>");
		}
		@mysql_close($hConn);
		exit();
	}

}
