<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	更新论坛统计数据控制器($)*/

!defined('DYHB_PATH') && exit;

class BoarddataController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('程序运行过程中可能会发生错误，你在这里可以从新更新数据。','boarddata').'</p>'.
				'<p>'.G::L('循环处理是为了防止处理数据数据过大而崩溃。','boarddata').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$this->display();
	}

	public function rebuild_count(){
		$sType=G::getGpc('type');
		if(!empty($_POST)){
			if(isset($_POST['forumsubmit'])){
				$sType='forum';
			}
			if(isset($_POST['membersubmit'])){
				$sType='member';
			}
			if(isset($_POST['threadsubmit'])){
				$sType='thread';
			}
		}

		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=50;
		!$nCount && $nCount=0;
		$arrMap=array();
		switch($sType){
			case 'forum':
				if($nStart && $nEnd){
					$arrMap['category_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
				}
				elseif($nStart){
					$arrMap['category_id']=array('egt',$nStart);
				}
				elseif($nEnd){
					$arrMap['category_id']=array('elt',$nEnd);
				}
				if(!$nTotal){
					$nTotal=CategoryModel::F()->where($arrMap)->all()->getCounts('category_id');
				}
				$arrDatas=CategoryModel::F()->where($arrMap)->all()->order('`category_id` ASC')->limit($nCount,$nPagesize)->query();
				break;
			case 'member':
				if($nStart && $nEnd){
					$arrMap['user_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
				}
				elseif($nStart){
					$arrMap['user_id']=array('egt',$nStart);
				}
				elseif($nEnd){
					$arrMap['user_id']=array('elt',$nEnd);
				}

				if(!$nTotal){
					$nTotal=UserModel::F()->where($arrMap)->all()->getCounts('user_id');
				}
				$arrDatas=UserModel::F()->where($arrMap)->all()->order('`user_id` ASC')->limit($nCount,$nPagesize)->query();
				break;
			case 'thread':
				if($nStart && $nEnd){
					$arrMap['blog_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
				}
				elseif($nStart){
					$arrMap['blog_id']=array('egt',$nStart);
				}
				elseif($nEnd){
					$arrMap['blog_id']=array('elt',$nEnd);
				}

				if(!$nTotal){
					$nTotal=BlogModel::F()->where($arrMap)->all()->getCounts('blog_id');
				}
				$arrDatas=BlogModel::F()->where($arrMap)->all()->order('`blog_id` ASC')->limit($nCount,$nPagesize)->query();
				break;
		}

		$arrRecords=array();
		foreach($arrDatas as $oDatas){
			$nNum=0;
			switch($sType){
				case 'forum':
					$this->rebuild_forum($oDatas);
					$arrRecords[]=$oDatas['category_id'];
					break;
				case 'member':
					$this->rebuild_member($oDatas);
					$arrRecords[]=$oDatas['user_id'];
					break;
				case 'thread':
					$this->rebuild_thread($oDatas);
					$arrRecords[]=$oDatas['blog_id'];
					break;
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
		$sUrl=G::U("boarddata/rebuild_count?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}&type={$sType}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理论坛成功记录','boarddata')."</h3><br/>";
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
			$this->assign('__JumpUrl__',G::U('boarddata/index'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sRecords);
		}
	}

	public function rebuild_forum($oDatas){
		$oDatas->category_blogs=BlogModel::F('category_id=?',$oDatas['category_id'])->all()->getCounts();
		$oDatas->category_comments=CommentModel::F('category_id=?',$oDatas['category_id'])->all()->getCounts();
		$oDatas->save(0,'update');
		if($oDatas->isError()){
			$this->E($oDatas->getErrorMessage());
		}
	}

	public function rebuild_member($oDatas){
		$oDatas->user_blogs=BlogModel::F('user_id=?',$oDatas['user_id'])->all()->getCounts();
		$oDatas->user_comments=CommentModel::F('user_id=?',$oDatas['user_id'])->all()->getCounts();
		$oDatas->save(0,'update');
		if($oDatas->isError()){
			$this->E($oDatas->getErrorMessage());
		}
	}

	public function rebuild_thread($oDatas){
		$oDatas->blog_commentnum=CommentModel::F('comment_relationtype=\'blog\' AND comment_relationvalue=?',$oDatas['blog_id'])->all()->getCounts();
		$oDatas->save(0,'update');
		if($oDatas->isError()){
			$this->E($oDatas->getErrorMessage());
		}
	}

}
