<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   文件上传 for jquery uploadify flash 文件上传($)*/

!defined('DYHB_PATH') && exit;

class UploadFileForUploadify extends UploadFile{

	protected $_sUploadifyDataName='Filedata';

	public function upload($sSavePath=''){
		if(empty($sSavePath)){$sSavePath=$this->_sSavePath;}// 如果不指定保存文件名，则由系统默认
		if(!is_dir($sSavePath)){// 检查上传目录
			if(is_dir(base64_decode($sSavePath))){// 检查目录是否编码后的
				$sSavePath=base64_decode($sSavePath);
			}
			else{// 尝试创建目录
				if(!$this->_bAutoCreateStoreDir){
					$this->_sError(G::L("存储目录不存在：“%s”",'NetDyhb',null,$sSavePath));
					return false;
				}
				else if(!G::makeDir($sSavePath)){
					$this->_sError=G::L('上传目录%s不可写','NetDyhb',null,$sSavePath);
					return false;
				}
			}
		}
		else{
			if(!is_writeable($sSavePath)){
				$this->_sError=G::L('上传目录%s不可写','NetDyhb',null,$sSavePath);
				return false;
			}
		}
		$arrFileInfo=array();
		$bIsUpload=false;
		$arrFile=$_FILES[$this->_sUploadifyDataName];// 获取上传的文件信息&对$_FILES数组信息处理
		if(!empty($arrFile['name'])){// 过滤无效的上传
			$this->_sLastInput=$arrFile['name'];
			$arrFile['key']=0;// 登记上传文件的扩展信息
			$arrFile['extension']=strtolower($this->getExt($arrFile['name']));
			$arrFile['savepath']=$sSavePath;
			$arrFile['savename']=$this->getSaveName($arrFile);
			$arrFile['isthumb']=$this->_bThumb?1:0;
			$arrFile['thumbprefix']=$this->_sThumbPrefix;
			$arrFile['thumbpath']=$this->_sThumbPath;
			$arrFile['module']=MODULE_NAME;
			if($this->_bAutoCheck){// 自动检查附件
				if(!$this->check($arrFile)){return false;}
			}
			if(!$this->save($arrFile))return false;// 保存上传文件
			if(function_exists($this->_sHashType)){
				$sFun=$this->_sHashType;
				$arrFile['hash']=$sFun(G::gbkToUtf8($arrFile['savepath'].'/'.$arrFile['savename'],'utf-8','gb2312'));
			}
			unset($arrFile['tmp_name'],$arrFile['error']);// 上传成功后保存文件信息，供其他地方调用
			$this->_arrLastFileinfo=$arrFile;
			$arrFileInfo[]=$arrFile;
			$bIsUpload =true;
		}
		if($bIsUpload){
			$this->_arrUploadFileInfo=$arrFileInfo;
			return true;
		}
		else{
			$this->_sError=G::L('文件只有部分被上传','NetDyhb');
			return false;
		}
	}

	public function setUploadifyDataName($sUploadifyDataName='Filedata'){
		$sOldValue=$this->_sUploadifyDataName;
		$this->_sUploadifyDataName=$sUploadifyDataName;
		return $sOldValue;
	}

	public function getUploadifyDataName(){
		return $this->_sUploadifyDataName;
	}

}
