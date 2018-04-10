<?php

return array(
	'name' => '意见反馈',
	'bzname' => 'IdouFeedback',
	'desc' => '微信端意见反馈功能',
	'version' => '0.4.0',
	'type' => '1',
	'author' => '艾逗笔',
	'logo' => 'logo.jpg',
	'config' => array(
		'respond_rule' => 0,
		'setting' => 1,
		'setting_list' => array(
			'top_title' => array(
				'title' => '浏览器标题',
				'type' => 'text',
				'placeholder' => '意见反馈'
			),
			'page_title' => array(
				'title' => '页面标题',
				'type' => 'text',
				'placeholder' => '意见反馈'
			),
			'need_name' => array(
				'title' => '是否需要填写姓名',
				'type' => 'radio',
				'options' => array(
					0 => '不需要',
					1 => '需要'
				)
			),
			'need_contact' => array(
				'title' => '是否需要填写联系方式',
				'type' => 'radio',
				'options' => array(
					0 => '不需要',
					1 => '需要'
				)
			),
			'contact_type' => array(
				'title' => '需要填写的联系方式',
				'type' => 'radio',
				'options' => array(
					0 => '手机号',
					1 => 'QQ号',
					2 => '微信号',
					3 => '邮箱'
				),
				'tip' => '开启需要填写联系方式后此选项才起作用'
			)
		),
		'entry' => 1,
		'entry_list' => array(
			'index' => '反馈入口'
		),
		'menu' => 1,
		'menu_list' => array(
			'lists' => '反馈列表'
		)
	),
	'install_sql' => 'install.sql'
);

?>