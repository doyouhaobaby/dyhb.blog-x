<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   this($)*/

!defined('DYHB_PATH') && exit;

/* 导入模板对象 */
require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateObj_.php');
require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Ojbect/TemplateHtmlObj_.php');
require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Ojbect/TemplateHtmlNode_.php');

abstract class TemplateObjParserBase{

	static public $_arrCompilers;
	public $_leftTag='{';
	public $_rightTag='}';

	static public function escapeCharacter($sTxt){
		$sTxt=str_replace('$','\$',$sTxt);
		$sTxt=str_replace('/','\/',$sTxt);
		$sTxt=str_replace('?','\\?',$sTxt);
		$sTxt=str_replace('*','\\*',$sTxt);
		$sTxt=str_replace('.','\\.',$sTxt);
		$sTxt=str_replace('!','\\!',$sTxt);
		$sTxt=str_replace('-','\\-',$sTxt);
		$sTxt=str_replace('+','\\+',$sTxt);
		$sTxt=str_replace('(','\\(',$sTxt);
		$sTxt=str_replace(')','\\)',$sTxt);
		$sTxt=str_replace('[','\\[',$sTxt);
		$sTxt=str_replace(']','\\]',$sTxt);
		$sTxt=str_replace(',','\\,',$sTxt);
		$sTxt=str_replace('{','\\{',$sTxt);
		$sTxt=str_replace('}','\\}',$sTxt);
		$sTxt=str_replace('|','\\|',$sTxt);
		return $sTxt;
	}

	abstract static public function regCompilers($sTag,$sClass);

}

class TemplateHtmlCodeParser extends TemplateObjParserBase{

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlCodeCompilers_.php');
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.Html.Code']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){
		self::$_arrCompilers['_code_'][$sTag]=$sClass;
	}

	protected function getRegexpParsingTemplateObj(array $arrCompilerNames){
		$sNames=implode('|',$arrCompilerNames);
		return "/".$this->_leftTag."({$sNames})(|.+?)".$this->_rightTag."/s";
	}

	public function parse(Template $oTemplate,$sTemplatePath,&$sCompiled){
		foreach(self::$_arrCompilers['_code_'] as $sObjType=>$sCompilers){
			$arrNames[]=TemplateObjParserBase::escapeCharacter($sObjType);// 处理一些正则表达式中有特殊意义的符号
		}
		if(!count($arrNames)){// 没有任何编译器
			return;
		}
		$sRegexp=$this->getRegexpParsingTemplateObj($arrNames);
		if($sRegexp===null){
			return;
		}
		$arrRes=array();// 分析
		if(preg_match_all($sRegexp,$sCompiled,$arrRes)){
			$nStartFindPos=0;
			foreach($arrRes[0] as $nIdx=>&$sObjectOriginTxt){
				$sObjType=trim($arrRes[1][$nIdx]);
				$sContent=trim($arrRes[2][$nIdx]);
				$oCompiler=Dyhb::instance(self::$_arrCompilers['_code_'][$sObjType]);
				$oTemplateObj=new TemplateHtmlObj($sObjectOriginTxt);// 创建对象
				$oTemplateObj->locate($sCompiled,$nStartFindPos);// 定位
				$oTemplateObj->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTemplateObj->getEndByte()+1;
				$oTemplateObj->setTemplate($oTemplate);// 进一步装配Template对象&所属模版
				$oTemplateObj->setParser($this);// 分析器
				$oTemplateObj->setCompiler($oCompiler);// 编译器
				$oTemplateObj->setContent($sContent);// 内容
				$oTemplate->putInTemplateObj($oTemplateObj);// 将Template对象加入到对象树中
			}
		}
	}

}

class TemplateHtmlGlobalParser extends TemplateObjParserBase{

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlGlobalCompilers_.php');
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.Global']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){}

	static public function encode($sContent){
		$nRand=rand(0,99999999);
		return "__##TemplateHtmlGlobalParser##START##{$nRand}:".base64_encode($sContent).'##END##TemplateHtmlGlobalParser##__';
	}

	public function parse(Template $oTemplate,$sTemplatePath,&$sCompiled){
		$oCompiler=TemplateHtmlGlobalCompiler::getGlobalInstance();
		$sLeftTag='[<\{]';
		$sRightTag='[\}>]';
		$arrRes=array();// 分析
		if(preg_match_all("/{$sLeftTag}tagself{$sRightTag}(.+?){$sLeftTag}\/tagself{$sRightTag}/isx",$sCompiled,$arrRes)){
			$nStartFindPos=0;
			foreach($arrRes[1] as $nIndex=>$sEncode){
				$sSource=trim($arrRes[0][$nIndex]);
				$sContent=trim($arrRes[1][$nIndex]);
				$oTemplateObj=new TemplateHtmlObj($sSource);// 创建对象
				$oTemplateObj->setCompiled($sContent);
				$oTemplateObj->locate($sCompiled,$nStartFindPos);// 定位
				$oTemplateObj->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTemplateObj->getEndByte()+1;
				$oTemplateObj->setTemplate($oTemplate);// 进一步装配Template对象&所属模版
				$oTemplateObj->setParser($this);// 分析器
				$oTemplateObj->setCompiler($oCompiler);// 编译器
				$oTemplateObj->setContent($sSource);// 内容
				$oTemplate->putInTemplateObj($oTemplateObj);// 将Template对象加入到对象树中
			}
		}
	}

}

class TemplateHtmlGlobalRevertParser extends TemplateObjParserBase{

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlGlobalRevertCompilers_.php');
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.GlobalRevert']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){}

	public function parse( Template $oTemplate,$sTemplatePath,&$sCompiled){
		$oCompiler=TemplateHtmlGlobalRevertCompiler::getGlobalInstance();
		$arrRes=array();// 分析
		if(preg_match_all('/__##TemplateHtmlGlobalParser##START##\d+:(.+?)##END##TemplateHtmlGlobalParser##__/',$sCompiled,$arrRes)){
			$nStartFindPos=0;
			foreach($arrRes[1] as $nIndex=>$sEncode){
				$sSource=$arrRes[0][$nIndex];
				$sContent=$arrRes[1][$nIndex];
				$oTemplateObj=new TemplateHtmlObj($sSource);// 创建对象
				$oTemplateObj->locate($sCompiled,$nStartFindPos);// 定位
				$oTemplateObj->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTemplateObj->getEndByte()+1;
				$oTemplateObj->setTemplate($oTemplate);// 进一步装配Template对象&所属模版
				$oTemplateObj->setParser($this);// 分析器
				$oTemplateObj->setCompiler($oCompiler);// 编译器
				$oTemplateObj->setContent($sContent);
				$oTemplate->putInTemplateObj($oTemplateObj);// 将Template对象加入到对象树中
			}
		}
	}

}

class TemplateHtmlNodeParser extends TemplateObjParserBase{

	private $_oNodeTagStack;

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlNodeCompilers_.php');
	}

	public function parse(Template $oTemplate,$sTemplatePath,&$sCompiled){
		$this->findNodeTag($oTemplate,$sCompiled,$sTemplatePath);// 查找分析Node的标签
		$this->packagingNode($oTemplate,$sCompiled);// 用标签组装Node
	}

	protected function findNodeTag(Template $oTemplate,&$sTemplateStream,$sTemplatePath){
		$this->_oNodeTagStack=new Stack('TemplateHtmlNodeTag');// 设置一个栈

		foreach(self::$_arrCompilers['_node_'] as $sObjType=>$sCompilers){// 所有一级节点名称
			$arrNames[]=TemplateObjParserBase::escapeCharacter($sObjType);// 处理一些正则表达式中有特殊意义的符号
		}
		if(!count($arrNames)){// 没有 任何编译器
			return;
		}

		$sNames=implode('|',$arrNames);
		$sRegexp="/<(\/?)(({$sNames})(:[^\s\>\}]+)?)(\s[^>\}]*?)?\/?>/isx";
		$nNodeNameIdx=2;// 标签名称位置
		$nNodeTopNameIdx=3;// 标签顶级名称位置
		$nTailSlasheIdx=1;// 尾标签斜线位置
		$nTagAttributeIdx=5;// 标签属性位置
		if(preg_match_all($sRegexp,$sTemplateStream,$arrRes)){// 依次创建标签对象
			$nStartFindPos=0;
			foreach($arrRes[0] as $nIdx=>&$sTagSource){
				$sNodeName=$arrRes[$nNodeNameIdx][$nIdx];
				$sNodeTopName=$arrRes[$nNodeTopNameIdx][$nIdx];
				$nNodeType=($arrRes[$nTailSlasheIdx][$nIdx]==='/')?(TemplateHtmlNodeTag::TYPE_TAIL):(TemplateHtmlNodeTag::TYPE_HEAD);
				$sNodeName=strtolower($sNodeName);// 将节点名称统一为小写
				$sNodeTopName=strtolower($sNodeTopName);
				$oTag=new TemplateHtmlNodeTag($sTagSource,$sNodeName,$nNodeType);// 创建对象
				if($oTag->getTagType()==TemplateHtmlNodeTag::TYPE_HEAD){// 头标签，创建一个
					$oTag->setTagAttributeSource($arrRes[$nTagAttributeIdx][$nIdx]);
				}
				$oTag->locate($sTemplateStream,$nStartFindPos);// 定位
				$oTag->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTag->getEndByte()+1;
				$oTag->setTemplate($oTemplate);// 进一步装配Template对象&所属模版
				$oTag->setParser($this);// 分析器
				$oTag->setCompiler(null);// 将属性分析器设为第一个编译器
				$this->_oNodeTagStack->in($oTag);// 加入到标签栈
			}
		}
	}

	protected function packagingNode(Template $oTemplate,&$sTemplateStream){
		$oTailStack=new Stack('TemplateHtmlNodeTag');// 尾标签栈
		while($oTag=$this->_oNodeTagStack->out()){// 载入节点属性分析器&依次处理所有标签
			if($oTag->getTagType()==TemplateHtmlNodeTag::TYPE_TAIL){// 尾标签，加入到$oTailStack中
				$oTailStack->in($oTag);
				continue;
			}
			$oCompiler=$this->queryCompilerByNodeName($oTag->getTagName());// 查询到对该节点负责的编译器
			$oTailTag=$oTailStack->out();// 从尾标签栈取出一项
			if(!$oTailTag or !$oTag->matchTail($oTailTag)){// 单标签节点
				$callback=array(get_class($oCompiler),'queryCanbeSingleTag');
				if(!call_user_func_array($callback,array($oTag->getTagName()))){
					G::E(G::L('%s 类型节点 必须成对使用，没有找到对应的尾标签; %s','Dyhb',null,$oTag->getTagName(),$oTag->getLocationDescription()));
				}
				if($oTailTag){// 退回栈中
					$oTailStack->in($oTailTag);
				}
				$oNode=new TemplateHtmlNode($oTag->getSource(),$oTag->getTagName());// 创建节点
				$oNode->setStartByte($oTag->getStartByte());// 装配节点
				$oNode->setEndByte($oTag->getEndByte());
				$oNode->setStartLine($oTag->getStartLine());
				$oNode->setEndLine($oTag->getEndLine());
				$oNode->setStartByteInLine($oTag->getStartByteInLine());
				$oNode->setEndByteInLine($oTag->getEndByteInLine());
				$oNode->setTemplateFile($oTag->getTemplateFile());
			}
			else{// 成对标签
				$nStart=$oTag->getStartByte();// 根据头标签开始和尾标签结束，取得整个节点内容
				$nLen=$oTailTag->getEndByte()-$nStart+1;
				$sNodeSource=substr($sTemplateStream,$nStart,$nLen);
				$oNode=new TemplateHtmlNode($sNodeSource,$oTag->getTagName());// 创建节点
				$oNode->setStartByte($oTag->getStartByte());// 装配节点
				$oNode->setEndByte($oTailTag->getEndByte());
				$oNode->setStartLine($oTag->getStartLine());
				$oNode->setEndLine($oTailTag->getEndLine());
				$oNode->setStartByteInLine($oTag->getStartByteInLine());
				$oNode->setEndByteInLine($oTailTag->getEndByteInLine());
				$oNode->setTemplateFile($oTag->getTemplateFile());
				$nStart=$oTag->getEndByte()+1;// 创建Body对象，并加入到对象树中
				$nLen=$oTailTag->getStartByte()-$nStart;
				if($nLen>0){
					$oBody=new TemplateHtmlObj(substr($sTemplateStream,$nStart,$nLen));// 创建
					$oBody->locate($sTemplateStream,$nStart);// 定位
					$oBody->setTemplate($oTemplate);// 装配&所属模版
					$oBody->setParser($this);// 分析器
					$oBody->setCompiler(null);// 编译器
					$oNode->addTemplateObj($oBody);// 加入&入树
				}
			}
			A::INSTANCE($oNode,'TemplateHtmlNode');
			$oAttribute=new TemplateHtmlNodeAttribute($oTag->getTagAttributeSource());// 为节点属性创建Template对象
			$oAttribute->locate($sTemplateStream,0);// 定位
			$oAttribute->setTemplate($oTemplate);// 所属模版
			$oAttribute->setParser($this);// 分析器
			$oAttribute->setCompiler('TemplateHtmlNodeAttributeParser');// 编译器
			$oNode->addTemplateObj($oAttribute);
			$oNode->setTemplate($oTemplate);// 装配节点&所属模版
			$oNode->setParser($this);// 分析器
			$oNode->setCompiler($oCompiler);// 编译器
			$oTemplate->putInTemplateObj($oNode);// 加入到节点树
		}
	}

	public function queryCompilerByNodeName($sNodeName){
		$arrNames=explode(':',$sNodeName);
		$oReturnCompiler=null;
		$arrCompilers=self::$_arrCompilers['_node_'];
		while($sName=array_shift($arrNames) and $arrCompilers){// 迭代查询
			$oCompiler=Dyhb::instance($arrCompilers[$sName]);;// 查询到编译器
			if(!$oCompiler){
				break;
			}
			$arrCompilers=$oCompiler->_arrCompilers;
			$oReturnCompiler=$oCompiler;
		}
		return $oReturnCompiler;
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.Html.Node']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){
		self::$_arrCompilers['_node_'][$sTag]=$sClass;
	}

}

class TemplateHtmlPhpParser extends TemplateHtmlGlobalParser{

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlPhpCompilers_.php');
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.Php']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){}

	public function parse(Template $oTemplate,$sTemplatePath,&$sCompiled){
		$oCompiler=TemplateHtmlPhpCompiler::getGlobalInstance();
		$arrRes=array();// 分析
		if(preg_match_all("/<\?(=|php|)(.+?)\?>/is",$sCompiled,$arrRes)){
			$nStartFindPos=0;
			foreach($arrRes[1] as $nIndex=>$sEncode){
				$sSource=trim($arrRes[0][$nIndex]);
				$sContent=trim($arrRes[1][$nIndex]);
				$oTemplateObj=new TemplateHtmlObj($sSource);// 创建对象
				$oTemplateObj->locate($sCompiled, $nStartFindPos);// 定位
				$oTemplateObj->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTemplateObj->getEndByte()+1;
				$oTemplateObj->setTemplate($oTemplate);// 进一步装配 Template对象&所属模版
				$oTemplateObj->setParser($this);// 分析器
				$oTemplateObj->setCompiler($oCompiler);// 编译器
				$oTemplate->putInTemplateObj($oTemplateObj);// 将Template对象加入到 对象树中
			}
		}
	}

}

class TemplateHtmlRevertParser extends TemplateObjParserBase{

	public function __construct(){
		require_once(DYHB_PATH.'/LibPHP/App/Package/Template/TemplateHtml/Compiler/TemplateHtmlRevertCompilers_.php');
	}

	static public function regToParser(){
		TemplateHtml::$_arrParses['Template.Revert']=__CLASS__;
		return __CLASS__;
	}

	static public function regCompilers($sTag,$sClass){}

	static public function encode($sContent){
		$nRand=rand(0,99999999);
		return "__##TemplateHtmlRevertParser##START##{$nRand}:".base64_encode($sContent).'##END##TemplateHtmlRevertParser##__';
	}

	public function parse( Template $oTemplate,$sTemplatePath,&$sCompiled){
		$oCompiler=TemplateHtmlRevertCompiler::getGlobalInstance();
		$arrRes=array();// 分析
		if(preg_match_all('/__##TemplateHtmlRevertParser##START##\d+:(.+?)##END##TemplateHtmlRevertParser##__/',$sCompiled,$arrRes)){
			$nStartFindPos=0;
			foreach($arrRes[1] as $nIndex=>$sEncode){
				$sSource=$arrRes[0][$nIndex];
				$oTemplateObj=new TemplateHtmlObj($sSource);// 创建对象
				$oTemplateObj->setCompiled($sEncode);
				$oTemplateObj->locate($sCompiled,$nStartFindPos);// 定位
				$oTemplateObj->setTemplateFile($sTemplatePath);
				$nStartFindPos=$oTemplateObj->getEndByte()+1;
				$oTemplateObj->setTemplate($oTemplate);// 进一步装配 Template对象&所属模版
				$oTemplateObj->setParser($this);// 分析器
				$oTemplateObj->setCompiler($oCompiler);// 编译器
				$oTemplate->putInTemplateObj($oTemplateObj);// 将Template对象加入到 对象树中
			}
		}
	}

}
