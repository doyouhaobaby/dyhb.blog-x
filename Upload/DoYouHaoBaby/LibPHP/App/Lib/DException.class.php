<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   异常捕获($) */

!defined('DYHB_PATH') && exit;

class DException extends Exception{

	private $_bExtra;
	private $_sType;

	public function __construct($sMessage,$nCode=0,$bExtra){
		parent::__construct($sMessage,$nCode);
		$this->_bExtra=$bExtra;
	}

	static public function exceptionPro(Exception &$oException){
		if(G::isKindOf($oException,__CLASS__)){
			$oException->defaultHandel();
		}
		else{
			self::generalHandel($oException);
		}
	}

	public function __toString(){
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}

	public function defaultHandel(){
		return self::generalHandel($this);
	}

	static public function generalHandel(Exception &$oException){
		G::exceptionHandler($oException);
	}

	public function formatException(){
		$arrError=array();
		$arrTrace=$this->getTrace();// 调试信息,通过throw_exception抛出的异常要去掉多余的调试信息
		if($this->_bExtra){
			array_shift($arrTrace);
		}
		$this->class=isset($arrTrace['0']['class'])?$arrTrace['0']['class']:'';
		$this->function=isset($arrTrace['0']['function'])?$arrTrace['0']['function']:'';
		$this->file=isset($arrTrace['0']['file'])?$arrTrace['0']['file']:'';
		$this->line=isset($arrTrace['0']['line'])?$arrTrace['0']['line']:'';

		$sTraceInfo='';
		$time= date('Y-m-d H:i:s',CURRENT_TIMESTAMP);
		foreach($arrTrace as $Val){
			$sClass=isset($Val['class'])?$Val['class']:'';
			$this->_sType=$sType=isset($Val['type'])?$Val['type']:'';
			$sFunction=isset($Val['function'])?$Val['function']:'';
			$sFile=isset($Val['file'])?$Val['file']:'';
			$sLine=isset($Val['line'])?$Val['line']:'';
			$args=isset($Val['args'])?$Val['args']:'';
			$sTraceInfo.='<b><font color="#fff">['.$time.']</font></b><br/> <font color="#698B22">'.$sFile.'</font> <font color="#EE4000">('.$sLine.')</font><br/>';
			$sTraceInfo.='<font color="#545454">'.$sClass.$sType.$sFunction.'(';
			if(is_array($args)){
				foreach($args as $sK=>$V){
					$sTraceInfo.=($sK!=0 ?',':'').gettype($V);
				}
			}
			$sTraceInfo.=")</font><br/><br/>";
		}
		$arrError['message']=$this->message;
		$arrError['type']=$this->_sType;
		$arrError['class']=$this->class;
		$arrError['code']=$this->getCode();
		$arrError['function']=$this->function;
		$arrError['line']=$this->line;
		$arrError['trace']=$sTraceInfo;
		return $arrError;
	}

}

/** 注册默认异常处理函数 */
set_exception_handler(array('DException','exceptionPro'));
