<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   断言函数集合($) */

!defined('DYHB_PATH') && exit;

class A{

	static public function ASSERT_($Exp,$sDes=null){
		if($Exp){return ;}
		if(empty($sDes)){$sDes='ASSERT_';}
		G::E($sDes);
	}

	static public function INT($Var,$sDes=null){
		if(is_int($Var)){return;}
		if(empty($sDes)){$sDes='INT';}
		G::E($sDes);
	}

	static public function NUM($Var,$sDes=null){
		if(is_numeric($Var)){return;}
		if(empty($sDes)){$sDes='NUM';}
		G::E($sDes);
	}

	static public function STRING($Var,$sDes=null){
		if(is_string($Var)){return;}
		if(empty($sDes)){$sDes='STRING';}
		G::E($sDes);
	}

	static public function OBJ($Var,$sDes=null){
		if(is_object($Var)){return;}
		if(empty($sDes)){$sDes='OBJ';}
		G::E($sDes);
	}

	static public function BOOL($Var,$sDes=null){
		if(is_bool($Var)){return;}
		if(empty($sDes)){$sDes='BOOL';}
		G::E($sDes);
	}

	static public function INSTANCE($Var,$Class='',$sDes=''){
		if(is_object($Var)){
			if($Class===''){return;}
			else if(class_exists($Class) AND G::isKindOf($Var,$Class)){return;}
			else if(interface_exists($Class) AND G::isImplementedTo($Var,$Class)){return;}
		}
		if(empty($sDes)){$sDes='INSTANCE';}
		G::E($sDes);
	}

	static public function DARRAY($Var,$eleTypes=null,$sDes=null){
		if(is_array($Var)){
			if($eleTypes===null){return;}
			if(!is_array($eleTypes)){$eleTypes =(array)$eleTypes;}
			if(G::checkArray($Var,$eleTypes)){return;}
		}
		if(empty($sDes)){$sDes='DARRAY';}
		G::E($sDes);
	}

	static public function DNULL($Var,$sDes=null){
		if($Var===null){return;}
		if(empty($sDes)){$sDes='DNULL';}
		G::E($sDes);
	}

	static public function NOTNULL($Var,$sDes=null){
		if($Var!==null){return;}
		if(empty($sDes)){$sDes='NOTNULL';}
		G::E($sDes);
	}

	static public function PATH($Var,$sDes=null){
		if(file_exists($Var)){return;}
		if(empty($sDes)){$sDes='PATH';}
		G::E($sDes);
	}

	static public function DFILE($Var,$sDes=null){
		if(is_file($Var)){return;}
		if(empty($sDes)){$sDes='DFILE';}
		G::E($sDes);
	}

	static public function DDIR($Var,$sDes=null){
		if(is_string($Var)&& is_dir($Var)){return;}
		if(empty($sDes)){$sDes='DDIR';}
		G::E($sDes);
	}

	static public function RESOURCE($Var,$sDes=null){
		if($Var and is_resource($Var)){return;}
		if(empty($sDes)){$sDes='RESOURCE';}
		G::E($sDes);
	}

	static public function CALLBACK($Var,$sDes=null){
		if($Var AND is_callable($Var,false)){return;}
		if(empty($sDes)){$sDes='CALLBACK';}
		G::E($sDes);
	}

	static public function INHERIT($SubClassName,$sParClass,$sDes=null){
		if(G::isKindOf($SubClassName,$sParClass)){return;}
		if(empty($sDes)){$sDes='INHERIT';}
		G::E($sDes);
	}

	static public function DIMPLEMENTS($SubClassName,$sInterfaceName,$bStrictly=true,$sDes=null){
		if(G::isImplementedTo($SubClassName,$sInterfaceName,$bStrictly)){return;}
		if(empty($sDes)){$sDes='DIMPLEMENTS';}
		G::E($sDes);
	}

	static public function ISTHESE($Var,$Types,$sDes=null){
		if(call_user_func_array(array('G','isThese'),array($Var,$Types))){return;}
		if(empty($sDes)){
			$sTypes=implode(', ',$Types);
			$sDes='ISTHESE';
		}
		G::E($sDes);
	}

	static public function ISCLASS($sClassName,$Base=array(),$sDes=null){
		self::STRING($sClassName);
		self::ISTHESE($Base,array('array:string','string','null'));
		if(!class_exists($sClassName)){
			if(empty($sDes)){$sDes='ISCLASS';}
			G::E($sDes);
		}
		if($Base!==null){
			// 检查基类和接口
			if(is_string($Base)){$arrBase=array($Base);}
			else{$arrBase=&$Base;}
			foreach($arrBase as $sClass){
				$bIsInterface=false;
				if(Package::classExists($sClass) AND ($bIsInterface=Package::classExists($sClass,true))){
					if(!G::isKindOf($sClassName,$sClass)){
						if(empty($sDes)){$sDes='ISCLASS';}
						G::E($sDes);
					}
				}
				else {// 错误的输入
					if(empty($sDes)){$sDes='ISCLASS';}
					G::E($sDes);
				}
			}
		}
	}

	static public function INVALUE($Var,array $arrValues,$sDes=null){
		if($sDes===NULL){$sDes='INVALUE';}
		self::ISTHESE($Var,array('string','int','bool'),$sDes);
		foreach($arrValues as &$Value){
			if($Var===$Value){return;}
		}
		G::E($sDes);
	}

	static public function INRANGE($Var,$Min,$Max,$sDes=null){
		if($sDes===NULL){$sDes='INRANGE';}
		self::ISTHESE($Var,array('bool','float'),$sDes);
		if($Var<$Min OR $Var>$Max){G::E($sDes);}
	}

}
