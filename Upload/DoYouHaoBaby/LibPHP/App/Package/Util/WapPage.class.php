<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   手机分页处理类($)*/

!defined('DYHB_PATH') && exit;

class WapPage extends Page{

	public static function RUN($nCount=0,$nSize=1,$nPage=1,$sPageUrl='',$bDefaultIns=true){
		if($bDefaultIns and self::$_oDefaultDbIns){
			return self::$_oDefaultDbIns;
		}
		$oPage=new self($nCount,$nSize,$nPage,$sPageUrl) ;
		if($bDefaultIns){
			self::$_oDefaultDbIns=$oPage;
		}
		return $oPage;
	}
	
	public function P($Id='pagenav'){
		$sStr='';
		if($Id!==null && $Id!==false){$sStr='<div id="'.$Id.'" class="'.$Id.'">';}
		$sStr='<span class="disabled">';// 总记录数量
		if($this->_nCount>0){// 显示记录总数
			$sStr.='T:'.$this->_nCount;
		}
		else{
			$sStr.='No';
		}
		$sStr.="</span>";
		if($this->_nPageCount>1){// 页码
			if($this->_bCurrentRecord){ // 显示当前记录信息
				$sStr.='<span class="disabled">'.$this->_nPage.'</span><span class="disabled">/</span><span class="disabled">'.$this->_nPageCount.'</span>';
			}
			$sStr.=$this->home();
			$sStr.=$this->prev();
			for($nI=$this->_nPageI;$nI<=$this->_nPageUb;$nI++){
				if($this->_nPage==$nI){
					$sStr.='<span class="current">'.$nI.'</span>';
				}
				else{
					$sStr.='<a href="'.$this->pageReplace($nI).'" title="'.sprintf('Page %d',$nI).'">';
					$sStr.=$nI."</a>";
				}
			}
			$sStr.=$this->next();
			$sStr.=$this->last();
		}
		if($Id!==null && $Id!==false){$sStr.='</div>';}
		return $sStr;
	}

	protected function home(){
		if($this->_nPage!=1){
			return '<a href="'.$this->pageReplace(1).'"  title="Home" >F</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">F</span>';
			}
		}
	}

	protected function prev(){
		if($this->_nPage!=1){
			return '<a href="'.$this->pageReplace($this->_nPage-1).'"  title="Previous" >P</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">P</span>';
			}
		}
	}

	protected function next(){
		if($this->_nPage!=$this->_nPageCount){
			return '<a href="'.$this->pageReplace($this->_nPage+1).'"  title="Next" >N</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">N</span>';
			}
		}
	}

	protected function last(){
		if($this->_nPage!=$this->_nPageCount){
			return '<a href="'.$this->pageReplace($this->_nPageCount).'"  title="Last" >L</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">N</li>';
			}
		}
	}

}
