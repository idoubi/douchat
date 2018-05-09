<?php

/**
 * 后台公用控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Admin\Controller;
use Common\Controller\CommonController;

class BaseController extends CommonController {
	
	// 初始化
	public function __construct() {
		parent::__construct();
		if (empty($this->user_access) || empty($this->user_access['admin'])) {
			$this->error('你没有访问此模块的权限');
		}
		
		global $_G;
		$topnav = [];
		if ($this->user_access['mp']) {
			$topnav[] = array(
				'title' => '账号中心',
				'url' => U('Mp/Mp/lists'),
				'class' => ''
			);
			$topnav[] = array(
				'title' => '应用中心',
				'url' => U('Mp/Addons/manage'),
				'class' => ''
			);
		}
		$topnav[] = array(
			'title' => '系统管理',
			'url' => U('Admin/Index/index'),
			'class' => 'active'
		);
		$sidenav = array(
			array(
				'title' => '首页',
				'url' => U('Index/index'),
				'class' => 'icon icon-home'
			),
			array(
				'title' => '全局设置',
				'url' => U('Setting/siteinfo'),
				'class' => 'icon icon-setting'
			),
			array(
				'title' => '用户权限管理',
				'url' => 'javascript:;',
				'class' => 'icon icon-user',
				'attr' => 'data="icon"',
				'children' => array(
					array(
						'title' => '用户管理',
						'url' => U('User/lists'),
						'class' => $this->controller == 'user' ? 'active' : ''
					),
					array(
						'title' => '角色管理',
						'url' => U('Role/lists'),
						'class' => $this->controller == 'role' ? 'active' : ''
					),
					// array(
					// 	'title' => '权限节点管理',
					// 	'url' => U('Node/lists'),
					// 	'class' => ''
					// )
				),
			),
			array(
				'title' => '账号管理',
				'url' => 'javascript:;',
				'class' => 'icon icon-reply',
				'attr' => 'data="icon"',
				'children' => array(
					array(
						'title' => '账号列表',
						'url' => U('Mp/lists'),
						'class' => $this->controller == 'mp' ? 'active' : ''
					),
					array(
						'title' => '账号套餐',
						'url' => U('MpGroup/lists'),
						'class' => $this->controller == 'mpgroup' ? 'active' : ''
					)
				)
			),
			array(
				'title' => '扩展管理',
				'url' => U('Addons/lists'),
				'class' => 'icon icon-job',
                'children' => [
                    [
                        'title' => '功能插件',
                        'url' => U('Addons/lists'),
                        'class' => $this->controller == 'addons' ? 'active' : ''
                    ]
                ]
			)
		);
		$this->assign('sidenav', $sidenav);
		$this->assign('topnav', $topnav);
		$this->assign('product_info', $_G['product_info']);
		$this->assign('system_settings', $_G['system_settings']);
		$this->assign('user_info', $this->user_info);
	}
	
	/**
	 * 初始化
	 */
	public function _initialize() {
		parent::_initialize();
	}
}


?>