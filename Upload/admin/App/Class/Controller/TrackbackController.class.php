<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	引用管理控制器($)*/

!defined('DYHB_PATH') && exit;

class TrackbackController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['trackback_title']=array('like',"%".G::getGpc('trackback_title')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('TrackBack是一种网络日志应用工具，它可以让网志作者知道有哪些人看到自己的文章后撰写了与之有关的短文。这种功能通过在网志之间互相「ping」的机制，实现了网站之间的互相通告；因此，它也可以提供提醒功能。 ','trackback').'</p>'.
				'<p>'.G::L('通过Trackback 你可以加强和其它的博客用户的联系。','trackback').'</p>'.
				'<p>'.G::L('通过本页，你可以批量删除引用，这样可以大大提高效率。','trackback').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return '<p>'.G::L('你可以编辑引用，在表单调好数据后，点击更新即可。','trackback').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',trim($sId));
			foreach($arrIds as $nId){
				$this->update_the_trackbacks($nId);
			}
		}
	}

	protected function update_the_trackbacks($nId){
		$oBlog=BlogModel::F('blog_id=?',$nId)->query();
		if(!empty($oBlog['blog_id'])){
			$oBlog->blog_trackbacknum-=1;
			if($oBlog->blog_trackbacknum<0){
				$oBlog->blog_trackbacknum=0;
			}
			$oBlog->save(0,'update');
			if($oBlog->isError()){
				$this->E($oBlog->getErrorMessage());
			}
		}
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_static();
	}

}
