<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   文章调用挂件($) */

!defined('DYHB_PATH') && exit;

class ArclistWidget extends Widget{

	public function render($arrData){
		$arrDefaultOption=array(
			'col'=>1,//分多少列显示（默认为单列）,本属性可以通过多种方式进行多行显示
			'row'=>10,//返回文章列表总数
			'category_id'=>0,//分类ID
			'title_cutnum'=>26,//标题长度 等同于title_cutnumgth
			'infolen'=>160,//表示内容简介长度 等同于infolength
			'imgwidth'=>120,//缩略图宽度
			'imgheight'=>90,//缩略图高度
			'orderby'=>'default',//文章排序方式
			'keyword'=>'',//含有指定关键字的文章列表，多个关键字用
			'blog_id'=>'',//指定文章ID
			'blog_id_list'=>'',//提取特定文章
			'limit'=>'',//（起始ID从0开始）表示限定的记录范围,如：limit='1,2' 表示从ID为1的记录开始，取2条记录
			'att'=>'',//自定义属性值：头条[h]推荐[c]图片[p]幻灯[f]滚动[s]图文[a]加粗[b]
			'noatt'=>'',//同att，但这里是表示不包含这些属性
			'orderway'=>'desc',//值为 desc 或 asc ，指定排序方式是降序还是顺向排序，默认为降序
			'subday'=>0,//表示在多少天以内的文章
			'sort'=>'',//排序
			'tag'=>'',//
			'tag_custom'=>'',//
			'list_type_more'=>'',//'d1'=>纯文本|'d1'=>列表调用样式2 - 带日期的长标题列表(d1)|'d2'=>列表调用样式3 - 带日期的短标题列表(d2)|'e5'=>图片列表1
			'list_type'=>'arclist',//arclist,imglist、imginfolist
			'list_type_custom'=>'',
			'd1_date_type'=>'m-d',
			'd1_date_title_cutnum'=>23,
			'd2_date_type'=>'Y-m-d',
			'd2_date_title_cutnum'=>19,
			'user'=>0,
			'li_class'=>'',
			'return'=>0,
		);
		if(!empty($arrData)){
			$arrData=array_merge($arrDefaultOption,$arrData);
		}
		else{
			$arrData=$arrDefaultOption;
		}
		if($arrData['sort']){$arrData['orderby']=$arrData['sort'];}
		else if($arrData['tag']=='hotpost'){$arrData['orderby']='blog_viewnum';}
		else if($arrData['tag']=='commentpost'){$arrData['orderby']='blog_commentnum';}
		else if($arrData['tag']=='top'){$arrData['orderby']='blog_istop';}
		if(trim($arrData['list_type'])=='custom'){$arrData['style_file_path']=TEMPLATE_PATH.'/'.$arrData['tag_custom'];}
		else if($arrData['list_type']=='imglist'){$arrData['style_file_path']='imglist';}
		else if($arrData['list_type']=='imgarclist'){$arrData['style_file_path']='imgarclist';}
		else if($arrData['list_type']=='headerarc'){$arrData['style_file_path']='headerarc';}
		else if($arrData['list_type']=='headerarc2'){$arrData['style_file_path']='headerarc2';}
		else if($arrData['list_type']=='digg'){$arrData['style_file_path']='digg';}
		else {$arrData['style_file_path']='arclist';}
		if(!empty($arrData['title_cutnumgth'])){$arrData['title_cutnum']=$arrData['title_cutnumgth'];}
		if(!empty($arrData['infolength'])){$arrData['infolen']=$arrData['infolength'];}
		if($arrData['list_type_more']=='d1'){
			$arrData['title_cutnum']=$arrData['d1_date_title_cutnum'];
		}
		elseif($arrData['list_type_more']=='d2'){
			$arrData['title_cutnum']=$arrData['d2_date_title_cutnum'];
		}
		return $this->getArticleLists($arrData);
	}

	public function getArticleLists($arrData){
		if(!$arrData['row']){$arrData['row']=10;}
		if(!$arrData['title_cutnum']){$arrData['title_cutnum']=30;}
		if(!$arrData['infolen']){$arrData['infolen']=160;}
		if(!$arrData['imgwidth']){$arrData['imgwidth']=120;}
		if(!$arrData['imgheight']){$arrData['imgheight']=120;}
		if(!$arrData['list_type']){$arrData['list_type']='arclist';}
		if(!$arrData['blog_id']){$arrData['blog_id']=0;}
		if(!$arrData['orderby']){$arrData['orderby']='default';}
		if(!isset($arrData['order'])){$arrData['order']='desc';}
		if(!$arrData['subday']){$arrData['subday']=0;}
		if(!empty($arrData['limit'])){$arrLimits=explode(",",$arrData['limit']);}
		else{$arrLimits=array(0,$arrData['row']);}
		$arrData['orderby']=strtolower($arrData['orderby']);
		$arrData['keyword']=trim($arrData['keyword']);
		if(!isset($arrData['tablewidth'])){$arrData['tablewidth']=100;}
		if(!$arrData['col']){$arrData['col']=1;}
		$nColWidth=ceil(100/$arrData['col']);
		$arrData['tablewidth']=$arrData['tablewidth']."%";
		$nColWidth=$nColWidth."%";
		if($arrData['att']=='0'){$arrData['att']='';}
		if($arrData['att']=='3'){$arrData['att']='f';}
		if($arrData['att']=='1'){$arrData['att']='h';}
		$arrOrWheres=array();
		$arrOrWheres['string_']='';
		if($arrData['blog_id_list']==''){// 按不同情况设定SQL条件 排序方式
			if($arrData['orderby']=='near' && strtolower(Global_Extend::getOption('global_blog_keyword_link'))==0){
				$arrData['keyword']='';
			}
			if($arrData['user']!=0){
				$arrOrWheres['user_id']=$arrData['user'];
			}
			if($arrData['subday'] > 0){// 时间限制(用于调用最近热门文章、热门评论之类)，这里的时间只能计算到天，否则缓存功能将无效
				$nTime=gmmktime(0,0,0,gmdate('m'),gmdate('d'),gmdate('Y'));
				$nLimitDay=$nTime -($arrData['subday'] * 24 * 3600);
				$arrOrWheres['blog_dateline']=array('gt',$nLimitDay);
			}
			if($arrData['keyword']!=''){// 关键字条件
				$arrData['keyword']=str_replace(',','|',$arrData['keyword']);
				$arrOrWheres['string_']=" CONCAT(blog_title,blog_keyword)REGEXP '".$arrData['keyword']."' ";
			}
			if(preg_match('/commend/i',$arrData['list_type']))$arrOrWheres['string_'].=" FIND_IN_SET('c',blog_type)>0 ";// 文章属性
			if(preg_match('/img/i',$arrData['list_type']))$arrOrWheres['string_'].=" FIND_IN_SET('p',blog_type)>0 ";
			if($arrData['att'] !=''){
				if(strpos($arrData['att'],'|')){
					$arrAtts=array_unique(explode('|',$arrData['att']));
					$arrTypeCondition=array();
					for($nI=0;count($arrAtts);$nI++){
						if(trim($arrAtts[$nI])=='')continue;
						$arrTypeCondition[]=" FIND_IN_SET('".$arrAtts[$nI]."',blog_type)>0 ";
					}
					$arrOrWheres['string_'].=implode('OR',$arrTypeCondition);
				}
				else{
					$arrOrWheres['string_'].=" FIND_IN_SET('".$arrData['att']."',blog_type)>0 ";
				}
			}
			if(!empty($arrData['category_id'])){
				if(preg_match('#,#',$arrData['category_id'])){// 指定了多个分类时，不再获取子类的id
					$arrCategoryIds=explode(',',$arrData['category_id']);
					$arrCategoryIds=array_unique($arrCategoryIds);
					$arrOrWheres['category_id']=array('in',join(',',$arrCategoryIds));
				}
				else{
					$arrOrWheres['category_id']=$arrData['category_id'];
				}
			}
			if(!empty($arrData['noatt'])){
				if(!preg_match('#,#',$arrData['noatt'])){
					$arrOrWheres['string_'].=" FIND_IN_SET('".$arrData['noatt']."',blog_type)<1 ";
				}
				else{
					$arrNoAtts=explode(',',$arrData['noatt']);
					$arrTypeCondition=array();
					foreach($arrNoAtts as $sNoAtt){
						if(trim($sNoAtt)==''){continue;}
						$arrTypeCondition[]=" FIND_IN_SET('".$sNoAtt."',blog_type)<1 ";
					}
					$arrOrWheres['string_'].=implode('AND',$arrTypeCondition);
				}
			}
		}
		else{
			$arrOrWheres['blog_id']=array('in',$arrData['blog_id_list']);
		}
		$arrOrWheres['blog_isshow']=1;
		$arrOrWheres['blog_ispage']=0;
		$sOrderSql='';// 文档排序的方式
		if(in_array($arrData['orderby'],array('hot','click','blog_viewnum'))){
			$sOrderSql=" blog_viewnum ".$arrData['orderway'];
		}
		else if(in_array($arrData['orderby'],array('pubdate','blog_dateline','dateline'))){
			$sOrderSql=" blog_dateline ".$arrData['orderway'];
		}
		else if(in_array($arrData['orderby'],array('id','blog_id'))){
			$sOrderSql=" blog_id ".$arrData['orderway'];
		}
		else if($arrData['orderby']=='near'){
			$sOrderSql=" ABS(blog_id - ".$arrData['blog_id'].")";
		}
		else if(in_array($arrData['orderby'],array('lastpost','blog_lastpost'))){
			$sOrderSql=" blog_lastpost ".$arrData['orderway'];
		}
		else if(in_array($arrData['orderby'],array('comment','commentnum','blog_commentnum'))){
			$sOrderSql=" blog_commentnum ".$arrData['orderway'];
		}
		else if($arrData['orderby']=='rand'){
			$sOrderSql=" rand()";
		}
		else if(in_array($arrData['orderby'],array('good','blog_good'))){
			$sOrderSql=" blog_good ".$arrData['orderway'];
		}
		else if(in_array($arrData['orderby'],array('bad','blog_bad'))){
			$sOrderSql=" blog_bad ".$arrData['orderway'];
		}
		else{
			$sOrderSql=" blog_id ".$arrData['orderway'];
		}
		if(empty($arrOrWheres['string_'])){
			unset($arrOrWheres['string_']);
		}
		$arrBlogData=array('where'=>$arrOrWheres,'order'=>$sOrderSql,'limit'=>$arrLimits);// 统一hash
		$sHash=md5(serialize($arrBlogData));
		$arrBlogLists=Model::C($sHash,'',array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration')));
		if($arrBlogLists===false){
			$arrBlogLists=BlogModel::F()->asArray()->where($arrOrWheres)->all()->order($sOrderSql)->limit($arrLimits[0],$arrLimits[1])->query();
			if(!empty($arrBlogLists) && E::oneImensionArray($arrBlogLists)){
				$arrBlogLists=array($arrBlogLists);
			}
			$arrBlogIds=array();
			foreach($arrBlogLists as $oBlogList){
				$arrBlogIds[]=$oBlogList['blog_id'];
			}
			$oDataindexModel=DataindexModel::F('dataindex_md5hash=?',$sHash)->query();
			if(!empty($oDataindexModel['dataindex_id'])){
				if((CURRENT_TIMESTAMP-$oDataindexModel['create_dateline'])>Global_Extend::getOption('global_blog_cache_expiration')){
					$oDataindexModel->dataindex_totals=count($arrBlogIds);
					$oDataindexModel->dataindex_ids=implode(',',$arrBlogIds);
					$oDataindexModel->save(0,'update');
					if($oDataindexModel->isError()){
						G::E($oDataindexModel->getErrorMessage());
					}
				}
			}
			else{
				$oDataIndex=new DataindexModel();
				$oDataIndex->dataindex_md5hash=$sHash;
				$oDataIndex->dataindex_datastring=addslashes(serialize($arrBlogData));
				$oDataIndex->dataindex_totals=count($arrBlogIds);
				$oDataIndex->dataindex_ids=implode(',',$arrBlogIds);
				$oDataIndex->save();
				if($oDataIndex->isError()){
					G::E($oDataIndex->getErrorMessage());
				}
			}
			Model::C($sHash,$arrBlogLists,array('cache_time'=>Global_Extend::getOption('global_blog_cache_expiration')));
		}
		if(!is_array($arrBlogLists)){
			return '';
		}
		if($arrData['return']==1){
			return $arrBlogLists;
		}
		if($arrData['style_file_path']=='arclist'){
			foreach($arrBlogLists as $oBlogData){
				echo "<li class=\"{$arrData['li_class']}\">".($arrData['list_type_more']=='d1'?"<span class=\"date\">".date($arrData['d1_date_type'],$oBlogData['blog_dateline'])."</span>":'').($arrData['list_type_more']=='d2'?"<span class=\"date\">".date($arrData['d2_date_type'],$oBlogData['blog_dateline'])."</span>":'')."<a ".($oBlogData['blog_color']?"style=\"color:{$oBlogData['blog_color']};\"":''). "title=\"{$oBlogData['blog_title']}\" href=\"".($oBlogData['blog_gotourl']?$oBlogData['blog_gotourl']:PageType_Extend::getBlogUrl($oBlogData))."\" >".String::subString($oBlogData['blog_title'],0,$arrData['title_cutnum'])."</a></li>";
			}
			unset($arrBlogLists,$arrData);
			return;
		}
		if($arrData['style_file_path']=='imgarclist'){
			foreach($arrBlogLists as $oBlogData){
				echo "<li><a title=\"{$oBlogData['blog_title']}\" href=\"".($oBlogData['blog_gotourl']?$oBlogData['blog_gotourl']:PageType_Extend::getBlogUrl($oBlogData))."\" ><img src=\"".Blog_Extend::getThumbImage($oBlogData)."\" alt=\"{$oBlogData['blog_title']}\" width=\"{$arrData['imgwidth']}\" height=\"{$arrData['imgheight']}\" /><span class=\"title\" ".($oBlogData['blog_color']?"style=\"color:{$oBlogData['blog_color']};\"":'')." >".String::subString($oBlogData['blog_title'],0,$arrData['title_cutnum'])."</span></a></li>";
			}
			unset($arrBlogLists,$arrData);
			return;
		}
		if($arrData['style_file_path']=='imglist'){
			foreach($arrBlogLists as $oBlogData){
				echo "<li><a title=\"{$oBlogData['blog_title']}\" href=\"".($oBlogData['blog_gotourl']?$oBlogData['blog_gotourl']:PageType_Extend::getBlogUrl($oBlogData))."\" ><img src=\"".Blog_Extend::getThumbImage($oBlogData)."\" alt=\"{$oBlogData['blog_title']}\" width=\"{$arrData['imgwidth']}\" height=\"{$arrData['imgheight']}\" /></a></li>";
			}
			unset($arrBlogLists,$arrData);
			return;
		}
		$arrData['blog_data']=$arrBlogLists;
		return $this->renderTpl($arrData['style_file_path'],$arrData);
	}

}
