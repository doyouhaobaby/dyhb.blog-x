<?php
/**

 //  The DoYouHaoBaby PHP FrameWork - websetup
 //  +---------------------------------------------------------------------
 //
 //  “Copyright”
 //  +---------------------------------------------------------------------
 //  | (C) 2010 - 2011 http://doyouhaobaby.net All rights reserved.
 //  | This is not a free software, use is subject to license terms
 //  +---------------------------------------------------------------------
 //
 //  “About This File”
 //  +---------------------------------------------------------------------
 //  | websetup SubmenuWidgget挂件（侧边栏导航条）
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

class SubmenuWidget extends Widget{

	public function render($arrData){
		$oMenu=new MenuModel();
		$arrData['menu']=$oMenu;
		$arrMain=$oMenu->getCurrentMainMenu();
		$arrData['main']=$oMenu->getCurrentMainMenu();
		$arrData['current']=$oMenu->getCurrentSubMenu($arrMain['item']);
 		$sBackdate=$this->renderTpl('submenu',$arrData);
		return $sBackdate;
	}

}
