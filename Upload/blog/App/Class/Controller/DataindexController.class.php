<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   数据调用控制器($) */

!defined('DYHB_PATH') && exit;

class DataindexController extends CommonController{

	public function call(){
		$sContent=$this->call_data();
		if(trim($sContent)!=''){
			$arrLine=explode("\n",$sContent);
			foreach($arrLine as $sLine){
				if(substr($sLine,strlen($sLine)-1,strlen($sLine))=="\n"){
					$sLine=substr($sLine,0,strlen($sLine)-1);
				}
				echo 'document.write(\''.addslashes(trim($sLine)).'\');'."\n";
			}
		}
	}

	public function call_data(){
		$nId=intval(G::getGpc('id','G'));
		$sHash=G::getGpc('hash','G');
		$arrMap=array();
		if(!empty($nId)){
			$arrMap['dataindex_id']=$nId;
		}
		if(!empty($sHash)){
			$arrMap['dataindex_md5hash']=$sHash;
		}
		$oDataindexModel=DataindexModel::F()->where($arrMap)->query();
		$arrBlogLists=Model::C($oDataindexModel['dataindex_md5hash'],'',array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration'),'cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		if($arrBlogLists===false){
			$arrWhereDatastring=E::mbUnserialize(stripslashes($oDataindexModel['dataindex_datastring']));
			$arrBlogLists=BlogModel::F()->asArray()->where($arrWhereDatastring['where'])->all()->order($arrWhereDatastring['order'])->limit($arrWhereDatastring['limit'][0],$arrWhereDatastring['limit'][1])->query();
			if(E::oneImensionArray($arrBlogLists) && !empty($arrBlogLists)){
				$arrBlogLists=array($arrBlogLists);
			}
			$arrBlogIds=array();
			foreach($arrBlogLists as $oBlogList){
				$arrBlogIds[]=$oBlogList['blog_id'];
			}
			$oDataindexModel->dataindex_totals=count($arrBlogIds);
			$oDataindexModel->dataindex_ids=implode(',',$arrBlogIds);
			$oDataindexModel->save(0,'update');
			if($oDataindexModel->isError()){
				G::E($oDataindexModel->getErrorMessage());
			}
			Model::C($oDataindexModel['dataindex_md5hash'],$arrBlogLists,array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration'),'cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		}
		$arrConditionstring=unserialize($oDataindexModel['dataindex_conditionstring']);
		$sContent='';
		$arrSaveData=array();
		foreach($arrBlogLists as $nBlogId=>$oBlogList){
			if($arrConditionstring['link_style']==0)$sTarget='';
			if($arrConditionstring['link_style']==1)$sTarget='target="_blank"';
			if($arrConditionstring['link_style']==2)$sTarget='target="_self"';
			$arrSaveData['blog_title']=$oBlogList['blog_title'];
			if(!empty($arrConditionstring['title_cutnum'])){
				$arrSaveData['blog_title']=String::subString($arrSaveData['blog_title'],0,$arrConditionstring['title_cutnum']);
			}
			$arrSaveData['blog_commentnum']=$oBlogList['blog_commentnum'];
			$arrSaveData['blog_viewnum']=$oBlogList['blog_viewnum'];
			$arrSaveData['blog_trackbacknum']=$oBlogList['blog_trackbacknum'];
			$arrSaveData['user_id']=$oBlogList['user_id'];
			if($arrSaveData['user_id']!=-1){
				$arrUser=UserModel::F('user_id=?',$arrSaveData['user_id'])->asArray()->query();
			}
			else{
				$arrUser=array();
			}
			$arrSaveData['user_name']=!empty($arrUser['user_name'])?$arrUser['user_name']:'nobody';
			$arrSaveData['user_nikename']=!empty($arrUser['user_nikename'])?$arrUser['user_nikename']:$arrUser['user_name'];
			$arrSaveData['blog_url']=$this->get_host_header().PageType_Extend::getBlogUrl($oBlogList);
			$arrSaveData['blog_title_link']="<a href=\"".$arrSaveData['blog_url']."\" title=\"".$arrSaveData['blog_title']."\" {$sTarget}>".$arrSaveData['blog_title']."</a>";
			$arrSaveData['blog_id']=$oBlogList['blog_id'];
			$arrSaveData['blog_excerpt']=strip_tags($oBlogList['blog_excerpt']);
			if(!empty($arrConditionstring['infolen'])){
				$arrSaveData['blog_excerpt']=String::subString($arrSaveData['blog_excerpt'],0,$arrConditionstring['infolen']);
			}
			$arrDateStyleEncode=array('Y-n-j','Y-m-d','Y'.G::L('年').'n'.G::L('月').'j'.G::L('日'),'Y'.G::L('年').'m'.G::L('月').'d'.G::L('日'),'Y-n-j g:i','Y-m-d H:i','Y-n-j g:i:s','Y-m-d H:i:s','Y-n-j g:i:s l','Y-m-d H:i:s l');
			$arrSaveData['blog_dateline']=date($arrDateStyleEncode[$arrConditionstring['date_style']],$oBlogList['blog_dateline']);
			$arrSaveData['category_id']=$oBlogList['category_id'];
			if($arrSaveData['category_id']!=-1){
				$arrCategory=CategoryModel::F('category_id=?',$arrSaveData['category_id'])->asArray()->query();
			}
			else{
				$arrCategory=array();
			}
			$arrSaveData['category_name']=!empty($arrCategory['category_name'])?$arrCategory['category_name']:G::L('未分类');
			$arrSaveData['blog_thumb_url']=$this->get_host_header().Blog_Extend::getThumbImage($oBlogList);
			$arrSaveData['blog_thumb']="<img src=\"".$arrSaveData['blog_thumb_url']."\" alt=\"".$oBlogList['blog_title']."\" />";
			$arrSaveData['blog_thumb_link']="<a href=\"".$arrSaveData['blog_url']."\" {$sTarget}>".$arrSaveData['blog_thumb']."</a>";
			$sTemplate=$oDataindexModel['dataindex_template'];
			foreach($arrSaveData as $sSaveDataKey=>$sSaveData){
				$sTemplate=str_replace('{'.$sSaveDataKey.'}',$sSaveData,$sTemplate);
			}
			$sContent.=$sTemplate;
		}
		return $sContent;
	}

}
