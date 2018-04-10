CREATE TABLE IF NOT EXISTS `dc_pinche_appointment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `iid` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `surplus` tinyint(4) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `iid` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `content` text NOT NULL,
  `img` text NOT NULL,
  `zan` int(11) NOT NULL DEFAULT '0',
  `reply` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_dynamic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `img` text,
  `time` int(11) DEFAULT NULL,
  `zan` int(11) DEFAULT '0',
  `uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_fav` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `iid` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `departure` varchar(1000) DEFAULT NULL,
  `destination` varchar(1000) DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `remark` text,
  `surplus` tinyint(4) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `vehicle` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `see` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT NULL,
  `addtime` int(11) NOT NULL,
  `mpid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `time` int(11) NOT NULL,
  `see` tinyint(4) NOT NULL DEFAULT '0',
  `type` varchar(50) DEFAULT NULL,
  `url` varchar(100) NOT NULL,
  `fid` int(11) NOT NULL DEFAULT '10000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mpid` int(10) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `dc_pinche_zan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `mpid` int(10) NOT NULL,
  `openid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;