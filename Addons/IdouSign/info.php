<?php

return array(
	'name' => '微签到',
	'bzname' => 'IdouSign',
	'desc' => '基础的微信签到功能',
	'version' => '0.4.0',
	'type' => '1',
	'author' => '艾逗笔',
	'logo' => 'logo.jpg',
	'config' => array(
		'respond_rule' => 1,
		'setting' => 1,
		'setting_list' => array(
			'per_score' => array(
				'title' => '每次签到获得积分',
				'type' => 'number',
				'placeholder' => 0,
				'tip' => '用户每次签到获得的积分'
			),
			'sign_success_tip' => array(
				'title' => '签到成功提示信息',
				'type' => 'textarea',
				'placeholder' => '签到成功，你获得{score}积分',
				'tip' => '用户签到成功的提示信息，可使用以下变量：<br>
						{score}：本次签到获得积分<br>
						{nickname}：用户昵称<br>
						{total_times}：累积已签到次数<br>
						{continue_times}：连续签到次数'
			)
		),
		'entry' => 0,
		'menu' => 1,
		'menu_list' => array(
			'record' => '签到记录'
		),
		'index_url' => U('/addon/IdouSign/Web/record')
	),
	'install_sql' => 'install.sql'
);

?>