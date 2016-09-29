<?php

return array(
	
    'DB_TYPE'   => '[DB_TYPE]', // 数据库类型
    'DB_HOST'   => '[DB_HOST]', // 服务器地址
    'DB_NAME'   => '[DB_NAME]', // 数据库名
    'DB_USER'   => '[DB_USER]', // 用户名
    'DB_PWD'    => '[DB_PWD]',  // 密码
    'DB_PORT'   => '[DB_PORT]', // 端口
    'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀

    'HTTP_HOST' => $_SERVER['HTTP_HOST'],   // 当前域名

    'URL_MODEL' => 3,                       // URL模式
    'URL_ROUTER_ON' => 1,
    'URL_ROUTE_RULES' => array(
        'interface/:id'    => 'Mp/Api/index',
        'addon/:addon/index' => 'Mp/Web/index',
        'addon/:addon/rule' => 'Mp/Web/rule',
        'addon/:addon/entry/:act' => 'Mp/Web/entry',
        'addon/:addon/setting' => 'Mp/Web/setting',
        'addon/:addon/web/:act' => 'Mp/Web/:2',
        'addon/:addon/mobile/:act' => 'Mp/Mobile/:2',
        'news/:id' => 'Mp/MobileBase/detail'
    ),
    
    'TOKEN_ON'      =>    true,            // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',      // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',           //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,            //令牌验证出错后是否重置令牌 默认为true

    'AUTOLOAD_NAMESPACE' => array(          // 自动加载命令空间
        'Addons' => './Addons/', 
    ),

    'URL_HTML_SUFFIX' => '',                 // 模板后缀
    'TMPL_ACTION_SUCCESS' => './App/Common/View/default/Public/success.html',
    'TMPL_ACTION_ERROR' => './App/Common/View/default/Public/error.html',

    'DEFAULT_FILTER' => 'trim,htmlspecialchars',    // 默认输入过滤  
    'DEFAULT_THEME'=> 'default',

    'RBAC_SUPERADMIN' => '[admin_username]',               //超级管理员名称
    'ADMIN_AUTH_KEY' => 'superadmin',           //超级管理员识别号
    'USER_AUTH_ON' => false,                     //是否开启验证
    'USER_AUTH_TYPE' => 2,                      //验证类型（1.登录时验证2.时时验证）
    'USER_AUTH_KEY' => 'user_id',                   //用户认证识别号
    'NOT_AUTH_MODULE' => 'Public',                        //无需认证的控制器
    'NOT_AUTH_ACTION' => '',             //无需认证的动作方法
    'RBAC_ROLE_TABLE' => '[DB_PREFIX]rbac_role',             //角色表名称
    'RBAC_USER_TABLE' => '[DB_PREFIX]rbac_role_user',        //角色与用户的中间表名称
    'RBAC_ACCESS_TABLE' => '[DB_PREFIX]rbac_access',         //权限表名称
    'RBAC_NODE_TABLE' => '[DB_PREFIX]rbac_node',             //节点表名称

);
