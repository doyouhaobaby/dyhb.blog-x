<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	不良词语控制器($)*/

!defined('DYHB_PATH') && exit;

class BadwordController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['badword_find']=array('like',"%".G::getGpc('badword_find')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('在天朝这个和谐社会你随时准备着被和谐吧。','badword').'</p>'.
				'<p>'.G::L('为了更加方便地管理信息，防止发了和谐词语，特别加入词语过滤系统。','badword').'</p>'.
				'<p>'.G::L('你可以直接点击和谐词语，将会生产表单，你可以编辑。','badword').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function _add_get_admin_help_description(){
		return '<p>'.G::L('你可以使用添加单个过滤词语，也可以批量添加过滤词语。','badword').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function many_insert(){
		$sBadwords=G::getGpc('badwords','P');
		if($sBadwords==''){
			$this->E(G::L('导入词汇不能为空！'));
		}
		$nType=G::getGpc('type','P');
		$oBadword=BadwordModel::F()->query();
		if($nType==0){
			$bResult=$oBadword->truncateBadword();
			if($bResult===false){
				$this->E(G::L('清空badword数据库出错！'));
			}
			$nType=1;
		}
		$arrValue=explode("\n",str_replace(array("\r","\n\n"),array("\r","\n"),$sBadwords));
		foreach($arrValue as $sValue){
			$arrValueTwo=explode("=",$sValue);
			$arrUserData=UserModel::M()->userData();
			if(!isset($arrValueTwo[1]))$arrValueTwo[1]='*';
			$bResult=$oBadword->addBadword($arrValueTwo[0],$arrValueTwo[1],$arrUserData['user_name'],$nType);
			if($bResult===false){
				$this->E(G::L('插入出错！').'<br/>'.$oBadword->getErrorMessage());
			}
		}
		$this->S(G::L('导入数据成功了'));
	}

	public function export(){
		$arrDatas=BadwordModel::F()->asArray()->all()->query();
		$sString='';
		if($arrDatas){
			foreach($arrDatas as $arrData){
				$sString.=$arrData['badword_find'].'='.$arrData['badword_replacement']."\r\n";
			}
		}
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment;filename=Badwords.txt');
		if(preg_match("/MSIE([0-9].[0-9]{1,2})/",$_SERVER['HTTP_USER_AGENT'])){
			header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
			header('Pragma: public');
		}
		else{
			header('Pragma: no-cache');
		}
		echo $sString;
	}

	protected function aInsert($nId){
		Cache_Extend::front_badword();
	}

	protected function aUpdate($nId){
		Cache_Extend::front_badword();
	}

	public function aForeverdelete($sId){
		Cache_Extend::front_badword();
	}

}
