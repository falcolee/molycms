# 广告表
DROP TABLE IF EXISTS pre_cms_ad;
CREATE TABLE pre_cms_ad (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `adname` varchar(60) NOT NULL DEFAULT '' COMMENT '广告名称',
  `timeset` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启时间限制',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `normbody` text COMMENT '广告内容',
  `expbody` varchar(255) NOT NULL DEFAULT '' COMMENT '广告过期显示内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告表' COLLATE=utf8_general_ci;

# 附件表
DROP TABLE IF EXISTS pre_cms_attach;
CREATE TABLE pre_cms_attach (
  aid int(10) unsigned NOT NULL AUTO_INCREMENT,
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  mid tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '模型ID(默认文章模型)', 
  filename char(80) NOT NULL DEFAULT '' COMMENT '文件原名',
  filetype char(10) NOT NULL DEFAULT '' COMMENT '后缀',
  filesize int(10) unsigned NOT NULL DEFAULT '0' COMMENT '大小', 
  filepath char(150) NOT NULL DEFAULT '' COMMENT '路径',
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  downloads int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数', 
  isimage tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是图片 (1为图片，0为文件)',
  PRIMARY KEY (aid),
  KEY id (id, aid,mid),
  KEY uid (uid, aid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='附件表' COLLATE=utf8_general_ci;

# 碎片表
DROP TABLE IF EXISTS pre_cms_block;
CREATE TABLE pre_cms_block (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '碎片标题',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '更多链接',
  `content` mediumtext NOT NULL COMMENT '碎片内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='碎片表' COLLATE=utf8_general_ci;

# 分类栏目表
DROP TABLE IF EXISTS pre_category;
CREATE TABLE pre_category (
  cid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  mid tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  type tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 (0为列表，1为频道)',
  upid int(10) NOT NULL DEFAULT '0' COMMENT '上级ID',	
  name varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',	 
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '唯一别名 (必填，只能是英文、数字、下划线，并且不超过50个字符，用于伪静态)', 
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '分类介绍',
  cate_tpl varchar(80) NOT NULL DEFAULT '' COMMENT '分类页模板',
  show_tpl varchar(80) NOT NULL DEFAULT '' COMMENT '内容页模板',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
  orderby smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  seo_title varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  seo_keywords varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  seo_description varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO描述',	
  user_post tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启投稿，针对文章模型 (1是，0否) 预留字段',
  PRIMARY KEY (cid),
  KEY mid (mid),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='分类栏目表' COLLATE=utf8_general_ci;

# 单页表
DROP TABLE IF EXISTS pre_cms_page;
CREATE TABLE pre_cms_page (
  cid smallint(5) unsigned NOT NULL COMMENT '分类ID',	 
  content mediumtext NOT NULL COMMENT '单页内容',
  PRIMARY KEY (cid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='单页表' COLLATE=utf8_general_ci;

# 评论表 
DROP TABLE IF EXISTS pre_cms_comment;
CREATE TABLE pre_cms_comment (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  mid tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '模型ID(默认文章模型)', 
  commentid int(10) unsigned NOT NULL AUTO_INCREMENT,
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  author char(30) NOT NULL DEFAULT '' COMMENT '称呼',
  content text NOT NULL COMMENT '评论内容',
  ip int(10) NOT NULL DEFAULT '0' COMMENT 'IP',
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  PRIMARY KEY  (commentid),
  KEY id (id,commentid,mid),
  KEY ip (ip,commentid)	# 用来做防灌水插件
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表' COLLATE=utf8_general_ci;

# 后台菜单表
DROP TABLE IF EXISTS pre_menu_admin;
CREATE TABLE pre_menu_admin (
  `cid` smallint(6) NOT NULL AUTO_INCREMENT,
  `upid` smallint(6) NOT NULL DEFAULT '0',
  `title` char(200) NOT NULL DEFAULT '' COMMENT '中文标题',
  `controller` char(200) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` char(200) NOT NULL DEFAULT 'index' COMMENT '方法',
  `param` char(255) NOT NULL DEFAULT '' COMMENT '参数',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1 显示 0 不显示',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '类型 1 权限控制菜单   2 普通菜单 ',
  `listorder` mediumint(6) NOT NULL DEFAULT '100' COMMENT '排序',
  `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统菜单 1 是  0 不是',
  `favorite` tinyint(1) NOT NULL DEFAULT '0' COMMENT '后台常用菜单   1 是  0 不是',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台菜单表' COLLATE=utf8_general_ci;

# 内容模型表
DROP TABLE IF EXISTS pre_models;
CREATE TABLE pre_models (
  mid tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  name char(10) NOT NULL DEFAULT '' COMMENT '模型名称',
  tablename char(20) NOT NULL DEFAULT '' COMMENT '模型表名',
  index_tpl char(80) NOT NULL DEFAULT '' COMMENT '默认频道页模板',
  cate_tpl char(80) NOT NULL DEFAULT '' COMMENT '默认列表页模板', 
  show_tpl char(80) NOT NULL DEFAULT '' COMMENT '默认内容页模板',
  system tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否由系统定义 (1为系统定义，0为自定义)',
  PRIMARY KEY (mid),
  UNIQUE KEY tablename (tablename)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='内容模型表' COLLATE=utf8_general_ci;

# 唯一别名表，用于伪静态 (只储存内容的别名，分类和其他别名放 kv 表)
DROP TABLE IF EXISTS pre_only_alias;
CREATE TABLE pre_only_alias (
  alias varchar(50) NOT NULL COMMENT '唯一别名 (只能是英文、数字、下划线)',	
  mid tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (alias),
  KEY mid_id (mid,id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='唯一别名表' COLLATE=utf8_general_ci;

# 文章投稿表
DROP TABLE IF EXISTS pre_cms_audit_article;
CREATE TABLE pre_cms_audit_article (
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  tags varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (字符串 多个以英文逗号隔开)',
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  pic varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  author varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  source varchar(150) NOT NULL DEFAULT '' COMMENT '来源',	
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  lasttime int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  iscomment tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  imagenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  filenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数', 
  flags tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)', 
  status tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 (0:未审核，1：已拒绝)',
  refuse varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝理由(简短)', 
  seo_title varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  seo_keywords varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  content mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY  (id),
  KEY cid_id (cid,id),
  KEY uid (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章投稿表' COLLATE=utf8_general_ci;

# 文章表 (可根据 id 范围分区, 审核/定时发布等考虑单独设计一张表)
DROP TABLE IF EXISTS pre_cms_article;
CREATE TABLE pre_cms_article (
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(80) NOT NULL DEFAULT '' COMMENT '标题', 
  color char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  tags varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  pic varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  author varchar(20) NOT NULL DEFAULT '' COMMENT '作者', 
  source varchar(150) NOT NULL DEFAULT '' COMMENT '来源',	
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  lasttime int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',	 
  iscomment tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  comments int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  imagenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  filenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  flags tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  seo_title varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  seo_keywords varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY  (id),
  KEY cid_id (cid,id),
  KEY uid (uid),
  KEY flags (flags)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章表' COLLATE=utf8_general_ci;

# 文章数据表 (大内容字段表，可根据 id 范围分区)
DROP TABLE IF EXISTS pre_cms_article_data;
CREATE TABLE pre_cms_article_data (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID', 
  content mediumtext NOT NULL COMMENT '内容',	 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章数据表' COLLATE=utf8_general_ci;

# 文章查看数表，用来分离主表的写压力
DROP TABLE IF EXISTS pre_cms_article_views;
CREATE TABLE pre_cms_article_views (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	 
  views int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数', 
  PRIMARY KEY  (id),
  KEY cid (cid,views),
  KEY views (views)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章查看数表' COLLATE=utf8_general_ci;

# 文章标签表
DROP TABLE IF EXISTS pre_cms_article_tag;
CREATE TABLE pre_cms_article_tag (
  tagid int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称', 
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',
  `content` VARCHAR( 255 ) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY  (tagid),
  UNIQUE KEY name (name),
  KEY count (count)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章标签表' COLLATE=utf8_general_ci;

# 文章标签数据表
DROP TABLE IF EXISTS pre_cms_article_tag_data;
CREATE TABLE pre_cms_article_tag_data (
  tagid int(10) unsigned NOT NULL COMMENT '标签ID',
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY  (tagid,id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章标签数据表' COLLATE=utf8_general_ci;

# 产品表 (可根据 id 范围分区, 审核/定时发布等考虑单独设计一张表)
DROP TABLE IF EXISTS pre_cms_product;
CREATE TABLE pre_cms_product (
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	 
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  color char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  tags varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  pic varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  author varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  source varchar(150) NOT NULL DEFAULT '' COMMENT '来源',	
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  lasttime int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',	
  iscomment tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)', 
  comments int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  imagenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  filenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数', 
  flags tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)', 
  seo_title varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  seo_keywords varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY  (id),
  KEY cid_id (cid,id),
  KEY uid (uid),
  KEY flags (flags)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品表' COLLATE=utf8_general_ci;

# 产品数据表 (大内容字段表，可根据 id 范围分区)
DROP TABLE IF EXISTS pre_cms_product_data;
CREATE TABLE pre_cms_product_data (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID', 
  images text NOT NULL COMMENT '产品图片',
  content mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品数据表' COLLATE=utf8_general_ci;

# 产品查看数表，用来分离主表的写压力
DROP TABLE IF EXISTS pre_cms_product_views;
CREATE TABLE pre_cms_product_views (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	 
  views int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY  (id),
  KEY cid (cid,views),
  KEY views (views)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品查看数表' COLLATE=utf8_general_ci;

# 产品标签表
DROP TABLE IF EXISTS pre_cms_product_tag;
CREATE TABLE pre_cms_product_tag (
  tagid int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',	
  `content` VARCHAR( 255 ) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY  (tagid),
  UNIQUE KEY name (name),
  KEY count (count)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品标签表' COLLATE=utf8_general_ci;

# 产品标签数据表
DROP TABLE IF EXISTS pre_cms_product_tag_data;
CREATE TABLE pre_cms_product_tag_data (
  tagid int(10) unsigned NOT NULL COMMENT '标签ID',
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY  (tagid,id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品标签数据表' COLLATE=utf8_general_ci;

# 图集表 (可根据 id 范围分区, 审核/定时发布等考虑单独设计一张表)
DROP TABLE IF EXISTS pre_cms_photo;
CREATE TABLE pre_cms_photo (
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  color char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  alias varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  tags varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  pic varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  author varchar(20) NOT NULL DEFAULT '' COMMENT '作者', 
  source varchar(150) NOT NULL DEFAULT '' COMMENT '来源',	
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  lasttime int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  listorder smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',	
  iscomment tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  comments int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  imagenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  filenum int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  flags tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  seo_title varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  seo_keywords varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY  (id),
  KEY cid_id (cid,id),
  KEY uid (uid),
  KEY flags (flags)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集表' COLLATE=utf8_general_ci;

# 图集数据表 (大内容字段表，可根据 id 范围分区)
DROP TABLE IF EXISTS pre_cms_photo_data;
CREATE TABLE pre_cms_photo_data (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID', 
  images text NOT NULL COMMENT '图集图片',	
  content mediumtext NOT NULL COMMENT '内容', 
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集数据表' COLLATE=utf8_general_ci;

# 图集查看数表，用来分离主表的写压力
DROP TABLE IF EXISTS pre_cms_photo_views;
CREATE TABLE pre_cms_photo_views (
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	 
  views int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY  (id),
  KEY cid (cid,views),
  KEY views (views)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集查看数表' COLLATE=utf8_general_ci;

# 图集标签表
DROP TABLE IF EXISTS pre_cms_photo_tag;
CREATE TABLE pre_cms_photo_tag (
  tagid int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',	
  `content` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY  (tagid),
  UNIQUE KEY name (name),
  KEY count (count)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集标签表' COLLATE=utf8_general_ci;

# 图集标签数据表
DROP TABLE IF EXISTS pre_cms_photo_tag_data;
CREATE TABLE pre_cms_photo_tag_data (
  tagid int(10) unsigned NOT NULL COMMENT '标签ID',
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY  (tagid,id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集标签数据表' COLLATE=utf8_general_ci;

# 留言表
DROP TABLE IF EXISTS pre_cms_guestbook;
CREATE TABLE pre_cms_guestbook (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  title varchar(50) NOT NULL DEFAULT '' COMMENT '留言标题',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态（1,已读 0,未读）',
  dateline int(10) unsigned NOT NULL DEFAULT '0' COMMENT '留言时间',
  author varchar(20) NOT NULL DEFAULT '' COMMENT '称呼',
  telephone varchar(50) NOT NULL DEFAULT '' COMMENT '电话或手机号',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `content` mediumtext NOT NULL COMMENT '留言内容', 
  `ip` int(10) NOT NULL DEFAULT '0' COMMENT '留言IP',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表' COLLATE=utf8_general_ci;

# 后台登陆日志表
DROP TABLE IF EXISTS pre_loginlog;
CREATE TABLE pre_loginlog (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `username` char(30) NOT NULL COMMENT '登录帐号',
  `logintime` int(10) NOT NULL COMMENT '登录时间戳',
  `loginip` char(20) NOT NULL COMMENT '登录IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,1为登录成功，0为登录失败',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT '尝试错误密码',
  `info` varchar(255) NOT NULL COMMENT '其他说明',
  PRIMARY KEY (`id`),
  KEY username (username)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台登陆日志表' COLLATE=utf8_general_ci;

# 后台操作日志表
DROP TABLE IF EXISTS pre_operationlog;
CREATE TABLE pre_operationlog (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `uid` smallint(6) NOT NULL COMMENT '操作帐号ID',
  `dateline` int(10) NOT NULL COMMENT '操作时间',
  `ip` char(20) NOT NULL DEFAULT '' COMMENT 'IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,0错误提示，1为正确提示',
  `info` varchar(255) NOT NULL COMMENT '其他说明',
  `get` varchar(255) NOT NULL COMMENT 'get数据',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `username` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='后台操作日志表' COLLATE=utf8_general_ci;

# 持久保存的 键值 数据 (包括设置信息)
DROP TABLE IF EXISTS pre_kv;
CREATE TABLE pre_kv (
  k char(32) NOT NULL DEFAULT '' COMMENT '键名',
  v text NOT NULL DEFAULT '' COMMENT '数据',
  expiry int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY(k)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='持久保存的 key value 数据表' COLLATE=utf8_general_ci;

# 缓存表
DROP TABLE IF EXISTS pre_runtime;
CREATE TABLE pre_runtime (
  k char(32) NOT NULL DEFAULT '' COMMENT '键名',
  v text NOT NULL DEFAULT '' COMMENT '数据',
  expiry int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY(k)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='缓存表' COLLATE=utf8_general_ci;


# 记录其它表的总行数
DROP TABLE IF EXISTS pre_framework_count;
CREATE TABLE pre_framework_count (
  name char(32) NOT NULL DEFAULT '' COMMENT '表名',
  count int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总行数',
  PRIMARY KEY (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='记录其它表的总行数表' COLLATE=utf8_general_ci;

# 记录其它表的最大ID
DROP TABLE IF EXISTS pre_framework_maxid;
CREATE TABLE pre_framework_maxid (
  name char(32) NOT NULL DEFAULT '' COMMENT '表名',
  maxid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大ID', 
  PRIMARY KEY (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='记录其它表的最大ID表' COLLATE=utf8_general_ci;

# 用户表，可根据 uid 范围进行分区
DROP TABLE IF EXISTS pre_user;
CREATE TABLE pre_user (
  uid int(10) unsigned NOT NULL AUTO_INCREMENT,
  username char(16) NOT NULL DEFAULT '' COMMENT '用户名',
  password char(32) NOT NULL DEFAULT '' COMMENT '密码',
  salt char(16) NOT NULL DEFAULT '' COMMENT '随机干扰字符，用来混淆密码',
  groupid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户组ID',
  email char(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  regip int(10) NOT NULL DEFAULT '0' COMMENT '注册IP', 
  regdate int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册日期',
  loginip int(10) NOT NULL DEFAULT '0' COMMENT '登陆IP',
  logindate int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆日期',
  lastip int(10) NOT NULL DEFAULT '0' COMMENT '上次登陆IP',
  lastdate int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次登陆日期', 
  contents int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
  comments int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  logins int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆数',
  author varchar(20) NOT NULL DEFAULT '' COMMENT '责任编辑,昵称',
  status tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:1正常，0待审核',
  isadmin tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台用户（0:否，1:是）', 
  avatar varchar(255) NOT NULL DEFAULT '' COMMENT '个人头像',
  PRIMARY KEY (uid),
  UNIQUE KEY username(username)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表' COLLATE=utf8_general_ci;

# 用户详情表
DROP TABLE IF EXISTS pre_user_data;
CREATE TABLE pre_user_data (
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  homepage varchar(255) NOT NULL DEFAULT '' COMMENT '个人主页',
  intro varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  qq char(11) NOT NULL DEFAULT '' COMMENT 'QQ号',
  mobile char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  tel varchar(40) NOT NULL DEFAULT '' COMMENT '电话',
  address varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  PRIMARY KEY (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户详情表' COLLATE=utf8_general_ci;

# 用户收藏表
DROP TABLE IF EXISTS pre_user_collect;
CREATE TABLE pre_user_collect (
  collect_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  uid int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',	
  mid tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  cid smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',	
  id int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (collect_id),
  KEY uid (uid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户收藏表' COLLATE=utf8_general_ci;

# 用户组表
DROP TABLE IF EXISTS pre_user_group;
CREATE TABLE pre_user_group (
  groupid smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  groupname char(20) NOT NULL DEFAULT '' COMMENT '用户组名',
  system tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否由系统定义 (1为系统定义，0为自定义)', 
  isadmin tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台用户组（0:否，1:是）',
  purviews text NOT NULL COMMENT '后台权限 (为空时不限制)',	
  PRIMARY KEY (groupid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户组表' COLLATE=utf8_general_ci;

# 友情链接表
DROP TABLE IF EXISTS pre_link;
CREATE TABLE pre_link (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  type mediumint(8) unsigned NOT NULL,
  title varchar(50) NOT NULL,
  thumb varchar(200) NOT NULL,
  url varchar(150) NOT NULL,
  remark mediumtext NOT NULL,
  listorder int(3) unsigned NOT NULL DEFAULT '99',
  status tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY category (type)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='友情链接表' COLLATE=utf8_general_ci;

# 模块类型表
DROP TABLE IF EXISTS pre_types;
CREATE TABLE pre_types (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(20) NOT NULL,
  class varchar(20) NOT NULL,
  remark varchar(100) NOT NULL,
  thumb varchar(100) NOT NULL,
  listorder tinyint(4) unsigned NOT NULL DEFAULT '99',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块类型表' COLLATE=utf8_general_ci;

# 幻灯片表
DROP TABLE IF EXISTS pre_slide;
CREATE TABLE pre_slide (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  type mediumint(8) unsigned NOT NULL,
  title varchar(50) NOT NULL,
  thumb varchar(200) NOT NULL,
  url varchar(150) NOT NULL,
  remark mediumtext NOT NULL,
  listorder int(3) unsigned NOT NULL DEFAULT '99',
  status tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (id),
  KEY category (type)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='幻灯片表' COLLATE=utf8_general_ci;