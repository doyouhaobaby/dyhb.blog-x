<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   HTML视图管理类($) */

!defined('DYHB_PATH')&& exit;

class ViewHtml extends View{

	static private $_oShareGlobalTemplate;
	private $_sTemplateFile;
	private $_nRuntime;
	private $_arrTrace;

	public function __construct($oPar,$sTemplate=null,$oTemplate=null){
		if($oTemplate){
			$this->setTemplate($oTemplate);
		}else{
			$this->setTemplate(self::createShareTemplate());
		}
		$this->_sTemplateFile=$sTemplate;
		parent::__construct($oPar);
	}

	static public function createShareTemplate(){
		if(!self::$_oShareGlobalTemplate){
			self::$_oShareGlobalTemplate=new TemplateHtml();
		}
		return self::$_oShareGlobalTemplate;
	}

	public function display($sTemplateFile='',$sCharset='',$sContentType='text/html'){
		header("Content-Type:".$sContentType."; charset=".$sCharset);
		header("Cache-control: private");//支持页面回跳
		header("X-Powered-By:DoYouHaoBaby".DYHB_VERSION);

		if(empty($sTemplateFile)){
			$sTemplateFile=&$this->_sTemplateFile;
		}
		$this->_nRuntime=E::getMicrotime();// 记录模板开始运行时间
		$TemplateFile=$sTemplateFile;
		if(!is_file($sTemplateFile)){
			$TemplateFile=$this->parseTemplateFile($sTemplateFile);
		}
		$oTemplate=$this->getTemplate();
		$oController=$this->getPar();// 设置模版变量
		$oTemplate->setVar('TheView',$this);
		$oTemplate->setVar('TheController',$oController);
		$sContent=$this->templateContentReplace($oTemplate->display($TemplateFile,$sCharset,$sContentType,false));
		if($GLOBALS['_commonConfig_']['HTML_CACHE_ON']) {Html::W($sContent);}// 如果开启静态化，则写入数据
		if($GLOBALS['_commonConfig_']['SHOW_RUN_TIME']){$sContent.=$this->templateRuntime();}
		if($GLOBALS['_commonConfig_']['SHOW_PAGE_TRACE']){$sContent.=$this->templateTrace();}
		echo $sContent;
		unset($sContent);
	}

	public function templateRuntime(){
		$sContent='<div id="dyhb_run_time" class="dyhb_run_time" style="display:none;">';
		$nEndTime=microtime(TRUE);
		$nTotalRuntime=number_format(($nEndTime-$GLOBALS['_beginTime_']),3);
		$sContent.="Processed in ".$nTotalRuntime." second(s)";
		if($GLOBALS['_commonConfig_']['SHOW_DETAIL_TIME']){
			$sContent.="(Template:".$this->getMicrotime()." s)";
		}
		if($GLOBALS['_commonConfig_']['SHOW_DB_TIMES']){
			$oDb=Db::RUN();
			$sContent.=" | ".$oDb->getConnect()->Q()." queries";
		}
		if($GLOBALS['_commonConfig_']['SHOW_GZIP_STATUS']){
			if($GLOBALS['_commonConfig_']['START_GZIP']){
				$sGzipString='disabled';
			}
			else{
				$sGzipString='disabled';
			}
			$sContent.=" | Gzip {$sGzipString}";
		}
		if(MEMORY_LIMIT_ON && $GLOBALS['_commonConfig_']['SHOW_USE_MEM']){
			$nStartMem=array_sum(explode(' ',$GLOBALS['_startUseMems_']));
			$nEndMem=array_sum(explode(' ',memory_get_usage()));
			$sContent.=' | UseMem:'. E::changeFileSize($nEndMem-$nStartMem);
		}
		$sContent.="</div>";
		return $sContent;
	}

	public function trace($Title,$Value=''){
		if(is_array($Title)){
			$this->_arrTrace=array_merge($this->_arrTrace,$Title);
		}else{
			$this->_arrTrace[$Title]=$Value;
		}
	}

	public function templateTrace(){
		$sTraceFile=APP_PATH.'/App/Config/Trace.php';
		$arrTrace=is_file($sTraceFile)?include $sTraceFile:array();
		$oTemplate=$this->getTemplate();
		A::INSTANCE($oTemplate,'TemplateHtml');
		$sCompiledFile=$oTemplate->returnCompiledPath();
		$this->trace(G::L('当前页面','dyhb'),$_SERVER['REQUEST_URI']);// 系统默认显示信息
		$this->trace(G::L('模板缓存','dyhb'),$sCompiledFile);
		$this->trace(G::L('请求方法','dyhb'),$_SERVER['REQUEST_METHOD']);
		$this->trace(G::L('通信协议','dyhb'),$_SERVER['SERVER_PROTOCOL']);
		$this->trace(G::L('请求时间','dyhb'),date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
		$this->trace(G::L('用户代理','dyhb'),$_SERVER['HTTP_USER_AGENT']);
		$this->trace(G::L('会话ID','dyhb'),session_id());
		$arrLog=Log::$_arrLog;
		$this->trace(G::L('日志记录','dyhb'),count($arrLog)?G::L('%d条日志','dyhb',null,count($arrLog)).'<br/>'.implode('<br/>',$arrLog):G::L('无日志记录','dyhb'));
		$arrFiles= get_included_files();
		$this->trace(G::L('加载文件','dyhb'),count($arrFiles).str_replace("\n",'<br/>',substr(substr(print_r($arrFiles,true),7),0,-2)));
		$arrTrace=array_merge($arrTrace,$this->_arrTrace);
		ob_start();
		include DYHB_PATH.'/Resource/Template/PageTrace.template.php';
		$sContent=ob_get_contents();
		ob_end_clean();
		return $sContent;
	}

	public function getMicrotime(){
		return round(E::getMicrotime()-$this->_nRuntime,5);
	}

	public function assign($Name, $Value=null){
		$oTemplate=$this->getTemplate();
		return $oTemplate->setVar($Name,$Value);
	}

	public function get($Name){
		return $this->getVar($Name);
	}

	public function getVar($Name){
		$oTemplate=$this->getTemplate();
		return $oTemplate->getVar($Name);
	}

	protected function templateContentReplace($sContent){
		$arrReplace= array(// 系统默认的特殊变量替换
			'__ENTER__'=>__ENTER__,// 项目入口文件
			'__LIBCOM__'=>__LIBCOM__,//框架内部资源目录
			'__APPPUB__'=>__APPPUB__,//项目入口公用目录
			'__TMPLPUB__'=>__TMPLPUB__,//项目模板公用目录
			'../PUBLIC__'=>__TMPLPUB__,// 项目公共目录
			'__PUBLIC__'=>__PUBLIC__,// 站点公共目录
			'__TMPL__'=>__TMPL__,// 项目模板目录
			'__ROOT__'=>__ROOT__,// 当前网站地址
			'__APP__'=>__APP__,// 当前项目地址
			'__ACTION__'=>__ACTION__,// 当前操作地址
			'__SELF__'=>__SELF__,// 当前页面地址
			'__URL__'=>__URL__,// 当前URL地址
			'__TMPL_FILE_NAME__'=>__TMPL_FILE_NAME__,// 当前模板文件地址
			'__MODULE_NAME__'=>MODULE_NAME,// 模块或叫做控制器
			'__ACTION_NAME__'=>ACTION_NAME,// 操作或叫做方法
			'__TEMPLATE_NAME__'=>TEMPLATE_NAME,// 当前主题名字
			'__LANG_NAME__'=>LANG_NAME,  // 当前语言名字
			'__THEME__'=>__THEME__,// 模板目录
			'__FRAMEWORK__' =>__FRAMEWORK__,// 框架内部入口
		);
		$sContent=str_replace(array_keys($arrReplace),array_values($arrReplace),$sContent);
		return $sContent;
	}

}
