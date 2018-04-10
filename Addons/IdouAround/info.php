<?php

return array(
	'name' => '周边查询',
	'bzname' => 'IdouAround',
	'desc' => '发送地理位置，可以查询周边的酒店、餐馆、KTV等信息。',
	'version' => '0.4.0',
	'type' => '1',
	'author' => '艾逗笔',
	'logo' => 'logo.png',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'setting_list' => array(
			'default_search_keyword' => array(
				'title' => '默认查询关键词',
				'type' => 'text',
				'placeholder' => '餐馆',
				'tip' => '例如此处填写的是餐馆，当用户发送位置消息时，默认查询附近的餐馆信息'
			)
		),
		'entry' => 0,
		'menu' => 0
	)
);

?>