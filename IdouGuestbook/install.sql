CREATE TABLE IF NOT EXISTS `dc_idou_guestbook_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `nickname` varchar(50) NOT NULL COMMENT '用户昵称',
  `content` text NOT NULL COMMENT '留言内容',
  `create_time` int(10) NOT NULL COMMENT '留言时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '留言状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言板留言表';