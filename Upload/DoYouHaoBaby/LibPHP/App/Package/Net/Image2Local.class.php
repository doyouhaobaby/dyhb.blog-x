<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   图片本地化实现类($)*/

!defined('DYHB_PATH') && exit;

class Image2Local {

	protected $_sOriginalContent;
	protected $_sBackContent;
	public $_sSavePath='';
	public $_bAutoCheck=true; // 是否自动检查附件
	public $_sSaveRule='';
	protected $_bAutoCreateStoreDir =false;
	protected $_sError='';
	protected $_bWriteSafeFile=true;
	protected $_nKey=0;
	protected $_bKeepOriginalName=false;
	public $_bAutoSub =false;
	public $_sSubType  ='hash';
	public $_sDateFormat='Ymd';
	public $_nHashLevel= 1; // hash的目录层次
	static public $MAXSIZE=204800;
	public $_nMaxSize=-1;
	public $_sHashType='md5_file';
	public $_bUploadReplace=false;
	public $_bThumb =false;
	public $_nThumbMaxWidth;
	public $_nThumbMaxHeight;
	public $_sThumbPrefix ='thumb_';
	public $_sThumbSuffix='';
	public $_sThumbPath='';
	public $_sThumbFile='';
	public $_bThumbRemoveOrigin=false;
	public $_bZipImages=false;
	protected $_arrUploadFileInfo;
	public $_bIsImagesWaterMark=false;
	const TEXT ='text';
	const IMG='img';
	public $_sImagesWaterMarkType=self::TEXT;
	public $_arrImagesWaterMarkImg=array('path'=>'','offset'=>'');
	public $_arrImagesWaterMarkText =array('content'=>'DoYouHaoBaby','textColor'=>'#000000','textFont'=>'','textFile'=>'','offset'=>'');
	public $_nWaterPos=0;

	public function __construct($sOriginalContent='',$nMaxSize=-1,$sSavePath='',$sSaveRule=''){
		if(!empty($nMaxSize)&& is_numeric($nMaxSize)){
			$this->_nMaxSize=$nMaxSize;
		}
		if(!empty($sSaveRule))$this->_sSaveRule=$sSaveRule;
		else {$this->_sSaveRule=$GLOBALS['_commonConfig_']['UPLOAD_FILE_RULE'];}
		$this->_sSavePath=$sSavePath;
		$this->_sOriginalContent=$sOriginalContent;
	}

	public function local($sContent='',$sSavePath=''){
		set_time_limit(0);// 数据处理量大的时候防止超时连接
		if(empty($sSavePath)){$sSavePath=$this->_sSavePath;}// 如果不指定保存文件名，则由系统默认
		if(empty($sContent)){$sContent=$this->_sOriginalContent;}
		if(!is_dir($sSavePath)){// 检查上传目录
			if(is_dir(base64_decode($sSavePath))){// 检查目录是否编码后的
				$sSavePath=base64_decode($sSavePath);
			}
			else{// 尝试创建目录
				if(!$this->_bAutoCreateStoreDir){
					$this->_sError(G::L("存储目录不存在：“%s”",'NetDyhb',null,$sSavePath));
					return false;
				}
				else if(!mkdir($sSavePath)){
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
		if($this->_bWriteSafeFile){
			$this->writeSafeFile($sSavePath);// 写入目录安全文件
		}
		$this->_sSavePath=$sSavePath;
		$sContent=G::stripslashes($sContent);
		$this->_sBackContent=preg_replace_callback("/<img([^>]+)src=\"([^>\"]+)\"?([^>]*)>/i",array($this,'local2'),$sContent);
		return $this->_sBackContent;
	}

	protected function save($arrFile,$sImgData){
		$sFilename=$arrFile['savepath'].'/'.$arrFile['savename'];
		if(!$this->_bUploadReplace && is_file($sFilename)){// 不覆盖同名文件
			$this->_sError=G::L('文件%s已经存在！','NetDyhb',null,$sFilename);
			return false;
		}
		if($hFp=@fopen($sFilename,'w')){// 保存文件
			fwrite($hFp,$sImgData);
		}
		else{
			$this->_sError=G::L('写入文件%s失败！','NetDyhb',null,$sFilename);
			return false;
		}
		@fclose($hFp);
		if($this->_bIsImagesWaterMark){// 创建水印
			$this->imageWaterMark($sFilename);
		}
		if($this->_bThumb){
			$arrImage= getimagesize($sFilename);
			if(false !== $arrImage){//是图像文件生成缩略图
				$arrThumbWidth=explode(',',$this->_nThumbMaxWidth);
				$arrThumbHeight=explode(',',$this->_nThumbMaxHeight);
				$arrThumbPrefix=explode(',',$this->_sThumbPrefix);
				$arrThumbSuffix=explode(',',$this->_sThumbSuffix);
				$arrThumbFile=explode(',',$this->_sThumbFile);
				$sThumbPath=$this->_sThumbPath?$this->_sThumbPath:$arrFile['savepath'];
				if(!is_dir($sThumbPath)){// 检查缩略图目录
					if(is_dir(base64_decode($sThumbPath))){// 检查目录是否编码后的
						$sThumbPath=base64_decode($sThumbPath);
					}
					else{// 尝试创建目录
						if(!$this->_bAutoCreateStoreDir){
							$this->_sError(G::L("存储目录不存在：“%s”",'NetDyhb',null,$sThumbPath));
							return false;
						}
						else if(!mkdir($sThumbPath)){
							$this->_sError=G::L('上传目录%s不可写','NetDyhb',null,$sThumbPath);
							return false;
						}
					}
					if($this->_bWriteSafeFile){
						$this->writeSafeFile($sThumbPath);// 写入目录安全文件
					}
				}
				$sRealFilename=$this->_bAutoSub?basename($arrFile['savename']):$arrFile['savename'];// 生成图像缩略图
				for($nI=0,$nLen=count($arrThumbWidth);$nI<$nLen;$nI++){
					$sThumbname=$sThumbPath.'/'.$arrThumbPrefix[$nI].substr($sRealFilename,0,strrpos($sRealFilename,'.')).$arrThumbSuffix[$nI].'.'.$arrFile['extension'];
					Image::thumb($sFilename,$sThumbname,'',$arrThumbWidth[$nI],$arrThumbHeight[$nI],true);
				}
				if($this->_bThumbRemoveOrigin){// 生成缩略图之后删除原图
					unlink($sFilename);
				}
			}
		}
		if($this->_bZipImages){}// 对图片压缩包在线解压
		return true;
	}

	protected function local2($arrMatches){
		$arrUrl=parse_url($arrMatches[2]);
		$sHost=$_SERVER['HTTP_HOST'];
		if(isset($arrUrl['host']) && $arrUrl['host']!=$sHost){
			$sPath=$arrUrl['path'];
			if(!empty($arrUrl['query'])){
				$sPath.='?'.$arrUrl['query'];
			}
			$sHttpRequest="GET {$sPath} HTTP/1.0\r\n";
			$sHttpRequest.="ACCEPT: */*\r\n";
			$sHttpRequest.="ACCEPT-LANGUAGE: zh-cn\r\n";
			$sHttpRequest.="USER_AGENT: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
			$sHttpRequest.="HOST: ".$arrUrl['host']."\r\n";
			$sHttpRequest.="CONNECTION: close\r\n";
			$sHttpRequest.="COOKIE: {$_COOKIE}\r\n\r\n";
			$sResponse='';
			if(FALSE!=($hFs=@fsockopen($arrUrl['host'],empty($arrUrl['port'])?80:$arrUrl['port'],$nErrNum,$sErrStr,10))){
				fwrite($hFs,$sHttpRequest);
				while(!feof($hFs)){
					$sResponse.=fgets($hFs,1160);
				}
				@fclose($hFs);
				$arrResponse=explode("\r\n\r\n",$sResponse,2);// 处理扩展信息
				unset($sResponse);
				$sImgData=$arrResponse[1];// 文件数据
				preg_match("/Content-Type: (.*)/i",$arrResponse[0],$arrImgType);// 取得文件类型
				$sImgType=$arrImgType[1];
				unset($arrImgType);
				preg_match("/Content-Length: (.*)\r\n/i",$arrResponse[0],$arrImgLength);// 取得文件长度
				$nImgLength=$arrImgLength[1];
				unset($arrImgLength);
				$sImgExt=strtolower(substr(strrchr($arrMatches[2],"."),1));// 处理图片扩展信息
				if(empty($sImgExt)||!in_array($sImgExt,array('jpeg','jpg','png','gif','bmp','jpg'))){
					if($sImgType=='image/pjpeg'){$sImgExt='jpeg';}
					elseif($sImgType=='image/jpeg'){$sImgExt='jpg';}
					elseif($sImgType=='image/x-png' || $sImgType=='image/png'){$sImgExt='png';}
					elseif($sImgType=='image/gif'){$sImgExt='gif';}
					elseif($sImgType=='image/bmp'){$sImgExt='bmp';}
					else{$sImgExt='jpg';}
					$sImgName='image2local_'.gmdate('YmdHis').'.'.$sImgExt;
				}
				else{
					$sImgName=basename($arrMatches[2]);// 文件名字
				}
				$arrFile =array();// 写入远程文件信息
				$arrFile["name"]=$sImgName;
				$arrFile["type"]=$sImgType;
				$arrFile["size"]=$nImgLength;
				$arrFile['key']=$this->_nKey;// 登记远程文件的扩展信息
				$arrFile['extension']=$sImgExt;
				$arrFile['savepath']=$this->_sSavePath;
				$arrFile['savename']=$this->getSaveName($arrFile);
				$arrFile['isthumb']=$this->_bThumb?1:0;
				$arrFile['thumbprefix']=$this->_sThumbPrefix;
				$arrFile['thumbpath']=$this->_sThumbPath;
				$arrFile['module']=MODULE_NAME;
				if($this->_bAutoCheck){// 自动检查附件
					if(!$this->check($arrFile)){return "<img{$arrMatches[1]}src=\"{$arrMatches[2]}\"{$arrMatches[3]}>";}// 验证错误直接返回原图
				}
				if(!$this->save($arrFile,$sImgData)){// 保存远程文件
					return "<img{$arrMatches[1]}src=\"{$arrMatches[2]}\"{$arrMatches[3]}>";
				}
				if(function_exists($this->_sHashType)){
					$sFun= $this->_sHashType;
					$arrFile['hash'] =$sFun(G::gbkToUtf8($arrFile['savepath'].'/'.$arrFile['savename'],'utf-8','gb2312'));
				}
				$this->_nKey++;
				$this->_arrUploadFileInfo[]=$arrFile;// 保存文件的信息
				return "[upload]".$arrFile['savename']."[/upload]";
			}
			else{
				return "<img{$arrMatches[1]}src=\"{$arrMatches[2]}\"{$arrMatches[3]}>";
			}
		}
		else{
			return "<img{$arrMatches[1]}src=\"{$arrMatches[2]}\"{$arrMatches[3]}>";
		}
	}

	protected function imageWaterMark($sFilename){
		if($this->_sImagesWaterMarkType==self::IMG && !empty($this->_arrImagesWaterMarkImg['path'])){// 图片水印
			$arrWaterArgs=&$this->_arrImagesWaterMarkImg;
			$arrWaterArgs['type']=self::IMG;
			$bResult=Image::imageWaterMark($sFilename,$this->_nWaterPos,$arrWaterArgs,false);
			if($bResult!==true){
				$this->_sError= $bResult;
				return false;
			}
		}
		elseif($this->_sImagesWaterMarkType==self::TEXT){// 文字水印
			$arrWaterArgs=&$this->_arrImagesWaterMarkText;
			$arrWaterArgs['type']=self::TEXT;
			$bResult=Image::imageWaterMark($sFilename,$this->_nWaterPos,$arrWaterArgs,false);
			if($bResult!==true){
				$this->_sError=$bResult;
				return false;
			}
		}
	}

	protected function getSaveName($arrFile){
		$sRule=$this->_sSaveRule;
		if($this->_bKeepOriginalName || empty($sRule)){// 没有定义命名规则，则保持文件名不变
			$sSaveName=$arrFile['name'];
		}
		else{
			if(function_exists($sRule)){// 使用函数生成一个唯一文件标识号
				$sSaveName=$sRule().".".$arrFile['extension'];
			}
			else {// 使用给定的文件名作为标识号
				$sSaveName=$sRule.'-'.md5($arrFile['name']).".".$arrFile['extension'];
			}
		}
		if($this->_bAutoSub){// 使用子目录保存文件
			$sSaveName =$this->getSubName($arrFile).'/'.$sSaveName;
		}
		return $sSaveName;
	}

	protected function writeSafeFile($sFileStoreDir){
		if(!file_exists($sFileStoreDir.'/index.php')){file_put_contents($sFileStoreDir.'/index.php',"<?php\necho '<font color=\"red\">Error! Do not allowed to browse here !</div><br/>';\necho '<font color=\"gray\">By DoYouHaoBaby Framework</div>';\n");}
		if(!file_exists($sFileStoreDir.'/index.html')){file_put_contents($sFileStoreDir.'/index.html',"<font color=\"red\">Error! Do not allowed to browse here !</font><br/>\n<font color=\"gray\">By DoYouHaoBaby Framework</font>");}
	}

	protected function getSubName($arrFile){
		switch($this->_sSubType){
			case 'date':
				$sDir=date($this->_sDateFormat,time());
				break;
			case 'hash':
			default:
				$sName=md5($arrFile['savename']);
				$sDir='';
				for($nI=0;$nI<$this->_nHashLevel;$nI++)
					$sDir.= $sName{0}.'/';
				break;
		}
		if(!is_dir($arrFile['savepath'].'/'.$sDir)){
			G::makeDir($arrFile['savepath'].'/'.$sDir);
		}
		return $sDir;
	}

	protected function check($arrFile){
		if(!$this->checkSize($arrFile['size'])){// 文件上传成功，进行自定义规则检查&检查文件大小
			return false;
		}
		return true;
	}

	protected function checkSize($nSize){
		return !($nSize > $this->_nMaxSize) || (-1==$this->_nMaxSize);
	}

	public function getUploadFileInfo(){
		return $this->_arrUploadFileInfo;
	}

	public function getErrorMessage(){
		return $this->_sError;
	}

	public function isError(){
		return !empty($this->_sError);
	}

	public function getBackContent(){
		return $this->_sBackContent;
	}

	public function getOriginalContent(){
		return $this->_sOriginalContent;
	}

	public function getCounts(){
		return count($this->_arrUploadFileInfo);
	}

	public function setAutoCreateStoreDir($bAutoCreateStoreDir=true){
		$bOldValue=$this->_bAutoCreateStoreDir;
		$this->_bAutoCreateStoreDir=$bAutoCreateStoreDir;
		return $bOldValue;
	}

	public function setWriteSafeFile($bWriteSafeFile=false){
		$bOldValue=$this->_bWriteSafeFile;
		$this->_bWriteSafeFile=$bWriteSafeFile;
		return $bOldValue;
	}

	public function setKeepOriginalName($bKeepOriginalName=true,$bUploadReplace=false){
		$this->_bKeepOriginalName=$bKeepOriginalName;
		$this->_bUploadReplace=$bUploadReplace;
	}

	public function setUploadReplace($bUploadReplace=false){
		$bOldValue=$this->_bUploadReplace;
		$this->_bUploadReplace=$bUploadReplace;
		return $bOldValue;
	}

	public function setMaxSize($nMaxSize=null){
		$nOldValue=$this->_nMaxSize;
		if($nMaxSize==null){
			$nMaxSize=self::$MAXSIZE;
		}
		$this->_nMaxSize=$nMaxSize;
		return $nOldValue;
	}

	public function setBackContent($sBackContent=''){
		$sOldValue=$this->_sBackContent;
		$this->_sBackContent=$sBackContent;
		return $sOldValue;
	}

	public function setOriginalContent($sOriginalContent=''){
		$sOldValue=$this->_sOriginalContent;
		$this->_sOriginalContent=$sOriginalContent;
		return $sOriginalContent;
	}

	static function getReadableFileSize($nByte,$nPrecision=2){
		$unit='Byte';
		if($nByte>=1024){
			$nByte/= 1024;
			$unit='KB';
		}
		if($nByte>=1048576){
			$nByte/= 1024;
			$unit='MB';
		}
		if($nByte>=1073741824){
			$nByte/= 1024;
			$unit='GB';
		}
		return round($nByte,$nPrecision).' '.$unit;
	}

}
