<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   节点编译器列表 ($)*/

!defined('DYHB_PATH') && exit;

abstract class TemplateHtmlNodeCompilerBase{

	public $_arrCompilers;
	protected $_arrNotNullAttributes=array();
	protected $_arrComparison=array(' nheq '=>' !== ',' heq '=>' === ',' neq '=>' != ',' eq '=>' == ',' egt '=>' >= ',' gt '=>' > ',' elt '=>' <= ',' lt '=>' < ');

	public function __construct(){}

	static public function attributeTextToBool($sTxtValue){
		return !in_array(strtolower($sTxtValue),array('false','0','off','no'));
	}

	public function checkNode(TemplateHtmlNode $oNode){
		$oAttribute=$oNode->getAttribute();
		A::INSTANCE($oAttribute,'TemplateHtmlNodeAttribute');
		foreach($this->_arrNotNullAttributes as $sAttributeName){
			$sAttributeName=strtolower($sAttributeName);
			if($oAttribute->getAttribute($sAttributeName)===null){
				G::E(G::L('Template节点“%s”缺少必须的属性：“%s”','Dyhb',null,$oNode->getNodeName(),$sAttributeName));
			}
		}
		return true;
	}

	public function regCompilers($sTag,$sClass){
		$this->_arrCompilers[$sTag]=$sClass;
	}

	public function parseCondition($sCondition){
		$sCondition=str_ireplace(array_keys($this->_arrComparison),array_values($this->_arrComparison),$sCondition);// 替换掉系统识别比较字符
		$sCondition=preg_replace('/\$(\w+):(\w+)\s/is','$\\1->\\2 ',$sCondition);// 解析: to ->
		switch(strtolower($GLOBALS['_commonConfig_']['TMPL_VAR_IDENTIFY'])){// 解析对
			case 'array':// 识别为数组
				$sCondition=preg_replace('/\$(\w+)\.(\w+)\s/is','$\\1["\\2"] ',$sCondition);
				break;
			case 'obj':// 识别为对象
				$sCondition=preg_replace('/\$(\w+)\.(\w+)\s/is','$\\1->\\2 ',$sCondition);
				break;
			default:// 自动判断数组或对象 只支持二维
				$sCondition=preg_replace('/\$(\w+)\.(\w+)\s/is','(is_array($\\1)?$\\1["\\2"]:$\\1->\\2)',$sCondition);
		}
		return $sCondition;
	}

	public function parseVar($sContent){
		if('d.'==strtolower(substr($sContent ,0,2))){
			return $this->parseD($sContent);// 特殊变量
		}
		elseif(strpos($sContent,'.')){
			$arrVars=explode('.', $sContent);
			switch(strtolower($GLOBALS['_commonConfig_']['TMPL_VAR_IDENTIFY'])){
				case 'array': // 识别为数组
					$sContent='$'.$arrVars[0].'[\''.$arrVars[1].'\']'.($this->arrayHandler($arrVars));
					break;
				case 'obj': // 识别为对象
					$sContent='$'.$arrVars[0].'->'.$arrVars[1].($this->arrayHandler($arrVars,2));
					break;
				default:// 自动判断数组或对象 支持多维
					$sContent='is_array($'.$arrVars[0].')? $'.$arrVars[0].'[\''.$arrVars[1].'\']'.($this->arrayHandler($arrVars)).' : $'.$arrVars[0].'->' .$arrVars[1].($this->arrayHandler($arrVars,2));
					break;
			}
		}
		elseif(strpos($sContent,':')){
			$sContent='$'.str_replace(':','->',$sContent);// 额外的对象方式支持
		}
		elseif(!defined($sContent)){
			$sContent='$'.$sContent;
		}
		return $sContent;
	}

	public function parseD($sVar){
		$arrVars=explode('.',$sVar);// 解析‘.’
		$arrVars[1]=strtoupper(trim($arrVars[1]));
		$nLen=count($arrVars);
		$sAction="\$_@";
		if($nLen >= 3){// cookie,session,get等等
			if(in_array(strtoupper($arrVars[1]), array('COOKIE', 'SESSION', 'GET', 'POST', 'SERVER','ENV','REQUEST'))){// PHP常用系统变量 < 忽略大小写 >
				$sCode=str_replace('@',$arrVars[1],$sAction).$this->arrayHandler($arrVars);// 替换调名称, 并将使用arrayHandler函数获取下标, 支持多维 ，以$demo[][][]方式
			}
			elseif(strtoupper($arrVars[1])=='LANG'){
				$sCode='G::L(\''.strtoupper($arrVars[2]).(isset($arrVars[3])?'.'.$arrVars[3]:'').'\')';
			}
			elseif(strtoupper($arrVars[1])=='CONFIG'){
				$sCode='G::C(\''.strtoupper($arrVars[2]).(isset($arrVars[3])?'.'.$arrVars[3]:'').'\')';
			}
			elseif( strtoupper($arrVars[1])=='CONST'){
				$sCode=strtoupper($arrVars[2]);
			}
			else{
				$sCode='';
			}
		}
		elseif($nLen===2){
			if(strtoupper($arrVars[1])=='NOW' or strtoupper($arrVars[1])=='TIME'){// 时间
				$sCode="date('Y-m-d H:i:s', time())";
			}
			elseif(strtoupper($arrVars[1])=='VERSION' || strtoupper($arrVars[1])=='DYHB_VERSION'){
				$sCode='DYHB_VERSION';
			}
			elseif(strtoupper($arrVars[1])=='LEFTTAG' || strtoupper($arrVars[1])=='LEFT'){
				$sCode='"{"';
			}
			elseif(strtoupper($arrVars[1])=='RIGHTTAG' || strtoupper($arrVars[1])=='RIGHT'){
				$sCode='"}"';
			}
			elseif(strtoupper($arrVars[1])=='TEMPLATE' || strtoupper($arrVars[1])=='BASENAME'){
				$sCode='__TMPL_FILE_NAME__';
			}
			else{
				$sCode=$arrVars[1];
			}
		}
		return $sCode;
	}

	public function parseVarFunction($sName,$arrVar){
		$nLen=count($arrVar);// 对变量使用函数
		$arrNot=explode(',',$GLOBALS['_commonConfig_']['TEMPLATE_NOT_ALLOWS_FUNC']);// 取得模板禁止使用函数列表
		for($nI=0; $nI<$nLen; $nI++){
			if(0===stripos($arrVar[$nI],'default=')){
				$arrArgs=explode('=',$arrVar[$nI],2);
			}
			else{
				$arrArgs=explode('=',$arrVar[$nI]);
			}
			$arrArgs[0]=trim($arrArgs[0]);// 模板函数过滤
			switch(strtolower($arrArgs[0])){
				case 'default':// 特殊模板函数
					$sName='('.$sName.')? ('.$sName.'): '.$arrArgs[1];
					break;
				default:// 通用模板函数
					if(!in_array($arrArgs[0],$arrNot)){
						if(isset($arrArgs[1])){
							if(strstr($arrArgs[1],'**')){
								$arrArgs[1]=str_replace('**',$sName,$arrArgs[1]);
								$sName="$arrArgs[0]($arrArgs[1])";
							}
							else{
								$sName="$arrArgs[0]($sName,$arrArgs[1])";
							}
					}
					elseif(!empty($arrArgs[0])){
						$sName="$arrArgs[0]($sName)";
					}
				}
			}
		}
		return $sName;
	}

	public function arrayHandler(&$arrVars,$nType=1,$nGo=2){
		$nLen=count($arrVars);
		$sParam='';
		if($nType==1){// 类似$hello['test']['test2']
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.="['{$arrVars[$nI]}']";
			}
		}
		elseif($nType=='2'){// 类似$hello->test1->test2
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.="->{$arrVars[$nI]}";
			}
		}
		elseif($nType=='3'){// 类似$hello.test1.test2
			for($nI=$nGo;$nI<$nLen;$nI++){
				$sParam.=".{$arrVars[$nI]}";
			}
		}
		return $sParam;
	}

}

class TemplateHtmlNodeAttributeParser extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oAttribute){
		$sAttributeSource=trim($oAttribute->getCompiled());
		self::escapeCharacter($sAttributeSource);
		// 正则匹配
		$arrRegexp[]="/(([^=\s]+)=)?\*([^\']+)\*/";// xxx=*yyy* 或 *yyy* 格式
		$arrRegexp[]="/(([^=\s]+)=)?\"([^\"]+)\"/";// xxx="yyy" 或 "yyy" 格式
		$arrRegexp[]="/(([^=\s]+)=)?'([^\']+)'/";// xxx='yyy' 或 'yyy' 格式
		$arrRegexp[]="/(([^=\s]+)=)?([^\s]+)/";// xxx=yyy 或 yyy 格式
		$nNameIdx=2;
		$nValueIdx=3;
		$nDefaultIdx=0;
		foreach($arrRegexp as $sRegexp){
			if(preg_match_all($sRegexp,$sAttributeSource,$arrRes)){
				foreach($arrRes[0] as $nIdx=>$sAttribute){
					$sName=$arrRes[$nNameIdx][$nIdx];
					$sValue=&$arrRes[$nValueIdx][$nIdx];
					if(empty($sName)){
						$sName='condition'.$nDefaultIdx++;
					}
					self::escapeCharacter($sValue,false);
					$oAttribute->setAttribute($sName,$sValue);
					$sAttributeSource=str_replace($sAttribute,'',$sAttributeSource);
				}
			}
		}
	}

	static public function escapeCharacter(&$sTxt,$bEsc=true){
		if($bEsc){// 转义
			$sTxt=str_replace('\\\\','\\',$sTxt);// 转义 \
			$sTxt=str_replace("\\'",'~~{#!`!#}~~',$sTxt);// 转义 '
			$sTxt=str_replace('\\"','~~{#!``!#}~~',$sTxt);// 转义 "
			$sTxt=str_replace('\\$','~~{#!S!#}~~',$sTxt);// 转义 $
			$sTxt=str_replace('\\.','~~{#!dot!#}~~',$sTxt);// 转义 .
		}
		else{// 还原
			$sTxt=str_replace('.','->',$sTxt);// 成员访问符 '->' 符号与  节点的边界符号('<>')冲突，以 . 替代 属性中 出现的 ->
			$sTxt=str_replace('[greater]','>',$sTxt);
			$sTxt=str_replace('[less]','<',$sTxt);
			$sTxt=str_replace("~~{#!`!#}~~","'",$sTxt);// 还原 '
			$sTxt=str_replace('~~{#!``!#}~~','"',$sTxt);// 还原 "
			$sTxt=str_replace('~~{#!S!#}~~','$',$sTxt);// 还原 $
			$sTxt=str_replace('~~{#!dot!#}~~','.',$sTxt);// 还原 .
		}
		return $sTxt;
	}

	static public function regToCompiler(){}

}

class TemplateHtmlNodeCompiler_tpl extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){}

	public function compile(TemplateObj $oObj){}

	static public function regToCompiler(){
		TemplateHtmlNodeParser::regCompilers('tpl',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_assign extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 各项属性
		$sValue=$oAttribute->getAttribute('value');
		if($sValue===null){
			$sValue='';
		}
		$sName=$this->parseVar($sName);
		if('$'==substr($sValue,0,1)){
			$sValue=$this->parseVar(substr($sValue,1));
		}
		else{
			$sValue='\''.$sValue.'\'';
		}
		$sCompiled='<?php '.$sName.'='.$sValue.'; ?>';// 编译
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('assign',__CLASS__);
		$oParent->regCompilers('assign',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_assign::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_compare extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$sType=$oAttribute->getAttribute('type');
		if($sType===NULL){// 缺省值
			$sType='eq';
		}
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,$sType);// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	public function compare($sContent,$sName,$sValue,$sType='eq'){
		$sType=$this->parseCondition(' '.$sType.' ');// 类型过滤处理
		$sName=str_replace('->','.',$sName);
		$arrVar=explode('|',$sName);
		$sName=array_shift($arrVar);
		$sName=$this->parseVar($sName);
		if(count($arrVar)>0){// 识别为函数
			$sName=$this->parseVarFunction($sName,$arrVar);
		}
		if('$'==substr($sValue,0,1)){
			$sValue=$this->parseVar(substr($sValue,1));
		}
		else{
			$sValue='"'.$sValue.'"';
		}
		$sParseStr='<?php if(('.$sName.')'.$sType.' '.$sValue.'): ?>'.$sContent.'<?php endif; ?>';
		return $sParseStr;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('compare',__CLASS__);
		$oParent->regCompilers('compare',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_compare::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_else extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return in_array(strtolower($sNodeName),array('else','Template:else'));
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oObj->setCompiled("<?php else: ?>");
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('else',__CLASS__);
		$oParent->regCompilers('else',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_else::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_elseif extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag( $sNodeName){
		return in_array(strtolower($sNodeName),array('elseif','Template:elseif'));
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sCondition=$oAttribute->getAttribute('condition');// 循环条件
		$sCondition=str_replace('->','.',$sCondition);
		$sCondition=$this->parseCondition($sCondition);
		$sCondition=str_replace(':','->',$sCondition);
		$sCondition=str_replace('+','::',$sCondition);
		$oObj->setCompiled("<?php elseif({$sCondition}): ?>");
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('elseif',__CLASS__);
		$oParent->regCompilers('elseif',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_elseif::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_eq extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'eq');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('eq',__CLASS__);
		$oParent->regCompilers('eq',__CLASS__);
		TemplateHtmlNodeParser::regCompilers('equal',__CLASS__);
		$oParent->regCompilers('equal',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_eq::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_foreach extends TemplateHtmlNodeCompilerBase{

	static private $_nForeachId=0;

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='for';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFor=$oAttribute->getAttribute('for');// 各项属性
		$sKey=$oAttribute->getAttribute('key');
		$sValue=$oAttribute->getAttribute('value');
		$sIndex=$oAttribute->getAttribute('index');
		if($sKey===null){
			$sKey='key';
		}
		if($sValue===null){
			$sValue='value';
		}
		if($sIndex===null){
			$sIndex='i';
		}
		$sFor=str_replace('->','.',$sFor);
		$sFor=$this->parseCondition($sFor);
		$sKey=str_replace('->','.',$sKey);
		$sKey=$this->parseCondition($sKey);
		$sValue=str_replace('->','.',$sValue);
		$sValue=$this->parseCondition($sValue);
		$sIndex=str_replace('->','.',$sIndex);
		$sIndex=$this->parseCondition($sIndex);
		$sFor=trim($sFor);
		$sKey=trim($sKey);
		$sValue=trim($sValue);
		$sIndex=trim($sIndex);
		$oBody=$oObj->getBody();// 循环体
		$sBody=$oBody->getCompiled();
		// 编译
		$sCompiled="<?php \${$sIndex}=1;?>
<?php if(is_array(\${$sFor})): foreach(\${$sFor} as \${$sKey}=>\${$sValue}): ?>
{$sBody}
<?php \${$sIndex}++;?>
<?php endforeach;endif; ?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('foreach',__CLASS__);
		$oParent->regCompilers('foreach',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_foreach::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_if extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='condition';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sCondition=$oAttribute->getAttribute('condition');// 条件 表达式
		$sCondition=str_replace('->','.',$sCondition);
		$sCondition=$this->parseCondition($sCondition);
		$sCondition=str_replace(':','->',$sCondition);
		$sCondition=str_replace('+','::',$sCondition);
		$oBody=$oObj->getBody();// 条件 体
		$sBody=$oBody->getCompiled();
		$sCompiled="<?php if({$sCondition}):?>{$sBody}<?php endif; ?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('if',__CLASS__);
		$oParent->regCompilers('if',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_if::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_import extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='file';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFile=$oAttribute->getAttribute('file');// 获取属性
		$sFile=str_replace('->','.',$sFile);// 还原属性值
		$sValue=$oAttribute->getAttribute('value');
		$sType=$oAttribute->getAttribute('type');
		$sTitle=$oAttribute->getAttribute('title');
		$sType=$sType!==null?strtolower($sType): $sType;
		$sBasepath=$oAttribute->getAttribute('basepath');
		if($sBasepath!==null){
			$sBasepath=str_replace('->','.',$sBasepath);// 还原属性值
		}
		if($sTitle!==null){
			$sTitle=str_replace('->','.',$sTitle);// 还原属性值
		}
		$sCompiled=$this->import($sFile,$sValue,$sBasepath,$sTitle,false,$sType);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	public function import($sFile,$sValue,$sBasepath,$sTitle,$bIsFile=false,$sType=''){
		$sParseStr='';
		$sEndStr='';
		if($sValue!==null && $sValue!=''){// 判断是否存在加载条件 允许使用函数判断(默认为isset)
			$arrValue=explode('|',$sValue);
			$sName=array_shift($arrValue);
			$sName=$this->parseVar($sName);
			if(!empty($varArray)){
				$sName=$this->parseVarFunction($sName,$arrValue);
			}
			else{
				$sName='isset('.$sName.')';
			}
			$sParseStr.='<?php if('.$sName.'): ?>';
			$sEndStr='<?php endif; ?>';
		}
		if($sTitle===null || $sTitle==''){// title判断
			$sTitle='DoYouHaoBaby - Import - title';
		}
		if($bIsFile){// 是否是文件
			$arrArray=explode(',',$sFile);// 文件方式导入&支持多文件导入
			foreach($arrArray as $sVal){
				if(!$sType || isset($sType)){
					$sType=strtolower(substr(strrchr($sVal,'.'),1));
				}
				$sVal=str_replace(G::tidyPath(DYHB_PATH),'<?php echo DYHB_PATH;?>',G::tidyPath($sVal));// 对$sVal进行替换，防止迁移路径错误
				$sVal=str_replace(G::tidyPath(APP_PATH),'<?php echo APP_PATH;?>',G::tidyPath($sVal));
				switch($sType){
					case 'js':
						$sParseStr.='<script type="text/javascript" src="'.$sVal.'"></script>';
						break;
					case 'css':
						$sParseStr.='<link rel="stylesheet" type="text/css" href="'.$sVal.'" />';
						break;
					case 'img':
						$sParseStr.='<img src="'.$sVal.'" title="'.$sTitle.'"/>';
						break;
					case 'php':
						$sParseStr.='<?php G::includeFile("'.$sVal.'"); ?>';
						break;
				}
			}
		}
		else{
			$sType=$sType!==null?$sType:'js';// 命名空间导入模式 默认是js
			if($sBasepath!==null){
				$sBasepath=str_replace(G::tidyPath(DYHB_PATH),'<?php echo DYHB_PATH;?>',G::tidyPath($sBasepath));// 对$sBasepath进行替换，防止迁移路径错误
				$sBasepath=str_replace(G::tidyPath(APP_PATH),'<?php echo APP_PATH;?>',G::tidyPath($sBasepath));
			}
			$sBasepath=$sBasepath!==null?$sBasepath:'<?php echo __LIBCOM__;?>';
			switch(strtoupper($sBasepath)){
				case 'PUBLIC':// web根目录
					$sBasepath='<?php echo __PUBLIC__;?>';
					$sBasepathTwo='__PUBLIC__';
					break;
				case 'APPPUB':// 项目入口公用目录
					$sBasepath='<?php echo __APPPUB__;?>';
					$sBasepathTwo='__APPPUB__';
					break;
				case 'TMPLPUB':// 模板公用目录
					$sBasepath='<?php echo __TMPLPUB__;?>';
					$sBasepathTwo='__TMPLPUB__';
  					break;
			}
			$arrArray=explode(',',$sFile);// 命名空间方式导入外部文件
			foreach($arrArray as $sVal){
				switch($sType){
					case 'js':
						$sParseStr.='<script type="text/javascript" src="'.$sBasepath.'/'.str_replace(array('.','#'),array('/','.'),$sVal).'.js"></script>';
						break;
					case 'css':
						$sParseStr.='<link rel="stylesheet" type="text/css" href="'.$sBasepath.'/'.str_replace(array('.','#'),array('/','.'),$sVal).'.css" />';
						break;
					case 'img':
						$sParseStr.='<img src="'.$sBasepath.'/'.str_replace(array('.','#'),array('/','.'),$sVal).'" title="'.$sTitle.'"/>';
						break;
					case 'php':
						$sParseStr.='<?php G::includeFile('.$sBasepathTwo.'."/'.str_replace(array('.','#'),array('/','.'),$sVal).'.php"); ?>';
						break;
				}
			}
		}
		return $sParseStr.$sEndStr;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('import',__CLASS__);
		$oParent->regCompilers('import',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_import::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_include_sub extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='file';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$sTemplateObjVarName='$oTemplate'.rand(0,9999);// Template对象变量名
		$sCompiled="<?php {$sTemplateObjVarName}=new TemplateHtml();";
		$oAttribute=$oObj->getAttribute();// 节点属性
		foreach($oAttribute->_arrAttributes as $sAttributeName=>$sVarExpress){// 变量
			$sAttributeName=$oAttribute->getAttributeOriginName($sAttributeName);
			if(preg_match('/^assign\.(.+)/i',$sAttributeName,$arrRes)){
				$sVarName=$arrRes[1];
				$sCompiled.="{$sTemplateObjVarName}->setVar('{$sVarName}',{$sVarExpress});";
			}
		}
		$sFilename=$oAttribute->getAttribute('file');// 文件名
		$sCharset=$oAttribute->getAttribute('char');
		$sContentType=$oAttribute->getAttribute('type');
		$bDisplayAtOnce=$oAttribute->getAttribute('display');
		$nVarFlags=$oAttribute->getAttribute('var');
		$sFilename=str_replace('->','.',$sFilename);// 将 -> 还原为 .
		$sCharset=str_replace('->','.',$sCharset);
		$sContentType=str_replace('->','.',$sContentType);
		$bDisplayAtOnce=str_replace('->','.',$bDisplayAtOnce);
		$nVarFlags=str_replace('->','.',$nVarFlags);
		$sFilename=str_replace('->','.',$sFilename);// 支持if的condition变量解析
		$sFilename=$this->parseCondition($sFilename);
		$sCharset=str_replace('->','.',$sCharset);
		$sCharset=$this->parseCondition($sCharset);
		$sContentType=str_replace('->','.',$sContentType);
		$sContentType=$this->parseCondition($sContentType);
		$bDisplayAtOnce=str_replace('->','.',$bDisplayAtOnce);
		$bDisplayAtOnce=$this->parseCondition($bDisplayAtOnce);
		$nVarFlags=str_replace('->','.',$nVarFlags);
		$nVarFlags=$this->parseCondition($nVarFlags);
		if(!$sCharset) {$sCharset='utf-8';}
		if(!$sContentType) {$sContentType='text/html';}
		if(!$bDisplayAtOnce) {$bDisplayAtOnce='false';}
		if(!$nVarFlags) $nVarFlags=TemplateHtml::VAR_ALL;
		$sCompiled.="{$sTemplateObjVarName}->display('{$sFilename}','{$sCharset}','{$sContentType}',{$bDisplayAtOnce},{$nVarFlags}); ?>";
		unset($sTemplateObjVarName,$sFilename,$sContentType,$bDisplayAtOnce,$nVarFlags);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('d*include',__CLASS__);
		$oParent->regCompilers('d*include',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_include_sub::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_include extends TemplateHtmlNodeCompiler_tpl_include_sub{

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFilename=$oAttribute->getAttribute('file');// 文件名
		$sContentType=$oAttribute->getAttribute('type');
		$sCharset=$oAttribute->getAttribute('char');
		$sContentTypeContent=$sContentType!==null && !empty($sContentType)? ',\''.$sContentType.'\'':',\'text/html\'';
		$sCharsetContent=$sCharset!==null && !empty($sCharset)?',\''.$sCharset.'\'':',\'utf-8\'';
		$sFilename=str_replace('->','.',$sFilename);// 将 -> 还原为 .
		$sContentTypeContent=str_replace('->','.',$sContentTypeContent);
		$sContentTypeContent=str_replace('->','.',$sContentTypeContent);
		$sFilename=$this->parseContent($sFilename); // 替换一下，防止迁移的时候由于物理路径的原因，需要重新生成编译文件
		$sFilename=str_replace(G::tidyPath(DYHB_PATH),'DYHB_PATH.\'',G::tidyPath($sFilename));
		$sFilename=str_replace(G::tidyPath(APP_PATH),'APP_PATH.\'',G::tidyPath($sFilename));
		$sFilename=rtrim($sFilename,'\'');
		$sFilename=strpos($sFilename,'$')===0?$sFilename:$sFilename.'\'';
		$sCompiled="<?php \$this->includeChildTemplate({$sFilename}{$sCharsetContent}{$sContentTypeContent});?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	public function parseContent($sTmplPublicName){
		$arrTemplateInfo=array();
		if(''==$sTmplPublicName){// 如果模板文件名为空 按照默认规则定位
			$arrTemplateInfo=array('file'=>($GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/' && MODULE_NAME==='public'?'Public':MODULE_NAME).$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'].ACTION_NAME.$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX']);
		}
		else if(!strpos($sTmplPublicName,':\\') && strpos($sTmplPublicName,'/')!==0 && substr($sTmplPublicName,0,1)!='$' && !is_file($sTmplPublicName)){//支持加载变量文件名&D:\phpcondition\......排除绝对路径分析&/home1/...... 排序Linux环境下面的绝对路径分析
			if(strpos($sTmplPublicName,'@')){// 分析主题
				$arrArray=explode('@',$sTmplPublicName);
				$arrTemplateInfo['theme']=ucfirst(strtolower(array_shift($arrArray)));
				$sTmplPublicName=array_shift($arrArray);
			}
			$arrValue=explode('+',$sTmplPublicName);
			$sTmplModuleName=$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/' && $arrValue[0]=='public'?'Public':$arrValue[0];
			$sTmplPublicName=str_replace($arrValue[0].'+',$sTmplModuleName.$GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR'],$sTmplPublicName);// 分析文件&模块和操作
			$arrTemplateInfo['file']=$sTmplPublicName.$GLOBALS['_commonConfig_']['TEMPLATE_SUFFIX'];
		}
		if(!empty($arrTemplateInfo)){
			$sPath=!empty($arrTemplateInfo['theme']) || $GLOBALS['_commonConfig_']['TMPL_MODULE_ACTION_DEPR']=='/'?dirname(dirname(__TMPL_FILE_PATH__)):dirname(__TMPL_FILE_PATH__);
			return $sPath.'/'.(!empty($arrTemplateInfo['theme'])?$arrTemplateInfo['theme'].'/':'').$arrTemplateInfo['file'];
		}
		else{
			if(substr($sTmplPublicName,0,1)=='$'){// 返回变量
				return $sTmplPublicName;
			}
			else{
				return $sTmplPublicName;
			}
		}
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('include',__CLASS__);
		$oParent->regCompilers('include',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_include::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_lang extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sLange=$oAttribute->getAttribute('lang');// 指定语言
		$sLange=$sLange?'\''.ucfirst($sLange).'\'':'null';
		$sPackage=$oAttribute->getAttribute('package');// 指定语言包
		$sPackage=$sPackage?'\''.ucfirst($sPackage).'\'':'null';
		$sArgs='';// 参数
		$oAttribute=$oObj->getAttribute();// 节点属性
		foreach($oAttribute->_arrAttributes as $sAttributeName=>$sVarExpress){// 变量
			$sAttributeName=$oAttribute->getAttributeOriginName($sAttributeName);
			if(preg_match('/^condition\d{1,2}$/',$sAttributeName)){// 匿名属性用作语句的参数
				$sConditionValue=str_replace('->','.',$sVarExpress);
				$sConditionValue=$this->parseCondition($sConditionValue);
				if(!empty($sConditionValue)){$sArgs.=',"'.$sConditionValue.'"';}
			}
		}
		$oBody= $oObj->getBody();// 句子
		$sSentence=addslashes(stripslashes(trim($oBody->getCompiled())));
		$sCompiled="<?php print G::L(\"{$sSentence}\",{$sPackage},{$sLange}{$sArgs}); ?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('lang',__CLASS__);
		$oParent->regCompilers('lang',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_lang::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_load extends TemplateHtmlNodeCompiler_tpl_import{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='file';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFile=$oAttribute->getAttribute('file');// 获取属性
		$sFile=str_replace('->','.',$sFile);// 还原属性值
		$sValue=$oAttribute->getAttribute('value');
		$sBasepath=$oAttribute->getAttribute('basepath');
		if($sBasepath!==null){
			$sBasepath=str_replace('->','.',$sBasepath);// 还原属性值
		}
		$sCompiled=$this->import($sFile,$sValue,$sBasepath,null,true);
		$oObj->setCompiled($sCompiled);
		return $sCompiled ;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('load',__CLASS__);
		$oParent->regCompilers('load',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_load::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_loop extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='start';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sStart=$oAttribute->getAttribute('start');// 循环条件
		$sEnd=$oAttribute->getAttribute('end');
		$sVar=$oAttribute->getAttribute('var');
		$sStep=$oAttribute->getAttribute('step');
		if($sEnd===NULL){// 缺省值
			$sEnd='0';
		}
		if($sVar===NULL){
			$sVar='nLoopValue'.rand(0,99999);
		}
		if($sStep===NULL){
			$sStep='1' ;
		}
		$sStartVarName='$nLoopStart'.rand(0,99999);// 循环记号
		$sEndVarName='$nLoopEnd'.rand(0,99999);
		$oBody=$oObj->getBody();// 循环体
		$sBody=$oBody->getCompiled();
		$sCompiled="<?php {$sStartVarName}={$sStart} ;{$sEndVarName}={$sEnd} ;
for(\${$sVar}={$sStartVarName}; \${$sVar}<={$sEndVarName}; \${$sVar} += {$sStep}): ?>
{$sBody}
<?php endfor; ?>
" ;
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('loop',__CLASS__);
		$oParent->regCompilers('loop',__CLASS__);
		TemplateHtmlNodeParser::regCompilers('for',__CLASS__);
		$oParent->regCompilers('for',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_loop::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_php extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$oBody=$oObj->getBody();// 条件 体
		$sBody=$oBody->getCompiled();
		$sBody=$this->parseCondition($sBody);
		$sCompiled="<?php {$sBody} ?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('php',__CLASS__);
		$oParent->regCompilers('php',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_php::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_volist extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$oBody=$oObj->getBody();// 循环体
		$sBody=$oBody->getCompiled();
		$sName=$oAttribute->getAttribute('name');// 各项属性
		$sId=$oAttribute->getAttribute('id');
		$sEmpty=$oAttribute->getAttribute('empty');
		$sI=$oAttribute->getAttribute('i');
		$sKey=$oAttribute->getAttribute('key');
		$nMod=$oAttribute->getAttribute('mod');
		$nLength=$oAttribute->getAttribute('length');
		$nOffset=$oAttribute->getAttribute('offset');
		if($sI===null){// 默认解析
			$sI='i';
		}
		if($sKey===null){
			$sKey='key';
		}
		if($nMod===null){
			$nMod=2;
		}
		if(preg_match("/[^\d-.,]/",$nMod)){
			$nMod='$'.$nMod;
		}
		if($sEmpty===null){
			$sEmpty='';
		}
		if($nLength===null){
			$nLength='';
		}
		if($nOffset===null){
			$nOffset='';
		}
		$sName=$this->parseVar($sName);
		$sCompiled='<?php if(is_array('.$sName.')): $'.$sI.'=0;';
		if(''!=$nLength){
			$sCompiled.=' $arrList=array_slice('.$sName.','.$nOffset.','.$nLength.');';
		}
		elseif(''!=$nOffset){
			$sCompiled.='$arrList=array_slice('.$sName.','.$nOffset.');';
		}
		else{
			$sCompiled.=' $arrList='.$sName.';';
		}
		$sCompiled.='if(count($arrList)==0): echo "'.$sEmpty.'";';
		$sCompiled.='else: ';
		$sCompiled.='foreach($arrList as $'.$sKey.'=>$'.$sId.'): ';
		$sCompiled.='++$'.$sI.';';
		$sCompiled.='$mod=($'.$sI.' % '.$nMod.')?>';
		$sCompiled.=$sBody;
		$sCompiled.='<?php endforeach; endif; else: echo "'.$sEmpty.'";endif; ?>';
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('volist',__CLASS__);
		$oParent->regCompilers('volist',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_volist::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_while extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='condition';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sCondition=$oAttribute->getAttribute('condition');// 循环条件
		$sCondition=$this->parseCondition($sCondition);
		$oBody=$oObj->getBody();// 循环体
		$oObj->setCompiled("<?php while({$sCondition}): ?> {$sBody} <?php endwhile; ?>");
		$oObj->setCompiler(null);
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('while',__CLASS__);
		$oParent->regCompilers('while',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_while::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_break extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag( $sNodeName){
		return in_array(strtolower($sNodeName),array('Template:break','break'));
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oObj->setCompiled('<?php break; ?>');
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('break',__CLASS__);
		$oParent->regCompilers('break',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_break::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_continue extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return in_array(strtolower($sNodeName),array('Template:continue','continue'));
	}

	public function compile(TemplateObj $oObj){
		$oObj->setCompiled('<?php continue; ?>');
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('continue',__CLASS__);
		$oParent->regCompilers('continue',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_continue::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_css extends TemplateHtmlNodeCompiler_tpl_import{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='file';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFile=$oAttribute->getAttribute('file');// 获取属性
		$sFile=str_replace('->','.',$sFile);// 还原属性值
		$sValue=$oAttribute->getAttribute('value');
		$sBasepath=$oAttribute->getAttribute('basepath');
		if($sBasepath!==null){
			$sBasepath=str_replace('->','.',$sBasepath);// 还原属性值
		}
		$sCompiled=$this->import($sFile,$sValue,$sBasepath,null,true);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('css',__CLASS__);
		$oParent->regCompilers('css',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_css::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_date extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return in_array(strtolower($sNodeName),array('Template:date','date'));
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFormat=$oAttribute->getAttribute('format');// 格式
		if(!$sFormat){
			$sFormat='Y.m.d G:i:s';
		}
		$sTime=$oAttribute->getAttribute('time');// 时间
		$sTime=str_replace('->','.',$sTime);
		$sTime=$this->parseCondition($sTime);
		if(!$sTime){
			$sTime='CURRENT_TIMESTAMP';
		}
		$oObj->setCompiled('<'."?php print date('{$sFormat}',{$sTime})?".'>');
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('date',__CLASS__);
		$oParent->regCompilers('date',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_date::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_default extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$sCompiled="<?php default: ?>";
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('default',__CLASS__);
		$oParent->regCompilers('default',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_default::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_defined extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(defined("'.$sName.'")): ?>'.$sBody.'<?php endif; ?>';// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('defined',__CLASS__);
		$oParent->regCompilers('defined',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_defined::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_do extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='while';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sWhile=$oAttribute->getAttribute('while');// 循环条件
		$sWhile=str_replace('->','.',$sWhile);
		$sWhile=$this->parseCondition($sWhile);
		$oBody=$oObj->getBody();// 循环体
		$sBody=$oBody->getCompiled();
		$oObj->setCompiled("<?php do{ ?>{$sBody}<?php }while({$sWhile});?>");
		$oObj->setCompiler(null);
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('do',__CLASS__);
		$oParent->regCompilers('do',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_do::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_egt extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'egt');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('egt',__CLASS__);
		$oParent->regCompilers('egt',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_egt::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_elt extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'elt');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('elt',__CLASS__);
		$oParent->regCompilers('elt',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_elt::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_empty extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(empty($'.$sName.')): ?>'.$sBody.'<?php endif; ?>';// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('empty',__CLASS__);
		$oParent->regCompilers('empty',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_empty::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_gt extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'gt');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('gt',__CLASS__);
		$oParent->regCompilers('gt',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_gt::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_heq extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'heq');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('heq',__CLASS__);
		$oParent->regCompilers('heq',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_heq::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_in extends TemplateHtmlNodeCompiler_tpl_range{

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 获取属性
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->range($sBody,$sName,$sValue,'in');
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('in',__CLASS__);
		$oParent->regCompilers('in',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_in::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_isset extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 条件 表达式
		$oBody=$oObj->getBody();// 条件 体
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(isset($'.$sName.')): ?>'.$sBody.'<?php endif; ?>';
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('isset',__CLASS__);
		$oParent->regCompilers('isset',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_isset::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_iterate extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 属性
		$sIterator=$oAttribute->getAttribute('iterator');// 迭代器
		if(!$sIterator){
			$sIterator=$oAttribute->getAttribute('condition0');
		}
		$sIterator=str_replace('->','.',$sIterator);
		$sIterator=$this->parseCondition($sIterator);
		A::ASSERT_($sIterator,G::L('Template:Iterate 类型节点的iterator属性不能为空' ,'Dyhb'));
		$sIdxName=$oAttribute->getAttribute('index');// 迭代指针
		$sIdxName=str_replace('->','.',$sIdxName);
		$sIdxName=$this->parseCondition($sIdxName);
		if($sIdxName){
			$sIdxDeclear="\r\n\${$sIdxName}=-1 ;";
			$sIdxIncreace="\r\n\t\${$sIdxName} ++;";
		}
		else{
			$sIdxDeclear=$sIdxIncreace='';
		}
		$sVarName=$oAttribute->getAttribute('var');// 项目变量
		$sVarName=str_replace('->','.',$sVarName);
		$sVarName=$this->parseCondition($sVarName);
		if($sVarName){
			$sVar="\r\n\t\${$sVarName}=\${$sIterator}->current();";
		}
		else{
			$sVar='';
		}
		$oBody=$oObj->getBody();// 迭代过程
		$sBody=$oBody->getCompiled();
		$sCompiled="<?php \${$sIterator}->first();{$sIdxDeclear}
while(!\${$sIterator}->isDone()): {$sIdxIncreace}{$sVar} ?>
{$sBody}
<?php \${$sIterator}->next();endwhile;  ?>";
		$oObj->setCompiled($sCompiled);
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('iterate',__CLASS__);
		$oParent->regCompilers('iterate',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_iterate::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_js extends TemplateHtmlNodeCompiler_tpl_import{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='file';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sFile=$oAttribute->getAttribute('file');// 获取属性
		$sFile=str_replace('->','.',$sFile);// 还原属性值
		$sValue=$oAttribute->getAttribute('value');
		$sBasepath=$oAttribute->getAttribute('basepath');
		if($sBasepath!==null){
			$sBasepath=str_replace('->','.',$sBasepath);// 还原属性值
		}
		$sCompiled=$this->import($sFile,$sValue,$sBasepath,null,true);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('js',__CLASS__);
		$oParent->regCompilers('js',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_js::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_lt extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'lt');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('lt',__CLASS__);
		$oParent->regCompilers('lt',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_lt::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_neq extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'neq');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('neq',__CLASS__);
		$oParent->regCompilers('neq',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_neq::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_nheq extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'heq');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('nheq',__CLASS__);
		$oParent->regCompilers('nheq',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_nheq::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notdefined extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(!defined("'.$sName.'")): ?>'.$sBody.'<?php endif; ?>';// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notdefined',__CLASS__);
		$oParent->regCompilers('notdefined',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notdefined::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notempty extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(!empty($'.$sName.')): ?>'.$sBody.'<?php endif; ?>';// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notempty',__CLASS__);
		$oParent->regCompilers('notempty',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notempty::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notequal extends TemplateHtmlNodeCompiler_tpl_compare{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 比较条件
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->compare($sBody,$sName,$sValue,'notequal');// 获取内容
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notequal',__CLASS__);
		$oParent->regCompilers('notequal',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notequal::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notin extends TemplateHtmlNodeCompiler_tpl_range{

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 获取属性
		$sValue=$oAttribute->getAttribute('value');
		$oBody=$oObj->getBody();// 内容
		$sBody=$oBody->getCompiled();
		$sCompiled=$this->range($sBody,$sName,$sValue,'notin');
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notin',__CLASS__);
		$oParent->regCompilers('notin',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notin::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notisset extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 条件 表达式
		$oBody=$oObj->getBody();// 条件 体
		$sBody=$oBody->getCompiled();
		$sCompiled='<?php if(!isset($'.$sName.')): ?>'.$sBody.'<?php endif; ?>';
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notisset',__CLASS__);
		$oParent->regCompilers('notisset',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notisset::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_notpresent extends TemplateHtmlNodeCompiler_tpl_notisset{

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('notpresent',__CLASS__);
		$oParent->regCompilers('notpresent',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_notpresent::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_print extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$oBody=$oObj->getBody();// 节点正文
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sDecorator=$oAttribute->getAttribute('decorator');
		if($oBody){// 成对标签 节点
			$sBody=$oBody->getCompiled();
			if($sDecorator){// 编译修饰器
				$sBody=self::compileDecorator($sBody);
			}
			$sCompiled="<?php print {$sBody};?>";
		}
		else{// 单行节点
			$sExp=$oAttribute->getAttribute('default0');
			$sExp=trim($sExp);
			if($sDecorator){// 编译修饰器
				$sExp=self::compileDecorator($sExp);
			}
			$sCompiled=empty($sExp)? '': "<?php print {$sExp};?>";
		}
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('print',__CLASS__);
		TemplateHtmlNodeParser::regCompilers('echo',__CLASS__);
		$oParent->regCompilers('print',__CLASS__);
		$oParent->regCompilers('echo',__CLASS__);
	}

	static public function compileDecorator($sVariable,$sDecoratorAttribute){
		A::STRING($sVariable);
		$sReturn=$sVariable;
		$arrDecoratorSources=explode('|',$sDecoratorAttribute);
		foreach($arrDecoratorSources as $sDecoratorSource){
			$sDecoratorSource=trim($sDecoratorSource);
			$arrResult=array();
			if(!preg_match('/^([\w_\$::]+)(\((.*)\))?$/',$sDecoratorSource,$arrResult)){
				A::ASSERT_(0,G::L('遇到无法编译的节点修饰器属性: %s','dyhb',null,$sDecoratorAttribute));
			}
			$sArgList=trim($arrResult[3]);// 参数
			$sArgList=str_replace('%%',$sReturn,$sArgList);
			if(strstr($arrResult[1],'::')===false){// 全局函数 修饰器
				$sReturn="{$arrResult[1]}({$sArgList})";
			}
			else{// 类方法 修饰器
				list($sClass,$sMethod)=explode('::',$arrResult[1]);
				$sClass=trim($sClass);
				$sMethod=trim($sMethod);
				if(substr($sClass,0,1)==='$'){// 对象方法
					$sReturn="{$sClass}->{$sMethod}({$sArgList})";
				}
				else{// 静态方法
					$sReturn="{$sClass}::{$sMethod}({$sArgList})";
				}
			}
		}
		return $sReturn;
	}

}
TemplateHtmlNodeCompiler_tpl_print::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_range extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
		$this->_arrNotNullAttributes[]='value';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sBody=$oBody->getCompiled();
		$sName=$oAttribute->getAttribute('name');// 获取属性
		$sValue=$oAttribute->getAttribute('value');
		$sType=$oAttribute->getAttribute('type');
		$sType=$sType!==null?strtolower($sType):$sType;
		$oBody=$oObj->getBody();// 内容
		$sCompiled=$this->range($sBody,$sName,$sValue);
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	public function range($sContent,$sName,$sValue,$sType='in'){
		$arrVar=explode('|',$sName);// 尝试解析
		$sName=array_shift($arrVar);
		$sName=$this->parseVar($sName);
		$sfun=($sType=='in')?'in_array':'!in_array';// 使用函数
		if(count($arrVar)>0){// 存在多余的元素，尝试使用函数来解析
			$sName=$this->parseVarFunction($sName,$arrVar);
		}
		if('$'==substr($sValue,0,1)){
			$sValue=$this->parseVar(substr($sValue,1));
			$sParseStr='<?php if('.$sfun.'(('.$sName.'),is_array('.$sValue.')?'.$sValue.':explode(\',\','.$sValue.'))): ?>'.$sContent.'<?php endif; ?>';
		}
		else{
			$sValue='"'.$sValue.'"';
			$sParseStr='<?php if('.$sfun.'(('.$sName.'),explode(\',\','.$sValue.'))): ?>'.$sContent.'<?php endif; ?>';
		}
		return $sParseStr;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('range',__CLASS__);
		$oParent->regCompilers('range',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_range::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_subtemplate extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='call';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('call');// 各项属性
		$sCompiled=TemplateHtmlNodeCompiler_tpl_subtemplate_declare::getSubtemplate($sName);// 编译
		$sRemoveNl=$oAttribute->getAttribute('remove');
		if(!in_array(strtolower($sRemoveNl),array('off','no','false','0'))){
			$sCompiled=str_replace("\r",'',$sCompiled);
			$sCompiled=str_replace("\n",'',$sCompiled);
		}
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('subtemplate',__CLASS__);
		$oParent->regCompilers('subtemplate',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_subtemplate::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_subtemplate_declare extends TemplateHtmlNodeCompilerBase{

	static private $_arrSubtemplates=array();

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 各项属性
		self::regSubtemplate($sName,$oObj);// 注册
		$oObj->setCompiled('');
		return '';
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('declare',__CLASS__);
		$oParent->regCompilers('declare',__CLASS__);
	}

	static public function regSubtemplate($sSubtemplateName,TemplateObj $oObj){
		A::ASSERT_(!isset(self::$_arrSubtemplates [$sSubtemplateName]),G::L('无法重复定义子模版：%s','dyhb',null,$sSubtemplateName));
		$sRegex="/".'<declare name=(|.+?)>'."(|.+?)".'<\/declare>'."/s";// 将子模板中的数据匹配出来
		preg_match($sRegex,$oObj->getCompiled(),$arrResult);
		self::$_arrSubtemplates [$sSubtemplateName]=$arrResult[2];
	}

	static public function getSubtemplate($sSubtemplateName){
		A::ASSERT_(isset(self::$_arrSubtemplates [$sSubtemplateName]),G::L('正在使用尚未声明的子模版：%s','dyhb',null,$sSubtemplateName));
		return self::$_arrSubtemplates [$sSubtemplateName];
	}

}
TemplateHtmlNodeCompiler_tpl_subtemplate_declare::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_switch extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='name';
	}

	static public function queryCanbeSingleTag( $sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sName=$oAttribute->getAttribute('name');// 各项属性
		$oBody=$oObj->getBody();// Switch体
		$sBody=$oBody->getCompiled();
		$sName=str_replace('->','.',$sName);
		$arrVar=explode('|',$sName);
		$sName=array_shift($arrVar);
		$sName=$this->parseVar($sName);
		if(count($arrVar)>0){
			$sName=$this->parseVarFunction($sName,$arrVar);
		}
		$sCompiled='<?php switch('.$sName.'): ?>'.trim($sBody).'<?php endswitch;?>';
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('switch',__CLASS__);
		$oParent->regCompilers('switch',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_switch::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_switch_case extends TemplateHtmlNodeCompilerBase{

	public function __construct(){
		parent::__construct();
		$this->_arrNotNullAttributes[]='value';
	}

	static public function queryCanbeSingleTag($sNodeName){
		return false;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sValue=$oAttribute->getAttribute('value');// 各项属性
		$sBreak=$oAttribute->getAttribute('break');
		$oBody=$oObj->getBody();// 执行体
		$sBody=$oBody->getCompiled();
		if('$'==substr($sValue,0,1)){// 变量
			$arrVar=explode('|',$sValue);
			$sValue=array_shift($arrVar);
			$sValue=$this->parseVar(substr($sValue,1));
			if(count($arrVar)>0){
				$sValue=$this->parseVarFunction($sValue,$arrVar);
			}
			$sValue='case '.$sValue.': ';
		}
		elseif(strpos($sValue,'|')){// 带有函数
			$arrValues=explode('|',$sValue);
			$sValue='';
			foreach($arrValues as $sVal){
				$sValue.='case "'.addslashes($sVal).'": ';
			}
		}
		else{
			$sValue='case "'.$sValue.'": ';
		}
		$sCompiled='<?php '.$sValue.' ?>'.$sBody;
		$bIsBreak=$sBreak!==null?$sBreak:'';
		if(''==$bIsBreak || $bIsBreak){
			$sCompiled.='<?php break;?>';
		}
		$oObj->setCompiled($sCompiled);
		return $sCompiled;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl_switch');
		TemplateHtmlNodeParser::regCompilers('case',__CLASS__);
		$oParent->regCompilers('case',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_switch_case::regToCompiler();

class TemplateHtmlNodeCompiler_tpl_tagphp extends TemplateHtmlNodeCompilerBase{

	static public function queryCanbeSingleTag($sNodeName){
		return true;
	}

	public function compile(TemplateObj $oObj){
		$this->checkNode($oObj);
		$oAttribute=$oObj->getAttribute();// 节点属性
		$sPos=$oAttribute->getAttribute('condition');// 条件 表达式
		if(strtolower($sPos)=='start'){$sRet='<'.'?php echo "&lt;?php";?'.'>';}
		else{$sRet='<'.'?php echo "?&gt;";?'.'>';}
		$oObj->setCompiled($sRet);
		return $sRet;
	}

	static public function regToCompiler(){
		$oParent=Dyhb::instance('TemplateHtmlNodeCompiler_tpl');
		TemplateHtmlNodeParser::regCompilers('tagphp',__CLASS__);
		$oParent->regCompilers('tagphp',__CLASS__);
	}

}
TemplateHtmlNodeCompiler_tpl_tagphp::regToCompiler();
