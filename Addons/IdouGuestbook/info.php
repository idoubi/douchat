<?php 

return array(
    'name' => '留言板',
    'bzname' => 'IdouGuestbook',
    'desc' => '微信端留言板功能',
    'type' => 'wechat',
    'version' => '1.0',
    'author' => '艾逗笔',
    'logo' => 'logo.jpg',
    'config' => array(
    	'index_url' => U('/addon/IdouGuestbook/web/messages'),
        'setting' => 1,
        'entry' => 1,
        'menu' => 1,
        'entry_list' => array(
            'index' => '留言板首页'
        ),
        'menu_list' => array(
            'messages' => '留言管理'
        ),
        'setting_list' => array(
            'need_audit' => array(
                'title' => '留言是否需要审核',
                'type' => 'radio',
                'options' => array(
                    0 => '不需要',
                    1 => '需要'
                ),
                'value' => 0,
                'group' => 'basic'
            ),
            'per' => array(
                'title' => '每页显示留言数',
                'type' => 'number',
                'value' => 10,
                'group' => 'basic'
            ),
            'share_title' => array(
                'title' => '自定义分享标题',
                'type' => 'text',
                'group' => 'share'
            ),
            'share_cover' => array(
                'title' => '自定义分享封面',
                'type' => 'image',
                'group' => 'share'
            ),
            'share_desc' => array(
                'title' => '自定义分享描述',
                'type' => 'textarea',
                'group' => 'share'
            )
        ),
        'setting_list_group' => array(
            'basic' => array(
                'title' => '基础配置',
                'is_show' => 1,
            ),
            'share' => array(
                'title' => '自定义分享配置',
                'is_show' => 1
            )
        ),
    ),
    'model' => array(
        'idou_guestbook_list' => array(
            'name' => 'idou_guestbook_list',
            'title' => '留言表',
            'lists' => array(
                array(
                    'name' => 'id',
                    'title' => '主键',
                    'format' => 'hidden'
                ),
                array(
                    'name' => 'openid',
                    'title' => '留言者头像',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'get_fans_headimg',
                        'params' => '###'
                    ),
                ),
                array(
                    'name' => 'nickname',
                    'title' => '留言者昵称'
                ),
                array(
                    'name' => 'content',
                    'title' => '留言内容' 
                ),
                array(
                    'name' => 'create_time',
                    'title' => '留言时间',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'date',
                        'params' => 'Y-m-d H:i:s,###'
                    )
                ),
                array(
                    'name' => 'status',
                    'title' => '留言状态',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            0 => '<font color="blue">未审核</font>',
                            1 => '<font color="green">审核通过</font>',
                            2 => '<font color="red">审核不通过</font>'
                        )
                    )
                ),
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            'edit' => array(
                                'title' => '编辑留言',
                                'url' => create_addon_url('edit', array('id'=>'{id}')),
                                'class' => 'btn btn-primary btn-sm icon-edit',
                                'attr' => ''
                            ),
                            'delete' => array(
                                'title' => '删除留言',
                                'url' => create_addon_url('delete', array('id'=>'{id}')),
                                'class' => 'btn btn-danger btn-sm icon-delete',
                                'attr' => ''
                            )
                        )
                    )
                )
            ),
            'list_map' => array('mpid'=>get_mpid()),
            'list_order' => 'create_time desc'
        )
    ),
    'install_sql' => 'install.sql'
);

?>