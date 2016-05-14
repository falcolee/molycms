INSERT INTO `pre_user_group` (`groupid`, `groupname`, `system`,`isadmin`, `purviews`) VALUES
(1, '超级管理员', 1,1, ''),
(2, '普通管理员', 1,1, ''),
(3, '网站编辑', 1,1, ''),
(11, '注册用户', 1,0, ''),
(12, 'VIP用户', 1,0, ''),
(13, '邮箱认证用户', 1,0, ''),
(14, '手机认证用户', 1,0, ''),
(15, '待验证用户组', 1,0, ''),
(16, '禁止用户组', 1,0, '');

INSERT INTO `pre_models` (`mid`, `name`, `tablename`, `index_tpl`, `cate_tpl`, `show_tpl`, `system`) VALUES
(1, '单页', 'page', '', 'page_show.htm', '', 1),
(2, '文章', 'article', 'article_index.htm', 'article_list.htm', 'article_show.htm', 1),
(3, '产品', 'product', 'product_index.htm', 'product_list.htm', 'product_show.htm', 1),
(4, '图集', 'photo', 'photo_index.htm', 'photo_list.htm', 'photo_show.htm', 1);

INSERT INTO `pre_kv` (`k`, `v`, `expiry`) VALUES
('link_keywords', '["tag","tag_top","comment","index","sitemap","admin","user","space","static","molycms"]', 0);

INSERT INTO `pre_menu_admin` (`cid`, `upid`, `title`, `controller`, `action`, `param`, `content`, `status`, `type`, `listorder`, `system`, `favorite`) VALUES
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
	(26, 4, '模型管理', 'models', 'index', '', '', 1, 1, 0, 1, 0),
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
	(48, 6, '友情链接', 'link', 'index', '', '', 1, 1, 6, 1, 0);

INSERT INTO `pre_category` VALUES(1, 2, 1, 0, '新闻中心', 'news', '', 'article_index.htm', 'article_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(2, 2, 0, 1, '公司新闻', 'news1', '', 'article_list.htm', 'article_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(3, 2, 0, 1, '行业新闻', 'news2', '', 'article_list.htm', 'article_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(4, 3, 1, 0, '产品中心', 'product', '', 'product_index.htm', 'product_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(5, 3, 0, 4, '手机', 'product1', '', 'product_list.htm', 'product_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(6, 3, 0, 4, '灯饰', 'product2', '', 'product_list.htm', 'product_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(7, 4, 1, 0, '图集中心', 'photo', '', 'photo_index.htm', 'photo_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(8, 4, 0, 7, '美女', 'photo1', '', 'photo_list.htm', 'photo_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(9, 4, 0, 7, '酷车', 'photo2', '', 'photo_list.htm', 'photo_show.htm', 0, 0, '', '', '',0);
INSERT INTO `pre_category` VALUES(10, 1, 0, 0, '关于我们', 'about', '', 'page_show.htm', '', 0, 0, '', '', '',0);

INSERT INTO `pre_cms_page` VALUES(10,'关于我们');

INSERT INTO `pre_kv` VALUES('navigate_3', '{"1":{"cid":1,"alias":"news","name":"\\u65b0\\u95fb\\u4e2d\\u5fc3","url":1,"target":"_self"},"2":{"cid":4,"alias":"product","name":"\\u4ea7\\u54c1\\u4e2d\\u5fc3","url":4,"target":"_self"},"3":{"cid":7,"alias":"photo","name":"\\u56fe\\u96c6","url":7,"target":"_self"},"4":{"cid":10,"alias":"about","name":"\\u5173\\u4e8e\\u6211\\u4eec","url":10,"target":"_self"},"5":{"cid":0,"alias":"","name":"BAIDU","url":"http:\\/\\/www.baidu.com","target":"_blank"}}', 0);

INSERT INTO `pre_types` (`id`, `title`, `class`, `remark`, `thumb`, `listorder`, `status`) VALUES
	(1, '默认链接', 'link', '', '', 99, 1),
	(2, '首页幻灯', 'slide', '', '', 99, 1),
	(3, '顶部导航', 'navigation', '', '', 99, 1);