<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   PHP运行时控制($) */

!defined('DYHB_PATH') && exit;

class RunTime{

	static private $_arrHandles=array();
	const HANDLE_ERROR='error';
	const HANDLE_SHUTDOWN='shutdown';

	static public function runtimeShutdown(){
		self::runtimeEvent(self::HANDLE_SHUTDOWN);
	}

	static public function registerShutdown($Callback){
		self::registerRuntimeHandle($Callback,self::HANDLE_SHUTDOWN);
	}

	static public function unRegisterShutdown($Callback){
		self::unRegisterRuntimeHandle($Callback,self::HANDLE_SHUTDOWN);
	}

	static public function isShutdown($Callback){
		self::isRuntimeHandle($Callback,self::HANDLE_SHUTDOWN);
	}

	static public function exitBeforeShutdown(){
		$arrError=error_get_last();
		if($arrError!==null){
			if((!($arrError['type']&E_WARNING) AND !($arrError['type']&E_NOTICE) AND !($arrError['type']&E_STRICT))){
				E("<b>[{$arrError['type']}]:</b> {$arrError['message']}<br><b>File:</b> {$arrError['file']}<br><b>Line:</b> {$arrError['line']}");
			}
		}
	}

	static public function errorHandel($nErrorNo,$sErrStr,$sErrFile,$nErrLine){
		foreach (self::$_arrHandles as $sHandleKey=>$arrFunctions){
			$arrHandleKeyEles=explode(':',$sHandleKey);
			if(count($arrHandleKeyEles)!=2 OR $arrHandleKeyEles[0]!==self::HANDLE_ERROR){
				continue;
			}

			// 比较错误类型
			$nHandleFlag = intval($arrHandleKeyEles[1]);
			if($nHandleFlag&$nErrorNo){
				self::runtimeEvent($sHandleKey,array($nErrorNo,$sErrStr,$sErrFile,$nErrLine));
			}
		}
		return false;
	}

	static public function registerError($Callback,$nErrorType=E_ALL){
		self::registerRuntimeHandle($Callback,self::HANDLE_ERROR.':'.strval($nErrorType));
	}

	static public function unRegisterError($Callback,$nErrorType=E_ALL){
		self::unRegisterRuntimeHandle($Callback,self::HANDLE_ERROR.':'.strval($nErrorType));
	}

	static public function isError($Callback,$nErrorType=E_ALL){
		self::isRuntimeHandle($Callback,self::HANDLE_ERROR.':'.strval($nErrorType));
	}

	static public function unRegisterRuntimeHandle($Callback,$sHandle){
		$nResult=self::findRuntimeHandle($Callback,$sHandle);
		if($nResult!==null){
			unset(self::$_arrHandles[$sHandle][$nResult]);
		}
	}

	public function isRuntimeHandle($Callback,$sHandle){
		return (self::findRuntimeHandle($Callback,$sHandle)!==null);
	}

	static public function registerRuntimeHandle($Callback,$sHandle){
		if(self::findRuntimeHandle($Callback,$sHandle)===null){
			self::$_arrHandles[$sHandle][]=$Callback;
		}
	}

	private static function findRuntimeHandle($Callback,$sHandle){
		if(isset(self::$_arrHandles[$sHandle])){
			foreach(self::$_arrHandles[$sHandle] as $nKey=>$Func){
				if(G::isSameCallback($Callback,$Func)){
					return $nKey;
				}
			}
		}
		return null;
	}

	static public function runtimeEvent($sHandle,$arrArgs=array()){
		if(isset(self::$_arrHandles[$sHandle])){
			foreach(self::$_arrHandles[$sHandle] as $Func){
				call_user_func_array($Func,$arrArgs);
			}
		}
	}

}
