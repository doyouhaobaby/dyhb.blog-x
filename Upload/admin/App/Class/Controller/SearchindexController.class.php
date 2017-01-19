<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	搜索记录管理控制器($)*/

!defined('DYHB_PATH') && exit;

class SearchindexController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['searchindex_keywords']=array('like',"%".G::getGpc('searchindex_keywords')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('这里是搜索记录管理面板，你可以管理网站搜索记录。','searchindex').'</p>'.
				'<p>'.G::L('清空搜索记录可以提高系统的运行速度。','searchindex').'</p>'.
				'<p>'.G::L('当然，最好还是保存搜索记录，这样你可以统计站点的用户感兴趣的事。','searchindex').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _clear_get_display_sceen_options(){
		return true;
	}

	public function _clear_get_sceen_options_value(){
		return "<input type='text' class='field' name='configs[admin_searchindex_file_everynum]' id='admin_searchindex_file_everynum' maxlength='10' value='".$this->_arrOptions['admin_searchindex_file_everynum']."' /> <label for='admin_searchindex_file_everynum'>".G::L("后台每页搜索导出文件显示数量")."</label>
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function clear(){
		$sAppPath=APP_PATH.'/App/Data/Search';
		$nEverynum=$this->_arrOptions['admin_searchindex_file_everynum'];
		$oPage=IoPage::RUN($sAppPath,$nEverynum,G::getGpc('page'),array('type'=>2));
		$this->assign('sPageNavbar',$oPage->P());
		$this->assign('arrData',$oPage->getCurrentData());
		$this->display();
	}

	public function export(){
		$nPagesize=intval(G::getGpc('pagesize'));
		$nTotal=intval(G::getGpc('total'));
		$nCount=intval(G::getGpc('count'));
		$nStart=intval(G::getGpc('start'));
		$nEnd=intval(G::getGpc('end'));
		$nActionNum=intval(G::getGpc('action_num'));
		!$nPagesize && $nPagesize=200;
		!$nCount && $nCount=0;
		$arrMap=array();
		if($nStart && $nEnd){
			$arrMap['searchindex_id']=array(array('egt',$nStart),array('elt',$nEnd),'and');
		}
		elseif($nStart){
			$arrMap['searchindex_id']=array('egt',$nStart);
		}
		elseif($nEnd){
			$arrMap['searchindex_id']=array('elt',$nEnd);
		}

		if(!$nTotal){
			$nTotal=SearchindexModel::F()->where($arrMap)->all()->getCounts();
		}
		$arrSearchindexs=SearchindexModel::F()->where($arrMap)->all()->order('`searchindex_id` ASC')->limit($nCount,$nPagesize)->query();
		$arrRecords=array();
		$this->export_one_data($arrSearchindexs);
		foreach($arrSearchindexs as $oSearchindexs){
			$arrRecords[]=$oSearchindexs->searchindex_id;
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
		$sUrl=G::U("searchindex/export?pagesize={$nPagesize}&start={$nStart}&end={$nEnd}&total={$nTotal}&count={$nCount}&action_num={$nActionNum}");
		$sResultInfo='';
		$sRecords='';
		if(!empty($arrRecords)){
			$sRecords="<br/><div style=\"color:green;\"><h3>".G::L('导出搜索成功记录','searchindex')."</h3><br/>";
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
			$this->assign('__JumpUrl__',G::U('searchindex/clear'));
			$this->assign('__WaitSecond__',5);
			$this->S($sResultInfo.$sRecords);
		}
	}

	public function export_one_data($arrSearchindexs){
		$sData="-- DYHB.BLOG X SearchIndex Dump\r\n";
		$sData.="-- version Blog " .BLOG_SERVER_VERSION. "  Release " .BLOG_SERVER_RELEASE."\r\n";
		$sData.="-- http://doyouhaobaby.net\r\n";
		$sData.="--\r\n";
		$sData.="-- ".G::L("主机").": ".$this->_arrOptions['blog_url']."\r\n";
		$sData.="-- ".G::L("生成日期").": ".date('Y-m-d H:i:s',CURRENT_TIMESTAMP)."\r\n\r\n";
		$sData.="------------------------------------------------------------------------\r\n";
		foreach($arrSearchindexs as $oSearchindexs){
			$sData.=G::L('编号','searchindex').": ".$oSearchindexs->searchindex_id."\r\n";
			$sData.=G::L('关键字','searchindex').": ".$oSearchindexs->searchindex_keywords."\r\n";
			$sData.=G::L('搜索时间','searchindex').": ".date('Y-m-d',$oSearchindexs->create_dateline)."\r\n";
			$sData.=G::L('搜索结果','searchindex').": ".$oSearchindexs->searchindex_totals."\r\n";
			$sData.=G::L('搜索范围','searchindex').": ".$oSearchindexs->searchindex_searchfrom."\r\n";
			$sData.=G::L('IP 地址','searchindex').": ".$oSearchindexs->searchindex_ip."\r\n";
			$sData.="-----------------------------------\r\n\r\n";
		}
		$sFilePath=APP_PATH.'/App/Data/Search/Searchindex_'.date('Y-m-d_H-i-s',CURRENT_TIMESTAMP).'.txt';
		if(!file_put_contents($sFilePath,$sData)){
			$this->E(G::L("搜索记录文件%s不可写，请设置该目录的权限为777",'app',null,$sFilePath));
		}
	}

	public function _clear_get_admin_help_description(){
		return '<p>'.G::L('清理搜索记录可以提高效率和准确性。','searchindex').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function clear_all(){
		$oDb=Db::RUN();
		$sSql="TRUNCATE ".PmModel::F()->query()->getTablePrefix()."searchindex";
		$oDb->query($sSql);
		$this->assign('__JumpUrl__',G::U('searchindex/index'));
		$this->S(G::L('清空搜索数据成功'));
	}

	public function delete_file(){
		$sName=G::getGpc('name','G');
		if(empty($sName)){
			$this->E(G::L('你没有指定要删除的搜索文件'));
		}
		$sFilePath =APP_PATH.'/App/Data/Search/'.ucfirst($sName);
		if(!file_exists($sFilePath))
			$this->E(G::L('待删除的搜索文件%s不存在','app',null,$sFilePath));
		else{
			if(!unlink($sFilePath)){
				$this->E(G::L('删除文件%s失败','app',null,$sFilePath));
			}
			else{
				$this->S(G::L('删除文件%s成功','app',null,$sFilePath));
			}
		}
	}

	public function remove(){
		$arrFilename=G::getGpc('filename','P');
		if(empty($arrFilename)){
			$this->E(G::L('你没有指定要删除的搜索文件'));
		}
		$sResult='';
		foreach($arrFilename as $sFilename){
			$sFilePath =APP_PATH.'/App/Data/Search/'.ucfirst($sFilename);
			if(!file_exists($sFilePath)){
				$sResult.=G::L('待删除的搜索文件%s不存在','app',null,$sFilePath).'<br/>';
			}
			else{
				if(!unlink($sFilePath)){
					$sResult.=G::L('删除文件%s失败','app',null,$sFilePath).'<br/>';
				}
				else{
					$sResult.=G::L('删除文件%s成功','app',null,$sFilePath).'<br/>';
				}
			}
		}
		$this->assign('__WaitSeccond__',10);
		$this->S(G::L('删除搜索文件操作成功了').'<br/>'.G::L('附件信息')."<br/>".$sResult);
	}

}
