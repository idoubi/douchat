<?php 

return array(
	'name' => '聊天机器人',
	'bzname' => 'IdouChat',
	'desc' => '微信智能聊天机器人插件，可在微信端开启机器人聊天模式',
	'type' => '1',
	'version' => '0.4.0',
	'author' => '艾逗笔',
	'logo' => 'logo.jpg',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'setting_list' => array(
			'can_voice' => array(
				'title' => '是否开启语音聊天',
				'type' => 'radio',
				'options' => array(
					0 => '不开启',
					1 => '开启'
				),
				'value' => 0,
				'tip' => '开启语音聊天，需要在微信后台开启语音识别功能'
			),
			'api_url' => array(
				'title' => '图灵API地址',
				'type' => 'text',
				'placeholder' => 'http://www.tuling123.com/openapi/api',
				'value' => '',
				'tip' => ''
			),
			'api_key' => array(
				'title' => '图灵API KEY',
				'type' => 'text',
				'placeholder' => '5b6d54d86d958fe4fabb67883903dbe9',
				'value' => '',
				'tip' => '<a href="http://www.tuling123.com/web/robot_access!index.action?cur=l_05" target="_blank">前往图灵机器人官网申请API</a>'
			),
			'enter_tip' => array(
				'title' => '进入聊天提示语',
				'type' => 'textarea',
				'placeholder' => '你想聊点什么呢',
				'value' => '',
				'tip' => '用户发送关键词进入机器人聊天模式时回复给用户的内容'
			),
			'keep_time' => array(
				'title' => '会话保持时间',
				'type' => 'text',
				'placeholder' => '300',
				'value' => '',
				'tip' => '在此时间范围内，用户一直处在机器人聊天模式中，默认300秒（5分钟）'
			),
			'exit_keyword' => array(
				'title' => '退出聊天关键词',
				'type' => 'text',
				'placeholder' => '退出',
				'value' => '',
				'tip' => '用户发送此关键词主动退出机器人聊天模式'
			),
			'exit_tip' => array(
				'title' => '退出聊天提示语',
				'type' => 'textarea',
				'placeholder' => '下次无聊的时候可以再找我聊天哦',
				'value' => '',
				'tip' => '用户退出机器人聊天模式时回复给用户的内容'
			)
		)
	)
);

?>