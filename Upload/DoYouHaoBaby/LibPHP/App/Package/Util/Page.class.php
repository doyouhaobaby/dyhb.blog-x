<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Page分页处理类($)*/

!defined('DYHB_PATH') && exit;

class Page{

	protected $_nCount;
	protected $_nSize;
	protected $_nPage;
	protected $_nPageStart;
	protected $_sParameter;
	protected $_nPageCount;
	protected $_sPageUrl;
	protected $_nPageI;
	protected $_nPageUb;
	protected $_nPageLimit;
	protected $_bPageSkip=false;
	protected $_bCurrentRecord=false;
	protected $_bDisableDisplay=false;
	protected static $_oDefaultDbIns=null;

	protected function __construct($nCount=0,$nSize=1,$nPage=1,$sPageUrl=''){
		$this->_nCount=$this->numeric($nCount); // 总记录数量
		$this->_nSize=$this->numeric($nSize); // 每页数量
		$this->_nPage=$this->numeric($nPage); // 当前page页码
		$this->_nPageLimit=($this->_nPage * $this->_nSize) - $this->_nSize;
		$this->_sPageUrl=$sPageUrl; // 页码URL，支持静态化衔接
		if($this->_nPage<1) $this->_nPage=1; // 页码如果小于1，则初始化为1，页码当然最小为1
		if($this->_nCount<0) $this->_nPage=0; // 如果记录为<0,那么这个时候也不存在页码了
		$this->_nPageCount=ceil($this->_nCount/$this->_nSize); // 取得所有页面数，ceil进一取整
		if($this->_nPageCount<1) $this->_nPageCount=1;
		if($this->_nPage>$this->_nPageCount) $this->_nPage=$this->_nPageCount; // 如果页面大于总记录数，那么这个记录直接取总记录数的大小
		$this->_nPageI=$this->_nPage-2;
		$this->_nPageUb=$this->_nPage+2;
		if($this->_nPageI<1){
			$this->_nPageUb=$this->_nPageUb+(1-$this->_nPageI);
			$this->_nPageI=1;
		}
		if($this->_nPageUb>$this->_nPageCount){
			$this->_nPageI=$this->_nPageI-($this->_nPageUb-$this->_nPageCount);
			$this->_nPageUb=$this->_nPageCount;
			if($this->_nPageI<1) $this->_nPageI=1;
		}
		$this->_nPageStart=($nPage-1)*$this->_nSize;
		if($this->_nPageStart<0) $this->_nPageStart=0;
	}

	public static function RUN($nCount=0,$nSize=1,$nPage=1,$sPageUrl='',$bDefaultIns=true){
		if($bDefaultIns and self::$_oDefaultDbIns){
			return self::$_oDefaultDbIns;
		}
		$oPage=new self($nCount,$nSize,$nPage,$sPageUrl);// 创建一个分页对象
		if($bDefaultIns){// 设置全局对象
			self::$_oDefaultDbIns=$oPage;
		}
		return $oPage;
	}

	public function P($Id='pagenav'){
		$sStr='';
		if($Id!==null && $Id!==false){$sStr='<div id="'.$Id.'" class="'.$Id.'">';}
		$sStr.='<span class="disabled">';// 总记录数量
		if($this->_nCount>0){// 显示记录总数
			$sStr.='Total:'.$this->_nCount;
		}
		else{
			$sStr.='No Record';
		}
		$sStr.="</span>";
		if($this->_nPageCount>1){// 页码
			if($this->_bCurrentRecord ){ // 显示当前记录信息
				$sStr.='<span class="disabled">'.$this->_nPage. '</span><span class="disabled">/</span><span class="disabled">'.$this->_nPageCount.'</span>';
			}
			$sStr.=$this->home();
			$sStr.=$this->prev();
			for($nI=$this->_nPageI;$nI <= $this->_nPageUb; $nI++){
				if($this->_nPage== $nI){
					$sStr.='<span class="current">'.$nI.'</span>';
				}
				else{
					$sStr.='<a href="'.$this->pageReplace($nI).'" title="'.sprintf('Page %d',$nI).'">';
					$sStr.=$nI."</a>";
				}
			}
			$sStr.=$this->next();
			$sStr.=$this->last();
			if($this->_bPageSkip){
				$sStr.='<input type="text" id="page_skip_id" size="2" value="'.$this->_nPage.'"';
				$sStr.='title="Enter the page number you want to reach" />';
				$sStr.=' <input type="button" class="button" size="4"';
				$sStr.='onclick="javascript:if(document.getElementById(\'page_skip_id\').value<='.$this->_nPageCount.'){location=\'';
				$sStr.=$this->pageReplace('\'+document.getElementById(\'page_skip_id\').value').';}else{ alert(\'Page number to jump over the total number of pages\'); }"';
				$sStr.=' value="Jump" />';
			}

		}
		if($Id!==null && $Id!==false){$sStr.='</div>';}
		return $sStr;
	}

	public function setDisableDisplay($bDisableDisplay=true){
		$bOldValue=$this->_bDisableDisplay;
		$this->bDisableDisplay=$bDisableDisplay;
		return $bOldValue;
	}

	public function setCurrentRecord($bCurrentRecord=true){
		$bOldValue=$this->_bCurrentRecord;
		$this->_bCurrentRecord=$bCurrentRecord;
		return $bOldValue;
	}

	public function setPageSkip($bPageSkip=true){
		$bOldValue=$this->_bPageSkip;
		$this->_bPageSkip=$bPageSkip;
		return $bOldValue;
	}

	public function setParameter($sParameter=''){
		$sOldValue=$this->_sParameter;
		$this->_sParameter=$sParameter;
		return $sOldValue;
	}

	public function returnCount(){
		return $this->_nCount;
	}

	public function returnSize(){
		return $this->_nSize;
	}

	public function returnPage(){
		return $this->_nPage;
	}

	public function returnPageStart(){
		return $this->_nPageStart;
	}

	public function returnPageCount(){
		return $this->_nPageCount;
	}

	public function returnPageI(){
		return $this->_nPageI;
	}

	public function returnPageUb(){
		return $this->_nPageUb;
	}

	public function returnPageLimit(){
		return $this->_nPageLimit;
	}

	protected function numeric($Id){
		if(strlen($Id)){
			if(!ereg("^[0-9]+$",$Id)){$Id=1;}
			else{$Id=substr($Id,0,11);}
		}
		else{
			$Id=1;
		}
		return $Id;
	}

	protected function pageReplace($nPage){
		$sPageUrl=$_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?') ? '&' : '?').$this->_sParameter;// 分析URL
		$arrParse=parse_url($sPageUrl);
		if(isset($arrParse['query'])){// 分析URL
			$arrParams=array();
			parse_str($arrParse['query'],$arrParams);
			unset($arrParams[ 'page' ]);
			$sPageUrl= $arrParse['path'].'?'.http_build_query($arrParams);
		}
		$sPageUrl=str_replace(array("%2F","%3D","%3F"),array('/','=','?'),$sPageUrl);
		$sPageUrl.=(substr($sPageUrl,-1,1)!='?'?'&':'').'page='.$nPage;
		if($this->_sPageUrl){
			$sPageUrl.='&page_html='.str_replace('{page}',$nPage,$this->_sPageUrl);
		}
		$sPageUrl=str_replace(array("%2F","%3D","%3F"),array('/','=','?'),$sPageUrl);
		return $sPageUrl;
	}

	protected function home(){
		if($this->_nPage!=1){// 页面不为第一页，加上超衔接
			return '<a href="'.$this->pageReplace(1).'"  title="Home" >&laquo; First</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">&laquo; First</span>';
			}
		}
	}

	protected function prev(){
		if($this->_nPage!=1){
			return '<a href="'.$this->pageReplace($this->_nPage-1).'"  title="Previous" >&#8249; Prev</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">&#8249; Prev</span>';
			}
		}
	}

	protected function next(){
		if($this->_nPage != $this->_nPageCount){
			return '<a href="'.$this->pageReplace($this->_nPage+1) .'"  title="Next" >Next &#8250;</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">Next &#8250;</span>';
			}
		}
	}

	protected function last(){
		if($this->_nPage!=$this->_nPageCount){
			return '<a href="'.$this->pageReplace($this->_nPageCount).'"  title="Last" >Last &raquo;</a>';
		}
		else{
			if($this->_bDisableDisplay){
				return '<span class="disabled">Next &raquo;</li>';
			}
		}
	}

}
