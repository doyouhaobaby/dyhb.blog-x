<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   搜索控制器($) */

!defined('DYHB_PATH') && exit;

class SearchController extends CommonController{

	public function index(){
		define('IS_SEARCHFORM',TRUE);
		define('CURSCRIPT','searchform');
		$this->assign('the_search_description',Global_Extend::getOption('the_search_description'));
		$this->display('search');
	}

	public function result(){
		$sSearchFrom=G::getGpc('search_from','P');
		$sSearchFrom=in_array($sSearchFrom,array('blog','comment'))? $sSearchFrom : 'blog';
		if($sSearchFrom=='comment' && Global_Extend::getOption('allow_search_comments')==0){
			$this->E(G::L('系统已经关闭了评论搜索'));
		}
		$nSearchSubmit=G::getGpc('searchsubmit','G');
		$bQuickSearch=false;
		if($nSearchSubmit=='yes'){
			$bQuickSearch=true;
			$_REQUEST['search_date_before']=0;
		}
		$sSearchKey=G::getGpc('search_key');
		$sSearchKey=trim($sSearchKey);
		if(empty($sSearchKey) && $bQuickSearch===false){
			$this->E(G::L('您没有指定要搜索的关键字'));
		}
		else{
			if(strlen($sSearchKey)< Global_Extend::getOption('search_keywords_min_length') && $bQuickSearch===false){
				$this->E(G::L("关键字不能少于 %d 个字节",'app',null,Global_Extend::getOption('search_keywords_min_length')));
			}
			$arrSearchIndex=array('id'=>0,'create_dateline'=>'0');
			$nSearchPostSpace=Global_Extend::getOption('search_post_space');
			$nCurrentTimeStamp=CURRENT_TIMESTAMP;
			$sSearchindexPrefix=SearchindexModel::F()->query()->getTablePrefix();
			$nIp=E::getIp();
			$sSql="SELECT searchindex_id,create_dateline,('{$nSearchPostSpace}'<>'0' AND {$nCurrentTimeStamp}-create_dateline<{$nSearchPostSpace})AS flood,searchindex_searchfrom='{$sSearchFrom}' AND searchindex_keywords='{$sSearchKey}' AS indexvalid FROM {$sSearchindexPrefix}searchindex WHERE('{$nSearchPostSpace}'<>'0' AND searchindex_ip='{$nIp}' AND {$nCurrentTimeStamp}-create_dateline<{$nSearchPostSpace})ORDER BY flood";
			$oDb=Db::RUN();
			$arrIndexs=$oDb->getAllRows($sSql);
			$arrTheRightSearchIndex=null;
			foreach($arrIndexs as $arrIndex){
				if($arrIndex['indexvalid'] && $arrIndex['create_dateline'] > $arrSearchIndex['create_dateline']){
					$arrSearchIndex=array('id'=>$arrIndex['searchindex_id'],'create_dateline'=>$arrIndex['create_dateline']);
					$arrTheRightSearchIndex=$arrIndex;
					break;
				}
				elseif($arrIndex['flood']){
					$this->E(G::L('对不起,您在 %d 秒内只能进行一次搜索','app',null,$nSearchPostSpace));
				}
			}
			$bExistSearchIndex=false;
			$arrMap=$this->the_blog_search_data();
			if(!empty($arrSearchIndex['id'])){
				$arrSearchstring=unserialize($arrTheRightSearchIndex['searchindex_searchstring']);
				$bExistSearchIndex=true;
				foreach($arrMap as $sKey=>$value){
					if(isset($arrSearchstring[$sKey])&& $value!=$arrSearchstring[$sKey]){
						$bExistSearchIndex=false;
						break;
					}
				}
			}
			if($bExistSearchIndex===true){
					$nSearchId=$arrSearchIndex['id'];
					$arrSearchstring=unserialize($arrTheRightSearchIndex['searchindex_searchstring']);
					$sTheOrderby=$arrSearchstring['theorderby'];
					$sSearchOrderbyAscdesc=$arrSearchstring['orderby'];
			}
			else{
				if(!empty($sSearchKey)){
					$sSearchKey=str_replace("_","\_",$sSearchKey);
					$sSearchKey=str_replace("%","\%",$sSearchKey);
					if(preg_match("(AND|\+|&|\s)",$sSearchKey) && !preg_match("(OR|\|)",$sSearchKey)){
						$sAndOr=' AND ';
						$sSqlTxtSrch='1';
						$sSearchKey=preg_replace("/(AND |&|)/is","+",$sSearchKey);
					}
					else{
						$sAndOr=' OR ';
						$sSqlTxtSrch='0';
						$sSearchKey=preg_replace("/(OR |\|)/is","+",$sSearchKey);
					}
					$sSearchKey=str_replace('*','%',addcslashes($sSearchKey,'%_'));
					foreach(explode("+",$sSearchKey)AS $sText){
						$sText=trim($sText);
						if($sText){
							$sSqlTxtSrch.=$sAndOr;
							$sSearchType=G::getGpc('search_type');
							$sSearchFilter=G::getGpc('search_filter');
							if($sSearchFrom=="blog"){
								$sSqlTxtSrch.=($sSearchType=='fulltext')? "(blog_content LIKE '%".str_replace('_','\_',$sText)."%' OR blog_excerpt LIKE '%".$sText."%' OR blog_title LIKE '%".$sText."%')" : "blog_title LIKE '%".$sText."%'";
							}
							else{
								$sSqlTxtSrch.=($sSearchFilter=='name')? "comment_name LIKE '%".$sText."%'" : "(comment_content LIKE '%".str_replace('_','\_',$sText)."%' OR comment_name LIKE '%".$sText."%')";
							}
						}
					}
				}
				else{
					$sSqlTxtSrch='';
				}

				if($sSearchFrom=='blog'){
					$sTheQuery="SELECT blog_id FROM ".BlogModel::F()->query()->getTablePrefix()."blog WHERE blog_isshow='1'";
					if(isset($arrMap['blog_ismobile'])){
						$sTheQuery.=" AND blog_ispage=1";
					}
					elseif(isset($arrMap['blog_ispage'])){
						$sTheQuery.=" AND blog_ismobile=1";
					}
					elseif(isset($arrMap['blog_istop'])){
						$sTheQuery.=" AND blog_istop=1";
					}
					if(isset($arrMap['user_id'])){
						$sTheQuery.=" AND user_id=".$arrMap['user_id'];
					}
					if($arrMap['search_date']){
						if($arrMap['search_date_before']==0){
							$arrMap['blog_dateline']=$nCurrentTimeStamp-$arrMap['search_date'];
							$sTheQuery.=" AND blog_dateline>=".($nCurrentTimeStamp-$arrMap['search_date']);
						}
						else{
							$arrMap['blog_dateline']=$nCurrentTimeStamp-$arrMap['search_date'];
							$sTheQuery.=" AND blog_dateline<=".($nCurrentTimeStamp-$arrMap['search_date']);
						}
					}
					$sSearchOrderby=G::getGpc('search_orderby1');
					$sSearchOrderbyAscdesc=G::getGpc('search_orderby_ascdesc');
					$sSearchOrderbyAscdesc=$sSearchOrderbyAscdesc=='asc' ? 'asc' : 'desc';
					$sTheOrderby='';
					switch($sSearchOrderby){
						case 'update_dateline':
							$sTheOrderby=$sSearchOrderby;
							break;
						case 'commentnum':
							$sTheOrderby='blog_commentnum';
							break;
						case 'trackbacknum':
							$sTheOrderby='blog_trackbacknum';
							break;
						case 'uploadnum':
							$sTheOrderby='blog_uploadnum';
							break;
						case 'good':
							$sTheOrderby='blog_good';
							break;
						case 'bad':
							$sTheOrderby='blog_bad';
							break;
						case 'create_dateline':
						default:
							$sTheOrderby='blog_dateline';
							break;
					}
					$arrMap['theorderby']=$sTheOrderby;
					$arrMap['orderby']=$sSearchOrderbyAscdesc;
					$sTheOrderby=&$sTheOrderby;
					$sSearchOrderbyAscdesc=&$sSearchOrderbyAscdesc;
					if(isset($arrMap['category_id'])){
						$sTheQuery.=" AND category_id IN(".$arrMap['category_id'].")";
					}
					$sTheQuery.=(!empty($sSqlTxtSrch)? "AND($sSqlTxtSrch)" :'')." ORDER BY {$sTheOrderby} {$sSearchOrderbyAscdesc}";
					$nTotals=$sIds=0;
					$arrBlogLists=$oDb->getAllRows($sTheQuery);
					foreach($arrBlogLists as $arrBlogList){
						$sIds.=','.$arrBlogList['blog_id'];
						$nTotals++;
					}
					$sSearchFrom='blog';
				}
				else{
					$arrMap['coment_isshow']=1;
					$sTheQuery="SELECT comment_id FROM ".CommentModel::F()->query()->getTablePrefix()."comment WHERE comment_isshow='1'";
					$sTheQuery.=" AND($sSqlTxtSrch)ORDER BY create_dateline DESC";
					$sTheOrderby='create_dateline';
					$sSearchOrderbyAscdesc='desc';
					$arrCommentLists=$oDb->getAllRows($sTheQuery);
					$nTotals=$sIds=0;
					foreach($arrCommentLists as $arrCommentList){
						$sIds.=','.$arrCommentList['comment_id'];
						$nTotals++;
					}
					$sSearchFrom='comment';
				}
				$oSearchIndex=new SearchindexModel();
				$oSearchIndex->searchindex_keywords=$sSearchKey;
				$oSearchIndex->searchindex_expiration=$nCurrentTimeStamp+$nSearchPostSpace;
				$oSearchIndex->searchindex_searchstring=serialize($arrMap);
				$oSearchIndex->searchindex_totals=$nTotals;
				$oSearchIndex->searchindex_ids=$sIds;
				$oSearchIndex->searchindex_searchfrom=$sSearchFrom;
				$oSearchIndex->save();
				if($oSearchIndex->isError()){
					$this->E($oSearchIndex->getErrorMessage());
				}
				$nSearchId=$oSearchIndex->searchindex_id;
			}
		}

		$sOrderMore='';
		if(!empty($sTheOrderby)){
			$sOrderMore.="&orderby={$sTheOrderby}";
		}
		if(!empty($sSearchOrderbyAscdesc)){
			$sOrderMore.="&ascdesc={$sSearchOrderbyAscdesc}";
		}
		if($sSearchFrom=='blog'){
			$sUrl=G::U('blog/index?searchid='.$nSearchId.$sOrderMore);
		}
		else{
			$sUrl=G::U('comment/index?searchid='.$nSearchId.$sOrderMore);
		}
		if(Global_Extend::getOption('show_search_result_message')){
			$this->assign('__JumpUrl__',$sUrl);
			$this->S(Global_Extend::getOption('show_search_result_message'));
		}
		else{
			G::urlGoTo($sUrl);
		}
	}

	public function the_comment_search_data(){
		$arrMap['comment_isshow']=1;
		$arrMap['search_filter']=G::getGpc('search_filter');
	}

	public function the_blog_search_data(){
		$arrMap['blog_isshow']=1;
		$arrMap['search_type']=G::getGpc('search_type');
		$sSearchFilter=G::getGpc('search_filter');
		$sSearchFilter=in_array($sSearchFilter,array('all','page','mobile','top'))? $sSearchFilter : 'all';
		switch($sSearchFilter){
			case 'page':
				$arrMap['blog_ispage']=1;
				break;
			case 'mobile':
				$arrMap['blog_ismobile']=1;
				break;
			case 'top':
				$arrMap['blog_istop']=1;
				break;
		}
		$sUser=G::getGpc('search_name');
		if(!empty($sUser)){
			if(!preg_match("/[^\d-.,]/",$sUser)){
				$arrMap['user_id']=$sUser;
			}
			else{
				$oUserModel=UserModel::F('user_name=?',$sUser)->query();
				if(empty($oUserModel->user_id)){
					$this->E(G::L('你搜索的用户名%s不存在','app',null,$sUser));
				}
				else{
					$arrMap['user_id']=$oUserModel->user_id;
				}
			}
		}
		$arrMap['search_date']=intval(G::getGpc('search_date'));
		$arrMap['search_date_before']=G::getGpc('search_date_before');
		$arrCategory=G::getGpc('search_category');
		unset($arrCategory[0]);
		$sCids=$sComma='';
		if(!empty($arrCategory)){
			$arrAllCategory=Blog_Extend::getWidgetCache('category');
			foreach($arrAllCategory as $arrData){
				if(in_array($arrData['category_id'],$arrCategory)){
					$sCids.=$sComma.intval($arrData['category_id']);
					$sComma=',';
				}
			}
		}
		if($sCids){
			$arrMap['category_id']=$sCids;
		}
		return $arrMap;
	}

}
