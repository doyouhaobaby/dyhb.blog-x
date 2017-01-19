<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	标签模型($)*/

!defined('DYHB_PATH') && exit;

class TagModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'tag',
			'props'=>array(
				'tag_id'=>array('readonly'=>true),
			),
			'attr_protected'=>'tag_id',
			'check'=>array(
				'tag_name'=>array(
					array('require',G::L('标签名不能为空')),
				),
				'tag_urlname'=>array(
					array('empty'),
					array('number_underline_english',G::L('URL 别名只能是字母，数字和下划线')),
					array('urlName',G::L('标签别名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
			),
		);
	}

	static function F(){
		$arrArgs=func_get_args();
		return ModelMeta::instance(__CLASS__)->findByArgs($arrArgs);
	}

	static function M(){
		return ModelMeta::instance(__CLASS__);
	}

	public function urlName(){
		$nId=G::getGpc('id','P');
		$sTagUrlName=G::getGpc('tag_urlname','P');
		$sTagInfo='';
		if($nId){
			$arrTag=self::F('tag_id=?',$nId)->asArray()->getOne();
			$sTagInfo=trim($arrTag['tag_urlname']);
		}

		if($sTagUrlName !=$sTagInfo){
			$arrResult=self::F()->getBytag_urlname($sTagUrlName)->toArray();
			if(!empty($arrResult['tag_id'])){
				return false;
			}
			else{
				return true;
			}
		}
		return true;
	}

	public function getTagsByBlogId($nBlogId,$nStart=0,$nEnd=0){
		$oTag=self::F()->where(array('blog_id'=>array('like','%'.$nBlogId.',%')))->order('`tag_id` DESC');
		if($nStart==0 && $nEnd!=0){
			$oTag->limit($nStart,$nEnd);
		}
		else if($nStart!=0 && $nEnd==0){
			$oTag->limit(0,$nStart);
		}
		return $oTag->all()->query();
	}

	public function getBlogIdStrByTagId($nTagId){
		if(!preg_match("/[^\d-.,]/",$nTagId)){
			$arrMap['tag_id']=$nTagId;
		}
		else{
			$arrMap['tag_urlname']=$nTagId;
		}
		$oTag=self::F()->setColumns('blog_id')->where($arrMap)->query();
		if(empty($oTag['blog_id'])){return null;}
		return rtrim($oTag['blog_id'],',');
	}

	public function addTag($sTag,$nBlogId){
		$arrTag=!empty($sTag)? explode(',',$sTag): array();
		$arrTag=array_unique($arrTag);
		foreach($arrTag as $sTagName){
			$oOneTag=self::F('tag_name=?',$sTagName)->query();
			$sBlogIds='';
			if(empty($oOneTag->tag_id)){
				$sBlogIds.=$nBlogId?",{$nBlogId},":'';
				$oTag=new self();
				$oTag->tag_name=$sTagName;
				$oTag->blog_id=$sBlogIds;
				$oTag->tag_usenum=1;
				$oTag->save();
				if($oTag->isError()){
					return false;
				}
			}
			else{
				$sBlogIds.=$nBlogId?"{$nBlogId},":'';
				$oTag=self::F('tag_name=?',$sTagName)->query();
				$oTag->blog_id=$oTag->blog_id.$sBlogIds;
				$oTag->tag_usenum=$oTag->tag_usenum+1;
				$oTag->save(0,'update');
				if($oTag->isError()){
					return false;
				}
			}
		}
		return true;
	}

	public function updateTag($sTag,$nBlogId){
		$arrTag=!empty($sTag)? explode(',',$sTag): array();
		$arrTag=array_unique($arrTag);
		$arrBlogTags=self::F("blog_id LIKE '%?%'",$nBlogId)->setColumns('tag_name')->all()->query();
		$arrOldTag=array();
		foreach($arrBlogTags as $oBlogTag){
			$arrOldTag[]=$oBlogTag->tag_name;
		}
		if(empty($arrOldTag)){
			$arrOldTag=array('');
		}
		$arrDiffTag=$this->findArray(array_filter($arrTag),$arrOldTag);
		for($nN=0;$nN<count($arrDiffTag);$nN++){
			for($nJ=0 ;$nJ<count($arrOldTag);$nJ++){
				if($arrDiffTag[$nN]==$arrOldTag[$nJ]){
					$oTag=self::F('tag_name=?',$arrDiffTag[$nN])->query();
					$oTag->blog_id=str_replace(",{$nBlogId},",',',$oTag->blog_id);
					$oTag->tag_usenum=$oTag->tag_usenum-(substr_count($nBlogId,',')+1);
					$oTag->save(0,'update');
					if($oTag->isError()){
						return false;
					}
					$oTagModelMeta=self::M();
					$oTagModelMeta->deleteWhere( "blog_id=?",',');
					if($oTagModelMeta->isError()){
						return false;
					}
					break;
				}
				elseif($nJ==count($arrOldTag)-1){
					$oOneTag=self::F('tag_name=?',trim($arrDiffTag[$nN]))->setColumns('tag_name')->query();
					if(empty($oOneTag->tag_id)){
						$oTag=new self();
						$oTag->tag_name=trim($arrDiffTag[$nN]);
						$oTag->blog_id=",{$nBlogId},";
						$oTag->tag_usenum=substr_count($nBlogId,',')+1;
						$oTag->save();
						if($oTag->isError()){
							return false;
						}
					}
					else{
						$oTag=self::F('tag_name=?',$arrDiffTag[$nN])->query();
						$oTag->blog_id=$oTag->blog_id."{$nBlogId},";
						$oTag->tag_usenum=$oTag->tag_usenum+(substr_count($nBlogId,',')+1);
						$oTag->save(0,'update');
						if($oTag->isError()){
							return false;
						}
					}
				}
			}
		}
		return true;
	}

	private function findArray($arr1,$arr2){
		$arrDiff1=array_diff($arr1,$arr2);
		$arrDiff2=array_diff($arr2,$arr1);
		return array_merge($arrDiff1,$arrDiff2);
	}

}
