<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	更新论坛缓存统计数据控制器($)*/

!defined('DYHB_PATH') && exit;

class BoardcachedataController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('这里竟会重建论坛缓存统计数据。','boardcachedata').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$this->display();
	}

	public function rebuild_count(){
		$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
		$oCacheData->countcache_topicsnum=BlogModel::F()->all()->getCounts();
		$oCacheData->countcache_postsnum=CommentModel::F()->all()->getCounts();
		$oCacheData->countcache_usersnum=UserModel::F()->all()->getCounts();
		$oCacheData->save(0,'update');
		if($oCacheData->isError()){
			$this->E($oCacheData->getErrorMessage());
		}
		$this->S(G::L('论坛缓存数据更新成功了'));
	}

}
