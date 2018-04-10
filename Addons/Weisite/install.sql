CREATE TABLE IF NOT EXISTS `dc_weisite_slider` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `sort` int(10) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_weisite_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` int(10) NOT NULL DEFAULT '0',
  `mpid` int(10) NOT NULL,
  `title` varchar(50) NOT NULL COMMENT '分类标题',
  `intro` text NOT NULL COMMENT '分类简介',
  `sort` int(10) NOT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图标',
  `index_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在首页展示',
  `index_show_count` tinyint(2) NOT NULL DEFAULT '5' COMMENT '在首页展示的内容数目',
  `index_show_title_style` varchar(50) DEFAULT '' COMMENT '首页展示的标题样式',
  `index_show_content_style` varchar(50) DEFAULT '' COMMENT '首页展示的内容样式',
  `cate_show_sub` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分类页是否展示子分类',
  `cate_show_style` varchar(50) DEFAULT '' COMMENT '分类页展示样式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_weisite_article` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mpid` int(10) NOT NULL,
  `cate_id` int(10) NOT NULL COMMENT '分类',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text NOT NULL COMMENT '简介',
  `content` text NOT NULL COMMENT '内容',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_weisite_page` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mpid` int(10) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text NOT NULL COMMENT '简介',
  `content` text NOT NULL COMMENT '内容',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_weisite_navigation` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mpid` int(10) NOT NULL,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text NOT NULL COMMENT '简介',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '导航类型',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '跳转地址',
  `selected_icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_weisite_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `theme` varchar(50) NOT NULL COMMENT '插件标识',
  `name` varchar(50) NOT NULL COMMENT '配置项',
  `value` text NOT NULL COMMENT '配置值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件配置参数表';