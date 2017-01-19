<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	文件上传模型($)*/

!defined('DYHB_PATH') && exit;

class UploadModel extends CommonModel{

	static public function init__(){
		return array(
			'table_name'=>'upload',
			'props'=>array(
				'upload_id'=>array('readonly'=>true),
				'uploadcategory'=>array(Db::BELONGS_TO=>'UploadcategoryModel','target_key'=>'uploadcategory_id'),
				'user'=>array(Db::BELONGS_TO=>'UserModel','target_key'=>'user_id'),
			),
			'attr_protected'=>'upload_id',
			'autofill'=>array(
				array('user_id','userId','create','callback'),
			),
			'check'=>array(
				'node_name'=>array(
					array('require',G::L('应用名不能为空')),
					array('nodeName',G::L('应用名已经存在'),'condition'=>'must','extend'=>'callback'),
				),
				'node_title'=>array(
					array('require',G::L('显示名不能为空')),
				),
				'node_parentid'=>array(
					array('nodeParentId',G::L('节点不能为自己'),'condition'=>'must','extend'=>'callback'),
				),
				'node_sort'=>array(
					array('number',G::L('序号只能是数字'),'condition'=>'notempty','extend'=>'regex'),
				)
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

	public function upload($arrUploadinfos,$nRecordId=-1){
		if(empty($arrUploadinfos)){return;}
		$arrUploadinfoTemps=array();
		$nUploadcategoryId=G::getGpc('uploadcategory_id','P');
		$sUploadModule=G::getGpc('module','P');
		if($nUploadcategoryId===null){$nUploadcategoryId=-1;}
		if($sUploadModule===null){$sUploadModule='upload';}
		foreach($arrUploadinfos as $nKey=>$arrUploadinfo){
			foreach($arrUploadinfo as $sKey=>$value){
				$arrUploadinfoTemps[$nKey]['upload_'.$sKey]=$value;
				$arrUploadinfoTemps[$nKey]['upload_record']=$nRecordId;
				$arrUploadinfoTemps[$nKey]['uploadcategory_id']=$nUploadcategoryId;
				$arrUploadinfoTemps[$nKey]['upload_module']=$sUploadModule;
			}
		}
		unset($arrUploadinfos);
		foreach($arrUploadinfoTemps as $arrUploadinfoTemp){
			$arrUploadinfoTemp['upload_savepath']=str_replace('./Public/Upload/','',$arrUploadinfoTemp['upload_savepath']);
			$arrUploadinfoTemp['upload_thumbpath']=str_replace('./Public/Upload/','',$arrUploadinfoTemp['upload_thumbpath']);
			$oUpload=new self($arrUploadinfoTemp);
			$oUpload->save();
			$this->increaseUploadNum($oUpload->upload_module,$oUpload->upload_record);
			if($oUpload->isError()){
				return FALSE;
			}
		}
		return TRUE;
	}

	public function reduceUploadNum($sUploadModule,$nUploadRecord,$nNum=1){
		if(empty($sUploadModule)){
			return;
		}
		switch($sUploadModule){
			case 'blog':
				$oBlogModel=BlogModel::F('blog_id=?',$nUploadRecord)->query();
				$oBlogModel->blog_uploadnum=$oBlogModel->blog_uploadnum-$nNum;
				if($oBlogModel->blog_uploadnum<0){
					$oBlogModel->blog_uploadnum=0;
				}
				$oBlogModel->save(0,'update');
				break;
		}
	}

	public function increaseUploadNum($sUploadModule,$nUploadRecord,$nNum=1){
		if(empty($sUploadModule))
			return;
		switch($sUploadModule){
			case 'blog':
				$oBlogModel=BlogModel::F('blog_id=?',$nUploadRecord)->query();
				$oBlogModel->blog_uploadnum=$oBlogModel->blog_uploadnum+$nNum;
				$oBlogModel->save(0,'update');
				break;
		}
	}

	protected function userId(){
		$nUserId=intval(G::getGpc('user_id'));
		if($nUserId<0 || $nUserId==0){
			$nUserId=$GLOBALS['___login___']['user_id'];
		}
		return $nUserId>0?$nUserId:-1;
	}

}
