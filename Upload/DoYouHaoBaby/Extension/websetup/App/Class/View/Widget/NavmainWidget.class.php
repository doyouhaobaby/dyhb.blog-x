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
 //  | websetup Navmain挂件（顶部导航条）
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

class NavmainWidget extends Widget{

	public function render($arrData){
		$oMenu=new MenuModel();
		$arrData['all_menu']=$oMenu->getAll();
		$arrData['current']=$oMenu->getCurrentMainMenu();
		$sBackdate=$this->renderTpl('navmain',$arrData);
		return $sBackdate;
	}

}
