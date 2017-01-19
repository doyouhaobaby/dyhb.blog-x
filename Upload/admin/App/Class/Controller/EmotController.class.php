<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	表情管理控制器($)*/

!defined('DYHB_PATH') && exit;

class EmotController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['emot_name']=array('like',"%".G::getGpc('emot_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('你可以在这里对表情进行管理，添加新表情。','emot').'</p>'.
				'<p>'.G::L('系统消息不能够被删除。','emot').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('在这里可以添加一个表情。','pm').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function bEdit_(){
		$nEmotId=intval(G::getGpc('id','G'));
		if($nEmotId < 51){
			$this->E(G::L('系统表情不能被编辑'));
		}
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		$arrIds=explode(',',$sId);
		foreach($arrIds as $nId){
			if($nId < 51){
				$this->E(G::L('系统表情不能被删除'));
			}
		}
	}

	protected function aInsert($nId){
		Cache_Extend::global_emot('admin');
		Cache_Extend::global_emot('blog');
	}

	protected function aUpdate($nId){
		Cache_Extend::global_emot('admin');
		Cache_Extend::global_emot('blog');
	}

	public function aForeverdelete($sId){
		Cache_Extend::global_emot('admin');
		Cache_Extend::global_emot('blog');
	}

}
