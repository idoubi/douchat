CREATE TABLE IF NOT EXISTS `dc_idou_weisite_category`  (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `wid` int(10) NOT NULL COMMENT '微站id',
  `title` varchar(50) NOT NULL COMMENT '分类标题',
  `icon` varchar(255) NULL COMMENT '分类图片',
  `url` varchar(255) NULL COMMENT '外链',
  `is_show` tinyint(1) DEFAULT 1 COMMENT '是否显示',
  `sort` int(10) DEFAULT 0 COMMENT '排序',
  `pid` int(10) DEFAULT 0 COMMENT '上级分类',
  `description` text NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微网站_分类表';

-- ----------------------------

CREATE TABLE IF NOT EXISTS `dc_idou_weisite_cms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `wid` int(10) NOT NULL COMMENT '微站id',
  `keyword` varchar(50) NULL COMMENT '关键词',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text NULL COMMENT '简介',
  `cate_id` int(10) unsigned DEFAULT 0 COMMENT '所属分类',
  `cover` varchar(255) NULL COMMENT '封面图片',
  `content` longtext NULL COMMENT '内容',
  `url` varchar(255) NULL COMMENT '外链地址',
  `ctime` int(10) NULL COMMENT '发布时间',
  `sort` int(10) unsigned DEFAULT 0 COMMENT '排序',
  `view_count` int(10) unsigned DEFAULT 0 COMMENT '浏览数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微网站_文章列表';

-- ----------------------------

CREATE TABLE `dc_idou_weisite_page` (
 `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 `mpid` int(10) NOT NULL COMMENT '公众号ID',
 `wid` int(10) NOT NULL COMMENT '站点ID',
 `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级页面ID',
 `title` varchar(255) NOT NULL COMMENT '标题',
 `content` text COMMENT '内容',
 `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
 `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微网站页面表'


CREATE TABLE IF NOT EXISTS `dc_idou_weisite_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `wid` int(10) NOT NULL COMMENT '微站id',
  `url` varchar(255) NULL COMMENT '关联URL',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `pid` int(10) DEFAULT 0 COMMENT '上级菜单',
  `sort` int(10) DEFAULT 0 COMMENT '排序',
  `icon` varchar(255) NULL COMMENT '图标',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微网站_菜单导航表';

-- ----------------------------

CREATE TABLE IF NOT EXISTS `dc_idou_weisite_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `title` varchar(50) NOT NULL COMMENT '微站标题',
  `description` text NULL COMMENT '微站描述',
  `cover` varchar(255) NULL COMMENT '封面图片',
  `index_temp` varchar(255) NULL COMMENT '首页模板',
  `sub_temp` varchar(255) NULL COMMENT '二级页面模板',
  `list_temp` varchar(255) NULL COMMENT '图文列表页模板',
  `cont_temp` varchar(255) NULL COMMENT '图文内容页模板',
  `nav_temp` varchar(255) NULL COMMENT '菜单导航模板',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------

CREATE TABLE IF NOT EXISTS `dc_idou_weisite_slideshow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `wid` int(10) NOT NULL COMMENT '微站id',
  `title` varchar(255) NULL COMMENT '标题',
  `img` varchar(255) NOT NULL COMMENT '图片',
  `url` varchar(255) NULL COMMENT '链接地址',
  `is_show` tinyint(1) DEFAULT 1 COMMENT '是否显示',
  `sort` int(10) unsigned DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微网站_轮播图片表';

