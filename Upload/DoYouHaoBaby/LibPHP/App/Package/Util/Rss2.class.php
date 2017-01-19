<?php
/* [DoYouHaoBaby!] (C)Dianniu From 2010.
   Rss2 生成类($)*/

!defined('DYHB_PATH') && exit;

class Rss2 extends Rss{

	protected $_sAtomLink='';

	public function addItem2($sTitle,$sLink,$sComments,$sPubdate,$sCreator,$sCategory,$sGuid,$sDescription,$sContentEncoded,$sCommentRss,$nCommentNum){
	 	$this->_arrItems[]=array('title'=>$sTitle,'link'=>$sLink,'comments'=>$sComments,'pubdata'=>$sPubdate,'creator'=>$sCreator,'category'=>$sCategory,'guid'=>$sGuid,'description'=>$sDescription,'content_encoded'=>$sContentEncoded,'comment_rss'=>$sCommentRss,'comment_num'=>$nCommentNum);
	}

	public function rssHeader(){
		$sRss="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
		$sRss.="<rss version=\"2.0\"\r\n";
		$sRss.="xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\r\n";
		$sRss.="xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"\r\n";
		$sRss.="xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\r\n";
		$sRss.="xmlns:atom=\"http://www.w3.org/2005/Atom\"\r\n";
		$sRss.="xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"\r\n";
		$sRss.="xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\">\r\n";
		$sRss.="<channel>\r\n";
		$sRss.="<title><![CDATA[".$this->_sChannelTitle."]]></title>\r\n";
		$sRss.="<atom:link href=\"".$this->_sAtomLink."\" rel=\"self\" type=\"application/rss+xml\" />\r\n";
		$sRss.="<link>".$this->_sChannelLink."</link>\r\n";
		$sRss.="<description><![CDATA[".$this->_sChannelDescription."]]></description>\r\n";
		$sRss.="<sy:updatePeriod>hourly</sy:updatePeriod>\r\n";
		$sRss.="<sy:updateFrequency>1</sy:updateFrequency>\r\n";
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

	public function getRssBody(){
		$sRss='';
		foreach($this->_arrItems as $arrItem){
			$sRss.="<item>\r\n";
			$sRss.="<title><![CDATA[".$arrItem['title']."]]></title>\r\n";
			$sRss.="<link>".$arrItem['link']."</link>\r\n";
			$sRss.="<comments>".$arrItem['comments']."</comments>\r\n";
			$sRss.="<pubDate>".$arrItem['pubdata']."</pubDate>\r\n";
			$sRss.="<dc:creator>".$arrItem['creator']."</dc:creator>\r\n";
			$sRss.="<category><![CDATA[".$arrItem['category']."]]></category>\r\n";
			$sRss.="<guid isPermaLink=\"false\">".$arrItem['guid']."</guid>\r\n";
			$sRss.="<description><![CDATA[".$arrItem['description']."]]></description>\r\n";
			$sRss.="<content:encoded><![CDATA[".$arrItem['content_encoded']."]]></content:encoded>\r\n";
			$sRss.="<wfw:commentRss>".$arrItem['comment_rss']."</wfw:commentRss>\r\n";
			$sRss.="<slash:comments>".$arrItem['comment_num']."</slash:comments>\r\n";
			$sRss.="</item>\r\n";
		}
		return $sRss;
	}

}
