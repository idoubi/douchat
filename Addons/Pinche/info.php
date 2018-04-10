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
        'entry_list' => array(
            'index' => '主页'
        ),
        'menu_list' => array(
            'infoList' => '拼车信息',
        ),
        'setting_list' => array(
            'title' => array(
                'title' => '标题',
                'type' => 'text',
                'placeholder' => '',
                // 'tip' => '单位：元。多个捐赠额用中文逗号分开'
            ),
            'ad1' => array(
                'title' => '首页幻灯片1',
                'type' => 'image',
                'placeholder' => '',
            ),
            'ad1url' => array(
                'title' => '幻灯片1跳转链接',
                'type' => 'text',
            ),
            'ad2' => array(
                'title' => '首页幻灯片2',
                'type' => 'image',
                'placeholder' => '',
            ),
            'ad2url' => array(
                'title' => '幻灯片2跳转链接',
                'type' => 'text',
            ),
            'ad3' => array(
                'title' => '首页幻灯片3',
                'type' => 'image',
                'placeholder' => '',
            ),
            'ad3url' => array(
                'title' => '幻灯片3跳转链接',
                'type' => 'text',
            ),

            'user_add_num' => array(
                'title' => '用户每日最多发布数量',
                'type' => 'text',
            ),

//            'is_audit' => array(
//                'title' => '是否开启审核',
//                'type' => 'radio',
//                'format' => 'enum',
//                'extra' => array(
//                    'options' => array(
//                        1 => '是',
//                        2 => '否',
//                    )
//                )
//            ),

            'timeout' => array(
                'title' => '过期是否显示',
                'type' => 'radio',
                'format' => 'enum',
                'extra' => array(
                    'options' => array(
                        1 => '是',
                        2 => '否',
                    )
                )
            ),

//			'owner' => array(
//				'title' => '车主是否需要认证',
//				'type' => 'radio',
//				'format' => 'enum',
//				'extra' => array(
//					'options' => array(
//						1 => '是',
//						2 => '否',
//					)
//				),
//				'tip' => '如果“是”，车主需要认证后才可以发布拼车信息、查看乘客联系方式'
//			),

            'attention' => array(
                'title' => '是否必须关注才可以使用',
                'type' => 'radio',
                'format' => 'enum',
                'extra' => array(
                    'options' => array(
                        1 => '是',
                        2 => '否',
                    )
                ),
                'tip' => '如果“是”，只有关注才可以使用，否则跳出二维码'
            ),

            'user_center_is_show_charge' => array(
                'title' => '用户中心是否显示【我的余额】',
                'type' => 'radio',
                'format' => 'enum',
                'extra' => array(
                    'options' => array(
                        1 => '是',
                        2 => '否',
                    )
                )
            ),

            'charge' => array(
                'title' => '发布收费金额(扣用户余额)',
                'type' => 'text',
                'placeholder' => '留空或为0则不收费'
            ),

            'charge1' => array(
                'title' => '置顶收费第一档',
                'type' => 'text',
                'placeholder' => '格式 如： 1#2 代表1天2元'
            ),
            'charge2' => array(
                'title' => '置顶收费第二档',
                'type' => 'text',
                'placeholder' => '格式 如： 3#5 代表3天5元'
            ),
            'charge3' => array(
                'title' => '置顶收费第三档',
                'type' => 'text',
                'placeholder' => '格式 如： 30#30 代表30天30元'
            ),

            'recharge' => array(
                'title' => '充值额度设置',
                'type' => 'textarea',
                'placeholder' => '5,10,30,100',
                'tip' => '单位：元。多个充值额度用英文逗号(,)分开'
            ),

            'car_is_cert_addinform' => array(
                'title' => '车主是否认证才能发布信息',
                'type' => 'radio',
                'format' => 'enum',
                'extra' => array(
                    'options' => array(
                        1 => '是',
                        2 => '否',
                    )
                )
            ),


            'is_open_check_sms' => array(
                'title' => '是否开启短信认证',
                'type' => 'radio',
                'format' => 'enum',
                'extra' => array(
                    'options' => array(
                        1 => '是',
                        2 => '否',
                    )
                ),
                'tip' => '开启后 用户发布需要填写姓名及验证手机号'
            ),

            'yunpian_apikey' => array(
                'title' => '云片短信 apikey',
                'type' => 'text',
                'tip' => '<span class="help-block">请选择一个如下方的模板：(云片网址:http://yunpian.com/)<pre>
您的验证码是#code#。如非本人操作，请忽略本短信
</pre></span>'
            ),

            'share_title' => array(
                'title' => '分享标题',
                'type' => 'text',
                'placeholder' => '',
            ),
            'share_desc' => array(
                'title' => '分享描述',
                'type' => 'text',
                'placeholder' => '',
            ),
            'share_pic' => array(
                'title' => '首页分享图片',
                'type' => 'image',
                'placeholder' => '',
            ),

        )
    ),


    'model' => array(
        'bs_pinche' => array(
            'name' => 'bs_pinche',
            'title' => '留言表',
            'lists' => array(
                array(
                    'name' => 'id',
                    'title' => '主键',
                    'format' => 'hidden'
                ),
                array(
                    'name' => 'openid',
                    'title' => '用户头像',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'get_fans_headimg',
                        'params' => '###'
                    ),
                ),
                array(
                    'name' => 'nickname',
                    'title' => '昵称'
                ),
                array(
                    'name' => 'types',
                    'title' => '拼车类型',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            1 => '车找人',
                            2 => '人找车',
                        )
                    )
                ),
                array(
                    'name' => 'gotime',
                    'title' => '出发时间',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'date',
                        'params' => 'm-d H:i,###'
                    )
                ),
                array(
                    'name' => 'from',
                    'title' => '出发地'
                ),
                array(
                    'name' => 'to',
                    'title' => '目的地'
                ),
                array(
                    'name' => 'money',
                    'title' => '费用'
                ),

//				array(
//					'name' => 'num',
//					'title' => '座位数'
//				),

//				array(
//					'name' => 'cartype',
//					'title' => '车型',
//					'format' => 'enum',
//					'extra' => array(
//						'options' => array(
//							0 => '未知',
//							1 => '小轿车',
//							2 => 'SUV',
//							3 => '微面',
//							4 => '货车',
//						)
//					)
//				),

                array(
                    'name' => 'through',
                    'title' => '途经'
                ),

//				array(
//					'name' => 'people_count',
//					'title' => '人数'
//				),

//				array(
//					'name' => 'remark',
//					'title' => '备注'
//				),

                array(
                    'name' => 'contact',
                    'title' => '联系人'
                ),

                array(
                    'name' => 'tel',
                    'title' => '手机号'
                ),

                array(
                    'name' => 'pubtime',
                    'title' => '发布时间',
                    'format' => 'function',
                    'extra' => array(
                        'function_name' => 'date',
                        'params' => 'm-d H:i,###'
                    )
                ),

                array(
                    'name' => 'is_top',
                    'title' => '是否置顶',
                    'format' => 'enum',
                    'extra' => array(
                        'options' => array(
                            0 => '未置顶',
                            1 => '已置顶',
                        )
                    )
                ),

//				array(
//					'name' => 'status',
//					'title' => '状态',
//					'format' => 'enum',
//					'extra' => array(
//						'options' => array(
//							0 => '<font color="blue">未审核</font>',
//							1 => '<font color="green">审核通过</font>',
//							2 => '<font color="red">审核不通过</font>'
//						)
//					)
//				),
                array(
                    'name' => 'id',
                    'title' => '操作',
                    'format' => 'custom',
                    'extra' => array(
                        'options' => array(
                            'edit' => array(
                                'title' => '编辑',
                                'url' => create_addon_url('edit', array('id' => '{id}')),
                                'class' => 'btn btn-primary btn-sm icon-edit',
                                'attr' => ''
                            ),
                            'delete' => array(
                                'title' => '删除',
                                'url' => create_addon_url('delete', array('id' => '{id}')),
                                'class' => 'btn btn-danger btn-sm icon-delete',
                                'attr' => ''
                            )
                        )
                    )
                )
            ),
            'list_map' => array('mpid' => get_mpid()),
            'list_order' => 'pubtime desc'
        )
    ),
    'install_sql' => 'install.sql',
    'logo' => 'logo.png'
);