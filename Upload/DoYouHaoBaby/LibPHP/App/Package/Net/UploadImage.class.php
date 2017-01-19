<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   图像文件上传($)*/

!defined('DYHB_PATH') && exit;

class UploadImage extends UploadFile{

	public $_arrAllowExts=array('gif','jpg','png','bmp','swf');
	static public $MAXWIDTH=1024;// 上传图片 宽度限制
	static public $MAXHEIGHT=800;// 上传图片 高度限制
	const ERR_NOTIMG=11;// 不是有效图像类型
	const ERR_EXWIDTH=12;// 图像宽度超过限制
	const ERR_EXHEIGHT=13;// 图像高度超过限制
	protected $_nMaxWidth;
	protected $_nMaxHeight;
	protected $_arrImageInfo;

	public function __construct($nMaxSize='',$AllowExts='',$AllowTypes='',$sSavePath='',$sSaveRule='',$nMaxWidth=null,$nMaxHeight=null){
		$this->_nMaxWidth=($nMaxWidth==null)?self::$MAXWIDTH: $nMaxWidth;
		$this->_nMaxHeight=($nMaxHeight==null)?self::$MAXHEIGHT: $nMaxHeight;
		parent::__construct($nMaxSize,$AllowExts,$AllowTypes,$sSavePath,$sSaveRule);
	}

	public function save($arrFile){
		$sFilename=$arrFile['upload_savepath'].'/'.$arrFile['upload_savename'];
		$this->_arrImageInfo[$arrFile['upload_name']]=getimagesize($this->getSavaPath($arrFile['upload_name']));// 获得 图像信息
		if($this->arrImageInfo[$arrFile['upload_name']]===false){
			$this->cancelUpload();
			$this->_sError=G::L('上传的文件不是有效的图片类型：“%s”','NetDyhb',null,$this->getOriginalName($arrFile['upload_name']));
			return false;
		}
		$nWidth=$this->getPhotoWidth();// 检查 图像宽度
		if($this->_nMaxWidth>0 and $this->_nMaxWidth<$nWidth){
			$this->cancelUpload();
			$this->_sError=G::L("图片宽度（%d px）超过限制（%d px）",'NetDyhb',null,$nWidth,$this->_nMaxWidth);
			return false;
		}
		$nHeight=$this->getPhotoHeight();// 检查 图像高度
		if($this->_nMaxHeight>0 and $this->_nMaxHeight<$nHeight){
			$this->cancelUpload();
			$this->_sError=G::L("图片高度（%d px）超过限制（%d px）",'NetDyhb',null,$nHeight,$this->_nMaxHeight);
			return false;
		}
		parent::save($arrFile);
		return true;
	}

	public function getPhotoWidth($sInputName=null){
		return $this->_arrImageInfo[($sInputName==null)?$this->_sLastInput:$sInputName][0];
	}

	public function getPhotoHeight($sInputName=null){
		return $this->_arrImageInfo[($sInputName==null)?$this->_sLastInput:$sInputName][1];
	}

	public function getPhotoType($sInputName=null){
		return $this->_arrImageInfo[($sInputName==null)?$this->_sLastInput:$sInputName][2];
	}

	public function setMaxSize($nMaxWidth=0,$nMaxHeight=0){
		$this->_nMaxWidth=$nMaxWidth;
		$this->_nMaxHeight=$nMaxHeight;
	}

	public function getMaxWidth(){
		return $this->_nMaxWidth;
	}

	public function getMaxHeight(){
		return $this->_nMaxHeight;
	}

}
