<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	标签管理控制器($)*/

!defined('DYHB_PATH') && exit;

class TagController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['tag_name']=array('like',"%".G::getGpc('tag_name')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('您可为文章指定一些关键词，这些关键词叫做“文章标签”。与分类目录不同的是，标签没有层级关系，换句话说，也就是标签之间没有关联。','tag').'</p>'.
				'<p>'.G::L('分类目录和标签的区别是什么呢？通常，标签是临时安排的一些关键词，用来标记文章中的关键信息（名字，题目等），也许其它文章也会拥有这个标签。而分类则是事先决定了的。若将您的站点比做一本书，那么分类目录就是书的目录，标签则是书前所列出的术语。','tag').'</p>'.
				'<p>'.G::L('当您创建一个新标签时，您须填写下列栏目：','tag').'</p><ul>'.
				'<li><strong>'.G::L('名字').'</strong> -'.G::L('标签在网站上的显示名称。','tag').'</li>'.
				'<li><strong>'.G::L('别名').'</strong> -'.G::L('“别名“是 URL 友好的另外一个叫法。它通常为小写并且只能包含字母，数字和连字符。','tag').'</li>'.
				'<li><strong>'.G::L('标签关键字').'</strong> -'.G::L('标签关键字用来提高搜索引擎的搜录。','tag').'</li>'.
				'<li><strong>'.G::L('标签描述').'</strong> -'.G::L('标签描述用来提高搜索引擎的搜录，提供描述，提高访客点击。','tag').'</li>'.
				'<p>'.G::L('标签可以友好地组织数据，你可以好好地利用。','tag').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return $this->_add_get_admin_help_description();
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('你可以编辑标签，在表单调好数据后，点击更新即可。','tag').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _repaire_get_admin_help_description(){
		return '<p>'.G::L('程序难免记录计算错每个标签使用次数，本功能是重新统计各个Tag的使用次数和清理不使用的Tag。','tag').'</p><ul>'.
				'<p>'.G::L('本次操作会根据标签表来更正标签统计，但这个不一定正确。','tag').'</p>'.
				'<p>'.G::L('由于删除文章的时候，标签没有被删除，那个这个时候标签也是错误的，这个时候我们可以点击“深度整理”来更新数据。','tag').'</p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function repaire(){
		$this->display();
	}

	public function tag_repaire(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['tag_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['tag_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['tag_id']=array('elt',$nEnd);
		}

		if(!$nTotal){
			$nTotal=TagModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrTags=TagModel::F()->where($arrMap)->all()->order('`tag_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrDeleteTags=array();
		$arrRepaireTags=array();
		$arrFailedTags=array();
		foreach($arrTags as $oTags){
				$sBlogIds=TagModel::F()->query()->getBlogIdStrByTagId($oTags->tag_id);
			if(empty($sBlogIds)){
				$oTagMeta=TagModel::M();
				$arrDeleteTags[]=$oTags->tag_id;// 记录被删除的标签
				$oTagMeta->deleteWhere('tag_id=?',$oTags->tag_id);
			}
			else{
				$nTagBlogNum=substr_count($oTags->blog_id,',')- 1;
				if($oTags->tag_usenum !=$nTagBlogNum){
					$oTags->tag_usenum=$nTagBlogNum;
					$oTags->save(0,'update');
					if($oTags->isError()){
						$arrFailedTags[]=$oTags->tag_id.' '.$oTags->getErrorMessage();
					}
					else{
						$arrRepaireTags[]=$oTags->tag_id;
					}
				}
			}
			$nActionNum++;
			$nCount++;
		}

		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("tag/tag_repaire?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sFaild='';
		if(!empty($arrFailedTags)){
			$sFaild="<br/><div style=\"color:red;\"><h3>".G::L('整理失败标签ID','tag')."</h3><br/>";
			$sFaild.=implode('<br/>',$arrFailedTags);
			$sFaild.="</div>";
		}
		$sRepaired='';
		if(!empty($arrRepaireTags)){
			$sRepaired="<br/><div style=\"color:green;\"><h3>".G::L('修复标签成功ID','tag')."</h3><br/>";
			$sRepaired.=implode('<br/>',$arrRepaireTags);
			$sRepaired.="</div>";
		}
		$sDeleteTags='';
		if(!empty($arrDeleteTags)){
			$sDeleteTags="<br/><div style=\"color:green;\"><h3>".G::L('删除标签成功ID','tag')."</h3><br/>";
			$sDeleteTags.=implode('<br/>',$arrDeleteTags);
			$sDeleteTags.="</div>";
		}
		if($nTotal>$nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo.$sDeleteTags.$sFaild.$sRepaired);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',G::U('tag/repaire'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sDeleteTags.$sFaild.$sRepaired);
		}
	}

	public function _deep_repaire_get_admin_help_description(){
		return '<p>'.G::L('程序难免记录错每篇文章的关键字和计算错每个关键字使用次数，本功能是重新统计各个Tag的使用次数和清理不使用的Tag。','tag').'</p><ul>'.
				'<p>'.G::L('本次操作将会精确统计系统标签数据。','tag').'</p>'.
				'<p>'.G::L('为了使Tags数据最准确，本次操作逐个读取标签的日志，并且判断其是否存在，从而达到精确统计标签数据。','tag').'</p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function deep_repaire(){
		$this->display();
	}

	public function deep(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['tag_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['tag_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['tag_id']=array('elt',$nEnd);
		}

		if(!$nTotal){
			$nTotal=TagModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrTags=TagModel::F()->where($arrMap)->all()->order('`tag_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrDeleteTags=array();
		$arrRepaireTags=array();
		$arrFailedTags=array();
		foreach($arrTags as $oTags){
			$sBlogIds=TagModel::F()->query()->getBlogIdStrByTagId($oTags->tag_id);
			if(empty($sBlogIds)){
				$oTagMeta=TagModel::M();
				$arrDeleteTags[]=$oTags->tag_id;// 记录被删除的标签
				$oTagMeta->deleteWhere('tag_id=?',$oTags->tag_id);
			}
			else{
				$arrBlogs=Dyhb::normalize(trim($oTags->blog_id));
				$arrNotExistBlog=array();
				foreach($arrBlogs as $nBlogId){
					$oBlog=BlogModel::F('blog_id=?',$nBlogId)->query();
					if(empty($oBlog->blog_id)){
						$arrNotExistBlog[]=$nBlogId;
					}
				}
				foreach($arrNotExistBlog as $nNotExistBlog){
					$oTags->blog_id=str_replace(",{$nNotExistBlog},",',',$oTags->blog_id);
					$oTags->tag_usenum=$oTags->tag_usenum-1;
				}
				if($oTags->blog_id==','){
					$oTagModelMeta=TagModel::M();
					$arrDeleteTags[]=$oTags->tag_id;// 记录被删除的标签
					$oTagModelMeta->deleteWhere("tag_id=?",$oTags->tag_id);
				}
				else{
					$oTags->save(0,'update');
					if($oTags->isError()){
						$arrFailedTags[]=$oTags->tag_id.' '.$oTags->getErrorMessage();
					}
					else{
						$arrRepaireTags[]=$oTags->tag_id;
					}
				}
			}
			$nActionNum++;
			$nCount++;
		}
		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("tag/deep?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sFaild='';
		if(!empty($arrFailedTags)){
			$sFaild="<br/><div style=\"color:red;\"><h3>".G::L('整理失败标签ID','tag')."</h3><br/>";
			$sFaild.=implode('<br/>',$arrFailedTags);
			$sFaild.="</div>";
		}
		$sRepaired='';
		if(!empty($arrRepaireTags)){
			$sRepaired="<br/><div style=\"color:green;\"><h3>".G::L('修复标签成功ID','tag')."</h3><br/>";
			$sRepaired.=implode('<br/>',$arrRepaireTags);
			$sRepaired.="</div>";
		}
		$sDeleteTags='';
		if(!empty($arrDeleteTags)){
			$sDeleteTags="<br/><div style=\"color:green;\"><h3>".G::L('删除标签成功ID','tag')."</h3><br/>";
			$sDeleteTags.=implode('<br/>',$arrDeleteTags);
			$sDeleteTags.="</div>";
		}
		if($nTotal>$nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo.$sDeleteTags.$sFaild.$sRepaired);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',G::U('tag/deep_repaire'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sDeleteTags.$sFaild.$sRepaired);
		}
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_hottag();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_hottag();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_hottag();
		Cache_Extend::front_widget_static();
	}

}
