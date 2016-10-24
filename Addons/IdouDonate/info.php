<?php 

return array(
    'name' => '微捐赠',
    'bzname' => 'IdouDonate',
    'desc' => '微信捐赠插件',
    'type' => 'customer',
    'version' => '1.0',
    'author' => '艾逗笔',
    'logo' => 'logo.gif',
    'config' => array(
        'setting' => 1,
        'entry' => 1,
        'menu' => 1,
        'entry_list' => array(
            'index' => '捐赠入口',
            'donate_list' => '捐赠列表'
        ),
        'menu_list' => array(
            'donations' => '捐赠管理'
        ),
        'setting_list' => array(
            'money' => array(
                'title' => '捐赠额设置',
                'type' => 'textarea',
                'placeholder' => '5，10，20，50，100，200',
                'tip' => '单位：元。多个捐赠额用中文逗号分开'
            )
        )
    ),
    'install_sql' => 'install.sql'
);

?>