<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Rss 生成类($)*/

!defined('DYHB_PATH') && exit;

class Rss{

	protected $_sChannelTitle='';
	protected $_sChannelLink='';
	protected $_sChannelDescription='';
	protected $_sLanguage='zh_CN';
	protected $_sPubDate='';
	protected $_sLastBuildDate='';
	protected $_sGenerator='';
	protected $_sChannelImgurl='';
	protected $_arrItems=array();

	public function __construct($sTitle,$sLink,$sDescription,$sImgUrl=''){
		$this->_sChannelTitle=$sTitle;
		$this->_sChannelLink=$sLink;
		$this->_sChannelDescription=$sDescription;
		$this->_sChannelImgurl=$sImgUrl;
		$this->_sPubDate=date('Y-m-d H:i:s',CURRENT_TIMESTAMP);
		$this->_sLastBuildDate=date('Y-m-d H:i:s',CURRENT_TIMESTAMP);
	}

	public function &__get($sKey){
		return $this->{$sKey};
	}

	public function __set($sKey,$sValue){
		return $this->setOption($sKey,$sValue);
	}

	public function setOption($sKey,$sValue){
	 	$oldValue=$this->{$sKey};
	 	$this->{$sKey}=$sValue;
	 	return $oldValue;
	}

	public function getOption($sKey,$sValue){
	 	return $this->{$sKey};
	}

	public function addItem($sTitle,$sLink,$sDescription){
	 	$this->_arrItems[]=array('title'=>$sTitle,'link'=>$sLink,'description'=>$sDescription);
	}

	public function rssHeader(){
		$sRss="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		$sRss.="<rss version=\"0.92\">\r\n";
		$sRss.="<channel>\r\n";
		$sRss.="<title><![CDATA[".$this->_sChannelTitle."]]></title>\r\n";
		$sRss.="<link>".$this->_sChannelLink."</link>\r\n";
		$sRss.="<description><![CDATA[".$this->_sChannelDescription."]]></description>\r\n";
		$sRss.="<docs>http://backend.userland.com/rss092</docs>\r\n";
		$sRss.="<language>".$this->_sLanguage."</language>\r\n";
		if(!empty($this->_sPubDate)){$sRss.="<pubDate>".$this->_sPubDate."</pubDate>\r\n";}
		if(!empty($this->_sLastBuildDate)){$sRss.="<lastBuildDate>".$this->_sLastBuildDate."</lastBuildDate>\r\n";}
		if(!empty($this->_sGenerator)){$sRss.="<generator>".$this->_sGenerator."</generator>\r\n";}
		if(!empty($this->_sChannelImgurl)){
			$sRss.="<image>\r\n";
			$sRss.="<title><![CDATA[".$this->_sChannelTitle."]]></title>\r\n";
			$sRss.="<link>".$this->_sChannelLink."</link>\r\n";
			$sRss.="<url>".$this->_sChannelImgurl."</url>\r\n";
			$sRss.="</image>\r\n";
		}
		return $sRss;
	}

	public function rssFooter(){
		return "</channel>\r\n</rss>";
	}

	public function fetch(){
		$sRss=$this->rssHeader();
		$sRss.=$this->getRssBody();
		$sRss.=$this->rssFooter();
		return $sRss;
	}

	public function getRssBody(){
		$sRss='';
		foreach($this->_arrItems as $arrItem){
			$sRss.="<item>\r\n";
			$sRss.="<title><![CDATA[".$arrItem['title']."]]></title>\r\n";
			$sRss.="<description><![CDATA[".$arrItem['description']."]]></description>\r\n";
			$sRss.="<link>".$arrItem['link']."</link>\r\n";
			$sRss.="</item>\r\n";
		}
		return $sRss;
	}

	public function display($sRssBody=''){
		header("Content-Type: text/xml; charset=utf-8");
		if(empty($sRssBody)){echo $this->fetch();}
		else{
			echo $this->rssHeader();
			echo $sRssBody;
			echo $this->rssFooter();
		}
		exit;
	}
}
