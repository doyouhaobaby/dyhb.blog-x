<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Ssmi API接口控制器($) */

!defined('DYHB_PATH') && exit;

class SsmiController extends Controller{

	public function __construct(){
		parent::__construct(__CLASS__);
	}

	public function index(){
		if(!isset($_REQUEST['param'])){
			$arrParam=array();
			foreach($_REQUEST as $sKey=>$sVal){
				if(preg_match('/^param_(.+)/i',$sKey,$arrRes)){$arrParam[$arrRes[1]]=$sVal;}
			}
		}
		else{$arrParam =array($_REQUEST['param']);}
		call_user_func_array(array($_REQUEST['class'],$_REQUEST['method']),$arrParam);
	}

}
