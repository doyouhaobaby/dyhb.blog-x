<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	Widget排序控制器($)*/

!defined('DYHB_PATH') && exit;

class WidgetController extends InitController{

	public function _index_get_display_sceen_options(){
		return false;
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('Widget管理可以对Widget进行修改和排序操作。','widget').'</p>'.
				'<p>'.G::L('点击“自定义新的widget”可以新增一个Widget。','widget').'</p>'.
				'<p>'.G::L('系统内置了二十几个Widget，轻松帮助搞定一个功能强大的博客。','widget').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function index(){
		$arrSystemWidgets=$this->get_system_widgets();
		$sWidgetName=$this->get_current_widget_name();
		$this->assign('sCurrentWidget',$sWidgetName);
		$arrWidgetsTrue=E::mbUnserialize($this->_arrOptions[$sWidgetName]);
		$this->assign('arrWidgetsTrue',$arrWidgetsTrue);
		$this->assign('arrOptions',$this->_arrOptions);
		$this->assign('sTagColorLists',@implode('|',@E::mbUnserialize($this->_arrOptions['widget_hottag_color'])));// 标签颜色处理
		$this->assign('sUploadcategoryFixedsize',implode('|',E::mbUnserialize($this->_arrOptions['widget_uploadcategory_fixed_size'])));// 分类封面大小处理
		$this->assign('sRecentimageImageSize',implode('|',E::mbUnserialize($this->_arrOptions['widget_recentimage_image_size'])));// 最新图片falsh大小处理
		$this->assign('sMenuWidgetClass',implode('|',E::mbUnserialize($this->_arrOptions['widget_menu_widget_class'])));// 菜单widget css
		$this->assign('arrCustomWidgetOptions',E::mbUnserialize($this->_arrOptions['custom_widget_option']));
		$this->display();
	}

	public function get_current_widget_name(){
		$arrSystemWidgets=$this->get_system_widgets();
		$sWidgetName=G::getGpc('widget','G');
		$sWidgetName=!empty($sWidgetName)&& in_array($sWidgetName,$arrSystemWidgets)? $sWidgetName:'widgets_main1';
		return $sWidgetName;
	}

	public function get_system_widgets(){
		return array('widgets_main1','widgets_main2','widgets_main3','widgets_main4','widgets_main5',
					'widgets_main6','widgets_main7','widgets_main8','widgets_main9','widgets_main10',
					'widgets_footer1','widgets_footer2','widgets_footer3','widgets_footer4','widgets_footer5',
					'widgets_footer6','widgets_footer7','widgets_footer8','widgets_footer9','widgets_footer10',
					'widgets_store',);
	}

	public function get_widget_name($sWidgetName){
		$bCustom=strpos($sWidgetName,'custom_widget_')===0?true:false;//是否为自定义菜单
		if($bCustom===false)
			return $this->_arrOptions["widget_{$sWidgetName}_name"];

		$arrCustomWidgetOptions=E::mbUnserialize($this->_arrOptions['custom_widget_option']);
		$sTitle=(isset($arrCustomWidgetOptions[$sWidgetName]['title']))? $arrCustomWidgetOptions[$sWidgetName]['title']:'';	//获取自定义菜单标题
		if(empty($sTitle)){
			preg_match("/^custom_widget_(\d+)/",$sTitle,$arrMatches);
			$sTitle='noname('.$arrMatches[1].')';
		}
		return $sTitle;
	}


	public function save_widget(){
		$sWidgetType=G::getGpc('widget','G');
		$arrConfigs=G::getGpc('configs','P');
		if($sWidgetType=='hottag'){$arrConfigs['widget_hottag_color']=serialize(explode('|',$arrConfigs['widget_hottag_color']));}
		if($sWidgetType=='uploadcategory'){$arrConfigs['widget_uploadcategory_fixed_size']=serialize(explode('|',$arrConfigs['widget_uploadcategory_fixed_size']));}
		if($sWidgetType=='recentimage'){$arrConfigs['widget_recentimage_image_size']=serialize(explode('|',$arrConfigs['widget_recentimage_image_size']));}
		if($sWidgetType=='menu'){$arrConfigs['widget_menu_widget_class']=serialize(explode('|',$arrConfigs['widget_menu_widget_class']));}
		foreach($arrConfigs as $sConfigKey=>$sConfigValue){
			$bResult=OptionModel::uploadOption($sConfigKey,$sConfigValue);
			if($bResult===false){$this->E(G::L('widget更新失败，请重试！'));}
		}
		$this->S(G::L('widget更新成功了！'));
	}

	public function add_widget(){
		$sTitle=G::getGpc('title','P');
		$sContent=G::getGpc('content','P');
		$nTitleShow=G::getGpc('titleshow','P');
		$arrCustomWidgetOption=unserialize($this->_arrOptions['custom_widget_option']);
		$nI=0;
		$nMaxKey=0;
		if(is_array($arrCustomWidgetOption)){
			foreach($arrCustomWidgetOption as $nKey=>$arrVal){
				preg_match("/^custom_widget_(\d+)/",$nKey,$arrMatches);
				$nK=$arrMatches[1];
				if($nK>$nI){
					$nMaxKey=$nK;
				}
				$nI=$nK;
			}
		}
		$nCustomWidgetIndex=$nMaxKey+1;
		$sCustomWidgetIndex='custom_widget_'.$nCustomWidgetIndex;
		$arrCustomWidgetOption[$sCustomWidgetIndex]['title']=($sTitle?$sTitle:$sCustomWidgetIndex);
		$arrCustomWidgetOption[$sCustomWidgetIndex]['content']=$sContent;
		$arrCustomWidgetOption[$sCustomWidgetIndex]['titleshow']=$nTitleShow;
		$bResult=OptionModel::uploadOption('custom_widget_option',serialize($arrCustomWidgetOption));
		if($bResult===true){
			$this->S(G::L('添加自定义Widget成功了！'));
		}
		else{
			$this->E(G::L('添加自定义Widget失败，请重试！'));
		}
	}

	public function update_widget(){
		$sWidgetId=G::getGpc('widget_id','P');
		$sTitle=G::getGpc('title','P');
		$sContent=G::getGpc('content','P');
		$nTitleShow=G::getGpc('titleshow','P');
		$arrCustomWidget=unserialize($this->_arrOptions['custom_widget_option']);
		$arrCustomWidget[$sWidgetId]['title']=($sTitle?$sTitle:'noname');
		$arrCustomWidget[$sWidgetId]['content']=$sContent;
		$arrCustomWidget[$sWidgetId]['titleshow']=$nTitleShow;
		$bResult=OptionModel::uploadOption('custom_widget_option',serialize($arrCustomWidget));
		if($bResult===true){
			$this->S(G::L('更新自定义Widget更新成功了！'));
		}
		else{
			$this->E(G::L('更新自定义widget失败，请重试！'));
		}
	}

	public function delete_widget(){
		$sWidgetType=G::getGpc('name','G');
		$arrCustomWidgets=unserialize($this->_arrOptions['custom_widget_option']);
		$arrSystemWidgets=$this->get_system_widgets();
		foreach($arrSystemWidgets as $nSystemWidgetsKey=>$sSystemWidget){
			$arrWidgets=unserialize($this->_arrOptions[$sSystemWidget]);
			if(is_array($arrWidgets)&& !empty($arrWidgets)){
				foreach($arrWidgets as $nKey=>$sVal){
					if($sVal==$sWidgetType){
						unset($arrWidgets[$nKey]);
					}
				}
				$bResult=OptionModel::uploadOption($sSystemWidget,serialize($arrWidgets));
				if($bResult===false){
					$this->E(G::L('从排序数据删除组件失败，请重试！'));
				}
			}
		}

		if(!empty($arrCustomWidgets)&& isset($arrCustomWidgets[$sWidgetType])){
			unset($arrCustomWidgets[$sWidgetType]);
		}
		$bResult=OptionModel::uploadOption('custom_widget_option',serialize($arrCustomWidgets));
		if($bResult===true){
			$this->S(G::L('删除自定义widget更新成功了！'));
		}
		else{
			$this->E(G::L('删除自定义widget失败，请重试！'));
		}
	}

	public function compages(){
		$arrWidgets=G::getGpc('widgets','P');
		$sWidget=G::getGpc('widget','P');
		$bResult=OptionModel::uploadOption($sWidget,serialize($arrWidgets));
		if($bResult===true){
			$this->S(G::L('排序widget更新成功了！'));
		}
		else{
			$this->E(G::L('排序wiget失败，请重试！'));
		}
	}

	public function reset(){
		$sWidget=G::getGpc('widget','P');
		$sWidget=$sWidget?$sWidget:'widgets_main1';
		$bResult=OptionModel::uploadOption($sWidget,serialize($this->get_default_widget()));
		if($bResult===false){
			$this->E(G::L('重置widget排序失败，请重试！'));
		}
		$bResult=OptionModel::uploadOption('custom_widget_option','a:0:{}');
		if($bResult===false){
			$this->E(G::L('重置自定义widget失败，请重试！'));
		}
		$this->S(G::L('重置widget数据成功！'));
	}

	protected function get_default_widget(){
		return array(
			0=>"search",
			1=>"blog",
			2=>"admin",
			3=>"calendar",
			4=>"category",
			5=>"hottag",
			6=>"link",
			7=>"rss",
		);
	}

}
