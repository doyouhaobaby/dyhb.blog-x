-- DYHB.BLOG X 数据库数据
-- version 2.0.1
-- http://www.doyouhaobaby.net
--
-- 开发: 点牛科技（成都）
-- 网站: http://dianniu.net

--
-- DYHB.BLOG X默认数据
--

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbblogx_blog`
--

INSERT INTO `#@__blog` (`blog_id`,`blog_title`,`blog_dateline`,`update_dateline`,`blog_content`,`blog_from`,`blog_fromurl`,`blog_urlname`,`user_id`,`category_id`,`blog_thumb`,`blog_viewnum`,`blog_uploadnum`,`blog_password`,`blog_istop`,`blog_isshow`,`blog_islock`,`blog_ispage`,`blog_trackbacknum`,`blog_allowedtrackback`,`blog_commentnum`,`blog_good`,`blog_bad`,`blog_ismobile`,`blog_keyword`,`blog_description`,`blog_excerpt`,`blog_gotourl`,`blog_isblank`,`blog_ip`,`blog_lastpost`,`blog_color`,`blog_type`) VALUES
(1,'欢迎使用DYHB.BLOG X！',1332682510,1332682510,'<p>DYHB.BLOG_X 2.0.1今天发布了，希望大家多多支持《<a href="http://bbs.doyouhaobaby.net">寂静的论坛</a>》,关于DYHB.BLOG_X的使用大家也可以讨论的。</p>','本站原创','','',1,1,'',4,0,'',0,1,0,0,0,1,1,0,0,0,'','','<p>享受生活，感受非凡的DYHB.BLOG_X!</p>','',0,'127.0.0.1','a:4:{s:10:"comment_id";s:1:"1";s:15:"create_dateline";i:1319187928;s:9:"user_name";s:6:"跌名";s:7:"user_id";i:-1;}','','');

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbblogx_category`
--

INSERT INTO `#@__category` (`category_id`,`category_name`,`create_dateline`,`update_dateline`,`category_urlname`,`category_compositor`,`category_logo`,`category_parentid`,`category_keyword`,`category_description`,`category_introduce`,`category_extra`,`category_blogs`,`category_comments`,`category_todaycomments`,`category_gotourl`,`category_color`,`category_rule`,`category_showsub`,`category_lastpost`,`category_columns`) VALUES
(1,'我的心情',1332682510,1332682510,'',0,'',0,'','','我的个人心情，记录生活的点点滴滴！','',1,1,1,'','','',0,'',0);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbblogx_taotao`
--

INSERT INTO `#@__taotao` (`taotao_id`,`taotao_content`,`create_dateline`,`user_id`,`taotao_ismobile`,`taotao_commentnum`,`taotao_islock`) VALUES
(1,'让心情更自然！<br />',1332682510,1,0,0,0);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbblogx_comment`
--

INSERT INTO `#@__comment` (`comment_id`,`create_dateline`,`update_dateline`,`user_id`,`category_id`,`comment_name`,`comment_content`,`comment_email`,`comment_url`,`comment_isshow`,`comment_ip`,`comment_parentid`,`comment_isreplymail`,`comment_ismobile`,`comment_relationtype`,`comment_relationvalue`) VALUES
(1,1332682510,0,-1,1,'DYHB.BLOG X先生','[emot]smile[/emot]欢迎加入DYHB.BLOG X 的大家庭。','635750556@qq.com','http://doyouhaobaby.net',1,'127.0.0.1',0,0,0,'blog',1);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_countcache`
--

INSERT INTO `#@__countcache` (`countcache_id`,`countcache_usersnum`,`countcache_topicsnum`,`countcache_postsnum`,`countcache_todaynum`,`countcache_yesterdaynum`,`countcache_mostnum`,`countcache_lastuser`,`countcache_todaydate`) VALUES
(1,1,1,1,1,1,1,'admin','0000-00-00');

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_emot`
--

INSERT INTO `#@__emot` (`emot_id`,`emot_name`,`emot_admin`,`user_id`,`create_dateline`,`update_dateline`,`emot_image`,`emot_thumb`,`empt_compositor`) VALUES
(1,'anger','admin',1,1313821235,0,'anger.gif','thumbnail/anger.gif',0),
(2,'bad','admin',1,1313821235,0,'bad.gif','thumbnail/bad.gif',0),
(3,'coffee','admin',1,1313821235,0,'coffee.gif','thumbnail/coffee.gif',0),
(4,'cool','admin',1,1313821235,0,'cool.gif','thumbnail/cool.gif',0),
(5,'cry','admin',1,1313821235,0,'cry.gif','thumbnail/cry.gif',0),
(6,'dog','admin',1,1313821235,0,'dog.gif','thumbnail/dog.gif',0),
(7,'envy','admin',1,1313821235,0,'envy.gif','thumbnail/envy.gif',0),
(8,'fear','admin',1,1313821235,0,'fear.gif','thumbnail/fear.gif',0),
(9,'grin','admin',1,1313821235,0,'grin.gif','thumbnail/grin.gif',0),
(10,'hamarneh','admin',1,1313821235,0,'hamarneh.gif','thumbnail/hamarneh.gif',0),
(11,'kill','admin',1,1313821235,0,'kill.gif','thumbnail/kill.gif',0),
(12,'love','admin',1,1313821235,0,'love.gif','thumbnail/love.gif',0),
(13,'ok','admin',1,1313821235,0,'ok.gif','thumbnail/ok.gif',0),
(14,'pig','admin',1,1313821235,0,'pig.gif','thumbnail/pig.gif',0),
(15,'puke','admin',1,1313821235,0,'puke.gif','thumbnail/puke.gif',0),
(16,'question','admin',1,1313821235,0,'question.gif','thumbnail/question.gif',0),
(17,'shock','admin',1,1313821235,0,'shock.gif','thumbnail/shock.gif',0),
(18,'shuai','admin',1,1313821235,0,'shuai.gif','thumbnail/shuai.gif',0),
(19,'shy','admin',1,1313821235,0,'shy.gif','thumbnail/shy.gif',0),
(20,'sleepy','admin',1,1313821235,0,'sleepy.gif','thumbnail/sleepy.gif',0),
(21,'smile','admin',1,1313821235,0,'smile.gif','smile.gif',0),
(22,'smoke','admin',1,1313821235,0,'smoke.gif','smoke.gif',0),
(23,'stupid','admin',1,1313821235,0,'stupid.gif','stupid.gif',0),
(24,'sweat','admin',1,1313821235,0,'sweat.gif','sweat.gif',0),
(25,'thumbdown','admin',1,1313821235,0,'thumbdown.gif','thumbdown.gif',0),
(26,'unhappy','admin',1,1313821235,0,'unhappy.gif','thumbnail/unhappy.gif',0),
(27,'uplook','admin',1,1313821235,0,'uplook.gif','thumbnail/uplook.gif',0),
(28,'zan','admin',1,1313821235,1317207189,'zan.gif','thumbnail/zan.gif',3),
(29,'demo1','admin',1,1317210329,0,'Custom/Demo/1.gif','Custom/Demo/thumbnail/1.gif',0),
(30,'demo2','admin',1,1317212179,1317212204,'Custom/Demo/2.gif','Custom/Demo/thumbnail/2.gif',0),
(31,'demo3','admin',1,1317212179,1317212204,'Custom/Demo/3.gif','Custom/Demo/thumbnail/3.gif',0),
(32,'demo4','admin',1,1317212179,1317212204,'Custom/Demo/4.gif','Custom/Demo/thumbnail/4.gif',0),
(33,'demo5','admin',1,1317212179,1317212205,'Custom/Demo/5.gif','Custom/Demo/thumbnail/5.gif',0),
(34,'demo6','admin',1,1317212179,1317212204,'Custom/Demo/6.gif','Custom/Demo/thumbnail/6.gif',0),
(35,'demo7','admin',1,1317212179,1317212204,'Custom/Demo/7.gif','Custom/Demo/thumbnail/7.gif',0),
(36,'demo8','admin',1,1317212179,1317212205,'Custom/Demo/8.gif','Custom/Demo/thumbnail/8.gif',0),
(37,'demo9','admin',1,1317212179,1317212204,'Custom/Demo/9.gif','Custom/Demo/thumbnail/9.gif',0),
(38,'demo10','admin',1,1317212179,1317212204,'Custom/Demo/10.gif','Custom/Demo/thumbnail/10.gif',0),
(39,'demo11','admin',1,1317212179,1317212205,'Custom/Demo/11.gif','Custom/Demo/thumbnail/11.gif',0),
(40,'demo12','admin',1,1317212179,1317212204,'Custom/Demo/12.gif','Custom/Demo/thumbnail/12.gif',0),
(41,'demo13','admin',1,1317212179,1317212204,'Custom/Demo/13.gif','Custom/Demo/thumbnail/13.gif',0),
(42,'demo14','admin',1,1317212179,1317212205,'Custom/Demo/14.gif','Custom/Demo/thumbnail/14.gif',0),
(43,'demo15','admin',1,1317212179,1317212204,'Custom/Demo/15.gif','Custom/Demo/thumbnail/15.gif',0),
(44,'demo16','admin',1,1317212179,1317212204,'Custom/Demo/16.gif','Custom/Demo/thumbnail/16.gif',0),
(45,'demo17','admin',1,1317212179,1317212205,'Custom/Demo/17.gif','Custom/Demo/thumbnail/17.gif',0),
(46,'demo18','admin',1,1317212179,1317212204,'Custom/Demo/18.gif','Custom/Demo/thumbnail/18.gif',0),
(47,'demo19','admin',1,1317212179,1317212204,'Custom/Demo/19.gif','Custom/Demo/thumbnail/19.gif',0),
(48,'demo20','admin',1,1317212179,1317212205,'Custom/Demo/20.gif','Custom/Demo/thumbnail/20.gif',0),
(49,'demo21','admin',1,1317212179,1317212204,'Custom/Demo/21.gif','Custom/Demo/thumbnail/21.gif',0),
(50,'demo22','admin',1,1317212670,0,'Custom/Demo/22.gif','Custom/Demo/thumbnail/22.gif',0);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_group`
--

INSERT INTO `#@__group` (`group_id`, `group_name`, `group_title`, `create_dateline`, `update_dateline`, `group_status`, `group_sort`, `group_show`) VALUES
(1, 'rbac', '权限', 1296454621, 1323676504, 1, 5, 1),
(2, 'blog', '内容', 1309436022, 1323676504, 1, 2, 1),
(3, 'option', '设置', 1312119640, 1323676504, 1, 1, 1),
(4, 'admin', '站长', 1312119691, 1323676504, 1, 8, 1),
(6, 'upload', '媒体', 1312119780, 1323676504, 1, 4, 1),
(7, 'run', '运营', 1312119811, 1323676504, 1, 7, 1),
(8, 'theme', '界面', 1312119847, 1323676504, 1, 3, 1),
(9, 'adv', '高级', 1312121353, 1323676504, 1, 6, 1),
(10, 'plugin', '插件', 1331134399, 1331134427, 1, 9, 1);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_node`
--

INSERT INTO `#@__node` (`node_id`, `node_name`, `node_title`, `node_status`, `node_remark`, `node_sort`, `node_parentid`, `node_level`, `node_type`, `group_id`, `create_dateline`, `update_dateline`) VALUES
(1, 'admin', 'admin后台管理', 1, '', 1, 0, 1, 0, 1, 0, 0),
(4, 'admin@index', '默认首页', 1, '', 5, 1, 2, 0, 9, 0, 1323677150),
(2, 'admin@role', '角色管理', 1, '', 3, 1, 2, 0, 1, 0, 0),
(3, 'admin@user', '用户管理', 1, '', 14, 1, 2, 0, 1, 0, 1323675420),
(7, 'admin@backup', '数据备份', 1, '', 1, 1, 2, 0, 4, 0, 1323677185),
(5, 'admin@group', '节点分组', 1, '', 2, 1, 2, 0, 1, 0, 0),
(6, 'admin@node', '节点管理', 1, '', 1, 1, 2, 0, 1, 0, 0),
(8, 'admin@badword', '词语过滤', 1, '', 9, 1, 2, 0, 2, 0, 1323677322),
(9, 'admin@domain', '域名解析', 1, '', 5, 1, 2, 0, 4, 0, 1323677185),
(10, 'admin@option', '系统设置', 1, '', 2, 1, 2, 0, 3, 0, 1323675204),
(11, 'admin@mail', '邮件列表', 1, '', 2, 1, 2, 0, 4, 0, 1323677185),
(12, 'admin@blog', '文章管理', 1, '', 1, 1, 2, 0, 2, 0, 1323677322),
(13, 'admin@category', '文章分类', 1, '', 3, 1, 2, 0, 2, 0, 1323677322),
(14, 'admin@tag', '标签管理', 1, '', 4, 1, 2, 0, 2, 0, 1323677322),
(15, 'admin@trackback', '引用管理', 1, '', 7, 1, 2, 0, 2, 0, 1323677322),
(16, 'admin@taotao', '微博管理', 1, '', 8, 1, 2, 0, 2, 0, 1323677322),
(17, 'admin@link', '友情衔接', 1, '', 1, 1, 2, 0, 7, 0, 1323677165),
(18, 'admin@upload', '媒体管理', 1, '', 1, 1, 2, 0, 6, 0, 1323676876),
(19, 'admin@uploadcategory','媒体归档', 1, '', 2, 1, 2, 0, 6, 0, 1323676876),
(20, 'admin@theme', '主题管理', 1, '', 1, 1, 2, 0, 8, 0, 1323676976),
(21, 'admin@menusort', '导航排序', 1, '', 3, 1, 2, 0, 8, 0, 1323676976),
(22, 'admin@page', '页面管理', 1, '', 2, 1, 2, 0, 2, 0, 1323677322),
(23, 'admin@widget', 'Widget', 1, '', 2, 1, 2, 0, 8, 0, 1323676976),
(24, 'admin@cache', '缓存服务', 1, '', 1, 1, 2, 0, 9, 1313915218, 1323677150),
(25, 'admin@relatedblog', '相关日志', 1, '', 10, 1, 2, 0, 2, 1314541358, 1323677322),
(26, 'admin@blogtrackback', '日志引用', 1, '', 11, 1, 2, 0, 2, 1314551188, 1323677322),
(27, 'admin@blogoption', '博客设置', 1, '', 1, 1, 2, 0, 3, 1314614998, 1323675204),
(28, 'admin@comment', '评论管理', 1, '', 5, 1, 2, 0, 2, 1315226863, 1323677322),
(29, 'admin@guestbook', '留言管理', 1, '', 6, 1, 2, 0, 2, 1315226899, 1323677322),
(30, 'admin@mp3player', 'Mp3播放器', 1, '', 4, 1, 2, 0, 6, 1315300307, 1323676876),
(31, 'admin@mp3playerdata', 'Mp3数据', 1, '', 3, 1, 2, 0, 6, 1315326653, 1323676876),
(32, 'admin@sound', '短消息声音', 1, '', 4, 1, 2, 0, 3, 1317047900, 1323675204),
(33, 'admin@pm', '短消息', 1, '', 2, 1, 2, 0, 7, 1317151208, 1323677165),
(34, 'admin@emot', '表情管理', 1, '', 4, 1, 2, 0, 9, 1317201353, 1323677150),
(35, 'admin@searchindex', '搜索记录', 1, '', 3, 1, 2, 0, 7, 1317271448, 1323677165),
(36, 'admin@frontendoptimization', '前端优化', 1, '', 4, 1, 2, 0, 7, 1317704033, 1323677165),
(37, 'admin@themeoption', '界面设置', 1, '', 4, 1, 2, 0, 8, 1317878590, 1323676976),
(38, 'admin@boarddata', '论坛统计', 1, '', 2, 1, 2, 0, 9, 1318625831, 1323677150),
(39, 'admin@boardcachedata', '缓存统计', 1, '', 3, 1, 2, 0, 9, 1318679544, 1323677150),
(40, 'admin@dataindex', '数据调用', 1, '', 3, 1, 2, 0, 4, 1318889948, 1323677185),
(41, 'admin@wapoption', 'Wap 设置', 1, '', 3, 1, 2, 0, 3, 1320429496, 1323675204),
(42, 'admin@loginlog', '登录记录', 1, '', 4, 1, 2, 0, 4, 1320681931, 1323677185),
(43, 'admin@resource', '扩展中心', 1, '', 0, 1, 2, 0, 10, 1331134585, 1331134674),
(44, 'admin@plugin', '插件列表', 1, '', 1, 1, 2, 0, 10, 1331135507, 0);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_option`
--

INSERT INTO `#@__option` (`option_name`,`option_value`) VALUES
('pmlimit1day','100'),
('pmfloodctrl','15'),
('pmcenter','1'),
('sendpmseccode','0'),
('accessemail',''),
('censoremail',''),
('censorusername',''),
('maildefault',''),
('mailserver',''),
('mailport','25'),
('mailauth','1'),
('mailfrom',''),
('mailauth_username',''),
('mailauth_password',''),
('timeoffset','Asia/Shanghai'),
('pmsendregdays','5'),
('mailsend','2'),
('maildelimiter','1'),
('changepasswordseccode','1'),
('changeinfoseccode','0'),
('loginseccode','0'),
('admineverynum','16'),
('upload_file_default','1'),
('images_water_position_offset','0'),
('file_input_num','12'),
('upload_store_where_type','month'),
('auto_excerpt','1'),
('blog_url',''),
('blog_name','DYHB.BLOG X'),
('all_allowed_upload_type','a:11:{i:0;s:3:"mp3";i:1;s:4:"jpeg";i:2;s:3:"jpg";i:3;s:3:"gif";i:4;s:3:"bmp";i:5;s:3:"png";i:6;s:4:"rmvb";i:7;s:3:"wma";i:8;s:3:"asf";i:9;s:3:"swf";i:10;s:3:"flv";}'),
('uploadfile_maxsize','-1'),
('blogfile_thumb_width_heigth','a:2:{i:0;s:3:"500";i:1;s:3:"500";}'),
('is_makeimage_thumb','1'),
('is_images_water_mark','1'),
('images_water_type','text'),
('images_water_position','9'),
('images_water_mark_img_imgurl',''),
('images_water_mark_text_content','DYHB-X-BLOG'),
('images_water_mark_text_color','#000000'),
('images_water_mark_text_fontsize','30'),
('images_water_mark_text_fontpath','framework-font'),
('blog_description','PHP 开源博客'),
('blog_seo_descrition','本站是 DoYouHaoBaby-X Blog! 博客产品的官方交流站点。提供程序交流、产品扩展、技术支持等全方位服务。'),
('blog_seo_keywords','PHP 开源 框架 技术支持 程序发布'),
('blog_program_name','DYHB.BLOG X'),
('blog_program_url','http://doyouhaobaby.net'),
('blog_seo_robots','all'),
('blog_logo',''),
('display_blog_list_num','10'),
('blog_list_auto_excerpt_num','300'),
('images_water_mark_text_fonttype','airbrush.ttf'),
('admintemplateeverynum','6'),
('admin_theme_name','Default'),
('front_theme_name','Default'),
('admin_email',''),
('blog_charset','UTF-8'),
('html_type','text/html'),
('normal_menu','a:7:{i:0;s:3:"tag";i:1;s:4:"link";i:2;s:6:"search";i:3;s:6:"record";i:4;s:6:"upload";i:5;s:6:"taotao";i:6;s:9:"guestbook";}'),
('normal_menu_option','a:7:{s:3:"tag";a:7:{s:5:"title";s:9:"标签云";s:11:"description";s:54:"标签云在前台列出所有标签，方便浏览。";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:4:"link";a:7:{s:5:"title";s:12:"友情衔接";s:11:"description";s:42:"列出合作伙伴或者朋友的网站。";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:6:"search";a:7:{s:5:"title";s:6:"搜索";s:11:"description";s:12:"搜索页面";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:6:"record";a:7:{s:5:"title";s:12:"日志归档";s:11:"description";s:30:"归档日志，方便查找。";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:6:"upload";a:7:{s:5:"title";s:12:"我的相册";s:11:"description";s:24:"列出上传的相片等";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:6:"taotao";a:7:{s:5:"title";s:9:"微博客";s:11:"description";s:21:"放飞你的心声。";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";i:0;s:6:"target";i:0;s:6:"system";i:1;}s:9:"guestbook";a:7:{s:5:"title";s:9:"留言板";s:11:"description";s:27:"方便访客与你交流。";s:4:"link";s:1:"#";s:5:"style";N;s:5:"color";s:1:"0";s:6:"target";s:1:"0";s:6:"system";i:1;}}'),
('custom_normal_menu','a:0:{}'),
('blog_icp',''),
('widget_blog_name','博客形象'),
('calendar_widget_title','日历'),
('widget_link_name','友情衔接'),
('widget_link_display_num','5'),
('widget_recentpost_name','最新日志'),
('widget_recentpost_display_num','5'),
('widget_randpost_name','随机日志'),
('widget_randpost_display_num','5'),
('search_widget_title','搜索'),
('search_widget_button_text','搜索'),
('widget_yearhotpost_title_cutnum','30'),
('widget_taotao_name','滔滔心情'),
('widget_taotao_display_num','5'),
('widget_category_name','分类目录'),
('widget_archive_name','日志归档'),
('widget_archive_display_num','8'),
('widget_link_display_rand','0'),
('widget_comment_name','最新评论'),
('widget_comment_display_num','5'),
('widget_comment_cutnum','50'),
('widget_guestbook_name','最新留言'),
('widget_daycommentpost_title_cutn','30'),
('widget_guestbook_display_num','5'),
('widget_hottag_name','热门标签'),
('widget_hottag_display_num','5'),
('widget_hottag_color','a:5:{i:0;s:7:"#00bb11";i:1;s:7:"#dd7722";i:2;s:7:"#dd55ff";i:3;s:7:"#00aadd";i:4;s:7:"#000000";}'),
('widget_hotpost_name','热门日志'),
('widget_hotpost_display_num','5'),
('widget_commentpost_name','最受欢迎日志'),
('widget_commentpost_display_num','5'),
('widget_yearhotpost_name','年度日志排行'),
('widget_yearhotpost_display_num','5'),
('widget_yearcommentpost_name','年度最受欢迎日志'),
('widget_yearcommentp_display_num','5'),
('widget_monthhotpost_name','当月排行日志'),
('widget_monthhotpost_display_num','5'),
('widget_monthcommentpost_name','当月最受欢迎日志'),
('widget_monthcommentp_display_num','5'),
('widget_dayhotpost_name','今日排行日志'),
('widget_dayhotpost_display_num','5'),
('widget_daycommentpost_name','今日最受欢迎日志'),
('widget_daycomment_display_num','5'),
('widget_uploadcategory_name','附件分类目录'),
('widget_ucategory_display_num','5'),
('widget_recentimage_name','最新图片'),
('widget_recentimage_display_num','5'),
('widget_recentimage_autoplaytime','3'),
('widget_theme_name','主题切换'),
('widget_pagepost_display_num','5'),
('widget_pagepost_name','页面'),
('widget_menu_name','菜单'),
('widget_static_name','博客统计'),
('today_visited_num','1'),
('all_visited_num','1'),
('widget_rss_name','Rss 订阅'),
('widgets_main1','a:8:{i:0;s:6:"search";i:1;s:4:"blog";i:2;s:5:"admin";i:3;s:8:"calendar";i:4;s:8:"category";i:5;s:6:"hottag";i:6;s:4:"link";i:7;s:3:"rss";}'),
('display_blog_friend_list_num','15'),
('show_content_emot','1'),
('global_badword_on','1'),
('widget_single_mp3player_width','400'),
('content_count_download','1'),
('content_auto_add_link','1'),
('content_short_en_url','1'),
('content_url_max_len','50'),
('content_auto_resize_img','0'),
('write_blog_editor','kindeditor'),
('only_login_can_view_upload','0'),
('is_limit_upload_leech','0'),
('widget_yearcommentpost_cutnum','5'),
('widgets_main2','a:11:{i:0;s:5:"admin";i:1;s:7:"archive";i:2;s:4:"blog";i:3;s:8:"calendar";i:4;s:8:"category";i:5;s:11:"commentpost";i:6;s:8:"pagepost";i:7;s:12:"monthhotpost";i:8;s:16:"monthcommentpost";i:9;s:4:"menu";i:10;s:4:"link";}'),
('widgets_main3','a:1:{i:0;s:5:"admin";}'),
('widgets_main4','a:5:{i:0;s:7:"archive";i:1;s:8:"category";i:2;s:11:"commentpost";i:3;s:14:"daycommentpost";i:4;s:7:"comment";}'),
('widgets_main5',''),
('widgets_main6',''),
('widgets_main7',''),
('widgets_main8',''),
('widgets_main9',''),
('widgets_main10',''),
('widgets_footer1',''),
('widgets_footer2',''),
('widgets_footer3',''),
('widgets_footer4',''),
('widgets_footer5',''),
('widgets_footer6',''),
('widgets_footer7',''),
('widgets_footer8',''),
('widgets_footer9',''),
('widgets_footer10',''),
('widgets_store','a:29:{i:0;s:5:"admin";i:1;s:7:"archive";i:2;s:4:"blog";i:3;s:8:"calendar";i:4;s:8:"category";i:5;s:11:"commentpost";i:6;s:7:"comment";i:7;s:14:"daycommentpost";i:8;s:10:"dayhotpost";i:9;s:9:"guestbook";i:10;s:7:"hotpost";i:11;s:6:"hottag";i:12;s:4:"lang";i:13;s:4:"link";i:14;s:4:"menu";i:15;s:16:"monthcommentpost";i:16;s:12:"monthhotpost";i:17;s:8:"pagepost";i:18;s:8:"randpost";i:19;s:11:"recentimage";i:20;s:10:"recentpost";i:21;s:3:"rss";i:22;s:6:"search";i:23;s:6:"static";i:24;s:6:"taotao";i:25;s:5:"theme";i:26;s:14:"uploadcategory";i:27;s:14:"uploadcategory";i:28;s:11:"yearhotpost";}'),
('widget_admin_name','管理操作'),
('widget_calendar_name','日历'),
('widget_lang_name','国际化'),
('widget_search_name','搜索'),
('custom_widget_option','a:0:{}'),
('widget_archive_link_select','1'),
('widget_yearhot_post_title_cutnum','5'),
('widget_archive_show_blog_num','1'),
('widget_uploadcategory_cover','0'),
('widget_uploadcategory_fixed','0'),
('widget_uploadcategory_fixed_size','a:2:{i:0;s:2:"80";i:1;s:2:"80";}'),
('widget_blog_show_admin','1'),
('widget_blog_admin_title','联系管理员'),
('widget_category_tree','1'),
('widget_theme_select','1'),
('widget_taotao_cutnum','30'),
('widget_search_showassistivetext','0'),
('widget_search_input_class','field'),
('widget_search_submit_class','submit_class'),
('widget_search_assistive_text','assistive-text'),
('widget_recentpost_title_cutnum','30'),
('widget_recentimage_image_size','a:2:{i:0;i:200;i:1;i:150;}'),
('widget_randpost_title_cutnum','20'),
('widget_pagepost_title_cutnum','30'),
('widget_monthhotpost_title_cutnum','30'),
('widget_monthcommentpost_tcutnum','30'),
('widget_menu_widget_class','a:2:{i:0;s:18:"widget-menu-active";i:1;s:18:"widget-menu-normal";}'),
('widget_link_description','0'),
('widget_link_images','1'),
('widget_hotpost_title_cutnum','30'),
('widget_guestbook_comment_cutnum','30'),
('widget_guestbook_comment_photo','comment-photo'),
('widget_guestbook_comment_avatar','comment-avatar'),
('widget_daycommentpost_cutnum','30'),
('widget_commentpost_title_cutnum','30'),
('widget_comment_photo','comment-photo'),
('widget_comment_avatar','comment-avatar'),
('avatar_crop_jpeg_quality','100'),
('avatar_origin_jpeg_quality','100'),
('default_comment_status','1'),
('display_blog_comment_list_num','5'),
('widget_admin_titleshow','1'),
('widget_archive_titleshow','1'),
('widget_blog_titleshow','1'),
('widget_comment_titleshow','1'),
('widget_daycommentpost_titleshow','1'),
('widget_dayhotpost_titleshow','1'),
('widget_guestbook_titleshow','1'),
('widget_calendar_titleshow','1'),
('widget_category_titleshow','1'),
('widget_commentpost_titleshow','1'),
('widget_hotpost_titleshow','1'),
('widget_hottag_titleshow','1'),
('widget_lang_titleshow','1'),
('widget_link_titleshow','1'),
('widget_menu_titleshow','1'),
('widget_monthcommentpost_tshow','1'),
('widget_monthhotpost_titleshow','1'),
('widget_pagepost_titleshow','1'),
('widget_recentpost_titleshow','1'),
('widget_rss_titleshow','1'),
('widget_search_titleshow','0'),
('widget_static_titleshow','1'),
('widget_taotao_titleshow','1'),
('widget_theme_titleshow','1'),
('widget_yearhotpost_titleshow','1'),
('widget_randpost_titleshow','1'),
('widget_recentimage_titleshow','1'),
('widget_yearcommentpost_titleshow','1'),
('widget_uploadcategory_titleshow','1'),
('widget_lang_select','1'),
('widget_dayhotpost_title_cutnum','30'),
('thread_comments_depth','5'),
('comment_avatar_size','32'),
('avatar_default','identicon'),
('avatars_rating','G'),
('use_blog_system_avatar','1'),
('comment_url_nofollow','1'),
('show_comment_avatar','1'),
('not_limit_leech_domail','a:1:{i:0;s:0:"";}'),
('is_upload_direct_to_really_path','0'),
('is_upload_inline','1'),
('is_hide_upload_really_path','0'),
('widget_single_mp3player_height','40'),
('widget_single_mp3player_bgcolor','#ffffff'),
('relatedblog_type','category'),
('relatedblog_sort','views_desc'),
('relatedblog_num','5'),
('relatedblog_inrss','1'),
('relatedblog_title_cutnum','25'),
('blogtrackback_sort','dateline_desc'),
('blogtrackback_num','0'),
('blogtrackback_title_cutnum','30'),
('blogtrackback_inrss','1'),
('blogtrackback_nofollow','1'),
('blogtrackback_dateline','1'),
('blogtrackback_dateline_type','Y-m-d h:I'),
('blog_artdialog_skin','chrome'),
('comment_dateformat','Y-m-d h:i:s'),
('html_lang','zh-CN'),
('blog_rss1_num','10'),
('blog_rss1_excerpt','0'),
('blog_rss2_num','10'),
('blog_rss2_excerpt','0'),
('blog_rss_comment_num','10'),
('trackback_url_expire','1'),
('allow_trackback','1'),
('trackback_url_javascript','1'),
('trackback_url_math','1'),
('audit_blog_comment','0'),
('audit_comment','0'),
('seccode','1'),
('blog_comment_seccode','0'),
('comment_mail_to_admin','0'),
('comment_mail_to_author','0'),
('day_time','20111021'),
('display_blog_guestbook_list_num','15'),
('blog_guestbook_seccode','0'),
('audit_blog_guestbook','0'),
('display_blog_taotao_list_num','15'),
('display_taotao_comment_list_num','5'),
('guestbook_status','1'),
('blog_comment_status','1'),
('taotao_status','1'),
('audit_taotao_comment','0'),
('display_blog_tag_list_num','100'),
('tag_list_show_color','1'),
('tag_list_show_fontsize','1'),
('allow_search_comments','1'),
('search_keywords_min_length','3'),
('search_post_space','30'),
('show_search_result_message',''),
('the_taotao_description','我是滔滔心情的介绍，你可以在后台更改！'),
('the_tag_description','我是标签列表的的介绍，你可以在后台修改！'),
('the_search_description','我是搜索文章的介绍'),
('the_404_description','抱歉，您浏览的页面未找到。也许搜索能帮到您。'),
('the_record_description','我是日志归档的介绍，你可以在后台修改！'),
('the_link_description','我是友情衔接的介绍，你可以在后台修改！'),
('the_guestbook_description','我是留言板的介绍，你有什么问题需要请教吗？'),
('display_blog_ucategory_list_num','6'),
('the_uploadcateogory_description','我是相册描述，你可以在后台修改它。'),
('display_blog_upload_list_num','15'),
('the_upload_description','这是相片列表描述，你可以在后台更改。'),
('display_upload_comment_list_num','5'),
('upload_status','1'),
('audit_upload_comment','0'),
('singlemp3player_bg','#fabda3'),
('singlemp3player_leftbg','#62c5f9'),
('singlemp3player_lefticon','#F2F2F2'),
('singlemp3player_rightbg','#62c5f9'),
('singlemp3player_rightbghover','#8080ff'),
('singlemp3player_righticon','#c8e68e'),
('singlemp3player_righticonhover','#FFFFFF'),
('singlemp3player_text','#800080'),
('singlemp3player_slider','#c0c0c0'),
('singlemp3player_track','#FFFFFF'),
('singlemp3player_border','#FFFFFF'),
('singlemp3player_loader','#8EC2F4'),
('singlemp3player_auto','yes'),
('singlemp3player_loop','yes'),
('mp3player_showdisplay','yes'),
('mp3player_showplaylist','yes'),
('mp3player_autostart','yes'),
('widget_mp3player_width','200'),
('widget_mp3player_name','畅爽音乐'),
('widget_mp3player_height','250'),
('widget_mp3player_bgcolor','#ffffff'),
('widget_mp3player_titleshow','1'),
('mp3player_data_num','0'),
('mp3player_data_max_size','0'),
('mp3player_data_min_size','0'),
('mp3player_data_category','0'),
('mp3player_data_id_ascdesc','desc'),
('mp3player_data_comment_ascdesc','desc'),
('website_licence','欢迎来到我们网站，这是我们的协议，请不要发布违法信息。'),
('the_register_description','我是注册描述，你可以在后台修改它'),
('audit_register','0'),
('the_publish_description','我是用户投稿描述，你可以在后台进行编辑。'),
('website_publish_licence','投稿许可协议，请遵循我们的投稿服务条款！'),
('display_blog_user_list_num','15'),
('the_user_description','这是用户列表描述。'),
('display_blog_trackback_list_num','15'),
('the_trackback_description','这里是引用描述...'),
('display_comments_list_num','15'),
('the_comment_description','评论描述......'),
('display_blog_pm_list_num','15'),
('message_sound_on','1'),
('message_sound_type','2'),
('message_sound_out_url',''),
('admin_theme_file_every','10'),
('admin_searchindex_file_everynum','20'),
('javascript_dir','cache'),
('jquery_lazyload','0'),
('jquery_slimbox','0'),
('blog_style_show','1'),
('seo_title','PHP 开源博客平台提供商'),
('global_blog_cache_expiration','86400'),
('global_blog_keyword_link','1'),
('url_model','0'),
('blog_lang_set','Zh-cn'),
('admin_lang_set','Zh-cn'),
('is_upload_auto','1'),
('sim_upload_limit','5'),
('cms_only_parentcategory','1'),
('allowed_register','0'),
('allowed_publish','0'),
('close_blog','0'),
('close_blog_why','update...'),
('image_max_width','600'),
('allowed_default_edit','0'),
('close_wap','0'),
('wap_display_blog_list_num','5'),
('wap_blog_cutnum','20'),
('wap_display_category_list_num','10'),
('wap_display_comment_list_num','5'),
('wap_display_tag_list_num','5'),
('wap_display_taotao_list_num','5'),
('wap_display_upload_list_num','5'),
('audit_trackback','0'),
('max_trackback_length','1000'),
('comment_max_len','400'),
('publishseccode','1'),
('registerseccode','1'),
('avatar_cache','1'),
('avatar_cache_time','7'),
('html_cache_on','0'),
('html_cache_time','700'),
('html_read_type','0'),
('comment_min_len','5'),
('comment_post_space','20'),
('start_gzip','1'),
('comment_banip_enable','1'),
('comment_ban_ip',''),
('comment_spam_enable','1'),
('comment_spam_words','虚拟主机,域名注册,出租网,六合彩,铃声下载,手机铃声,和弦铃声,手机游戏,免费铃声,彩铃,网站建设,操你妈,rinima,日你妈,鸡,操,鸡吧,小姐,fuck,胡锦涛,温家宝,胡温,李洪志,法轮,民运,反共,专制,专政,独裁,极权,中共,共产,共党,六四,民主,人权,毛泽东,中国政府,中央政府,游行示威,天安门,达赖,他妈的,我操,强奸,法轮'),
('comment_spam_url_num','3'),
('comment_spam_content_size','2000'),
('disallowed_all_english_word','1'),
('disallowed_spam_word_to_database','1'),
('widget_flashimage_display_num', '10'),
('widget_flashimage_autoplaytime', '3'),
('attach_expire_hour', ''),
('footer_extend_message', '');

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_role`
--

INSERT INTO `#@__role` (`role_id`,`role_name`,`role_parentid`,`role_status`,`role_remark`,`role_ename`,`create_dateline`,`update_dateline`) VALUES
(1,'领导组',0,1,'我是领导组',NULL,1295530584,1307529261),
(2,'员工组',0,1,'我是员工组',NULL,1295530598,1307529240),
(3,'普通用户',0,1,'我是普通用户组',NULL,1295581016,1307529214);

-- --------------------------------------------------------

--
-- 转存表中的数据 `dyhbxblog_user`
--

INSERT INTO `#@__user` (`user_id`,`user_lastpost`,`user_birthday`,`user_homepage`,`user_school`,`user_primaryschool`,`user_juniorhighschool`,`user_highschool`,`user_university`,`user_hometown`,`user_nowplace`,`user_name`,`user_nikename`,`user_password`,`user_registerip`,`user_lastlogintime`,`user_lastloginip`,`user_logincount`,`user_email`,`user_remark`,`create_dateline`,`update_dateline`,`user_status`,`user_random`,`user_sex`,`user_age`,`user_work`,`user_marry`,`user_love`,`user_qq`,`user_alipay`,`user_aliwangwang`,`user_yahoo`,`user_msn`,`user_google`,`user_skype`,`user_renren`,`user_doyouhaobaby`,`user_twritercom`,`user_weibocom`,`user_tqqcom`,`user_diandian`,`user_blogs`,`user_comments`) VALUES
(1,0,'0000-00-00','',4,'','','','','四川','成都','admin','D先生','3431cdc51b67a7749d2951e2d6715878','127.0.0.1',1319145823,'127.0.0.1',0,'','',1304067406,1319145823,1,'38F222',1,21,'学生',1,'游泳','','','doyouhaobaby','','','','','','','','','','',1,0);

