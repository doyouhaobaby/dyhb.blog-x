<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	公用控制器($)*/

!defined('DYHB_PATH') && exit;

class InitController extends Controller{

	public $_arrOptions=array();

	public function get_display_sceen_options(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_display_sceen_options')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_display_sceen_options'));
		}
		return false;
	}

	public function get_metabox_prefs_title(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_metabox_prefs_title')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_metabox_prefs_title'));
		}
		return G::L('在页面上显示');
	}

	public function get_metabox_prefs_value(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_metabox_prefs_value')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_metabox_prefs_value'));
		}
		return '';
	}

	public function get_sceen_options_title(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_sceen_options_title')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_sceen_options_title'));
		}
		return G::L('在页面上显示');
	}

	public function get_sceen_options_value(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_sceen_options_value')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_sceen_options_value'));
		}
		return '';
	}

	public function get_admin_help_description(){
		if(method_exists($this,'_'.ACTION_NAME.'_get_admin_help_description')){
			return call_user_func(array($this,'_'.ACTION_NAME.'_get_admin_help_description'));
		}
		return G::L('没有帮助信息');
	}

	public function _index_get_sceen_options_value(){
		return "<input type='text' class='field' name='configs[admineverynum]' id='admineverynum' maxlength='3' value='".$this->_arrOptions['admineverynum']."' /> <label for='admineverynum'>".G::L("后台每页显示数量")."</label>
<input type=\"submit\" name=\"screen-options-apply\" id=\"screen-options-apply\" class=\"button\" value=\"".G::L("应用")."\"  />";
	}

	public function _index_get_display_sceen_options(){
		return true;
	}

	public function init__(){
		parent::init__();
		if(isset($_POST['screen-options-apply'])){
			$arrOption=G::getGpc('configs','P');
			foreach($arrOption as $sKey=>$val){
				$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
				$oOptionModel->option_value=$val;
				$oOptionModel->save(0,'update');
				Model::C('global_option',null);
				if($oOptionModel->isError()){
					$this->E($oOptionModel->getErrorMessage());
				}
			}
			Cache_Extend::global_option();
			Cache_Extend::global_option('blog');
			Cache_Extend::global_option('wap');
			$this->S(G::L('配置文件更新成功了！'));
		}

		if(empty($this->_arrOptions)){
			$arrOptions=OptionModel::optionData();
			if($arrOptions===false){
				$this->E(G::L('获取数据库配置信息错误，也许你的数据库相关信息错误！'));
			}
			$this->_arrOptions=$arrOptions;
		}
		UserModel::M()->authData();
		if(UserModel::M()->isBehaviorError()){// 捕获错误
			$this->E(UserModel::M()->getBehaviorErrorMessage());
		}
		if(MODULE_NAME!=='upload'){// 由于flash 丢失COOKIE,我们采用另一种方式来验证数据
			UserModel::M()->checkRbac();
			if(UserModel::M()->isBehaviorError()){
				$this->E(UserModel::M()->getBehaviorErrorMessage());
			}
		}
		$arrUserData=UserModel::M()->userData();
		if(empty($arrUserData['user_id'])){
			$GLOBALS['___login___']=false;
		}
		else{
			$GLOBALS['___login___']=$arrUserData;
		}
		unset($arrUserData);
		$this->init2();
	}

	public function init2(){
		$this->assign('sBlogArtdialogSkin',$this->_arrOptions['blog_artdialog_skin']);
		$arrConfigData=array('TPL_DIR'=>ucfirst(Global_Extend::getOption('admin_theme_name')),
							'LANG'=>ucfirst(Global_Extend::getOption('admin_lang_set')),
							'TIME_ZONE'=>Global_Extend::getOption('timeoffset'));
		foreach($arrConfigData as $sKey=>$sConfigData){
			if($GLOBALS['_commonConfig_'][$sKey]!==$sConfigData){
				G::C($sKey,$sConfigData);
			}
		}
		unset($arrConfigData);
	}

	public function index(){
		$arrMap=$this->map();
		if(method_exists($this,'filter_')){
			$this->filter_($arrMap);
		}
		$this->get_list($arrMap);
		$this->display();
	}

	protected function map($sName=''){
		if(empty($sName)){
			$sName=MODULE_NAME;
		}
		$sName=ucfirst($sName).'Model';
		$arrField=array();
		eval('$arrField='.$sName.'::M()->_arrFieldToProp;');
		$arrMap=array();
		foreach($arrField  as $sField=>$sProp){
			if(isset($_REQUEST [ $sField ])&&
				!empty($_REQUEST [ $sField ])){
				$arrMap[ $sField ]=$_REQUEST [ $sField ];
			}
		}
		return $arrMap;
	}

	protected function get_list($arrMap){
		$sParameter='';
		$arrSortUrl=array();
		$nTotalRecord=0;
		eval('$nTotalRecord='.ucfirst(MODULE_NAME).'Model::F()->where($arrMap)->all()->getCounts();');
		$nEverynum=$this->_arrOptions['admineverynum'];
		foreach($arrMap as $sKey=>$sVal){
			if(!is_array($sVal)){
				$sParameter.=$sKey.'='.urlencode($sVal).'&';
				$arrSortUrl[]='"'.$sKey.'='.urlencode($sVal).'"';
			}
		}
		$sSortBy=strtoupper(G::getGpc('sort_'))=='ASC'?'ASC':'DESC' ;
		$sOrder=G::getGpc('order_')? G::getGpc('order_'): MODULE_NAME.'_id';
		$this->assign('sSortByUrl',strtolower($sSortBy)=='desc'? 'asc':'desc');
		$this->assign('sSortByDescription',strtolower($sSortBy)=='desc'?G::L('倒序'): G::L('升序'));
		$this->assign('sOrder',$sOrder);
		$this->assign('sSortUrl','new Array('.implode(',',$arrSortUrl).')');
		$oPage=Page::RUN($nTotalRecord,$nEverynum,G::getGpc('page','G'));
		$oPage->setParameter($sParameter);
		$sPageNavbar=$oPage->P();
		$oList=array();
		eval('$oList='.ucfirst(MODULE_NAME).'Model::F()->where($arrMap)->all()->order($sOrder.\' \'.$sSortBy)->limit($oPage->returnPageStart(),$nEverynum)->query();');
		$this->assign('sPageNavbar',$sPageNavbar);
		$this->assign('oList',$oList);
	}

	public function input_change_ajax(){
		$sModel=MODULE_NAME;
		$oModelMeta=null;
		eval('$oModelMeta='.ucwords($sModel).'Model::M();');
		$sPk=reset($oModelMeta->_arrIdName);
			$nInputAjaxId=G::getGpc('input_ajax_id');
			$sInputAjaxField=G::getGpc('input_ajax_field');
			$sInputAjaxVal=G::getGpc('input_ajax_val');
			$arrData=array(
			$sPk=>$nInputAjaxId,
			$sInputAjaxField=>$sInputAjaxVal,
		);

		if($sModel=='badword' && $sInputAjaxField=='badword_find'){
			$arrData['badword_findpattern']='/'.$sInputAjaxVal.'/is';
		}
		if($sInputAjaxField=='badword_find'){
			$this->input_change_unique();
		}
		$oModelMeta->updateDbWhere($arrData);
		if($oModelMeta->isError()){
			$this->E($oModelMeta->getErrorMessage());
		}
		else{
			$this->afterInputChangeAjax();
				$arrVo=array(
					'id'=>$sInputAjaxField.'_'.$nInputAjaxId,
					'value'=>$sInputAjaxVal,
				);
			$this->A($arrVo,G::L('数据更新成功！'));
		}
	}

	public function afterInputChangeAjax(){}

	public function input_change_unique(){
		$sModel=ucfirst(MODULE_NAME);
		$oModelMeta=null;
		eval('$oModelMeta='.$sModel.'Model::M();');
		$nId=G::getGpc('input_ajax_id');
		$sField=G::getGpc('input_ajax_field');
		$sName=G::getGpc('input_ajax_val');
		$sInfo='';
		if($nId){
			$oModel=null;
			eval('$oModel='.$sModel.'Model::F(\''.MODULE_NAME.'_id=?\','.$nId.')->query();');
			$arrInfo=$oModel->toArray();
			$sInfo=$arrInfo[ $sField ];
		}
		if($sName !=$sInfo){
			$oSelect=null;
			eval('$oSelect='.$sModel.'Model::F();');
			$sFunc='getBy'.$sField;
			$arrResult=$oSelect->{$sFunc}($sName)->toArray();
			if(!empty($arrResult[ $sField ])){
				$this->E(G::L('该项数据已经存在！'));
			}
		}
	}

	public function seccode(){
		G::seccode();
	}

	public function insert(){
		$oModel=null;
		eval('$oModel=new '.ucfirst(MODULE_NAME).'Model();');
		if(method_exists($this,'AInsertObject_'))
		call_user_func(array($this,'AInsertObject_'),$oModel);
		$oModel->save();
		$sPrimaryKey=MODULE_NAME.'_id';
		if(!$oModel->isError()){
			$this->aInsert(intval($oModel->{$sPrimaryKey}));
			if(!in_array(MODULE_NAME,array('user','plugin'))){
				$this->A($oModel->toArray(),G::L('数据保存成功！'),1);
			}
			else{
				$arrUser=$oModel->toArray();
				$nId=reset($arrUser);
				$this->assign('__JumpUrl__',G::U(MODULE_NAME.'/edit?id='.$nId));
				$this->S(G::L('数据保存成功！'));
			}
		}
		else{
			$this->E($oModel->getErrorMessage());
		}
	}

	protected function aInsert($nId=null){}

	public function add(){$this->display();}

	public function edit(){
		$nId=G::getGpc('id','G');
		if(!empty($nId)){
			$oModel=null;
			eval('$oModel='.ucfirst(MODULE_NAME).'Model::F(\''.MODULE_NAME.'_id=?\','.$nId.')->query();');
			if(method_exists($this,'AEditObject_')){
				call_user_func(array($this,'AEditObject_'),$oModel);
			}
			if(!empty($oModel->{MODULE_NAME.'_id'})){
				$this->assign('oValue',$oModel);
				$this->assign('nId',$nId);
				$this->display(MODULE_NAME.'+add');
			}
			else{
				$this->E(G::L('数据库中并不存在该项，或许它已经被删除了！'));
			}
		}
		else{
			$this->E(G::L('编辑项不存在！'));
		}
	}

	public function update(){
		$nId=G::getGpc('id');
		$oModel=null;
		eval('$oModel='.ucfirst(MODULE_NAME).'Model::F(\''.MODULE_NAME.'_id=?\','.$nId.')->query();');
		if(method_exists($this,'AUpdateObject_'))
		call_user_func(array($this,'AUpdateObject_'),$oModel);
		$oModel->save(0,'update');
		$sPrimaryKey=MODULE_NAME.'_id';
		if(!$oModel->isError()){
			$this->aUpdate(intval($oModel->{$sPrimaryKey}));
			$this->S(G::L('数据更新成功！'));
		}
		else{
			$this->E($oModel->getErrorMessage());
		}
	}

	protected function aUpdate(){}

	public function foreverdelete(){
		$sModel=MODULE_NAME;
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$oModelMeta=null;
			eval('$oModelMeta='.ucfirst($sModel).'Model::M();');
			$sPk=reset($oModelMeta->_arrIdName);
			$oModelMeta->deleteWhere(array($sPk=>array('in',$sId)));
			if($oModelMeta->isError()){
				$this->E($oModelMeta->getErrorMessage());
			}
			else{
				$this->aForeverdelete($sId);
				if($this->isAjax()){
					$this->A('',G::L('删除记录成功！'),1);
				}
				else{
					$this->S(G::L('删除记录成功！'));
				}
			}
		}
		else{
			$this->E(G::L('删除项不存在！'));
		}
	}

	protected function aForeverdelete($sId){}

	public function forbid(){
		$sModel=MODULE_NAME;
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$oModelMeta=null;
			eval('$oModelMeta='.ucfirst($sModel).'Model::M();');
			$sPk=reset($oModelMeta->_arrIdName);
			$oModelMeta->updateDbWhere(array($sModel.'_status'=>0),array($sPk=>$sId));
			if($oModelMeta->isError()){
				$this->E($oModelMeta->getErrorMessage());
			}
			else{
				$this->aForbid();
				$this->assign('__JumpUrl__',G::U($sModel.'/index'));
				$this->S(G::L('禁用成功！'));
			}
		}
		else{
			$this->E(G::L('禁用项不存在！'));
		}
	}

	protected function aForbid(){}

	public function resume(){
		$sModel=MODULE_NAME;
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$oModelMeta=null;
			eval('$oModelMeta='.ucfirst($sModel).'Model::M();');
			$sPk=reset($oModelMeta->_arrIdName);
			$oModelMeta->updateDbWhere(array($sModel.'_status'=>1),array($sPk=>$sId));
			if($oModelMeta->isError()){
				$this->E($oModelMeta->getErrorMessage());
			}
			else{
				$this->aResume();
				$this->assign('__JumpUrl__',G::U($sModel.'/index'));
				$this->S(G::L('恢复成功！'));
			}
		}
		else{
			$this->E(G::L('恢复项不存在！'));
		}
	}

	protected function aResume(){}

	public function hide(){
		$sModel=MODULE_NAME;
		if($sModel=='guestbook')
			$sModel='comment';
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',$sId);
			foreach($arrIds as $nId){
				$oModelMeta=null;
				eval('$oModelMeta='.ucfirst($sModel).'Model::M();');
				$sPk=reset($oModelMeta->_arrIdName);
				$oModelMeta->updateDbWhere(array(($sModel=='guestbook'?'comment':$sModel).'_isshow'=>0),array($sPk=>$nId));
				if($oModelMeta->isError()){
					$this->E($oModelMeta->getErrorMessage());
				}
			}
		}
		else{
			$this->E(G::L('隐藏项不存在！'));
		}
		$this->assign('__JumpUrl__',G::U($sModel.'/index'));
		$this->S(G::L('隐藏成功！'));
	}

	public function show(){
		$sModel=MODULE_NAME;
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',$sId);
			foreach($arrIds as $nId){
				$oModelMeta=null;
				eval('$oModelMeta='.ucfirst($sModel).'Model::M();');
				$sPk=reset($oModelMeta->_arrIdName);
				$oModelMeta->updateDbWhere(array(($sModel=='guestbook'?'comment':$sModel).'_isshow'=>1),array($sPk=>$nId));
				if($oModelMeta->isError()){
					$this->E($oModelMeta->getErrorMessage());
				}
			}
		}
		else{
			$this->E(G::L('显示项不存在！'));
		}
		$this->assign('__JumpUrl__',G::U($sModel.'/index'));
		$this->S(G::L('显示成功！'));
	}

	public function save_sort(){
		$sMoveResult=G::getGpc('moveResult','P');
		if(!empty($sMoveResult)){
			$oModel=null;
			eval('$oModel=new '.ucfirst(MODULE_NAME).'Model();');
			$oDb=$oModel->getDb();
			$arrCol=explode(',',$sMoveResult);
			$oDb->getConnect()->startTransaction();
			$bResult=true;
			foreach($arrCol as $val){
				$val=explode(':',$val);
				$oModel=null;
				eval('$oModel='.ucfirst(MODULE_NAME).'Model::F(\''.MODULE_NAME.'_id=?\','.$val[0].')->query();');
				$oModel->{MODULE_NAME.'_sort'}=$val[1];
				$oModel->save(0,'update');
				if($oModel->isError()){
					$bResult=false;
					break;
				}
			}
			$oDb->getConnect()->commit();
			if($bResult !==false){
				$this->S(G::L('更新成功'));
			}
			else{
				$oDb->getConnect()->rollback();
				$this->E($oModel->getErrorMessage());
			}
		}
		else{
			$this->E(G::L('没有可以排序的数据！'));
		}
	}

	public function get_avatar($sType='origin',$nUid=''){
		if(empty($nUid)){
			$nUid=$GLOBALS['___login___']['user_id'];
		}
		return E::getAvatar($nUid,$sType);
	}

	public function get_avatar_url($sType='',$nUid=''){
		$bExistAvatar=file_exists(DYHB_PATH.'/../Public/Avatar/data/'.$this->get_avatar())?true:false;
		if(empty($sType)&& empty($nUid)){
			$arrResult=array();
			$arrResult['exist']=$bExistAvatar;
			$arrResult['origin']=$bExistAvatar ?__PUBLIC__.'/Avatar/data/'.$this->get_avatar():__PUBLIC__.'/Avatar/images/noavatar_big.gif';
			$arrResult['big']=$bExistAvatar?__PUBLIC__.'/Avatar/data/'.$this->get_avatar('big'):__PUBLIC__.'/Avatar/images/noavatar_big.gif';
			$arrResult['middle']=$bExistAvatar?__PUBLIC__.'/Avatar/data/'.$this->get_avatar('middle'):__PUBLIC__.'/Avatar/images/noavatar_middle.gif';
			$arrResult['small']=$bExistAvatar?__PUBLIC__.'/Avatar/data/'.$this->get_avatar('small'):__PUBLIC__.'/Avatar/images/noavatar_small.gif';
			return $arrResult;
		}
		else{
			if(empty($sType)){
				$sType='origin';
			}
			return $bExistAvatar?__PUBLIC__.'/Avatar/data/'.$this->get_avatar($sType):__PUBLIC__.'/Avatar/images/noavatar_'.($sType=='origin'?'big':$sType).'.gif';
		}
	}

	public static function get_gravatar_com($sEmail='',$nSize='',$sDefault='',$sRating=''){
		if(empty($nSize)){
			$nSize=Global_Extend::getOption('comment_avatar_size');
		}
		if(empty($sDefault)){
			$sDefault=Global_Extend::getOption('avatar_default');
		}
		if(empty($sRating)){
			$sRating=Global_Extend::getOption('avatars_rating');
		}
		if(Global_Extend::getOption('avatar_cache')){
			$sEmail=md5($sEmail);//通过MD5加密邮箱
			$sCachePath=DYHB_PATH."/../Public/Avatar/cache";//缓存文件夹路径
			if(!is_dir($sCachePath)){
				if(!G::makeDir($sCachePath)){
					exit(G::L('创建缓存缓存目录%s失败','app',null,$sCachePath,$sCachePath));
				}
			}
			$sAvatarUrl=__PUBLIC__.'/Avatar/cache/'.$sEmail.'.jpg';//头像相对路径
			$sAvatarAbsUrl=$sCachePath."/".$sEmail.'.jpg';//头像绝对路径
			$sCacheTime=24*3600*(Global_Extend::getOption('avatar_cache_time')?Global_Extend::getOption('avatar_cache_time'):7);//缓存时间为7天
			if(!file_exists($sAvatarAbsUrl)||(time()-filemtime($sAvatarAbsUrl))> $sCacheTime){
				$sNewAvatar="http://www.gravatar.com/avatar/".$sEmail."?s=".$nSize."&d=".strtolower($sDefault)."&r=".strtolower($sRating);
				copy($sNewAvatar,$sAvatarAbsUrl);
			}
			return $sAvatarUrl;
		}
		else{
			return "http://www.gravatar.com/avatar/".md5($sEmail)."?s=".$nSize."&d=".strtolower($sDefault)."&r=".strtolower($sRating);
		}
	}

}
