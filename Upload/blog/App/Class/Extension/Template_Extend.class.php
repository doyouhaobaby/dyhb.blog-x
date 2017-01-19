<?php
/* [DYHB.BLOG X!](C)Dianniu From 2010.
   基本模板函数($) */

!defined('DYHB_PATH') && exit;

class Template_Extend{

	public static function getConfig($arrDefaultOption,$arrData){
		if(!empty($arrData)){ $arrData=array_merge($arrDefaultOption,$arrData); }
		else{$arrData=$arrDefaultOption;}
		return $arrData;
	}

	static public function echoHeader($arrData){
		echo $arrData['before_widget'];
		if($arrData['widget_title']==1){echo $arrData['before_title'].$arrData['block_title'].$arrData['after_title'];}
	}

	public static function show($arrData=array()){
		$arrData=self::getConfig(array('widget'=>'widgets_main1','before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div id="#-@%s@-#" class="widget #-@%s@-#">','after_widget'=>'</div>'),$arrData);
		$arrWidgetsTrue=unserialize(Global_Extend::getOption($arrData['widget']));
		if(empty($arrWidgetsTrue)){return;}
		$sOld=$arrData['before_widget'];
		foreach($arrWidgetsTrue as $sWidgetTrue){
			$arrData['before_widget']=str_replace("#-@%s@-#",$sWidgetTrue,$arrData['before_widget']); // 替换
			unset($arrData['widget']);
			if(strpos($sWidgetTrue,'custom_widget_')===0){
				$arrData['custom_widget_name']=$sWidgetTrue;
				call_user_func(array("Template_Extend",'customW'),$arrData);
			}
			else{
				call_user_func(array("Template_Extend",$sWidgetTrue.'W'),$arrData);
			}
			$arrData['before_widget']=$sOld;
		}
	}

	public static function customW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_custom">','after_widget'=>'</div>','custom_widget_name'=>'custom_widget_1','p_class'=>'custom_get'),$arrData);
		$arrWidgetOptions=unserialize(Global_Extend::getOption('custom_widget_option'));
		if(!isset($arrWidgetOptions[$arrData['custom_widget_name']])){
			return '';
		}
		$arrCurrentWidget=$arrWidgetOptions[$arrData['custom_widget_name']];
		unset($arrWidgetOptions);
		if(!isset($arrData['block_title'])){$arrData['block_title']=$arrCurrentWidget['title'];}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=$arrCurrentWidget['titleshow'];}
		if(!isset($arrData['content'])){$arrData['content']=$arrCurrentWidget['content'];}
		self::echoHeader($arrData);
		echo "<p class=\"{$arrData['p_class']}\">{$arrData['content']}</p>{$arrData['after_widget']}";
		unset($arrData,$arrCurrentWidget);
	}

	public static function echoPost($sName,$arrData){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>"<div class=\"widget widget_{$sName}\">",'after_widget'=>'</div>','ul_class'=>'','ul_class'=>''),$arrData);
		$sName='widget_'.$sName;
		$arrDatas=Model::C($sName);
		if($arrDatas===false){
			call_user_func(array('Cache_Extend','front_'.$sName),$arrData);
			$arrDatas=Model::C($sName);
		}
		switch($sName){
			case 'widget_recentpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_recentpost_name')?Global_Extend::getOption('widget_recentpost_name'):G::L('最新日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_recentpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_recentpost_titleshow');}
				break;
			case 'widget_commentpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_commentpost_name')?Global_Extend::getOption('widget_commentpost_name'):G::L('最受欢迎日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_commentpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_commentpost_titleshow');}
				break;
			case 'widget_hotpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_hotpost_name')?Global_Extend::getOption('widget_hotpost_name'):G::L('热门日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_hotpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_hotpost_titleshow');}
				break;
			case 'widget_yearhotpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_yearhotpost_name')?Global_Extend::getOption('widget_yearhotpost_name'):G::L('年度排行日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_yearhotpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_yearhotpost_titleshow');}
				break;
			case 'widget_yearcommentpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_yearcommentpost_name')?Global_Extend::getOption('widget_yearcommentpost_name'):G::L('年度最受欢迎日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_yearcommentpost_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_yearcommentpost_titleshow');}
				break;
			case 'widget_pagepost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_pagepost_name')?Global_Extend::getOption('widget_pagepost_name'):G::L('页面');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_pagepost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_pagepost_titleshow');}
				break;
			case 'widget_dayhotpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_dayhotpost_name')?Global_Extend::getOption('widget_dayhotpost_name'):G::L('今日排行日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_dayhotpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_dayhotpost_titleshow');}
				break;
			case 'widget_daycommentpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_daycommentpost_name')?Global_Extend::getOption('widget_daycommentpost_name'):G::L('今日最受欢迎日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_daycommentpost_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_daycommentpost_titleshow');}
				break;
			case 'widget_monthcommentpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_monthcommentpost_name')?Global_Extend::getOption('widget_monthcommentpost_name'):G::L('当月最受欢迎日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_monthcommentpost_tcutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_monthcommentpost_tshow');}
				break;
			case 'widget_monthhotpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_monthhotpost_name')?Global_Extend::getOption('widget_monthhotpost_name'):G::L('当月排行日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_monthhotpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_monthhotpost_titleshow');}
				break;
			case 'widget_randpost':
				if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_randpost_name')?Global_Extend::getOption('widget_randpost_name'):G::L('随机日志');}
				if(!isset($arrData['title_cutnum'])){$arrData['title_cutnum']=intval(Global_Extend::getOption('widget_randpost_title_cutnum'));}
				if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_randpost_titleshow');}
				break;
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		foreach($arrDatas as $arrV){echo "<li class=\"{$sName}-item\"><a title=\"{$arrV['blog_title']}\" href=\"".($sName=='widget_pagepost'?PageType_Extend::getPageUrl($arrV):PageType_Extend::getBlogUrl($arrV))."\" >".String::subString($arrV['blog_title'],0,$arrData['title_cutnum'])."</a></li>";}
		echo "</ul>{$arrData['after_widget']}";
		unset($arrDatas);
	}

	public static function recentpostW($arrData=array()){self::echoPost('recentpost',$arrData);}
	public static function commentpostW($arrData=array()){self::echoPost('commentpost',$arrData);}
	public static function hotpostW($arrData=array()){self::echoPost('hotpost',$arrData);}
	public static function yearhotpostW($arrData=array()){self::echoPost('yearhotpost',$arrData);}
	public static function yearcommentpostW($arrData=array()){self::echoPost('yearcommentpost',$arrData);}
	public static function pagepostW($arrData=array()){self::echoPost('pagepost',$arrData);}
	public static function dayhotpostW($arrData=array()){self::echoPost('dayhotpost',$arrData);}
	public static function daycommentpostW($arrData=array()){self::echoPost('daycommentpost',$arrData);}
	public static function monthcommentpostW($arrData=array()){self::echoPost('monthcommentpost',$arrData);}
	public static function monthhotpostW($arrData=array()){self::echoPost('monthhotpost',$arrData);}
	public static function randpostW($arrData=array()){self::echoPost('randpost',$arrData);}

	public public function calendarW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_calendar">','after_widget'=>'</div>'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_calendar_name')?Global_Extend::getOption('widget_calendar_name'):G::L('日历');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_calendar_titleshow');}
		$arrData['category_content']=self::getCalendar();
		self::echoHeader($arrData);
		echo "{$arrData['category_content']}{$arrData['after_widget']}";
	}

	public static function getCalendar(){
		$arrBlogData=Model::C('widget_calendar');
		if($arrBlogData===false){
			Cache_Extend::front_widget_calendar();
			$arrBlogData=Model::C('widget_calendar');
		}
		$nNowYear=date('Y',CURRENT_TIMESTAMP);
		$nNowYear2=date('Y',CURRENT_TIMESTAMP);
		$nNowMonth=date('m',CURRENT_TIMESTAMP);
		$nNowDay=date('d',CURRENT_TIMESTAMP);
		$nNowTime=date('Ymd',CURRENT_TIMESTAMP);
		$nNowYearMonth=date('Ym',CURRENT_TIMESTAMP);
		$nArchive=G::getGpc('value','G');
		$sFilter=G::getGpc('filter','G');
		if($sFilter=='archive' && !empty($nArchive)){
			$nNowYear=substr(intval($nArchive),0,4);
			$nNowYear2=substr(intval($nArchive),0,4);
			$nNowMonth=substr(intval($nArchive),4,2);
			$nNowYearMonth=substr(intval($nArchive),0,6);
		}
		$nPrevMonth=$nNowMonth-1;
		$nNextMonth=$nNowMonth+1;
		$nPrevMonth=($nPrevMonth<10)?'0'.$nPrevMonth:$nPrevMonth;// 补0
		$nNextMonth=($nNextMonth<10)?'0'.$nNextMonth:$nNextMonth;
		$nYearUp=$nNowYear;
		$nYearDown=$nNowYear;
		if($nNextMonth>12){
			$nNextMonth='01';
			$nYearUp=$nNowYear+1;
		}
		if($nPrevMonth<1){
			$nPrevMonth='12';
			$nYearDown=$nNowYear-1;
		}
		$sUrl=PageType_Extend::getArchiveUrl(($nNowYear-1). $nNowMonth);// 上一年份
		$sUrl2=PageType_Extend::getArchiveUrl(($nNowYear+1). $nNowMonth);// 下一年份
		$sUrl3=PageType_Extend::getArchiveUrl($nYearDown.$nPrevMonth);// 上一月份
		$sUrl4=PageType_Extend::getArchiveUrl($nYearUp.$nNextMonth);// 下一月份
		$sCalendar="<table id=\"calendar_header\" cellspacing=\"0\">
<tr>
	<td><a href=\"{$sUrl}\">&laquo;</a>{$nNowYear2}<a href=\"{$sUrl2}\">&raquo;</a></td>
	<td><a href=\"{$sUrl3}\"> &laquo;</a>{$nNowMonth}<a href=\"{$sUrl4}\"> &raquo;</a></td>
</tr>
</table>
<table id=\"calendar\" cellspacing=\"0\">
	<tr><td class=\"calendar_mon\">".G::L('一')."</td>
			<td class=\"calendar_week\">".G::L('二')."</td>
			<td class=\"calendar_week\">".G::L('三')."</td>
			<td class=\"calendar_week\">".G::L('四')."</td>
			<td class=\"calendar_week\">".G::L('五')."</td>
			<td class=\"calendar_week\">".G::L('六')."</td>
			<td class=\"calendar_sun\">".G::L('日')."</td>
</tr>";
		$nWeek=@date("w",mktime(0,0,0,$nNowMonth,1,$nNowYear));// 获取给定年月的第一天是星期几
		$nLastday=@date("t",mktime(0,0,0,$nNowMonth,1,$nNowYear));// 获取给定年月的天数
		$nLastweek=@date("w",mktime(0,0,0,$nNowMonth,$nLastday,$nNowYear));// 获取给定年月的最后一天是星期几
		if($nWeek==0){
			$nWeek=7;
		}
		$nJ=1;
		$nW=7;
		$bIsend=false;
		for($nI=1;$nI<=6;$nI++){
			if($bIsend ||($nI==6 && $nLastweek==0)){
				break;
			}
			$sCalendar.='<tr>';
			for($nJ;$nJ<=$nW;$nJ++){
				if($nJ<$nWeek){
					$sCalendar.='<td>&nbsp;</td>';
				}
				elseif($nJ<=7){
					$nNowR=$nJ-$nWeek+1;
					$nTime=$nNowYear.$nNowMonth.'0'.$nNowR;// 如果该日有日志就显示url样式
					if(@in_array($nTime,$arrBlogData) && $nTime==$nNowTime){
						$sCalendar.='<td class="calendar_day"><a href="'.PageType_Extend::getArchiveUrl($nTime).'">'. $nNowR .'</a></td>';
					}
					elseif(@in_array($nTime,$arrBlogData))
						$sCalendar.='<td class="calendar_day2"><a href="'.PageType_Extend::getArchiveUrl($nTime).'">'. $nNowR .'</a></td>';
					elseif($nTime==$nNowTime){
						$sCalendar.='<td class="calendar_day">'. $nNowR .'</td>';
					}
					else{
						$sCalendar.='<td>'. $nNowR .'</td>';
					}
				}
				else{
					$nT=$nJ -($nWeek-1);
					if($nT>$nLastday){
						$bIsend=true;
						$sCalendar.='<td>&nbsp;</td>';
					}
					else{
						$nT<10?$nTime=$nNowYear.$nNowMonth.'0'.$nT:$nTime=$nNowYear.$nNowMonth.$nT;
						if(@in_array($nTime,$arrBlogData)&& $nTime==$nNowTime){
							$sCalendar.='<td class="calendar_day"><a href="'.PageType_Extend::getArchiveUrl($nTime).'">'. $nT .'</a></td>';
						}
						elseif(@in_array($nTime,$arrBlogData)){
							$sCalendar.='<td class="calendar_day2"><a href="'.PageType_Extend::getArchiveUrl($nTime).'">'. $nT .'</a></td>';
						}
						elseif($nTime==$nNowTime){
							$sCalendar.='<td class="calendar_day">'. $nT .'</td>';
						}
						else{
							$sCalendar.='<td>'.$nT.'</td>';
						}
					}
				}
			}
			$sCalendar.='</tr>';
			$nW +=7;
		}
		$sCalendar.='</table>';
		return $sCalendar;
	}

	public static function blogW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_blog">','after_widget'=>'</div>'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_blog_name')?Global_Extend::getOption('widget_blog_name'):G::L('博客形象');}
		if(!isset($arrData['show_admin'])){$arrData['show_admin']=intval(Global_Extend::getOption('widget_blog_show_admin'));}
		if(!isset($arrData['admin_title'])){$arrData['admin_title']=Global_Extend::getOption('widget_blog_admin_title');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_blog_titleshow');}
		$arrData['admin_email']=Global_Extend::getOption('admin_email');
		$arrData['blog_description']=Global_Extend::getOption('blog_description');
		$arrData['blog_logo']=Global_Extend::getOption('blog_logo');
		$arrData['blog_name']=Global_Extend::getOption('blog_name');
		self::echoHeader($arrData);
		if($arrData['blog_logo']==1){ echo "<img src='{$arrData['blog_logo']}' title='{$arrData['blog_name']} {$arrData['blog_description']}' >";}
		if($arrData['show_admin']==1){ echo "<br><a href='mailto:{$arrData['admin_email']}'>{$arrData['admin_title']}</a>";}
		if($arrData['blog_description']){ echo "<br><span>{$arrData['blog_description']}</span>";}
		echo $arrData['after_widget'];
	}

	public static function adminW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_admin">','after_widget'=>'</div>','before_seccond_title'=>'<b>','after_seccond_title'=>'</b>','ul_class'=>''),$arrData);
		$arrUserData=$GLOBALS['___login___'];
		if(empty($arrUserData['user_id'])){
			$is_login=false;
			$user_id=-1;
			$user_name=G::L('跌名');
		}
		else{
			$is_login=true;
			$user_id=$arrUserData['user_id'];
			$user_name=$arrUserData['user_name'];
		}

		$arrData['is_login']=$is_login;
		$arrData['user_id']=$user_id;
		$arrData['user_name']=$user_name;

		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_admin_name')?Global_Extend::getOption('widget_admin_name'):G::L('管理操作');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_admin_titleshow');}
		$arrData['blog_program_url']=Global_Extend::getOption('blog_program_url');
		$arrData['blog_program_name']=Global_Extend::getOption('blog_program_name');
		$arrData['blog_url']=Global_Extend::getOption('blog_url');
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">
<li>{$arrData['before_seccond_title']}".G::L('系统功能')."{$arrData['after_seccond_title']}</li>
<li><a href=\"".G::U('register/index')."\">".G::L('用户注册')."</a></li>
<li><a href=\"".G::U('publish/index')."\">".G::L('在线投稿')."</a></li>
<li><a href=\"".G::U('feed/rss2')."\" title=\"".G::L('使用 RSS 2.0 订阅本站点内容')."\">".G::L('文章')." <abbr title=\"".G::L('真正简单地聚合')."\">RSS</abbr></a></li>
<li><a href=\"".G::U('comment/feed')."\" title=\"".G::L('使用 RSS 订阅本站点的所有文章的近期评论')."\">".G::L('评论')." <abbr title=\"".G::L('真正简单地聚合')."\">RSS</abbr></a></li>
<li>{$arrData['before_seccond_title']}".G::L('个人地盘')."{$arrData['after_seccond_title']}</li>";
		if($arrData['is_login']){
			echo "<li><a href=\"{$arrData['blog_url']}/admin.php\">".G::L('管理中心')."</a></li>
<li><a href=\"".G::U('member/index')."\" title=\"{$arrData['user_name']} ".G::L('个人中心')."\">".G::L('个人中心')."</a></li>
<li><a href=\"".G::U('author@?id='.$arrData['user_id'])."\" title=\"{$arrData['user_name']} ".G::L('个人信息')."\">".G::L('个人信息')."</a></li>
<li><a href=\"".G::U('pm/index')."\" title=\"{$arrData['user_name']} ".G::L('短消息')."\">".G::L('短消息')."</a></li>
<li><a href=\"".G::U('login/logout')."\" title=\"".G::L('注销')."\">".G::L('注销')."</a></li>";
		}
		else{
			echo "<li><a href=\"".G::U('login/index')."\">".G::L('管理登录')."</a></li>";
		}
		echo "<li>{$arrData['before_seccond_title']}".G::L('技术支持')."{$arrData['after_seccond_title']}</li>
	<li><a href=\"{$arrData['blog_program_url']}\" title=\"".G::L('基于')." {$arrData['blog_program_name']}，".G::L('一个小巧的个人信息发布平台。')."\">{$arrData['blog_program_name']}</a></li>
</ul>
{$arrData['after_widget']}";
	}

	public function staticW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_static">','after_widget'=>'</div>','ul_class'=>''),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_static_name')?Global_Extend::getOption('widget_static_name'):G::L('博客统计');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_static_titleshow');}
		$arrStaticData=Model::C('widget_static');
		if($arrStaticData===false){
			Cache_Extend::front_widget_static();
			$arrStaticData=Model::C('widget_static');
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">
	<li class=\"static-item\">
		<a title=\"".G::L('日志数量')."({$arrStaticData['blog']})\" href=\"".__APP__."\" >".G::L('日志数量')."({$arrStaticData['blog']})</a>
	</li>
	<li class=\"static-item\">
		<a title=\"".G::L('评论留言')."({$arrStaticData['comment']})\" href=\"".G::U('comment/index')."\" >".G::L('评论留言')."({$arrStaticData['comment']})</a>
	</li>
	<li class=\"static-item\">
		<a title=\"".G::L('标签数量')."({$arrStaticData['tag']})\" href=\"".G::U('tag/index')."\" >".G::L('标签数量')."({$arrStaticData['tag']})</a>
	</li>
	<li class=\"static-item\">
		<a title=\"".G::L('引用数量')."({$arrStaticData['trackback']})\" href=\"".G::U('trackback/index')."\" >".G::L('引用数量')."({$arrStaticData['trackback']})</a>
	</li>
	<li class=\"static-item\">
		<a title=\"".G::L('附件数量')."({$arrStaticData['upload']})\" href=\"".G::U('upload/index')."\" >".G::L('附件数量')."({$arrStaticData['upload']})</a>
	</li>
	<li class=\"static-item\">
		<a title=\"".G::L('用户数量')."({$arrStaticData['user']})\" href=\"".G::U('user/index')."\" >".G::L('用户数量')."({$arrStaticData['user']})</a>
	</li>
	<li class=\"static-item\">
		".G::L('今日访问')."({$arrStaticData['today_visited_num']})
	</li>
	<li class=\"static-item\">
		".G::L('总访问量')."({$arrStaticData['all_visited_num']})
	</li>
</ul>
{$arrData['after_widget']}";
	}

	public static function rssW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_rss">','after_widget'=>'</div>','rss'=>(file_exists(TEMPLATE_PATH.'/Public/Images/rss.png')?STYLE_IMG_DIR.'/rss.png':IMG_DIR.'/rss.png')),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_rss_name')?Global_Extend::getOption('widget_rss_name'):G::L('Rss 订阅');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_rss_titleshow');}
		self::echoHeader($arrData);
		echo "<a title='".G::L('Rss 订阅')."' href=\"".G::U('feed/rss2')."\"><img src='{$arrData['rss']}' ></a>
{$arrData['after_widget']}";
	}

	public static function searchW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_search">','after_widget'=>'</div>'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_search_name')?Global_Extend::getOption('widget_search_name'):G::L('搜索');}
		if(!isset($arrData['button_text'])){$arrData['button_text']=Global_Extend::getOption('search_widget_button_text')?Global_Extend::getOption('search_widget_button_text'):G::L('搜索');}
		if(!isset($arrData['show_assistive_text'])){$arrData['show_assistive_text']=intval(Global_Extend::getOption('widget_search_showassistivetext'));}
		if(!isset($arrData['input_class'])){$arrData['input_class']=Global_Extend::getOption('widget_search_input_class');}
		if(!isset($arrData['submit_class'])){$arrData['submit_class']=Global_Extend::getOption('widget_search_submit_class');}
		if(!isset($arrData['assistive_text'])){$arrData['assistive_text']=Global_Extend::getOption('widget_search_assistive_text');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_search_titleshow');}
		self::echoHeader($arrData);
		echo "<form name=\"keyform\" method=\"post\" action=\"".G::U('search/result')."\">";
		if($arrData['show_assistive_text']==1){ echo "<label for=\"key\" class=\"{$arrData['assistive_text']}\">{$arrData['button_text']}</label>";}
		echo "<input class=\"{$arrData['input_class']}\" id=\"search-key\" name=\"search_key\" type=\"text\" placeholder=\"{$arrData['button_text']}\" value=\"\" />
	<input type=\"submit\" class=\"{$arrData['submit_class']}\" name=\"submit\" id=\"searchsubmit\" value=\"{$arrData['button_text']}\" />
</form>
{$arrData['after_widget']}";
	}

	public static function themeW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_theme">','after_widget'=>'</div>','theme_dropdown'=>'theme-dropdown'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_theme_name')?	Global_Extend::getOption('widget_theme_name'):G::L('主题切换');}
		if(!isset($arrData['theme_select'])){$arrData['theme_select']=intval(Global_Extend::getOption('widget_theme_select'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_theme_titleshow');}
		$arrThemeDatas=Model::C('widget_theme');
		if($arrThemeDatas===false){
			$arrThemeDatas=E::listDir(APP_PATH.'/Theme');
			Model::C('widget_theme',$arrThemeDatas);
		}
		echo $arrData['before_widget'];
		if($arrData['widget_title']==1){ echo $arrData['before_title'].$arrData['block_title'].$arrData['after_title'];}
		$sThemeCookieName=APP_NAME.'_template';
		if($arrData['theme_select']==1){
			echo "<select name=\"{$arrData['theme_dropdown']}\" onchange='document.location.href=this.options[this.selectedIndex].value;'>
<option value=\"\">".G::L('选择主题')."</option>";
			foreach($arrThemeDatas as $key=>$sThemeData){
				echo "<option value='?t=".strtolower($sThemeData)."'".(strtolower(G::cookie($sThemeCookieName))==strtolower($key)?"selected":'').">{$sThemeData}</option>";
			}
			echo "</select>";
		}
		else{
			foreach($arrThemeDatas as $key=>$sThemeData){
				echo "<a title=\"".G::L('主题')."\" href=\"?t=".strtolower($sThemeData)."\" class=\"theme-item theme-item-".strtolower($sThemeData).(strtolower(G::cookie($sThemeCookieName))==strtolower($key)?" theme-item-current":'')."\" >{$sThemeData}</a>";
			}
		}
		echo $arrData['after_widget'];
	}

	public static function mp3playerW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_mp3player">','after_widget'=>'</div>','player_text'=>G::L('播放音乐'),'the_class'=>'the-mp3play-button',),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_mp3player_name')?Global_Extend::getOption('widget_mp3player_name'):G::L('畅爽音乐');}
		if(!isset($arrData['width'])){$arrData['width']=intval(Global_Extend::getOption('widget_mp3player_width'));}
		if(!isset($arrData['height'])){$arrData['height']=intval(Global_Extend::getOption('widget_mp3player_height'));}
		if(!isset($arrData['bgcolor'])){$arrData['bgcolor']=Global_Extend::getOption('widget_mp3player_bgcolor');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_menu_titleshow');}
		$arrData['id']=rand(1000,99999);
		$arrData['url']=__PUBLIC__."/Images/Mp3/mp3player.swf";
		self::echoHeader($arrData);
		echo "<div class=\"{$arrData['the_class']}\">
<a href=\"javascript:playmedia('player_{$arrData['id']}', 'swf', '{$arrData['url']}', '{$arrData['width']}', '{$arrData['height']}','{$arrData['bgcolor']}');\">{$arrData['player_text']}</a>
</div>
<div id=\"player_{$arrData['id']}\" style=\"display:none;\"></div>
{$arrData['after_widget']}";
	}

	public static function linkW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_link">','after_widget'=>'</div>','ul_class'=>'','type'=>'all'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_link_name')?Global_Extend::getOption('widget_link_name'):G::L('友情衔接');}
		if(!isset($arrData['description'])){$arrData['description']=intval(Global_Extend::getOption('widget_link_description'));}
		if(!isset($arrData['images'])){$arrData['images']=intval(Global_Extend::getOption('widget_link_images'));}
		if(!isset($arrData['name'])){$arrData['name']=intval(Global_Extend::getOption('widget_link_name'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_link_titleshow');}
		$arrLinkDatas=Model::C('widget_link');
		if($arrLinkDatas===false){
			Cache_Extend::front_widget_link($arrData);
			$arrLinkDatas=Model::C('widget_link');
		}
		$link_rand=!isset($arrData['display_rand'])?intval(Global_Extend::getOption('widget_link_display_rand')):$arrData['display_rand'];
		if($link_rand==1){
			shuffle($arrLinkDatas);
		}
		$arrLinkSave=array();
		if($arrData['type']=='image'){
			foreach($arrLinkDatas as $arrLink){
				if($arrLink['link_logo']){
					$arrLinkSave[]=$arrLink;
				}
			}
		}
		else if($arrData['type']=='text'){
			foreach($arrLinkDatas as $arrLink){
				if(empty($arrLink['link_logo'])){
					$arrLinkSave[]=$arrLink;
				}
			}
		}
		else{
			$arrLinkSave=$arrLinkDatas;
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		foreach($arrLinkSave as $arrLinkData){
			if($arrLinkData['link_isdisplay']==1){
				echo "<li class=\"link-item\">
		<a title=\"{$arrLinkData['link_name']} {$arrLinkData['link_description']}\"
		href=\"{$arrLinkData['link_url']}\" >";
				if($arrData['images']==1 && !empty($arrLinkData['link_logo'])){
					echo "<img src=\"{$arrLinkData['link_logo']}\" alt=\"{$arrLinkData['link_name']}\" />";
				}
				else{
					echo $arrLinkData['link_name'];
				}
				echo "</a>";
				if($arrData['description']==1 && !empty($arrLinkData['link_description'])){ echo "<span>{$arrLinkData['link_description']}</span>";}
				echo "</li>";
			}
		}
		echo "</ul>{$arrData['after_widget']}";
		unset($arrLinkDatas,$arrLinkSave);
	}

	public static function hottagW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_hottag">','after_widget'=>'</div>','li'=>0,'style'=>1),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_hottag_name')?Global_Extend::getOption('widget_hottag_name'):G::L('热门标签');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_hottag_titleshow');}
		$arrHottagDatas=Model::C('widget_hottag');
		if($arrHottagDatas===false){
			Cache_Extend::front_widget_hottag($arrData);
			$arrHottagDatas=Model::C('widget_hottag');
		}
		self::echoHeader($arrData);
		foreach($arrHottagDatas as $arrTagData){
			if($arrData['li']==0){
				echo "<span>
	<a ".($arrData['style']==1?"style=\"color:{$arrTagData['color']};font-size:{$arrTagData['fontsize']}pt;line-height:{$arrTagData['fontsize']}pt\"":'')." title=\"".G::L('标签')." {$arrTagData['tag_name']} {$arrTagData['tag_description']}\"
		href=\"".PageType_Extend::getTagUrl($arrTagData)."\"
		class=\"tag-item\" >{$arrTagData['tag_name']}({$arrTagData['tag_usenum']})</a>
	</span>";
			}
			else{
				echo "<li>
	<a ".($arrData['style']==1?"style=\"color:{$arrTagData['color']};font-size:{$arrTagData['fontsize']}pt;line-height:{$arrTagData['fontsize']}pt\"":'')." title=\"".G::L('标签')." {$arrTagData['tag_name']} {$arrTagData['tag_description']}\"
		href=\"".PageType_Extend::getTagUrl($arrTagData)."\"
		class=\"tag-item\" >{$arrTagData['tag_name']}({$arrTagData['tag_usenum']})</a>
	</li>";
			}
		}
		echo $arrData['after_widget'];
		unset($arrHottagDatas);
	}

	public static function archiveW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_archive">','after_widget'=>'</div>','archive_dropdown'=>'archive-dropdown','li-class'=>'archive-item'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_archive_name')?Global_Extend::getOption('widget_archive_name'):G::L('日志归档');}
		if(!isset($arrData['show_blog_num'])){$arrData['show_blog_num']=intval(Global_Extend::getOption('widget_archive_show_blog_num'));}
		if(!isset($arrData['link_select'])){$arrData['link_select']=intval(Global_Extend::getOption('widget_archive_link_select'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_archive_titleshow');}
		$arrArchiveDatas=Model::C('widget_archive');
		if($arrArchiveDatas===false){
			Cache_Extend::front_widget_archive();
			$arrArchiveDatas=Model::C('widget_archive');
		}
		if(!isset($arrData['display_num'])){
			$nArchiveNum=intval(Global_Extend::getOption('widget_archive_display_num'));
		}
		else{
			$nArchiveNum=$arrData['archive_num'];
		}
		$arrArchiveDatas=reset(array_chunk($arrArchiveDatas,$nArchiveNum,true));
		$arrData['archive_value']=intval(G::getGpc('value','G'));
		self::echoHeader($arrData);
		if($arrData['link_select']==1){
			echo "<select name=\"{$arrData['archive_dropdown']}\" onchange='document.location.href=this.options[this.selectedIndex].value;'>
<option value=\"\">".G::L('选择月份')."</option>";
			foreach($arrArchiveDatas as $key=>$nArchiveData){
				echo "<option value='".PageType_Extend::getArchiveUrl($key)."' ".($arrData['archive_value']==$key?" selected":'')." >{$key}".($arrData['show_blog_num']?"({$nArchiveData})":'')."</option>";
			}
			echo "</select>";
		}
		else{
			echo "<ul>";
			foreach($arrArchiveDatas as $key=>$nArchiveData){
				echo "<li class=\"".$arrData['li_class']."\">
		<a title=\"".G::L('日志归档')." {$key}\" href=\"".PageType_Extend::getArchiveUrl($key)."\" ".($arrData['archive_value']==$key?" class=\"archive-current\"":'').">
		 {$key}".($arrData['show_blog_num']?"({$nArchiveData})":'')."
		</a>
	</li>";
			}
			echo "</ul>";
		}
		echo $arrData['after_widget'];
		unset($arrArchiveDatas);
	}

	protected static $_arrCategoryData=array();
	protected static $_arrData=array();

	public static function categoryW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_category">','after_widget'=>'</div>','ul_class'=>array('category-parent','category-child'),'li_class'=>array('category-parent-item','category-child-item'),'rss_image'=>(file_exists(TEMPLATE_PATH.'/Public/Images/feed_small.gif')?STYLE_IMG_DIR.'/feed_small.gif':IMG_DIR.'/feed_small.gif'),'rss_image_title'=>G::L('追踪这个分类的RSS')),$arrData);
		if(is_string($arrData['ul_class'])){
			$arrData['ul_class']=array($arrData['ul_class'],'category-child');
		}
		if(is_string($arrData['li_class'])){
			$arrData['li_class']=array($arrData['li_class'],'category-child-item');
		}
		if(!isset($arrData['block_data'])){$arrData['block_title']=Global_Extend::getOption('widget_category_name')?Global_Extend::getOption('widget_category_name'):G::L('分类目录');}
		if(!isset($arrData['tree'])){$arrData['tree']=intval(Global_Extend::getOption('widget_category_tree'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_category_titleshow');}
		self::$_arrData=&$arrData;
		$arrCategoryData=Model::C('widget_category');
		if($arrCategoryData===false){
			Cache_Extend::front_widget_category();
			$arrCategoryData=Model::C('widget_category');
		}
		self::$_arrCategoryData=&$arrCategoryData;
		if($arrData['tree']==1){
			$oCategoryTree=new TreeCategory();
			$oCategoryTree->setTreeLiCallback(array('TemplateExtend','getDisplayTreeItemLiMore'));
			foreach($arrCategoryData as $arrCategory){
				$oCategoryTree->setNode($arrCategory['category_id'],$arrCategory['category_parentid'],$arrCategory['category_name']);
			}
			$arrData['category_data']=$oCategoryTree->displayTree(0,array('Template_Extend','getDisplayTreeItem'),$arrData['ul_class'],$arrData['li_class']);
		}
		else{
			$arrData['category_data']=$arrCategoryData;
		}
		self::echoHeader($arrData);
		if($arrData['tree']==1){
			echo $arrData['category_data'];
		}
		else{
			echo "<ul class=\"{$arrData['ul_class'][0]}\">";
			foreach($arrData['category_data'] as $arrCategoryData){
				echo "<li class=\"category-item\">
<a {if title=\"{$arrCategoryData['category_name']}\" href=\"".PageType_Extend::getCategoryUrl($arrCategoryData)."\" >{$arrCategoryData['category_name']}</a>".
self::getCategoryRssUrl($arrCategoryData['category_id'])."</li>";
			}
			echo "</ul>";
		}
		echo $arrData['after_widget'];
	}

	static public function getDisplayTreeItem($nItem){
		if($nItem<1)return false;
		$arrCategoryDatas=&self::$_arrCategoryData;
		foreach($arrCategoryDatas as $arrCategoryData){
			if($arrCategoryData['category_id']==$nItem){
				return PageType_Extend::getCategoryUrl($arrCategoryData);
			}
		}
	}

	static public function getDisplayTreeItemLiMore($nItem){
		if($nItem<1){return false;}
		return self::getCategoryRssUrl($nItem);
	}

	static public function getCategoryRssUrl($nCategory){
		$arrData=self::$_arrData;
		return "<a href=\"".G::U('feed/rss2?cid='.$nCategory)."\"><img src=\"".$arrData['rss_image']."\" border=\"0\" alt=\"RSS\" title=\"".$arrData['rss_image_title']."\" /></a>";
	}

	public static function guestbookW($arrData=array()){
		self::commentW($arrData,true);
	}

	public static function commentW($arrData=array(),$bGuestbook=false){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_comment">','after_widget'=>'</div>','ul_class'=>'','image'=>1),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_comment_name')?Global_Extend::getOption('widget_comment_name'):G::L('最新评论');}
		if(!isset($arrData['comment_cutnum'])){$arrData['comment_cutnum']=intval(Global_Extend::getOption('widget_comment_cutnum'));}
		if(!isset($arrData['comment_photo'])){$arrData['comment_photo']=Global_Extend::getOption('widget_comment_photo');}
		if(!isset($arrData['comment_avatar'])){$arrData['comment_avatar']=Global_Extend::getOption('widget_comment_avatar');}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_comment_titleshow');}
		if($bGuestbook===false){
			$arrCommentDatas=Model::C('widget_comment');
			if($arrCommentDatas===false){
				Cache_Extend::front_widget_comment($arrData);
				$arrCommentDatas=Model::C('widget_comment');
			}
		}
		else{
			$arrCommentDatas=Model::C('widget_guestbook');
			if($arrCommentDatas===false){
				Cache_Extend::front_widget_guestbook($arrData);
				$arrCommentDatas=Model::C('widget_guestbook');
			}
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		foreach($arrCommentDatas as $arrCommentData){
			echo "<li class=\"comment-item\">";
			if($arrData['image']==1){
				echo "<i class=\"{$arrData['comment_photo']}\">
		 <img class=\"{$arrData['comment_avatar']}\" src=\"".($arrCommentData['user_id']>0?Blog_Extend::getGravatar($arrCommentData['user_id'],'small'):Blog_Extend::getGravatar('','email',array('email'=>$arrCommentData['comment_email'])))."\" alt=\"{$arrCommentData['comment_name']}\" width=\"32\" height=\"32\" />
		</i>
		<a href=\"".CommentModel::getACommentUrl($arrCommentData)."\">{$arrCommentData['comment_name']}</a>";
			}
			else{
				echo "<small><a href=\"".CommentModel::getACommentUrl($arrCommentData)."\" class=\"username\">{$arrCommentData['comment_name']}</a> ".G::L('评论')." <a href=\"".CommentModel::getACommentUrl($arrCommentData)."\" class=\"title\">{$arrCommentData['comment_name']}</a></small>";
			}
			echo "<p>".String::subString($arrCommentData['comment_content'],0,$arrData['comment_cutnum'])."</p></li>";
		}
		echo "</ul>{$arrData['after_widget']}";
		unset($arrCommentDatas);
	}

	public static function langW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_lang">','after_widget'=>'</div>','lang_dropdown'=>'lang-dropdown'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_lang_name')?Global_Extend::getOption('widget_lang_name'):G::L('国际化');}
		if(!isset($arrData['lang_select'])){$arrData['lang_select']=intval(Global_Extend::getOption('widget_lang_select'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_lang_titleshow');}
		$arrLangDatas=Model::C('widget_lang');
		if($arrLangDatas===false){
			Cache_Extend::front_widget_lang();
			$arrLangDatas=Model::C('widget_lang');
		}
		self::echoHeader($arrData);
		$sLangCookieName=APP_NAME.'_language';
		if($arrData['lang_select']==1){
			echo "<select name=\"{$arrData['lang_dropdown']}\" onchange='document.location.href=this.options[this.selectedIndex].value;'>
<option value=\"\">".G::L('选择语言')."</option>";
			foreach($arrLangDatas as $sLangData){
				echo "<option value='".__APP__."?l=".strtolower($sLangData)."' ".(strtolower(G::cookie($sLangCookieName))==strtolower($sLangData)?" selected":'').">{$sLangData}</option>";
			}
			echo "</select>";
		}
		else{
			foreach($arrLangDatas as $sLangData){
				echo "<a title=\"".G::L('语言')."\" href=\"?l=".strtolower($sLangData)."\" class=\"".(strtolower(G::cookie($sLangCookieName))==strtolower($sLangData)?" lang-item-current":'')."\" >{$sLangData}</a>";
			}
		}
		echo $arrData['after_widget'];
	}

	public static function taotaoW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_taotao">','after_widget'=>'</div>','before_dateline'=>'<p>','after_dateline'=>'</p>','ul_class'=>'','mobile_img'=>(file_exists(TEMPLATE_PATH.'/Public/Images/mobile.gif')?STYLE_IMG_DIR.'/mobile.gif':IMG_DIR.'/mobile.gif')),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_taotao_name')?Global_Extend::getOption('widget_taotao_name'):G::L('滔滔心情');}
		if(!isset($arrData['taotao_cutnum'])){$arrData['taotao_cutnum']=intval(Global_Extend::getOption('widget_taotao_cutnum'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_taotao_titleshow');}
		$arrTaotaoDatas=Model::C('widget_taotao');
		if($arrTaotaoDatas===false){
			Cache_Extend::front_widget_taotao($arrData);
			$arrTaotaoDatas=Model::C('widget_taotao');
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		foreach($arrTaotaoDatas as $arrTaotaoData){
			echo "<li class=\"taotao-item\">".
			($arrTaotaoData['taotao_ismobile']?"<img src=\"{$arrData['mobile_img']}\" title=\"".G::L('手机心情')."\" />":'').String::subString(strip_tags($arrTaotaoData['taotao_content']),0,$arrData['taotao_cutnum']).
			$arrData['before_dateline'].Date::smartDate($arrTaotaoData['create_dateline']).$arrData['after_dateline']."</li>";
		}
		echo "</ul><p><a href=\"".G::U('taotao/index')."\">".G::L('更多')."&raquo;</a></p></ul>{$arrData['after_widget']}";
		unset($arrTaotaoDatas);
	}

	public static function uploadcategoryW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_uploadcategory">','after_widget'=>'</div>','ul_class'=>''),$arrData);
		if(!isset($arrData['cover'])){$arrData['cover']=intval(Global_Extend::getOption('widget_uploadcategory_cover'));}
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_uploadcategory_name')?Global_Extend::getOption('widget_uploadcategory_name'):G::L('附件分类目录');}
		if(!isset($arrData['fixed'])){$arrData['fixed']=intval(Global_Extend::getOption('widget_uploadcategory_fixed'));}
		if(!isset($arrData['fixed'])){$arrData['fixed']=unserialize(Global_Extend::getOption('widget_uploadcategory_fixed_size'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_uploadcategory_titleshow');}
		$arrUploadcategoryDatas=Model::C('widget_uploadcategory');
		if($arrUploadcategoryDatas===false){
			Cache_Extend::front_widget_uploadcategory();
			$arrUploadcategoryDatas=Model::C('widget_uploadcategory');
		}
		foreach($arrUploadcategoryDatas as $nUploadcategoryDataKey=>$arrUploadcategory){
			$arrUploadcategoryDatas[$nUploadcategoryDataKey]['uploadcategory_cover']=Blog_Extend::getUploadcategoryCover($arrUploadcategory);
		}
		$arrUploadcategorySize=self::getUploadcategoryCoverSize($arrData);
		if(!isset($arrData['uploadcategory_num'])){
			$arrData['uploadcategory_num']=intval(Global_Extend::getOption('widget_ucategory_display_num'));
		}
		$arrUploadcategoryDatas=reset(array_chunk($arrUploadcategoryDatas,$arrData['uploadcategory_num'],true));
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		if(!empty($arrUploadcategoryDatas)){
			foreach($arrUploadcategoryDatas as $arrUploadcategoryData){
				echo "<li class=\"uploadcategory-item\">
	 <a title=\"{$arrUploadcategoryData['uploadcategory_name']}\" href=\"".PageType_Extend::getUploadcategoryUrl($arrUploadcategoryData)."\" >";
				if($arrData['cover']==1){
					echo "<img ".(!empty($arrUploadcategorySize['width'])?"width=\"{$arrUploadcategorySize['width']}\"":" ").(!empty($arrUploadcategorySize['height'])?"height=\"{$arrUploadcategorySize['height']}\"":" ")." src=\"{$arrUploadcategoryData['uploadcategory_cover']}\" />";
				}
				else{
					echo $arrUploadcategoryData['uploadcategory_name'];
				}
				echo "</a></li>";
			}
		}
		echo "</ul>{$arrData['after_widget']}";
		unset($arrUploadcategoryDatas);
	}

	public static function getUploadcategoryCoverSize($arrData=array()){
		if($arrData['fixed']==1){
			$arrUploadCategorySize['width']=$arrData['fixed_size'][0];
			$arrUploadCategorySize['height']=$arrData['fixed_size'][1];
		}
		else{
			$arrUploadCategorySize['width']='';
			$arrUploadCategorySize['height']='';
		}
		return $arrUploadCategorySize;
	}

	public static function recentimageW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_recentimage">','after_widget'=>'</div>'),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_recentimage_name')?Global_Extend::getOption('widget_recentimage_name'):G::L('最新图片');}
		if(!isset($arrData['image_size'])){$arrData['image_size']=unserialize(Global_Extend::getOption('widget_recentimage_image_size'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_recentimage_titleshow');}
		self::echoHeader($arrData);
		echo "<embed src=\"".__FRAMEWORK__."/Resource/Images/image_view.swf?bcastr_xml_url=".G::U('recentimage/index')."\" width=\"{$arrData['image_size'][0]}\" height=\"{$arrData['image_size'][1]}\"
	loop=\"false\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"
	type=\"application/x-shockwave-flash\" salign=\"T\" menu=\"false\" wmode=\"transparent\">
</embed>{$arrData['after_widget']}";
	}

	public static function menuW($arrData=array()){
		$arrData=self::getConfig(array('before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_menu">','after_widget'=>'</div>','ul_class'=>'','home'=>G::L('首页')),$arrData);
		if(!isset($arrData['block_title'])){$arrData['block_title']=Global_Extend::getOption('widget_menu_name')?Global_Extend::getOption('widget_menu_name'):G::L('菜单');}
		if(!isset($arrData['widget_class'])){$arrData['widget_class']=unserialize(Global_Extend::getOption('widget_menu_widget_class'));}
		if(!isset($arrData['widget_title'])){$arrData['widget_title']=Global_Extend::getOption('widget_menu_titleshow');}
		$arrMenuDatas=Model::C('menu');
		if($arrMenuDatas===false){
			$arrMenuDatas=Cache_Extend::front_menu($arrData['home']);
		}
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">";
		foreach($arrMenuDatas as $arrMenuData){
			echo "<li class=\"menu-item\">
		<a ".(!empty($arrMenuData['style'])?"style=\"{$arrMenuData['style']}\"":'')." title=\"{$arrMenuData['title']}-{$arrMenuData['description']}\" href=\"{$arrMenuData['link']}\" {$arrMenuData['target']}>
		 {$arrMenuData['title']}
		</a>
	</li>";
		}
		echo "</ul>{$arrData['after_widget']}";
		unset($arrMenuDatas,$arrMenuData);
	}

	public static function membermenuW($arrData=array()){
		$arrData=self::getConfig(array('widget_title'=>1,'before_title'=>'<h2>','after_title'=>'</h2>','before_widget'=>'<div class="widget widget_membermenu">','after_widget'=>'</div>','before_seccond_title'=>'<b>','after_seccond_title'=>'</b>','block_title'=>G::L('个人中心'),'ul_class'=>''),$arrData);
		self::echoHeader($arrData);
		echo "<ul class=\"{$arrData['ul_class']}\">
<li>{$arrData['before_seccond_title']}".G::L('个人资料')."{$arrData['after_seccond_title']}</li>
<li ".(MODULE_NAME==='member' && (ACTION_NAME==='avatar' || ACTION_NAME==='upload')?"class=\"current\"":'')."><a href=\"".G::U('member/avatar')."\">".G::L('修改头像')."</a></li>
<li ".(MODULE_NAME==='member' && ACTION_NAME==='index'?"class=\"current\"":'')."><a href=\"".G::U('member/index')."\">".G::L('个人中心')."</a></li>
<li ".(MODULE_NAME==='user' && ACTION_NAME==='show'?"class=\"current\"":'')."><a href=\"".PageType_Extend::getAuthorUrl($GLOBALS['___login___'])."\">".G::L('个人资料')."</a></li>
<li ".(MODULE_NAME==='pm'?"class=\"current\"":'')."><a href=\"".G::U('pm/index')."\">".G::L('短消息')."</a></li>
<li>{$arrData['before_seccond_title']}".G::L('感兴趣的')."{$arrData['after_seccond_title']}</li>
<li ".(MODULE_NAME==='my' && ACTION_NAME==='friend'?"class=\"current\"":'')."><a href=\"".G::U('my/friend')."\">".G::L('我的好友')."</a></li>
<li><a href=\"".PageType_Extend::getUserUrl($GLOBALS['___login___'])."\">".G::L('我的文章')."</a></li>
<li>{$arrData['before_seccond_title']}".G::L('安全')."{$arrData['after_seccond_title']}</li>
<li ".(MODULE_NAME==='member' && ACTION_NAME==='password'?"class=\"current\"":'')."><a href=\"".G::U('member/password')."\">".G::L('修改密码')."</a></li>
</ul>{$arrData['after_widget']}";
	}

	public static function loginlog($nDisplayNum=6){
		$arrLoginlogData=Model::C('loginlog');
		if($arrLoginlogData===false){
			$arrLoginlogData=Cache_Extend::front_loginlog($nDisplayNum);
		}
		return $arrLoginlogData;
	}

	public static function categoryArticle($arrData=array()){
		$arrDefaultOption=array('category_id'=>0,'category_children'=>'');
		if(!empty($arrData)){$arrData=array_merge($arrDefaultOption,$arrData);}
		else{$arrData=$arrDefaultOption;}
		$arrMap=array();
		if($arrData['category_id']!=0){
			$arrMap['category_id']=array('in',$arrData['category_id']);
		}
		if($arrData['category_children']!==''){
			$arrMap['category_parentid']=$arrData['category_children'];
		}
		if(empty($arrMap)){
			$arrCategoryData=Model::C('widget_category');
			if($arrCategoryData===false){
				Cache_Extend::front_widget_category();
				$arrCategoryData=Model::C('widget_category');
			}
		}
		else{
			$arrCategoryData=CategoryModel::F()->where($arrMap)->asArray()->all()->order('`category_compositor` ASC')->query();
		}
		return $arrCategoryData;
	}

}
