<?php

/**
 * 插件信息文件
 * @author 艾逗笔<http://idoubi.cc>
 */
return [
	'name' => '微站',
	'bzname' => 'Weisite',
	'version' => '0.4.0',
	'author' => '艾逗笔',
    'logo' => 'logo.png',
	'desc' => '微站小程序，快速生成行业模板',
	'type' => '2',      // 仅支持微信小程序
	'config' => [
		'setting' => 1,
		'setting_list' => [
			[
				'name' => 'title',
				'title' => '站点标题',
				'type' => 'text',
				'placeholder' => '',
				'group' => 'site',
			],
			[
				'name' => 'description',
				'title' => '站点简介',
				'type' => 'textarea',
				'group' => 'site',
			],
            [
                'name' => 'copyright',
                'title' => '版权信息',
                'type' => 'text',
                'group' => 'site'
            ],
            [
                'name' => 'theme_color',
                'title' => '主题颜色',
                'type' => 'text',
                'tip' => '全站主题色，填写#ffffff格式的颜色值',
                'group' => 'site'
            ],
            [
                'name' => 'slider_is_show',
                'title' => '是否显示幻灯片',
                'type' => 'radio',
                'options' => [
                    0 => '不显示',
                    1 => '显示'
                ],
                'value' => 1,
                'tip' => '如果开启，将会显示幻灯片管理里面添加的内容',
                'group' => 'index_show'
            ],
            [
                'name' => 'slider_count',
                'title' => '幻灯片显示数目',
                'type' => 'number',
                'value' => 3,
                'tip' => '最多显示的幻灯片数目',
                'group' => 'index_show'
            ],
            [
                'name' => 'slider_height',
                'title' => '幻灯片高度',
                'type' => 'number',
                'value' => 250,
                'tip' => 'rpx值，默认250',
                'group' => 'index_show'
            ],
            [
                'name' => 'notice_is_show',
                'title' => '是否显示网站公告',
                'type' => 'radio',
                'options' => [
                    0 => '不显示',
                    1 => '显示'
                ],
                'value' => 0,
                'group' => 'index_show'
            ],
            [
                'name' => 'notice_style',
                'title' => '公告样式',
                'type' => 'radio',
                'options' => [
                    'style_1' => '样式一'
                ],
                'value' => 'style_1',
                'group' => 'index_show'
            ],
            [
                'name' => 'notice_icon',
                'title' => '公告小喇叭图标',
                'type' => 'image',
                'group' => 'index_show'
            ],
            [
                'name' => 'notice_content',
                'title' => '公告内容',
                'type' => 'textarea',
                'group' => 'index_show'
            ],
            [
                'name' => 'nav_is_show',
                'title' => '是否显示导航',
                'type' => 'radio',
                'options' => [
                    0 => '不显示',
                    1 => '显示'
                ],
                'value' => 1,
                'tip' => '如果开启，将会显示导航管理里面添加的内容',
                'group' => 'index_show'
            ],
            [
                'name' => 'nav_count',
                'title' => '导航显示数目',
                'type' => 'number',
                'value' => 4,
                'tip' => '最多显示的导航数目，默认显示4个',
                'group' => 'index_show'
            ],
            [
                'name' => 'nav_style',
                'title' => '导航样式',
                'type' => 'radio',
                'options' => [
                    'style_1' => '样式一',
                    'style_2' => '样式二'
                ],
                'value' => 'style_1',
                'group' => 'index_show'
            ],
            [
                'name' => 'category_is_show',
                'title' => '是否显示分类',
                'type' => 'radio',
                'options' => [
                    0 => '不显示',
                    1 => '显示'
                ],
                'value' => 0,
                'tip' => '如果开启，将会显示分类管理里面添加到首页展示的内容',
                'group' => 'index_show'
            ],
            [
                'name' => 'is_show',
                'title' => '是否显示底部导航',
                'type' => 'radio',
                'options' => [
                    0 => '不显示',
                    1 => '显示'
                ],
                'value' => 0,
                'tip' => '如果开启，将使用导航管理处配置的内容在小程序底部进行展示',
                'group' => 'tabbar'
            ],
            [
                'name' => 'font_color',
                'title' => '未选中状态下文字颜色值',
                'type' => 'text',
                'tip' => '填写#ffffff格式的颜色值',
                'group' => 'tabbar'
            ],
            [
                'name' => 'selected_font_color',
                'title' => '选中状态下文字颜色',
                'type' => 'text',
                'tip' => '填写#ffffff格式的颜色值',
                'group' => 'tabbar'
            ],
            [
                'name' => 'backgroud_color',
                'title' => '菜单背景颜色',
                'type' => 'text',
                'tip' => '填写#ffffff格式的颜色值',
                'group' => 'tabbar'
            ],
            [
                'name' => 'border_color',
                'title' => '菜单上边框颜色',
                'type' => 'text',
                'tip' => '填写#ffffff格式的颜色值',
                'group' => 'tabbar'
            ]
		],
		'setting_list_group' => [
			'site' => [
				'name' => 'site',
				'title' => '站点设置',
				'is_show' => 1
			],
            'index_show' => [
                'name' => 'index_show',
                'title' => '首页展示设置',
                'is_show' => 1
            ],
            'tabbar' => [
                'name' => 'tabbar',
                'title' => '底部导航设置',
                'is_show' => 1
            ]
		],
		'menu' => 1,
		'menu_list' => [
			'sliderList' => '幻灯片管理'
		],
        'index_url' => U('/addon/Weisite/web/setting')
	],
    'install_sql' => 'install.sql'
];