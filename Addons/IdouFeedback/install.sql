CREATE TABLE `dc_idou_feedback` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `mpid` int(10) NOT NULL COMMENT '公众号标识',
 `openid` varchar(255) NOT NULL COMMENT '用户标识',
 `name` varchar(255) DEFAULT NULL COMMENT '反馈者姓名',
 `contact` varchar(255) NOT NULL COMMENT '联系方式内容',
 `contact_type` tinyint(1) DEFAULT NULL COMMENT '联系方式类型',
 `content` text COMMENT '反馈内容',
 `create_time` int(10) NOT NULL COMMENT '反馈时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈表';