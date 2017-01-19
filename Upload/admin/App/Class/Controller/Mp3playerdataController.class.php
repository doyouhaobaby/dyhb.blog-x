<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Mp3播放器数据调用设置控制器($)*/

!defined('DYHB_PATH') && exit;

class Mp3playerdataController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function index(){
		$this->assign('arrOptions',$this->_arrOptions);
		$this->assign('arrUploadcategorys',$this->get_upload_category());
		$this->display();
	}

	public function update_config(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->update_mp3data($arrOption);
		$this->S(G::L('配置文件更新成功了！'));
	}

	public function update_mp3data($arrOption){
		$sMp3playerXml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$sMp3playerXml.="<player showDisplay=\"".$this->_arrOptions['mp3player_showdisplay']."\" showPlaylist=\"".$this->_arrOptions['mp3player_showplaylist']."\" autoStart=\"".$this->_arrOptions['mp3player_autostart']."\">\n";
		$arrMap=array();
		$arrMap['upload_extension']='mp3';
		if(!empty($arrOption['mp3player_data_max_size'])){$arrMap['upload_size']=array('elt',$arrOption['mp3player_data_max_size']);}
		if(!empty($arrOption['mp3player_data_min_size'])){$arrMap['upload_size']=array('egt',$arrOption['mp3player_data_min_size']);}
		if($arrOption['mp3player_data_category']!=0){$arrMap['uploadcategory_id']=$arrOption['mp3player_data_category'];}
		$sOrder='';
		if($arrOption['mp3player_data_id_ascdesc']=='asc'){$sOrder ="upload_id ASC,";}
		else{$sOrder="upload_id DESC,";}
		if($arrOption['mp3player_data_comment_ascdesc']=='asc'){$sOrder.="upload_commentnum ASC";}
		else{$sOrder.="upload_commentnum DESC";}
		$oUpload=UploadModel::F($arrMap)->all()->order($sOrder);
		if(!empty($arrOption['mp3player_data_num'])){
			$arrMp3s=$oUpload->limit(0,$arrOption['mp3player_data_num'])->query();
		}
		else{
			$arrMp3s=$oUpload->query();
		}
		foreach($arrMp3s as $oMp3){
			$sMp3Path=__PUBLIC__.'/Upload/'.$oMp3['upload_savepath'].'/'.$oMp3['upload_savename'];
			$sMp3playerXml.="<song path=\"{$sMp3Path}\" title=\"".str_ireplace('.mp3','',$oMp3['upload_name'])."\" />\n";
		}
		$sMp3playerXml.="</player>";
		$sMp3plaerXmlPath=DYHB_PATH.'/../Public/Images/Mp3/mp3player.xml';
		if(!is_writable($sMp3plaerXmlPath)){
			$this->E(G::L('音乐播放器XML 文件不可读'));
		}
		else{
			$hHp=fopen($sMp3plaerXmlPath,"wb+");
			if(!fwrite($hHp,$sMp3playerXml)){
				$this->E(G::L('音乐播放器XML 文件不可写，Linux环境请确保其权限为0777'));
			}
		}
	}

	public function get_upload_category(){
		return UploadcategoryModel::F()->order('uploadcategory_id DESC')->all()->query();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('从这里开始侧边栏播放器数据采集，这里只采集附件系统中的mp3格式音乐。','mp3playerdata').'</p>'.
				'<p>'.G::L('因为外部音乐衔接很容易失效，于是我们决定只适用你自己博客中音乐。','mp3playerdata').'</p>'.
				'<p>'.G::L('自己上传音乐到服务器，请注意做好防盗链设置，防止迅雷、百度吸血。','mp3playerdata').'</p>'.
				'<p>'.G::L('音乐数据会被保存在一个xml格式的文件中，系统不会自动更新，你需要更新数据，请来这里更新好了。','mp3playerdata').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

}
