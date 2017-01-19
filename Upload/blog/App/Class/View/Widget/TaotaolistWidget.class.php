<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   心情列表挂件($) */

!defined('DYHB_PATH') && exit;

class TaotaolistWidget extends Widget{

	static protected $_oTaotaoModel=null;

	public function render($arrData){
		$arrDefaultOption=array(
			'before_widget'=>'<div id="taotao-box" class="taotao-box">',
			'after_widget'=>'</div>',
			'ul_class'=>'taotao-list',
			'before_img'=>'<div class="taotao-main-img">',
			'after_img'=>'</div>',
			'before_taotao_content'=>'<p class="taotao-post-left">',
			'after_taotao_content'=>'</p>',
			'before_taotao_bottom'=>'<div class="taotao-bottom">',
			'after_taotao_bottom'=>'</div>',
			'clear'=>'<div class="clearer"></div>',
			'taotao_reply'=>'taotao-reply',
			'taotao_time'=>'taotao-time',
			'no_img'=>__TMPLPUB__.'/Images/avatar.gif',
			'avatar_size'=>32,
			'mobile_img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/mobile.gif')?STYLE_IMG_DIR.'/mobile.gif':IMG_DIR.'/mobile.gif'),
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		return $this->renderTpl('',$arrData);
	}

	static public function getATaotaoComments($nTaotaoId){
		$oTaotaoModel=self::$_oTaotaoModel;
		if($oTaotaoModel===null){
			self::$_oTaotaoModel=$oTaotaoModel=new TaotaoModel();
		}
		return $oTaotaoModel->getATaotaoComments($nTaotaoId);
	}

}
