-- DYHB.BLOG X 数据库表
-- version 2.0.1
-- http://www.doyouhaobaby.net
--
-- 开发: 点牛科技（成都）
-- 网站: http://dianniu.net

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- DYHB.BLOG X数据库
--

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_access`
--

DROP TABLE IF EXISTS `#@__access`;
CREATE TABLE `#@__access` (
	`role_id` smallint(6) unsigned NOT NULL COMMENT '角色ID',
	`node_id` smallint(6) unsigned NOT NULL COMMENT '节点ID',
	`access_level` tinyint(1) NOT NULL COMMENT '级别，1（应用），2（模块），3（方法）',
	`access_parentid` smallint(6) NOT NULL COMMENT '父级ID',
	`access_module` varchar(50) default NULL COMMENT 'moudel留待以后扩展',
	`access_status` tinyint(1) unsigned NOT NULL default '1' COMMENT '状态',
	KEY `group_id` (`role_id`),
	KEY `node_id` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_badword`
--

DROP TABLE IF EXISTS `#@__badword`;
CREATE TABLE `#@__badword` (
	`badword_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '词语替换ID',
	`badword_admin` varchar(50) NOT NULL default '' COMMENT '添加词语过滤用户',
	`badword_find` varchar(300) NOT NULL default '' COMMENT '待查找的过滤词语',
	`badword_replacement` varchar(300) NOT NULL default '' COMMENT '待替换的过滤词语',
	`badword_findpattern` varchar(300) NOT NULL default '' COMMENT '查找的正则表达式',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`badword_id`),
	UNIQUE KEY `find` (`badword_find`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_blog`
--

DROP TABLE IF EXISTS `#@__blog`;
CREATE TABLE `#@__blog` (
	`blog_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '日志ID',
	`blog_title` varchar(300) character set ucs2 NOT NULL default '' COMMENT '标题',
	`blog_dateline` int(10) NOT NULL COMMENT '发布时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`blog_content` longtext NOT NULL COMMENT '正文',
	`blog_from` varchar(25) NOT NULL COMMENT '来源',
	`blog_fromurl` varchar(300) NOT NULL COMMENT '来源URL',
	`blog_urlname` varchar(300) NOT NULL COMMENT '别名，用于URL优化',
	`user_id` mediumint(8) NOT NULL default '-1' COMMENT '用户ID',
	`category_id` mediumint(8) NOT NULL default '-1' COMMENT '分类ID',
	`blog_thumb` varchar(300) NOT NULL COMMENT '缩略图，可以为一个完整的图片URL，或者系统的图片附件ID',
	`blog_viewnum` mediumint(8) unsigned NOT NULL default '1' COMMENT '点击量',
	`blog_uploadnum` mediumint(8) NOT NULL default '0' COMMENT '附件数量',
	`blog_password` varchar(50) NOT NULL COMMENT '密码',
	`blog_istop` tinyint(1) NOT NULL default '0' COMMENT '是否置顶',
	`blog_isshow` tinyint(1) NOT NULL default '1' COMMENT '是否显示',
	`blog_islock` tinyint(1) NOT NULL default '0' COMMENT '是否锁定',
	`blog_ispage` tinyint(1) NOT NULL default '0' COMMENT '是否为页面',
	`blog_trackbacknum` mediumint(8) NOT NULL default '0' COMMENT '引用数量',
	`blog_allowedtrackback` tinyint(1) NOT NULL default '1' COMMENT '是否允许引用',
	`blog_commentnum` mediumint(8) NOT NULL default '0' COMMENT '评论数量',
	`blog_good` int(8) NOT NULL default '0' COMMENT '好评数量',
	`blog_bad` int(8) NOT NULL default '0' COMMENT '差评数量',
	`blog_ismobile` tinyint(1) NOT NULL default '0' COMMENT '是否为手机发布',
	`blog_keyword` varchar(300) NOT NULL COMMENT 'SEO关键字',
	`blog_description` varchar(300) NOT NULL COMMENT 'SEO描述',
	`blog_excerpt` text NOT NULL COMMENT '摘要',
	`blog_gotourl` varchar(300) NOT NULL COMMENT '外部URL',
	`blog_isblank` tinyint(1) NOT NULL default '0' COMMENT '是否新窗口打开',
	`blog_ip` varchar(40) NOT NULL COMMENT '发表日志IP',
	`blog_lastpost` text NOT NULL COMMENT '主题最后帖子发布时间',
	`blog_color` char(7) NOT NULL COMMENT '标题颜色',
	`blog_type` set('c','h','p','f','s','a','b') NOT NULL COMMENT '文章类型：头条[h]推荐[c]幻灯[f]特荐[a]滚动[s]加粗[b]图片[p]',
	PRIMARY KEY	(`blog_id`),
	KEY `user_id` (`user_id`),
	KEY `dateline` (`blog_dateline`),
	KEY `blog_istop` (`blog_istop`,`blog_isshow`,`blog_islock`,`blog_ispage`),
	KEY `blog_type` (`blog_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_category`
--

DROP TABLE IF EXISTS `#@__category`;
CREATE TABLE `#@__category` (
	`category_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '日志分类ID',
	`category_name` varchar(50) NOT NULL COMMENT '分类名字',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`category_urlname` varchar(50) NOT NULL COMMENT '分类别名',
	`category_compositor` int(8) NOT NULL default '0' COMMENT '分类排序',
	`category_logo` varchar(300) NOT NULL COMMENT '分类小Logo，又名ICON',
	`category_parentid` smallint(8) NOT NULL default '0' COMMENT '分类父级ID',
	`category_keyword` varchar(300) NOT NULL COMMENT '分类SEO关键字',
	`category_description` varchar(300) NOT NULL COMMENT '分类SEO描述',
	`category_introduce` varchar(300) NOT NULL COMMENT '分类介绍',
	`category_extra` text NOT NULL COMMENT '分类扩展设置',
	`category_blogs` mediumint(8) NOT NULL COMMENT '日志数量',
	`category_comments` mediumint(8) NOT NULL COMMENT '评论数量和文章数量',
	`category_todaycomments` mediumint(8) NOT NULL COMMENT '今日评论和主题发布数量',
	`category_gotourl` varchar(300) NOT NULL COMMENT '外部衔接',
	`category_color` char(7) NOT NULL COMMENT '板块颜色',
	`category_rule` text NOT NULL COMMENT '板块规则',
	`category_showsub` tinyint(1) NOT NULL default '0' COMMENT '是否显示下级板块',
	`category_lastpost` text NOT NULL COMMENT '最后发表的主题',
	`category_columns` int(2) NOT NULL default '0' COMMENT '版块子版块横向排放的数量',
	`category_template` varchar(20) NOT NULL COMMENT '分类主题',
	PRIMARY KEY	(`category_id`),
	KEY `category_parentsortid` (`category_parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbblogx_pluginvar`
--

DROP TABLE IF EXISTS `#@__pluginvar`;
CREATE TABLE `#@__pluginvar` (
  `pluginvar_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '变量ID',
  `plugin_id` smallint(6) unsigned NOT NULL default '0' COMMENT '插件ID',
  `pluginvar_displayorder` tinyint(3) NOT NULL default '0' COMMENT '变量显示顺序',
  `pluginvar_title` varchar(100) NOT NULL default '' COMMENT '变量的标题',
  `pluginvar_description` varchar(255) NOT NULL default '' COMMENT '配置变量的描述',
  `pluginvar_variable` varchar(40) NOT NULL default '' COMMENT '配置变量名',
  `pluginvar_value` text NOT NULL COMMENT '配置变量的值',
  `pluginvar_type` varchar(20) NOT NULL COMMENT '变量配置类型',
  `pluginvar_extra` text NOT NULL COMMENT '变量配置扩展，用于select',
  PRIMARY KEY  (`pluginvar_id`),
  KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_comment`
--

DROP TABLE IF EXISTS `#@__comment`;
CREATE TABLE `#@__comment` (
	`comment_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '评论ID',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`user_id` mediumint(8) NOT NULL default '0' COMMENT '用户ID，在线用户评论',
	`category_id` mediumint(8) NOT NULL default '-1' COMMENT '版块（分类）ID',
	`comment_name` varchar(25) NOT NULL COMMENT '名字',
	`comment_content` text NOT NULL COMMENT '内容',
	`comment_email` varchar(300) NOT NULL COMMENT '邮件',
	`comment_url` varchar(300) NOT NULL COMMENT 'URL',
	`comment_isshow` tinyint(1) NOT NULL default '1' COMMENT '是否显示',
	`comment_ip` varchar(16) NOT NULL COMMENT 'IP',
	`comment_parentid` mediumint(8) NOT NULL default '0' COMMENT '父级ID',
	`comment_isreplymail` tinyint(1) NOT NULL default '0' COMMENT '是否邮件通知，通知给评论者',
	`comment_ismobile` tinyint(1) NOT NULL default '0' COMMENT '是否为手机评论',
	`comment_relationtype` char(10) NOT NULL COMMENT '关联类型，如taotao对应心情评论，blog对应日志评论等等',
	`comment_relationvalue` mediumint(8) NOT NULL COMMENT '关联类型的值，如日志ID，心情ID等',
	PRIMARY KEY	(`comment_id`),
	KEY `comment_isshow` (`comment_isshow`),
	KEY `comment_relationtype` (`comment_relationtype`,`comment_relationvalue`),
	KEY `user_id` (`user_id`),
	KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_countcache`
--

DROP TABLE IF EXISTS `#@__countcache`;
CREATE TABLE `#@__countcache` (
	`countcache_id` int(10) unsigned NOT NULL auto_increment COMMENT '缓存ID',
	`countcache_usersnum` int(10) unsigned NOT NULL COMMENT '用户数量',
	`countcache_topicsnum` int(10) unsigned NOT NULL COMMENT '主题数量',
	`countcache_postsnum` int(10) unsigned NOT NULL COMMENT '评论数量',
	`countcache_todaynum` int(10) unsigned NOT NULL COMMENT '今日发帖量',
	`countcache_yesterdaynum` int(10) unsigned NOT NULL COMMENT '昨日发帖量',
	`countcache_mostnum` int(10) unsigned NOT NULL COMMENT '最高日发帖量',
	`countcache_lastuser` varchar(45) NOT NULL COMMENT '最新会员',
	`countcache_todaydate` date NOT NULL COMMENT '今日时间',
	PRIMARY KEY	(`countcache_id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_dataindex`
--

DROP TABLE IF EXISTS `#@__dataindex`;
CREATE TABLE `#@__dataindex` (
	`dataindex_id` int(11) NOT NULL auto_increment COMMENT '数据调用索引ID',
	`dataindex_md5hash` char(32) NOT NULL COMMENT 'hash值',
	`create_dateline` int(10) NOT NULL default '0' COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`dataindex_datastring` text NOT NULL COMMENT '配置字符串',
	`dataindex_totals` smallint(6) NOT NULL default '0' COMMENT '总结果数',
	`dataindex_ids` text NOT NULL COMMENT '数据索引ID值',
	`dataindex_auto` tinyint(1) NOT NULL default '1' COMMENT '是否为自动创建，自动创建为arclist widget创建',
	`dataindex_template` text NOT NULL COMMENT '数据调用模板',
	`dataindex_expiration` int(10) NOT NULL default '0' COMMENT '0表示一直过期',
	`dataindex_conditionstring` text NOT NULL COMMENT '条件配置',
	PRIMARY KEY	(`dataindex_id`),
	KEY `dateline` (`create_dateline`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_domain`
--

DROP TABLE IF EXISTS `#@__domain`;
CREATE TABLE `#@__domain` (
	`domain_id` int(10) unsigned NOT NULL auto_increment COMMENT '域名ID',
	`domain_name` char(40) NOT NULL default '' COMMENT '域名如www.doyouhaobaby.net',
	`domain_ip` char(15) NOT NULL default '' COMMENT 'IP如127.0.0.1',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`domain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_emot`
--

DROP TABLE IF EXISTS `#@__emot`;
CREATE TABLE `#@__emot` (
	`emot_id` mediumint(8) NOT NULL auto_increment COMMENT '表情ID',
	`emot_name` varchar(25) character set utf8 NOT NULL COMMENT '表情代号',
	`emot_admin` varchar(25) character set utf8 NOT NULL COMMENT '操作人',
	`user_id` mediumint(8) NOT NULL default '-1' COMMENT '用户ID',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`emot_image` varchar(100) character set utf8 NOT NULL COMMENT '表情路径',
	`emot_thumb` varchar(150) character set utf8 NOT NULL COMMENT '表情缩略图',
	`empt_compositor` int(8) NOT NULL default '0' COMMENT '表情排序',
	PRIMARY KEY	(`emot_id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_friend`
--

DROP TABLE IF EXISTS `#@__friend`;
CREATE TABLE `#@__friend` (
	`user_id` mediumint(8) NOT NULL COMMENT '用户ID',
	`friend_friendid` mediumint(8) unsigned NOT NULL default '0' COMMENT '好友ID',
	`friend_direction` tinyint(1) NOT NULL default '1' COMMENT '关系，1（A加B），2（B加A），3（A与B彼此相加）',
	`friend_delstatus` tinyint(1) NOT NULL default '0' COMMENT '状态',
	`friend_comment` char(255) NOT NULL default '' COMMENT '备注',
	`create_dateline` int(10) NOT NULL COMMENT '添加时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	KEY `user_id` (`user_id`),
	KEY `friend_friendid` (`friend_friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_group`
--

DROP TABLE IF EXISTS `#@__group`;
CREATE TABLE `#@__group` (
	`group_id` smallint(3) unsigned NOT NULL auto_increment COMMENT '角色分组ID',
	`group_name` varchar(25) NOT NULL COMMENT '名字，英文',
	`group_title` varchar(50) NOT NULL COMMENT '别名，中文等注解',
	`create_dateline` int(11) unsigned NOT NULL COMMENT '创建时间',
	`update_dateline` int(11) unsigned NOT NULL default '0' COMMENT '更新时间',
	`group_status` tinyint(1) unsigned NOT NULL default '0' COMMENT '状态',
	`group_sort` smallint(3) unsigned NOT NULL default '0' COMMENT '排序',
	`group_show` tinyint(1) unsigned NOT NULL default '0' COMMENT '是否显示',
	PRIMARY KEY	(`group_id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_link`
--

DROP TABLE IF EXISTS `#@__link`;
CREATE TABLE `#@__link` (
	`link_id` mediumint(8) unsigned NOT NULL auto_increment COMMENT '衔接ID',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`link_name` varchar(30) NOT NULL COMMENT '名字',
	`link_url` varchar(250) NOT NULL COMMENT 'URL',
	`link_description` varchar(300) NOT NULL COMMENT '描述',
	`link_logo` varchar(360) NOT NULL default '0' COMMENT 'LOGO',
	`link_isdisplay` tinyint(1) NOT NULL default '1' COMMENT '是否显示',
	`link_compositor` smallint(8) NOT NULL COMMENT '排序',
	PRIMARY KEY	(`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_loginlog`
--

DROP TABLE IF EXISTS `#@__loginlog`;
CREATE TABLE `#@__loginlog` (
	`loginlog_id` mediumint(8) NOT NULL auto_increment COMMENT '登录ID',
	`user_id` mediumint(8) NOT NULL COMMENT '用户ID',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`loginlog_user` varchar(50) NOT NULL COMMENT '登录用户',
	`loginlog_ip` varchar(40) NOT NULL COMMENT '登录IP',
	`loginlog_status` tinyint(1) NOT NULL default '0' COMMENT '登录状态',
	`login_application` varchar(20) NOT NULL COMMENT '登录应用',
	PRIMARY KEY	(`loginlog_id`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_mail`
--

DROP TABLE IF EXISTS `#@__mail`;
CREATE TABLE `#@__mail` (
	`mail_id` int(10) unsigned NOT NULL auto_increment COMMENT '邮件ID',
	`mail_touserid` mediumint(8) unsigned NOT NULL default '0' COMMENT '接受用户ID',
	`mail_fromuserid` mediumint(8) NOT NULL default '0' COMMENT '发送用户ID',
	`mail_tomail` varchar(100) NOT NULL COMMENT '接收者邮件地址',
	`mail_frommail` varchar(100) NOT NULL COMMENT '发送者邮件地址',
	`mail_subject` varchar(300) NOT NULL COMMENT '主题',
	`mail_message` text NOT NULL COMMENT '内容',
	`mail_charset` varchar(15) NOT NULL COMMENT '编码',
	`mail_htmlon` tinyint(1) NOT NULL default '0' COMMENT '是否开启html',
	`mail_level` tinyint(1) NOT NULL default '1' COMMENT '紧急级别',
	`create_dateline` int(10) unsigned NOT NULL default '0' COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`mail_isfailure` tinyint(1) unsigned NOT NULL default '0' COMMENT '状态，是否成功',
	`mail_application` varchar(20) NOT NULL COMMENT '来源应用',
	PRIMARY KEY	(`mail_id`),
	KEY `level` (`mail_level`,`mail_isfailure`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_node`
--

DROP TABLE IF EXISTS `#@__node`;
CREATE TABLE `#@__node` (
	`node_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '节点ID',
	`node_name` varchar(50) NOT NULL COMMENT '名字',
	`node_title` varchar(50) default NULL COMMENT '别名',
	`node_status` tinyint(1) default '0' COMMENT '状态',
	`node_remark` varchar(300) default NULL COMMENT '备注',
	`node_sort` smallint(6) unsigned default NULL COMMENT '排序',
	`node_parentid` smallint(6) unsigned NOT NULL default '0' COMMENT '父级ID',
	`node_level` tinyint(1) unsigned NOT NULL default '1' COMMENT '级别，1（应用），2（模块），3（方法）',
	`node_type` tinyint(1) NOT NULL default '0' COMMENT '本字段目前处于废弃状态',
	`group_id` tinyint(3) unsigned default '0' COMMENT '分组ID',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`node_id`),
	KEY `node_level` (`node_level`),
	KEY `node_name` (`node_name`),
	KEY `node_status` (`node_status`),
	KEY `node_parentid` (`node_parentid`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_option`
--

DROP TABLE IF EXISTS `#@__option`;
CREATE TABLE `#@__option` (
	`option_name` varchar(32) NOT NULL default '' COMMENT '名字',
	`option_value` text NOT NULL COMMENT '值',
	PRIMARY KEY	(`option_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 表的结构 `dyhbxblog_plugin`
--

DROP TABLE IF EXISTS `#@__plugin`;
CREATE TABLE `#@__plugin` (
  `plugin_id` mediumint(8) NOT NULL auto_increment COMMENT '插件ID',
  `create_dateline` int(10) NOT NULL COMMENT '创建时间',
  `update_dateline` int(10) NOT NULL COMMENT '更新时间',
  `plugin_active` tinyint(1) NOT NULL default '0' COMMENT '是否激活',
  `plugin_name` varchar(50) NOT NULL COMMENT '名字',
  `plugin_identifier` varchar(40) NOT NULL COMMENT '插件唯一识别',
  `plugin_author` varchar(30) NOT NULL COMMENT '作者',
  `plugin_authorurl` varchar(300) NOT NULL COMMENT '作者URL地址',
  `plugin_description` tinytext NOT NULL COMMENT '描述',
  `plugin_dir` varchar(50) NOT NULL COMMENT '目录',
  `plugin_module` text NOT NULL COMMENT '模块',
  `plugin_copyright` varchar(100) NOT NULL COMMENT '版权信息',
  `plugin_version` varchar(20) NOT NULL COMMENT '版本',
  PRIMARY KEY  (`plugin_id`),
  KEY `plugin_identifier` (`plugin_identifier`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_pm`
--

DROP TABLE IF EXISTS `#@__pm`;
CREATE TABLE `#@__pm` (
	`pm_id` int(10) unsigned NOT NULL auto_increment COMMENT '短消息ID',
	`pm_msgfrom` varchar(50) NOT NULL default '' COMMENT '来源',
	`pm_msgfromid` mediumint(8) unsigned NOT NULL default '0' COMMENT '来源用户ID',
	`pm_msgtoid` mediumint(8) unsigned NOT NULL default '0' COMMENT '接收ID',
	`pm_isread` tinyint(1) NOT NULL default '0' COMMENT '是否已经阅读',
	`pm_subject` varchar(75) NOT NULL default '' COMMENT '主题',
	`create_dateline` int(10) unsigned NOT NULL default '0' COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`pm_message` text NOT NULL COMMENT '内容',
	`pm_delstatus` tinyint(1) unsigned NOT NULL default '0' COMMENT '删除状态',
	`pm_fromapp` varchar(30) NOT NULL COMMENT '来源应用',
	`pm_type` enum('system','user') NOT NULL default 'user' COMMENT '类型',
	PRIMARY KEY	(`pm_id`),
	KEY `msgtoid` (`pm_msgtoid`,`create_dateline`),
	KEY `msgfromid` (`pm_msgfromid`,`create_dateline`),
	KEY `getnum` (`pm_msgtoid`,`pm_delstatus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_role`
--

DROP TABLE IF EXISTS `#@__role`;
CREATE TABLE `#@__role` (
	`role_id` smallint(6) unsigned NOT NULL auto_increment COMMENT '角色ID',
	`role_name` varchar(25) NOT NULL COMMENT '名字',
	`role_parentid` smallint(6) default NULL COMMENT '父级ID',
	`role_status` tinyint(1) unsigned default NULL COMMENT '状态',
	`role_remark` varchar(300) default NULL COMMENT '备注',
	`role_ename` varchar(5) default NULL COMMENT '此字段目前处于废弃状态',
	`create_dateline` int(11) unsigned NOT NULL COMMENT '创建时间',
	`update_dateline` int(11) unsigned NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`role_id`),
	KEY `role_parentid` (`role_parentid`),
	KEY `role_status` (`role_status`),
	KEY `role_ename` (`role_ename`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_searchindex`
--

DROP TABLE IF EXISTS `#@__searchindex`;
CREATE TABLE `#@__searchindex` (
	`searchindex_id` int(11) NOT NULL auto_increment COMMENT '搜索索引ID',
	`searchindex_keywords` varchar(255) NOT NULL default '' COMMENT '关键字',
	`create_dateline` int(10) NOT NULL default '0' COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`searchindex_expiration` int(10) NOT NULL COMMENT '过期时间',
	`searchindex_searchstring` text NOT NULL COMMENT '配置字符串',
	`searchindex_totals` smallint(6) NOT NULL default '0' COMMENT '总结果数',
	`searchindex_ids` text NOT NULL COMMENT '数据索引ID值',
	`searchindex_searchfrom` enum('blog','comment') NOT NULL default 'blog' COMMENT '搜索类型，评论或者日志，comment,blog',
	`searchindex_ip` varchar(16) NOT NULL default '' COMMENT 'IP',
	`user_id` mediumint(8) NOT NULL default '0' COMMENT '用户',
	PRIMARY KEY	(`searchindex_id`),
	KEY `dateline` (`create_dateline`),
	KEY `searchfrom` (`searchindex_searchfrom`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_session`
--

DROP TABLE IF EXISTS `#@__session`;
CREATE TABLE `#@__session` (
	`session_hash` varchar(6) NOT NULL COMMENT 'HASH',
	`session_auth_key` varchar(32) NOT NULL COMMENT 'AUTH_KEY',
	`user_id` mediumint(8) NOT NULL COMMENT '用户ID',
	`session_seccode` varchar(6) NOT NULL COMMENT '验证码'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_systempm`
--

DROP TABLE IF EXISTS `#@__systempm`;
CREATE TABLE `#@__systempm` (
	`user_id` mediumint(8) NOT NULL default '0' COMMENT '用户ID',
	`systempm_readids` text character set utf8 NOT NULL COMMENT '用户阅读过的系统短消息ID，用来记录用户阅读系统短消息的情况'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_tag`
--

DROP TABLE IF EXISTS `#@__tag`;
CREATE TABLE `#@__tag` (
	`tag_id` mediumint(8) NOT NULL auto_increment COMMENT '标签ID',
	`tag_name` varchar(60) NOT NULL COMMENT '名字',
	`tag_urlname` varchar(50) NOT NULL COMMENT '别名',
	`tag_usenum` mediumint(8) NOT NULL default '0' COMMENT '使用数量',
	`blog_id` text NOT NULL COMMENT '日志ID',
	`tag_keyword` varchar(300) NOT NULL COMMENT '关键字',
	`tag_description` varchar(300) NOT NULL COMMENT '描述',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_taotao`
--

DROP TABLE IF EXISTS `#@__taotao`;
CREATE TABLE `#@__taotao` (
	`taotao_id` mediumint(8) NOT NULL auto_increment COMMENT '滔滔心情ID',
	`taotao_content` varchar(400) NOT NULL COMMENT '内容',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`user_id` mediumint(8) NOT NULL default '-1' COMMENT '用户ID',
	`taotao_ismobile` tinyint(1) NOT NULL default '0' COMMENT '是否为手机心情',
	`taotao_commentnum` mediumint(8) NOT NULL default '0' COMMENT '评论数量',
	`taotao_islock` tinyint(1) NOT NULL default '0' COMMENT '是否锁定',
	PRIMARY KEY	(`taotao_id`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_trackback`
--

DROP TABLE IF EXISTS `#@__trackback`;
CREATE TABLE `#@__trackback` (
	`trackback_id` mediumint(8) NOT NULL auto_increment COMMENT '引用ID',
	`blog_id` mediumint(8) NOT NULL COMMENT '日志ID',
	`trackback_title` varchar(300) NOT NULL COMMENT '标题',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`trackback_excerpt` text NOT NULL COMMENT '内容',
	`trackback_url` varchar(300) NOT NULL COMMENT '文章地址',
	`trackback_blogname` varchar(300) NOT NULL COMMENT '博客名字',
	`trackback_ip` varchar(16) NOT NULL COMMENT 'IP',
	`trackback_status` tinyint(1) NOT NULL default '1' COMMENT 'trackback是否显示',
	`trackback_points` int(2) NOT NULL default '0' COMMENT '引用得分',
	PRIMARY KEY	(`trackback_id`),
	KEY `blog_id` (`blog_id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_upload`
--

DROP TABLE IF EXISTS `#@__upload`;
CREATE TABLE `#@__upload` (
	`upload_id` int(10) NOT NULL auto_increment COMMENT '附件ID',
	`upload_name` varchar(300) NOT NULL COMMENT '名字',
	`upload_type` varchar(40) NOT NULL COMMENT '类型',
	`upload_size` int(8) NOT NULL COMMENT '大小，单位KB',
	`upload_key` varchar(25) NOT NULL COMMENT '上传KEY',
	`upload_extension` varchar(20) NOT NULL COMMENT '后缀',
	`upload_savepath` varchar(300) NOT NULL COMMENT '保存路径',
	`upload_savename` varchar(300) NOT NULL COMMENT '保存名字',
	`upload_hash` varchar(300) NOT NULL COMMENT 'HASH',
	`upload_module` varchar(50) NOT NULL COMMENT '上传模块',
	`upload_record` int(10) NOT NULL default '-1' COMMENT '上传模块记录',
	`upload_isthumb` tinyint(1) NOT NULL default '0' COMMENT '是否存在缩略图',
	`upload_thumbprefix` varchar(25) NOT NULL COMMENT '缩略图前缀',
	`upload_thumbpath` varchar(300) NOT NULL COMMENT '缩略图路径',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	`uploadcategory_id` mediumint(8) NOT NULL COMMENT '分类ID',
	`upload_description` varchar(300) NOT NULL COMMENT '描述',
	`upload_download` int(10) NOT NULL COMMENT '下载次数',
	`upload_commentnum` mediumint(8) NOT NULL default '0' COMMENT '评论数量',
	`upload_islock` tinyint(1) NOT NULL default '0' COMMENT '是否锁定',
	`user_id` mediumint(8) NOT NULL default '-1' COMMENT '用户ID',
	PRIMARY KEY	(`upload_id`),
	KEY `upload_module` (`upload_module`),
	KEY `upload_record` (`upload_record`),
	KEY `uploadcategory_id` (`uploadcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_uploadcategory`
--

DROP TABLE IF EXISTS `#@__uploadcategory`;
CREATE TABLE `#@__uploadcategory` (
	`uploadcategory_id` mediumint(8) NOT NULL auto_increment COMMENT '附件分类ID',
	`uploadcategory_name` varchar(50) NOT NULL COMMENT '分类名字',
	`uploadcategory_cover` varchar(300) NOT NULL COMMENT '分类封面，可以为一个文章的图片地址或者附件库中一个图片附件的ID',
	`uploadcategory_compositor` smallint(8) NOT NULL default '0' COMMENT '排序',
	`create_dateline` int(10) NOT NULL COMMENT '创建时间',
	`update_dateline` int(10) NOT NULL COMMENT '更新时间',
	PRIMARY KEY	(`uploadcategory_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_user`
--

DROP TABLE IF EXISTS `#@__user`;
CREATE TABLE `#@__user` (
	`user_id` int(10) NOT NULL auto_increment COMMENT '用户ID',
	`user_lastpost` int(10) NOT NULL default '0' COMMENT '最后发布时间',
	`user_birthday` date NOT NULL default '0000-00-00' COMMENT '生日',
	`user_homepage` varchar(300) NOT NULL COMMENT '主页',
	`user_school` tinyint(1) NOT NULL default '4' COMMENT '当前求学状态',
	`user_primaryschool` varchar(50) NOT NULL COMMENT '小学',
	`user_juniorhighschool` varchar(50) NOT NULL COMMENT '初中',
	`user_highschool` varchar(50) NOT NULL COMMENT '高中',
	`user_university` varchar(50) NOT NULL COMMENT '大学',
	`user_hometown` varchar(50) NOT NULL COMMENT '家乡',
	`user_nowplace` varchar(50) NOT NULL COMMENT '现居地',
	`user_name` varchar(50) character set ucs2 NOT NULL COMMENT '用户名',
	`user_nikename` varchar(50) default NULL COMMENT '昵称',
	`user_password` char(32) NOT NULL COMMENT '密码',
	`user_registerip` varchar(40) NOT NULL COMMENT '注册IP地址',
	`user_lastlogintime` int(11) default NULL COMMENT '最后登录时间',
	`user_lastloginip` varchar(40) default NULL COMMENT '最后登录IP',
	`user_logincount` mediumint(8) default NULL COMMENT '登录次数',
	`user_email` varchar(150) default NULL COMMENT '邮件',
	`user_remark` varchar(300) default NULL COMMENT '介绍或者叫做备注',
	`create_dateline` int(11) default NULL COMMENT '创建时间',
	`update_dateline` int(11) default NULL COMMENT '更新时间',
	`user_status` tinyint(1) default '0' COMMENT '状态',
	`user_random` char(6) NOT NULL COMMENT '随机码，用于密码验证',
	`user_sex` tinyint(1) NOT NULL COMMENT '性别',
	`user_age` tinyint(3) NOT NULL COMMENT '年龄',
	`user_work` varchar(50) NOT NULL COMMENT '职业',
	`user_marry` tinyint(1) NOT NULL COMMENT '婚姻状况',
	`user_love` varchar(500) NOT NULL COMMENT '爱好',
	`user_qq` char(20) NOT NULL COMMENT 'QQ',
	`user_alipay` varchar(300) NOT NULL COMMENT '支付宝',
	`user_aliwangwang` varchar(50) NOT NULL COMMENT '阿里旺旺',
	`user_yahoo` varchar(300) NOT NULL COMMENT 'Yahoo帐号',
	`user_msn` varchar(300) NOT NULL COMMENT 'MSN',
	`user_google` varchar(300) NOT NULL COMMENT 'Google',
	`user_skype` varchar(300) NOT NULL COMMENT 'Skype',
	`user_renren` varchar(300) NOT NULL COMMENT '人人',
	`user_doyouhaobaby` varchar(300) NOT NULL COMMENT 'DoYouHaoBaby',
	`user_twritercom` varchar(300) NOT NULL COMMENT 'Twriter',
	`user_weibocom` varchar(300) NOT NULL COMMENT '新浪微博',
	`user_tqqcom` varchar(300) NOT NULL COMMENT '腾迅微博',
	`user_diandian` varchar(300) NOT NULL COMMENT '点点',
	`user_blogs` mediumint(8) NOT NULL default '0' COMMENT '用户日志数量',
	`user_comments` mediumint(8) NOT NULL default '0' COMMENT '用户评论数量',
	PRIMARY KEY	(`user_id`)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dyhbxblog_userrole`
--

DROP TABLE IF EXISTS `#@__userrole`;
CREATE TABLE `#@__userrole` (
	`role_id` mediumint(9) unsigned default NULL COMMENT '角色ID',
	`user_id` char(32) default NULL COMMENT '用户ID',
	KEY `group_id` (`role_id`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
