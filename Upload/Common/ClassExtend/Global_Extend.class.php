<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   全局扩展函数($) */

!defined('DYHB_PATH') && exit;

class Global_Extend{

	static public function getOption($sOptionName){
		return OptionModel::getOption($sOptionName);
	}

	static public function isLogin($bData=false){
		$arrUserData=$GLOBALS['___login___'];
		if($arrUserData!==false){
			if($bData===true){
				return $arrUserData;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}

	static public function visitor(){
		$sBrowsers='MSIE|Netscape|Opera|Konqueror|Mozilla';// 浏览器
		$sSpiders='Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';// 机器人
		if(!preg_match("/({$sSpiders})/",$_SERVER['HTTP_USER_AGENT']) && preg_match("/($sBrowsers)/",$_SERVER['HTTP_USER_AGENT'])){
			$sOnlineIp=E::getIp();
			$sCookieIp=G::cookie('visitor_ip');
			if($sCookieIp!=$sOnlineIp){
				G::cookie('visitor_ip',$sOnlineIp,86400);
				$sNowTime=date('Ymd',CURRENT_TIMESTAMP);
				$oOptionModel=OptionModel::F(array('option_name'=>'day_time','option_value'=>$sNowTime))->query();
				if($oOptionModel instanceof OptionModel){
					OptionModel::uploadOption('day_time',$sNowTime);
					OptionModel::uploadOption('today_visited_num',1);
				}
				else{
					OptionModel::uploadOption('today_visited_num',(Global_Extend::getOption('today_visited_num')+1));
				}
				OptionModel::uploadOption('all_visited_num',(Global_Extend::getOption('all_visited_num')+1));
				Cache_Extend::front_widget_static();
			}
		}
	}

	static public function getEmot($arrMatches){
		$arrResult=Dyhb::cache('global_emot');
		$sCacheType=APP_NAME==='blog'?'blog':'admin';
		if($arrResult===false){// 没有缓存，则更新一遍缓存
			Cache_Extend::global_emot($sCacheType);
			Cache_Extend::global_emot('blog');
			$arrResult=Dyhb::cache('global_emot');
		}
		$sCurrentEmot=$arrMatches[1];
		$sEmotImage=self::getEmotByname($sCurrentEmot);
		return "<img src=\"{$sEmotImage}\" border=\"0\" alt=\"{$sCurrentEmot}\" />";
	}

	static public function getEmotByname($sName){
		$arrResult=Dyhb::cache('global_emot');
		$sCacheType=APP_NAME==='blog'?'blog':'admin';
		if($arrResult===false){// 没有缓存，则更新一遍缓存
			Cache_Extend::global_emot($sCacheType);
			Cache_Extend::global_emot('blog');
			$arrResult=Dyhb::cache('global_emot');
		}
		$sEmotImages=$arrResult[$sName]['image'];
		return __PUBLIC__."/Images/Emot/{$sEmotImages}";
	}

	static public function getBadwordCache(){
		$arrResult=Dyhb::cache('badword');
		if($arrResult===false){// 没有缓存，则更新一遍缓存
			Cache_Extend::front_badword();
			$arrResult=Dyhb::cache('badword');
		}
		return $arrResult;
	}

	static public function backword($sContent){
		$sContent=trim($sContent);
		if(empty($sContent)){
			return '';
		}
		if(Global_Extend::getOption('global_badword_on')==0){
			return $sContent;
		}
		$arrBadwords=self::getBadwordCache();
		foreach($arrBadwords as $arrBadword){
			$sContent=preg_replace($arrBadword['regex'],$arrBadword['value'],$sContent);
		}
		return $sContent;
	}

	static public function replaceEmot($sContent){
		return preg_replace_callback("/\[emot\]([^ ]+?)\[\/emot\]/is",array('Global_Extend','getEmot'),$sContent);
	}

	static public function updateCountCacheData(){
		$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
		if(trim($oCacheData['countcache_todaydate'])!=trim(date('Y-m-d',CURRENT_TIMESTAMP))){
			$oCacheData['countcache_yesterdaynum']=$oCacheData['countcache_todaynum'];
			$oCacheData['countcache_todaydate']=date('Y-m-d',CURRENT_TIMESTAMP);
			if($oCacheData['countcache_todaynum']>$oCacheData['countcache_mostnum']){
				$oCacheData['countcache_mostnum']=$oCacheData['countcache_todaynum'];
			}
			$oCacheData['countcache_todaynum']=0;
			$oCacheData->save(0,'update');
			$oDb=Db::RUN();
			$sSql="UPDATE ". CategoryModel::F()->query()->getTablePrefix() ."category SET category_todaycomments=0";
			$oDb->query($sSql);
		}
		return $oCacheData;
	}

	static public function hideIp($sIp){
		return preg_replace('/((?:\d+\.){3})\d+/',"\\1*",$sIp);
	}

	static function aidencode($nId) {
		static $sSidAuth='';
		$sSidAuth=$sSidAuth!=''?$sSidAuth:E::authcode(G::cookie($GLOBALS['_commonConfig_']['RBAC_DATA_PREFIX'].'hash'),false);
		return rawurlencode(base64_encode($nId.'|'.substr(md5($nId.md5($GLOBALS['_commonConfig_']['DYHB_AUTH_KEY']).CURRENT_TIMESTAMP),0,8).'|'.CURRENT_TIMESTAMP.'|'.$sSidAuth));
	}

	static function runHooks(){
		if(!defined('HOOKTYPE')){
			define('HOOKTYPE','hookscript');
		}
		if(defined('CURMODULE')){
			$arrPluginHooks=Model::C('plugin_hooks','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			if($arrPluginHooks==false){
				Cache_Extend::global_plugin_hooks();
				Cache_Extend::global_plugin_hooks('blog');
				$arrPluginHooks=Model::C('plugin_hooks','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			}
			if($arrPluginHooks['plugins'][HOOKTYPE.'_common']){
				self::hookScript('common','global','funcs',array(),'common');
			}
			self::hookScript(CURMODULE,BASESCRIPT);
		}
	}

	static function hookScript($sScript,$sHScript,$sType='funcs',$arrParam=array(),$sFunc=''){
		static $arrPluginClasses;
		$arrPluginHooks=Model::C('plugin_hooks','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		if($arrPluginHooks==false){
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$arrPluginHooks=Model::C('plugin_hooks','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}

		if(!isset($arrPluginHooks[HOOKTYPE][$sHScript][$sScript][$sType])){
			return;
		}

		$arrGlobalPlugins=Model::C('global_plugin','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		if($arrGlobalPlugins==false){
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			$arrGlobalPlugins=Model::C('global_plugin','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}

		foreach((array)$arrPluginHooks[HOOKTYPE][$sHScript][$sScript]['module'] as $sIdentifier=>$sInclude){
			@include_once DYHB_PATH.'/../Public/Plugin/'.$sInclude.'.class.php';
		}

		if(@is_array($arrPluginHooks[HOOKTYPE][$sHScript][$sScript][$sType])){
			$GLOBALS['inhookscript']=true;
			$sFunc=!$sFunc?$arrPluginHooks[HOOKTYPE][$sHScript][$sScript][$sType]:array($sFunc=>$arrPluginHooks[HOOKTYPE][$sHScript][$sScript][$sType][$sFunc]);
			foreach($sFunc as $sHookkey=>$arrHookFuncs){
				foreach($arrHookFuncs as $arrHookFunc2){
					$sClassKey='plugin_'.($arrHookFunc2[0].($sHScript!='global'?'_'.$sHScript:''));
					if(!class_exists($sClassKey)){
						continue;
					}
					if(!isset($arrPluginClasses[$sClassKey])){
						$arrPluginClasses[$sClassKey]=new $sClassKey;
					}
					if(!method_exists($arrPluginClasses[$sClassKey],$arrHookFunc2[1])){
						continue;
					}
					$return=$arrPluginClasses[$sClassKey]->$arrHookFunc2[1]($arrParam);
					if(is_array($return)){
						if(!isset($GLOBALS['pluginhooks'][$sHookkey]) || is_array($GLOBALS['pluginhooks'][$sHookkey])){
							foreach($return as $k=>$v){
								$GLOBALS['pluginhooks'][$sHookkey][$k]=$v;
							}
						}
					}else{
						if(@!is_array($GLOBALS['pluginhooks'][$sHookkey])){
							$GLOBALS['pluginhooks'][$sHookkey]=$return;
						}else{
							foreach($GLOBALS[$sHookkey] as $k=>$v){
								$GLOBALS['pluginhooks'][$sHookkey][$k]=$return;
							}
						}
					}
				}
			}
		}
		$GLOBALS['inhookscript']=false;
	}

	static function hookScriptOutput($sTplFile){
		if(!empty($GLOBALS['hookscriptoutput'])){
			return;
		}
		self::hookScript('global','global');
		if(defined('CURMODULE')){
			$arrParam=array('template'=>$sTplFile);
			self::hookScript(CURMODULE,BASESCRIPT,'outputfuncs',$arrParam);
		}
		$GLOBALS['hookscriptoutput']=true;
	}

	static public function templateHooks($sHookName){
		$bPluginDeveloper=defined('PLUGINDEVELOPER') && PLUGINDEVELOPER===TRUE;
		$sOut='';
		if($bPluginDeveloper){
			$sOut.='<hook>['.(!strpos($sHookName,'/')?'string':'array').' '.$sHookName.']</hook>';
		}
		if(!strpos($sHookName,'/')){
			if(!empty($GLOBALS['pluginhooks'][$sHookName])){
				$sOut.=$GLOBALS['pluginhooks'][$sHookName];
			}
		}else{
			$arrValue=explode('/',$sHookName);
			$sOut.=$GLOBALS['pluginhooks'][$arrValue[0]][$arrValue[1]];
		}
		return $sOut;
	}

}
