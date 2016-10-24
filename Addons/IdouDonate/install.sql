CREATE TABLE IF NOT EXISTS `dc_idou_donate_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `money` float(10,2) DEFAULT NULL COMMENT '捐赠额',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名',
  `pay_status` int(1) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `create_time` int(10) DEFAULT NULL COMMENT '捐赠时间',
  `content` text COMMENT '留言内容',
  `is_show` tinyint(1) DEFAULT NULL COMMENT '是否显示',
  `orderid` varchar(50) DEFAULT NULL COMMENT '订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微捐赠插件捐赠列表';