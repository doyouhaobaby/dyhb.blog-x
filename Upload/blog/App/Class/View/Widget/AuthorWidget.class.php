<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   个人中心操作挂件($) */

!defined('DYHB_PATH') && exit;

class AuthorWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array('before_widget'=>'<div id="profile-content" >','after_widget'=>'</div>',);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		$data=&$arrData['data'];
		$nAllPost=BlogModel::F()->all()->getCounts();
		$nUserPost=BlogModel::F('user_id=?',$data['user_id'])->all()->getCounts();
		@$nPercent=round($nUserPost * 100 / $nAllPost,2);
		$nPostPerDay=CURRENT_TIMESTAMP - $data['create_dateline'] > 86400?round(86400 * $nUserPost /(CURRENT_TIMESTAMP - $data['create_dateline']),2): $nUserPost;
		$sUserNikename=empty($data['user_nikename'])?G::L('暂无昵称'):$data['user_nikename'];
		if($data->user_sex=='0'){
			$sUserSex=G::L('男');
		}
		elseif($data->user_sex=='1'){
			$sUserSex=G::L('女');
		}
		else{
			$sUserSex=G::L('保密');
		}
		if($data->user_marry=='0'){
			$sUserMarry=G::L('未婚');
		}
		elseif($data->user_marry=='1'){
			$sUserMarry=G::L('已婚');
		}
		else{
			$sUserMarry=G::L('保密');
		}
		$arrData['sUserMarry']=$sUserMarry;
		$arrData['sUserNikename']=$sUserNikename;
		$arrData['sUserSex']=$sUserSex;
		$arrData['nUserPost']=$nUserPost;
		$arrData['nPostPerDay']=$nPostPerDay;
		$arrData['nPercent']=$nPercent;
		return $this->renderTpl('',$arrData);
	}

}
