-- --------------------------------------------------------
-- 主机:                           127.0.0.1
-- 服务器版本:                        5.6.12-log - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出  表 molycms.moly_category 结构
CREATE TABLE IF NOT EXISTS `moly_category` (
  `cid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `mid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 (0为列表，1为频道)',
  `upid` int(10) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名称',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '唯一别名 (必填，只能是英文、数字、下划线，并且不超过50个字符，用于伪静态)',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '分类介绍',
  `cate_tpl` varchar(80) NOT NULL DEFAULT '' COMMENT '分类页模板',
  `show_tpl` varchar(80) NOT NULL DEFAULT '' COMMENT '内容页模板',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
  `orderby` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `seo_title` varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` varchar(255) NOT NULL DEFAULT '' COMMENT 'SEO描述',
  `user_post` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启投稿，针对文章模型 (1是，0否) 预留字段',
  PRIMARY KEY (`cid`),
  UNIQUE KEY `alias` (`alias`),
  KEY `mid` (`mid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='分类栏目表';

-- 正在导出表  molycms.moly_category 的数据：10 rows
/*!40000 ALTER TABLE `moly_category` DISABLE KEYS */;
INSERT INTO `moly_category` (`cid`, `mid`, `type`, `upid`, `name`, `alias`, `intro`, `cate_tpl`, `show_tpl`, `count`, `orderby`, `seo_title`, `seo_keywords`, `seo_description`, `user_post`) VALUES
	(1, 2, 1, 0, '新闻中心', 'news', '', 'article_index.htm', 'article_show.htm', 0, 0, '', '', '', 0),
	(2, 2, 0, 1, '公司新闻', 'news1', '', 'article_list.htm', 'article_show.htm', 0, 0, '', '', '', 0),
	(3, 2, 0, 1, '行业新闻', 'news2', '', 'article_list.htm', 'article_show.htm', 0, 0, '', '', '', 0),
	(4, 3, 1, 0, '产品中心', 'product', '', 'product_index.htm', 'product_show.htm', 0, 0, '', '', '', 0),
	(5, 3, 0, 4, '手机', 'product1', '', 'product_list.htm', 'product_show.htm', 0, 0, '', '', '', 0),
	(6, 3, 0, 4, '灯饰', 'product2', '', 'product_list.htm', 'product_show.htm', 0, 0, '', '', '', 0),
	(7, 4, 1, 0, '图集中心', 'photo', '', 'photo_index.htm', 'photo_show.htm', 0, 0, '', '', '', 0),
	(8, 4, 0, 7, '美女', 'photo1', '', 'photo_list.htm', 'photo_show.htm', 0, 0, '', '', '', 0),
	(9, 4, 0, 7, '酷车', 'photo2', '', 'photo_list.htm', 'photo_show.htm', 0, 0, '', '', '', 0),
	(10, 1, 0, 0, '关于我们', 'about', '', 'page_show.htm', '', 0, 0, '', '', '', 0);
/*!40000 ALTER TABLE `moly_category` ENABLE KEYS */;


-- 导出  表 molycms.moly_cate_fields 结构
CREATE TABLE IF NOT EXISTS `moly_cate_fields` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `description` varchar(40) DEFAULT NULL,
  `model` smallint(10) unsigned DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `length` smallint(10) unsigned DEFAULT NULL,
  `source` tinytext,
  `width` smallint(10) DEFAULT NULL,
  `height` smallint(10) DEFAULT NULL,
  `rules` tinytext,
  `ruledescription` tinytext,
  `searchable` tinyint(1) unsigned DEFAULT NULL,
  `listable` tinyint(1) unsigned DEFAULT NULL,
  `listorder` int(5) unsigned DEFAULT NULL,
  `editable` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`model`),
  KEY `model` (`model`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  molycms.moly_cate_fields 的数据：0 rows
/*!40000 ALTER TABLE `moly_cate_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cate_fields` ENABLE KEYS */;


-- 导出  表 molycms.moly_cate_models 结构
CREATE TABLE IF NOT EXISTS `moly_cate_models` (
  `id` mediumint(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(40) NOT NULL,
  `perpage` varchar(2) NOT NULL,
  `level` tinyint(2) unsigned NOT NULL DEFAULT '1',
  `hasattach` tinyint(1) NOT NULL DEFAULT '0',
  `built_in` tinyint(1) DEFAULT '0',
  `auto_update` tinyint(1) DEFAULT '0',
  `thumb_preferences` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  molycms.moly_cate_models 的数据：0 rows
/*!40000 ALTER TABLE `moly_cate_models` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cate_models` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_ad 结构
CREATE TABLE IF NOT EXISTS `moly_cms_ad` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `adname` varchar(60) NOT NULL DEFAULT '' COMMENT '广告名称',
  `timeset` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启时间限制',
  `starttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `normbody` text COMMENT '广告内容',
  `expbody` varchar(255) NOT NULL DEFAULT '' COMMENT '广告过期显示内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='广告表';

-- 正在导出表  molycms.moly_cms_ad 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_ad` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_ad` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_article 结构
CREATE TABLE IF NOT EXISTS `moly_cms_article` (
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  `color` char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  `source` varchar(150) NOT NULL DEFAULT '' COMMENT '来源',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',
  `iscomment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  `comments` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `imagenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  `filenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  `flags` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  `seo_title` varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY (`id`),
  KEY `cid_id` (`cid`,`id`),
  KEY `uid` (`uid`),
  KEY `flags` (`flags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章表';

-- 正在导出表  molycms.moly_cms_article 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_article` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_article_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_article_data` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `content` mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章数据表';

-- 正在导出表  molycms.moly_cms_article_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_article_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_article_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_article_tag 结构
CREATE TABLE IF NOT EXISTS `moly_cms_article_tag` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY (`tagid`),
  UNIQUE KEY `name` (`name`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章标签表';

-- 正在导出表  molycms.moly_cms_article_tag 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_article_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_article_tag` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_article_tag_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_article_tag_data` (
  `tagid` int(10) unsigned NOT NULL COMMENT '标签ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (`tagid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章标签数据表';

-- 正在导出表  molycms.moly_cms_article_tag_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_article_tag_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_article_tag_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_article_views 结构
CREATE TABLE IF NOT EXISTS `moly_cms_article_views` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`views`),
  KEY `views` (`views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章查看数表';

-- 正在导出表  molycms.moly_cms_article_views 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_article_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_article_views` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_attach 结构
CREATE TABLE IF NOT EXISTS `moly_cms_attach` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `mid` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '模型ID(默认文章模型)',
  `filename` char(80) NOT NULL DEFAULT '' COMMENT '文件原名',
  `filetype` char(10) NOT NULL DEFAULT '' COMMENT '后缀',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '大小',
  `filepath` char(150) NOT NULL DEFAULT '' COMMENT '路径',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `downloads` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是图片 (1为图片，0为文件)',
  PRIMARY KEY (`aid`),
  KEY `id` (`id`,`aid`,`mid`),
  KEY `uid` (`uid`,`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='附件表';

-- 正在导出表  molycms.moly_cms_attach 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_attach` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_attach` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_audit_article 结构
CREATE TABLE IF NOT EXISTS `moly_cms_audit_article` (
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (字符串 多个以英文逗号隔开)',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  `source` varchar(150) NOT NULL DEFAULT '' COMMENT '来源',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `iscomment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  `imagenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  `filenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  `flags` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 (0:未审核，1：已拒绝)',
  `refuse` varchar(255) NOT NULL DEFAULT '' COMMENT '拒绝理由(简短)',
  `seo_title` varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  `content` mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `cid_id` (`cid`,`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章投稿表';

-- 正在导出表  molycms.moly_cms_audit_article 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_audit_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_audit_article` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_block 结构
CREATE TABLE IF NOT EXISTS `moly_cms_block` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '碎片标题',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '更多链接',
  `content` mediumtext NOT NULL COMMENT '碎片内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='碎片表';

-- 正在导出表  molycms.moly_cms_block 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_block` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_block` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_comment 结构
CREATE TABLE IF NOT EXISTS `moly_cms_comment` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `mid` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '模型ID(默认文章模型)',
  `commentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `author` char(30) NOT NULL DEFAULT '' COMMENT '称呼',
  `content` text NOT NULL COMMENT '评论内容',
  `ip` int(10) NOT NULL DEFAULT '0' COMMENT 'IP',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  PRIMARY KEY (`commentid`),
  KEY `id` (`id`,`commentid`,`mid`),
  KEY `ip` (`ip`,`commentid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';

-- 正在导出表  molycms.moly_cms_comment 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_comment` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_guestbook 结构
CREATE TABLE IF NOT EXISTS `moly_cms_guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '留言标题',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态（1,已读 0,未读）',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '留言时间',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '称呼',
  `telephone` varchar(50) NOT NULL DEFAULT '' COMMENT '电话或手机号',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `content` mediumtext NOT NULL COMMENT '留言内容',
  `ip` int(10) NOT NULL DEFAULT '0' COMMENT '留言IP',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表';

-- 正在导出表  molycms.moly_cms_guestbook 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_guestbook` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_guestbook` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_page 结构
CREATE TABLE IF NOT EXISTS `moly_cms_page` (
  `cid` smallint(5) unsigned NOT NULL COMMENT '分类ID',
  `content` mediumtext NOT NULL COMMENT '单页内容',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='单页表';

-- 正在导出表  molycms.moly_cms_page 的数据：1 rows
/*!40000 ALTER TABLE `moly_cms_page` DISABLE KEYS */;
INSERT INTO `moly_cms_page` (`cid`, `content`) VALUES
	(10, '关于我们');
/*!40000 ALTER TABLE `moly_cms_page` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_photo 结构
CREATE TABLE IF NOT EXISTS `moly_cms_photo` (
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  `color` char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  `source` varchar(150) NOT NULL DEFAULT '' COMMENT '来源',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',
  `iscomment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  `comments` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `imagenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  `filenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  `flags` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  `seo_title` varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY (`id`),
  KEY `cid_id` (`cid`,`id`),
  KEY `uid` (`uid`),
  KEY `flags` (`flags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集表';

-- 正在导出表  molycms.moly_cms_photo 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_photo` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_photo` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_photo_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_photo_data` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `images` text NOT NULL COMMENT '图集图片',
  `content` mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集数据表';

-- 正在导出表  molycms.moly_cms_photo_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_photo_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_photo_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_photo_tag 结构
CREATE TABLE IF NOT EXISTS `moly_cms_photo_tag` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY (`tagid`),
  UNIQUE KEY `name` (`name`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集标签表';

-- 正在导出表  molycms.moly_cms_photo_tag 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_photo_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_photo_tag` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_photo_tag_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_photo_tag_data` (
  `tagid` int(10) unsigned NOT NULL COMMENT '标签ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (`tagid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集标签数据表';

-- 正在导出表  molycms.moly_cms_photo_tag_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_photo_tag_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_photo_tag_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_photo_views 结构
CREATE TABLE IF NOT EXISTS `moly_cms_photo_views` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`views`),
  KEY `views` (`views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图集查看数表';

-- 正在导出表  molycms.moly_cms_photo_views 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_photo_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_photo_views` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_product 结构
CREATE TABLE IF NOT EXISTS `moly_cms_product` (
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL DEFAULT '' COMMENT '标题',
  `color` char(6) NOT NULL DEFAULT '' COMMENT '标题颜色',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '英文别名',
  `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签 (json数据)',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '摘要',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '缩略图',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  `source` varchar(150) NOT NULL DEFAULT '' COMMENT '来源',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表时间',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序值（越小越在后面）',
  `iscomment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止评论 (1为禁止 0为允许)',
  `comments` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `imagenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片附件数',
  `filenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件附件数',
  `flags` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性 (单选)',
  `seo_title` varchar(80) NOT NULL DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(100) NOT NULL DEFAULT '' COMMENT 'SEO关键词',
  PRIMARY KEY (`id`),
  KEY `cid_id` (`cid`,`id`),
  KEY `uid` (`uid`),
  KEY `flags` (`flags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品表';

-- 正在导出表  molycms.moly_cms_product 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_product` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_product_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_product_data` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `images` text NOT NULL COMMENT '产品图片',
  `content` mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品数据表';

-- 正在导出表  molycms.moly_cms_product_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_product_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_product_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_product_tag 结构
CREATE TABLE IF NOT EXISTS `moly_cms_product_tag` (
  `tagid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT 'tag名称',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tag内容数量',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT 'tag描述',
  PRIMARY KEY (`tagid`),
  UNIQUE KEY `name` (`name`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品标签表';

-- 正在导出表  molycms.moly_cms_product_tag 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_product_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_product_tag` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_product_tag_data 结构
CREATE TABLE IF NOT EXISTS `moly_cms_product_tag_data` (
  `tagid` int(10) unsigned NOT NULL COMMENT '标签ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (`tagid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品标签数据表';

-- 正在导出表  molycms.moly_cms_product_tag_data 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_product_tag_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_product_tag_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_cms_product_views 结构
CREATE TABLE IF NOT EXISTS `moly_cms_product_views` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`views`),
  KEY `views` (`views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品查看数表';

-- 正在导出表  molycms.moly_cms_product_views 的数据：0 rows
/*!40000 ALTER TABLE `moly_cms_product_views` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_cms_product_views` ENABLE KEYS */;


-- 导出  表 molycms.moly_fieldtypes 结构
CREATE TABLE IF NOT EXISTS `moly_fieldtypes` (
  `k` varchar(20) NOT NULL,
  `v` varchar(20) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  molycms.moly_fieldtypes 的数据：15 rows
/*!40000 ALTER TABLE `moly_fieldtypes` DISABLE KEYS */;
INSERT INTO `moly_fieldtypes` (`k`, `v`) VALUES
	('int', '整形(INT)'),
	('float', '浮点型(FLOAT)'),
	('input', '单行文本框(VARCHAR)'),
	('textarea', '文本区域(VARCHAR)'),
	('select', '下拉菜单(VARCHAR)'),
	('select_from_model', '下拉菜单(模型数据)(INT)'),
	('linked_menu', '联动下拉菜单(VARCHAR)'),
	('radio', '单选按钮(VARCHAR)'),
	('radio_from_model', '单选按钮(模型数据)(INT)'),
	('checkbox', '复选框(VARCHAR)'),
	('checkbox_from_model', '复选框(模型数据)(VARCHAR)'),
	('wysiwyg', '编辑器(TEXT)'),
	('wysiwyg_basic', '编辑器(简)(TEXT)'),
	('datetime', '日期时间(VARCHAR)'),
	('content', '内容模型调用(INT)');
/*!40000 ALTER TABLE `moly_fieldtypes` ENABLE KEYS */;


-- 导出  表 molycms.moly_framework_count 结构
CREATE TABLE IF NOT EXISTS `moly_framework_count` (
  `name` char(32) NOT NULL DEFAULT '' COMMENT '表名',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总行数',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='记录其它表的总行数表';

-- 正在导出表  molycms.moly_framework_count 的数据：17 rows
/*!40000 ALTER TABLE `moly_framework_count` DISABLE KEYS */;
INSERT INTO `moly_framework_count` (`name`, `count`) VALUES
	('runtime', 0),
	('loginlog', 6),
	('menu_admin', 50),
	('operationlog', 26),
	('diy_models', 6),
	('cms_block', 0),
	('slide', 0),
	('cms_guestbook', 0),
	('cms_ad', 0),
	('types', 3),
	('model_fields', 37),
	('cms_article', 0),
	('cms_product', 0),
	('cms_photo', 0),
	('link', 0),
	('models', 4),
	('cate_models', 0);
/*!40000 ALTER TABLE `moly_framework_count` ENABLE KEYS */;


-- 导出  表 molycms.moly_framework_maxid 结构
CREATE TABLE IF NOT EXISTS `moly_framework_maxid` (
  `name` char(32) NOT NULL DEFAULT '' COMMENT '表名',
  `maxid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大ID',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='记录其它表的最大ID表';

-- 正在导出表  molycms.moly_framework_maxid 的数据：5 rows
/*!40000 ALTER TABLE `moly_framework_maxid` DISABLE KEYS */;
INSERT INTO `moly_framework_maxid` (`name`, `maxid`) VALUES
	('loginlog', 6),
	('menu_admin', 50),
	('operationlog', 26),
	('diy_models', 6),
	('model_fields', 43);
/*!40000 ALTER TABLE `moly_framework_maxid` ENABLE KEYS */;


-- 导出  表 molycms.moly_kv 结构
CREATE TABLE IF NOT EXISTS `moly_kv` (
  `k` char(32) NOT NULL DEFAULT '' COMMENT '键名',
  `v` text NOT NULL COMMENT '数据',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='持久保存的 key value 数据表';

-- 正在导出表  molycms.moly_kv 的数据：4 rows
/*!40000 ALTER TABLE `moly_kv` DISABLE KEYS */;
INSERT INTO `moly_kv` (`k`, `v`, `expiry`) VALUES
	('link_keywords', '["tag","tag_top","comment","index","sitemap","admin","user","space","static","molycms"]', 0),
	('navigate_3', '{"1":{"cid":1,"alias":"news","name":"\\u65b0\\u95fb\\u4e2d\\u5fc3","url":1,"target":"_self"},"2":{"cid":4,"alias":"product","name":"\\u4ea7\\u54c1\\u4e2d\\u5fc3","url":4,"target":"_self"},"3":{"cid":7,"alias":"photo","name":"\\u56fe\\u96c6","url":7,"target":"_self"},"4":{"cid":10,"alias":"about","name":"\\u5173\\u4e8e\\u6211\\u4eec","url":10,"target":"_self"},"5":{"cid":0,"alias":"","name":"BAIDU","url":"http:\\/\\/www.baidu.com","target":"_blank"}}', 0),
	('cfg', '{"webname":"MOLYCMS","webdomain":"www.test.com","webdir":"\\/molycms\\/","webmail":"admin@molycms.com","tongji":"<script type=\\"text\\/javascript\\">var cnzz_protocol = ((\\"https:\\" == document.location.protocol) ? \\" https:\\/\\/\\" : \\" http:\\/\\/\\");document.write(unescape(\\"%3Cspan id=\'cnzz_stat_icon_1253619239\'%3E%3C\\/span%3E%3Cscript src=\'\\" + cnzz_protocol + \\"s95.cnzz.com\\/stat.php%3Fid%3D1253619239\' type=\'text\\/javascript\'%3E%3C\\/script%3E\\"));<\\/script>","beian":"\\u9102ICP\\u590714018391\\u53f7-1","dis_comment":0,"user_comment":0,"comment_filter":"","footer_info":"Power by Molycms \\u7248\\u6743\\u6240\\u6709","seo_title":"\\u7f51\\u7ad9\\u5efa\\u8bbe\\u5229\\u5668\\uff01","seo_keywords":"MOLYCMS","seo_description":"MOLYCMS\\uff0c\\u7f51\\u7ad9\\u5efa\\u8bbe\\u5229\\u5668\\uff01","link_show":"{cate_alias}\\/{id}.html","link_show_type":2,"link_show_end":".html","link_cate_page_pre":"\\/page_","link_cate_page_end":".html","link_cate_end":"\\/","link_tag_pre":"tag\\/","link_tag_end":".html","link_comment_pre":"comment\\/","link_comment_end":".html","link_index_end":".html","up_img_ext":"jpg,jpeg,gif,png","up_img_max_size":"3074","up_file_ext":"zip,gz,rar,iso,xsl,doc,ppt,wps","up_file_max_size":"10240","thumb_article_w":150,"thumb_article_h":150,"thumb_product_w":150,"thumb_product_h":150,"thumb_photo_w":150,"thumb_photo_h":150,"thumb_type":2,"thumb_quality":90,"watermark_pos":9,"watermark_pct":90}', 0),
	('user_cfg', '{"open_user_model":0,"open_user_reg":0,"user_active_method":0,"email_active_content":"","email_pwd_content":""}', 0);
/*!40000 ALTER TABLE `moly_kv` ENABLE KEYS */;


-- 导出  表 molycms.moly_link 结构
CREATE TABLE IF NOT EXISTS `moly_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` mediumint(8) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `url` varchar(150) NOT NULL,
  `remark` mediumtext NOT NULL,
  `listorder` int(3) unsigned NOT NULL DEFAULT '99',
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- 正在导出表  molycms.moly_link 的数据：0 rows
/*!40000 ALTER TABLE `moly_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_link` ENABLE KEYS */;


-- 导出  表 molycms.moly_loginlog 结构
CREATE TABLE IF NOT EXISTS `moly_loginlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `username` char(30) NOT NULL COMMENT '登录帐号',
  `logintime` int(10) NOT NULL COMMENT '登录时间戳',
  `loginip` char(20) NOT NULL COMMENT '登录IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,1为登录成功，0为登录失败',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT '尝试错误密码',
  `info` varchar(255) NOT NULL COMMENT '其他说明',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='后台登陆日志表';

-- 正在导出表  molycms.moly_loginlog 的数据：6 rows
/*!40000 ALTER TABLE `moly_loginlog` DISABLE KEYS */;
INSERT INTO `moly_loginlog` (`id`, `username`, `logintime`, `loginip`, `status`, `password`, `info`) VALUES
	(1, 'admin', 1419254605, '127.0.0.1', 1, '密码保密', '登录成功'),
	(2, 'admin', 1419255731, '127.0.0.1', 1, '密码保密', '登录成功'),
	(3, 'admin', 1419335455, '127.0.0.1', 1, '密码保密', '登录成功'),
	(4, 'admin', 1419768542, '127.0.0.1', 1, '密码保密', '登录成功'),
	(5, 'admin', 1419858092, '127.0.0.1', 1, '密码保密', '登录成功'),
	(6, 'admin', 1419865576, '127.0.0.1', 1, '密码保密', '登录成功');
/*!40000 ALTER TABLE `moly_loginlog` ENABLE KEYS */;


-- 导出  表 molycms.moly_menu_admin 结构
CREATE TABLE IF NOT EXISTS `moly_menu_admin` (
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
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- 正在导出表  molycms.moly_menu_admin 的数据：50 rows
/*!40000 ALTER TABLE `moly_menu_admin` DISABLE KEYS */;
INSERT INTO `moly_menu_admin` (`cid`, `upid`, `title`, `controller`, `action`, `param`, `content`, `status`, `type`, `listorder`, `system`, `favorite`) VALUES
	(1, 0, '首页', 'index', '', '', '', 1, 2, 0, 1, 0),
	(2, 0, '我的', 'my', '', '', '', 1, 2, 0, 1, 0),
	(3, 0, '设置', 'setting', '', '', '', 1, 2, 0, 1, 0),
	(4, 0, '分类', 'category', '', '', '', 1, 2, 0, 1, 0),
	(5, 0, '内容', 'content', '', '', '', 1, 2, 0, 1, 0),
	(6, 0, '模块', 'module', '', '', '', 1, 2, 0, 1, 0),
	(7, 0, '主题', 'theme', '', '', '', 1, 2, 0, 1, 0),
	(8, 0, '插件', 'plugin', '', '', '', 1, 2, 0, 1, 0),
	(9, 0, '用户', 'user', '', '', '', 1, 2, 0, 1, 0),
	(10, 0, '工具', 'tool', '', '', '', 1, 2, 0, 1, 0),
	(11, 1, '站点统计', 'index', 'main', '', '', 1, 1, 0, 1, 0),
	(12, 1, '登录日志', 'log', 'login', '', '', 1, 1, 0, 1, 0),
	(13, 1, '后台操作日志', 'log', 'opt', '', '', 1, 1, 0, 1, 0),
	(14, 2, '我的信息', 'my', 'index', '', '', 1, 1, 0, 1, 0),
	(15, 2, '修改密码', 'my', 'password', '', '', 1, 1, 0, 1, 0),
	(16, 2, '修改个人资料', 'my', 'profile', '', '', 1, 1, 0, 1, 0),
	(17, 3, '基本设置', 'setting', 'index', '', '', 1, 1, 0, 1, 0),
	(18, 3, 'SEO设置', 'setting', 'seo', '', '', 1, 1, 0, 1, 0),
	(19, 3, '链接设置', 'setting', 'link', '', '', 1, 1, 0, 1, 0),
	(20, 3, '上传设置', 'setting', 'attach', '', '', 1, 1, 0, 1, 0),
	(21, 3, '图片设置', 'setting', 'image', '', '', 1, 1, 0, 1, 0),
	(22, 3, '评论设置', 'setting', 'comment', '', '', 1, 1, 0, 1, 0),
	(23, 3, '邮箱设置', 'setting', 'email', '', '', 1, 1, 0, 1, 0),
	(24, 4, '分类管理', 'category', 'index', '', '', 1, 1, 0, 1, 0),
	(25, 6, '导航管理', 'navigate', 'nav', '', '', 1, 1, 2, 1, 0),
	(26, 4, '系统模型', 'models', 'system', '', '', 1, 1, 0, 1, 0),
	(27, 5, '文章管理', 'article', 'index', '', '', 1, 1, 0, 1, 0),
	(28, 5, '产品管理', 'product', 'index', '', '', 1, 1, 0, 1, 0),
	(29, 5, '图集管理', 'photo', 'index', '', '', 1, 1, 0, 1, 0),
	(30, 5, '评论管理', 'comment', 'index', '', '', 1, 1, 0, 1, 0),
	(31, 5, '标签管理', 'tag', 'index', '', '', 1, 1, 0, 1, 0),
	(32, 6, '碎片管理', 'block', 'index', '', '', 1, 1, 1, 1, 0),
	(33, 6, '幻灯片管理', 'slide', 'index', '', '', 1, 1, 3, 1, 0),
	(34, 6, '留言管理', 'guestbook', 'index', '', '', 1, 1, 4, 1, 0),
	(35, 6, '广告管理', 'ad', 'index', '', '', 1, 1, 5, 1, 0),
	(36, 7, '主题管理', 'theme', 'index', '', '', 1, 1, 0, 1, 0),
	(37, 7, '模板管理', 'template', 'index', '', '', 1, 1, 0, 1, 0),
	(38, 8, '插件管理', 'plugin', 'index', '', '', 1, 1, 0, 1, 0),
	(39, 9, '用户管理', 'user', 'index', '', '', 1, 1, 0, 1, 0),
	(40, 9, '用户组管理', 'user_group', 'index', '', '', 1, 1, 0, 1, 0),
	(41, 10, '清除缓存', 'tool', 'index', '', '', 1, 1, 0, 1, 0),
	(42, 10, '重新统计', 'tool', 'rebuild', '', '', 1, 1, 0, 1, 0),
	(43, 10, '数据库管理', 'database', 'index', '', '', 1, 1, 0, 1, 0),
	(44, 10, '后台菜单', 'menu', 'index', '', '', 1, 1, 0, 1, 0),
	(45, 9, '用户模块设置', 'user', 'setting', '', '', 1, 1, 0, 1, 0),
	(46, 9, '投稿列表', 'audit', 'index', '', '', 1, 1, 0, 1, 0),
	(47, 6, '模块分类', 'types', 'index', '', '', 1, 1, 7, 1, 0),
	(48, 6, '友情链接', 'link', 'index', '', '', 1, 1, 6, 1, 0),
	(49, 4, '内容模型', 'models', 'index', '', '', 1, 1, 0, 0, 0),
	(50, 4, '分类模型', 'cates', 'index', '', '', 1, 1, 0, 0, 0);
/*!40000 ALTER TABLE `moly_menu_admin` ENABLE KEYS */;


-- 导出  表 molycms.moly_models 结构
CREATE TABLE IF NOT EXISTS `moly_models` (
  `mid` tinyint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '' COMMENT '模型名称',
  `tablename` char(20) NOT NULL DEFAULT '' COMMENT '模型表名',
  `index_tpl` char(80) NOT NULL DEFAULT '' COMMENT '默认频道页模板',
  `cate_tpl` char(80) NOT NULL DEFAULT '' COMMENT '默认列表页模板',
  `show_tpl` char(80) NOT NULL DEFAULT '' COMMENT '默认内容页模板',
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否由系统定义 (1为系统定义，0为自定义)',
  `hasattach` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `thumb_preferences` text,
  PRIMARY KEY (`mid`),
  UNIQUE KEY `tablename` (`tablename`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='内容模型表';

-- 正在导出表  molycms.moly_models 的数据：10 rows
/*!40000 ALTER TABLE `moly_models` DISABLE KEYS */;
INSERT INTO `moly_models` (`mid`, `name`, `tablename`, `index_tpl`, `cate_tpl`, `show_tpl`, `system`, `hasattach`, `thumb_preferences`) VALUES
	(1, '单页', 'page', '', 'page_show.htm', '', 1, 0, NULL),
	(2, '文章', 'article', 'article_index.htm', 'article_list.htm', 'article_show.htm', 1, 0, NULL),
	(3, '产品', 'product', 'product_index.htm', 'product_list.htm', 'product_show.htm', 1, 0, NULL),
	(4, '图集', 'photo', 'photo_index.htm', 'photo_list.htm', 'photo_show.htm', 1, 0, NULL),
	(5, '文章', 'art', '', '', '', 0, 1, '{"enabled":[],"default":"original"}'),
	(6, '页面', 'pages', '', '', '', 0, 1, '{"enabled":[],"default":"original"}'),
	(7, '滚动图片', 'slidepic', '', '', '', 0, 1, '{"enabled":[],"default":"original"}'),
	(8, '视频', 'video', '', '', '', 0, 1, '{"enabled":[],"default":"original"}'),
	(9, '链接', 'link', '', '', '', 0, 0, NULL),
	(10, '测试1', 'test1', '', '', '', 0, 0, NULL);
/*!40000 ALTER TABLE `moly_models` ENABLE KEYS */;


-- 导出  表 molycms.moly_model_fields 结构
CREATE TABLE IF NOT EXISTS `moly_model_fields` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(40) NOT NULL,
  `model` smallint(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT NULL,
  `length` smallint(10) unsigned DEFAULT NULL,
  `source` tinytext NOT NULL,
  `width` smallint(10) unsigned NOT NULL,
  `height` smallint(10) unsigned NOT NULL,
  `rules` tinytext NOT NULL,
  `ruledescription` tinytext NOT NULL,
  `searchable` tinyint(1) unsigned NOT NULL,
  `listable` tinyint(1) unsigned NOT NULL,
  `listorder` int(5) unsigned DEFAULT NULL,
  `editable` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`model`),
  KEY `model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- 正在导出表  molycms.moly_model_fields 的数据：33 rows
/*!40000 ALTER TABLE `moly_model_fields` DISABLE KEYS */;
INSERT INTO `moly_model_fields` (`id`, `name`, `description`, `model`, `type`, `length`, `source`, `width`, `height`, `rules`, `ruledescription`, `searchable`, `listable`, `listorder`, `editable`) VALUES
	(1, 'title', '标题', 6, 'input', 50, '', 500, 0, 'required', '*标题', 1, 1, 1, 1),
	(2, 'post_category', '文章分类', 6, 'select_from_model', 10, 'category|cate_name', 20, 20, 'required', '选择分类', 1, 1, 2, 1),
	(3, 'content', '内容', 6, 'wysiwyg', 10000, '', 0, 0, 'required', '', 1, 0, 3, 1),
	(4, 'title', '标题', 7, 'input', 30, '', 0, 0, 'required', '填写标题', 1, 1, 1, 1),
	(5, 'content', '单页内容', 7, 'wysiwyg', 10000, '', 0, 0, 'required', '*填写内容', 1, 0, 2, 1),
	(6, 'writer', '作者', 6, 'input', 30, '', 0, 0, '', '', 0, 0, 4, 0),
	(8, 'attr', '属性', 6, 'checkbox', 10, '0=推荐|1=头条|2=最新', 0, 0, '', '', 0, 0, 10, 1),
	(9, 'post_status', '文章状态', 6, 'radio', 0, '1=发布|2=草稿|3=丢弃', 0, 0, 'required', '', 0, 1, 9, 1),
	(10, 'tags', '关键词', 6, 'input', 30, '', 0, 0, '', '文章TAG', 1, 0, 7, 1),
	(11, 'intro', '简介', 6, 'textarea', 150, '', 0, 0, '', '', 0, 0, 6, 1),
	(12, 'click', '点击次数', 6, 'int', 10, '0', 0, 0, '', '点击次数', 0, 1, 5, 1),
	(14, 'slug', '英文标识', 6, 'input', 30, '', 0, 0, '', '文章标识', 0, 0, 8, 1),
	(33, 'slug', '别名', 7, 'input', 20, '', 0, 0, '', '输入英文唯一别名,URL识别', 0, 1, 3, 1),
	(19, 'title', '标题', 8, 'input', 50, '', 0, 0, 'required', '', 1, 1, 1, 1),
	(20, 'summy', '简介', 8, 'input', 150, '', 400, 0, '', '', 0, 0, 2, 1),
	(21, 'linkword', '链接词', 8, 'input', 20, '', 0, 0, 'required', '', 0, 1, 3, 1),
	(22, 'imgurl', '图片地址', 8, 'file', 200, 'jpg|gif|png|bmp', 0, 0, '', '', 0, 1, 4, 1),
	(23, 'linkurl', '链接地址', 8, 'input', 100, '', 0, 0, 'required', '链接地址', 0, 1, 5, 1),
	(24, 'title', '标题', 9, 'input', 50, '', 0, 0, 'required', '', 0, 1, 1, 1),
	(25, 'file', '视频地址', 9, 'input', 100, '', 0, 0, 'required', '', 0, 1, 2, 1),
	(26, 'thumbnail', '缩略图', 9, 'file', 100, '', 0, 0, '', '', 0, 1, 3, 1),
	(27, 'name', '名称', 10, 'input', 30, '', 0, 0, 'required', '', 1, 1, 1, 1),
	(28, 'url', '链接地址', 10, 'input', 100, '', 0, 0, 'required', '', 1, 1, 2, 1),
	(29, 'website_profile', '网站描述', 10, 'input', 150, '', 300, 0, '', 'ferr', 0, 1, 3, 1),
	(30, 'is_show', '是否显示', 10, 'select', 0, '0=是|1=否', 0, 0, '', 'ffe', 0, 1, 4, 1),
	(31, 'email', '站长邮件', 10, 'input', 50, '', 0, 0, 'valid_email', '', 0, 0, 5, 1),
	(32, 'oicq', '站长QQ', 10, 'input', 20, '', 0, 0, '', '', 0, 0, 6, 1),
	(34, 'keywords', '关键词', 7, 'input', 50, '', 0, 0, '', 'SEO关键词,分割', 0, 1, 7, 1),
	(35, 'post_category', '页面目录', 7, 'select_from_model', 3, 'page_category|cate_name', 0, 0, 'required', '目录选择', 0, 1, 4, 1),
	(36, 'show_area', '显示位置', 7, 'radio', 0, '0=菜单显示|1=底部显示|2=不显示', 0, 0, '', '', 0, 1, 6, 1),
	(37, 'order', '排序', 8, 'int', 1, '0', 0, 0, '', '', 0, 1, 6, 1),
	(38, 'order', '排序', 7, 'int', 1, '0', 0, 0, '', '', 0, 1, 8, 1),
	(40, 't1', 'te', 11, 'input', 20, '', 0, 0, 'required,valid_email', '', 1, 1, NULL, 1);
/*!40000 ALTER TABLE `moly_model_fields` ENABLE KEYS */;


-- 导出  表 molycms.moly_only_alias 结构
CREATE TABLE IF NOT EXISTS `moly_only_alias` (
  `alias` varchar(50) NOT NULL COMMENT '唯一别名 (只能是英文、数字、下划线)',
  `mid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (`alias`),
  KEY `mid_id` (`mid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='唯一别名表';

-- 正在导出表  molycms.moly_only_alias 的数据：0 rows
/*!40000 ALTER TABLE `moly_only_alias` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_only_alias` ENABLE KEYS */;


-- 导出  表 molycms.moly_operationlog 结构
CREATE TABLE IF NOT EXISTS `moly_operationlog` (
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
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='后台操作日志表';

-- 正在导出表  molycms.moly_operationlog 的数据：26 rows
/*!40000 ALTER TABLE `moly_operationlog` DISABLE KEYS */;
INSERT INTO `moly_operationlog` (`id`, `uid`, `dateline`, `ip`, `status`, `info`, `get`) VALUES
	(1, 1, 1419255705, '127.0.0.1', 1, '提示语：添加菜单成功！<br/>控制器：menu,方法：add<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=menu-add'),
	(2, 1, 1419257039, '127.0.0.1', 1, '提示语：编辑成功！<br/>控制器：models,方法：edit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=models-edit-id-1'),
	(3, 1, 1419339660, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.8193509646225721'),
	(4, 1, 1419341953, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.7764097647741437'),
	(5, 1, 1419343482, '127.0.0.1', 0, '提示语：添加失败：所属内容模型不存在！<br/>控制器：models,方法：fadd<br/>请求方式：GET', 'http://www.test.com/molycms/admin/index.php?u=models-dlist-id-1'),
	(6, 1, 1419343590, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.8662618286907673'),
	(7, 1, 1419343829, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.8662618286907673'),
	(8, 1, 1419343855, '127.0.0.1', 1, '提示语：添加成功,ID:6<br/>控制器：models,方法：add<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=models-add'),
	(9, 1, 1419343885, '127.0.0.1', 0, '提示语：编辑失败：模型标识重复<br/>控制器：models,方法：edit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=models-edit-id-6'),
	(10, 1, 1419344056, '127.0.0.1', 1, '提示语：编辑成功！<br/>控制器：models,方法：edit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=models-edit-id-6'),
	(11, 1, 1419345411, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.6451520407572389'),
	(12, 1, 1419345607, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.6451520407572389'),
	(13, 1, 1419345862, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.6451520407572389'),
	(14, 1, 1419768648, '127.0.0.1', 1, '提示语：editor_um停用完成！<br/>控制器：plugin,方法：disabled<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=plugin-index&_=0.414719320833683'),
	(15, 1, 1419771381, '127.0.0.1', 1, '提示语：启用完成！<br/>控制器：plugin,方法：enable<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=plugin-index&_=0.5534242482390255'),
	(16, 1, 1419773808, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.712324645370245'),
	(17, 1, 1419774358, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.4073260778095573'),
	(18, 1, 1419774549, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.587387703359127'),
	(19, 1, 1419775166, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.587387703359127'),
	(20, 1, 1419776234, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.8797157311346382'),
	(21, 1, 1419858441, '127.0.0.1', 1, '提示语：编辑成功！<br/>控制器：models,方法：fedit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=models-fedit-id-25'),
	(22, 1, 1419864627, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.6701798215508461'),
	(23, 1, 1419864980, '127.0.0.1', 1, '提示语：清除缓存完成！<br/>控制器：tool,方法：index<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=tool-index&_=0.6701798215508461'),
	(24, 1, 1419865532, '127.0.0.1', 1, '提示语：编辑菜单成功！<br/>控制器：menu,方法：edit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=menu-edit-cid-26'),
	(25, 1, 1419865546, '127.0.0.1', 1, '提示语：编辑菜单成功！<br/>控制器：menu,方法：edit<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=menu-edit-cid-49'),
	(26, 1, 1419865567, '127.0.0.1', 1, '提示语：添加菜单成功！<br/>控制器：menu,方法：add<br/>请求方式：POST', 'http://www.test.com/molycms/admin/index.php?u=menu-add-upid-4');
/*!40000 ALTER TABLE `moly_operationlog` ENABLE KEYS */;


-- 导出  表 molycms.moly_runtime 结构
CREATE TABLE IF NOT EXISTS `moly_runtime` (
  `k` char(32) NOT NULL DEFAULT '' COMMENT '键名',
  `v` text NOT NULL COMMENT '数据',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='缓存表';

-- 正在导出表  molycms.moly_runtime 的数据：1 rows
/*!40000 ALTER TABLE `moly_runtime` DISABLE KEYS */;
INSERT INTO `moly_runtime` (`k`, `v`, `expiry`) VALUES
	('cfg', '{"webname":"MOLYCMS","webdomain":"www.test.com","webdir":"\\/molycms\\/","webmail":"admin@molycms.com","tongji":"<script type=\\"text\\/javascript\\">var cnzz_protocol = ((\\"https:\\" == document.location.protocol) ? \\" https:\\/\\/\\" : \\" http:\\/\\/\\");document.write(unescape(\\"%3Cspan id=\'cnzz_stat_icon_1253619239\'%3E%3C\\/span%3E%3Cscript src=\'\\" + cnzz_protocol + \\"s95.cnzz.com\\/stat.php%3Fid%3D1253619239\' type=\'text\\/javascript\'%3E%3C\\/script%3E\\"));<\\/script>","beian":"\\u9102ICP\\u590714018391\\u53f7-1","dis_comment":0,"user_comment":0,"comment_filter":"","footer_info":"Power by Molycms \\u7248\\u6743\\u6240\\u6709","seo_title":"\\u7f51\\u7ad9\\u5efa\\u8bbe\\u5229\\u5668\\uff01","seo_keywords":"MOLYCMS","seo_description":"MOLYCMS\\uff0c\\u7f51\\u7ad9\\u5efa\\u8bbe\\u5229\\u5668\\uff01","link_show":"{cate_alias}\\/{id}.html","link_show_type":2,"link_show_end":".html","link_cate_page_pre":"\\/page_","link_cate_page_end":".html","link_cate_end":"\\/","link_tag_pre":"tag\\/","link_tag_end":".html","link_comment_pre":"comment\\/","link_comment_end":".html","link_index_end":".html","up_img_ext":"jpg,jpeg,gif,png","up_img_max_size":"3074","up_file_ext":"zip,gz,rar,iso,xsl,doc,ppt,wps","up_file_max_size":"10240","thumb_article_w":150,"thumb_article_h":150,"thumb_product_w":150,"thumb_product_h":150,"thumb_photo_w":150,"thumb_photo_h":150,"thumb_type":2,"thumb_quality":90,"watermark_pos":9,"watermark_pct":90,"theme":"default","view":"\\/molycms\\/molycms\\/view\\/","webroot":"http:\\/\\/www.test.com","weburl":"http:\\/\\/www.test.com\\/molycms\\/","table_arr":{"1":"page","2":"article","3":"product","4":"photo","5":"art","6":"pages","7":"slidepic","8":"video","9":"link","10":"test1"},"mod_name":{"2":"\\u6587\\u7ae0","3":"\\u4ea7\\u54c1","4":"\\u56fe\\u96c6","5":"\\u6587\\u7ae0","6":"\\u9875\\u9762","7":"\\u6eda\\u52a8\\u56fe\\u7247","8":"\\u89c6\\u9891","9":"\\u94fe\\u63a5","10":"\\u6d4b\\u8bd51"},"cate_arr":{"1":"news","9":"photo2","8":"photo1","7":"photo","6":"product2","5":"product1","4":"product","3":"news2","2":"news1","10":"about"}}', 0);
/*!40000 ALTER TABLE `moly_runtime` ENABLE KEYS */;


-- 导出  表 molycms.moly_slide 结构
CREATE TABLE IF NOT EXISTS `moly_slide` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` mediumint(8) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `url` varchar(150) NOT NULL,
  `remark` mediumtext NOT NULL,
  `listorder` int(3) unsigned NOT NULL DEFAULT '99',
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='幻灯片表';

-- 正在导出表  molycms.moly_slide 的数据：0 rows
/*!40000 ALTER TABLE `moly_slide` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_slide` ENABLE KEYS */;


-- 导出  表 molycms.moly_types 结构
CREATE TABLE IF NOT EXISTS `moly_types` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `class` varchar(20) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `listorder` tinyint(4) unsigned NOT NULL DEFAULT '99',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='模块类型表';

-- 正在导出表  molycms.moly_types 的数据：3 rows
/*!40000 ALTER TABLE `moly_types` DISABLE KEYS */;
INSERT INTO `moly_types` (`id`, `title`, `class`, `remark`, `thumb`, `listorder`, `status`) VALUES
	(1, '默认链接', 'link', '', '', 99, 1),
	(2, '首页幻灯', 'slide', '', '', 99, 1),
	(3, '顶部导航', 'navigation', '', '', 99, 1);
/*!40000 ALTER TABLE `moly_types` ENABLE KEYS */;


-- 导出  表 molycms.moly_user 结构
CREATE TABLE IF NOT EXISTS `moly_user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(16) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(16) NOT NULL DEFAULT '' COMMENT '随机干扰字符，用来混淆密码',
  `groupid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `email` char(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `regip` int(10) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `regdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册日期',
  `loginip` int(10) NOT NULL DEFAULT '0' COMMENT '登陆IP',
  `logindate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆日期',
  `lastip` int(10) NOT NULL DEFAULT '0' COMMENT '上次登陆IP',
  `lastdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上次登陆日期',
  `contents` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容数',
  `comments` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `logins` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登陆数',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '责任编辑,昵称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:1正常，0待审核',
  `isadmin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台用户（0:否，1:是）',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '个人头像',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- 正在导出表  molycms.moly_user 的数据：1 rows
/*!40000 ALTER TABLE `moly_user` DISABLE KEYS */;
INSERT INTO `moly_user` (`uid`, `username`, `password`, `salt`, `groupid`, `email`, `regip`, `regdate`, `loginip`, `logindate`, `lastip`, `lastdate`, `contents`, `comments`, `logins`, `author`, `status`, `isadmin`, `avatar`) VALUES
	(1, 'admin', '8d05355c8c909c4970e8da327485092e', 'DU>VEHDzT2RH0YfA', 1, '', 2130706433, 1419250471, 2130706433, 1419865576, 2130706433, 1419858092, 0, 0, 6, 'admin', 1, 1, '');
/*!40000 ALTER TABLE `moly_user` ENABLE KEYS */;


-- 导出  表 molycms.moly_user_collect 结构
CREATE TABLE IF NOT EXISTS `moly_user_collect` (
  `collect_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '内容模型ID',
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容ID',
  PRIMARY KEY (`collect_id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户收藏表';

-- 正在导出表  molycms.moly_user_collect 的数据：0 rows
/*!40000 ALTER TABLE `moly_user_collect` DISABLE KEYS */;
/*!40000 ALTER TABLE `moly_user_collect` ENABLE KEYS */;


-- 导出  表 molycms.moly_user_data 结构
CREATE TABLE IF NOT EXISTS `moly_user_data` (
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `homepage` varchar(255) NOT NULL DEFAULT '' COMMENT '个人主页',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  `qq` char(11) NOT NULL DEFAULT '' COMMENT 'QQ号',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `tel` varchar(40) NOT NULL DEFAULT '' COMMENT '电话',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户详情表';

-- 正在导出表  molycms.moly_user_data 的数据：1 rows
/*!40000 ALTER TABLE `moly_user_data` DISABLE KEYS */;
INSERT INTO `moly_user_data` (`uid`, `homepage`, `intro`, `qq`, `mobile`, `tel`, `address`) VALUES
	(1, 'http://#', '', '', '', '', '');
/*!40000 ALTER TABLE `moly_user_data` ENABLE KEYS */;


-- 导出  表 molycms.moly_user_group 结构
CREATE TABLE IF NOT EXISTS `moly_user_group` (
  `groupid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` char(20) NOT NULL DEFAULT '' COMMENT '用户组名',
  `system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否由系统定义 (1为系统定义，0为自定义)',
  `isadmin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否后台用户组（0:否，1:是）',
  `purviews` text NOT NULL COMMENT '后台权限 (为空时不限制)',
  PRIMARY KEY (`groupid`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- 正在导出表  molycms.moly_user_group 的数据：9 rows
/*!40000 ALTER TABLE `moly_user_group` DISABLE KEYS */;
INSERT INTO `moly_user_group` (`groupid`, `groupname`, `system`, `isadmin`, `purviews`) VALUES
	(1, '超级管理员', 1, 1, ''),
	(2, '普通管理员', 1, 1, ''),
	(3, '网站编辑', 1, 1, ''),
	(11, '注册用户', 1, 0, ''),
	(12, 'VIP用户', 1, 0, ''),
	(13, '邮箱认证用户', 1, 0, ''),
	(14, '手机认证用户', 1, 0, ''),
	(15, '待验证用户组', 1, 0, ''),
	(16, '禁止用户组', 1, 0, '');
/*!40000 ALTER TABLE `moly_user_group` ENABLE KEYS */;


-- 导出  表 molycms.moly_validations 结构
CREATE TABLE IF NOT EXISTS `moly_validations` (
  `k` varchar(20) NOT NULL,
  `v` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 正在导出表  molycms.moly_validations 的数据：2 rows
/*!40000 ALTER TABLE `moly_validations` DISABLE KEYS */;
INSERT INTO `moly_validations` (`k`, `v`) VALUES
	('required', '必填'),
	('valid_email', 'E-mail格式');
/*!40000 ALTER TABLE `moly_validations` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
