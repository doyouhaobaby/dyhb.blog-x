<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   this($)*/

!defined( 'DYHB_PATH' ) && exit;

class TemplateHtmlObj extends TemplateObj{

	protected $_sContent='';
	
	protected function replaceCompiled($nStart,$nLen,&$sNewContent){
		A::INT($nStart);
		A::INT($nLen);
		A::STRING($sNewContent);
		$sCompiled=$this->getCompiled();
		$sCompiled=substr_replace($sCompiled,$sNewContent,$nStart,$nLen);
		$this->setCompiled($sCompiled);
	}

	public function compile(){
		$arrChildTemplateObj=$this->_arrChildTemplateObj;
		while(!empty($arrChildTemplateObj)){
			$oChildTemplateObj=array_pop($arrChildTemplateObj);
			A::INSTANCE($oChildTemplateObj,'TemplateHtmlObj');
			$oChildTemplateObj->compile();// 编译子对象
			$nStart=$oChildTemplateObj->getStartByte()-$this->getStartByte();// 置换对象
			$nLen=$oChildTemplateObj->getEndByte()-$oChildTemplateObj->getStartByte()+1;
			$sCompiled=$oChildTemplateObj->getCompiled();
			$this->replaceCompiled($nStart,$nLen,$sCompiled);
		}
		TemplateObj::compile();// 编译自己
		
	}

	public function setContent($sContent){
		$this->_sContent=$sContent;
	}

	public function getContent(){
		return $this->_sContent;
	}

}
