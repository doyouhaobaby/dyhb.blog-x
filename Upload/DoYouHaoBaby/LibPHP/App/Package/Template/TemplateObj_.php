<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   模版对象类($)*/

!defined('DYHB_PATH') && exit;

class TemplateObj{

	private $_oTemplate;
	private $_oParser;
	private $_oCompiler;
	private $_sSourceStream;
	private $_sCompiledStream;
	public $_arrChildTemplateObj=array();
	private $_sTemplateFile='';
	private $_nStartLine=0;
	private $_nEndLine=0;
	private $_nStartByte=0;
	private $_nEndByte=0;
	private $_nStartByteInLine=0;
	private $_nEndByteInLine=0;
	protected $_arrGet=array();
	protected $_arrSet=array();
	const LOCAL_MIN=1;
	const LOCAL_IN=1;
	const LOCAL_OUT=2;
	const LOCAL_FRONT=3;
	const LOCAL_BEHIND=4;
	const LOCAL_MAX=1;

	public function __construct($sSourceStream){
		$this->setSource($sSourceStream);
		$this->setCompiled($sSourceStream);
		$this->_arrGet['getTemplateFile']=&$this->_sTemplateFile;
		$this->_arrGet['getStartLine']=&$this->_nStartLine;
		$this->_arrGet['getEndLine']=&$this->_nEndLine;
		$this->_arrGet['getStartByte']=&$this->_nStartByte;
		$this->_arrGet['getEndByte']=&$this->_nEndByte;
		$this->_arrGet['getStartByteInLine']=&$this->_nStartByteInLine;
		$this->_arrGet['getEndByteInLine']=&$this->_nEndByteInLine;
		$this->_arrSet['setTemplateFile']=&$this->_sTemplateFile;
		$this->_arrSet['setStartLine']=&$this->_nStartLine;
		$this->_arrSet['setEndLine']=&$this->_nEndLine;
		$this->_arrSet['setStartByte']=&$this->_nStartByte;
		$this->_arrSet['setEndByte']=&$this->_nEndByte;
		$this->_arrSet['setStartByteInLine']=&$this->_nStartByteInLine;
		$this->_arrSet['setEndByteInLine']=&$this->_nEndByteInLine;
	}

	public function setTemplate(Template $oTempate){
		$oOldVal=$this->_oTemplate;
		$this->_oTemplate=$oTempate;
		return $oOldVal;

	}

	public function getTemplate(){
		return $this->_oTemplate;
	}

	public function setParser($oParser){
		$oOldVar=$this->_oParser;
		$this->_oParser=$oParser;
		return $oOldVar;
	}

	public function getParser(){
		return $this->_oParser;
	}

	public function setCompiler($Compiler){
		if(is_string($Compiler)){
			$oCompiler=Dyhb::instance($Compiler);
		}else{
			$oCompiler=$Compiler;
		}
		$oOldVar=$this->_oCompiler;
		$this->_oCompiler=$oCompiler;
		return $oOldVar;
	}

	public function getCompiler(){
		return $this->_oCompiler;
	}

	public function getSource(){
		return $this->_sSourceStream;
	}

	protected function setSource($sSource){
		A::STRING($sSource);
		$sOldValue=$this->_sSourceStream;
		$this->_sSourceStream=$sSource;
		return $sOldValue;
	}

	public function getCompiled(){
		return $this->_sCompiledStream;
	}

	public function setCompiled($sStream){
		A::STRING($sStream);
		$sOldVar=$this->_sCompiledStream;
		$this->_sCompiledStream=$sStream;
		return $sOldVar;
	}

	public function __call($sMethod, $arrArgs){
		if(isset($this->_arrGet[$sMethod])){return $this->_arrGet[$sMethod];}// 查询属性
		if(isset($this->_arrSet[$sMethod])){// 设置属性
			A::ASSERT_(isset($arrArgs[0]),G::L('缺少设置内容','Dyhb'));
			$sOldValue=$this->_arrSet[$sMethod];
			$this->_arrSet[$sMethod]=$arrArgs[0];
			return $sOldValue;
		}
		G::E(G::L('正在访问未知的方法：%s','Dyhb',null,$sMethod));
	}

	public function compareLocal(TemplateObj $oTemplateObj){
		if($oTemplateObj->getEndByte()<=$this->getStartByte()){return self::LOCAL_FRONT;}// 前
		if($oTemplateObj->getStartByte()>=$this->getEndByte()){return self::LOCAL_BEHIND;}// 后
		if($oTemplateObj->getStartByte()>=$this->getStartByte()){return self::LOCAL_IN;}// 内
		if($oTemplateObj->getStartByte()<= $this->getStartByte()){return self::LOCAL_OUT;}// 外
		G::E(G::L('不支持交叉Template对象。','Dyhb'));
	}

	public function addTemplateObj(TemplateObj $oTemplateObj){
		$arrNewList=array();
		foreach($this->_arrChildTemplateObj as $oMyTemplateObj){
			A::INSTANCE($oMyTemplateObj,'TemplateObj');
			if($oTemplateObj){
				$nLocal=$oMyTemplateObj->compareLocal($oTemplateObj);
				switch($nLocal){
					case self::LOCAL_FRONT:
						$arrNewList[]=$oTemplateObj;// 插入到当前位置
						$arrNewList[]=$oMyTemplateObj;
						$oTemplateObj=null;
						break;
					case self::LOCAL_BEHIND:
						$arrNewList[]=$oMyTemplateObj;
						break;
					case self::LOCAL_IN:
						$oMyTemplateObj->addTemplateObj($oTemplateObj);
						$arrNewList[]=$oMyTemplateObj;
						$oTemplateObj=null;
						break;
					case self::LOCAL_OUT:
						$oTemplateObj->addTemplateObj($oMyTemplateObj);
						break;
				}
			}
			else
				$arrNewList[]=$oMyTemplateObj;
		}
		if($oTemplateObj){// 加入到最后
			$arrNewList[]=$oTemplateObj;
		}
		$this->_arrChildTemplateObj=$arrNewList;
	}

	public function removeTemplateObj($nIdx){
		A::INT($nIdx);
		if(isset($this->_arrChildTemplateObj[$nIdx])){
			$oRet=$this->_arrChildTemplateObj[$nIdx];
			unset($this->_arrChildTemplateObj[$nIdx]);
			return $oRet;
		}
	}

	public function getTemplateObj($nIdx){
		A::INT($nIdx);
		return isset($this->_arrChildTemplateObj[$nIdx])? $this->_arrChildTemplateObj[$nIdx]: null;
	}

	public function locate($sTemplateStream, $nStart){
		A::STRING($sTemplateStream);
		A::INT($nStart);
		$sSourceStream=$this->getSource();
		if(empty($sSourceStream)){// 空对象
			$this->setStartByte(-1);
			$this->setEndByte(-1);
			$this->setStartLine(-1);
			$this->setEndLine(-1);
			$this->setEndLine(-1);
			$this->setStartByteInLine(-1);
			$this->setEndByteInLine(-1);
			return;
		}
		$nTotalByte=strlen($sTemplateStream);
		$nStartByte=strpos($sTemplateStream,$sSourceStream,$nStart);// 起止字节位置
		$nEndByte=$nStartByte+strlen($sSourceStream)-1;
		$this->setStartByte($nStartByte);
		$this->setEndByte($nEndByte);
		$nStartLine=($nStartByte<=0)?0:substr_count($sTemplateStream,"\n",0,$nStartByte);// 起止行数
		$nEndLine =($nEndByte<=0)?0:substr_count($sTemplateStream,"\n",0,$nEndByte);
		$this->setStartLine($nStartLine);
		$this->setEndLine($nEndLine);
		$nLineHeadOfStart=strrpos(substr($sTemplateStream,0,$nStartByte),"\n")+1;// 在行上的起止位置&&起始点 所在行 的行首位置
		$nLineHeadOfEnd=strrpos(substr($sTemplateStream,0,$nEndByte),"\n")+1;// 结束点 所在行 的行首位置
		$nStartInLine=$nStartByte-$nLineHeadOfStart;
		$nEndInLine=$nEndByte-$nLineHeadOfEnd;
		$this->setStartByteInLine($nStartInLine);
		$this->setEndByteInLine($nEndInLine);
	}

	public function compile(){
		while($oCompiler=$this->setCompiler(null)){// 编译自己
			$oCompiler->compile($this);
		}
	}

	public function getLocationDescription(){
		return G::L('行: %s; 列: %s; 文件: %s','Dyhb',null,$this->getStartLine(),$this->getStartByteInLine(),$this->getTemplateFile());
	}

}
