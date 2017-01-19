<?php
/**

 //  [Websetup!] 图像界面工具
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
 //  | websetup Menu模型（菜单）
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

class MenuModel{

	/**
	 * 所有菜单
	 */
	protected $_arrAll;

	/**
	 * 构造函数
	 */
	public function __construct(){
		$arrConfig = $this->config_();
		$this->_arrAll = $this->prepareMenus_($arrConfig);
	}

	/**
	 * 返回所有
	 */
	public function getAll(){
		return $this->_arrAll;
	}

	/**
	 * 获取当前主菜单
	 */
	public function getCurrentMainMenu(){
		foreach($this->_arrAll as $arrMain){
			if($arrMain['c']===MODULE_NAME ){
				return $arrMain;
			}
		}
		return null;
	}

	/**
	 * 获取submenu菜单
	 */
	public function getCurrentSubMenu(array $arrMenu){
		foreach($arrMenu as $arrItem){
			if($arrItem['c']===MODULE_NAME && $arrItem['a']===ACTION_NAME){
				return $arrItem;
			}
		}
		return null;
	}

	/**
	 * 比较以便于css样式
	 */
	public function compare($arrMenu1,$arrMenu2){
		if($arrMenu1['c']!=$arrMenu2['c']){return false;}
		if($arrMenu1['a']!=$arrMenu2['a']){return false;}
		return true;
	}

	/**
	 * 分析配置文件
	 */
	protected function prepareMenus_(array $arrConfig){
		$arrRet=array();
		foreach($arrConfig as $arrMenu){
			if(empty($arrMenu['a'])){
				$arrMenu['a']=$GLOBALS['_commonConfig_']['DEFAULT_CONTROL'];
			}
			if(!empty($arrMenu['item'])){
				$arrMenu['item']=$this->prepareMenus_($arrMenu['item']);
			}
			$arrRet[]=$arrMenu;
		}
		return $arrRet;
	}

	/**
 	 * 配置
 	 */
 	private function config_(){
 		return array(
			array(
				'title'=>G::L('基本设置'),
				'c'=>'index',
				'a'=>'index',
				'item'=>array(
					array(
						'c'=>'index',
						'a'=>'index',
						'title'=>G::L('确认应用程序基本信息'),
					),
					array(
						'c'=>'index',
						'a'=>'config',
						'title'=>G::L('应用程序配置初始化'),
					),
					array(
					'c'=>'index',
					'a'=>'thanks',
					'title'=>G::L('感谢'),
					),
				),
			),
			array(
				'title'=>G::L('代码生成器'),
				'c'=>'create',
				'a'=>'controllers',
				'item'=>array(
					array(
						'c'=>'create',
						'a'=>'controllers',
						'title'=>G::L('列出控制器'),
					),
					array(
						'c'=>'create',
						'a'=>'models',
						'title'=>G::L('列出模型'),
					),
				),
			),
		);
	}

}
