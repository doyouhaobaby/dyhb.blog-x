<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	导航条排序控制器($)*/

!defined('DYHB_PATH') && exit;

class MenusortController extends InitController{

	public $_arrPages=array();

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('菜单管理可以对菜单进行修改和排序操作。','menusort').'</p>'.
				'<p>'.G::L('点击“自定义新的菜单”可以新增一个菜单。','menusort').'</p>'.
				'<p>'.G::L('系统内置了几个菜单，页面菜单也会被写过来。','menusort').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrNormalMenusTrue=unserialize($this->_arrOptions['normal_menu']);
		$this->assign('arrNormalMenusTrue',$arrNormalMenusTrue);
		$arrSystemMenus=array(
			'tag'=>G::L('标签云'),
			'link'=>G::L('友情衔接'),
			'search'=>G::L('搜索'),
			'record'=>G::L('日志归档'),
			'upload'=>G::L('我的相册'),
			'taotao'=>G::L('微博客'),
			'guestbook'=>G::L('留言板'),
		);
		$this->assign('arrSystemMenus',$arrSystemMenus);
		$this->assign('arrNormalMenuOptions',E::mbUnserialize($this->_arrOptions['normal_menu_option']));
		$this->assign('arrCustomNormalMenuOptions',E::mbUnserialize($this->_arrOptions['custom_normal_menu']));
		$arrSystemPages=BlogModel::F('blog_isshow=? AND blog_ispage=1',1)->all()->query();
		$arrPages=array();
		foreach($arrSystemPages as $oSystemPage){
			$arrPages['system_page_'.$oSystemPage->blog_id]=$oSystemPage;
		}
		$this->_arrPages=$arrPages;
		$this->assign('arrPages',$arrPages);
		$this->display();
	}

	public function get_menu_name($sMenuName){
		$bCustom=strpos($sMenuName,'custom_menu_')===0?true:false;//是否为自定义菜单
		$bPage=strpos($sMenuName,'system_page_')===0?true:false;//是否为页面
		$arrNormalMenuOptions=E::mbUnserialize($this->_arrOptions['normal_menu_option']);
		if($bCustom===false && $bPage===false){
			return $arrNormalMenuOptions[$sMenuName]['title'];
		}
		if($bCustom===true){
			$arrCustomNormalMenu=unserialize($this->_arrOptions['custom_normal_menu']);
			$sTitle=(isset($arrCustomNormalMenu[$sMenuName]['title']))? $arrCustomNormalMenu[$sMenuName]['title']:'';	//获取自定义菜单标题
			if(empty($sTitle)){
				preg_match("/^custom_menu_(\d+)/",$sTitle,$arrMatches);
				$sTitle='noname('.$arrMatches[1].')';
			}
		}
		if($bPage===true){
			$oPage=$this->_arrPages[$sMenuName];
			$sTitle=$oPage->blog_title;
		}
		return $sTitle;
	}

	public function save_menu(){
		$sMenuType=G::getGpc('menu','G');
		$sTitle=G::getGpc('title','P');
		$sDescription=G::getGpc('description','P');
		$sLink=G::getGpc('link','P');
		$arrStyle=G::getGpc('style','P');
		$nColor=G::getGpc('color','P');
		$nTarget=G::getGpc('target','P');
		$arrNormalMenuOptions=E::mbUnserialize($this->_arrOptions['normal_menu_option']);
		if(in_array($sMenuType,array('tag','link','search','guestbook','taotao','upload','record'))){
			$arrNormalMenuOptions[$sMenuType]['title']=($sTitle?$sTitle:'noname');
			$arrNormalMenuOptions[$sMenuType]['description']=$sDescription;
			$arrNormalMenuOptions[$sMenuType]['link']=$sLink;
			$arrNormalMenuOptions[$sMenuType]['style']=(!empty($arrStyle)?$arrStyle:array());
			$arrNormalMenuOptions[$sMenuType]['color']=$nColor;
			$arrNormalMenuOptions[$sMenuType]['target']=$nTarget;
		}
		$bResult=OptionModel::uploadOption('normal_menu_option',serialize($arrNormalMenuOptions));
		if($bResult===true){
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('菜单更新成功了！'));
		}
		else{
			$this->E(G::L('菜单更新失败，请重试！'));
		}
	}

	public function add_menu(){
		$sTitle=G::getGpc('title','P');
		$sDescription=G::getGpc('description','P');
		$sLink=G::getGpc('link','P');
		$arrStyle=G::getGpc('style','P');
		$nColor=G::getGpc('color','P');
		$nTarget=G::getGpc('target','P');
		$arrCustomNormalMenu=unserialize($this->_arrOptions['custom_normal_menu']);
		$nI=0;
		$nMaxKey=0;
		if(is_array($arrCustomNormalMenu)){
			foreach($arrCustomNormalMenu as $nKey=>$arrVal){
				preg_match("/^custom_menu_(\d+)/",$nKey,$arrMatches);
				$nK=$arrMatches[1];
				if($nK>$nI){
					$nMaxKey=$nK;
				}
				$nI=$nK;
			}
		}
		$nCustomMenuIndex=$nMaxKey+1;
		$sCustomMenuIndex='custom_menu_'.$nCustomMenuIndex;
		$arrCustomNormalMenu[$sCustomMenuIndex]['title']=($sTitle?$sTitle:$sCustomMenuIndex);
		$arrCustomNormalMenu[$sCustomMenuIndex]['description']=$sDescription;
		$arrCustomNormalMenu[$sCustomMenuIndex]['link']=$sLink;
		$arrCustomNormalMenu[$sCustomMenuIndex]['style']=(!empty($arrStyle)?$arrStyle:array());
		$arrCustomNormalMenu[$sCustomMenuIndex]['color']=$nColor;
		$arrCustomNormalMenu[$sCustomMenuIndex]['target']=$nTarget;
		$arrCustomNormalMenu[$sCustomMenuIndex]['system']=0;
		$bResult=OptionModel::uploadOption('custom_normal_menu',serialize($arrCustomNormalMenu));
		if($bResult===true){
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('添加自定义菜单成功了！'));
		}
		else{
			$this->E(G::L('添加自定义菜单失败，请重试！'));
		}
	}

	public function update_menu(){
		$sMenuId=G::getGpc('menu_id','P');
		$sTitle=G::getGpc('title','P');
		$sDescription=G::getGpc('description','P');
		$sLink=G::getGpc('link','P');
		$arrStyle=G::getGpc('style','P');
		$nColor=G::getGpc('color','P');
		$nTarget=G::getGpc('target','P');

		$arrCustomNormalMenu=unserialize($this->_arrOptions['custom_normal_menu']);
		$arrCustomNormalMenu[$sMenuId]['title']=($sTitle?$sTitle:'noname');
		$arrCustomNormalMenu[$sMenuId]['description']=$sDescription;
		$arrCustomNormalMenu[$sMenuId]['link']=$sLink;
		$arrCustomNormalMenu[$sMenuId]['style']=(!empty($arrStyle)?$arrStyle:array());
		$arrCustomNormalMenu[$sMenuId]['color']=$nColor;
		$arrCustomNormalMenu[$sMenuId]['target']=$nTarget;
		$arrCustomNormalMenu[$sMenuId]['system']=0;

		$bResult=OptionModel::uploadOption('custom_normal_menu',serialize($arrCustomNormalMenu));
		if($bResult===true){
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('更新自定义菜单更新成功了！'));
		}
		else{
			$this->E(G::L('更新自定义菜单失败，请重试！'));
		}
	}

	public function delete_menu(){
		$sMenuType=G::getGpc('menu','G');
		$arrCustomNormalMenu=unserialize($this->_arrOptions['custom_normal_menu']);
		$arrNormalMenus=E::mbUnserialize($this->_arrOptions['normal_menu']);
		if(is_array($arrNormalMenus)&& !empty($arrNormalMenus)){
			foreach($arrNormalMenus as $nKey=>$sNormalMenu){
				if($sNormalMenu==$sMenuType){
					unset($arrNormalMenus[$nKey]);
				}
			}
			$bResult=OptionModel::uploadOption('normal_menu',serialize($arrNormalMenus));
			if($bResult===false)
				$this->E(G::L('从排序数据删除组件失败，请重试！'));
		}
		if(!empty($arrCustomNormalMenu)&& isset($arrCustomNormalMenu[$sMenuType])){
			unset($arrCustomNormalMenu[$sMenuType]);
		}
		$bResult=OptionModel::uploadOption('custom_normal_menu',serialize($arrCustomNormalMenu));
		if($bResult===true){
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('删除自定义菜单更新成功了！'));
		}
		else{
			$this->E(G::L('删除自定义菜单失败，请重试！'));
		}
	}

	public function compages(){
		$arrMenus=G::getGpc('menus','P');
		$bResult=OptionModel::uploadOption('normal_menu',serialize($arrMenus));
		if($bResult===true){
			Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			$this->S(G::L('排序菜单更新成功了！'));
		}
		else{
			$this->E(G::L('排序菜单失败，请重试！'));
		}
	}

	public function reset(){
		$bResult=OptionModel::uploadOption('normal_menu',serialize($this->get_default_menu()));
		if($bResult===false){
			$this->E(G::L('重置菜单排序失败，请重试！'));
		}
		$bResult=OptionModel::uploadOption('normal_menu_option',serialize($this->get_default_menu_option()));
		if($bResult===false){
			$this->E(G::L('重置菜单配置失败，请重试！'));
		}
		$bResult=OptionModel::uploadOption('custom_normal_menu','a:0:{}');
		if($bResult===false){
			$this->E(G::L('重置自定义菜单失败，请重试！'));
		}
		Model::C('menu',null,array('cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
		$this->S(G::L('重置菜单数据成功！'));
	}

	protected function get_default_menu(){
		return array(
			0=>"tag",
			1=>"link",
			2=>"search",
			3=>"record",
			4=>"upload",
			5=>"taotao",
			6=>"guestbook",
		);
	}

	protected function get_default_menu_option(){
		return array(
			"tag"=>array(
				"title"=> G::L("标签云"),
				"description"=>G::L("标签云在前台列出所有标签，方便浏览。"),
				"link"=>"#",
				"style"=>NULL,
				"color"=> 0,
				"target"=>0,
				"system"=>1,
			),
			"link"=>array(
				"title"=>G::L("友情衔接"),
				"description"=> G::L("列出合作伙伴或者朋友的网站。"),
				"link"=> "#",
				"style"=>NULL,
				"color"=> 0,
				"target"=> 0,
				"system"=>1,
			),
			"search"=>array(
				"title"=>G::L("搜索"),
				"description"=>G::L("搜索页面"),
				"link"=>"#",
				"style"=>NULL,
				"color"=>0,
				"target"=>0,
				"system"=>1,
			),
			"record"=>array(
				"title"=>G::L("日志归档"),
				"description"=> G::L("归档日志，方便查找。"),
				"link"=>"#",
				"style"=>NULL,
				"color"=>0,
				"target"=>0,
				"system"=>1,
			),
			"upload"=>array(
				"title"=>G::L("我的相册"),
				"description"=>G::L("列出上传的相片等"),
				"link"=>"#",
				"style"=>NULL,
				"color"=>0,
				"target"=>0,
				"system"=>1,
			),
			"taotao"=>array(
				"title"=>G::L("微博客"),
				"description"=>G::L("放飞你的心声。"),
				"link"=>"#",
				"style"=>NULL,
				"color"=>0,
				"target"=>0,
				"system"=>1,
			),
			"guestbook"=>array(
				"title"=>G::L("留言板"),
				"description"=>G::L("方便访客与你交流。"),
				"link"=>"#",
				"style"=>NULL,
				"color"=>"0",
				"target"=>"0",
				"system"=>1,
			)
		);
	}

}
