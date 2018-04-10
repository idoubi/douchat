-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-06-16 20:21:19
-- 服务器版本： 5.6.29-log
-- PHP Version: 5.5.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `douchat`
--

CREATE TABLE `dc_access_key` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `mpid` int(10) NOT NULL DEFAULT '0' COMMENT '账号id',
  `ak` varchar(255) NOT NULL DEFAULT '' COMMENT 'api请求ak',
  `sk` varchar(255) NOT NULL DEFAULT '' COMMENT 'api请求sk',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态。0：停用，1：使用中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='接口授权key表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_addons`
--

CREATE TABLE IF NOT EXISTS `dc_addons` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '插件名称',
  `bzname` varchar(50) NOT NULL COMMENT '标识名',
  `desc` text COMMENT '描述',
  `version` varchar(10) NOT NULL COMMENT '版本号',
  `author` varchar(50) NOT NULL COMMENT '作者姓名',
  `logo` varchar(255) NOT NULL COMMENT 'LOGO',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '插件配置',
  `type` varchar(50) DEFAULT NULL COMMENT '插件分类'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_addons_access`
--

CREATE TABLE IF NOT EXISTS `dc_addons_access` (
  `user_id` int(10) NOT NULL,
  `addon` varchar(50) NOT NULL,
  `mpid` int(10) NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_addon_entry`
--

CREATE TABLE IF NOT EXISTS `dc_addon_entry` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `addon` varchar(50) NOT NULL COMMENT '插件名称',
  `name` varchar(255) DEFAULT NULL COMMENT '入口名称',
  `act` varchar(50) NOT NULL COMMENT '操作',
  `title` varchar(255) NOT NULL COMMENT '封面标题',
  `desc` text COMMENT '封面描述',
  `cover` varchar(255) NOT NULL DEFAULT '0' COMMENT '封面图片'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件功能入口表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_addon_setting`
--

CREATE TABLE IF NOT EXISTS `dc_addon_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `addon` varchar(50) NOT NULL COMMENT '插件标识',
  `name` varchar(50) NOT NULL COMMENT '配置项',
  `value` text NOT NULL COMMENT '配置值',
  `theme` varchar(50) NOT NULL DEFAULT '' COMMENT '主题',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '类别',
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='插件配置参数表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_attach`
--

CREATE TABLE IF NOT EXISTS `dc_attach` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号ID',
  `user_id` int(10) DEFAULT NULL COMMENT '上传者的用户ID',
  `file_name` varchar(255) DEFAULT NULL COMMENT '文件名',
  `file_extension` varchar(10) DEFAULT NULL COMMENT '附件后缀名',
  `file_size` int(10) DEFAULT NULL COMMENT '附件大小',
  `file_path` varchar(255) DEFAULT NULL COMMENT '附件存储位置',
  `hash` varchar(50) DEFAULT NULL COMMENT '哈希',
  `create_time` int(10) DEFAULT NULL COMMENT '附件创建时间',
  `item_type` varchar(50) DEFAULT NULL COMMENT '类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp`
--

CREATE TABLE IF NOT EXISTS `dc_mp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `group_id` varchar(50) DEFAULT NULL COMMENT '可用套餐ID',
  `name` varchar(50) NOT NULL COMMENT '公众号名称',
  `origin_id` varchar(50) NOT NULL COMMENT '公众号原始ID',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '公众号类型（1：普通订阅号；2：认证订阅号；3：普通服务号；4：认证服务号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：禁用，1：正常，2：审核中）',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `valid_token` varchar(40) DEFAULT NULL COMMENT '接口验证Token',
  `token` varchar(50) DEFAULT NULL COMMENT '公众号标识',
  `encodingaeskey` varchar(50) DEFAULT NULL COMMENT '消息加解密秘钥',
  `appid` varchar(50) DEFAULT NULL COMMENT 'AppId',
  `appsecret` varchar(50) DEFAULT NULL COMMENT 'AppSecret',
  `mp_number` varchar(50) DEFAULT NULL COMMENT '微信号',
  `desc` text COMMENT '描述',
  `headimg` varchar(255) DEFAULT NULL COMMENT '头像',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '二维码',
  `login_name` varchar(50) DEFAULT NULL COMMENT '公众号登录名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_auto_reply`
--

CREATE TABLE IF NOT EXISTS `dc_mp_auto_reply` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `type` varchar(50) DEFAULT NULL COMMENT '回复场景',
  `reply_type` varchar(50) DEFAULT NULL COMMENT '回复类型',
  `material_id` int(10) DEFAULT NULL COMMENT '回复素材ID',
  `keyword` varchar(50) DEFAULT NULL COMMENT '绑定的关键词',
  `addon` varchar(50) DEFAULT NULL COMMENT '处理消息的插件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号自动回复表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_fans`
--

CREATE TABLE IF NOT EXISTS `dc_mp_fans` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) NOT NULL COMMENT '粉丝标识',
  `is_subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '关注时间',
  `unsubscribe_time` int(10) DEFAULT NULL COMMENT '取消关注时间',
  `nickname` varchar(50) DEFAULT NULL COMMENT '粉丝昵称',
  `sex` tinyint(1) DEFAULT NULL COMMENT '粉丝性别',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '粉丝头像',
  `relname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `signature` text COMMENT '个性签名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `is_bind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否绑定',
  `language` varchar(50) DEFAULT NULL COMMENT '使用语言',
  `country` varchar(50) DEFAULT NULL COMMENT '国家',
  `province` varchar(50) DEFAULT NULL COMMENT '身份',
  `city` varchar(50) DEFAULT NULL COMMENT '城市',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `groupid` int(10) DEFAULT NULL COMMENT '分组ID',
  `tagid_list` varchar(255) DEFAULT NULL COMMENT '标签',
  `score` int(10) DEFAULT '0' COMMENT '积分',
  `money` int(10) DEFAULT '0' COMMENT '金钱',
  `latitude` varchar(50) DEFAULT NULL COMMENT '纬度',
  `longitude` varchar(50) DEFAULT NULL COMMENT '经度',
  `location_precision` varchar(50) DEFAULT NULL COMMENT '精度'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号粉丝表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_group`
--

CREATE TABLE IF NOT EXISTS `dc_mp_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '套餐名称',
  `addons` text COMMENT '可管理的插件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号套餐表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_material`
--

CREATE TABLE IF NOT EXISTS `dc_mp_material` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `type` varchar(50) DEFAULT NULL COMMENT '素材类型',
  `content` text COMMENT '文本素材内容',
  `image` varchar(255) DEFAULT NULL COMMENT '图片素材路径',
  `title` varchar(255) DEFAULT NULL COMMENT '图文素材标题',
  `picurl` varchar(255) DEFAULT NULL COMMENT '图文素材封面',
  `url` varchar(255) DEFAULT NULL COMMENT '图文链接',
  `description` text COMMENT '图文素材描述',
  `detail` text COMMENT '图文素材详情',
  `create_time` int(10) DEFAULT NULL COMMENT '素材创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号素材表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_message`
--

CREATE TABLE IF NOT EXISTS `dc_mp_message` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(50) NOT NULL COMMENT '用户标识',
  `msgid` varchar(50) DEFAULT NULL COMMENT '消息ID',
  `msgtype` varchar(10) NOT NULL COMMENT '消息类型',
  `content` text COMMENT '消息内容',
  `create_time` int(10) NOT NULL COMMENT '消息发送时间',
  `picurl` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `mediaid` varchar(255) DEFAULT NULL COMMENT '媒体ID',
  `format` varchar(50) DEFAULT NULL COMMENT '语音格式',
  `recognition` text COMMENT '语音识别结果',
  `thumb_mediaid` varchar(255) DEFAULT NULL COMMENT '视频消息缩略图ID',
  `location_x` float DEFAULT NULL COMMENT '地理位置纬度',
  `location_y` float DEFAULT NULL COMMENT '地理位置精度',
  `scale` int(5) DEFAULT NULL COMMENT '地图缩放大小',
  `label` varchar(50) DEFAULT NULL COMMENT '地理位置信息',
  `title` varchar(255) DEFAULT NULL COMMENT '链接消息标题',
  `description` varchar(255) DEFAULT NULL COMMENT '链接消息描述',
  `url` varchar(255) DEFAULT NULL COMMENT '链接消息地址',
  `reply_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '回复状态',
  `save_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '保存为素材状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_payment`
--

CREATE TABLE IF NOT EXISTS `dc_mp_payment` (
  `id` int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `orderid` varchar(255) DEFAULT NULL COMMENT '订单号',
  `create_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `detail` text COMMENT '支付详情'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号支付配置';
-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_rule`
--

CREATE TABLE IF NOT EXISTS `dc_mp_rule` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `addon` varchar(50) DEFAULT NULL COMMENT '插件标识',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词内容',
  `type` varchar(50) DEFAULT NULL COMMENT '触发类型',
  `entry_id` int(10) DEFAULT NULL COMMENT '功能入口ID',
  `reply_id` int(10) DEFAULT NULL COMMENT '自动回复ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号响应规则';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_score_record`
--

CREATE TABLE IF NOT EXISTS `dc_mp_score_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号标识',
  `openid` varchar(255) NOT NULL COMMENT '粉丝openid',
  `type` varchar(50) DEFAULT 'score' COMMENT '积分类型，socre、money等',
  `source` varchar(50) DEFAULT 'system' COMMENT '积分来源，system，addon',
  `value` int(10) NOT NULL COMMENT '积分值',
  `flag` varchar(50) DEFAULT NULL COMMENT '标识，fans_bind，IdouChat',
  `remark` text COMMENT '积分说明'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分记录表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_mp_setting`
--

CREATE TABLE IF NOT EXISTS `dc_mp_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `mpid` int(10) NOT NULL COMMENT '公众号ID',
  `name` varchar(255) NOT NULL COMMENT '配置项',
  `value` text COMMENT '配置值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号配置';

-- --------------------------------------------------------

--
-- 表的结构 `dc_rbac_access`
--

CREATE TABLE IF NOT EXISTS `dc_rbac_access` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '开启状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_rbac_mp_access`
--

CREATE TABLE IF NOT EXISTS `dc_rbac_mp_access` (
  `role_id` int(10) NOT NULL COMMENT '角色ID',
  `mp_groups` varchar(255) DEFAULT NULL COMMENT '可使用的公众号套餐',
  `mp_count` int(5) DEFAULT NULL COMMENT '可创建公众号数',
  `register_invite_count` int(10) DEFAULT NULL COMMENT '注册邀请数',
  `addons` varchar(255) DEFAULT NULL COMMENT '插件权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公众号权限表';

-- --------------------------------------------------------

--
-- 表的结构 `dc_rbac_node`
--

CREATE TABLE IF NOT EXISTS `dc_rbac_node` (
  `id` smallint(6) unsigned AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `remark` varchar(255) DEFAULT NULL,
  `sort` smallint(6) unsigned DEFAULT NULL,
  `pid` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_rbac_role`
--

CREATE TABLE IF NOT EXISTS `dc_rbac_role` (
  `id` smallint(6) unsigned AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `name` varchar(20) NOT NULL,
  `pid` smallint(6) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL COMMENT '角色类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 转存表中的数据 `dc_rbac_role`
--

INSERT INTO `dc_rbac_role` (`id`, `name`, `pid`, `status`, `remark`, `type`) VALUES
(1, '超级管理员', 0, 1, '拥有系统管理和公众号管理权限', 'system_manager'),
(2, '系统管理员', 0, 1, '拥有系统后台管理权限', 'admin_manager'),
(3, '公众号管理员', 0, 1, '拥有公众号管理权限', 'mp_manager') 
ON DUPLICATE KEY UPDATE name=VALUES(`name`);

--
-- 表的结构 `dc_rbac_role_user`
--

CREATE TABLE IF NOT EXISTS `dc_rbac_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  UNIQUE INDEX `role_user_key` (`role_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_scene_qrcode`
--
CREATE TABLE IF NOT EXISTS `dc_scene_qrcode` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_type` char(10) DEFAULT '0' COMMENT '二维码类型',
  `scene_id` int(32) DEFAULT NULL COMMENT '场景值ID',
  `scene_str` varchar(255) DEFAULT NULL COMMENT '场景值字符串',
  `expire` int(10) DEFAULT NULL COMMENT '过期时间',
  `ticket` varchar(255) DEFAULT NULL COMMENT '二维码Ticket',
  `url` varchar(255) DEFAULT NULL COMMENT '二维码图片解析后的地址',
  `ctime` int(10) DEFAULT NULL COMMENT '二维码创建时间',
  `short_url` varchar(255) DEFAULT NULL COMMENT '二维码短地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_scene_qrcode`
--

CREATE TABLE IF NOT EXISTS `dc_scene_qrcode_statistics` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '扫码者openid',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_id` varchar(255) DEFAULT NULL COMMENT '场景ID/场景字符串',
  `scan_type` varchar(255) DEFAULT NULL COMMENT '扫描类型',
  `ctime` int(10) DEFAULT NULL COMMENT '扫描时间',
  `qrcode_id` int(10) DEFAULT NULL COMMENT '二维码ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dc_scene_qrcode`
--

CREATE TABLE IF NOT EXISTS `dc_scene_qrcode` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_type` char(10) DEFAULT '0' COMMENT '二维码类型',
  `scene_id` int(32) DEFAULT NULL COMMENT '场景值ID',
  `scene_str` varchar(255) DEFAULT NULL COMMENT '场景值字符串',
  `expire` int(10) DEFAULT NULL COMMENT '过期时间',
  `ticket` varchar(255) DEFAULT NULL COMMENT '二维码Ticket',
  `url` varchar(255) DEFAULT NULL COMMENT '二维码图片解析后的地址',
  `ctime` int(10) DEFAULT NULL COMMENT '二维码创建时间',
  `short_url` varchar(255) DEFAULT NULL COMMENT '二维码短地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- 表的结构 `dc_qrcode_statistics`
--

CREATE TABLE IF NOT EXISTS `dc_scene_qrcode_statistics` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT '主键',
  `mpid` int(10) DEFAULT NULL COMMENT '公众号标识',
  `openid` varchar(255) DEFAULT NULL COMMENT '扫码者openid',
  `scene_name` varchar(255) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关联关键词',
  `scene_id` varchar(255) DEFAULT NULL COMMENT '场景ID/场景字符串',
  `scan_type` varchar(255) DEFAULT NULL COMMENT '扫描类型',
  `ctime` int(10) DEFAULT NULL COMMENT '扫描时间',
  `qrcode_id` int(10) DEFAULT NULL COMMENT '二维码ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- 表的结构 `dc_system_setting`
--

CREATE TABLE IF NOT EXISTS `dc_system_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `name` varchar(255) NOT NULL COMMENT '配置项',
  `value` text DEFAULT NULL COMMENT '配置值',
  `type` varchar(50) DEFAULT NULL COMMENT '配置类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='全局配置表';

-- --------------------------------------------------------

--
-- 转存表中的数据 `dc_system_setting`
--

INSERT INTO `dc_system_setting` (`id`, `name`, `value`) VALUES
(1, 'site_name', ''),
(2, 'site_intro', ''),
(3, 'site_keywords', ''),
(4, 'site_copyright', ''),
(5, 'site_icp_beian', ''),
(6, 'register_on', '0'),
(7, 'register_default_role_id', '3') 
ON DUPLICATE KEY UPDATE value=VALUES(`value`);
--
-- 表的结构 `dc_user`
--

CREATE TABLE IF NOT EXISTS `dc_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT '自增ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `headimg` varchar(255) DEFAULT NULL COMMENT '头像',
  `default_mpid` int(10) DEFAULT NULL COMMENT '默认管理的公众号ID',
  `email` varchar(255) DEFAULT NULL COMMENT '用户邮箱',
  `group_id` int(11) DEFAULT NULL COMMENT '用户组',
  `register_time` int(10) NOT NULL COMMENT '注册时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

-- --------------------------------------------------------

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;