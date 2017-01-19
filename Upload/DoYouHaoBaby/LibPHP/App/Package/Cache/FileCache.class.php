<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   使用硬盘文件进行缓存($) */

!defined('DYHB_PATH') && exit;

class FileCache extends DCache{

	protected $_arrOptions=array(
		'serialize'=>true,
		'encoding_filename'=>true,
		'test_validity'=>true,
		'test_method'=>'crc32',
	);
	static protected $_sStaticHead='<?php die();?>';
	static protected $_nStaticHeadLen=15;
	static protected $_nHeadLen=64;

	public function __construct($arrOptions=array()){
		$this->_nCacheTime=isset($arrOptions['cache_time'])?(int)$arrOptions['cache_time']:86400;
		unset($arrOptions['cache_time']);
		$this->_arrOptions=$this->backOptions($arrOptions);
		if(!empty($this->_arrOptions['cache_path'])){$this->_arrOptions['cache_path']=$arrOptions['cache_path'];}// 再次对默认数据进行验证
		else{$this->_arrOptions['cache_path']=APP_RUNTIME_PATH.'/Data';}
		$this->_arrOptions['cache_path']=rtrim($this->_arrOptions['cache_path'],'/\\');
		$this->checkCachePath();
		$this->_bConnected=is_dir($this->_arrOptions['cache_path']) && is_writeable($this->_arrOptions['cache_path']);
		$this->_sCacheType=self::FILE_CACHE;
	}

	private function isConnected(){
		return $this->_bConnected;
	}

	public function checkCache($sCacheName,$nTime=-1){
		$sFilePath=$this->getCacheFilePath($sCacheName);
		if(!is_file($sFilePath)) return true;
 		return(filemtime($sFilePath)+$nTime< CURRENT_TIMESTAMP && $nTime!==-1);
	}

	public function getCache($sCacheName){
		if(!$this->_bCacheEnabled) return;
		$this->Q(1);
		$arrOptions=&$this->_arrOptions;
		$arrOptions['cache_time']=$this->_nCacheTime;
		$sCacheFilePath=$this->getCacheFilePath($sCacheName);
		clearstatcache();
		if(!file_exists($sCacheFilePath)){ return false; }
		$hFp=fopen($sCacheFilePath,'rb');// 读取文件头部
		if(!$hFp){ return false; }
		flock($hFp,LOCK_SH);
		$nLen=filesize($sCacheFilePath);
		$bMqr=get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		$sHead=fread($hFp,self::$_nHeadLen);// 头部的 32 个字节存储了该缓存的配置
		$sHead=substr($sHead,self::$_nStaticHeadLen);
		$nLen-=self::$_nHeadLen;
		$arrTmp=unpack('Ic/Ss/St',substr($sHead,0,8));
		$arrOptions['cache_time']=$arrTmp['c'];
		$arrOptions['serialize']=$arrTmp['s'];
		$arrOptions['test_validity']=$arrTmp['t'];
		$arrOptions['test_method']=trim(substr($sHead,8,8));
		do{
			if($this->checkCache($sCacheName,$this->_nCacheTime)){// 检查缓存是否已经过期
				$EncryptTest=null;
				$Data=false;
				break;
			}
			if($arrOptions['test_validity']){// 检查缓存数据的完整性
				$EncryptTest=fread($hFp,32);
				$nLen-=32;
			}
			if($nLen>0){$Data=fread($hFp,$nLen);}
			else{$Data=false;}
			set_magic_quotes_runtime($bMqr);

		}while(false);
		flock($hFp,LOCK_UN);
		fclose($hFp);
		if($Data===false){return false;}
		if($arrOptions['test_validity']){
			$Encrypt=$this->encryptData($Data,$arrOptions['test_method']);
			if($Encrypt!=$EncryptTest){
				if($arrOptions['cache_time']==-1){unlink($sCacheFilePath);}// 如果是永不过期的缓存文件没通过验证，则直接删除
				else{touch($sCacheFilePath,CURRENT_TIMESTAMP-2*abs($arrOptions['cache_time']));}// 否则设置文件时间为已经过期}
				return false;
			}
		}
		if($arrOptions['serialize']){$Data=unserialize($Data);}// 解码
		return $Data;
	}

	public function setCache($sCacheName,$Data){
		if(!$this->_bCacheEnabled) return;
		$this->W(1);
		if($this->_arrOptions['serialize']){$Data=serialize($Data);}
		$sCacheFilePath=$this->getCacheFilePath($sCacheName);
		$arrOptions=$this->_arrOptions;
		$arrOptions['cache_time']=$this->_nCacheTime;
		$sHead=self::$_sStaticHead;// 构造缓存文件头部
		$sHead.=pack('ISS',$arrOptions['cache_time'],$arrOptions['serialize'],$arrOptions['test_validity']);
		$sHead.=sprintf('% 8s',$arrOptions['test_method']);
		$sHead.=str_repeat(' ',self::$_nHeadLen-strlen($sHead));
		$sContent=$sHead;
		if($arrOptions['test_validity']){
			$sContent .=$this->encryptData($Data,$arrOptions['test_method']);// 接下来的 32 个字节写入用于验证数据完整性的验证码
		}
		$sContent.=$Data;
		unset($Data);
		$this->writeData($sCacheFilePath,$sContent);
	}

	public function deleleCache($sCacheName){
		if(!$this->_bCacheEnabled) return;
		$sCacheFilePath=$this->getCacheFilePath($sCacheName);
		if($this->existCache($sCacheName)){
			unlink($sCacheFilePath);
		}
	}

	public function existCache($sCacheName){
		if(!$this->_bCacheEnabled) return;
		$sCacheFilePath=$this->getCacheFilePath($sCacheName);
		return is_file($sCacheFilePath);
	}

	public function clearCache(){
		$sCachePath=$this->_arrOptions['cache_path'];
		if($hDir=opendir($sCachePath)){
			while($sFile=readdir($hDir)){
				$bCheck=is_dir($sFile);
				if(!$bCheck){unlink($sCachePath.'/'.$sFile);}
			}
			closedir($hDir);
			return true;
		}
	}

	private function getCacheFilePath($sCacheName){
		if($this->_arrOptions['encoding_filename']){// 编码文件名防止出现非法字符而无法创建文件
			$sCacheName=$this->encryptKey($sCacheName);
		}
		return $this->_arrOptions['cache_path'].'/'.$this->_sPrefix.$sCacheName.'.php';
	}

	 public function writeData($sFileName,$sData){
		!is_dir(dirname($sFileName)) && G::makeDir(dirname($sFileName));
		return file_put_contents($sFileName,$sData,LOCK_EX);
	}

	protected function encryptData($sData,$sType){
		switch($sType){
			case 'md5':
				return md5($sData);
			case 'crc32':
				return sprintf('% 32d',crc32($sData));
			default:
				return sprintf('% 32d',strlen($sData));
		}
	}

}
