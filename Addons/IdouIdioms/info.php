<?php

return array(
	'name' => '成语接龙',
	'bzname' => 'IdouIdioms',
	'desc' => '调用API实现微信端成语接龙',
	'version' => '0.4.0',
	'type' => '1',
	'author' => '艾逗笔',
	'logo' => 'logo.jpg',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'setting_list' => array(
			'begin_text' => array(
				'title' => '进入成语接龙时提示',
				'type' => 'text',
				'placeholder' => '请输入一个成语，比如：一马当先'
			),
			'end_text' => array(
				'title' => '退出成语接龙时提示',
				'type' => 'text',
				'placeholder' => '你已退出成语接龙模式，再次回复【成语接龙】即可进入~'
			),
			'end_keyword' => array(
				'title' => '退出成语接龙关键词',
				'type' => 'text',
				'placeholder' => '退出'
			)
		),
		'entry' => 0,
		'menu' => 0
	),
);

?>