<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   包管理类($) */

!defined('DYHB_PATH') && exit;

class Package{

	static private $OBJECTS=array();
	static private $_arrClassRegex=array('/^(.+)\.class\.php$/i','/^(.+)\.interface\.php$/i');
	static private $_bAutoLoad=true;
	static private $CLASS_PATH='Class.inc';
	static private $_arrImportedPackDir=array();
	static private $_arrClassFilePat=array('%DirPath%/%ClassName%.class.php');
	static private $_arrInterPat=array('%DirPath%/%ClassName%.interface.php');
	static private $_arrInsPat=array('%DirPath%/%ClassName%.ins.php');
	static private $_arrNotFiles=array();
	static private $_arrTempPackagePath='';
	static private $CLASS_PATHS=array(
	'CORE'=>'LibPHP/Core',
	'MODEL'=>'LibPHP/Core/Model',
	'CONTROLLER'=>'LibPHP/Core/Controller',
	'CHECK'=>'LibPHP/Core/Check',
	'VIEW'=>'LibPHP/Core/View',
	'VIEWHTML'=>'LibPHP/Core/View/ViewHtml',
	'APP'=>'LibPHP/App',
	'CONFIG'=>'LibPHP/App/Lib/Config',
	'HTMLRESOURCE'=>'LibPHP/App/Lib/HtmlResource',
	'CACHE'=>'LibPHP/App/Package/Cache',
	'DS'=>'LibPHP/App/Package/Ds',
	'TEMPLATE'=>'LibPHP/App/Package/Template',
	'TEMPLATEHTML'=>'LibPHP/App/Package/Template/TemplateHtml',
	'COMPILER'=>'LibPHP/App/Package/Template/TemplateHtml/Compiler',
	);

	static public function import($sPackage,$bForce=false){
		if(!is_dir($sPackage)){
			G::E("Package:'{$sPackage}' does not exists.");
		}

		// 包路径
		self::$_arrTempPackagePath=$sPackagePath=realpath($sPackage).'/';
		$sClassPathFile=$sPackagePath.self::$CLASS_PATH;

		if($bForce || !is_file($sClassPathFile)){
			$arrFileStores=array();

			// 扫描类
			$arrClassPath=self::viewClass($sPackagePath);
			foreach($arrClassPath as $arrMap){
				$arrFileStores[$arrMap['class']]=$arrMap['file'];
				$arrKeys[]=$arrMap['class'];
			}

			// 检查是否有重复的类
			if(!empty($arrKeys) && count($arrKeys) != count(array_unique($arrKeys))){
				$arrDiffKeys=array();
				$arrDiffUnique=array_unique($arrKeys);
				foreach($arrDiffUnique as $nKey=>$sValue){
					if(in_array($sValue,$arrKeys)){unset($arrKeys[$nKey]);}
				}
				E(G::L('出现了相同的类：%s','LibDyhb',null,implode(',',$arrKeys)));
			}
			foreach($arrFileStores as $nKeyFileStore=>$sFileStoreValue){
				if(in_array(DYHB_PATH.'/LibPHP/'.$sFileStoreValue,(array)(include DYHB_PATH.'/LibPHP/Common/Paths.inc.php'))){
					unset($arrFileStores[$nKeyFileStore]);
				}
			}
			$sFileContents=serialize($arrFileStores);

			// 类路径文件
			if(!is_file($sClassPathFile)){
				if($hFile=fopen($sClassPathFile,'a')){
					fclose($hFile);
					chmod($sClassPathFile,0666);
				}
				else{
					return false;
				}
			}

			// 写入文件
			if(!file_put_contents($sClassPathFile,$sFileContents)){
				E(G::L('请确保拥有权限，无法创建 Class Path 文件：%s','LibDyhb',null,$sClassPathFile));
			}
		}

		// 读取Classes Path文件
		self::$OBJECTS=array_merge(self::$OBJECTS,array_map(array('Package','reallyPath'),self::readCache($sClassPathFile)));
	}

	static public function reallyPath($sValue){
		return self::$_arrTempPackagePath.$sValue;
	}

	static public function readCache($sCacheFile){
		if($hFp=fopen($sCacheFile,'r')){
			$sData=fread($hFp,filesize($sCacheFile));
			fclose($hFp);
		}
		return unserialize($sData);
	}

	static public function addPackagePath($sPackageName,$sPackagePath){
		if(isset(self::$CLASS_PATHS[$sPackageName])){
			G::E(G::L('包%s已经存在于：%s，无法重复注册。','LibDyhb',null,$sPackageName,$sPackagePath));
		}
		self::$CLASS_PATHS[$sPackageName]=$sPackagePath;
	}

	static public function regClass($sClass,$sPath){
		if(isset(self::$OBJECTS[$sClass])){
			G::E(G::L('类%s已经存在于：%s，无法重复注册。','LibDyhb',null,$sClass,$sPath));
		}
		self::$OBJECTS[$sClass]=$sPath;
	}

	static public function importC($sClass,$sPackageName=null){
		if(self::classExists($sClass)){// 类已经导入
			return;
		}
		if(is_file($sClass)){// 指定文件
			require($sClass);
			return;
		}
		else{// 提供类名
			A::STRING($sPackageName);
			if(is_dir($sPackageName)){// 包所在目录
				$sDir=&$sPackageName;
			}
			elseif(is_string($sPackageName)){// 根据包名字查找包路径
				$sDir=self::getPackagePath($sPackageName);
			}
			else {
				A::ASSERT_(0,G::L("参数\$sPackageName({%s})无效。",'LibDyhb',null,$sPackageName));
			}

			if(is_dir($sDir )){
				$sClassFilePath=self::findClassFile($sClass,$sDir);// 按照命名规则寻找类文件
				if($sClassFilePath===false){
					G::E(G::L("类 %s文件找不到 ",'LibDyhb',null, $sClass).G::L('附加信息：我们无法从如下文件列表中找到你需要的文件，%s<br/>','LibDyhb',null,implode('<br/>',self::$_arrNotFiles)));
				}
				require $sClassFilePath;// 载入
			}
			else{
				G::E(G::L("文件夹 %s找不到。",'LibDyhb',null,$sDir));
			}
		}
	}

	static public function importI($sInterface,$sPackage=null){
		if(is_file($sInterface)){// 指定文件
			require($sInterface);
			return;
		}
		else{
			if(self::classExists($sInterface,true)){return;}// 类已经导入
			$sPackageDir=self::getPackagePath($sPackage);// 包所在目录
			$sPath=self::findClassFile($sInterface,$sPackageDir,self::$_arrInterPat);// 按照命名规则寻找类文件
			if($sPath===false){
				 G::E(G::L("接口 %s文件找不到。",'LibDyhb',null,$sInterface).G::L('附加信息：我们无法从如下文件列表中找到你需要的文件，%s','LibDyhb',null,implode('<br/>',self::$_arrNotFiles)));
			}
			require $sPath;// 载入
		}
	}

	static public function setAutoload($bAutoload){
		if(!is_bool($bAutoload)){
			$bAutoload=$bAutoload?true:false;
		}
		else{
			$bAutoload =&$bAutoload;
		}
		$bOldValue=self::$_bAutoLoad;
		self::$_bAutoLoad=$bAutoload;
		return $bOldValue;
	}

	static public function autoLoad($sClassName){
		if(!self::$_bAutoLoad){
			return;
		}
		if(isset(self::$OBJECTS[$sClassName]) && !self::classExists($sClassName) && !self::classExists($sClassName,true)){
			require(self::$OBJECTS[$sClassName]);
		}
	}

	static public function getAutoload(){
		return self::$_bAutoLoad;
	}

	static public function classExists($sClassName,$bInter=false,$bAutoload=false){
		$bAutoloadOld=self::setAutoload($bAutoload);
		$sFuncName=$bInter?'interface_exists':'class_exists';
		$bResult=$sFuncName($sClassName);
		self::setAutoload($bAutoloadOld);
		return $bResult;
	}

	static public function getPackagePath($sPackName){
		$sPackName=strtoupper($sPackName);
		if(isset(self::$CLASS_PATHS[$sPackName])){
			$sPackagePath=self::$CLASS_PATHS[$sPackName];
			if(is_dir($sPackagePath)){
				return $sPackagePath;
			}
			else{
				return DYHB_PATH.'/'.$sPackagePath;
			}
		}
		else{
			return null;
		}
	}

	static private function viewClass($sDirectory,$sPreFilename=''){
		$arrReturnClass=array();
		$sDirectoryPath=realpath($sDirectory).'/';
		$hDir=opendir($sDirectoryPath);
		while($sFilename=readdir($hDir)){
			$sPath=$sDirectoryPath.$sFilename;
			if(is_file($sPath)){// 文件
				foreach(self::$_arrClassRegex as $sRegexp){
					$arrRes=array();// 找到类文件
					if(preg_match($sRegexp,$sFilename,$arrRes)){
						$sClassName=isset($arrRes[1])?$arrRes[1]:null;
						if($sClassName){
							$arrReturnClass[]=array('class'=>$sClassName,'file'=>$sPreFilename.$sFilename);
						}
					}
				}
			}
			else if(is_dir($sPath)){// 目录
				$sSpecialDir=array('.','..','.svn','#note','common','Widget');
				if(in_array($sFilename,$sSpecialDir)){// 排除特殊目录
					unset($sSpecialDir);
					continue;
				}
				else{// 递归子目录
					$arrReturnClass=array_merge($arrReturnClass,self::viewClass($sPath,$sPreFilename.$sFilename.'/'));
				}
			}
			else{
				A::ASSERT_(0,G::L("\$sPath: %s不是一个有效的路径",'LibDyhb',null,$sPath));
			}
		}
		return $arrReturnClass;
	}

	static public function findClassFile($sClassName,$Dirs=null,$arrPat=null){
		if($Dirs===null){// 目录路径分析
			$arrDirs=self::$_arrImportedPackDir;
		}else if(is_string($Dirs)){
			$arrDirs=array($Dirs);
		}else if(is_array($Dirs)){
			$arrDirs=&$Dirs;
		}
		if(!is_array($arrDirs)){
			G::E(G::L('参数 $Dirs 必须为数组或字符串。','LibDyhb'));
		}
		if($arrPat===null){// 文件匹配正则
			$arrPat=&self::$_arrClassFilePat;
		}
		foreach($arrDirs as $sDir){
			$sDir=G::tidyPath($sDir);
			foreach($arrPat as $sPat){
				$sPath=str_replace('%ClassName%',$sClassName,$sPat);
				$sPath=str_replace('%DirPath%',$sDir,$sPath);
				$bFindout=is_file($sPath);
				if($bFindout){
					return $sPath;
				}else{
					self::$_arrNotFiles[]=$sPath;
				}
			}
		}
		return false;
	}

	//[RUNTIME]
	static private function packupJs($sDir,$sPackagePath){
		$sPackOrderFile=$sDir.'/Pack.order.php';
		if(is_file($sPackOrderFile)){
			$sPackOrder=file_get_contents($sPackOrderFile);
			$sPackOrder=str_replace("\r",'',$sPackOrder);
			$arrPackOrder=explode("\n",$sPackOrder);
		}else{
			$arrPackOrder=array();
		}

		// 开始历遍包内的文件
		$hFiles=opendir($sDir);
		while(($sFileName=readdir($hFiles))!==false){
			$sPath="{$this->_sPath}/{$sFileName}";
			if(is_file($sPath)){// 文件
				if(!in_array($sFileName,$arrPackOrder)){
					if(preg_match('/^.+\.(class|interface)\.js$/i',$sFileName)){
						$arrPackOrder[]=$sFileName;
					}
				}
			}
		}

		// 打包
		$hPackage=fopen($sPackagePath,'w');
		chmod($sPackagePath,0666);
		foreach($arrPackOrder as $sFileName){
			if(!$sFileName){
				continue;
			}
			$sFilePath=$sDir.'/'.$sFileName;
			if(is_file($sFilePath)){
				$sContent=file_get_contents($sFilePath);
				$sContent=trim(preg_replace(array('/(^|\r|\n)\/\*.+?(\r|\n)\*\/(\r|\n)/is','/\/\/note.+?(\r|\n)/i','/\/\/debug.+?(\r|\n)/i','/(^|\r|\n)(\s|\t)+/','/(\r|\n)/',"/\/\*(.*?)\*\//ies"),'',$sContent));
				fwrite($hPackage,$sContent);
			}else{
				fwrite($hPackage,G::L("throw new Error('丢失JavaScript文件:%s!');",'LibDyhb',null,$sFilePath));
			}
		}
		fclose($hPackage);
	}
	//[/RUNTIME]

}
