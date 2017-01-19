<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	数据库备份处理控制器($)*/

!defined('DYHB_PATH') && exit;

class BackupController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以对数据库进行备份，时常备份是一个非常好的习惯，这样防止数据丢失非常有帮助。','backup').'</p>'.
				'<p>'.G::L('点击自定义备份，你可以选择性地备份数据库表。','backup').'</p>'.
				'<p>'.G::L('Extended Insert 方式备份方式备份的数据体积小，但是如果数据不完整（即使用分卷备份），无法恢复。一般请不要选择。','backup').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _runsql_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以直接用SQL 语句操作数据库。','backup').'</p>'.
				'<p>'.G::L('直接操作数据库是一个非常危险的事情。建议非专业人士请不要操作，否则将出现不可估量错误。','backup').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _restore_get_admin_help_description(){
		return '<p>'.G::L('这里你可以管理你的数据库备份文件，你可以恢复数据库，也可以删除数据库备份文件。','backup').'</p>'.
				'<p>'.G::L('恢复数据将会覆盖当前数据库，请谨慎操作，以免损害数据库。','backup').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _optimize_get_admin_help_description(){
		return '<p>'.G::L('这里你可以对当前数据库进行优化，优化操作将会清除碎片。','backup').'</p>'.
				'<p>'.G::L('优化数据库可以大大提高数据的运行速度和稳定性。','backup').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$oDb=Db::RUN();
		$arrTables=$oDb->getConnect()->getCol("SHOW TABLES LIKE '".$GLOBALS['_commonConfig_']['DB_PREFIX']."%'");
		$nAllowMaxSize=E::returnBytes(ini_get('upload_max_filesize'));// 单位为字节
		$nAllowMaxSize=$nAllowMaxSize / 1024;// 转换单位为 KB

		$nMask=$this->fileModeInfo(APP_PATH.'/App/Data/Backup');
		if($nMask===false){
			$this->assign('sWarning',G::L("备份目录不存在%s",'app',null,APP_PATH.'/App/Data/Backup'));
		}
		else if($nMask !=15){
			$sWarning=G::L("文件权限警告：",'app',null,APP_PATH.'/App/Data/Backup');
			if(($nMask & 1)< 1){
				$sWarning.=G::L('不可读');
			}
			if(($nMask & 2)< 1){
				$sWarning.=G::L('不可写');
			}
			if(($nMask & 4)< 1){
				$sWarning.=G::L('不可增加');
			}
			if(($nMask & 8)< 1){
				$sWarning.=G::L('不可修改');
			}
			$this->assign('sWarning',$sWarning);
		}
		$this->assign('arrTables',$arrTables);
		$this->assign('nVolSize',$nAllowMaxSize);
		$this->assign('sSqlName',Backup::getRandomName(). '.sql');
		$this->display();
	}

	public function dumpsql(){
		$oDb=Db::RUN();
		$nMask=$this->fileModeInfo(APP_PATH.'/App/Data/Backup');
		if($nMask===false){
			$this->assign('sWarning',G::L("备份目录不存在%s",'app',null,APP_PATH.'/App/Data/Backup'));
		}
		else if($nMask !=15){
			$sWarning=G::L("文件权限警告：",'app',null,APP_PATH.'/App/Data/Backup');
			if(($nMask & 1)< 1){
				$sWarning.=G::L('不可读');
			}
			if(($nMask & 2)< 1){
				$sWarning.=G::L('不可写');
			}
			if(($nMask & 4)< 1){
				$sWarning.=G::L('不可追加');
			}
			if(($nMask & 8)< 1){
				$sWarning.=G::L('不可修改');
			}
			$this->assign('sWarning',$sWarning);
		}

		@set_time_limit(300);
		$oConnect=$oDb->getConnect();
		$oBackup=new Backup($oConnect);
		$sRunLog=APP_PATH.'/App/Data/Backup/run.log';

		$sSqlFileName=G::getGpc('sql_file_name');
		if(empty($sSqlFileName)){
			$sSqlFileName=BackUp::getRandomName();
		}
		else{
			$sSqlFileName=str_replace("0xa",'',trim($sSqlFileName));// 过滤 0xa 非法字符
			$nPos=strpos($sSqlFileName,'.sql');
			if($nPos !==false){
				$sSqlFileName=substr($sSqlFileName,0,$nPos);
			}
		}

		$nMaxSize=G::getGpc('vol_size');
		$nMaxSize=empty($nMaxSize)? 0:intval($nMaxSize);
		$nVol=G::getGpc('vol');
		$nVol=empty($nVol)? 1:intval($nVol);
		$bIsShort=G::getGpc('ext_insert');
		$bIsShort=$bIsShort==0?false:true;
		$oBackup->setIsShort($bIsShort);
		$nAllowMaxSize=intval(@ini_get('upload_max_filesize'));//单位M
		if($nAllowMaxSize > 0 && $nMaxSize >($nAllowMaxSize * 1024)){
			$nMaxSize=$nAllowMaxSize * 1024;//单位K
		}
		if($nMaxSize > 0){
			$oBackup->setMaxSize($nMaxSize * 1024);
		}

		$sType=G::getGpc('type','P');
		$sType=empty($sType)? '':trim($sType);
		$arrTables=array();
		switch($sType){
			case 'full':
				$arrTemp=$oDb->getConnect()->getCol("SHOW TABLES LIKE '".$GLOBALS['_commonConfig_']['DB_PREFIX']."%'");
				foreach($arrTemp as $sTable){
					$arrTables[$sTable]=-1;
				}
				$oBackup->putTablesList($sRunLog,$arrTables);
				break;
			case 'custom':
				foreach(G::getGpc('customtables') as $sTable){
					$arrTables[$sTable]=-1;
				}
				$oBackup->putTablesList($sRunLog,$arrTables);
				break;
		}

		$arrTables=$oBackup->dumpTable($sRunLog,$nVol);
		if($arrTables===false){
			$this->E($oBackup->getErrorMessage());
		}

		if(empty($arrTables)){
			if($nVol > 1){
				if(!@file_put_contents(APP_PATH.'/App/Data/Backup/'.$sSqlFileName.'_'.$nVol.'.sql',$oBackup->getDumpSql())){
					$this->E(G::L("备份文件写入失败%s",'app',null,$sSqlFileName. '_'.$nVol.'.sql'));
				}
				$arrList=array();
				for($nI=1;$nI <=$nVol;$nI++){
					$arrList[]=array(
						'name'=>$sSqlFileName.'_'.$nI.'.sql',
						'href'=>__APPPUB__.'/Backup/'.$sSqlFileName.'_'.$nI.'.sql'
					);
				}
				$arrMessage=array(
					'list'=>$arrList
				);
				$this->sql_dump_message($arrMessage);
			}
			else{
				if(!@file_put_contents(APP_PATH.'/App/Data/Backup/'.$sSqlFileName. '.sql',$oBackup->getDumpSql())){
					$this->E(G::L('备份文件写入失败%s','app',null,$sSqlFileName.'_'.$nVol.'.sql'));
				};
				$arrList=array(
					array('name'=>$sSqlFileName.'.sql',
						'href'=>__APPPUB__.'/Backup/'. $sSqlFileName.'.sql'
					)
				);
				$arrMessage=array(
					'list'=>$arrList
				);
				$this->sql_dump_message($arrMessage);
			}
		}
		else{
			if(!@file_put_contents(APP_PATH.'/App/Data/Backup/'.$sSqlFileName.'_'.$nVol.'.sql',$oBackup->getDumpSql())){
				$this->E(G::L("备份文件无法写入%s",'app',null,$sSqlFileName.'_'.$nVol.'.sql'));
			}
			$arrList=array(
				'sql_file_name'=>$sSqlFileName,
				'vol_size'=>$nMaxSize,
				'vol'=>$nVol+1
			);
			$sLink=G::U('backup/dumpsql',$arrList);
			$arrMessage=array(
				'auto_link'=>$sLink,
				'auto_redirect'=>1,
				'done_file'=>$sSqlFileName.'_'.$nVol.'.sql',
				'list'=>$arrList
			);
			$this->sql_dump_message($arrMessage);
		}
	}

	private function sql_dump_message($arrMessage){
		$sBackMsg="<div style=\"text-align:center;margin:auto;\">
				<span style=\"font-size: 14px;font-weight: bold\">".G::L('备份成功了').(isset($arrMessage['done_file'])? $arrMessage['done_file']:'')."</span><br/>";

		if(isset($arrMessage['auto_redirect'])&& $arrMessage['auto_redirect']){
			$this->assign('__JumpUrl__',$arrMessage['auto_link']);
			$this->assign('__WaitSecond__',3);
		}
		else{
			if(is_array($arrMessage['list'])){
				foreach($arrMessage['list'] as $arrFile){
					$sBackMsg.="<a href=\"{$arrFile['href']}\">{$arrFile['name']}</a><br/>";
				}
			}
			$this->assign('__JumpUrl__',G::U('backup/restore'));
			$this->assign('__WaitSecond__',5);
		}
		$sBackMsg.="</div>";
		$this->S($sBackMsg);
	}

	public function runsql(){
		$sSql=G::getGpc('sql');
		if(!empty($sSql)){
			$this->assign('sSql',$sSql);
			$this->assign_sql($sSql);
		}
		$this->display();
	}

	private function assign_sql($sSql){
		$oDb=Db::RUN();
		$sSql=stripslashes($sSql);
		$sSql=str_replace("\r",'',$sSql);
		$arrQueryItems=explode(";\n",$sSql);
		$arrQueryItems=array_filter($arrQueryItems,'strlen');
		if(count($arrQueryItems)> 1){
			foreach($arrQueryItems as $sKey=>$sValue){
				if($oDb->getConnect()->query($sValue)){
					$this->assign('nType',1);
				}
				else{
					$this->assign('nType', 0);
					$this->assign('sError',$oDb->getConnect()->getErrorMessage());
					return;
				}
			}
			return;
		}

		if(preg_match("/^(?:UPDATE|DELETE|TRUNCATE|ALTER|DROP|FLUSH|INSERT|REPLACE|SET|CREATE)\\s+/i",$sSql)){// 执行，但不返回结果型
			if($oDb->getConnect()->query($sSql)){
				$this->assign('nType', 1);
			}
			else{
				$this->assign('nType', -1);
				$this->assign('sError',$oDb->getConnect()->getErrorMessage());
			}
		}
		else{
			$arrData=$oDb->getConnect()->getAllRows($sSql);
			if($arrData===FALSE){
				$this->assign('nType', -1);
				$this->assign('sError',$oDb->getConnect()->getErrorMessage());
			}
			else{
				$sResult='';
				if(is_array($arrData)&& isset($arrData[0])){
					$sResult="<table class=\"data full\" id=\"checkList\"> \n<thead> \n<tr>";
					$arrKeys=array_keys($arrData[0]);
					$nNum=count($arrKeys);
					for($nI=0;$nI < $nNum;$nI++){
						$sResult.="<th>".$arrKeys[$nI]."</th>\n";
					}
					$sResult.="</tr> \n</thead>\n<tbody>\n";
					foreach($arrData as $arrData1){
						$sResult.="<tr>\n";
						foreach($arrData1 as $sValue){
							$sResult.="<td>".$sValue."</td>";
						}
						$sResult.="</tr>\n";
					}
					$sResult.="</tbody></table>\n";
				}
				else{
					$sResult="<center><h3>".G::L("没有发现任何记录!")."</h3></center>";
				}
				$this->assign('nType',	2);
				$this->assign('sResult',$sResult);
			}
		}
	}

	public function optimize(){
		$oDb=Db::RUN();
		$nDbVer=$oDb->getConnect()->getVersion();
		$sSql="SHOW TABLE STATUS LIKE '" .$GLOBALS['_commonConfig_']['DB_PREFIX']. "%'";
		$nNum=0;
		$arrList=array();
		$arrReuslt=$oDb->getConnect()->getAllRows($sSql);
		foreach($arrReuslt as $arrRow){
			if(strpos($arrRow['Name'],'_session')!==false){
				$arrRes['Msg_text']='Ignore';
				$arrRow['Data_free']='Ignore';
			}
			else{
				$arrRes=$oDb->getConnect()->getRow('CHECK TABLE '.$arrRow['Name'],null,false);
				$nNum+=$arrRow['Data_free'];
			}
			$sType=$nDbVer >='4.1'?$arrRow['Engine']:$arrRow['Type'];
			$sCharset=$nDbVer >='4.1'?$arrRow['Collation']:'N/A';
			$arrList[]=array('table'=>$arrRow['Name'],'type'=>$sType,'rec_num'=>$arrRow['Rows'],'rec_size'=>sprintf(" %.2f KB",$arrRow['Data_length'] / 1024),'rec_index'=>$arrRow['Index_length'], 'rec_chip'=>$arrRow['Data_free'],'status'=>$arrRes['Msg_text'],'charset'=>$sCharset);
		}
		unset($arrReuslt,$sCharset,$sType);
		$this->assign('arrList',$arrList);
		$this->assign('nNum',$nNum);
		$this->display();
	}

	public function run_optimize(){
		$oDb=Db::RUN();
		$arrTables=$oDb->getConnect()->getCol("SHOW TABLES LIKE '".$GLOBALS['_commonConfig_']['DB_PREFIX']."%'");
		$sResult='';
		foreach($arrTables as $sTable){
			if($arrRow=$oDb->getConnect()->getRow('OPTIMIZE TABLE '.$sTable,null,false)){
				if($arrRow['Msg_type']=='error' && strpos($arrRow['Msg_text'],'repair')!==false){
					$sResult.=G::L("优化数据库表%s失败",'app',null,$sTable).'<br/>';
					if($oDb->getConnect()->query('REPAIR TABLE '.$sTable)){
						$sResult.=G::L("优化失败后，尝试修复数据库%s成功",'app',null,$sTable).'<br/>';
					}
					else{
						$sResult.=G::L("优化失败后，尝试修复数据库%s失败",'app',null,$sTable).'<br/>';
					}
				}
				else{
					$sResult.=G::L("优化数据库表%s成功",'app',null,$sTable).'<br/>';
				}
				foreach(G::getGpc('do','P')as $sDo){
					if($oDb->query($sDo.' TABLE '.$sTable)){
						$sResult.=$sDo.G::L("数据库表%s成功",'app',null,$sTable).'<br/>';
					}
					else{
						$sResult.=$sDo.G::L("数据库表%s失败",'app',null,$sTable).'<br/>';
					}
				}
				$sResult.='<br/><br/>';
			}
		}
		$this->assign('__WaitSecond__',10);
		$this->S(G::L("数据表优化成功，共清理碎片  %d",'app',null,G::getGpc('num')).'<br/><br/>'.G::L('附加信息').": ".$sResult);
	}

	public function restore(){
		$arrList=array();
		$nMask=$this->fileModeInfo(APP_PATH.'/App/Data/Backup');
		if($nMask===false){
			$this->assign('sWarning',G::L("备份目录不存在%s",'app',null,APP_PATH.'/App/Data/Backup'));
		}
		elseif(($nMask & 1)< 1){
			$this->assign('sWarning',G::L('不可读'));
		}
		else{
			$arrRealList=array();
			$hFolder=opendir(APP_PATH.'/App/Data/Backup');
			while($sFile=readdir($hFolder)){
				if(strpos($sFile,'.sql')!==false){
					$arrRealList[]=$sFile;
				}
			}
			natsort($arrRealList);
			$arrMatch=array();
			foreach($arrRealList as $sFile){
				if(preg_match('/_([0-9])+\.sql$/',$sFile,$arrMatch)){
					if($arrMatch[1]==1){
						$nMark=1;
					}
					else{
						$nMark=2;
					}
				}
				else{
					$nMark=0;
				}
				$nFileSize=filesize(APP_PATH.'/App/Data/Backup/'. $sFile);
				$arrInfo=Backup::getHead(APP_PATH.'/App/Data/Backup/'. $sFile);
				$arrList[]=array(
					'name'=>$sFile,
					'add_time'=>$arrInfo['date'],
					'vol'=>$arrInfo['vol'],
					'file_size'=>numBitunit($nFileSize),
					'mark'=>$nMark
				);
			}
		}
		$this->assign('arrList',$arrList);
		$this->display();
	}

	public function remove(){
		$arrFile=G::getGpc('file');
		if(!empty($arrFile)){
			$arrMFile=array();//多卷文件
			$arrSFile=array();//单卷文件
			foreach($arrFile as $sFile){
				if(preg_match('/_[0-9]+\.sql$/',$sFile)){
					$arrMFile[]=substr($sFile,0,strrpos($sFile,'_'));
				}
				else{
					$arrSFile[]=$sFile;
				}
			}
			if($arrMFile){
				$arrMFile=array_unique($arrMFile);
				$arrRealFile=array();
				$hFolder=opendir(APP_PATH.'/App/Data/Backup');
				while($sFile=readdir($hFolder)){
					if(preg_match('/_[0-9]+\.sql$/',$sFile)&& is_file(APP_PATH.'/App/Data/Backup/'. $sFile)){
						$arrRealFile[]=$sFile;
					}
				}
				foreach($arrRealFile as $sFile){
					$sShortFile=substr($sFile,0,strrpos($sFile,'_'));
					if(in_array($sShortFile,$arrMFile)){
						@unlink(APP_PATH.'/App/Data/Backup/'. $sFile);
					}
				}
			}
			if($arrSFile){
				foreach($arrSFile as $sFile){
					@unlink(APP_PATH.'/App/Data/Backup/'. $sFile);
				}
			}
			$this->S(G::L("删除备份文件成功了"));
		}
		else{
			$this->E(G::L("你没有选择任何文件"));
		}
	}

	public function import(){
		$oDb=Db::RUN();
		$bIsContrim=G::getGpc('confirm');
		$bIsConfirm=empty($bIsContrim)? false:true;
		$sFileName=G::getGpc('file_name');
		$sFileName=empty($sFileName)? '': trim($sFileName);
		@set_time_limit(300);
		if(preg_match('/_[0-9]+\.sql$/',$sFileName)){
			if($bIsConfirm==false){
				$sUrl=G::U('backup/import?confirm=1&file_name='. $sFileName);
				$this->assign("__JumpUrl__",$sUrl);
				$this->assign('__WaitSecond__',60);
				$this->S(G::L("你确定要导入? &nbsp;<a href='%s'>确定</a>",'app',null,$sUrl));
			}
			$sShortName=substr($sFileName,0,strrpos($sFileName,'_'));
			$arrRealFile=array();
			$hFolder=opendir(APP_PATH.'/App/Data/Backup');
			while($sFile=readdir($hFolder)){
				if(is_file(APP_PATH.'/App/Data/Backup/'.$sFile)&& preg_match('/_[0-9]+\.sql$/',$sFile)){
					$arrRealFile[]=$sFile;
				}
			}
			$arrPostList=array();
			foreach($arrRealFile as $sFile){
				$sTmpName=substr($sFile,0,strrpos($sFile,'_'));
				if($sTmpName==$sShortName){
					$arrPostList[]=$sFile;
				}
			}
			natsort($arrPostList);
			foreach($arrPostList as $sFile){
				$arrInfo=Backup::getHead(APP_PATH.'/App/Data/Backup/'. $sFile);
				if(!$this->sql_import(APP_PATH.'/App/Data/Backup/'. $sFile)){
					$this->E(G::L("导入数据库备份文件失败").'<br/>'.$oDb->getConnect()->getErrorMessage());
				}
			}
			$this->assign("__JumpUrl__",G::U('backup/restore'));
			$this->S(G::L("数据导入成功"));
		}
		else{

			$arrInfo=Backup::getHead(APP_PATH.'/App/Data/Backup/'. $sFileName);
			if($this->sql_import(APP_PATH.'/App/Data/Backup/'. $sFileName)){
				$this->assign("__JumpUrl__",G::U('backup/restore'));
				$this->S(G::L("数据导入成功"));
			}
			else{
				$this->E(G::L("导入数据库备份文件失败").'<br/>'.$oDb->getConnect()->getErrorMessage());
			}
		}
	}

	public function upload_sql(){
		$oDb=Db::RUN();
		$sSqlFile=APP_PATH.'/App/Data/Backup/upload_database_bak.sql';
		$sSqlVerConfirm=G::getGpc('sql_ver_confirm');
		if(empty($sSqlVerConfirm)){
			$arrSqlfile=G::getGpc('sqlfile','F');
			if(empty($arrSqlfile)){
				$this->E(G::L("你没有选择任何文件"));
			}
			if((isset($arrSqlfile['error'])
				&& $arrSqlfile['error'] > 0)
				||(!isset($arrSqlfile['error'])
				&& $arrSqlfile['tmp_name']=='none')){
				$this->E(G::L('上传文件失败'));
			}

			if($arrSqlfile['type']=='application/x-zip-compressed'){
				$this->E(G::L("不能是zip格式"));
			}
			if(!preg_match("/\.sql$/i",$arrSqlfile['name'])){
				$this->E(G::L("不是sql格式"));
			}

			if(is_file($sSqlFile)){
				unlink($sSqlFile);
			}
			if(!move_uploaded_file($arrSqlfile['tmp_name'],$sSqlFile)){
				$this->E(G::L("文件移动失败"));
			}
		}

		// 获取sql文件头部信息
		$arrSqlInfo=Backup::getHead($sSqlFile);
		if(empty($sSqlVerConfirm)){// 检查数据库版本是否正确
			if(empty($arrSqlInfo['database_ver'])){
				$this->E(G::L("没有确定数据库版本"));
			}
			else{
				$nSqlVer=$oDb->getConnect()->getVersion();
				if($arrSqlInfo['database_ver'] !=$nSqlVer){
					$sMessage="<a href='".G::U('backup/upload_sql?sql_ver_confrim=1')."'>".G::L("重试")."</a></br>< href='".G::U('backup/restore')."'>".G::L("返回")."</a>";
					$this->E($sMessage);
				}
			}
		}

		@set_time_limit(300);
		if($this->sql_import($sSqlFile)){
			if(is_file($sSqlFile)){
				unlink($sSqlFile);
			}
			$this->S(G::L('数据库导入成功'));
		}
		else{
			if(is_file($sSqlFile)){
				unlink($sSqlFile);
			}
			$this->E(G::L('数据库导入失败'));
		}
	}

	private function sql_import($sSqlFile){
		$oDb=Db::RUN();
		$nDbVer=$oDb->getConnect()->getVersion();
		$sSqlStr=array_filter(file($sSqlFile),'removeComment');
		$sSqlStr=str_replace("\r",'',implode('',$sSqlStr));
		$arrRet=explode(";\n",$sSqlStr);
		$nRetCount=count($arrRet);
		if($nDbVer > '4.1'){
			for($nI=0;$nI < $nRetCount;$nI++){
				$arrRet[$nI]=trim($arrRet[$nI]," \r\n;");//剔除多余信息
				if(!empty($arrRet[$nI])){
					if((strpos($arrRet[$nI],'CREATE TABLE')!==false)&&(strpos($arrRet[$nI],'DEFAULT CHARSET='. str_replace('-','',$GLOBALS['_commonConfig_']['DB_CHAR']))===false)){
						// 建表时缺 DEFAULT CHARSET=utf8
						$arrRet[$nI]=$arrRet[$nI].'DEFAULT CHARSET='. str_replace('-','',$GLOBALS['_commonConfig_']['DB_CHAR']);
					}
					$oDb->getConnect()->query($arrRet[$nI]);
				}
			}
		}
		else{
			for($nI=0;$nI < $nRetCount;$nI++){
				$arrRet[$nI]=trim($arrRet[$nI]," \r\n;");//剔除多余信息
				if((strpos($arrRet[$nI],'CREATE TABLE')!==false)&&(strpos($arrRet[$nI],'DEFAULT CHARSET='. str_replace('-','',$GLOBALS['_commonConfig_']['DB_CHAR']))!==false)){
					$arrRet[$nI]=str_replace('DEFAULT CHARSET='. str_replace('-','',$GLOBALS['_commonConfig_']['DB_CHAR']),'',$arrRet[$nI]);
				}
				if(!empty($arrRet[$nI])){
					$oDb->getConnect()->query($arrRet[$nI]);
				}
			}
		}
		return true;
	}

	protected function fileModeInfo($sFilePath){
		if(!file_exists($sFilePath)){// 如果不存在，则不可读、不可写、不可改
			return false;
		}
		$nMark=0;
		if(strtoupper(substr(PHP_OS,0,3))=='WIN'){
			$sTestFile=$sFilePath.'/test.txt';// 测试文件
		if(is_dir($sFilePath)){// 如果是目录
			$sDir=@opendir($sFilePath);// 检查目录是否可读
			if($sDir===false){
				return $nMark;//如果目录打开失败，直接返回目录不可修改、不可写、不可读
			}
			if(@readdir($sDir)!==false){
				$nMark^=1; //目录可读 001，目录不可读 000
			}
			@closedir($sDir);
			$hFp=@fopen($sTestFile,'wb');// 检查目录是否可写/
			if($hFp===false){
				return $nMark; //如果目录中的文件创建失败，返回不可写。
			}
			if(@fwrite($hFp,'access test!')!==false){
				$nMark^=2; //目录可写可读011，目录可写不可读 010
			}
			@fclose($hFp);
			@unlink($sTestFile);
			$hFp=@fopen($sTestFile,'ab+');// 检查目录是否可修改
			if($hFp===false){
				return $nMark;
			}
			if(@fwrite($hFp,"modify test.\r\n")!==false){
				$nMark^=4;
			}
			@fclose($hFp);
				if(@rename($sTestFile, $sTestFile)!==false){// 检查目录下是否有执行rename()函数的权限
					$nMark^=8;
				}
				@unlink($sTestFile);
			}
			elseif(is_file($sFilePath)){// 如果是文件
				$hFp=@fopen($sFilePath,'rb');// 以读方式打开
				if($hFp){
					$nMark^=1; //可读 001
				}
				@fclose($hFp);
				$hFp=@fopen($sFilePath,'ab+');// 试着修改文件

				if($hFp && @fwrite($hFp,'')!==false){
					$nMark^=6; //可修改可写可读 111，不可修改可写可读011...
				}
				@fclose($hFp);
				if(@rename($sTestFile,$sTestFile)!==false){// 检查目录下是否有执行rename()函数的权限
					$nMark^=8;
				}
			}
		}
		else{
			if(@is_readable($sFilePath)){
				$nMark^=1;
			}
			if(@is_writable($sFilePath)){
				$nMark^=14;
			}
		}
		return $nMark;
	}

}

function removeComment($sVar){
	return(substr($sVar,0,2)!='--');
}
