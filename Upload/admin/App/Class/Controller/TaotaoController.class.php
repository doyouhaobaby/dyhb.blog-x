<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	微博管理控制器($)*/

!defined('DYHB_PATH') && exit;

class TaotaoController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['taotao_content']=array('like',"%".G::getGpc('taotao_content')."%");
		$nUserId=G::getGpc('uid');
		if(!empty($nUserId)){
			$arrMap['user_id']=$nUserId;
		}
	}

	public function taotao(){
		$this->assign('arrOptions',$this->_arrOptions);
		$nTotalRecord=TaotaoModel::F()->all()->getCounts();
		$nEverynum=$this->_arrOptions['admineverynum'];
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page'));
		$sPageNavbar=$oPage->P();
		$arrTaotaoList=TaotaoModel::F()->all()->order('`taotao_id` DESC')->limit($oPage->returnPageStart(),$nEverynum)->query();
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('arrTaobaoList',$arrTaotaoList);
		$this->display();
	}

	public function set(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			if($sKey=='is_show_taotao' && empty($val))
			$val=0;
			$val=trim($val);
			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}
		Cache_Extend::global_option('admin');
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_taotao();
		$this->S(G::L('微博配置更新成功了！'));
	}

	public function _taotao_get_admin_help_description(){
		return $this->_index_get_admin_help_description();
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('微博，即微博客（MicroBlog）的简称，是一个基于用户关系的信息分享、传播以及获取平台，用户可以通过WEB、WAP以及各种客户端组件个人社区，以140字左右的文字更新信息，并实现即时分享。','taotao').'</p>'.
				'<p>'.G::L('本博客软件集成微博，如果发布一些比较简单的信息，可以不用写一篇日志。','taotao').'</p>'.
				'<p>'.G::L('通过本页，你可以批量删除微博，这样可以大大提高效率。','taotao').'</p>'.
				'<p>'.G::L('微博以两种方式呈现，你可以点击微博样式查看。','taotao').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('你可以添加微博，在表单调好数据后，点击发表即可。','taotao').'</p>'.
				'<p>'.G::L('按照国际惯例，微博只能删除，不可以编辑。','taotao').'</p>'.
				'<p>'.G::L('你可以在微博中填上表情，为微博内容增加颜色。','taotao').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	protected function aInsert($nId){
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_taotao();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_widget_taotao();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_widget_static();
		Cache_Extend::front_widget_taotao();
	}

	public function _build_get_admin_help_description(){
		return '<p>'.G::L('心情数据在处理过程中难免会出现错误，这个时候我们需要修复错误。','taotao').'</p>'.
				'<p>'.G::L('有些错误为你直接操作数据中的记录，而不是通过我们的程序来做的。','taotao').'</p>'.
				'<p>'.G::L('还有些错误是因为网络的原因，造成数据更新错误。','taotao').'</p>'.
				'<p>'.G::L('我们在操作过程中评论有可能出现错误统计，这个时候可以修复它。','taotao').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function build(){
		$this->display();
	}

	public function repaire_taotao(){
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
			$arrMap['taotao_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['taotao_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['taotao_id']=array('elt',$nEnd);
		}

		if(!$nTotal){
			$nTotal=TaotaoModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrTaotaos=TaotaoModel::F()->where($arrMap)->all()->order('`taotao_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrRecords=array();
		foreach($arrTaotaos as $oTaotaos){
			$nNum=0;
			$nNum=$this->repaire_blog_comment($oTaotaos);
			$arrRecords[]=$oTaotaos->taotao_id.'-'.$nNum;
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
		$sUrl=G::U("taotao/repaire_taotao?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('整理心情成功记录','taotao')."</h3><br/>";
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
			$this->assign('__JumpUrl__',G::U('taotao/build'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sRecords);
		}
	}

	protected function repaire_blog_comment($oTaotaos){
		$nNum=CommentModel::F('`comment_relationtype`=\'taotao\' AND `comment_relationvalue`=?',$oTaotaos->taotao_id)->all()->getCounts();
		if($nNum<0){
			$nNum=0;
		}
		$oTaotaos->taotao_commentnum=$nNum;
		$oTaotaos->save(0,'update');
		return $nNum;
	}

	public function lock(){
		$nTaotaoId=intval(G::getGpc('id','G'));
		if($nTaotaoId){
			$oTaotao=TaotaoModel::F('taotao_id=?',$nTaotaoId)->query();
			$oTaotao->taotao_islock=1;
			$oTaotao->save(0,'update');
			if($oTaotao->isError()){
				$this->E($oTaotao->getErrorMessage());
			}
			else{
				$this->S(G::L('锁定滔滔心情成功了'));
			}
		}
		else{
			$this->E(G::L('你没有指定待锁定的心情'));
		}
	}

	public function un_lock(){
		$nTaotaoId=intval(G::getGpc('id','G'));
		if($nTaotaoId){
			$oTaotao=TaotaoModel::F('taotao_id=?',$nTaotaoId)->query();
			$oTaotao->taotao_islock=0;
			$oTaotao->save(0,'update');
			if($oTaotao->isError()){
				$this->E($oTaotao->getErrorMessage());
			}
			else{
				$this->S(G::L('解锁滔滔心情成功了'));
			}
		}
		else{
			$this->E(G::L('你没有指定待解锁的心情'));
		}
	}

}
