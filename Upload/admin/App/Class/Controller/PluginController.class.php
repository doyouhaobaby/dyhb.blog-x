<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	插件列表控制器($)*/

!defined('DYHB_PATH') && exit;

class PluginController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('插件列表显示目前系统中所安装的插件信息。','plugin').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _make_get_admin_help_description(){
		return '<p>'.G::L('本功能仅供插件开发者使用。','plugin').'</p>'.
				'<p>'.G::L('插件开发人员在使用本功能前请务必仔细阅读《<a href=\'http://doyouhaobaby.net/index.php/dyhbblog-x-2-plugin-making/index.html\'>DoYouHaoBaby插件制作</a>》中的内容。','plugin').'</p>'.
				'<p>'.G::L('警告: 不正确的插件设计或安装可能危及到整个站点的正常使用。','plugin').'</p>'.
				'<p>'.G::L('如果把导出的插件文件放置在 Public/Plugin/插件目录/ 目录下，则可以通过插件管理直接安装插件。','plugin').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_make_get_admin_help_description();
	}

	public function make(){
		$this->display();
	}

	public function aInsert($nId=null){
		Cache_Extend::global_plugin();
		Cache_Extend::global_plugin('blog');
		Cache_Extend::global_plugin_hooks();
		Cache_Extend::global_plugin_hooks('blog');
	}

	public function update_plugin_cache(){
		Cache_Extend::global_plugin();
		Cache_Extend::global_plugin('blog');
		Cache_Extend::global_plugin_hooks();
		Cache_Extend::global_plugin_hooks('blog');
		$this->S(G::L('更新插件缓存成功了！'));
	}

	public function AInsertObject_($oPluginModel){
		$oPluginModel->plugin_dir=$oPluginModel->getPlugindir();
	}

	public function AUpdateObject_($oPluginModel){
		$oPluginModel->plugin_dir=$oPluginModel->getPlugindir();
	}

	public function AEditObject_($oPluginModel){
		$arrModules=@unserialize($oPluginModel['plugin_module']);
		$this->assign('arrModules',$arrModules);
	}

	public function update_module(){
		$nModuleId=intval(G::getGpc('id','P'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$arrModules=@unserialize($oPluginModel['plugin_module']);
			$arrNewModules=array();
			if(is_array($arrModules)){
				foreach($arrModules as $nModuleId=>$sModule){
					if(isset($_POST['deleteOrNot']) && in_array((string)$nModuleId,$_POST['delete'])){
						continue;
					}
					$arrNewModules[]=array(
						'name'=>$_POST['name'][$nModuleId],
						'type'=>$_POST['type'][$nModuleId],
						'menuname'=>$_POST['menuname'][$nModuleId],
						'url'=>$_POST['url'][$nModuleId],
						'displayorder'=>intval($_POST['displayorder'][$nModuleId]),
						'description'=>$_POST['description'][$nModuleId]
					);
				}
			}
			if($_POST['newname']){
				$arrNewModules[]=array(
					'name'=>$_POST['newname'],
					'type'=>$_POST['newtype'],
					'menuname'=>$_POST['newmenuname'],
					'url'=>$_POST['newurl'],
					'displayorder'=>intval($_POST['newdisplayorder']),
					'description'=>$_POST['newdescription']);
			}
			usort($arrNewModules,array($this,'module_cmp'));
			$oPluginModel->plugin_module=serialize($arrNewModules);
			$oPluginModel->save(0,'update');
			if($oPluginModel->isError()){
				$this->E($oPluginModel->getErrorMessage());
			}
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$this->S(G::L('设置插件的模块成功！'));
		}
	}

	public function add_var(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$this->assign('nPluginId',$nModuleId);
			$this->assign('oPluginValue',$oPluginModel);
			$this->display();
		}
	}

	public function var_insert(){
		$nModuleId=intval(G::getGpc('id','P'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginvarModel=new PluginvarModel();
			$oPluginvarModel->plugin_id=$nModuleId;
			$oPluginvarModel->save(0);
			if($oPluginvarModel->isError()){
				$this->E($oPluginvarModel->getErrorMessage());
			}
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$this->assign('__JumpUrl__',G::U('plugin/edit?id='.$nModuleId).'#tab_var');
			$this->S(G::L('设置插件的变量成功！'));
		}
	}

	public function edit_var(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginvarModel=PluginvarModel::F('plugin_id=? AND pluginvar_id=?',$nModuleId,intval(G::getGpc('var_id','G')))->getOne();
			if(empty($oPluginvarModel['pluginvar_id'])){
				$this->E(G::L('你指定待编辑的变量不存在！'));
			}else{
				$this->assign('oPluginValue',$oPluginModel);
				$this->assign('oValue',$oPluginvarModel);
				$this->assign('nPluginId',$nModuleId);
				$this->display('plugin+add_var');
			}
		}
	}

	public function update_var(){
		$nModuleId=intval(G::getGpc('id','P'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginvarModel=PluginvarModel::F('plugin_id=? AND pluginvar_id=?',$nModuleId,intval(G::getGpc('var_id','P')))->getOne();
			if(empty($oPluginvarModel['pluginvar_id'])){
				$this->E(G::L('你指定待编辑的变量不存在！'));
			}else{
				$oPluginvarModel->plugin_id=intval($nModuleId);
				$oPluginvarModel->save(0,'update');
				if($oPluginvarModel->isError()){
					$this->E($oPluginvarModel->getErrorMessage());
				}
				Cache_Extend::global_plugin();
				Cache_Extend::global_plugin('blog');
				Cache_Extend::global_plugin_hooks();
				Cache_Extend::global_plugin_hooks('blog');
				$this->S(G::L('变量更新成功了！'));
			}
		}
	}

	public function delete_var(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginvarModel=PluginvarModel::F('plugin_id=? AND pluginvar_id=?',$nModuleId,intval(G::getGpc('var_id','G')))->getOne();
			if(empty($oPluginvarModel['pluginvar_id'])){
				$this->E(G::L('你指定待编辑的变量不存在！'));
			}else{
				$oPluginvarModel->destroy();
				Cache_Extend::global_plugin();
				Cache_Extend::global_plugin('blog');
				Cache_Extend::global_plugin_hooks();
				Cache_Extend::global_plugin_hooks('blog');
				$this->S(G::L('删除变量成功了！'));
			}
		}
	}

	public function enable(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginModel->plugin_active=1;
			$oPluginModel->save(0,'update');
			if($oPluginModel->isError()){
				$this->E($oPluginModel->getErrorMessage());
			}
			Cache_Extend::get_cachedata_plugin();
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$this->S(G::L('启用插件成功了！'));
		}
	}

	public function disable(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$oPluginModel->plugin_active=0;
			$oPluginModel->save(0,'update');
			if($oPluginModel->isError()){
				$this->E($oPluginModel->getErrorMessage());
			}
			Cache_Extend::get_cachedata_plugin();
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$this->S(G::L('关闭插件成功了！'));
		}
	}

	public function delete_plugin(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$oPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->getOne();
			if(empty($oPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			$sDir=$oPluginModel['plugin_dir'];
			PluginModel::M()->deleteWhere(array('plugin_id'=>$nModuleId));
			PluginvarModel::M()->deleteWhere(array('plugin_id'=>$nModuleId));
			if($sDir){
				$sFile=DYHB_PATH.'/../Public/Plugin/'.$sDir.'/dyhb-x-blog-plugin-'.$sDir.'.xml';
				if(file_exists($sFile)){
					$sImportTxt=@implode('',file($sFile));
					$arrPluginData=Xml::xmlUnserialize(trim($sImportTxt));
					$arrPluginData=$arrPluginData['root'];
					if(!empty($arrPluginData['uninstallfile']) && preg_match('/^[\w\.]+$/',$arrPluginData['uninstallfile'])){
						$this->U(G::U('plugin/import_uninstall?dir='.$sDir));
					}
				}
			}
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			Model::C('plugin_menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('卸载插件成功了！'));
		}
	}

	public function install(){
		$arrPlugins=PluginModel::F()->order('plugin_active DESC,plugin_id DESC')->getAll();
		$arrInstalledDir=array();
		foreach($arrPlugins as $arrPlugin){
			$arrInstalledDir[]=$arrPlugin['plugin_dir'];
		}
		$sPluginDir=DYHB_PATH.'/../Public/Plugin';
		$arrNewPlugins=array();
		if($h1=opendir($sPluginDir)){
			while(FALSE !== ($entry=readdir($h1))){
				if(!in_array($entry,array('.', '..')) && is_dir($sPluginDir.'/'.$entry) && !in_array($entry,$arrInstalledDir)) {
					$sEntryDir=$sPluginDir.'/'.$entry;
					$nFileTime=filemtime($sEntryDir);
					if($h2=opendir($sEntryDir)){
						while(FALSE !== ($entry2=readdir($h2))){
							if(preg_match('/^dyhb\-x\-blog\-plugin\-'.$entry.'(\_\w+)?\.xml$/',$entry2)){
								$sEntryTitle=$entry;
								$sEntryVersion=$sEntryCopyright='';
								if(file_exists($sEntryDir.'/dyhb-x-blog-plugin-'.$entry.'.xml')){
									$sImportTxt=@implode('',file($sEntryDir.'/dyhb-x-blog-plugin-'.$entry.'.xml'));
									$arrPluginData=Xml::xmlUnserialize(trim($sImportTxt));
									$arrPluginData=$arrPluginData['root'];
									if(!empty($arrPluginData['plugin']['plugin_name'])){
										$sEntryTitle=htmlspecialchars($arrPluginData['plugin']['plugin_name']);
										$sEntryVersion=htmlspecialchars($arrPluginData['plugin']['plugin_version']);
										$sEntryCopyright=htmlspecialchars($arrPluginData['plugin']['plugin_copyright']);
									}
								}
								$sFile=$sEntryDir.'/'.$entry2;
								$arrNewPlugins[]=array('title'=>$sEntryTitle,'version'=>$sEntryVersion,'copyright'=>$sEntryCopyright,'dir'=>$entry,'filetime'=>$nFileTime);
							}
						}
					}
				}
			}
		}
		$this->assign('arrNewPlugins',$arrNewPlugins);
		$this->display();
	}

	public function import_a_plugin(){
		$sDir=G::getGpc('dir','G');
		if(file_exists(DYHB_PATH.'/../Public/Plugin/'.$sDir.'/dyhb-x-blog-plugin-'.$sDir.'.xml')){
			$sImportTxt=@implode('',file(DYHB_PATH.'/../Public/Plugin/'.$sDir.'/dyhb-x-blog-plugin-'.$sDir.'.xml'));
			$arrPluginData=Xml::xmlUnserialize(trim($sImportTxt));
			$arrPluginData=$arrPluginData['root'];
			if(!$this->is_plugin_key($arrPluginData['plugin']['plugin_identifier'])) {
				$this->E(G::L('插件唯一标识符存在非法字符'));
			}
			if(is_array($arrPluginData['var'])) {
				foreach($arrPluginData['var'] as $arrConfig) {
					if(!$this->is_plugin_key($arrConfig['pluginvar_variable'])) {
						$this->E(G::L('导入的插件变量名字存在非法字符'));
					}
				}
			}
			$oTryPlugin=PluginModel::F('plugin_identifier=?',$arrPluginData['plugin']['plugin_identifier'])->getOne();
			if(!empty($oTryPlugin['plugin_id'])) {
				$this->E(G::L('导入的插件%s已经存在了','app',null,$oTryPlugin['plugin_name']));
			}
			$this->plugin_install($arrPluginData);
			if(!empty($sDir) && !empty($arrPluginData['installfile']) && preg_match('/^[\w\.]+$/',$arrPluginData['installfile'])){
				$this->U(G::U('plugin/import_install&dir='.$sDir));
			}
			$this->S(G::L('插件%s安装成功了','app',null,$sDir));
		}else{
			$this->E(G::L('你准备安装的插件不存在！'));
		}
	}

	public function import_install(){
		$this->import_install_or_uninstall('install');
	}

	public function import_uninstall(){
		$this->import_install_or_uninstall('uninstall');
	}

	public function import_install_or_uninstall($sOperation){
		$bFinish=FALSE;
		$sDir=G::getGpc('dir','G');
		if(file_exists(DYHB_PATH.'/../Public/Plugin/'.$sDir.'/dyhb-x-blog-plugin-'.$sDir.'.xml')){
			$sImportTxt=@implode('',file(DYHB_PATH.'/../Public/Plugin/'.$sDir.'/dyhb-x-blog-plugin-'.$sDir.'.xml'));
			$arrPluginData=Xml::xmlUnserialize(trim($sImportTxt));
			$arrPluginData=$arrPluginData['root'];
			if($sOperation=='install'){
				$sFilename=$arrPluginData['installfile'];
			}elseif($sOperation=='uninstall'){
				$sFilename=$arrPluginData['uninstallfile'];
			}
			if(!empty($sFilename) && preg_match('/^[\w\.]+$/',$sFilename)){
				$sFilename=DYHB_PATH.'/../Public/Plugin/'.$sDir.'/'.$sFilename;
				if(file_exists($sFilename)){
					@include_once $sFilename;
				}else{
					$bFinish=TRUE;
				}
			}else{
				$bFinish=TRUE;
			}
			if($bFinish){
				Cache_Extend::global_plugin_hooks('admin');
				Cache_Extend::global_plugin_hooks('blog');
				Cache_Extend::global_plugin_hooks();
				Cache_Extend::global_plugin_hooks('blog');
				Model::C('plugin_menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
				Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
				if($sOperation=='install'){
					$this->assign('__JumpUrl__',G::U('plugin/index'));
					$this->S(G::L('安装插件成功了！'));
				}
				if($sOperation=='uninstall'){
					$this->assign('__JumpUrl__',G::U('plugin/index'));
					$this->S(G::L('卸载插件成功了！'));
				}
			}
		}else{
			$this->E(G::L('你准备安装的插件不存在！'));
		}
	}

	public function export(){
		$nModuleId=intval(G::getGpc('id','G'));
		if(empty($nModuleId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$arrPluginModel=PluginModel::F('plugin_id=?',$nModuleId)->asArray()->getOne();
			if(empty($arrPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			unset($arrPluginModel['plugin_id']);
			$arrPluginData=array();
			$arrPluginData['plugin']=$arrPluginModel;
			$arrPluginData['version']='1.0';
			$arrVars=PluginvarModel::F('plugin_id=?',$nModuleId)->order('`pluginvar_displayorder` DESC')->asArray()->getAll();
			foreach($arrVars as $nKey=>$arrVar){
				unset($arrVar['pluginvar_id'],$arrVar['plugin_id']);
				$arrPluginData['var']['var'.$nKey]=$arrVar;
			}
			$sPluginDir=DYHB_PATH.'/../Public/Plugin/'.$arrPluginData['plugin']['plugin_dir'].'/';
			if(file_exists($sPluginDir.'/install.php')) {
				$arrPluginData['installfile']='install.php';
			}
			if(file_exists($sPluginDir.'/uninstall.php')) {
				$arrPluginData['uninstallfile']='uninstall.php';
			}
			$sName='dyhb-x-blog-plugin-'.$arrPluginData['plugin']['plugin_identifier'].'.xml';
			$sXmlData=Xml::xmlSerialize(G::stripslashes($arrPluginData),true);
			header('Content-type: text/xml');
			header('Content-Disposition: attachment; filename="'.$sName);
			echo $sXmlData;
			exit();
		}
	}

	public function config(){
		$nId=intval(G::getGpc('id','G'));
		$nPluginId=0;
		$arrSubMenuitem=array();
		if(empty($nId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$arrPluginModel=PluginModel::F('plugin_id=?',$nId)->asArray()->getOne();
			if(empty($arrPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			if(!$arrPluginModel['plugin_active']){
				$this->E(G::L('你指定待设置的插件尚未启用！'));
			}
			$nPluginId=$arrPluginModel['plugin_id'];
			$arrPluginModel['plugin_module']=@unserialize($arrPluginModel['plugin_module']);
			$arrPluginVars=array();
			$arrVars=PluginvarModel::F('plugin_id=?',$nPluginId)->order('pluginvar_displayorder DESC')->getAll();
			foreach($arrVars as $arrVar){
				$arrPluginVars[$arrVar['pluginvar_variable']]=$arrVar;
			}
			if($arrPluginVars){
				$arrSubMenuitem[]=array(G::L('设置'),G::U('plugin/config?id='.$nPluginId),!G::getGpc('pmod','G'));
			}
			if(is_array($arrPluginModel['plugin_module'])){
				foreach($arrPluginModel['plugin_module'] as $arrModule){
					if($arrModule['type']=='admin_plugin_menu'){
						if(!$arrPluginVars && empty($_GET['pmod'])){
							$_GET['pmod']=$arrModule['name'];
						}
						$arrSubMenuitem[]=array($arrModule['menuname'],G::U("plugin/config?id={$nPluginId}&identifier={$arrPluginModel['plugin_identifier']}&pmod={$arrModule['name']}"),G::getGpc('pmod','G')==$arrModule['name']);
					}
				}
			}
			if(empty($_GET['pmod'])){
				if($arrPluginVars){
					$arrExtra=array();
					foreach($arrPluginVars as &$arrVar){
						$arrVar['pluginvar_variable']='varsnew['.$arrVar['pluginvar_variable'].']';
						if($arrVar['pluginvar_type']=='number'){
							$arrVar['pluginvar_type']='text';
						}elseif($arrVar['pluginvar_type']=='select'){
							$arrVar['pluginvar_type']="<select name=\"{$arrVar['pluginvar_variable']}\">\n";
							foreach(explode("\n",$arrVar['pluginvar_extra']) as $sKey=>$sOption){
								$sOption=trim($sOption);
								if(strpos($sOption,'=')===FALSE){
									$sKey=$sOption;
								}else{
									$arrItem=explode('=',$sOption);
									$sKey=trim($arrItem[0]);
									$sOption=trim($arrItem[1]);
								}
								$arrVar['pluginvar_type'].="<option value=\"".htmlspecialchars($sKey)."\" ".($arrVar['pluginvar_value']==$sKey?'selected':'').">{$sOption}</option>\n";
							}
							$arrVar['pluginvar_type'].="</select>\n";
							$arrVar['pluginvar_variable']=$arrVar['pluginvar_value']='';
						}elseif($arrVar['pluginvar_type']=='selects'){
							$arrVar['pluginvar_value']=@unserialize($arrVar['pluginvar_value']);
							$arrVar['pluginvar_value']=is_array($arrVar['pluginvar_value'])?$arrVar['pluginvar_value']:array($arrVar['pluginvar_value']);
							$arrVar['pluginvar_type']="<select name=\"{$arrVar[pluginvar_variable]}[]\" multiple=\"multiple\" size=\"10\" style=\"height:120px;\">\n";
							foreach(explode("\n", $arrVar['pluginvar_extra']) as $sKey=>$sOption){
								$sOption=trim($sOption);
								if(strpos($sOption,'=')===FALSE){
									$sKey=$sOption;
								}else{
									$arrItem=explode('=',$sOption);
									$sKey=trim($arrItem[0]);
									$sOption=trim($arrItem[1]);
								}
								$arrVar['pluginvar_type'].="<option value=\"".htmlspecialchars($sKey)."\" ".(in_array($sKey,$arrVar['pluginvar_value'])?'selected':'').">{$sOption}</option>\n";
							}
							$arrVar['pluginvar_type'].="</select>\n";
							$arrVar['pluginvar_variable']=$arrVar['pluginvar_value']='';
						}
					}
				}
				$this->assign('arrSubMenuitems',$arrSubMenuitem);
				$this->assign('arrPluginVars',$arrPluginVars);
				$this->assign('arrPluginModel',$arrPluginModel);
				$this->display();
			}else{
				$sModfile='';
				if(is_array($arrPluginModel['plugin_module'])){
					foreach($arrPluginModel['plugin_module'] as $arrModule){
						if($arrModule['type']=='admin_plugin_menu' && $arrModule['name']==G::getGpc('pmod','G')){
							$sModfile=DYHB_PATH.'/../Public/Plugin/'.$arrPluginModel['plugin_dir'].'/'.$arrModule['name'].'.inc.php';
							break;
						}
					}
				}
				if($sModfile){
					$this->assign('arrSubMenuitems',$arrSubMenuitem);
					$this->assign('arrPluginModel',$arrPluginModel);
					if(!@include($sModfile)){
						$this->E(G::L('插件模块文件%s不存在！','app',null,$sModfile));
					}else{
						exit();
					}
				}else{
					$this->E(G::L('插件模块文件错误'));
				}
			}
		}
	}

	public function update_config(){
		$nId=intval(G::getGpc('id','P'));
		$nPluginId=0;
		if(empty($nId)){
			$this->E(G::L('你没有指定待设置的插件！'));
		}else{
			$arrPluginModel=PluginModel::F('plugin_id=?',$nId)->asArray()->getOne();
			if(empty($arrPluginModel['plugin_id'])){
				$this->E(G::L('你指定待设置的插件不存在！'));
			}
			if(!$arrPluginModel['plugin_active']){
				$this->E(G::L('你指定待设置的插件尚未启用！'));
			}
			$nPluginId=$arrPluginModel['plugin_id'];
			$arrPluginVars=array();
			$arrVars=PluginvarModel::F('plugin_id=?',$nPluginId)->order('pluginvar_displayorder DESC')->getAll();
			foreach($arrVars as $arrVar){
				$arrPluginVars[$arrVar['pluginvar_variable']]=$arrVar;
			}
			if(is_array($_POST['varsnew'])){
				foreach($_POST['varsnew'] as $sVariable=>$value){
					if(isset($arrPluginVars[$sVariable])) {
						if($arrPluginVars[$sVariable]['pluginvar_type']=='number'){
							$value=(float)$value;
						}elseif($arrPluginVars[$sVariable]['pluginvar_type']=='selects'){
							$value=addslashes(serialize($value));
						}
						$oVariable=PluginvarModel::F('plugin_id=? AND pluginvar_variable=?',$nPluginId,$sVariable)->getOne();
						$oVariable->pluginvar_value=$value;
						$oVariable->save(0,'update');
						$oVariable=null;
					}
				}
			}
			Cache_Extend::global_plugin();
			Cache_Extend::global_plugin('blog');
			Cache_Extend::global_plugin_hooks();
			Cache_Extend::global_plugin_hooks('blog');
			$this->S(G::L('插件参数设置成功了'));
		}
	}

	public function module_cmp($arrA,$arrB){
		return $arrA['displayorder']>$arrB['displayorder']?1:-1;
	}

	public function is_plugin_key($sKey) {
		return preg_match("/^[a-z]+[a-z0-9_]*$/i",$sKey);
	}

	public function plugin_install($arrPluginData){
		if(!$arrPluginData || !$arrPluginData['plugin']['plugin_identifier']){
			return false;
		}
		$oTryPlugin=PluginModel::F('plugin_identifier=?',$arrPluginData['plugin']['plugin_identifier'])->getOne();
		if(!empty($oTryPlugin['plugin_id'])){
			return false;
		}
		$arrData=array();
		foreach($arrPluginData['plugin'] as $sKey=>$sVal){
			if($sKey=='plugin_active'){
				$sVal=0;
			}
			$arrData[$sKey]=$sVal;
		}
		$oPlugin=new PluginModel($arrData);
		$oPlugin->save(0);
		if($oPlugin->isError()){
			$this->E($oPlugin->getErrorMessage());
		}
		if(is_array($arrPluginData['var'])){
			foreach($arrPluginData['var'] as $arrConfig){
				$arrData=array('plugin_id'=>$oPlugin['plugin_id']);
				foreach($arrConfig as $sKey=>$sVal){
					$arrData[$sKey]=$sVal;
				}
				$oPluginvar=new PluginvarModel($arrData);
				$oPluginvar->save(0);
				if($oPluginvar->isError()){
					$this->E($oPluginvar->getErrorMessage());
				}
			}
		}
		return true;
	}

	public function runQuery($sSql){
		$sTabelPrefix=$GLOBALS['_commonConfig_']['DB_PREFIX'];
		$sDbCharset=$GLOBALS['_commonConfig_']['DB_CHAR'];
		$sSql=str_replace(array(' dyhbblogx_',' `dyhbblogx_',' prefix_',' `prefix_'),array(' {tableprefix}',' `{tableprefix}',' {tableprefix}',' `{tableprefix}'),$sSql);
		$sSql=str_replace("\r","\n",str_replace(array(' {tableprefix}',' `{tableprefix}'),array(' '.$sTabelPrefix,' `'.$sTabelPrefix),$sSql));

		$arrResult=array();
		$nNum=0;
		foreach(explode(";\n",trim($sSql)) as $sQuery){
			$arrQueries=explode("\n",trim($sQuery));
			foreach($arrQueries as $sQuery){
				$arrResult[$nNum].=$sQuery[0]=='#' || $sQuery[0].$sQuery[1]=='--'?'':$sQuery;
			}
			$nNum++;
		}
		unset($sSql);

		$oDb=Db::RUN();
		foreach($arrResult as $sQuery){
			$sQuery=trim($sQuery);
			if($sQuery){
				if(substr($sQuery,0,12)=='CREATE TABLE'){
					$sName=preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1",$sQuery);
					$oDb->query($this->create_table($sQuery,$sDbCharset));
				}else{
					$oDb->query($sQuery);
				}
			}
		}
	}

	public function create_table($sSql,$sDbCharset){
		$sType=strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2",$sSql));
		$sType=in_array($sType,array('MYISAM','HEAP'))?$sType:'MYISAM';
		return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU","\\1",$sSql).(mysql_get_server_info()>'4.1'?" ENGINE={$sType} DEFAULT CHARSET={$sDbCharset}":" TYPE={$sType}");
	}

}
