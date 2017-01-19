<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	留言控制器($)*/

!defined('DYHB_PATH') && exit;

class GuestbookController extends CommentController{

	public function filter_(&$arrMap){
		$arrMap['comment_name']=array('like',"%".G::getGpc('comment_name')."%");
		$arrMap['comment_relationtype']='';
		$arrMap['comment_relationvalue']='';
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('留言和评论基本一致，你可以在这里进行批量操作。','comment').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _edit_get_admin_help_description(){
		return '<p>'.G::L('这里你可以修改留言信息。','comment').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function edit(){
		$nId=G::getGpc('id','G');
		if(!empty($nId)){
			$oModel=CommentModel::F('comment_id=?',$nId)->query();
			if(!empty($oModel['comment_id'])){
				$this->assign('oValue',$oModel);
				$this->assign('nId',$nId);
				$this->display('guestbook+add');
			}
			else{
				$this->E(G::L('数据库中并不存在该留言，或许它已经被删除了！'));
			}
		}
		else{
			$this->E(G::L('编辑项不存在！'));
		}
	}

	public function index(){
		$arrMap=$this->map();
		if(method_exists($this,'filter_')){
			$this->filter_($arrMap);
		}
		$this->get_list($arrMap);
		$this->display();
	}

	protected function get_list($arrMap){
		$sParameter='';
		$arrSortUrl=array();
		$nTotalRecord=CommentModel::F()->where($arrMap)->all()->getCounts();
		$nEverynum=$this->_arrOptions['admineverynum'];
		foreach($arrMap as $sKey=>$sVal){
			if(!is_array($sVal)){
				$sParameter.=$sKey.'='.urlencode($sVal).'&';
				$arrSortUrl[]='"'.$sKey.'='.urlencode($sVal).'"';
			}
		}
		$sSortBy=strtoupper(G::getGpc('sort_'))=='ASC'?'ASC':'DESC' ;
		$sOrder=G::getGpc('order_')? G::getGpc('order_'): 'comment_id';
		$this->assign('sSortByUrl',strtolower($sSortBy)=='desc'? 'asc':'desc');
		$this->assign('sSortByDescription',strtolower($sSortBy)=='desc'?G::L('倒序'): G::L('升序'));
		$this->assign('sOrder',$sOrder);
		$this->assign('sSortUrl','new Array('.implode(',',$arrSortUrl).')');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page'));
		$oPage->setParameter($sParameter);
		$sPageNavbar=$oPage->P();
		$oList=CommentModel::F()->where($arrMap)->all()->order($sOrder.' '.$sSortBy)->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('oList',$oList);
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
	}

	protected function aForeverdelete($sId){
		Cache_Extend::front_widget_guestbook();
		Cache_Extend::front_widget_static();
	}

	public function repaire_comment(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		$arrMap['comment_relationtype']='';
		$arrMap['comment_relationvalue']='';
		if($nStart && $nEnd){
			$arrMap['comment_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['comment_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['comment_id']=array('elt',$nEnd);
		}

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
		$nBarlen=$nPercent*4;
		$sUrl=G::U("guestbook/repaire_comment?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理留言成功记录','comment')."</h3><br/>";
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
