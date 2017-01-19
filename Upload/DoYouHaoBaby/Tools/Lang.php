<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
	语言包制作工具($)*/

/** 防止PHP页面乱码 */
ini_set('default_charset','utf-8');

/** 导入计时器 */
include(dirname(__FILE__).'/Common/Timer.class.php');

/** 定义PHP运行的操作系统是否是 WINDOWS平台 */
define('IS_WIN',strstr(PHP_OS, 'WIN')? 1 : 0);

/**
 * 用于框架开发过程中的语言包制作
 *
 * < 本工具不会调用Google API来制作，需要人工获取数据。
 *   本工具会在人工获取的数据中，整理成数组后进行过滤清理，提高语言包开发效率。 >
 */
class MakeLang{

	/**
	 * 待处理的语言包
	 *
	 * @access public
	 * @var static
	 */
	static public $_sFromLangPath='';

	/**
	 * 处理后的语言包
	 *
	 * @access public
	 * @var static
	 */
	static public $_sToLangPath='';

	/**
	 * 第三方语言包
	 *
	 * @access public
	 * @var static
	 */
	static public $_sThreeLangPath='';

	/**
	 * 批量处理语言包
	 *
	 * @access public
	 * @static
	 * @param string $sFromLangPath 语言包出处
	 * @param string $sToLangPath 语言包输出路径
	 * @param string $sThreeLangPath 第三方语言包
	 * @return void
	 */
	static public function many($sFromLangPath='',$sToLangPath='',$sThreeLangPath=''){
		if($sFromLangPath==''){
			$sFromLangPath=self::$_sFromLangPath;
		}
		echo 'Init FromLangPath...<br/>';
		if($sToLangPath==''){
			$sToLangPath=self::$_sToLangPath;
		}
		echo 'Init ToLangPath...<br/>';
		if($sThreeLangPath=='')
			$sThreeLangPath=self::$_sThreeLangPath;
		echo 'Init ThreeLangPath...<br/>';
		echo '------------------------------------------------------------------------------------------------------------------------------------<br/>';
		$arrFromLangPaths=self::includeLangFile($sFromLangPath);// 读取语言包
		echo 'Reading FromLangPath...<br/>';
		if(!empty($sThreeLangPath)&& is_dir($sThreeLangPath)){
			$arrThreeLangPath=self::includeLangFile($sThreeLangPath);
		}
		else{
			$arrThreeLangPath=array();
		}
		echo 'Reading ThreeLangPath...<br/>';
		echo '------------------------------------------------------------------------------------------------------------------------------------<br/>';
			foreach($arrFromLangPaths as $sFromLangPaths){
				if(!empty($arrThreeLangPath)){
					$sThreeLangPaths=array_shift($arrThreeLangPath);
				}
				else{
					$sThreeLangPaths='';
				}
				echo 'Init savepath...<br/>';
				echo 'FromLangPath File '.$sFromLangPaths.'...<br/>';
				echo 'ToLangPath File '.$sToLangPath.'/'.basename($sFromLangPaths).'...<br/>';
				echo 'ThreeLangPath File '.$sThreeLangPaths.'...<br/><br/>';
				self::beforeSave($sFromLangPaths,$sThreeLangPaths);
				self::saveInit($sFromLangPaths,$sToLangPath.'/'.basename($sFromLangPaths),$sThreeLangPaths);
		}
	}

	/**
	 * 对源代码进行预处理一遍
	 *
	 * @access public
	 * @static
	 * @param string $sFromLangPath 语言包出处
	 * @param string $sThreeLangPath 第三方语言包
	 * @return void
	 */
	static public function beforeSave($sFromLangPath,$sThreeLangPath=''){
		$sFromLangs=self::readLangContent($sFromLangPath);// 读取原语言包
		if(is_file($sThreeLangPath)){$sThreeLangs=self::readLangContent($sThreeLangPath);}
		else{$sThreeLangs='';}
		if(empty($sThreeLangs)){
			$arrFromLangDatas= explode("\n",$sFromLangs);
			$arrFromLangDatas=self::filterLangData($arrFromLangDatas);
			self::saveContent($sFromLangPath,$arrFromLangDatas);
		}
		if(!empty($sThreeLangs)){
			$arrThreeLangDatas= explode("\n",$sThreeLangs);
			$arrThreeLangDatas=self::filterLangData($arrThreeLangDatas);
			self::saveContent($sThreeLangPath,$arrThreeLangDatas);
		}
	}

	/**
	 * 单独处理一个语言包
	 *
	 * @access public
	 * @static
	 * @param string $sFromLangPath 语言包出处
	 * @param string $sToLangPath 语言包输出路径
	 * @param string $sThreeLangPath 第三方语言包
	 * @return void
	 */
	static public function saveInit($sFromLangPath,$sToLangPath,$sThreeLangPath=''){
		$arrFromLangs=self::readLangData($sFromLangPath);// 读取原语言包
		if(is_file($sThreeLangPath)){$arrThreeLangs=self::readLangData($sThreeLangPath);}
		else{$arrThreeLangs=array();}
		self::save($arrFromLangs,$sToLangPath,$arrThreeLangs);
	}

	/**
	 * 保存预处理后的源代码
	 *
	 * @access public
	 * @static
	 * @param string $sLangPath 语言包路径
	 * @param array $arrLangDatas 语言包数据
	 * @return void
	 */
	static public function saveContent($sLangPath,$arrLangDatas){
		if(!is_dir(dirname($sLangPath))&& !self::makeDir(dirname($sLangPath))){
			exit(printf('Can not create directory %s,set directory permissions to 0777.',dirname($sLangPath)));
		}
		if(!file_put_contents($sLangPath,implode("\n",$arrLangDatas))){
			exit(printf('File %s is not writable',$sLangPath));
		}
	 }

	/**
	 * 处理后的语言包
	 *
	 * @access public
	 * @static
	 * @param array $arrLangs 语言包数据
	 * @param string $sToPath 待输出的路径
	 * @param array $arrThreeLangs 第三方语言，针对键值损坏的语言包
	 * @return void
	 */
	static public function save($arrLangs,$sToPath,$arrThreeLangs=array()){
		echo 'Deal with Lang...<br/>';
		$sOut='<'."?php\r\n" ;// 待保存的数据
		$sOut.='/** DoYouHaoBaby Framework Lang File, Do not to modify it! */'."\r\n" ;
		$sOut.='return array('."\r\n";
		foreach($arrLangs as $sKey=>$sValue){
			$sKey=self::filterKey($sKey);
			if(!empty($arrThreeLangs)){
				$sValue=array_shift($arrThreeLangs);
			}
			$sValue=self::filterValue($sValue);
			$sOut.="'{$sKey}'=>{$sValue},\r\n" ;
			echo "'{$sKey}'=>{$sValue}<br/>" ;
		}
		$sOut.=")\r\n";
		$sOut.="\r\n?".'>' ;
		if(!is_dir(dirname($sToPath)) && !self::makeDir(dirname($sToPath))){
			 exit(printf('Can not create directory %s,set directory permissions to 0777.',dirname($sToPath)));
		}
		if(!file_put_contents($sToPath,$sOut)){
			exit(printf('File %s is not writable',$sToPath));
		}
		echo '<br/><br/>';
	}

	/**
	 * 预处理源代码
	 *
	 * @access public
	 * @static
	 * @param $arrFromLangDatas array 待处理的语言包值
	 * @return array
	 */
	static public function filterLangData($arrFromLangDatas){
		$arrSaveFromLangDatas=array();
		foreach($arrFromLangDatas as $sFromLangDatas){
			$sFromLangDatas=str_ireplace('\ $','\$',$sFromLangDatas);
			$sFromLangDatas=trim($sFromLangDatas);
			$sFromLangDatas=trim($sFromLangDatas,',');
			if(strrpos($sFromLangDatas,'=>')==(strlen($sFromLangDatas)-2)||strrpos($sFromLangDatas,'=>')==strlen($sFromLangDatas)-1){
				$arrSaveFromLangDatas[]=$sFromLangDatas;
			}
			elseif(strpos($sFromLangDatas,'=>')){
				$arrSaveFromLangDatas[]=self::filterCodeByCut($sFromLangDatas,'=>');
			}
			else{
				$sFromLangDatas=trim($sFromLangDatas,'=');
				$sFromLangDatas=trim($sFromLangDatas);
				$sFromLangDatas=rtrim($sFromLangDatas,',');
				$sFromLangDatas=rtrim($sFromLangDatas,'.');
				if($sFromLangDatas!='?>'){
					$sFromLangDatas=rtrim($sFromLangDatas,'>');
				}
				if(strpos($sFromLangDatas,'\'>')||strpos($sFromLangDatas,'">')){
					$arrSaveFromLangDatas[]=self::filterCodeByCut($sFromLangDatas,'>');
				}
				elseif(strpos($sFromLangDatas,'=')){
					$arrSaveFromLangDatas[]=self::filterCodeByCut($sFromLangDatas,'=');
				}
				else{
					$arrSaveFromLangDatas[]=$sFromLangDatas;
				}
			}
		}
		return $arrSaveFromLangDatas;
	}

	/**
	 * 按分隔符过滤数据
	 *
	 * @access public
	 * @static
	 * @param $sFromLangDatas string 源代码
	 * @return string
	 */
	static public function filterCodeByCut($sFromLangDatas,$sCut='=>'){
		if(strpos($sFromLangDatas,$sCut)){
			list($sKey,$sValue)= explode($sCut,$sFromLangDatas);
			$sKey=self::filterCodeKey($sKey);
			$sValue=self::filterCodeValue($sValue);
			return $sKey.'=>'.$sValue;
		}
		else{return $sFromLangDatas;}
	 }

	/**
	 * 过滤源代码值
	 *
	 * @access public
	 * @static
	 * @param $sValue string 源码值
	 * @return string
	 */
	static public function filterCodeValue($sValue){
		$sValue=trim($sValue);
		$sValue=trim($sValue,',');
		$sValue=trim($sValue,'.');
		$sValue=trim($sValue,'«');
		$sValue=trim($sValue,'»');
		$sValue=trim($sValue,'"');
		$sValue=trim($sValue,'\'');
		$sValue=trim($sValue,'"');
		$sValue=trim($sValue,'\'');
		$sValue=str_replace('\'','',$sValue);
		$sValue=str_replace('"','',$sValue);
		$sValue=str_replace('\'','',$sValue);
		$sValue='"'.$sValue.'",';
		return $sValue;
	}

	/**
	 * 过滤源代码键值
	 *
	 * @access public
	 * @static
	 * @param $sValue string 源码键值
	 * @return string
	 */
	static public function filterCodeKey($sKey){
		$sKey=trim($sKey);
		$sKey=trim($sKey,'\'');
		$sKey=trim($sKey,'"');
		$sKey=trim($sKey,'«');
		$sKey=trim($sKey,'»');//echo $sKey;
		if(strpos($sKey,'\'')|| strpos($sKey,'"')){
			$sNum=substr($sKey,-3,2);
			$sKey=$sNum.substr($sKey,0,-3);
		}
		$sKey=str_replace('\'','',$sKey);
		$sKey=str_replace('=','',$sKey);
		$sKey=str_replace('"','',$sKey);
		$sKey=str_replace(' ','',$sKey);
		$sKey=str_replace('.','',$sKey);
		$sKey='\''.strtolower(trim($sKey)).'\'';
		return $sKey;
	}

	/**
	 * 过滤语言包值
	 *
	 * @access public
	 * @static
	 * @param $sValue mixed 待处理的语言包值
	 * @return string
	 */
	static public function filterValue($sValue){
		if($sValue===false){return 'FALSE';}
		if($sValue===true){return 'TRUE';}
		if($sValue==''){return '""';}
		$sValue= trim($sValue);// 清除两边的空格
		$sValue=trim($sValue,',');// 清除两边的',',制作数组的时候产生的
		$sValue= trim($sValue);
		$sValue=trim($sValue,'،');
		$sValue= trim($sValue);
		$sValue=trim($sValue,'"');
		$sValue= trim($sValue);
		$sValue=trim($sValue,'\'');
		$sValue= trim($sValue);
		$sValue=str_replace('"','\\"',$sValue);// 转义字符
		$sValue=str_replace("\n","\\n",$sValue);
		$sValue=str_replace("\r","\\r",$sValue);
		$sValue=str_replace('$','\\$',$sValue);
		$sValue=str_ireplace('% s','%s',$sValue);//纠正分离的%s,%d等
		$sValue=str_ireplace('% d','%d',$sValue);
		$sValue=str_ireplace('％d','%d',$sValue);
		$sValue=str_ireplace('％s','%s',$sValue);
		$sValue=str_ireplace('% з','',$sValue);
		$sValue=str_ireplace('</ strong>','</strong>',$sValue);//纠正一些常用的畸形
		$sValue=str_ireplace('<stong>','<strong>',$sValue);
		$sValue=str_ireplace('</ stong>','</strong>',$sValue);
		$sValue=str_ireplace('</ Stöng>','</strong>',$sValue);
		$sValue=str_ireplace('\ \$',' \$',$sValue);
		$sValue=str_ireplace('\\",','\\"',$sValue);
		$sValue=str_ireplace('\',','\\"',$sValue);
		$sValue=str_ireplace('</ silných>','</strong>',$sValue);
		$sValue=str_ireplace('</ a>','</a>',$sValue);
		$sValue=str_ireplace('</ p>','</p>',$sValue);
		$sValue=str_ireplace('\ r \ n','\r\n',$sValue);
		$sValue=str_ireplace('< p>','<p>',$sValue);
		$sValue=str_ireplace('</ b>','</b>',$sValue);
		$sValue=str_ireplace('< / b>','</b>',$sValue);
		$sValue=str_replace('<B>','<b>',$sValue);
		$sValue=str_replace('<EM>','<em>',$sValue);
		$sValue=str_ireplace('</ EM>','</em>',$sValue);
		return '"'.trim($sValue).'"';
	}

	/**
	 * 过滤键值
	 *
	 * @access public
	 * @static
	 * @param $sKey string 待处理的语言包键值
	 * @return string
	 */
	static public function filterKey($sKey){
		$sKey=trim($sKey); // 清理键值两边的空格
		$sKey=trim($sKey,','); // 清理键值的','
		$sKey=str_replace(' ','',$sKey);// 清理键值中间的空格
		$sKey=str_replace('.','',$sKey);//清理键值中'.'
		$sKey=strtolower($sKey);// 将键值转化为小写
		$sKey=strtolower($sKey);// 日本语键值
		$sKey=trim($sKey,'は');
		return $sKey;
	}

	/**
	 * 读取一个语言包数据
	 *
	 * @access public
	 * @static
	 * @param $sLangPath string 语言包
	 * @return array
	 */
	static public function readLangData($sLangPath){
		if(!is_file($sLangPath)){
			exit(printf('Language pack %s does not exist',$sLangPath));
		}
		return (array)(include $sLangPath);
	}

	/**
	 * 读取一个语言包内容
	 *
	 * @access public
	 * @static
	 * @param $sLangPath string 语言包
	 * @return string
	 */
	static public function readLangContent($sLangPath){
		if(!is_file($sLangPath)){
			exit(printf('Language pack %s does not exist',$sLangPath));
		}
		return file_get_contents($sLangPath);
	}

	/**
	 * 创建目录
	 *
	 * @access public
	 * @static
	 * @param $Dir string 目录
	 * @param $nMode int 权限
	 * @return bool
	 */
	static public function makeDir($Dir,$nMode=0777){
		if(is_dir($Dir)){
			return true;
		}
		if(is_string($Dir)){
			$arrDirs=explode('/',str_replace('\\','/',trim($Dir,'/')));
		}
		else{
			$arrDirs=$Dir;
		}
		$sMakeDir=IS_WIN?'':'/';
		foreach($arrDirs as $sDir){
			$sMakeDir.=$sDir.'/';
			!is_dir($sMakeDir) && mkdir($sMakeDir,$nMode);
		}
		return TRUE;
	 }

	/**
	 *读取一个目录中的所有PHP 语言包
	 *
	 * @access public
	 * @static
	 * @param $sPath string 目录
	 * @return array
	 */
	static function includeLangFile($sPath){
		if(!is_dir($sPath)){
			exit(printf('Language pack Dir %s does not exist',$sPath));
		}
		return glob($sPath.'/*.php');
	}

}
