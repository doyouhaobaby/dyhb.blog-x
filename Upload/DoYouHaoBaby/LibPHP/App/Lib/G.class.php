<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   全局函数集($) */

!defined('DYHB_PATH') && exit;

class G{

	static function getGpc($sKey,$sVar='R'){
		$sVar=strtoupper($sVar);
		switch($sVar){
			case 'G':$sVar=&$_GET;break;
			case 'P':$sVar=&$_POST;break;
			case 'C':$sVar=&$_COOKIE;break;
			case 'S':$sVar=&$_SESSION;break;
			case 'R':$sVar=&$_REQUEST;break;
			case 'F':$sVar=&$_FILES;break;
		}
		return isset($sVar[$sKey])?$sVar[$sKey]:NULL;
	}

	public static function seccode($arrOption=null,$bVisitedDb=true){
		@header("Expires: -1");// 定义头部
		@header("Cache-Control: no-store,private,post-check=0,pre-check=0,max-age=0",FALSE);
		@header("Pragma: no-cache");
		if($bVisitedDb===true){// 使用数据库
			$arrUserData =UserModel::M()->userData();// 取得用户登录信息，取出里面的session_seccode
			$sHash=isset($arrUserData['session_hash'])? $arrUserData['session_hash']:'';
			$sAuthKey=isset($arrUserData['session_auth_key'])? $arrUserData['session_auth_key']:'';
			$nUserId=isset($arrUserData['user_id'])? $arrUserData['user_id']:'';
			$nSeccode=isset($arrUserData['session_seccode'])? $arrUserData['session_seccode']:'';
			if(self::getGpc('update')){// 更新session数据库
				$nSeccode=G::randString(6,null,true);
				UserModel::M()->updateSession($sHash,$nUserId,$sAuthKey,$nSeccode);
			}
		}
		else{// 使用COOKIE保存数据
			$nSeccode=G::randString(6,null,true);
			G::cookie('_seccode_',$nSeccode,86400);
		}
		$oCode=new Seccode($arrOption);// 实例化对象
		$oCode->setCode($nSeccode)->display();
	}

	public static function checkSeccode($sSeccode){
		$nOldSeccode =trim( G::cookie('_seccode_'));
		if(empty($nOldSeccode)){
			return false;
		}
		E::seccodeConvert($nOldSeccode);// 转化字符
		return trim($nOldSeccode)==trim($sSeccode);// 开始比较数据
	}

	static public function stripslashes($String,$bRecursive=true){
		if($bRecursive===true and is_array($String)){// 递归
			foreach($String as $sKey=>$value){
				$String[self::stripslashes($sKey)]=self::stripslashes($value);// 如果你只注意到值，却没有注意到key
			}
		}
		else{
			if(is_string($String)){
				$String=stripslashes($String);
			}
		}
		return $String;
	}

	static public function addslashes($String,$bRecursive=true){
		if($bRecursive===true and is_array($String)){
			foreach($String as $sKey=>$value){
				$String[self::addslashes($sKey)]=self::addslashes($value);// 如果你只注意到值，却没有注意到key
			}
		}
		else{
			if(!self::getMagicQuotesGpc() and is_string($String)){
				$String=addslashes($String);
			}
		}
		return $String;
	}

	static public function getMagicQuotesGpc(){
		return(defined('MAGIC_QUOTES_GPC') && MAGIC_QUOTES_GPC===TRUE);
	}

	static public function varType($Var,$sType){
		$sType=trim($sType);// 整理参数，以支持array:ini格式
		$arrTypes=explode(':',$sType);
		$sRealType=$arrTypes[0];
		$sAllow=isset($arrTypes[1])?$arrTypes[1]:null;
		$sRealType=strtolower($sRealType);
		switch($sRealType){
			case 'string':// 字符串
				return is_string($Var);
			case 'integer':// 整数
			case 'int' :
				return is_int($Var);
			case 'float':// 浮点
				return is_float($Var);
			case 'boolean':// 布尔
			case 'bool':
				return is_bool($Var);
			case 'num':// 数字
			case 'numeric':
				return is_numeric($Var);
			case 'base':// 标量（所有基础类型）
			case 'scalar':
				return is_scalar($Var);
			case 'handle':// 外部资源
			case 'resource':
				return is_resource($Var);
			case 'array':{// 数组
				if($sAllow){
					$arrAllow=explode(',',$sAllow);
					return self::checkArray($Var,$arrAllow);
				}
				else
					return is_array($Var);
			}
			case 'object':// 对象
				return is_object($Var);
			case 'null':// 空
			case 'NULL':
				return($Var===null);
			case 'callback':// 回调函数
				return is_callable($Var,false);
			default :// 类
				return self::isKindOf($Var,$sType);
		}
	}

	static public function throwException($sMsg,$sType='DException',$nCode=0){
		if($sType ==''||$sType===null) $sType='DException';
		if(Package::classExists($sType)){
			throw new $sType($sMsg,$nCode,true);
		}
		else{
			self::halt($sMsg);// 异常类型不存在则输出错误信息字串
		}
	}

	static public function E($sMsg,$sType='DException',$nCode=0){
		self::throwException($sMsg,$sType,$nCode);
	}

	static public function halt($Error){
		$arrError=array();
		if($GLOBALS['_commonConfig_']['APP_DEBUG']){
			if(!is_array($Error)){//调试模式下输出错误信息
				$arrTrace=debug_backtrace();
				$arrError['message']=$Error;
				$arrError['file']=$arrTrace[0]['file'];
				$arrError['class']=$arrTrace[0]['class'];
				$arrError['function']=$arrTrace[0]['function'];
				$arrError['line']=$arrTrace[0]['line'];
				$arrError['code']=0;
				$sTraceInfo='';// 调试信息
				$time=date("Y-m-d H:i:m",time());
				foreach($arrTrace as $Val){
					$sFile=isset($Val['file'])?$Val['file']:'no file';
					$sTraceInfo.='<b><font color="#fff">['.$time.']</font></b><br/> <font color="#698B22">'.$sFile.'</font><font color="#EE4000">('.(isset($Val['line'])?$Val['line']:'Unknown line').')</font><br/> ';
					$sTraceInfo.='<font color="#545454">'.(isset($Val['class'])?$Val['class']:'#class').(isset($Val['type'])?$Val['type']:'#type').$Val['function'].'(';
					if(isset($Val['args']) and is_array($Val['args'])){
						foreach($Val['args'] as $sK=>$V){
							$sTraceInfo.=($sK!=0?',':'').gettype($V);
						}
					}
					$sTraceInfo.=")</font><br/><br/>";
				}
				$arrError['trace']=$sTraceInfo;
			}
			else{
				$arrError=$Error;
			}
			include(DYHB_PATH.'/Resource/Template/DException.template.php');// 包含异常页面模板
		}
		else{
			$sErrorPage=$GLOBALS['_commonConfig_']['ERROR_PAGE']?DYHB_PATH.'/Resource/Template/'.$GLOBALS['_commonConfig_']['ERROR_PAGE'].".template.php":'';// 否则定向到错误页面
			if(!empty($sErrorPage)){
				self::urlGoTo($sErrorPage);
			}
			else{
				if($GLOBALS['_commonConfig_']['SHOW_ERROR_MSG']){$arrError['message']=is_array($Error)?$Error['message']:$Error;}
				else{$arrError['message']='Error';}
				include(DYHB_PATH.'/Resource/Template/DException.template.php');// 包含异常页面模板
			}
		}
		exit;
	}

	static public function includeFile($sFile){
		static $FILES=array();
		if(!is_file($sFile)){ return false;}
		if(!isset($FILES[md5($sFile)])){
			include($sFile);
			$FILES[md5($sFile)]=TRUE;
			return $FILES[md5($sFile)];
		}
		return FALSE; // 不存在则返回FALSE
	}

	static public function urlGoTo($sUrl,$nTime=0,$sMsg=''){
		$sUrl=str_replace(array("\n","\r"),'',$sUrl);// 多行URL地址支持
		if(empty($sMsg)){
			$sMsg=self::L("系统将在%d秒之后自动跳转到%s。",'LibDyhb',null,$nTime,$sUrl);
		}
		if(!headers_sent()){
			if(0==$nTime){
				header("Location:".$sUrl);
			}
			else{
				header("refresh:{$nTime};url={$sUrl}");
				echo($sMsg);
			}
			exit();
		}
		else{
			$sStr="<meta http-equiv='Refresh' content='{$nTime};URL={$sUrl}'>";
			if($nTime!=0){
				$sStr.=$sMsg;
			}
			exit($sStr);
		}
	}

	static public function C($sName='',$Value=NULL,$bForce=false){
		static $oConfig=null;
		if($oConfig===null){// 创建对象，并且初始化数据
			$oConfig=new Config(APP_RUNTIME_PATH);// 实例化项目配置对象
		}
		if(!empty($sName) && $Value===null){// ''时返回配置数据
			if(!strpos($sName,'.')){
				return $oConfig->getItem($sName);
			}
			$arrName=explode('.',$sName);// 多维数组获取
			$arrConfigValue=$oConfig->getItems();
			$sString='$arrConfigValue'.E::arrayHandler($arrName,1,0);
			$sString="\$sName=isset({$sString})? {$sString} : null;";
			eval($sString);
			return $sName;
		}
		if($sName==='' && $Value===null){// $sName='' 返回所有配置值
			return $oConfig->getItems();
		}
		if(is_array($sName)){// 如果都不是，那么就是设置配置值,那么我们设置最顶层的配置数据
			foreach($sName as $sK =>$v){
				if(!($bForce===false && $oConfig->hasItem($sK)) || $oConfig->getItem($sName)!==$Value){
					$oConfig->setItem($sK,$v);
				}
			}
			return;
		}
		else{
			if(!($bForce===false && $oConfig->hasItem($sName)) || $oConfig->getItem($sName)!==$Value){
				if(!strpos($sName,'.')){
					return $oConfig->setItem($sName,$Value);
				}
				// 多维数组设置
				$arrName=explode('.',$sName);
				$sString='$arrConfigValue'.E::arrayHandler($arrName,1,0);
				$sString="{$sString} ='{$Value}';";
				eval($sString);
				return $oConfig->setItem($sName,$arrConfigValue);
			}
		}
	}

	static public function L($sValue,$Package=null,$Lang=null/*Argvs*/){
		$arrArgvs=func_get_args();
		if(!isset($arrArgvs[1]) OR empty($Package)){$arrArgvs[1]='app';}// 参数处理
		if(!isset($arrArgvs[2])){$arrArgvs[2]=LANG_NAME;}
		$sValue=call_user_func_array(array('Lang','setEx'),$arrArgvs);
		return $sValue;
	}

	static public function isSameCallback($CallbackA,$CallbackB){
		A::CALLBACK($CallbackA);
		A::CALLBACK($CallbackB);
		if(is_array($CallbackA)){
			if(is_array($CallbackB)){
				return($CallbackA[0]===$CallbackB[0]) AND (strtolower($CallbackA[1])===strtolower($CallbackB[1]));
			}else{
				return false;
			}
		}else{
			return strtolower($CallbackA)===strtolower($CallbackB);
		}
	}

	static public function randString($nLength,$sCharBox=null,$bNumeric=false){
		if($bNumeric===true){
			return sprintf('%0'.$nLength.'d',mt_rand(1,pow(10,$nLength)-1));
		}
		if($sCharBox===null){
			$sBox=strtoupper(md5(self::now(true).rand(1000000000,9999999999)));
			$sBox.=md5(self::now(true).rand(1000000000,9999999999));
		}else{
			$sBox=&$sCharBox;
		}
		$nN=$nLength;
		$nBoxEnd=strlen($sBox)-1;
		$sRet='';
		while($nN--){
			$sRet.=substr($sBox,rand(0,$nBoxEnd),1);
		}
		return $sRet;
	}

	static public function now($bExact=true){
		if($bExact){
			list($nMS,$nS)=explode(' ',microtime());
			return $nS+$nMS;
		}else{
			return CURRENT_TIMESTAMP;
		}
	}

	static public function W($sName,$Data='',$bReturn=FALSE){
		$sClass=ucwords(strtolower($sName)).'Widget';// 获取widget类名
		self::includeFile(APP_PATH.'/App/Class/View/Widget/'.$sClass.'.class.php');// 载入分析的模版
		if(!class_exists($sClass,false)){// 不存在class，则抛出异常
			self::E(self::L('类不存在：%s','LibDyhb',null,$sClass));
		}
		$oWidget=Dyhb::instance($sClass);// 获取widget唯一实例
		if(is_string($Data)){
			parse_str($Data,$Data);
		}
		$sContent=$oWidget->render($Data);
		if($bReturn){
			return $sContent;
		}else{
			echo $sContent;
		}
	}

	static public function gbkToUtf8($FContents,$sFromChar,$sToChar='utf-8'){
		if(empty($FContents)) return $FContents;
		$sFromChar=strtolower($sFromChar)=='utf8'?'utf-8':strtolower($sFromChar);
		$sToChar=strtolower($sToChar )=='utf8'?'utf-8':strtolower($sToChar);
		if($sFromChar==$sToChar || (is_scalar($FContents) && !is_string($FContents))){// 如果编码相同或者非字符串标量则不转换
			return $FContents;
		}

		if(is_string($FContents)){
			if(function_exists('mb_convert_encoding')){
				return mb_convert_encoding($FContents,$sFromChar,$sToChar);
			}elseif(function_exists('iconv')){
				return iconv($FContents,$sFromChar,$sToChar);
			}else{
				return $FContents;
			}
		}
		elseif(is_array($FContents)){
			foreach($FContents as $sKey=>$sVal){
				$sKeyTwo=self::gbkToUtf8($sKey,$sFromChar,$sToChar);// 键值漏洞
				$FContents[$sKeyTwo]=self::gbkToUtf8($sVal,$sFromChar,$sToChar);
				if($sKey!=$sKeyTwo)
					unset($FContents[$sKeyTwo]);
			}
			return $FContents;
		}else{
			return $FContents;
		}
	}

	static public function exceptionHandler(Exception $oE){
		$sErrstr=$oE->getMessage();// 记录日志
		$sErrfile=$oE->getFile();
		$nErrline=$oE->getLine();
		$nErrno=$oE->getCode();
		$sErrorStr="[$nErrno] $sErrstr ".basename($sErrfile).self::L(" 第 %d 行。",'LibDyhb',null ,$nErrline);
		if($GLOBALS['_commonConfig_']['LOG_RECORD'] && self::C('LOG_MUST_RECORD_EXCEPTION')) Log::W($sErrstr,Log::EXCEPTION);
		if(method_exists($oE,'formatException')){
			self::halt($oE->formatException());
		}else{
			self::halt($oE->getMessage());
		}
	}

	static public function errorHandler($nErrno,$sErrstr,$sErrfile,$nErrline){
		switch($nErrno){
			case E_ERROR:
			case E_USER_ERROR:
				$sErrstr="[$nErrno] $sErrstr ".basename($sErrfile).self::L(" 第 %d 行。", 'LibDyhb',null,$nErrline);
				if($GLOBALS['_commonConfig_']['LOG_RECORD']) Log::W($sErrstr,Log::ERR,$GLOBALS['_commonConfig_']['LOG_MUST_RECORD_ERROR']);
				self::halt($sErrstr);
				break;
			case E_STRICT:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			default:
				$sErrstr="[$nErrno] $sErrstr ".basename($sErrfile).self::L(" 第 %d 行。",'LibDyhb',null,$nErrline);
				Log::R($sErrstr,Log::NOTICE,$GLOBALS['_commonConfig_']['LOG_MUST_RECORD_ERROR']);
				if($GLOBALS['_commonConfig_']['STRICT_ECHO_PHP_ERROR']) self::halt($sErrstr);
				break;
		}
	}

	static public function isThese($Var,$Types){
		if(!self::varType($Types,'string') && !self::checkArray($Types,array('string'))){
			self::E(self::L('正确格式:参数 $Types 必须为 string 或 各项元素为string的数组','LibDyhb'));
		}
		if(is_string($Types)){
			$arrTypes=array($Types);
		}else{
			$arrTypes=$Types;
		}
		foreach($arrTypes as $sType){// 类型检查
			if(self::varType($Var,$sType)){
				return true;
			}
		}
		return false;
	}

	static public function isKindOf($SubClass,$sBaseClass){
		if(Package::classExists($sBaseClass,true)){// 接口
			return self::isImplementedTo($SubClass,$sBaseClass);
		}else{// 类
			if(is_object($SubClass)){// 统一类名,如果不是，返回false
				$sSubClassName=get_class($SubClass);
			}elseif(is_string($SubClass)){
				$sSubClassName=&$SubClass;
			}else{
				return false;
			}

			if($sSubClassName==$sBaseClass){// 子类名 即为父类名
				return true;
			}
			$sParClass=get_parent_class($sSubClassName);// 递归检查
			if(!$sParClass){
				return false;
			}
			return self::isKindOf($sParClass,$sBaseClass);
		}
	}

	static public function isImplementedTo($Class,$sInterface,$bStrictly=false){
		if(is_object($Class)){// 尝试获取类名，否则返回false
			$sClassName=get_class($Class);
		}elseif(is_string($Class)){
			$sClassName=&$Class;
		}else{
			return false;
		}

		if(!is_string($sClassName)){// 类型检查
			return false;
		}
		if(!class_exists($sClassName) || !interface_exists($sInterface)){// 检查类和接口是否都有效
			return false;
		}

		// 建立反射
		$oReflectionClass=new ReflectionClass($sClassName);
		$arrInterfaceRefs=$oReflectionClass->getInterfaces();
		foreach($arrInterfaceRefs as $oInterfaceRef){
			if($oInterfaceRef->getName()!=$sInterface){
				continue;
			}
			if(!$bStrictly){// 找到 匹配的 接口
				return true;
			}
			// 依次检查接口中的每个方法是否实现
			$arrInterfaceFuncs=get_class_methods($sInterface);
			foreach($arrInterfaceFuncs as $sFuncName){
				$sReflectionMethod=$oReflectionClass->getMethod($sFuncName);
				if($sReflectionMethod->isAbstract()){// 发现尚为抽象的方法
					return false;
				}
			}
			return true;
		}

		//递归检查父类
		if($sParName=get_parent_class($sClassName)){
			return self::isImplementedTo($sParName,$sInterface,$bStrictly);
		}else{
			return false;
		}
	}

	static public function checkArray($arrArray,array $arrTypes){
		if(!is_array($arrArray)){// 不是数组直接返回
			return false;
		}

		// 判断数组内部每一个值是否为给定的类型
		foreach($arrArray as &$Element){
			$bRet=false;
			foreach($arrTypes as $Type){
				if(self::varType($Element,$Type)){
					$bRet=true;
					break;
				}
			}
			if(!$bRet){
				return false;
			}
		}
		return true;
	}

	static public function cookie($sName,$Value='',$Option=null,$bThisPrefix=true,$bReturnCookie=false){
		static $oCookie=null;
		$arrCookieConfig=array(// 默认设置
			'prefix'=>$GLOBALS['_commonConfig_']['COOKIE_PREFIX'],// cookie名称前缀
			'expire'=>86400,// cookie保存时间
			'path'=>'/',// cookie保存路径
			'domain'=>'',// cookie有效域名
			'secure'=>false,// cookie是否加密http传输
		);

		if(!empty($Option)){// 参数设置(会覆盖黙认设置)
			if(is_numeric($Option)){$Option=array('expire'=>$Option);}// 数字直接为时间
			elseif(is_string($Option)){parse_str($Option,$Option);}// 字符串 分析成数组
			$arrCookieConfig=array_merge($arrCookieConfig,array_change_key_case($Option));// 数组
		}
		if($oCookie===null){
			$oCookie=Cookie::startCookie($arrCookieConfig['expire'],$arrCookieConfig['path'],$arrCookieConfig['domain'],$arrCookieConfig['prefix'],$arrCookieConfig['secure']);
		}
		if($bReturnCookie===true){return $oCookie;}
		if(is_null($sName)){// 清除指定前缀的所有cookie
			if(empty($_COOKIE)) return;
			$sPrefix=empty($Value)?$arrCookieConfig['prefix']:$Value;// 要删除的cookie前缀，不指定则删除config设置的指定前缀
			if(!empty($sPrefix) && $bThisPrefix===true){// 如果前缀为空字符串将不作处理直接返回
				$oCookie->clearCookie(true);
			}else{
				$oCookie->clearCookie(false);
			}
			return;
		}

		if(''===$Value){// 如果值为空，则获取cookie
			return $oCookie->getCookie($sName);// 获取指定Cookie
		}
		else{
			if(is_null($Value)){// 如果值为null，则删除指定COOKIE
				$oCookie->deleteCookie($sName);
			}else{// 设置COOKIE
				$oCookie->setCookie($sName,$Value);
			}
		}
	}

	static public function tidyPath($sPath,$bUnix=true){
		$sRetPath=str_replace('\\','/',$sPath);// 统一 斜线方向
		$sRetPath=preg_replace('|/+|','/',$sRetPath);// 归并连续斜线
		$arrDirs=explode('/',$sRetPath);// 削除 .. 和  .
		$arrDirs2=array();
		while(($sDirName=array_shift($arrDirs))!==null){
			if($sDirName=='.'){
				continue;
			}
			if($sDirName=='..'){
				if(count($arrDirs2)){
					array_pop($arrDirs2);
					continue;
				}
			}
			array_push($arrDirs2,$sDirName);
		}

		$sRetPath=implode('/',$arrDirs2);// 目录 以  '/' 结尾
		if(is_dir($sRetPath)){// 存在的目录
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.= '/';
			}
		}else if(preg_match("|\.$|",$sPath)){// 不存在，但是符合目录的格式
			if(!preg_match('|/$|',$sRetPath)){
				$sRetPath.= '/';
			}
		}

		$sRetPath=str_replace(':/',':\\',$sRetPath);// 还原 驱动器符号
		if(!$bUnix){// 转换到 Windows 斜线风格
			$sRetPath=str_replace('/','\\',$sRetPath);
		}
		$sRetPath=rtrim($sRetPath,'\\/');// 删除结尾的“/”或者“\”
		return $sRetPath;
	}

	static public function dump($Var,$bEcho=true,$sLabel=null,$bStrict=true){
		$SLabel=($sLabel===null)?'':rtrim($sLabel).' ';
		if(!$bStrict){
			if(ini_get('html_errors')){
				$sOutput=print_r($Var,true);
				$sOutput="<pre>".$sLabel.htmlspecialchars($sOutput,ENT_QUOTES)."</pre>";
			}else{
				$sOutput=$sLabel." : ".print_r($Var, true);
			}
		}
		else{
			ob_start();
			var_dump($Var);
			$sOutput=ob_get_clean();
			if(!extension_loaded('xdebug')){
				$sOutput=preg_replace("/\]\=\>\n(\s+)/m","] => ",$sOutput);
				$sOutput='<pre>'.$sLabel.htmlspecialchars($sOutput,ENT_QUOTES).'</pre>';
			}
		}

		if($bEcho){
			echo $sOutput;
			return null;
		}else{
			return $sOutput;
		}
	}

	static public function makeDir($Dir,$nMode=0777){
		if(is_dir($Dir)){
			return true;
		}
		if(is_string($Dir)){
			$arrDirs=explode('/',str_replace('\\','/',trim($Dir,'/')));
		}else{
			$arrDirs=$Dir;
		}
		$sMakeDir=IS_WIN?'':'/';
		foreach($arrDirs as $sDir){
			$sMakeDir.=$sDir.'/';
			!is_dir($sMakeDir) && mkdir($sMakeDir,$nMode);
		}
		return TRUE;
	}

	static public function U($sUrl, $arrParams=array(),$bRedirect=false,$bSuffix=true){
		$sUrl=ltrim($sUrl,'\\/');// 清理URL前面的斜杠和反斜杠
		if(!strpos($sUrl,'://')){$sUrl=str_replace('_','dyhb-bhyd',APP_NAME).'://'.$sUrl;}
		if(stripos($sUrl,'@?')){$sUrl=str_replace('@?','@dyhb?',$sUrl);}
		elseif(stripos($sUrl,'@')!=strlen($sUrl)-1){$sUrl=$sUrl;}
		elseif(stripos($sUrl,'@')){$sUrl=$sUrl.MODULE_NAME;}
		$arrArray=parse_url($sUrl);// app&&路由
		$sApp=isset($arrArray['scheme'])?$arrArray['scheme']:APP_NAME;// APP
		$sRoute=isset($arrArray['user'])?$arrArray['user']:'';// 路由
		if(isset($arrArray['path'])){// 分析获取模块和操作
			$sAction=substr($arrArray['path'],1);
			if(!isset($arrArray['host'])){$sModule=MODULE_NAME;}// 没有指定模块名
			else{$sModule=$arrArray['host'];}// 直接获取模块名
		}else{// 只指定操作
			$sModule=MODULE_NAME;
			$sAction=($arrArray['host']=='LibDyhb'?ACTION_NAME:$arrArray['host']);
		}

		if(isset($arrArray['query'])){// 如果指定了查询参数
			$arrQuery =array();
			parse_str($arrArray['query'],$arrQuery);
			$arrParams=array_merge($arrQuery,$arrParams);
		}
		$sApp=str_replace('dyhb-bhyd','_',$sApp);
		if($GLOBALS['_commonConfig_']['URL_MODEL']>0){// 如果开启了URL解析，则URL模式为非普通模式
			$sDepr=$GLOBALS['_commonConfig_']['URL_PATHINFO_MODEL']==2?$GLOBALS['_commonConfig_']['URL_PATHINFO_DEPR']:'/';
			$sStr=$sDepr;
			foreach($arrParams as $sVar=>$sVal){$sStr.=$sVar.$sDepr.$sVal.$sDepr;}
			$sStr=substr($sStr,0,-1);// 删除末尾的分隔符
			if(!empty($sRoute)){$sUrl=str_replace(APP_NAME,$sApp,__APP__).'/'.$sRoute.$sStr;}// 如果存在路由
			else{$sUrl=str_replace(APP_NAME,$sApp,__APP__).'/'.$sModule.$sDepr.$sAction.$sStr;}
			if($bSuffix && $GLOBALS['_commonConfig_']['URL_HTML_SUFFIX']){$sUrl.=$GLOBALS['_commonConfig_']['URL_HTML_SUFFIX'];}// 是否添加静态后缀
		}
		else{
			$arrParams= http_build_query($arrParams);
			if(empty($sRoute)){
				$sUrl=str_replace(APP_NAME,$sApp,__APP__).'?c='.$sModule.'&a='.$sAction.($arrParams?'&'.$arrParams:'');
			}
			else{
				$sUrl=str_replace(APP_NAME,$sApp,__APP__).'?'.($sRoute ? 'r='.$sRoute :'').($arrParams?'&'.$arrParams :'');
			}
		}
		if($bRedirect){self::urlGoTo($sUrl);}
		else{return $sUrl;}
	}

	static public function getRelativePath($sFromPath,$sToPath){
		if(is_file($sFromPath)){// 如果 $sFromPath 是一个文件，取其目录部分
			$sFrom=dirname($sFromPath);
		}
		else{
			$sFrom=&$sFromPath;
		}
		if(IS_WIN){
			$sFrom=strtolower($sFrom);
			$sToPath=strtolower($sToPath);
		}
		$sFrom=self::tidyPath($sFrom);// 整理路径为统一格式
		$sTo=self::tidyPath($sToPath);
		$arrFromPath=explode('/',$sFrom);// 切为数组
		$arrToPath=explode('/',$sTo);
		array_diff($arrFromPath,array(''));// 排除 空元素
		array_diff($arrToPath,array(''));
		$nSameLevel=0;// 开始比较
		while(
			($sFromOneDir=array_shift($arrFromPath))!==null
			and ($sToOneDir=array_shift($arrToPath))!==null
			and ($sFromOneDir===$sToOneDir)
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
			return null;
		}
		$nLevel=count($arrFromPath)-1;// 返回
		$sRelativePath=($nLevel>0)?str_repeat('../',$nLevel):'';
		$sRelativePath.=implode('/',$arrToPath);
		$sRelativePath=rtrim($sRelativePath,'/');
		return $sRelativePath;
	}

}
