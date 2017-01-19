<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	主题模型($)*/

!defined('DYHB_PATH') && exit;

class ThemeModel{

	public $_arrOkThemes=array();
	public $_arrBrokenThemes=array();

	public function getThemeData($sThemePath){
		$arrDefaultHeaders=array(
			'Name'=>'Theme Name',
			'URI'=>'Theme URI',
			'Description'=>'Description',
			'Author'=>'Author',
			'AuthorURI'=>'Author URI',
			'Version'=>'Version',
			'Template'=>'Template',
			'Status'=>'Status',
			'Tags'=>'Tags'
		);
		$arrThemeData=File_Extend::getFileData($sThemePath,$arrDefaultHeaders);
		$arrTestTemp=Dyhb::normalize($arrThemeData);
		if(empty($arrTestTemp)){
			unset($arrTestTemp);
			return false;
		}
		$arrThemeData['Name']=$arrThemeData['Title']=strip_tags($arrThemeData['Name']);
		$arrThemeData['URI']=Safe::escUrl($arrThemeData['URI']);
		$arrThemeData['Description']=$arrThemeData['Description'];
		$arrThemeData['AuthorURI']=Safe::escUrl($arrThemeData['AuthorURI']);
		$arrThemeData['Template']=$arrThemeData['Template'];
		$arrThemeData['Version']=$arrThemeData['Version'];

		if($arrThemeData['Status']==''){
			$arrThemeData['Status']='publish';
		}
		else{
			$arrThemeData['Status']=$arrThemeData['Status'];
		}

		if($arrThemeData['Tags']==''){
			$arrThemeData['Tags']=array();
		}
		else{
			$arrThemeData['Tags']=array_map('trim',explode(',',$arrThemeData['Tags']));
		}

		if($arrThemeData['Author']==''){
			$arrThemeData['Author']=$arrThemeData['AuthorName']='NoBoby';
		}
		else{
			$arrThemeData['AuthorName']=$arrThemeData['Author'];
			if(empty($arrThemeData['AuthorURI'])){
				$arrThemeData['Author']=$arrThemeData['AuthorName'];
			}
			else{
				$arrThemeData['Author']=sprintf('<a href="%1$s" title="%2$s">%3$s</a>',$arrThemeData['AuthorURI'],G::L('访问作者的主页'),$arrThemeData['AuthorName']);
			}
		}
		return $arrThemeData;
	}

	public function getThemes($arrCurrentThemes){
		$arrOkThemes=array();
		$arrBrokenThemes=array();
		foreach((array)$arrCurrentThemes as $sTheme){
			$sThemePath=file_exists($sTheme.'/Public/Css/style.css')?$sTheme.'/Public/Css/style.css':$sTheme.'/Public/Css/style_append.css';
			$arrTemps=Dyhb::normalize(G::tidyPath($sTheme),'/');
			$sTemplateDir=end($arrTemps);
			unset($arrTemps);
			if(!file_exists($sThemePath)){
				$arrBrokenThemes[]=array('Name'=>$sTemplateDir,'Path'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sThemePath)),'Description'=>G::L('主题样式表丢失'));
				continue;
			}

			if(!is_readable($sThemePath)){
				$arrBrokenThemes[]=array('Name'=>$sTemplateDir,'Path'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sThemePath)),'Description'=>G::L('主题样式表不可读'));
				continue;
			}
			$arrThemeData=$this->getThemeData($sThemePath);
			if($arrThemeData===false){
				$arrBrokenThemes[]=array('Name'=>$sTemplateDir,'Path'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sThemePath)),'Description'=>G::L('主题样式表已经损坏'));
				continue;
			}
			$sName=$arrThemeData['Name'];
			$sTitle=$arrThemeData['Title'];
			$sDescription=$arrThemeData['Description'];
			$nVersion=$arrThemeData['Version'];
			$sAuthor=$arrThemeData['Author'];
			$sTemplate=$arrThemeData['Template'];
			$sStylesheet=dirname(dirname(dirname($sThemePath)));
			$bPreview=false;
			foreach(array('png','gif','jpg','jpeg')as $sExt){
				if(file_exists("{$sStylesheet}/dyhb-x-blog-preview.{$sExt}")){
					$sPreview="dyhb-x-blog-preview.{$sExt}";
					break;
				}
			}
			if(empty($sName)){
				$sName=dirname(dirname(dirname($sThemePath)));
				$sTitle=$sName;
			}
			if(empty($sTemplate)){
				$sTemplate=$sTemplateDir;
			}
			$arrOkThemes[$sTemplate]=array(
				'Name'=>$sName,
				'Title'=>$sTitle,
				'Description'=>$sDescription,
				'Author'=>$sAuthor,
				'Author Name'=>$arrThemeData['AuthorName'],
				'Author URI'=>$arrThemeData['AuthorURI'],
				'Version'=>$nVersion,
				'Template'=>$sTemplate,
				'Stylesheet'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sThemePath)),
				'Template Dir'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sStylesheet)),
				'Stylesheet Dir'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath($sTheme.'/Public/Css/')),
				'Status'=>$arrThemeData['Status'],
				'Screenshot'=>$sPreview,
				'Tags'=>$arrThemeData['Tags'],
				'Theme Root'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath(dirname($sStylesheet))),
				'Theme Root URI'=>str_replace(G::tidyPath(APP_PATH),'{APP_PATH}',G::tidyPath(dirname($sStylesheet))),
			);
		}
		unset($arrCurrentThemes);
		$this->_arrOkThemes=$arrOkThemes;
		$this->_arrBrokenThemes=$arrBrokenThemes;
		return true;
	}

}
