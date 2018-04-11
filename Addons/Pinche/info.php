<?php
/*
 *---------------------------------------------------------------
 *  酷猴工作室 官方网址:http://kuhou.net
 *  淘宝店铺:https://shop137493962.taobao.com/
 *---------------------------------------------------------------
 *  author:  baoshu
 *  website: kuhou.net
 *  email:   83507315@qq.com
 */

return array(
    'name' => '拼车',
    'bzname' => 'Pinche',
    'desc' => '微信拼车插件，支持微信小程序',
    'version' => '0.4.0',
    'type' => '2',
    'author' => '艾逗笔',
    'config' => array(
        'respond_rule' => 0,
        'setting' => 0,
        'entry' => 0,
        'menu' => 1,
        'index_url' => U('/addon/Pinche/web/infoList'),
        'entry_list' => array(
            'index' => '主页'
        ),
        'menu_list' => array(
            'infoList' => '拼车信息',
        ),
        'setting_list' => array(

        )
    ),

    'install_sql' => 'install.sql',
    'logo' => 'logo.png'
);