<?php 

return array(
    'name' => '微网站',
    'bzname' => 'IdouWeisite',
    'desc' => '微信端微网站，用于最基本的官网展示、企业宣传等',
    'type' => 'wechat',
    'version' => '1.0',
    'author' => '艾逗笔',
    'logo' => 'logo.png',
    'config' => array(
        'index_url' => U('/addon/IdouWeisite/web/web_list'),
        'setting' => 1,
        'entry' => 1,
        'menu' => 1,
        'menu_list' => array(
            'web_list' => '微网列表',
        )
    ),
    'model' => array(
        'weisite_web_list' => array(
            'name' => 'idou_weisite_list',
            'title' => '微站列表',
            'btn' => array(
                array(
                    'title' => '添加微站',
                    'url' => create_addon_url('web_add'),
                    'class' => 'btn btn-primary'
                )
            ),
            'lists' => array(
                array(
                    'name' => 'title',
                    'title' => '微站名称',
                ),
                array(
                    'name' => 'description',
                    'title' => '描述'
                ),
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            array(
                                'title' => '预览',
                                'url' => create_addon_url('web_preview',array('wid'=>'{id}')),
                                'attr' => 'target="_Blank"',
                                'class'=>'btn btn-info btn-sm icon-best',
                            ),
                            array(
                                'title' => '管理',
                                'url' => create_addon_url('web_edit',array('wid'=>'{id}')),
                                'class'=>'btn btn-primary btn-sm icon-edit',
                            ),
                            array(
                                'title' => '删除',
                                'url' => create_addon_url('web_del',array('web'=>'{id}')),
                                'class'=>'btn btn-danger btn-sm icon-delete',
                            )
                        )
                    )
                )
            ),          
            'list_map' => array(
                'mpid' => get_mpid(),
            ),
            'list_order' => 'id desc'
        ),
        'weisite_web_add' => array(
            'name' => 'idou_weisite_list',
            'title' => '添加微网',
            'subnav' => array(
                array(
                    'title' => '添加微网',
                    'url' => '',
                    'class' => 'active'
                )
            ),
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'description',
                    'title' => '描述',
                    'type' => 'textarea',
                ),
                array(
                    'name' => 'cover',
                    'title' => '封面图片',
                    'type' => 'image'
                )
            ),
            'validate' => array(
                array('title', 'require', '名称必须填写')
            ),
            'auto' => array(
                array('status', 1),
                array('mpid', 'get_mpid', 1, 'function')
            ),
            'add_success_url' => create_addon_url('web_list')
        ),
        'weisite_web_edit' => array(
            'subnav' => array(
                array(
                    'title' => '微网列表',
                    'url' => create_addon_url('web_list'),
                    'class' => ''
                ),
                array(
                    'title' => '微站设置',
                    'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
                    'class' => 'active'
                ),
                array(
                    'title' => '分类管理',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '文章管理',
                    'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '页面管理',
                    'url' => create_addon_url('page_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '链接管理',
                    'url' => create_addon_url('link_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '导航管理',
                    'url' => create_addon_url('nav_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '轮播图片',
                    'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '模板设置',
                    'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
            ),
            'name' => 'idou_weisite_list',
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'description',
                    'title' => '描述',
                    'type' => 'textarea',
                ),
                array(
                    'name' => 'cover',
                    'title' => '封面图片',
                    'type' => 'image'
                )
            ),
            'info' => M('idou_weisite_list')->where(array('mpid'=>get_mpid(),'id'=>I('get.wid')))->find(),
            'edit_map' => array('mpid'=>get_mpid(),'id'=>I('get.wid')),
            'edit_success_url' => create_addon_url('web_list')
        ),
        'weisite_cate_list' => array(
            'subnav' => array(
                array(
                    'title' => '微网列表',
                    'url' => create_addon_url('web_list'),
                    'class' => ''
                ),
                array(
                    'title' => '微站设置',
                    'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '分类管理',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => 'active'
                ),
                array(
                    'title' => '文章管理',
                    'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '页面管理',
                    'url' => create_addon_url('page_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '链接管理',
                    'url' => create_addon_url('link_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '导航管理',
                    'url' => create_addon_url('nav_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '轮播图片',
                    'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '模板设置',
                    'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
            ),
            'btn' => array(
                array(
                    'class' => 'btn btn-primary',
                    'url' => create_addon_url('cate_add',array('wid'=>I('get.wid'))),
                    'title'=>'添加分类'
                )
            ),
            'name' => 'idou_weisite_category',
            'title' => '分类列表',
            'lists' => array(
                array(
                    'name' => 'title',
                    'title' => '名称',
                ),
                array(
                    'name' => 'icon',
                    'title' => '图标',
                    'format' => 'image',
                    'extra' => array(
                        'attr' => 'width=50 height=50',
                        'placeholder' => SITE_URL . 'Public/Common/img/nopic.jpg'
                    )
                ),
                array(
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            0 => '不显示',
                            1 => '显示'
                        )
                    ),
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                ),  
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            array(
                                'title' => '编辑',
                                'url' => create_addon_url('cate_edit', array('wid'=>I('get.wid'),'cid'=>'{id}')),
                                'class'=>'btn btn-primary btn-sm icon-edit',
                            ),
                            array(
                                'title' => '删除',
                                'url' => create_addon_url('cate_del', array('cid'=>'{id}')),
                                'class'=>'btn btn-danger btn-sm icon-delete',
                            )
                        )
                    )
                )
            ),          
            'list_map' => array(
                'wid' => I('get.wid'),
                'mpid' => get_mpid()
            ),
            'list_order' => 'sort desc'
        ),
        'weisite_cate_add' => array(
            'subnav' => array(
                array(
                    'title' => '返回分类列表',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '添加分类',
                    'url' => '',
                    'class' => 'active'
                )
            ),
            'name' => 'idou_weisite_category',
            'title' => '添加分类',
            'fields' => array(
                array(
                    'name' => 'wid',
                    'title' => '微站ID',
                    'type' => 'hidden',
                    'value' => I('get.wid')
                ),
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'icon',
                    'title' => '图标',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '链接',
                    'type' => 'text',
                    'tip' => '不填写时默认打开分类列表或下属二级分类',
                ),
                array(
                    'name' => 'description',
                    'title' => '说明',
                    'type' => 'textarea',
                    'tip' => '需要模板支持，有些模板不显示分类说明',
                ),
                array(
                    'name' => 'pid',
                    'title' => '上级分类',
                    'type' => 'select',
                    'tip' => '目前最多支持二级分类',
                    'options' => 'callback',
                    'callback_name' => 'get_categories'
                ),
                array(   
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'options' => array(
                        1 => '显示',
                        0 => '不显示'
                    )
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'text',
                    'tip' => '数字越小排名越靠前',
                ),
            ),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'auto' => array(
                array('wid', I('get.wid')),
                array('mpid', get_mpid())
            ),
            'add_success_url' => create_addon_url('cate_list',array('wid'=>I('get.wid')))
        ),
        'weisite_cate_edit' => array(
            'subnav' => array(
                array(
                    'title' => '返回分类列表',
                    'url' => create_addon_url('cate_list', array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '编辑分类',
                    'url' => '',
                    'class' => 'active'
                )
            ),
            'name' => 'idou_weisite_category',
            'title' => '编辑分类',
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'icon',
                    'title' => '图标',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '链接',
                    'type' => 'text',
                    'tip' => '不填写时默认打开分类列表或下属二级分类',
                ),
                array(
                    'name' => 'description',
                    'title' => '说明',
                    'type' => 'textarea',
                    'tip' => '需要模板支持，有些模板不显示分类说明',
                ),
                array(
                    'name' => 'pid',
                    'title' => '上级分类',
                    'type' => 'select',
                    'tip' => '目前最多支持二级分类',
                    'options' => 'callback',
                    'callback_name' => 'get_categories'
                ),
                array(   
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'options' => array(
                        1 => '显示',
                        0 => '不显示'
                    )
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'text',
                    'tip' => '数字越小排名越靠前',
                ),
            ),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'info' => M('idou_weisite_category')->where(array('mpid'=>get_mpid(),'wid'=>I('get.wid'),'id'=>I('get.cid')))->find(),
            'edit_map' => array('id'=>I('get.cid'),'wid'=>I('get.wid'),'mpid'=>get_mpid()),
            'edit_success_url' => create_addon_url('cate_list',array('wid'=>I('get.wid')))
        ),
        'weisite_cms_list' => array(
            'subnav' => array(
                array(
                    'title' => '微网列表',
                    'url' => create_addon_url('web_list'),
                    'class' => ''
                ),
                array(
                    'title' => '微站设置',
                    'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '分类管理',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '文章管理',
                    'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
                    'class' => 'active'
                ),
                array(
                    'title' => '页面管理',
                    'url' => create_addon_url('page_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '链接管理',
                    'url' => create_addon_url('link_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '导航管理',
                    'url' => create_addon_url('nav_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '轮播图片',
                    'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '模板设置',
                    'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
            ),
            'btn' => array(
                array(
                    'class' => 'btn btn-primary',
                    'url' => create_addon_url('cms_add',array('wid'=>I('get.wid'))),
                    'title'=>'添加文章'
                )
            ),
            'name' => 'idou_weisite_cms',
            'title' => '文章列表',
            'lists' => array(
                array(
                    'name' => 'title',
                    'title' => '文章标题',
                ),
                array(
                    'name' => 'cate_id',
                    'title' => '所属分类',
                    'format' => 'callback',
                    'extra' => array(
                        'callback_name' => 'get_category'
                    )
                ),
                array(
                    'name' => 'ctime',
                    'title' => '发布时间',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'date',
                        'params' => 'Y-m-d H:i:s,###'
                    )
                ),
                array(
                    'name' => 'view_count',
                    'title' => '阅读次数'
                ),      
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            array(
                                'title' => '编辑',
                                'url' => create_addon_url('cms_edit',array('wid'=>I('get.wid'),'aid'=>'{id}')),
                                'class'=>'btn btn-primary btn-sm icon-edit',
                            ),
                            array(
                                'class'=>'btn btn-danger btn-sm icon-delete',
                                'title' => '删除',
                                'url' => create_addon_url('cms_del',array('wid'=>I('get.wid'),'aid'=>'{id}'))
                            )
                        )
                    )
                )
            ),          
            'list_map' => array(
                'wid' => I('get.wid'),
            ),
            'list_order' => 'sort desc'
        ),
        'weisite_cms_add' => array(
            'subnav' => array(
                array(
                    'title' => '返回文章列表',
                    'url' => create_addon_url('cms_list', array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '添加文章',
                    'url' => '',
                    'class' => 'active'
                ),
            ),
            'name' => 'idou_weisite_cms',
            'title' => '添加微网',
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'number',
                    'value' => 0,
                    'tip' => '数字越小排名越靠前',
                ),
                array(   
                    'name' => 'cate_id',
                    'title' => '所属分类',
                    'type' => 'select',
                    'options' => 'callback',
                    'callback_name' => 'get_categories'
                ),
                array(
                    'name' => 'intro',
                    'title' => '描述',
                    'type' => 'textarea',
                ),
                array(
                    'name' => 'ctime',
                    'title' => '发布时间',
                    'type' => 'time'
                ),
                array(
                    'name' => 'cover',
                    'title' => '封面图片',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '外链地址',
                    'type' => 'text'
                ),
                array(   
                    'name' => 'content',
                    'title' => '内容',
                    'type' => 'editor',
                ),
            ),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'auto' => array(
                array('mpid', get_mpid()),
                array('wid', I('get.wid')),
                array('ctime', 'time', 1, 'function')
            ),
            'add_success_url' => create_addon_url('cms_list',array('wid'=>I('get.wid')))
        ),
        'weisite_cms_edit' => array(
            'subnav' => array(
                array(
                    'title' => '返回文章列表',
                    'url' => create_addon_url('cms_list'),
                    'class' => ''
                ),
                array(
                    'title' => '编辑文章',
                    'url' => '',
                    'class' => 'active'
                ),
            ),
            'name' => 'idou_weisite_cms',
            'title' => '微网站',
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'number',
                    'value' => 0,
                    'tip' => '数字越小排名越靠前',
                ),
                array(
                    'name' => 'intro',
                    'title' => '描述',
                    'type' => 'textarea',
                ),
                array(
                    'name' => 'ctime',
                    'title' => '发布时间',
                    'type' => 'time'
                ),
                array(  
                    'name' => 'cate_id',
                    'title' => '所属分类',
                    'type' => 'select',
                    'options' => 'callback',
                    'callback_name' => 'get_categories'
                ),
                array(
                    'name' => 'cover',
                    'title' => '封面图片',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '外链地址',
                    'type' => 'text'
                ),
                array(  
                    'name' => 'content',
                    'title' => '内容',
                    'type' => 'editor',
                ),
            ),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'info' => M('idou_weisite_cms')->where(array('wid'=>I('get.wid'),'id'=>I('get.aid')))->find(),
            'edit_map' => array('mpid'=>get_mpid(),'wid'=>I('get.wid'),'id'=>I('get.aid')),
            'edit_success_url' => create_addon_url('cms_list',array('wid'=>I('get.wid')))
        ),
        'weisite_slideshow_list' => array(
            'subnav' => array(
                array(
                    'title' => '微网列表',
                    'url' => create_addon_url('web_list'),
                    'class' => ''
                ),
                array(
                    'title' => '微站设置',
                    'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '分类管理',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '文章管理',
                    'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '页面管理',
                    'url' => create_addon_url('page_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '链接管理',
                    'url' => create_addon_url('link_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '导航管理',
                    'url' => create_addon_url('nav_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '轮播图片',
                    'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
                    'class' => 'active'
                ),
                array(
                    'title' => '模板设置',
                    'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
            ),
            'btn' => array(
                array(
                    'class' => 'btn btn-primary',
                    'url' => create_addon_url('slideshow_add',array('wid'=>I('get.wid'))),
                    'title'=>'添加轮播'
                )
            ),
            'name' => 'idou_weisite_slideshow',
            'title' => '轮播列表',
            'lists' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                ),
                array(
                    'name' => 'img',
                    'title' => '图片',
                    'format' => 'image',
                    'extra' => array(
                        'attr' => 'width=300 height=100',
                        'placeholder' => __ROOT__ . '/Addons/WeiSite/View/Public/img/nopic.jpg'
                    )
                ),
                array(
                    'name' => 'url',
                    'title' => '链接'
                ),
                array(
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            0 => '不显示',
                            1 => '显示'
                        )
                    )
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                ),  
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            array(
                                'title' => '编辑',
                                'class'=>'btn btn-primary btn-sm icon-edit',
                                'url' => create_addon_url('slideshow_edit', array('wid'=>I('get.wid'),'sid'=>'{id}'))
                            ),
                            array(
                                'title' => '删除',
                                'class'=>'btn btn-danger btn-sm icon-delete',
                                'url' => create_addon_url('slideshow_del', array('wid'=>I('get.wid'),'sid'=>'{id}'))
                            )
                        )
                    )
                )
            ),          
            'list_map' => array(
                'wid' => I('get.wid'),
            ),
            'list_order' => 'sort desc'
        ),
        'weisite_slideshow_add' => array(
            'subnav' => array(
                array(
                    'title' => '返回轮播图列表',
                    'url' => create_addon_url('slideshow_list'),
                    'class' => ''
                ),
                array(
                    'title' => '添加轮播图',
                    'url' => '',
                    'class' => 'active'
                ),
            ),
            'name' => 'idou_weisite_slideshow',
            'title' => '添加轮播',
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'img',
                    'title' => '图片',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '链接',
                    'type' => 'text'
                ),
                array(   
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'options' => array(
                        0 => '不显示',
                        1 => '显示'
                    ),
                    'value' => 1
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'number',
                    'value' => 0,
                    'tip' => '数字越小排名越靠前',
                ),
            ),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'auto' => array(
                array('mpid', get_mpid()),
                array('wid', I('get.wid'))
            ),
            'add_success_url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid')))
        ),
        'weisite_slideshow_edit' => array(
            'subnav' => array(
                array(
                    'title' => '返回轮播图列表',
                    'url' => create_addon_url('slideshow_list'),
                    'class' => ''
                ),
                array(
                    'title' => '编辑轮播图',
                    'url' => '',
                    'class' => 'active'
                ),
            ),
            'name' => 'idou_weisite_slideshow',
            'title' => '轮播',
            'fields' => array(
                array(
                    'name' => 'wid',
                    'title' => '微网id',
                    'type' => 'hidden'
                ),
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'img',
                    'title' => '图片',
                    'type' => 'image'
                ),
                array(
                    'name' => 'url',
                    'title' => '链接',
                    'type' => 'text'
                ),
                array(   
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'options' => array(
                        0 => '不显示',
                        1 => '显示'
                    )
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'text',
                    'tip' => '数字越小排名越靠前',
                ),
            ),
            'info' => M('idou_weisite_slideshow')->where(array('mpid'=>get_mpid(),'wid'=>I('get.wid'),'id'=>I('get.sid')))->find(),
            'validate' => array(
                array('title', 'require', '标题必须填写')
            ),
            'edit_map' => array('wid'=>I('get.wid'),'id'=>I('get.sid')),
            'edit_success_url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid')))
        ),
        'weisite_page_list' => array(
            'subnav' => array(
                array(
                    'title' => '微网列表',
                    'url' => create_addon_url('web_list'),
                    'class' => ''
                ),
                array(
                    'title' => '微站设置',
                    'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '分类管理',
                    'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '文章管理',
                    'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '页面管理',
                    'url' => create_addon_url('page_list',array('wid'=>I('get.wid'))),
                    'class' => 'active'
                ),
                array(
                    'title' => '链接管理',
                    'url' => create_addon_url('link_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '导航管理',
                    'url' => create_addon_url('nav_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '轮播图片',
                    'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '模板设置',
                    'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
            ),
            'btn' => array(
                array(
                    'title' => '添加页面',
                    'url' => create_addon_url('page_add', array('wid'=>I('get.wid'))),
                    'class' => 'btn btn-primary'
                )
            ),
            'name' => 'idou_weisite_page',
            'lists' => array(
                array(
                    'name' => 'title',
                    'title' => '标题'
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序'
                ),
                array(
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            0 => '不显示',
                            1 => '显示'
                        )
                    )
                ),
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            array(
                                'title' => '编辑',
                                'url' => create_addon_url('page_edit', array('wid'=>I('get.wid'),'pid'=>'{id}')),
                                'class' => 'btn btn-primary btn-sm icon-edit'
                            ),
                            array(
                                'title' => '删除',
                                'url' => create_addon_url('page_delete', array('wid'=>I('wid'),'pid'=>'{id}')),
                                'class' => 'btn btn-sm btn-danger icon-delete'
                            )
                        )
                    )
                )
            )
        ),
        'weisite_page_add' => array(
            'subnav' => array(
                array(
                    'title' =>' 返回页面列表',
                    'url' => create_addon_url('page_list', array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '添加页面',
                    'url' => create_addon_url('page_add', array('wid'=>I('get.wid'))),
                    'class' => 'active'
                )
            ),
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'pid',
                    'title' => '上级页面',
                    'type' => 'select'
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'number',
                    'extra' => array(
                        'value' => 0
                    )
                ),
                array(
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'extra' => array(
                        'options' => array(
                            0 => '不显示',
                            1 => '显示'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'name' => 'content',
                    'title' => '内容',
                    'type' => 'editor'
                )
            ),
            'name' => 'idou_weisite_page',
            'validate' => array(
                array('title', 'require', '标题必填')
            ),
            'auto' => array(
                array('mpid', get_mpid()),
                array('wid', I('get.wid'))
            ),
            'add_success_url' => create_addon_url('page_list', array('wid'=>I('get.wid')))
        ),
        'weisite_page_edit' => array(
            'subnav' => array(
                array(
                    'title' =>' 返回页面列表',
                    'url' => create_addon_url('page_list', array('wid'=>I('get.wid'))),
                    'class' => ''
                ),
                array(
                    'title' => '编辑页面',
                    'url' => '',
                    'class' => 'active'
                )
            ),
            'fields' => array(
                array(
                    'name' => 'title',
                    'title' => '标题',
                    'type' => 'text'
                ),
                array(
                    'name' => 'pid',
                    'title' => '上级页面',
                    'type' => 'select'
                ),
                array(
                    'name' => 'sort',
                    'title' => '排序',
                    'type' => 'number',
                    'extra' => array(
                        'value' => 0
                    )
                ),
                array(
                    'name' => 'is_show',
                    'title' => '是否显示',
                    'type' => 'radio',
                    'extra' => array(
                        'options' => array(
                            0 => '不显示',
                            1 => '显示'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'name' => 'content',
                    'title' => '内容',
                    'type' => 'editor'
                )
            ),
            'name' => 'idou_weisite_page',
            'validate' => array(
                array('title', 'require', '标题必填')
            ),
            'auto' => array(
                array('mpid', get_mpid()),
                array('wid', I('get.wid'))
            ),
            'info' => M('idou_weisite_page')->where(array('mpid'=>get_mpid(),'wid'=>I('get.wid'),'id'=>I('get.pid')))->find(),
            'edit_map' => array('mpid'=>get_mpid(),'wid'=>I('get.wid'),'id'=>I('get.pid')),
            'edit_success_url' => create_addon_url('page_list', array('wid'=>I('get.wid')))
        )
    ),
    'install_sql' => 'install.sql'
);

?>