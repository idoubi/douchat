CREATE TABLE IF NOT EXISTS `dc_idou_sign_record` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) NOT NULL COMMENT '用户标识',
  `sign_time` int(10) NOT NULL COMMENT '签到时间',
  `sign_date` int(10) NOT NULL COMMENT '签到日期',
  `total_times` int(5) NOT NULL COMMENT '签到总次数',
  `continue_times` int(5) NOT NULL COMMENT '连续签到次数',
  `score` int(5) NOT NULL COMMENT '所获积分'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;