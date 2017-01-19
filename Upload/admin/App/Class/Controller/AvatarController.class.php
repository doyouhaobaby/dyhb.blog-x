<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	头像上传控制器($)*/

!defined('DYHB_PATH') && exit;

class AvatarController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('本软件内置头像系统，你可以为你上传一张个性图片作为你的名片。','avatar').'</p>'.
				'<p>'.G::L('你的头像被创建后，将会生成4张图片，大头像200*200、中等头像120*120、小头像48*48，同时还有一张原始图片作为高清图片选项。','avatar').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _upload_get_admin_help_description(){
		return '<p>'.G::L('在这里，你可以选择裁剪图片，裁剪后的图片将会被压缩为200*200的大头像，保存后将会以大头像创建中等头像120*120和小头像48*48。','avatar').'</p>'.
				'<p>'.G::L('程序最后会将上传的原始图像保存下来为高清头像。','avatar').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrAvatar=$this->get_avatar_url();
		$this->assign('bExistAvatar',$arrAvatar['exist']);
		$this->assign('sOriginAvatar',$arrAvatar['origin']);
		$this->assign('sBigAvatar',$arrAvatar['big']);
		$this->assign('sMiddleAvatar',$arrAvatar['middle']);
		$this->assign('sSmallAvatar',$arrAvatar['small']);
		$this->display();
	}

	public function upload(){
		if($_FILES['image']['error']==4){
			$this->E(G::L('你没有选择任何文件！'));
			return;
		}

		$oUploadfile=new UploadFileForUploadify('',array('gif','jpg','jpeg','png'),'','./Public/Avatar');
		$oUploadfile->_sSaveRule=array('AvatarController','get_temp_avatar');// 设置上传文件规则
		$oUploadfile->setUploadifyDataName('image');
		$oUploadfile->setUploadReplace(TRUE);
		$oUploadfile->setAutoCreateStoreDir(TRUE);
		if(!$oUploadfile->upload()){
			$this->E($oUploadfile->getErrorMessage());
		}
		else{
			$arrPhotoInfo=$oUploadfile->getUploadFileInfo();
		}
		$this->assign('arrPhotoInfo',reset($arrPhotoInfo));
		$this->display();
	}

	public function save_crop(){
		$nTargW=$nTargH=200;
		$nJpegQuality=intval($this->_arrOptions['avatar_crop_jpeg_quality']);
		$sSrc=G::getGpc('temp_image','P');
		$sSrc=DYHB_PATH.'/../Public/Avatar/'.$sSrc;
		$arrPhotoInfo=@getimagesize($sSrc);
		if(function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')){
			switch($arrPhotoInfo['mime']){
				case 'image/jpeg':
					$sImageCreateFromFunc=function_exists('imagecreatefromjpeg')? 'imagecreatefromjpeg':'';
					break;
				case 'image/gif':
					$sImageCreateFromFunc=function_exists('imagecreatefromgif')? 'imagecreatefromgif':'';
					break;
				case 'image/png':
					$sImageCreateFromFunc=function_exists('imagecreatefrompng')? 'imagecreatefrompng':'';
					break;
			}
			if(empty($sImageCreateFromFunc)){
				$this->E(G::L("请不要重复提交图像，因为已经上传过图像，临时文件已经被删除！"));
			}
			$oImgR=$sImageCreateFromFunc($sSrc);
			$oDstR=ImageCreateTrueColor($nTargW,$nTargH);
			imagecopyresampled($oDstR,$oImgR,0,0,intval(G::getGpc('x','P')),intval(G::getGpc('y','P')),$nTargW,$nTargH,intval(G::getGpc('w','P')),intval(G::getGpc('h','P')));
			$sOutfile=DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('big');
			$this->create_file_dir($sOutfile);
			imagejpeg($oDstR,$sOutfile,$nJpegQuality);
			imagedestroy($oDstR);
			$this->create_thumb($sOutfile,$sImageCreateFromFunc);
		}
		else{
			$this->E(G::L("你的PHP 版本或者配置中不支持如下的函数 “imagecreatetruecolor”、“imagecopyresampled”等图像函数，所以创建不了头像。"));
		}
		$this->create_high_jpg($sSrc,$sImageCreateFromFunc);
		$this->assign('__JumpUrl__',G::U('avatar/index'));
		$this->S(G::L("头像上传成功了！"));
	}

	public function create_high_jpg($sSrc,$sImageCreateFromFunc){
		$oImgR=$sImageCreateFromFunc($sSrc);
		imagejpeg($oImgR,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar(),intval($this->_arrOptions['avatar_origin_jpeg_quality']));
		imagedestroy($oImgR);
		unlink($sSrc);
	}

	static public function get_temp_avatar($arrData){
		return 'temp/temp_'. $GLOBALS['___login___']['user_id'].'.'.$arrData['extension'];
	}

	public function create_file_dir($sDir){
		if(!is_dir(dirname($sDir))&& !G::makeDir(dirname($sDir))){
			$this->E(G::L('上传目录%s不可写',null,null,dirname($sDir)));
		}
	}

	public function create_thumb($sOutfile){
		$arrThumbs=array(
			array($sOutfile,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('middle'),array(120,120)),
			array($sOutfile,DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar('small'),array(48,48)),
		);
		foreach($arrThumbs as $arrThumb){
			$this->create_one_thumb($arrThumb[0],$arrThumb[1],$arrThumb[2]);
		}
	}

	public function create_one_thumb($sFilename,$sThumbPath,$arrSize=array()){
		$this->create_file_dir($sThumbPath);
		Image::thumb($sFilename,$sThumbPath,'',$arrSize[0],$arrSize[1],true);
	}

}
