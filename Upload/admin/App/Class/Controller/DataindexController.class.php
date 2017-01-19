<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
	数据调用记录管理控制器($)*/

!defined('DYHB_PATH') && exit;

class DataindexController extends InitController{

	public function filter_(&$arrMap){
		$arrMap['dataindex_ids']=array('like',"%".G::getGpc('dataindex_ids')."%");
	}

	public function _index_get_admin_help_description(){
		return '<p>'.G::L('这里是数据调用相关信息，你可以在这里进行相关操作。','dataindex').'</p>'.
				'<p>'.G::L('删除数据调用相关信息，系统会再次重建。','dataindex').'</p>'.
				'<p>'.G::L('这里，我们主要方便大家将数据调用调出到站外。','dataindex').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function clear(){
		$this->display();
	}

	public function _clear_get_admin_help_description(){
		return '<p>'.G::L('清理数据调用记录可以提高效率和准确性。','dataindex').'</p>'.
				'<p><strong>'.G::L('更多信息：').'</strong></p>'.
				'<p><a href="http://bbs.doyouhaobaby.net" target="_blank">'.G::L('支持论坛').'</a></p>';
	}

	public function clear_all(){
		$arrAllDatas=DataindexModel::F()->all()->query();
		foreach($arrAllDatas as $oAllData){
			if(!empty($oAllData['dataindex_id'])){
				Model::C($oAllData['dataindex_md5hash'],null,array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration'),'cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
			}
		}
		$oDb=Db::RUN();
		$sSql="TRUNCATE ".PmModel::F()->query()->getTablePrefix()."dataindex";
		$oDb->query($sSql);
		$this->assign('__JumpUrl__',G::U('dataindex/index'));
		$this->S(G::L('清空数据调用数据成功'));
	}

	public function AEditObject_($oModel){
		if($oModel['dataindex_auto']==1){
			$this->E(G::L('模板自动创建的数据调用不能够被编辑'));
		}
	}

	public function option(){
		$arrOptionData=&$this->_arrOptions;
		$this->assign('arrOptions',$arrOptionData);
		$this->display();
	}

	public function bForeverdelete_(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			$arrIds=explode(',',trim($sId));
			foreach($arrIds as $nId){
				$oDataindex=DataindexModel::F('dataindex_id=?',$nId)->query();
				if(!empty($oDataindex['dataindex_id'])){
					Model::C($oDataindex['dataindex_md5hash'],null,array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration'),'cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
				}
			}
		}
	}

	public function update_option(){
		$arrOption=G::getGpc('configs','P');
		foreach($arrOption as $sKey=>$val){
			$val=trim($val);
			$oOptionModel=OptionModel::F('option_name=?',$sKey)->getOne();
			$oOptionModel->option_value=$val;
			$oOptionModel->save(0,'update');
			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}
		Cache_Extend::global_option();
		Cache_Extend::global_option('blog');
		Cache_Extend::global_option('wap');
		$this->S(G::L('配置文件更新成功了！'));
	}

	public function bAdd_(){
		$oCategoryTree=new TreeCategory();
		foreach($this->getBlogCategory()as $oCategory){
			$oCategoryTree->setNode($oCategory->category_id,$oCategory->category_parentid,$oCategory->category_name);
		}
		$this->assign('oCategoryTree',$oCategoryTree);
	}

	public function bEdit_(){
		$this->bAdd_();
	}

	public function getBlogCategory(){
		return CategoryModel::F()->order('category_id ASC,category_compositor DESC')->all()->query();
	}

	public function update_cache(){
		$sId=G::getGpc('id','G');
		if(!empty($sId)){
			foreach(explode(',',$sId) as $nId){
				$oData=DataindexModel::F('dataindex_id=?',$nId)->query();
				if(!empty($oData['dataindex_id'])){
					$arrWhereDatastring=E::mbUnserialize(stripslashes($oData['dataindex_datastring']));
					$arrBlogLists=BlogModel::F()->asArray()->where($arrWhereDatastring['where'])->all()->order($arrWhereDatastring['order'])->limit($arrWhereDatastring['limit'][0],$arrWhereDatastring['limit'][1])->query();
					if(E::oneImensionArray($arrBlogLists) && !empty($arrBlogLists)){
						$arrBlogLists=array($arrBlogLists);
					}
					$arrBlogIds=array();
					foreach($arrBlogLists as $oBlogList){
						$arrBlogIds[]=$oBlogList['blog_id'];
					}
					$oData->dataindex_totals=count($arrBlogIds);
					$oData->dataindex_ids=implode(',',$arrBlogIds);
					$oData->save(0,'update');
					if($oData->isError()){
						G::E($oData->getErrorMessage());
					}
					Model::C($oData['dataindex_md5hash'],$arrBlogLists,array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration'),'cache_path'=>DYHB_PATH.'/../blog/App/~Runtime/Data/DbCache'));
				}
				else{
					$this->E(G::L('更新缓存项不存在！'));
				}
			}
			$this->S(G::L('更新数据调用缓存成功！'));
		}
		else{
			$this->E(G::L('更新缓存项不存在！'));
		}
	}

	public function add_call(){
		$arrCondition=$_POST;
		unset($arrCondition['__hash__'],$arrCondition['dataindex_template']);
		$oDataindex=new DataindexModel();
		$oDataindex->dataindex_conditionstring=addslashes(serialize($arrCondition));
		$oDataindex->dataindex_auto=0;
		$arrBlogData=$this->make_sql_condition();
		$sHash=md5(serialize($arrBlogData));
		$oDataindex->dataindex_md5hash=$sHash;
		$oDataindex->dataindex_datastring=addslashes(serialize($arrBlogData));
		$arrBlogLists=BlogModel::F()->where($arrBlogData['where'])->all()->order($arrBlogData['order'])->limit($arrBlogData['limit'][0],$arrBlogData['limit'][1])->query();
		$arrBlogIds=array();
		foreach($arrBlogLists as $oBlogList){
			$arrBlogIds[]=$oBlogList['blog_id'];
		}
		$oDataindex->dataindex_totals=count($arrBlogIds);
		$oDataindex->dataindex_ids=implode(',',$arrBlogIds);
		$oDataindex->save();
		if($oDataindex->isError()){
			$this->E($oDataindex->getErrorMessage());
		}
		else{
			$this->assign('__JumpUrl__',G::U('dataindex/edit?id='.$oDataindex['dataindex_id']));
			$this->S(G::L('数据调用保存成功'));
		}
	}

	public function update_call(){
		$arrCondition=$_POST;
		unset($arrCondition['__hash__'],$arrCondition['dataindex_template']);
		$oDataindex=DataindexModel::F('dataindex_id=?',G::getGpc('id','P'))->query();
		$oDataindex->dataindex_conditionstring=addslashes(serialize($arrCondition));
		$oDataindex->dataindex_auto=0;
		$arrBlogData=$this->make_sql_condition();
		$sHash=md5(serialize($arrBlogData));
		$oDataindex->dataindex_md5hash=$sHash;
		$oDataindex->dataindex_datastring=addslashes(serialize($arrBlogData));
		if((CURRENT_TIMESTAMP-$oDataindex['create_dateline'])>$oDataindex['dataindex_expiration']){
			$arrBlogLists=BlogModel::F()->where($arrBlogData['where'])->all()->order($arrBlogData['order'])->limit($arrBlogData['limit'][0],$arrBlogData['limit'][1])->query();
			$arrBlogIds=array();
			foreach($arrBlogLists as $oBlogList){
				$arrBlogIds[]=$oBlogList['blog_id'];
			}
			$oDataindex->create_dateline=CURRENT_TIMESTAMP;
			$oDataindex->dataindex_totals=count($arrBlogIds);
			$oDataindex->dataindex_ids=implode(',',$arrBlogIds);
		}
		$oDataindex->save(0,'update');
		if($oDataindex->isError()){
			$this->E($oDataindex->getErrorMessage());
		}
		else{
			$this->assign('__JumpUrl__',G::U('dataindex/edit?id='.$oDataindex['dataindex_id']));
			$this->S(G::L('数据调用更新成功'));
		}
	}

	public function make_sql_condition(){
		$arrData=$_POST;
		$arrOrWheres=array();
		$arrOrWheres['string_']='';
		if(!empty($arrData['blog_id_list'])){
			if(!empty($arrData['filter'])){
				if($arrData['filter']==1){
					$arrOrWheres['blog_istop']=1;
				}
				else{
					$arrOrWheres['blog_istop']=array('neq',1);
				}
			}
			if(!empty($arrData['include_thumb'])){
				if($arrData['include_thumb']==1){
					$arrOrWheres['blog_thumb']=array('neq','');
				}
				else{
					$arrOrWheres['blog_thumb']='';
				}
			}
			if(!empty($arrData['user_id'])){
				$arrOrWheres['user_id']=$arrData['user_id'];
			}
			if(!empty($arrData['subday'])){
				$nTime=gmmktime(0,0,0,gmdate('m'),gmdate('d'),gmdate('Y'));
				$nLimitDay=$nTime -($arrData['subday'] * 24 * 3600);
				$arrOrWheres['blog_dateline']=array('gt',$nLimitDay);
			}
			if(!empty($arrData['keyword'])){
				$arrData['keyword']=str_replace(',','|',$arrData['keyword']);
				$arrOrWheres['string_']=" CONCAT(blog_title,blog_keyword)REGEXP '".$arrData['keyword']."' ";
			}
			if(!empty($arrData['blog_flag'])&& $arrData['blog_flags']){
				$arrCondition=array();
				for($nI=0;count($arrData['blog_flags']);$nI++){
					if(trim($arrData['blog_flags'][$nI])==''){continue;}
					$arrTypeCondition[]=" FIND_IN_SET('".$arrData['blog_flags'][$nI]."',blog_type)>0 ";
				}
				$arrOrWheres['string_'].=implode('OR',$arrTypeCondition);
			}
			else{
				$arrOrWheres['string_'].=" FIND_IN_SET('".implode(',',$arrData['blog_flags'])."',blog_type)>0 ";
			}

			if(!empty($arrData['blog_category'])&&!empty($arrData['blog_categorys'])){
				if(count($arrData['blog_categorys'])>1){
					$arrOrWheres['category_id']=array('in',join(',',$arrData['blog_categorys']));
				}
				else{
					$arrOrWheres['category_id']=$arrData['blog_categorys'][0];
				}
			}
			if(!empty($arrData['blog_un_flags'])){
				$arrTypeCondition=array();
				foreach($arrData['blog_un_flags'] as $sNoAtt){
					if(trim($sNoAtt)==''){continue;}
					$arrTypeCondition[]=" FIND_IN_SET('".$sNoAtt."',blog_type)<1 ";
				}
				$arrOrWheres['string_'].=implode('AND',$arrTypeCondition);
			}
			else{
				$arrOrWheres['blog_id']=array('in',$arrData['blog_id_list']);
			}
		}

		if(!empty($arrData['nopwd'])){
			$arrOrWheres['blog_password']='';
		}
		$arrOrWheres['blog_isshow']=1;
		$arrOrWheres['blog_ispage']=0;
		if(empty($arrOrWheres['string_'])){
			unset($arrOrWheres['string_']);
		}
		$arrLimits=array($arrData['limit_start'],$arrData['limit_num']);
		$sOrderSql='';
		$sOrderWay=$arrData['orderway']==0 ?'asc':'desc';
		switch($arrData['orderby']){
			case 0:
				$sOrderSql.=" blog_dateline ".$sOrderWay;
				break;
			case 1:
				$sOrderSql.=" blog_commentnum ".$sOrderWay;
				break;
			case 2:
				$sOrderSql.=" blog_viewnum ".$sOrderWay;
				break;
			case 3:
				$sOrderSql.=" blog_lastpost ".$sOrderWay;
				break;
			case 4:
				$sOrderSql.=" blog_id ".$sOrderWay;
				break;
			case 5:
				$sOrderSql.=" blog_good ".$sOrderWay;
				break;
			case 6:
				$sOrderSql.=" blog_bad ".$sOrderWay;
				break;
			case 7:
				$sOrderSql.=" rand()";
				break;
		}
		$arrBlogData=array('where'=>$arrOrWheres,'order'=>$sOrderSql,'limit'=>$arrLimits);
		return $arrBlogData;
	}

}
