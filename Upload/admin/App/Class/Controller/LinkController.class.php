<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	衔接管理控制器($)*/

!defined('DYHB_PATH') && exit;

class LinkController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['link_name']=array('like',"%".G::getGpc('link_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('您可以在这里添加在您站点中显示的链接。我们预置了几个链接至 DoYouHaoBaby 社区的链接作为例子。','link').'</p>'.
				'<p>'.G::L('你可以批量删除衔接，删除后不可恢复。','link').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('您可以在相关部件中添加或编辑链接。只有链接的地址和名称是必填项。','link').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_link();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_link();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_link();
	}

	public function forbid(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$oModelMeta=LinkModel::M();
			$oModelMeta->updateDbWhere(array('link_isdisplay'=>0),array('link_id'=>$sId));
			if($oModelMeta->isError()){
				$this->E($oModelMeta->getErrorMessage());
			}
			else{
				Cache_Extend::front_widget_link();
				$this->assign('__JumpUrl__',G::U('link/index'));
				$this->S(G::L('禁用成功！'));
			}
		}
		else{
			$this->E(G::L('禁用项不存在！'));
		}
	}

	public function resume(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$oModelMeta=LinkModel::M();
			$oModelMeta->updateDbWhere(array('link_isdisplay'=>1),array('link_id'=>$sId));
			if($oModelMeta->isError()){
				$this->E($oModelMeta->getErrorMessage());
			}
			else{
				Cache_Extend::front_widget_link();
				$this->assign('__JumpUrl__',G::U('link/index'));
				$this->S(G::L('恢复成功！'));
			}
		}
		else{
			$this->E(G::L('恢复项不存在！'));
		}
	}

}
