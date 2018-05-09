<?php

return array(
	'name' => '示例插件',
	'bzname' => 'Demo',
	'type' => '1,2',
	'desc' => '豆信核心功能示例，包含小程序接口请求逻辑',
	'version' => '0.4.0',
	'author' => '艾逗笔',
    'logo' => 'logo.png',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'entry' => 1,
		'menu' => 1,
		'menu_list' => [
			'diaryList' => '日记管理'
		],
		'setting_list_group' => [
			'basic' => [
				'title' => '基本设置',
				'is_show' => 1
			]
		],
		'setting_list' => [
			'title' => [
				'title' => '标题',
				'tip' => '在微信浏览器或小程序顶栏显示的标题',
				'type' => 'text',
				'placeholder' => '豆信示例',
				'group' => 'basic'
			],
			'copyright' => [
				'title' => '版权信息',
				'type' => 'text',
				'value' => 'Copyright © 2015-2018 豆信',
				'group' => 'basic'
			],
			'about' => [
				'title' => '关于',
				'tip' => '关于程序的介绍',
				'type' => 'textarea',
				'placeholder' => '豆信是一个简洁、高效、优雅的微信开发框架，学习交流请加QQ群：473027882',
				'group' => 'basic'
			]
		]
	),
	'install_sql' => 'install.sql'
);

?>