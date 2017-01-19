<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	主题管理控制器($)*/

!defined('DYHB_PATH') && exit;

class ThemeController extends InitController{

	public $_sCurrentTheme='';

	public function _index_get_sceen_options_value(){
		return "<input type='text' class='field' name='configs[admintemplateeverynum]' id='admintemplateeverynum' maxlength='3' value='".$this->_arrOptions['admintemplateeverynum']."' /> <label for='admineverynum'>".G::L("后台每页主题显示数量")."</label>
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function _admin_get_display_sceen_options(){
		return true;
	}

	public function _admin_get_sceen_options_value(){
		return $this->_index_get_sceen_options_value();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以看见各个主题的缩略图，主题列表支持分页。','theme').'</p>'.
				'<p>'.G::L('点击启用，可以启用你的主题。','theme').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _admin_get_admin_help_description(){
		return $this->_index_get_admin_help_description();
	}

	public function index(){
		$this->_sCurrentTheme=$this->_arrOptions['front_theme_name'];
		$this->showThemes('blog/Theme');
		$this->assign('bIsAdmin',false);
		$this->assign('sApp','blog');
		$this->display();
	}

	public function admin(){
		$this->_sCurrentTheme=$this->_arrOptions['admin_theme_name'];
		$this->showThemes(G::tidyPath(APP_PATH.'/Theme'));
		$this->assign('bIsAdmin',true);
		$this->assign('sApp','admin');
		$this->display('theme+index');
	}

	protected function showThemes($sThemePath){
		$arrThemes=$this->getThemes($sThemePath);
		$bCurrentThemeIn=true;
		if(!in_array($sThemePath.'/'.$this->_sCurrentTheme,$arrThemes)){
			$arrThemes[]=$sThemePath.'/'.$this->_sCurrentTheme;
			$bCurrentThemeIn=false;
		}
		$oThemeModel=new ThemeModel();
		$oThemeModel->getThemes($arrThemes);
		$arrOkThemes=$oThemeModel->_arrOkThemes;
		$arrBrokenThemes=$oThemeModel->_arrBrokenThemes;
		$this->assign('arrCurrentTheme',$arrOkThemes[$this->_sCurrentTheme]);
		if($bCurrentThemeIn===false){
			unset($arrOkThemes[$this->_sCurrentTheme]);
		}
		$this->assign('arrOkThemes',$arrOkThemes);
		$this->assign('arrBrokenThemes',$arrBrokenThemes);
		$this->assign('nOkThemeNums',count($arrOkThemes));
		$this->assign('nBrokenThemeNums',count($arrBrokenThemes));
		$this->assign('nOkThemeRowNums',ceil(count($arrOkThemes)/3));
	}

	protected function getThemes($sDir){
		$nEverynum=$this->_arrOptions['admintemplateeverynum'];
		$oPage=IoPage::RUN($sDir,$nEverynum,G::getGpc('page'));
		$this->assign('sPageNavbar',$oPage->P());
		$this->assign('nPage',G::getGpc('page'));
		return $oPage->getCurrentData();
	}

	public function change(){
		$sType=G::getGpc('type','G');
		$sName=G::getGpc('name','G');
			if($sType=='admin'){
				$sOptionName='admin_theme_name';
			}
			else{
				$sOptionName='front_theme_name';
			}
		$oOptionModel=OptionModel::F('option_name=?',$sOptionName)->getOne();
		$oOptionModel->option_value=$sName;
		$oOptionModel->save(0,'update');
		if($oOptionModel->isError()){
			$this->E($oOptionModel->getErrorMessage());
		}
		if($sType=='admin'){
			G::cookie('admin_template',$sName);
		}
		else{
			G::cookie('blog_template',$sName);
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		$this->S(G::L("启用主题%s成功！",'app',null,$sName));
	}

	public function delete(){
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		$sName=G::getGpc('name','G');
		if(empty($sName)){
			$this->E(G::L('模板主题不能为空'));
		}
		if(strtolower($sName)=='default'||strtolower($sName)=='board'||strtolower($sName)=='cms'){// 防止windows平台
			$this->E(G::L('默认主题不能被删除'));
		}
		$sAppPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sName);
		if(!is_dir($sAppPath)){
			$this->E(G::L('待删除主题不存在'));
		}
		if(@rmdir($sAppPath)){
			$this->E(G::L('删除主题%s失败','app',null,$sAppPath));
		}
		else{
			Cache_Extend::front_widget_theme();
			$this->S(G::L('删除主题%s成功','app',null,$sAppPath));
		}
	}

	public function _edit_get_display_sceen_options(){
		return true;
	}

	public function _edit_get_sceen_options_value(){
		return "<input type='text' class='field' name='configs[admin_theme_file_every]' id='admin_theme_file_every' maxlength='3' value='".$this->_arrOptions['admin_theme_file_every']."' /> <label for='admin_theme_file_every'>".G::L("后台每页主题文件数量")."</label>
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function _edit_get_admin_help_description(){
		return '<p>'.G::L('这里是当前主题的文件列表，点击名字可以进行编辑。','theme').'</p>'.
				'<p>'.G::L('主题文件支持分页，点击上边的“设置”可以更改每页分页数量。','theme').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function edit(){
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		if($this->_arrOptions['allowed_default_edit']==0){
		if(strtolower($sTheme)=='default'||strtolower($sTheme)=='board'||strtolower($sTheme)=='cms')
			$this->E(G::L('默认主题不能够被编辑'));
		}
		$nEverynum=$this->_arrOptions['admin_theme_file_every'];
		$sDir=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme);
		$oPage=IoPage::RUN($sDir,$nEverynum,G::getGpc('page'),array('type'=>2,'recursion'=>true,'allowedtype'=>array('css','js','html','php'),'disallowedfilename'=>array('Indexs.inc')));
		$this->assign('sPageNavbar',$oPage->P());
		$arrCurrentDatas=$oPage->getCurrentData();
		$arrSaveData=array();
		$nI=0;
		foreach($arrCurrentDatas as $sCurrentData){
			$arrSaveData[$nI]['extension']=$sExtension= $this->get_ext_name($sCurrentData,2);
			$arrSaveData[$nI]['filename']=$sCurrentData;
			$nI++;
		}
		$this->assign('sTheme',$sTheme);
		$this->assign('sApp',$sApp);
		$this->assign('arrCurrentData',$arrSaveData);
		$this->display();
	}

	public function _cache_get_admin_help_description(){
		return '<p>'.G::L('这里是当前主题的缓存文件列表，点击删除可以删除。','theme').'</p>'.
				'<p>'.G::L('如果，你在线修改了模板文件，如果这个又设置模板永不过期，那么这个时候你可以在这里删除模板缓存。','theme').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function cache(){
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		if(is_dir(DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Cache/'.ucfirst($sTheme))){
			$nEverynum=$this->_arrOptions['admin_theme_file_every'];
			$sDir=DYHB_PATH.'/../'.$sApp.'/App/~Runtime/Cache/'.ucfirst($sTheme);
			$oPage=IoPage::RUN($sDir,$nEverynum,G::getGpc('page'),array('type'=>2,'recursion'=>true,'fullfilepath'=>true));
			$this->assign('sPageNavbar',$oPage->P());
			$arrCurrentDatas=$oPage->getCurrentData();
		}
		else{
			$arrCurrentDatas=array();
			$this->assign('sPageNavbar','');
		}
		$this->assign('arrCurrentData',$arrCurrentDatas);
		$this->display();
	}

	public function delete_cache_file(){
		$sPath=G::getGpc('path','G');
		if(file_exists($sPath)){
			if(!unlink($sPath))
				$this->E(G::L('删除模板缓存文件%s失败','app',null,$sPath));
			else{
				$this->S(G::L('删除模板缓存文件%s成功','app',null,$sPath));
			}
		}
		else{
			$this->E(G::L('模板缓存文件%s不存在','app',null,$sPath));
		}
	}

	public function _modify_get_admin_help_description(){
		return '<p>'.G::L('在文本框中修改模板文件后，点击确定即可保存文件','theme').'</p>'.
			'<p>'.G::L('模板文件内容不能为空，否则可能无法保存数据','theme').'</p>'.
			'<p><strong>'.G::L('更多信息：').'</strong></p>'.
			'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function modify(){
		$sName=G::getGpc('name','G');
		$sExtension=G::getGpc('extension','G');
		$sApp=G::getGpc('app','G');
		if(!in_array($sExtension,array('php','js','css','html'))){
			$sExtension='php';
		}
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		if(empty($sApp)){
			$sApp='blog';
		}
		if(strpos($sName,'.lang.php')){
			$this->E(G::L('语言包文件不能被编辑'));
		}
		if($sExtension=='css'){
			$sPath='Public/Css/';
		}
		elseif($sExtension=='js'){
			$sPath='Public/Js/';
		}
		elseif($sExtension=='php'){
			$sPath='Public/Php/';
		}
		else{
			$sPath='';
		}
		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'. $sPath.$sName;
		if(file_exists($sPath)){
			$bWriteAble=false;
			if(is_writeable($sPath)){
				$bWriteAble=true;
			}
			$hFp=@fopen($sPath,'r');
			$sContents=@fread($hFp,filesize($sPath));
			@fclose($hFp);
			$sContents=htmlspecialchars($sContents);
			$this->assign('sContents',$sContents);
			$this->assign('sExtension',$sExtension);
			$this->assign('sName',$sName);
			$this->assign('sTheme',$sTheme);
			$this->assign('sApp',$sApp);
			$this->display();
		}
		else{
			$this->E(G::L('模板文件%s不存在','app',null,$sPath));
		}
	}

	public function get_ext_name($sFileName,$nCase=0){
		if(!preg_match('/\./',$sFileName)){
			return '';
		}
		$arr=explode('.',$sFileName);
		$sExtName=end($arr);
		if($nCase==1){
			return strtoupper($sExtName);
		}
		elseif($nCase==2){
			return strtolower($sExtName);
		}
		else{
			return $sExtName ;
		}
	}

	public function modify_yes(){
		$sName=G::getGpc('name','P');
		$sExtension=G::getGpc('extension','P');
		if(!in_array($sExtension,array('php','js','css','html'))){
			$sExtension='php';
		}
		$sTheme=G::getGpc('theme','P');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','P');
		if(empty($sApp)){
			$sApp='blog';
		}
		if(strpos($sName,'.lang.php')){
			$this->E(G::L('语言包文件不能被编辑'));
		}
		if($sExtension=='css'){
			$sPath='Public/Css/';
		}
		elseif($sExtension=='js/'){
			$sPath='Public/Js/';
		}
		elseif($sExtension=='php'){
			$sPath='Public/Php/';
		}
		else{
			$sPath='';
		}
		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'. $sPath.$sName;
		if(file_exists($sPath)){
			$sContent=G::stripslashes(trim(G::getGpc('filecontent','P')));
			$hFp=@fopen($sPath,'wb');
			@fwrite($hFp,$sContent);
			@fclose($hFp);
			Cache_Extend::front_css();
			$this->assign('__JumpUrl__',G::U("theme/modify?name={$sName}&extension={$sExtension}&theme={$sTheme}&app={$sApp}"));
			$this->S(G::L('模板修改成功'));
		}
		else{
			$this->E(G::L('模板文件%s不存在','app',null,$sPath));
		}
	}

	public function delete_file(){
		$sName=G::getGpc('name','G');
		$sExtension=G::getGpc('extension','G');
		if(!in_array($sExtension,array('php','js','css','html'))){
			$sExtension='php';
		}
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		if(strpos($sName,'.lang.php')){
			$this->E(G::L('语言包文件不能被删除'));
		}
		if($sExtension=='css'){
			$sPath='Public/Css/';
		}
		elseif($sExtension=='js'){
			$sPath='Public/Js/';
		}
		elseif($sExtension=='php'){
			$sPath='Public/Php/';
		}
		else{
			$sPath='';
		}
		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'.$sPath.$sName;
		if(file_exists($sPath)){
			if(!unlink($sPath))
				$this->E(G::L('删除模板文件%s失败','app',null,$sPath));
			else{
				@unlink(DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'.$sPath.'Index.inc');
				$this->S(G::L('删除模板文件%s成功','app',null,$sPath));
			}
		}
		else{
			$this->E(G::L('模板文件%s不存在','app',null,$sPath));
		}
	}

	public function _add_file_get_admin_help_description(){
		return '<p>'.G::L('在文本框中修改模板文件后，点击确定即可保存文件','theme').'</p>'.
			'<p>'.G::L('模板文件内容不能为空，否则可能无法保存数据','theme').'</p>'.
			'<p>'.G::L('模板文件名字必须带扩展名，而且模板文件只能是css,js,html,php格式','theme').'</p>'.
			'<p><strong>'.G::L('更多信息：').'</strong></p>'.
			'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function add_file(){
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		$this->assign('sTheme',$sTheme);
		$this->assign('sApp',$sApp);
		$this->display();
	}

	public function add_file_yes(){
		$sTheme=G::getGpc('theme','P');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','P');
		if(empty($sApp)){
			$sApp='blog';
		}
		$sName=G::getGpc('name','P');
		$sContent=G::stripslashes(trim(G::getGpc('filecontent','P')));
		if(empty($sContent)){
			$this->E(G::L('模板文件内容不能为空'));
		}
		if(empty($sName)){
			$this->E(G::L('模板文件名字不能为空'));
		}
		$sExtension=$this->get_ext_name($sName,2);
		if(!in_array($sExtension,array('php','js','css','html'))){
			$this->E(G::L('模板文件后缀只能是css,js,php,html'));
		}

		if($sExtension=='css'){
			$sPath='Public/Css/';
		}elseif($sExtension=='js'){
			$sPath='Public/Js/';
		}elseif($sExtension=='php'){
			$sPath='Public/Php/';
		}else{
			$sPath='';
		}

		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'.$sPath.$sName;
		if(file_exists($sPath)){
			$this->E(G::L('模板文件%s已经存在','app',null,$sPath));
		}else{
			if(!is_dir(dirname($sPath)) && !G::makeDir(dirname($sPath))){
				if(!file_put_contents($sPath,$sContent)){
					$this->E(G::L('数据写入模板文件%s失败，请确认模板文件的权限0777','app',null,$sPath));
				}else{
					@unlink(DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/'.$sPath.'Index.inc');
					$this->assign('__JumpUrl__',G::U('theme/modify?name='.$sName.'&extension='.$sExtension.'&theme='.$sTheme));
					$this->S(G::L('创建模板文件%s成功','app',null,$sPath));
				}
			}else{
				$this->E(G::L('创建模板文件%s失败','app',null,$sPath));
			}
		}
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('新建模板的过程是完整复制default模板到新的模板名字的目录.然后再用编辑模板功能编辑','theme').'</p>'.
				'<p>'.G::L('熟悉HTML和CSS的朋友完全可以通过模板编辑功能在后台不用任何工具做出一套全新的模板,从而免除登陆FTP的步骤','theme').'</p>'.
				'<p>'.G::L('但前提是templates目录具备可写权限','theme').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function add(){
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$this->assign('sTheme',$sTheme);
		$this->assign('sApp',$sApp);
		$this->display();
	}

	public function add_yes(){
		$sApp=G::getGpc('app','P');
		if(empty($sApp)){
			$sApp='blog';
		}
		$sTheme=G::getGpc('theme','P');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sName=G::getGpc('name','P');
		if(empty($sName)){
			$this->E(G::L('创建的模板名字不能为空'));
		}
		Check::RUN();
		if(!Check::C($sName,'number_underline_english')){
			$this->E(G::L('模板名字只能是英文、数字和下划线组成'));
		}
		$sThemePath =DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sName);
		$sDefaultPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme);
		if(is_dir($sThemePath)){
			$this->E(G::L('模板主题%s已经存在','app',null,$sThemePath));
		}
		if(!is_dir($sDefaultPath)){
			$this->E(G::L('模板默认主题%s不存在','app',null,$sThemePath));
		}
		// 开始拷贝数据
		if($this->copyDir($sDefaultPath,$sThemePath)){
			Cache_Extend::front_widget_theme();
			Cache_Extend::front_css();
			$this->assign('__JumpUrl__',G::U('theme/edit?app='.$sApp.'&theme='.$sName));
			$this->S(G::L('模板主题%s创建成功','app',null,$sThemePath));
		}
		else{
			$this->S(G::L('模板主题%s创建失败','app',null,$sThemePath));
		}
	}

	public function copyDir($sSourcePath,$sTargetPath,$arrFilter=array('.','..','.svn','.cvs','_svn')){
		if(!is_dir($sSourcePath)){
			return false;
		}
		if(file_exists($sTargetPath)){
			return false;
		}
		mkdir($sTargetPath,0777,true);// 创建目标目录
		$hFiles=opendir($sSourcePath);
		while(($sFileName=readdir($hFiles))!==false){
			if(!in_array($sFileName,$arrFilter)){// 过滤
				$sIOPath=$sSourcePath.'/'.$sFileName;
				$sNewPath=$sTargetPath.'/'.$sFileName;
				if(is_file($sIOPath)){// 拷贝文件
					if(!copy($sIOPath,$sNewPath)){
						return false;
					}
				}
				else if(is_dir($sIOPath)){// 递归拷贝子目录
					if(!$this->copyDir($sIOPath,$sNewPath)){
						return false;
					}
				}
			}
		}
		return true;
	}

	public function diy(){
		$sTheme=G::getGpc('theme','G');
		if(empty($sTheme)){
			$sTheme='default';
		}
		if($this->_arrOptions['allowed_default_edit']==0){
			if(strtolower($sTheme)=='default'||strtolower($sTheme)=='board'||strtolower($sTheme)=='cms'){
				$this->E(G::L('默认模板不能够被DIY'));
			}
		}
		$sApp=G::getGpc('app','G');
		if(empty($sApp)){
			$sApp='blog';
		}
		if(strtolower($sApp)=='admin'){
			$this->E(G::L('后台主题不能够DIY'));
		}
		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/dyhb-x-blog-style-'.strtolower($sTheme).'.xml';
		if(file_exists($sPath)){
			$bWriteAble=false;
			if(is_writeable($sPath)){
				$bWriteAble=true;
			}
			$hFp=@fopen($sPath,'r');
			$sContents=@fread($hFp,filesize($sPath));
			@fclose($hFp);
			$arrData=Xml::xmlUnserialize(trim($sContents));
			$arrData=$arrData['root']['data'];
		}
		else{
			$arrData=array();
		}
		$nAdv=G::getGpc('adv','G');
		$arrThemeDir=E::listDir(DYHB_PATH.'/../'.$sApp.'/Theme');
		$this->assign('arrThemeDir',$arrThemeDir);
		$this->assign('arrThemeData',G::stripslashes($arrData));
		$this->assign('sApp',$sApp);
		$this->assign('sTheme',$sTheme);
		$this->assign('bAdv',($nAdv==1?true:false));
		if($nAdv==1){
			$this->display('theme+diy_adv');
		}
		else{
			$this->display();
		}
	}

	public function diy_yes(){
		$sTheme=G::getGpc('theme','P');
		if(empty($sTheme)){
			$sTheme='default';
		}
		$sApp=G::getGpc('app','P');
		if(empty($sApp)){
			$sApp='blog';
		}
		$sPath=DYHB_PATH.'/../'.$sApp.'/Theme/'.ucfirst($sTheme).'/dyhb-x-blog-style-'.strtolower($sTheme).'.xml';
		$arrData['title']='DYHB.BLOG X Style';
		$arrData['version']='1.0';
		$arrData['time']='2011-10-02 02:55';
		$arrData['from']='DYHB.BLOG X(http://www.doyouhaobaby.net)';
		$arrData['copyright']=G::L('点牛科技(成都)');
		$arrStyleVarBgImg=G::getGpc('stylevarbgimg','P');// 提取背景
		$arrStyleVarBgExtra=G::getGpc('stylevarbgextra','P');
		$arrStyleVar=G::getGpc('stylevar','P');
		$arrMenuBgColor=array(
			'color'=>$arrStyleVar['menu_bg_color'],
			'img'=>$arrStyleVarBgImg['menu_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['menu_bg_color_extra'],
		);

		$arrHeaderBgColor=array(
			'color'=>$arrStyleVar['header_bg_color'],
			'img'=>$arrStyleVarBgImg['header_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['header_bg_color_extra'],
		);

		$arrSideBgColor=array(
			'color'=>$arrStyleVar['side_bg_color'],
			'img'=>$arrStyleVarBgImg['side_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['side_bg_color_extra'],
		);

		$arrBgColor=array(
			'color'=>$arrStyleVar['bg_color'],
			'img'=>$arrStyleVarBgImg['bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['bg_color_extra'],
		);

		$arrDropMenuBgColor=array(
			'color'=>$arrStyleVar['drop_menu_bg_color'],
			'img'=>$arrStyleVarBgImg['drop_menu_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['drop_menu_bg_color_extra'],
		);

		$arrFooterBgColor=array(
			'color'=>$arrStyleVar['footer_bg_color'],
			'img'=>$arrStyleVarBgImg['footer_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['footer_bg_color_extra'],
		);

		$arrFloatBgColor=array(
			'color'=>$arrStyleVar['float_bg_color'],
			'img'=>$arrStyleVarBgImg['float_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['float_bg_color_extra'],
		);

		$arrFloatMaskBgColor=array(
			'color'=>$arrStyleVar['float_mask_bg_color'],
			'img'=>$arrStyleVarBgImg['float_mask_bg_color_img'],
			'extra'=>$arrStyleVarBgExtra['float_mask_bg_color_extra'],
		);
		unset($arrStyleVarBgExtra,$arrStyleVarBgExtra,$arrStyleVar['menu_bg_color'],$arrStyleVar['header_bg_color'],$arrStyleVar['side_bg_color'],$arrStyleVar['bg_color'],$arrStyleVar['drop_menu_bg_color'],$arrStyleVar['footer_bg_color'],$arrStyleVar['float_bg_color'],$arrStyleVar['float_mask_bg_color']);

		$arrStyleVar['menu_bg_color']=$arrMenuBgColor;
		$arrStyleVar['header_bg_color']=$arrHeaderBgColor;
		$arrStyleVar['side_bg_color']=$arrSideBgColor;
		$arrStyleVar['bg_color']=$arrBgColor;
		$arrStyleVar['drop_menu_bg_color']=$arrDropMenuBgColor;
		$arrStyleVar['footer_bg_color']=$arrFooterBgColor;
		$arrStyleVar['float_bg_color']=$arrFloatBgColor;
		$arrStyleVar['float_mask_bg_color']=$arrFloatMaskBgColor;
		$arrData['data']=array(
			'name'=>ucfirst($sTheme),
			'template_nikename'=>G::getGpc('namenew','P'),
			'template_id'=>G::getGpc('template_id','P'),
			'doyouhaobaby_template_base'=>G::getGpc('templateidnew','P'),
			'directory'=>'Theme/'.ucfirst($sTheme),
			'copyright'=>'',
			'data'=>$arrStyleVar,
			'version'=>'1.0',
		);

		if(!file_put_contents($sPath,Xml::xmlSerialize(G::stripslashes($arrData),true)))
			$this->E(G::L('写入主题风格配置文件%s失败','app',null,$sPath));
		else{
			Cache_Extend::front_css();
			$this->S(G::L('写入主题风格配置文件%s成功','app',null,$sPath));
		}
	}

	public function update_css(){
		Cache_Extend::front_css();
		$this->S(G::L('更新Css 缓存成功哦'));
	}

}
