<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   HTML静态化生成管理类($) */

!defined('DYHB_PATH') && exit;

class Html{

	static private $_nCacheTime;
	static private $_bRequireCache=false;

	static private function RC(){
		$arrHtmls=$GLOBALS['_commonConfig_']['_HTML_']; // 读取静态规则
		if(!empty($arrHtmls)){
			// 静态规则文件定义格式 controllerName=>array(‘静态规则’,’缓存时间’,’附加规则')
			// 'read'=>array('{id},{name}',60,'md5')必须保证静态规则的唯一性 和 可判断性
			// 检测静态规则
			if(isset($arrHtmls[ MODULE_NAME.':'.ACTION_NAME])){$arrHtml=$arrHtmls[MODULE_NAME.':'.ACTION_NAME ]; }// 某个模块的操作的静态规则
			elseif(isset($arrHtmls[ MODULE_NAME.':'])){$arrHtml=$arrHtmls[ MODULE_NAME.':'];}// 某个模块的静态规则
			elseif(isset($arrHtmls[ ACTION_NAME])){$arrHtml=$arrHtmls[ ACTION_NAME ]; }// 所有操作的静态规则
			elseif(isset($arrHtmls['*'])){$arrHtml=$arrHtmls['*'];}// 全局静态规则
			elseif(isset($arrHtmls['empty:index'])&& !class_exists(ucwords(MODULE_NAME).'Controller')){$arrHtml=$arrHtmls['empty:index'];}// 空模块静态规则
			elseif(isset($htmls[  MODULE_NAME.':empty_'])&& self::EC(MODULE_NAME, ACTION_NAME)){$arrHtml=$arrHtmls[MODULE_NAME.':empty_']; }// 空操作静态规则
			if(!empty($arrHtml)){
				self::$_bRequireCache=true;// 需要缓存
				$sRule=$arrHtml[0];// 解读静态规则
				$sRule =preg_replace('/{\$(_\w+)\.(\w+)\|(\w+)}/e',"\\3(\$\\1['\\2'])",$sRule);// 以$_开头的系统变量
				$sRule =preg_replace('/{\$(_\w+)\.(\w+)}/e',"\$\\1['\\2']",$sRule);
				$sRule =preg_replace('/{(\w+)\|(\w+)}/e',"\\2(\$_GET['\\1'])",$sRule);// {ID|FUN} GET变量的简写
				$sRule =preg_replace('/{(\w+)}/e',"\$_GET['\\1']",$sRule);
				$sRule =str_ireplace(// 特殊系统变量
					array('{:app}','{:module}','{:action}'),
					array(APP_NAME,MODULE_NAME,ACTION_NAME),
					$sRule
				);
				$sRule=preg_replace('/{|(\w+)}/e',"\\1()",$sRule);// {|FUN} 单独使用函数
				if(!empty($arrHtml[2]))$sRule=$arrHtml[2]($sRule); // 应用附加函数
				self::$_nCacheTime=isset($arrHtml[1])?$arrHtml[1]:$GLOBALS['_commonConfig_']['HTML_CACHE_TIME']; // 缓存有效期
				define('HTML_FILE_NAME',APP_PATH.'/App/Html/'.$sRule.$GLOBALS['_commonConfig_']['HTML_FILE_SUFFIX']);// 当前缓存文件
				return true;
			}
		}
		return false;
	}

	public static function R(){
		 if(self::RC()&& self::C(HTML_FILE_NAME,self::$_nCacheTime)){ //静态页面有效
			if($GLOBALS['_commonConfig_']['HTML_READ_TYPE']==1){G::urlGoTo(str_replace(array(realpath($_SERVER["DOCUMENT_ROOT"]),"\\"),array('',"/"),realpath(HTML_FILE_NAME)));}// 重定向到静态页面
			else{
				readfile(HTML_FILE_NAME);
				exit();
			}
		}
		return ;
	}

	static public function W($sContent){
		if(self::$_bRequireCache){
			// 静态文件写入
			// 如果开启HTML功能 检查并重写HTML文件
			// 没有模版的操作不生成静态文件
			if(!is_dir(dirname(HTML_FILE_NAME))){G::makeDir(dirname(HTML_FILE_NAME));}
			if(false===file_put_contents(HTML_FILE_NAME,$sContent)){G::E(G::L('模版编译或者静态无法写入。','dyhb'));}
		}
		return;
	}

	static public function C($sCacheFile='',$CacheTime=''){
		if(!is_file($sCacheFile)){return false;}// 存在缓冲文件
		elseif(filemtime(__TMPL_FILE_PATH__)>filemtime($sCacheFile)){return false;}// 模板文件如果更新静态文件需要更新
		elseif(!is_numeric($CacheTime)&& function_exists($CacheTime)){return $CacheTime($sCacheFile);}// 使用函数
		elseif($CacheTime!=-1&&time()>filemtime($sCacheFile)+$CacheTime){return false;}// 文件是否在有效期
		return true;
	}

	static private function EC($sModule,$sAction){
		$sClassName= ucwords($sModule).'Controller';
		$oClass=new $sClassName;
		return !method_exists($oClass,$sAction);
	}

}
