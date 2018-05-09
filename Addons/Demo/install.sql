CREATE TABLE IF NOT EXISTS `dc_demo_diary` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mpid` int(10) NOT NULL DEFAULT '0' COMMENT '账号ID',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户标识',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '日记标题',
  `content` text NOT NULL COMMENT '建议内容',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `deleted_at` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;