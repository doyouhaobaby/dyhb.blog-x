<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   项目编译类($)*/

!defined('DYHB_PATH') && exit;

class AppRuntime{

	public function run(){
		if(!is_writeable(APP_PATH)){
			E('Project directory is not writable,the directory can not be automatically generated! Build the project manually compiled directory');
		}
		if(!is_dir(APP_RUNTIME_PATH)|| !is_file(APP_RUNTIME_PATH.'/~Runtime.inc.lock')){
			if(!is_dir(APP_RUNTIME_PATH)){
				if(!mkdir(APP_PATH.'/App',0777)){
					E(sprintf('Dir:%s made failed!',APP_PATH.'/App'));
				}
				if(!mkdir(APP_RUNTIME_PATH,0777)){
					E(sprintf('Dir:%s made failed!',APP_RUNTIME_PATH));
				}
			}
			if(!file_put_contents(APP_RUNTIME_PATH.'/~Runtime.inc.lock','App Lock! Do Not To Remove This File or The App Dirs Will Rebuild !')){
				E(sprintf('File:%s write failed!',APP_RUNTIME_PATH.'/~Runtime.inc.lock'));
			}
			$this->buildApp();
		}
		else{
			$this->checkRuntime();
		}
		$this->combileRuntime();
		return;
	}

	private function combileRuntime(){
		$bAllinone=defined('RUNTIME_ALLINONE');
		$sContent=$this->compileAFile(DYHB_PATH.'/LibPHP/Common/Define.inc.php',$bAllinone);
		$arrList=array();// 编译加载文件
		$arrList=array_merge($arrList,(array)(include DYHB_PATH.'/LibPHP/Common/Paths.inc.php'));
		foreach($arrList as $sFile){$sContent.=$this->compileAFile($sFile,$bAllinone);}
		if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE===false){
			if(!file_put_contents(APP_RUNTIME_PATH.'/~Runtime.inc.php','<?php'.$sContent)){
				E(sprintf('File:%s write failed!',APP_RUNTIME_PATH.'/~Runtime.inc.php'));
			};
		}
		else{
			if(!file_put_contents(APP_RUNTIME_PATH.'/~Runtime.inc.php',$this->stripWhitespace('<?php'.$sContent))){
				E(sprintf('File:%s write failed!',APP_RUNTIME_PATH.'/~Runtime.inc.php'));
			};
		}
		unset($sContent);
		echo 'App Runtime File Build Succeed! Please refresh!<br/>';
	}

	private function compileAFile($sFile,$bAllinone=false){
		$sContent=file_get_contents($sFile);
		if(true===$bAllinone){$sContent=preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s','',$sContent);}
		$sContent=substr(trim($sContent),5);
		$sContent=str_replace('!defined(\'DYHB_PATH\') && exit;','',$sContent);
		$sBrCode=IS_WIN?"\r\n":"\n";
		$sContent=str_replace(array("\r","\n"),'__dyhb_framework_pk_with_you__',$sContent);
		$sContent=preg_replace("/(__dyhb_framework_pk_with_you__)+/i",'__dyhb_framework_pk_with_you__',$sContent);
		$sContent=str_replace('__dyhb_framework_pk_with_you__',$sBrCode,$sContent);
		if('?>'==substr($sContent,-2))
		$sContent=substr($sContent,0,-2);
		return $sContent;
	}

	private function stripWhitespace($sContent){
		$sStripStr='';
		$arrTokens=token_get_all($sContent);// 分析php源码
		$bLastSpace=false;
		for($nI=0,$nJ=count($arrTokens);$nI<$nJ;$nI++){
			if(is_string($arrTokens[$nI])){
				$bLastSpace=false;
				$sStripStr.=$arrTokens[$nI];
			}
			else{
				switch($arrTokens[$nI][0]){
					case T_COMMENT: //过滤各种PHP注释
					case T_DOC_COMMENT:
						break;
					case T_WHITESPACE:// 过滤空格
						if(!$bLastSpace){
							$sStripStr.=' ';
							$bLastSpace=true;
						}
						break;
					default:
						$bLastSpace=false;
						$sStripStr.=$arrTokens[$nI][1];
				}
			}
		}
		return $sStripStr;
	}

	private function buildApp(){
		$arrError=array();
		$sAppName=preg_replace('[^a-z0-9_]', '', APP_NAME);// 过滤App_name中的非法字符
		if(!$sAppName){
			$arrError[]=sprintf('The application name(%s) invalid.',$sAppName);
		}
		$sAppPath=dirname(APP_PATH);
		$sReallyPath=realpath($sAppPath);
		if(!$sAppPath || $sReallyPath == dirname(__FILE__) || !is_dir($sReallyPath)){
			$arrError[]=sprintf('Project directory name(%s) invalid.',$sAppPath);
		}
		else{
			$sAppPath=$sReallyPath;
		}
		if(empty($arrError)){
			require dirname(dirname(dirname(__FILE__))). '/Extension/Generator/GeneratorApplication.class.php';
			ob_start();
			$sTestAppDir=null;
			$oApp=new GeneratorApplication();
			$oApp->APP($sAppName,$sAppPath,null,$sTestAppDir,true);
			$sOutput=ob_get_clean();
			$sAppPath=$sAppName='';
			E(nl2br(htmlspecialchars($sOutput)));
			return;
		}
		else{
			E(implode('<br/>',$arrError));
		}
	}

	private function checkRuntime(){
		if(!is_writeable(APP_RUNTIME_PATH)){
			E('Dir [ '.APP_RUNTIME_PATH.' ] Can not write!');
		}
		if(!is_dir(APP_RUNTIME_PATH.'/Temp')){ mkdir(APP_RUNTIME_PATH.'/Temp',0777);}
		if(!is_dir(APP_RUNTIME_PATH.'/Data')){ mkdir(APP_RUNTIME_PATH.'/Data',0777);}
		if(!is_dir(APP_RUNTIME_PATH.'/Cache')){ mkdir(APP_RUNTIME_PATH.'/Cache',0777);}
		if(!is_dir(APP_RUNTIME_PATH.'/Log')){ mkdir(APP_RUNTIME_PATH.'/Log',0777);}
		return true;
	}

}
