<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   应用程序创建类($) */

class GeneratorApplication{

	private $_arrContentSearch=array('%DYHB_PATH%','%APP_NAME%','%APP_PATH%','%APP_NOTES%');
	private $_arrContentReplace=array();
	private $_bByEnter=false;

	public function APP($sAppName,$sAppPath,$sEnter=null,$sTestAppDir=null,$bByEnter=false){
		$this->_bByEnter=$bByEnter;
		if($sEnter===null) $sEnter='';
		if(empty($sTestAppDir)){// 源目录
			$sTestAppDir=dirname(__FILE__). '/TestApp';
		}
		$sAppName=preg_replace('/[^a-z0-9_]+/i','',$sAppName);// 项目名判断
		if(empty($sAppName)){
			$this->formatError(sprintf('Invalid appname "%s".',$sAppName));
		}
		if(!is_dir($sAppPath)){// 项目路径判断
			$this->formatError(sprintf('Invalid dir "%s".',$sAppPath));
		}
		clearstatcache(); // 清除文件状态缓存
		$sAppPath=rtrim(realpath($sAppPath),'/\\');
		$sAppDir=$sAppPath.'/'.$sAppName;
		if($this->_bByEnter===false and is_dir($sAppDir)){// 检测项目路径
			$this->formatError(sprintf('Application dir "%s" already exists.',$sAppDir));
		}
		if(!is_dir($sAppDir) && !mkdir($sAppDir,0777)){// 创建项目
			$this->formatError(sprintf('Creation application dir "%s" failed.',$sAppDir));
		}
		if($sEnter==''){// 获取入口文件到框架入口文件之间的相对路径
			$sDyhbPath=self::getRelativePath($sAppDir.'/Index',dirname(dirname(dirname(__FILE__))));
		}
		else{
			$sEnter=self::tidyPath($sEnter);
			if(is_file($sEnter)){// 判断入口文件是否已经存在，防止覆盖别的文件
				rmdir($sAppDir);
				$this->formatError(sprintf('Application enter file "%s" already exists.',$sEnter));
			}
			$sDyhbPath=self::getRelativePath($sEnter,dirname(dirname(dirname(__FILE__))));
		}
		if($sEnter!=''){
			$sAppPathRelation=self::getRelativePath($sEnter,$sAppDir);
		}
		else{
			$sAppPathRelation='';
		}
		if($sAppPathRelation){
			$sAppNotes='';
			$sAppPathContent="define('APP_PATH',dirname(__FILE__).'/{$sAppPathRelation}'); ";
		}
		else{
			$sAppNotes='//';
			$sAppPathContent="//define('APP_PATH',dirname(__FILE__)); ";
		}
		$this->_arrContentReplace=array(($sDyhbPath ? $sDyhbPath.'/' :'').'DoYouHaoBaby.php',$sAppName,$sAppPathContent,$sAppNotes);
		echo "Building application {$sAppName}......\n\n";
		$this->copyDir_($sTestAppDir,$sAppDir,$sEnter);
		echo "\nSuccessed.\n\n";
	}

	protected function copyDir_($sSource,$sTarget,$sEnter=''){
		$sSource=rtrim($sSource,'/\\'). '/';// 整理路径
		$sTarget=rtrim($sTarget,'/\\'). '/';
		$h=opendir($sSource);// 打开源目录
		$arrSkip=array('.','..','.svn','.cvs','_svn'); // 过滤svn
		while(($file=readdir($h))!==false){// 读取
			if(in_array($file,$arrSkip))continue;// 如果是svn，则路过
			if($file=='index.php' and  $this->_bByEnter===true)continue;// 如果是入口文件创建项目，路过入口文件
			$path=$sSource.$file;// 源文件路径
			if($file=='index.php' and  $this->_bByEnter===true){}
			elseif($file=='index.php' and $sEnter!=''){
				echo '  create ',$sEnter;
			}else{
				echo '  create ',$sTarget . $file;
			}
			echo "\n";
			if(is_dir($path)){// 如果是目录，则递归调用
				if(!is_dir($sTarget.$file)){// 已经存在文件，直接路过
					mkdir($sTarget.$file,0777);
				}
				$this->copyDir_($path,$sTarget . $file);
			}else{// 文件，则执行拷贝
				// 读取源文件
				$nFilesize=filesize($path);
				if($nFilesize){
					$hFp=fopen($path,'rb');
					$sContent=fread($hFp,$nFilesize);
					fclose($hFp);
				}else{
					$sContent='';
				}
				$sExtname=strtolower(pathinfo($path,PATHINFO_EXTENSION));// 替换代码
				if($sExtname=='php' || $sExtname=='phpd'){
					$sContent=str_replace($this->_arrContentSearch,$this->_arrContentReplace,$sContent);
					$sContent=str_replace(array("\n\r","\r\n","\r"),"\n",$sContent);
				}
				if($file=='index.php' and  $sEnter!=''){// 写入数据，拷贝完成
					$sTargetFilePath=$sEnter;
				}else{
					$sTargetFilePath=$sTarget . $file;
				}
				if(file_exists($sTargetFilePath)){  continue; }
				$hFp=fopen($sTargetFilePath,'wb');
				if($hFp===false){
					$this->formatError(sprintf('Open file %s failed and we can not copy.',$sTargetFilePath,dirname($sTargetFilePath)));
				}
				fwrite($hFp,$sContent);
				fclose($hFp);
				chmod($sTargetFilePath,0666);
				unset($sContent);
			}
		}
		closedir($h);
	}

	static public function getRelativePath($sFromPath,$sToPath){
		if(is_file($sFromPath)){
			$sFrom=dirname($sFromPath);
		}else{
			$sFrom=&$sFromPath ;
		}
		$sFrom=self::tidyPath($sFrom);
		$sTo=self::tidyPath($sToPath);
		$arrFromPath=explode('/',$sFrom);
		$arrToPath=explode('/',$sTo);
		array_diff($arrFromPath,array(''));
		array_diff($arrToPath,array(''));
		$nSameLevel=0 ;
		while(
			($sFromOneDir=array_shift($arrFromPath))!==null
			and($sToOneDir=array_shift($arrToPath)) !==null
			and($sFromOneDir===$sToOneDir)
		)
		{
			$nSameLevel++;
		}
		if($sFromOneDir!==null){// 将 相同的 目录 压回 栈中
			array_unshift($arrFromPath,$sFromOneDir);
		}
		if($sToOneDir!==null){
			array_unshift($arrToPath,$sToOneDir);
		}
		if($nSameLevel<=0){// 不在 同一 磁盘驱动器 中(Windows 环境下)
			return null ;
		}
		$nLevel=count($arrFromPath)-1 ;
		$sRelativePath=($nLevel>0)? str_repeat('../',$nLevel): '' ;
		$sRelativePath.=implode('/',$arrToPath);
		$sRelativePath=rtrim($sRelativePath,'/');
		return $sRelativePath;
	}

	static public function tidyPath($sPath,$bUnix=true){
		$sRetPath=str_replace('\\','/',$sPath);
		$sRetPath=preg_replace('|/+|','/',$sRetPath);
		$arrDirs=explode('/',$sRetPath);
		$arrDirs2=array();
		while(($sDirName=array_shift($arrDirs))!==null){
			if($sDirName=='.'){
				continue ;
			}
			if($sDirName=='..'){
				if(count($arrDirs2)){
					array_pop($arrDirs2);
					continue ;
				}
			}
			array_push($arrDirs2,$sDirName);
		}
		$sRetPath=implode('/',$arrDirs2);
		if(is_dir($sRetPath)){
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.='/' ;
			}
		}
		else if(preg_match("|\.$|",$sPath)){
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.='/' ;
			}
		}
		$sRetPath=str_replace(':/',':\\',$sRetPath);
		if(!$bUnix){
			$sRetPath=str_replace('/','\\',$sRetPath);
		}
		$sRetPath=rtrim($sRetPath,'\\/');
		return $sRetPath ;
	}

	protected function formatError($sContent){
		E($sContent);
	}

}

if(!function_exists('E')){
	function E($sMessage){
		exit('DoYouHaoBaby Need Exit: '.$sMessage);
	}
}
