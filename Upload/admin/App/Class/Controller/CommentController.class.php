<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	评论控制器($)*/

!defined('DYHB_PATH') && exit;

class CommentController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['comment_name']=array('like',"%".G::getGpc('comment_name')."%");
		$arrMap['comment_relationtype']=array('neq','');
		$arrMap['comment_relationvalue']=array('neq','');
		$sType=G::getGpc('type');
		if(!empty($sType)){
			$arrMap['comment_relationtype']=$sType;
		}
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('你可以编辑评论，你可以设置嵌套深度，也可以设置是否启用邮件通知。','comment').'</p>'.
				'<p>'.G::L('你可以在系统评论设置中对评论相关数据进行设置，比如说评论显示条数。','comment').'</p>'.
				'<p>'.G::L('你也可以对评论进行垃圾防止处理，这样可以狙击spam信息。','comment').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return '<p>'.G::L('这里你可以修改评论的信息。','comment').'</p>'.
				'<p>'.G::L('注意，如果评论者为登录会员，那么前台评论信息会从会员信息中直接提取数据，即使你修改了评论数据库信息。','comment').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		$arrIds=explode(',',$sId);
		foreach($arrIds as $nId){
			$oTheComment=CommentModel::F('comment_id=?',$nId)->query();
			$arrComments=CommentModel::F('comment_parentid=?',$nId)->all()->query();
			foreach($arrComments as $oComment){
				$oComment->comment_parentid=$oTheComment->comment_parentid;
				$oComment->save(0,'update');
				if($oComment->isError()){
					$this->E($oComment->getErrorMessage());
				}
			}
		}
		$this->update_the_commentnum($sId);
	}

	public function update_the_commentnum($sId){
		$arrIds=explode(',',$sId);
		foreach($arrIds as $nId){
			$oTheComment=CommentModel::F('comment_id=?',$nId)->query();
			switch($oTheComment->comment_relationtype){
				case "taotao":
					$oTaotao=TaotaoModel::F('taotao_id=?',$oTheComment->comment_relationvalue)->query();
					if(!empty($oTaotao['taotao_id'])){
						$oTaotao->taotao_commentnum-=1;
					}
					if($oTaotao->taotao_commentnum<0){
						$oTaotao->taotao_commentnum =0;
					}
					$oTaotao->save(0,'update');
					if($oTaotao->isError()){
						$this->E($oTaotao->getErrorMessage());
					}
					break;
				case "upload":
					$oUpload=UploadModel::F('upload_id=?',$oTheComment->comment_relationvalue)->query();
					if(!empty($oUpload['upload_id'])){
						$oUpload->upload_commentnum-=1;
					}
					if($oUpload->upload_commentnum<0){
						$oUpload->upload_commentnum=0;
					}
					$oUpload->save(0,'update');
					if($oUpload->isError()){
						$this->E($oUpload->getErrorMessage());
					}
					break;
				case "blog":
					$oBlog=BlogModel::F('blog_id=?',$oTheComment->comment_relationvalue)->query();
					if(!empty($oBlog['blog_id'])){
						$oBlog->blog_commentnum-=1;
					}
					if($oBlog->blog_commentnum<0){
						$oBlog->blog_commentnum=0;
					}
					$oBlog->save(0,'update');
					if($oBlog->isError()){
						$this->E($oBlog->getErrorMessage());
					}
					if($oTheComment->category_id){
					$oCategory=CategoryModel::F('category_id=?',$oTheComment->category_id)->query();
					$oCategory->category_comments=$oCategory->category_comments-1;
					$oCategory->save(0,'update');
					if($oCategory->isError()){
						$this->E($oCategory->getErrorMessage());
					}
					Cache_Extend::front_widget_category();
				}
				$oCacheData=CountcacheModel::F('countcache_id=?',1)->query();
				$oCacheData->countcache_postsnum=$oCacheData->countcache_postsnum  -1;
				$oCacheData->save(0,'update');
				if($oCacheData->isError()){
					$this->E($oCacheData->getErrorMessage());
				}
				break;
			}
		}

		if($oTheComment->user_id!=-1){
			$oUser=UserModel::F('user_id=?',$oTheComment->user_id)->query();
			$oUser->user_comments=$oUser->user_comments-1;
			$oUser->save(0,'update');
			if($oUser->isError()){
				$this->E($oUser->getErrorMessage());
			}
		}
	}

	protected function aForeverdelete($sId){
		Cache_Extend::front_widget_guestbook();
		Cache_Extend::front_widget_comment();
		Cache_Extend::front_widget_static();
	}

	public function _build_get_admin_help_description(){
		return '<p>'.G::L('评论数据在处理过程中难免会出现错误，这个时候我们需要修复错误。','comment').'</p>'.
				'<p>'.G::L('有些错误为你直接操作数据中的记录，而不是通过我们的程序来做的。','comment').'</p>'.
				'<p>'.G::L('还有些错误是因为网络的原因，造成数据更新错误。','comment').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function build(){
		$this->display();
	}

	public function repaire_comment(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$sType=G::getGpc('type');
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		$arrMap['comment_relationtype']=array('neq','');
		$arrMap['comment_relationvalue']=array('neq','');
		if($nStart && $nEnd){
			$arrMap['comment_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['comment_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['comment_id']=array('elt',$nEnd);
		}
		switch($sType){
			case 'taotao':
				$arrMap['comment_relationtype']='taotao';
				break;
			case 'upload':
				$arrMap['comment_relationtype']='upload';
				break;
			case 'blog':
				$arrMap['comment_relationtype']='blog';
				break;
		}
		$oComment=CommentModel::F();
		if(!$nTotal){
			$nTotal=CommentModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrComments=CommentModel::F()->where($arrMap)->all()->order('`comment_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrRecords=array();
		foreach($arrComments as $oComment){
			if($oComment['comment_parentid']!=0){
				$oTheParentComment=CommentModel::F('comment_id=?',$oComment['comment_parentid'])->query();
				if(empty($oTheParentComment['comment_id'])){
					$oComment->comment_parentid=0;
					$oComment->save(0,'update');
					if($oComment->isError()){
						$this->E($oComment->getErrorMessage());
					}
					$arrRecords[]=$oComment->comment_id;
					$nActionNum++;
				}
			}
			$nCount++;
		}

		if($nTotal>0){
			$nPercent=ceil(($nCount/$nTotal)*100);
		}
		else{
			$nPercent=100;
		}
		$nBarlen=$nPercent * 4;
		$sUrl=G::U("comment/repaire_comment?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}&type={$sType}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理评论成功记录','comment')."</h3><br/>";
			$sRecords.=implode('<br/>',$arrRecords);
			$sRecords.="</div>";
		}
		if($nTotal>$nCount){
			$sResultInfo.=G::L("生成进度")."<br /><div style=\"margin:auto;height:25px;width:400px;background:#FFFAF0;border:1px solid #FFD700;text-align:left;\"><div style=\"background:red;width:{$nBarlen}px;height:25px;\">&nbsp;</div></div>{$nPercent}%";
			$this->assign('__JumpUrl__',$sUrl);
			$this->assign('__WaitSecond__',2);
			$this->S($sResultInfo.$sRecords);
		}
		else{
			$sResultInfo=G::L("任务完成，共处理 <b>%d</b>个文件。",'app',null,$nActionNum)."<br>".G::L("5秒后系统自动跳转...");
			$this->assign('__JumpUrl__',G::U('comment/build'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sRecords);
		}
	}

}
