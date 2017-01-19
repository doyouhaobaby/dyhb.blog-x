<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	短消息管理控制器($)*/

!defined('DYHB_PATH') && exit;

class PmController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['pm_message']=array('like',"%".G::getGpc('pm_message')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('你可以在这里对短消息进行管理，发送系统短消息等等。','pm').'</p>'.
				'<p>'.G::L('短消息被删除后，不可恢复。','pm').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('在这里你可以发送系统短消息，这样用户就会接受到。','pm').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		$arrIds=explode(',',$sId);
		foreach($arrIds as $nId){
			$arrPmSystems=SystempmModel::F()->all()->query();
			foreach($arrPmSystems as $oPmSystem){
				$arrReadIds=unserialize($oPmSystem['systempm_readids']);
				if(in_array($nId,$arrReadIds)){
					foreach($arrReadIds as $nKey=>$nReadIds){
						if($nReadIds==$nId){
							unset($arrReadIds[$nKey]);
						}
					}
				}

				if(empty($arrReadIds)){
					$oDb=Db::RUN();
					$sSql="DELETE FROM ". SystempmModel::F()->query()->getTablePrefix()."systempm WHERE `user_id`=".$oPmSystem['user_id'];
					$oDb->query($sSql);
				}
				else{
					$oPmSystem->systempm_readids=serialize($arrReadIds);
					$oPmSystem->save(0,'update');
					if($oPmSystem->isError()){
						$this->E($oPmSystem->getErrorMessage());
					}
				}
			}
		}
	}

}
