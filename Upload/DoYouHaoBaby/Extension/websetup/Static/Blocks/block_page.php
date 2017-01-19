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
 //  | websetup 分页代码块
 //  +---------------------------------------------------------------------

*/

!defined('DYHB_PATH') && exit;

/**
 * 分页代码块
 */
function block_page( $sTable ){
	$sModuleName=ucfirst($sTable);
	return "// 自动化page方法体代码
		// 获取总记录数
		\$nTotalRecord=\$o{$sModuleName}Model::F( )->all()->getCounts( );
		// 分页记录数量
		\$nEverynum=5;
		// 使用分页类
		\$oPage=Page::RUN( \$nTotalRecord,\$nEverynum, G::getGpc( 'page' ) );
		\$sPageNavbar=\$oPage->P( );
		// 获取数据列表
		\$arrList=\$o{$sModuleName}Model::F( )
					->all()
					->limit( \$oPage->returnPageStart( ), \$nEverynum )
					->asArray()
					->query();
		\$this->assign( 'sPageNavbar',\$sPageNavbar );
		\$this->assign( 'arrList',\$arrList );
		";
}
