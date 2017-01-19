<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Flash 图像查看类（flash地址位于DYHB_PATH.'/Resource/Images/image_view.swf'）($) */

!defined('DYHB_PATH') && exit;

class ImageView{

	protected $_oDOMDocment=null;
	protected $_oRoot=null;
	protected $_oAutoPlayTime=null;
	protected $_nAutoPlayTime=3;
	protected $_arrDatas=array();
	protected $_bEach=false;

	public function __construct($arrDatas=array(),$nAutoPlayTime=3,$bEach=false){
		$this->_oDOMDocment=new DOMDocument("1.0",'');// 创建一个新的DOMDocment 文件对象
		$this->_arrDatas=$arrDatas;// 初始化数据
		$this->_nAutoPlayTime=$nAutoPlayTime;
		$this->_bEach=$bEach;
		$this->createHeader();// 初始化操作
		$this->createRoot();
	}

	public function RUN(){
		$this->createAutoPlayTime();// 创建
		$this->createData();
		if($this->_bEach===true){$this->saveDoc();}
		else{return $this->saveDoc();}
	}

	public function createHeader(){
		header("Content-Type: text/plain");
	}

	public function createRoot(){
		$this->_oRoot=$this->_oDOMDocment->createElement("bcaster"); // 创建根节点
		$this->_oDOMDocment->appendChild($this->_oRoot);
	}

	public function createAutoPlayTime(){
		$this->_oAutoPlayTime=$this->_oDOMDocment->createAttribute("autoPlayTime");
		$this->_oRoot->appendChild($this->_oAutoPlayTime);
		$oValue=$this->_oDOMDocment->createTextNode($this->_nAutoPlayTime);
		$this->_oAutoPlayTime->appendChild($oValue);
	}

	public function createData(){
		foreach($this->_arrDatas as $arrData){// 将数据载入文档中
			$oTtem=$this->_oDOMDocment->createElement("item");// 创建根结点的子节点
			$this->_oRoot->appendChild($oTtem);
			$oItemName=$this->_oDOMDocment->createAttribute("item_url");// 创建路径节点属性为节点赋值
			$oTtem->appendChild($oItemName);
			$oItemValue=$this->_oDOMDocment->createTextNode($arrData['image']);
			$oItemName->appendChild($oItemValue);
			$oItemName=$this->_oDOMDocment->createAttribute("link");// 创建URL衔接
			$oTtem->appendChild($oItemName);
			$oItemValue=$this->_oDOMDocment->createTextNode($arrData['url']);
			$oItemName->appendChild($oItemValue);
			$oItemName=$this->_oDOMDocment->createAttribute("itemtitle");// 创建名字
			$oTtem->appendChild($oItemName);
			$oItemValue=$this->_oDOMDocment->createTextNode($arrData['title']);
			$oItemName->appendChild($oItemValue);
		}
	}

	public function saveDoc(){
		if($this->_bEach===true){echo $this->_oDOMDocment->saveXML();}
		else{return $this->_oDOMDocment->saveXML();}
	}

	public function setEach($bEach=true){
		$bOldValue=$this->_bEach;
		$this->_bEach=$bEach;
		return $bOldValue;
	}

	public function setDatas($arrDatas){
		$arrOldValue=$this->_arrDatas;
		$this->_arrDatas=$arrDatas;
		return $arrOldValue;
	}

	public function setAutoPlayTime($nAutoPlayTime){
		$nOldValue=$this->_nAutoPlayTime;
		$this->_nAutoPlayTime=$nAutoPlayTime;
		return $nOldValue;
	}

	public function getEach(){
		return $this->_bEach;
	}

	public function getDatas(){
		return $this->_arrDatas;
	}

	public function getAutoPlayTime(){
		return $this->_nAutoPlayTime;
	}

}
