<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   系统 缓存文件($) */

!defined('DYHB_PATH') && exit;

class Cache_Extend{

	public static function config($sApp='admin'){
		$sConfigFilePath=DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Config.php';
		if(is_file($sConfigFilePath)){
			unlink($sConfigFilePath);
		}
	}

	public static function global_option($sApp='admin'){
		$arrOptionData=OptionModel::F()->asArray()->all()->query();
		if($arrOptionData===false){ return false; }
		foreach($arrOptionData as $nKey=>$arrValue){
			$arrOptionData[$arrValue['option_name']]=$arrValue['option_value'];
			unset($arrOptionData[$nKey]);
		}
		Model::C('global_option',$arrOptionData,array('cache_path'=>DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Data/DbCache'));
	}

	public static function global_plugin($sApp='admin'){
		$arrData=array();
		$arrPlugins=PluginModel::F('plugin_active=?',1)->getAll();
		foreach($arrPlugins as $oPlugin){
			$sDir=$oPlugin['plugin_dir'];
			$oPlugin['plugin_module']=@unserialize($oPlugin['plugin_module']);
			$arrVars=PluginvarModel::F('plugin_id=?',$oPlugin['plugin_id'])->getAll();
			foreach($arrVars as $oVar){
				$arrData[$oPlugin['plugin_identifier']][$oVar['pluginvar_variable']]=$oVar['pluginvar_value'];
			}
		}
		Model::C('global_plugin',$arrData,array('cache_path'=>DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Data/DbCache'));
	}

	public static function get_cachedata_plugin($sMethod=''){
		$arrData=array();
		$arrData['plugins']=$arrData['pluginlinks']=$arrData['hookscript']=array();
		$arrData['plugins']['hookscript_common']=false;
		$arrPlugins=PluginModel::F()->asArray()->getAll();
		$arrData['plugins']['active']=array();
		foreach($arrPlugins as $arrPlugin){
			$bActive=(!$sMethod && $arrPlugin['plugin_active']) || ($sMethod && ($arrPlugin['plugin_active'] || $sMethod==$arrPlugin['plugin_identifier']));
			$arrPlugin['plugin_module']=@unserialize($arrPlugin['plugin_module']);
			if($bActive){
				$arrData['plugins']['active'][]=$arrPlugin['plugin_identifier'];
				$arrData['plugins']['version'][$arrPlugin['plugin_identifier']]=$arrPlugin['plugin_version'];
			}
			if(is_array($arrPlugin['plugin_module'])){
				$arrPluginMenus=array();
				foreach($arrPlugin['plugin_module'] as $k=>$arrModule){
					if($bActive && isset($arrModule['name'])){
						$k='';
						switch($arrModule['type']){
							case 'front_main_menu':
								$arrPluginMenus[$arrPlugin['plugin_identifier']][$arrModule['name']]=$arrModule;
								break;
							case 'admin_plugin_menu':
								break;
							case 'front_template_in':
								$k='hookscript';
								$sScript=$arrPlugin['plugin_dir'].'/'.$arrModule['name'];
								@include_once DYHB_PATH.'/../Public/Plugin/'.$sScript.'.class.php';
								$arrClasses=get_declared_classes();
								$arrClassNames=array();
								$sNameKey='plugin_'.$arrPlugin['plugin_identifier'];
								$nCnLen=strlen($sNameKey);
								foreach($arrClasses as $sClassName){
									if(substr($sClassName,0,$nCnLen)==$sNameKey){
										$sHScript=substr($sClassName,$nCnLen+1);
										$arrClassNames[$sHScript?$sHScript:'global']=$sClassName;
									}
								}
								foreach($arrClassNames as $sHScript=>$arrClassName){
									$arrHookMethods=get_class_methods($arrClassName);
									foreach($arrHookMethods as $sFuncName){
										if($sHScript=='global' && $sFuncName=='common'){
											$arrData['plugins'][$k.'_common']=true;
										}
										$arrV=explode('_',$sFuncName);
										$sCurScript=$arrV[0];
										if(!$sCurScript || $sClassName==$sFuncName){
											continue;
										}
										if(!@in_array($sScript,$arrData[$k][$sHScript][$sCurScript]['module'])){
											$arrData[$k][$sHScript][$sCurScript]['module'][$arrPlugin['plugin_identifier']]=$sScript;
										}
										if(preg_match('/\_output$/',$sFuncName)){
											$sVarName=preg_replace('/\_output$/','',$sFuncName);
											$arrData[$k][$sHScript][$sCurScript]['outputfuncs'][$sVarName][]=array('displayorder'=>$arrModule['displayorder'],'func'=>array($arrPlugin['plugin_identifier'],$sFuncName));
										}elseif(preg_match('/\_message$/',$sFuncName)){
											$sVarName=preg_replace('/\_message$/','',$sFuncName);
											$arrData[$k][$sHScript][$sCurScript]['messagefuncs'][$sVarName][]=array('displayorder'=>$arrModule['displayorder'],'func'=>array($arrPlugin['plugin_identifier'],$sFuncName));
										}else{
											$arrData[$k][$sHScript][$sCurScript]['funcs'][$sFuncName][]=array('displayorder'=>$arrModule['displayorder'],'func'=>array($arrPlugin['plugin_identifier'],$sFuncName));
										}
									}
								}
								break;
						}
					}
				}
				Model::C('plugin_menu',$arrPluginMenus,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
				Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			}
		}

		Model::C('plugin_active',$arrData['plugins']['active'],array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));

		$arrData['pluginhooks']=array();
		foreach(array('hookscript') as $sHookType){
			foreach($arrData[$sHookType] as $sHScript=>$arrHookScript){
				foreach($arrHookScript as $sCurScript=>$arrScriptData){
					if(isset($arrScriptData['funcs']) && is_array($arrScriptData['funcs'])){
						foreach($arrScriptData['funcs'] as $sFuncName=>$arrFuncs){
							usort($arrFuncs,array('Cache_Extend','module_cmp'));
							$arrTmp=array();
							foreach($arrFuncs as $k=>$v){
								$arrTmp[$k]=$v['func'];
							}
							$arrData[$sHookType][$sHScript][$sCurScript]['funcs'][$sFuncName]=$arrTmp;
						}
					}
					if(isset($arrScriptData['outputfuncs']) && is_array($arrScriptData['outputfuncs'])){
						foreach($arrScriptData['outputfuncs'] as $sFuncName=>$arrFuncs){
							usort($arrFuncs,array('Cache_Extend','module_cmp'));
							$arrTmp=array();
							foreach($arrFuncs as $k=>$v){
								$arrTmp[$k]=$v['func'];
							}
							$arrData[$sHookType][$sHScript][$sCurScript]['outputfuncs'][$sFuncName]=$arrTmp;
						}
					}
					if(isset($arrScriptData['messagefuncs']) && is_array($arrScriptData['messagefuncs'])){
						foreach($arrScriptData['messagefuncs'] as $sFuncName=>$arrFuncs){
							usort($arrFuncs,array('Cache_Extend','module_cmp'));
							$arrTmp=array();
							foreach($arrFuncs as $k=>$v){
								$arrTmp[$k]=$v['func'];
							}
							$arrData[$sHookType][$sHScript][$sCurScript]['messagefuncs'][$sFuncName]=$arrTmp;
						}
					}
				}
			}
		}

		return array($arrData['plugins'],$arrData['pluginlinks'],$arrData['hookscript']);
	}

	static public function global_plugin_hooks($sApp='admin'){
		$arrPluginData=array();
		list($arrPluginData['plugins'],$arrPluginData['pluginlinks'],$arrPluginData['hookscript'])=self::get_cachedata_plugin();
		Model::C('plugin_hooks',$arrPluginData,array('cache_path'=>DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Data/DbCache'));
	}

	static public function module_cmp($arrA,$arrB){
		return $arrA['displayorder']>$arrB['displayorder']?1:-1;
	}

	static public function front_javascript(){
		$sDir=DYHB_PATH.'/../Public/Images/Js/';
		$hDh=opendir($sDir);
		$arrRemove=array(
			'/(^|\r|\n)\/\*.+?(\r|\n)\*\/(\r|\n)/is',
			'/\/\/note.+?(\r|\n)/i',
			'/\/\/debug.+?(\r|\n)/i',
			'/(^|\r|\n)(\s|\t)+/',
			'/(\r|\n)/',
			"/\/\*(.*?)\*\//ies",
		);

		while(($sEntry=readdir($hDh))!==false){
			if(E::getExtName($sEntry)=='js'){
				$sJavascriptFile=$sDir.$sEntry;
				$hFp=fopen($sJavascriptFile,'r');
				$sJavascritpData=@fread($hFp,filesize($sJavascriptFile));
				fclose($hFp);
				$sJavascritpData=preg_replace($arrRemove,'',$sJavascritpData);
				if(@$hFp=fopen(DYHB_PATH.'/../blog/App/~Runtime/Data/Javascript/'.$sEntry,'w')){
					fwrite($hFp,$sJavascritpData);
					fclose($hFp);
				}
				else{
					exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,$sDir,DYHB_PATH.'/../blog/App/~Runtime/Data/Javascript/'));
				}
			}
		}
	}

	public static function front_css(){
		$arrTheme=E::listDir(DYHB_PATH.'/../blog/Theme');
		$arrSaveDatas=array();
		$arrStyleIcons=array();
		foreach($arrTheme as $sTheme){
			$sPath=DYHB_PATH.'/../blog/Theme/'.ucfirst($sTheme).'/dyhb-x-blog-style-'.strtolower($sTheme).'.xml';
			if(file_exists($sPath)){
				$bWriteAble=false;
				if(is_writeable($sPath)){
					$bWriteAble=true;
				}
				$hFp=@fopen($sPath,'r');
				$sContents=@fread($hFp,filesize($sPath));
				@fclose($hFp);
				$arrData=Xml::xmlUnserialize(trim($sContents));
				$arrData=$arrData['root'];
				$arrTempVar=$arrData['data']['data'];
				unset($arrData['data']['data']);
				$arrSaveData=array_merge($arrData['data'],$arrTempVar);
			}
			else{
				$arrNonestyleTheme[]=$sTheme;
			}
			$arrDataNew=array();
			$arrSaveData['img_dir']=$arrSaveData['img_dir']?$arrSaveData['img_dir']:'Theme/Default/Public/Images';
			$arrSaveData['style_img_dir']=$arrSaveData['style_img_dir']?$arrSaveData['style_img_dir']:$arrSaveData['img_dir'];
			$arrSaveData['img_dir']=__ROOT__.'/blog/'.$arrSaveData['img_dir'];
			$arrSaveData['style_img_dir']=__ROOT__.'/blog/'.$arrSaveData['style_img_dir'];
			foreach($arrSaveData as $sKeyData=>$arrValue){
				if($sKeyData!='menu_hover_bg_color'&&substr($sKeyData,-8,8)=='bg_color'){
					$sNewKey=substr($sKeyData,0,-8).'bgcode';
					$arrDataNew[$sNewKey]=self::set_css_background($arrSaveData,$sKeyData);
				}
			}
			$arrSaveData=array_merge($arrSaveData,$arrDataNew);
			$arrStyleIcons[$arrSaveData['template_id']]=$arrSaveData['menu_hover_bg_color'];
			if(strstr($arrSaveData['logo'],',')){
				$arrFlash=explode(",",$arrSaveData['logo']);
				$arrFlash[0]=trim($arrFlash[0]);
				$arrFlash[0]=preg_match('/^http:\/\//i',$arrFlash[0])?$arrFlash[0]:$arrSaveData['style_img_dir'].'/'.$arrFlash[0];
				$arrSaveData['logo_str']="<embed src=\"".$arrFlash[0]."\" width=\"".trim($arrFlash[1])."\" height=\"".trim($arrFlash[2])."\" type=\"application/x-shockwave-flash\" wmode=\"transparent\"></embed>";
			}
			else{
				$arrSaveData['logo']=preg_match('/^http:\/\//i',$arrSaveData['logo'])?$arrSaveData['logo']:$arrSaveData['style_img_dir'].'/'.$arrSaveData['logo'];
				$arrSaveData['logo_str']="<img src=\"".$arrSaveData['logo']."\" alt=\"".Global_Extend::getOption('blog_name')."\" border=\"0\" />";
			}

			$nContentWidthInt=intval($arrSaveData['content_width']);
			$nContentWidthInt=$nContentWidthInt?$nContentWidthInt:600;
			$nImageMaxWidth=Global_Extend::getOption('image_max_width');
			if(substr(trim($nContentWidthInt),-1,1)!='%'){
				if(substr(trim($nImageMaxWidth),-1,1)!='%'){
					$arrSaveData['image_max_width']=$nImageMaxWidth>$nContentWidthInt?$nContentWidthInt:$nImageMaxWidth;
				}
				else{
					$arrSaveData['image_max_width']=intval($nContentWidthInt * $nImageMaxWidth / 100);
				}
			}
			else{
				if(substr(trim($nImageMaxWidth),-1,1)!='%'){
					$arrSaveData['image_max_width']='%'.$nImageMaxWidth;
				}
				else{
					$arrSaveData['image_max_width']=($nImageMaxWidth>$nContentWidthInt?$nContentWidthInt:$nImageMaxWidth).'%';
				}
			}
			$arrSaveData['verhash']=G::randString(3,null,true);
			$arrSaveDatas[strtolower($sTheme)]=$arrSaveData;
		}

		foreach($arrSaveDatas as $arrSaveData){
			$arrSaveData['__style_icons__']=$arrStyleIcons;
			$sTemplateIdPath=DYHB_PATH.'/../blog/App/~Runtime/Data/Css/'.ucfirst($arrSaveData['template_id']);
			if(!is_dir($sTemplateIdPath)&& !G::makeDir($sTemplateIdPath)){
				exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,'#__TEMPLATE__#',$sTemplateIdPath));
			}
			self::writeToCache(DYHB_PATH.'/../blog/App/~Runtime/Data/Css/'.ucfirst($arrSaveData['template_id']),'style.php',self::getCacheVars($arrSaveData,'CONST')) ;
			self::write_to_css_cache($arrSaveData);
		}
	}

	static public function writeToCache($sCachePath,$sCacheName,$sCacheData=''){
		if(!is_dir($sCachePath)){echo '12345';
			if(!G::makeDir($sCachePath)){
				exit(G::L('创建缓存目录%s失败','app',null,$sCachePath));
			}
		}

		if($hFp=@fopen($sCachePath.'/'.$sCacheName,'wb')){
			fwrite($hFp,"<?php \n//DYHB.BLOG X! cache file,DO NOT modify me!".
							"\n//Created: ".date("M j,Y,G:i").
							"\n!defined('DYHB_PATH') && exit;".
							"\n\n{$sCacheData}?>");
			fclose($hFp);
		}
		else{
			exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,'#__TEMPLATE__#',$sCachePath).'fddddddddddd');
		}
	}

	public static function getCacheVars($arrData,$sType='VAR'){
		$sEvaluate='';
		foreach($arrData as $sKey=>$sVal){
			if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/",$sKey)){
				continue;
			}
			if(is_array($sVal)){
				$sEvaluate.="\$$sKey=".self::arrayEval($sVal).";\n";
			}
			else{
				$sVal=addcslashes($sVal,'\'\\');
				$sEvaluate.=$sType=='VAR'?"\$$sKey='$sVal';\n":"define('".strtoupper($sKey)."','$sVal');\n";
			}
		}
		return $sEvaluate;
	}

	static public function arrayEval($arrValue,$nLevel=0){
		if(!is_array($arrValue)){
			return "'".$arrValue."'";
		}
		if(is_array($arrValue) && function_exists('var_export')){
			return var_export($arrValue,true);
		}
		$sSpace='';
		for($nI=0;$nI<=$nLevel;$nI++){
			$sSpace.="\t";
		}
		$sEvaluate="Array\n{$sSpace}(\n";
		$sComma=$sSpace;
		if(is_array($arrValue)){
			foreach($arrValue as $key=>$val){
				$key=is_string($key)?'\''.addcslashes($key,'\'\\').'\'':$key;
				$val=!is_array($val) &&(!preg_match("/^\-?[1-9]\d*$/",$val) || strlen($val) > 12)?'\''.addcslashes($val,'\'\\').'\'':$val;
				if(is_array($val)){
					$sEvaluate.="{$sComma}{$key}=>".self::arrayEval($val,$nLevel + 1);
				}
				else{
					$sEvaluate.="{$sComma}{$key}=>{$val}";
				}
				$sComma=",\n{$sSpace}";
			}
		}
		$sEvaluate.="\n{$sSpace})";
		return $sEvaluate;
	}

	public static function write_to_css_cache($arrData=array()){
		$sCssData='';
		foreach(array('style'=>array('style','style_append'),
			'common'=>array('common','common_append'),
			'widget'=>array('widget','widget_append'),
			'layout'=>array('layout','layout_append'),
			'page'=>array('page','page_append')) as $sExtra=>$arrCssfiles){
			$sCssData='';
			foreach($arrCssfiles as $sCss){
				$sCssfile=DYHB_PATH.'/../blog/Theme/'.ucfirst($arrData['template_id']).'/Public/Css/'.$sCss.'.css';
				!file_exists($sCssfile) && $sCssfile=DYHB_PATH.'/../blog/Theme/'.ucfirst($arrData['doyouhaobaby_template_base']).'/Public/Css/'.$sCss.'.css';
				!file_exists($sCssfile) && $sCssfile=DYHB_PATH.'/../blog/Theme/Default/Public/Css/'.$sCss.'.css';
				if(file_exists($sCssfile)){
					$hFp=fopen($sCssfile,'r');
					$sCssData.=@fread($hFp,filesize($sCssfile))."\n\n";
					fclose($hFp);
				}
			}
			$sCssData=@preg_replace("/\{([A-Z0-9_]+)\}/e",'\$arrData[strtolower(\'\1\')]',stripslashes($sCssData));
			$sCssData=preg_replace("/<\?.+?\?>\s*/",'',$sCssData);
			if($sExtra!='style'){
				$sCssData=preg_replace(array('/\s*([,;:\{\}])\s*/','/[\t\n\r]/','/\/\*.+?\*\//'),array('\\1','',''),$sCssData);
			}

			$sFileDir=DYHB_PATH.'/../blog/App/~Runtime/Data/Css/'.ucfirst($arrData['template_id']).'/';
			if(!is_dir($sFileDir)){
				if(!G::makeDir($sFileDir)){
					exit(G::L('创建缓存目录%s失败','app',null,$sFileDir));
				}
			}

			if(@$hFp=fopen($sFileDir.$sExtra.'.css','w')){
				fwrite($hFp,$sCssData);
				fclose($hFp);
				$arrScriptCsss=Glob($sFileDir.'/scriptstyle_*.css');
				foreach($arrScriptCsss as $sScriptCsss){
					if(!unlink($sScriptCsss)){
						exit(G::L('无法删除缓存文件,请检查缓存目录%s的权限是否为0777','app',null,$sFileDir));
					}
				}
			}
			else{
				exit(G::L('无法写入缓存文件,请检查原目录%s和缓存目录%s的权限是否为0777','app',null,dirname($sCssfile),DYHB_PATH.'/../blog/App/~Runtime/Data/Css/'.ucfirst($arrData['template_id']).'/'));
			}
		}

	}

	public static function set_css_background(&$arrSaveData,$sKeyData){
		$arrCode=$arrSaveData[$sKeyData];
		$sCss=$sCodeValue='';
		if(!empty($arrCode['color'])){
			$sCss.=strtolower($arrCode['color']);
			$sCodeValue=strtoupper($arrCode['color']);
		}
		if(!empty($arrCode['img'])){
			if(preg_match('/^http:\/\//i',$arrCode['img'])){
				$sCss.=' url("'.$arrCode['img'].'") ';
			}
			else{
				$sCss.=' url("'.$arrSaveData['style_img_dir'].'/'.$arrCode['img'].'") ';
			}
		}
		if(!empty($arrCode['extra'])){
			$sCss.=' '.$arrCode['extra'];
		}
		$arrSaveData[$sKeyData]=$sCodeValue;
		$sCss=trim($sCss);
		return $sCss?'background: '.$sCss:'';
	}

	static public function front_menu($sHome=''){
		$GLOBALS['_commonConfig_']['URL_MODEL']=Global_Extend::getOption('url_model');
		if(empty($sHome)){
			$sHome=G::L('首页');
		}
		$arrNormalMenusTrue=@unserialize(Global_Extend::getOption('normal_menu'));
		$arrNormalMenuOptions=E::mbUnserialize(Global_Extend::getOption('normal_menu_option'));
		$arrCustomNormalMenuOptions=E::mbUnserialize(Global_Extend::getOption('custom_normal_menu'));
		$arrSystemPages=BlogModel::F('blog_isshow=? AND blog_ispage=1',1)->all()->query();
		$arrPages=array();
		foreach($arrSystemPages as $oSystemPage){
			$arrPages['system_page_'.$oSystemPage->blog_id]=$oSystemPage;
		}
		$arrSaveMenus=array();
		$arrSaveMenus['home']=array(
			'title'=>$sHome,
			'description'=>G::L('博客首页'),
			'link'=>__ROOT__.'/index.php',
			'style'=>'',
			'target'=>'',
			'system'=>1,
		);
		foreach($arrNormalMenusTrue as $sNormalMenuTrueValue){
			$sStyle='';
			if(self::isCustomMenu($sNormalMenuTrueValue)===true){
				$sStyle.=self::getColor($arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['color']);// 样式，颜色
				$sStyle.=self::getStyle($arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['style']);
				unset($arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['color']);
				$arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['style']=trim($sStyle);
				if($arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']==1){// target
					$arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']='target="_blank"';
				}
				else{
					$arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']='';
				}
				$arrSaveMenus[$sNormalMenuTrueValue]=$arrCustomNormalMenuOptions[$sNormalMenuTrueValue];
			}
			elseif(self::isPageMenu($sNormalMenuTrueValue)===true){
				$oPage=$arrPages[$sNormalMenuTrueValue];
				$arrPageMenuOptions=array();
				$arrPageMenuOptions['title']=$oPage->blog_title;
				$arrPageMenuOptions['description']=$oPage->blog_title;
				$arrPageMenuOptions['link']=self::getPageUrl($oPage);
				$arrPageMenuOptions['style']='';
				$arrPageMenuOptions['target']=$oPage->blog_isblank==1?'target="_blank"':'';
				$arrPageMenuOptions['system']=1;
				$arrSaveMenus[$sNormalMenuTrueValue]=$arrPageMenuOptions;
			}
			else{
				$arrNormalMenuOptions[$sNormalMenuTrueValue]['link']=G::U('index://'.$sNormalMenuTrueValue.'/index');
				$sStyle.=self::getColor($arrNormalMenuOptions[$sNormalMenuTrueValue]['color']);// 样式，颜色
				$sStyle.=self::getStyle($arrNormalMenuOptions[$sNormalMenuTrueValue]['style']);
				unset($arrNormalMenuOptions[$sNormalMenuTrueValue]['color']);
				$arrNormalMenuOptions[$sNormalMenuTrueValue]['style']=trim($sStyle);
				if(isset($arrCustomNormalMenuOptions[$sNormalMenuTrueValue])&& $arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']==1){// target
					$arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']='target="_blank"';
				}
				else{
					$arrCustomNormalMenuOptions[$sNormalMenuTrueValue]['target']='';
					$arrSaveMenus[$sNormalMenuTrueValue]=$arrNormalMenuOptions[$sNormalMenuTrueValue];
				}
			}
		}
		$arrPluginDatas=Model::C('plugin_menu','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));// plugin menu
		if($arrPluginDatas===false){
			Cache_Extend::get_cachedata_plugin();
			$arrPluginDatas=Model::C('plugin_menu','',array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}
		foreach($arrPluginDatas as $sPluginKey=>$arrPluginData){
			foreach($arrPluginData as $sPluginKey2=>$arrPluginDataChild){
				$arrPluginMenuOptions=array();
				$arrPluginMenuOptions['title']=$arrPluginDataChild['menuname'];
				$arrPluginMenuOptions['description']=$arrPluginDataChild['description']?$arrPluginDataChild['description']:$arrPluginDataChild['menuname'];
				$arrPluginMenuOptions['link']=$arrPluginDataChild['url']?$arrPluginDataChild['url']:G::U('plugin/index?id='.$sPluginKey.'&module='.$arrPluginDataChild['name']);
				$arrPluginMenuOptions['style']='';
				$arrPluginMenuOptions['target']='';
				$arrPluginMenuOptions['system']=1;
				$arrSaveMenus['plugin_'.$sPluginKey.'_'.$arrPluginDataChild['name']]=$arrPluginMenuOptions;
			}
		}
		Model::C('menu',$arrSaveMenus,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		return $arrSaveMenus;
	}

	static public function getPageUrl($arrBlog){
		if(!empty($arrBlog['blog_gotourl'])){
			return $arrBlog['blog_gotourl'];
		}
		if(empty($arrBlog['blog_urlname'])){
			return G::U('index://page/'.$arrBlog['blog_id']);
		}
		else{
			return __ROOT__."/index.php/page/".$arrBlog['blog_urlname'].$GLOBALS['_commonConfig_']["HTML_FILE_SUFFIX"];
		}
	}

	static protected function isCustomMenu($sMenuName){
		return strpos($sMenuName,'custom_menu_')===0 ? true: false;
	}

	static protected function isPageMenu($sMenuName){
		return strpos($sMenuName,'system_page_')===0 ? true: false;
	}

	static protected function getColor($nStyle){
		switch($nStyle){
			case 1:
				return 'color:red;';
				break;
			case 2:
				return 'color:orange;';
				break;
			case 3:
				return 'color:yellow;';
				break;
			case 4:
				return 'color:green;';
				break;
			case 5:
				return 'color:cyan;';
				break;
			case 6:
				return 'color:blue;';
				break;
			case 7:
				return 'color:purple;';
				break;
			case 8:
				return 'color:gray;';
				break;
			default:
				return '';
				break;
		}
	}

	static protected function getStyle($arrStyle){
		$sBackStyle='';
		if(!empty($arrStyle[1])){
			$sBackStyle.="text-decoration: underline;";
		}
		if(!empty($arrStyle[2])){
			$sBackStyle.="font-style: italic;";
		}
		if(!empty($arrStyle[3])){
			$sBackStyle.="font-weight: bold;";
		}
		return $sBackStyle;
	}

	public static function global_emot($sApp='admin'){
		$arrEmotData=EmotModel::F()->asArray()->order('empt_compositor ASC,emot_id ASC')->all()->query();
		if($arrEmotData===false) return false;
		$arrSaveData=array();
		foreach($arrEmotData as $nKey=>$arrValue){
			$arrSaveData[$arrValue['emot_name']]['name']=$arrValue['emot_name'];
			$arrSaveData[$arrValue['emot_name']]['image']=$arrValue['emot_image'];
			$arrSaveData[$arrValue['emot_name']]['thumb']=$arrValue['emot_thumb'];
		}
		Model::C('global_emot',$arrSaveData,array('cache_path'=>DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Data/DbCache'));
	}

	public function meta_cache($arrData=array()){
		$oDb=Db::RUN();
		$arrOption=array(
			'cache_path'=>DYHB_PATH.'/../'.$arrData[1].'/App/~Runtime/Data/DbMeta',
		);
		$sBackend='FileCache';
		$arrMeta=$oDb->getConnect()->metaColumns($arrData[0]);
		$arrMeta=$oDbTableColumnInfo->makeSql();
		$arrFields=array();
		foreach($arrMeta as $field){
			$arrFields[$field['name']]=true;
		}
		$arrDataMetas=array($arrMeta,$arrFields);
		Dyhb::writeCache($arrData[0],$arrDataMetas,$arrOption,$sBackend);
	}

	public static function front_loginlog($nDisplayNum=6){
		$arrLoginlogData=LoginlogModel::F()->setColumns('user_id,loginlog_user')->distinct(true)->asArray()->all()->order('`loginlog_id` DESC')->limit(0,$nDisplayNum)->query();
		if(E::oneImensionArray($arrLoginlogData) && !empty($arrLoginlogData)){
			$arrLoginlogData=array($arrLoginlogData);
		}
		Model::C('loginlog',$arrLoginlogData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_badword(){
		$arrBadwordData=BadwordModel::F()->asArray()->order('badword_id ASC')->all()->query();
		if($arrBadwordData===false) return false;
		$arrSaveData=array();
		foreach($arrBadwordData as $nKey=>$arrValue){
			$arrSaveData[$arrValue['badword_id']]['regex']=$arrValue['badword_findpattern'];
			$arrSaveData[$arrValue['badword_id']]['value']=$arrValue['badword_replacement'];
		}
		Model::C('badword',$arrSaveData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_yearhotpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrYearhotpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[2],$arrSqlTime[3]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_viewnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_yearhotpost_display_num')):$arrData['display_num'])->query();
		if(E::oneImensionArray($arrYearhotpostData) && !empty($arrYearhotpostData)){
			$arrYearhotpostData=array($arrYearhotpostData);
		}
		Model::C('widget_yearhotpost',$arrYearhotpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_yearcommentpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrYearcommentpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[2],$arrSqlTime[3]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_commentnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_yearcommentp_display_num')) :$arrData['display_num'])->query();
		if(E::oneImensionArray($arrYearcommentpostData) && !empty($arrYearcommentpostData)){
			$arrYearcommentpostData=array($arrYearcommentpostData);
		}
		Model::C('widget_yearcommentpost',$arrYearcommentpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_uploadcategory(){
		$arrUploadcategoryData=UploadcategoryModel::F()->asArray()->all()->order('`uploadcategory_compositor` ASC')->query();
		if(E::oneImensionArray($arrUploadcategoryData) && !empty($arrUploadcategoryData)){
			$arrUploadcategoryData=array($arrUploadcategoryData);
		}
		Model::C('widget_uploadcategory',$arrUploadcategoryData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_theme(){
		$arrThemeData=E::listDir(DYHB_PATH.'/../blog/Theme');
		Model::C('widget_theme',$arrThemeData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_taotao($arrData=array()){
		$arrTaotaoData=TaotaoModel::F()->asArray()->all()->order('`create_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_taotao_display_num')):$arrData['display_num'])->query();
		if(E::oneImensionArray($arrTaotaoData) && !empty($arrTaotaoData)){
			$arrTaotaoData=array($arrTaotaoData);
		}
		Model::C('widget_taotao',$arrTaotaoData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_static(){
		$arrStaticData['blog']=BlogModel::F()->all()->getCounts();// 日志数量
		$arrStaticData['comment']=CommentModel::F()->all()->getCounts();// 评论留言
		$arrStaticData['tag']=TagModel::F()->all()->getCounts();// 标签
		$arrStaticData['trackback']=TrackbackModel::F()->all()->getCounts();// 引用
		$arrStaticData['upload']=UploadModel::F()->all()->getCounts();// 附件
		$arrStaticData['user']=UserModel::F()->all()->getCounts();// 注册用户
		$arrStaticData['today_visited_num']=Global_Extend::getOption('today_visited_num');// 今日访问量
		$arrStaticData['all_visited_num']=Global_Extend::getOption('all_visited_num');// 全部访问量
		Model::C('widget_static',$arrStaticData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_recentpost($arrData=array()){
		$arrRecentpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_recentpost_display_num')):$arrData['display_num'])->query();
		if(E::oneImensionArray($arrRecentpostData) && !empty($arrRecentpostData)){
			$arrRecentpostData=array($arrRecentpostData);
		}
		Model::C('widget_recentpost',$arrRecentpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_randpost($arrData=array()){
		$oBlogModel=new BlogModel();
		$sSql="SELECT r1.blog_id,r1.blog_dateline,r1.blog_title,r1.blog_urlname
FROM ".$oBlogModel->getTablePrefix()."blog AS r1 JOIN
(SELECT ROUND(RAND() *
(SELECT MAX(blog_id)
FROM ".$oBlogModel->getTablePrefix()."blog)) AS blog_id)
AS r2
WHERE r1.blog_id >=r2.blog_id AND
r1.blog_isshow=1 AND r1.blog_ispage=0
ORDER BY r1.blog_id ASC
LIMIT ".(!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_randpost_display_num')) :$arrData['display_num']).";";
			$arrRandpostData=$oBlogModel->getDb()->getAllRows($sSql);
			if($arrRandpostData===false){
				G::E($oBlogModel->getDb()->getErrorMessage());
			}
			Model::C('widget_randpost',$arrRandpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_pagepost($arrData=array()){
		$arrPagepostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>1))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_viewnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_pagepost_display_num')) :$arrData['display_num'])->query();
		if(E::oneImensionArray($arrPagepostData) && !empty($arrPagepostData)){
			$arrPagepostData=array($arrPagepostData);
		}
		Model::C('widget_pagepost',$arrPagepostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_monthhotpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrMonthhotpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[0],$arrSqlTime[1]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_viewnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_monthhotpost_display_num')):$arrData['display_num']	)->query();
		if(E::oneImensionArray($arrMonthhotpostData) && !empty($arrMonthhotpostData)){
			$arrMonthhotpostData=array($arrMonthhotpostData);
		}
		Model::C('widget_monthhotpost',$arrMonthhotpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_monthcommentpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrMonthcommentpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[0],$arrSqlTime[1]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_commentnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_monthcommentp_display_num')):$arrData['title_cutnum'])->query();
		if(E::oneImensionArray($arrMonthcommentpostData) && !empty($arrMonthcommentpostData)){
			$arrMonthcommentpostData=array($arrMonthcommentpostData);
		}
		Model::C('widget_monthcommentpost',$arrMonthcommentpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_link($arrData=array()){
		$arrLinkData=LinkModel::F()->where(array('link_isdisplay'=>1))->asArray()->all()->order('`link_compositor` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_link_display_num')) :$arrData['display_num'])->query();
		if(E::oneImensionArray($arrLinkData) && !empty($arrLinkData)){
			$arrLinkData=array($arrLinkData);
		}
		Model::C('widget_link',$arrLinkData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_lang(){
		$arrLangData=E::listDir(DYHB_PATH.'/../blog/App/Lang');
		Model::C('widget_lang',$arrLangData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_hottag($arrData=array()){
		$arrHottagData=TagModel::F()->asArray()->all()->order('`tag_usenum` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_hottag_display_num')) :$arrData['display_num'])->query();
		if(E::oneImensionArray($arrHottagData) && !empty($arrHottagData)){
			$arrHottagData=array($arrHottagData);
		}
		if(!isset($arrData['tag_color'])){
			$arrColor=@E::mbUnserialize(stripslashes(Global_Extend::getOption('widget_hottag_color')));
		}
		$nI=0;
		$nJ=0;
		$nTagNum=!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_hottag_display_num')) :$arrData['display_num'];// 标签总数
		$nMaxUseNum=0;// 标签最多的日志数
		$nMinUseNum=0;// 标签最少的日志数
		foreach($arrHottagData as $arrHottag){
			if($arrHottag['tag_usenum'] > $nI){
				$nMaxUseNum=$arrHottag['tag_usenum'];
				$nI=$arrHottag['tag_usenum'];
			}
			if($arrHottag['tag_usenum'] < $nJ){
				$nMinUseNum=$arrHottag['tag_usenum'];
			}
			$nJ=$arrHottag['tag_usenum'];
		}
		$nSpread=($nTagNum>12?12:$nTagNum);//这里可以换
		$nRank=$nMaxUseNum-$nMinUseNum;
		$nRank=($nRank==0?1:$nRank);
		$nRank=$nSpread/$nRank;
		foreach($arrHottagData as $nHotTagKey=>$arrHottag){
			$arrHottagData[$nHotTagKey]['color']=$arrColor[rand(0,count($arrColor))%count($arrColor)];
			if(empty($arrHottagData[$nHotTagKey]['color'])){
				$arrHottagData[$nHotTagKey]['color']='#000000';
			}
			$arrHottagData[$nHotTagKey]['fontsize']=10 + round(($arrHottag['tag_usenum'] - $nMinUseNum) * $nRank);// maxfont:22pt,minfont:10pt
		}
		Model::C('widget_hottag',$arrHottagData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_hotpost($arrData=array()){
		$arrHotpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_viewnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_hotpost_display_num')) :$arrData['display_num']	)->query();
		if(E::oneImensionArray($arrHotpostData) && !empty($arrHotpostData)){
			$arrHotpostData=array($arrHotpostData);
		}
		Model::C('widget_hotpost',$arrHotpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_guestbook($arrData=array()){
		$arrGuestbookData=CommentModel::F()->where(array('comment_isshow'=>1,'comment_relationtype'=>''))->asArray()->all()->order('`comment_id` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_guestbook_display_num')):$arrData['display_num'])->query();
		if(E::oneImensionArray($arrGuestbookData) && !empty($arrGuestbookData)){
			$arrGuestbookData=array($arrGuestbookData);
		}
		Model::C('widget_guestbook',$arrGuestbookData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_dayhotpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrDayhotpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[4],$arrSqlTime[5]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_viewnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_dayhotpost_display_num')) :$arrData['display_num'])->query();
		if(E::oneImensionArray($arrDayhotpostData) && !empty($arrDayhotpostData)){
			$arrDayhotpostData=array($arrDayhotpostData);
		}
		Model::C('widget_dayhotpost',$arrDayhotpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_daycommentpost($arrData=array()){
		$arrSqlTime=Date::getTheFirstOfYearOrMonth(date('Ymd',CURRENT_TIMESTAMP));
		$arrDaycommentpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0,'blog_dateline'=>array('between',array($arrSqlTime[4],$arrSqlTime[5]))))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_commentnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_daycomment_display_num')):$arrData['display_num']	)->query();
		if(E::oneImensionArray($arrDaycommentpostData) && !empty($arrDaycommentpostData)){
			$arrDaycommentpostData=array($arrDaycommentpostData);
		}
		Model::C('widget_daycommentpost',$arrDaycommentpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_comment($arrData=array()){
		$arrCommentData=CommentModel::F()->where(array('comment_isshow'=>1))->asArray()->all()->order('`comment_id` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_comment_display_num')):$arrData['display_num'])->query();
		if(E::oneImensionArray($arrCommentData) && !empty($arrCommentData)){
			$arrCommentData=array($arrCommentData);
		}
		Model::C('widget_comment',$arrCommentData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_commentpost($arrData=array()){
		$arrCommentpostData=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0))->asArray()->setColumns('blog_id,blog_title,blog_dateline,blog_urlname')->all()->order('`blog_commentnum` DESC,`blog_istop` DESC,`blog_dateline` DESC')->limit(0,!isset($arrData['display_num'])?intval(Global_Extend::getOption('widget_commentpost_display_num')) :$arrData['display_num']	)->query();
		if(E::oneImensionArray($arrCommentpostData) && !empty($arrCommentpostData)){
			$arrCommentpostData=array($arrCommentpostData);
		}
		Model::C('widget_commentpost',$arrCommentpostData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_category(){
		$arrCategoryData=CategoryModel::F()->asArray()->all()->order('`category_compositor` ASC')->query();
		if(E::oneImensionArray($arrCategoryData) && !empty($arrCategoryData)){
			$arrCategoryData=array($arrCategoryData);
		}
		Model::C('widget_category',$arrCategoryData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_calendar(){
		$arrBlogLists=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0))->setColumns('blog_dateline')->all()->query();
		$arrBlogData=array();
		foreach($arrBlogLists as $oBlogList){
			$arrBlogData[]=date('Ymd',$oBlogList->blog_dateline);
		}
		Model::C('widget_calendar',$arrBlogData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_archive(){
		$arrArchiveDatas=BlogModel::F()->where(array('blog_isshow'=>1,'blog_ispage'=>0))->setColumns('blog_dateline')->all()->order('`blog_dateline` DESC')->query();
		$arrArchiveData=array();
		foreach($arrArchiveDatas as $oValue){
			$arrArchiveData[]=date('Ym',$oValue->blog_dateline);
		}
		$arrArchiveData=array_count_values($arrArchiveData);
		Model::C('widget_archive',$arrArchiveData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

	public static function front_widget_recentimage(){
		$arrRecentimageData=UploadModel::F()->where(array('upload_extension'=>array(array('eq','jpg'),array('eq','jpeg'),array('eq','png'),array('eq','gif'),array('eq','bmp'),'or')))->asArray()->all()->order('`create_dateline` DESC')->limit(0,Global_Extend::getOption('widget_recentimage_display_num') ?Global_Extend::getOption('widget_recentimage_display_num') :5	)->query();
		if(E::oneImensionArray($arrRecentimageData) && !empty($arrRecentimageData)){
			$arrRecentimageData=array($arrRecentimageData);
		}
		Model::C('widget_recentimage',$arrRecentimageData,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
	}

}