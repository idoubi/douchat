<?php

/**
 * 账号模块公用控制器
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace Mp\Controller;

use Common\Controller\CommonController;

class BaseController extends CommonController
{

	public $mpid;					// 当前账号id
	public $mp_type;				// 当前账号类别
	public $mp_info;				// 当前账号信息
	public $addon;					// 当前插件

	// 初始化
	public function __construct()
	{
		parent::__construct();
		if (empty($this->user_access) || empty($this->user_access['mp'])) {
			$this->error('你没有此模块的访问权限');
		}

		$this->mpid = get_mpid();
		$this->mp_type = I('mp_type', get_mp_type(), 'intval');
		$this->mp_info = get_mp_info();
		$this->addon = get_addon();

		global $_G;
		$_G['mpid'] = $this->mpid;
		$_G['mp_type'] = $this->mp_type;
		$_G['mp_info'] = $this->mp_info;
		$_G['addon'] = $this->addon;

		if (!in_array($_G['controller'], ['mp', 'user', 'material', 'accesskey']) && !($_G['controller'] == 'addons' && $_G['action'] == 'manage')) {
			if (empty($_G['mpid']) || empty($_G['mp_info'])) {
				$this->error('请先选择管理账号', U('Mp/lists'));
			}
		}

		$modctl = $_G['module'] . '/' . $_G['controller'];
		if (in_array($modctl, ['mp/mp', 'mp/user', 'mp/accesskey'])) {
			$topnav[] = array(
				'title' => '账号中心',
				'url' => U('Mp/Mp/lists'),
				'icon' => 'wechat',
				'class' => 'active'
			);
			if ($this->user_access['admin']) {
				$topnav[] = array(
					'title' => '系统管理',
					'icon' => 'gear',
					'url' => U('Admin/Index/index'),
					'class' => ''
				);
			}
		}
		if (in_array($modctl, ['mp/index', 'mp/addons', 'mp/payment', 'mp/message', 'mp/fans', 'mp/autoreply', 'mp/material', 'mp/custommenu', 'mp/sceneqrcode'])) {
			$topnav[] = array(
				'title' => '微信公众号',
				'url' => U('Mp/Index/index'),
				'icon' => 'wechat',
				'class' => 'active'
			);
			$topnav[] = array(
				'title' => '返回账号中心',
				'url' => U('Mp/Mp/lists', ['type' => 1]),
				'icon' => 'reply',
				'class' => ''
			);
		}
		if (get_addon()) {
			$topnav[] = [
				'title' => get_addon_name($this->addon),
				'class' => 'active',
				'icon' => 'plug',
				'url' => U('/addon/' . $this->addon . '/web/index')
			];
			$topnav[] = [
				'title' => '返回公众号',
				'class' => '',
				'icon' => 'reply',
				'url' => U('Mp/Addons/manage')
			];
		}


		add_hook('editor', 'Mp\Behavior\EditorBehavior');
		add_hook('sidenav', 'Mp\Behavior\SidenavBehavior');				// 添加生成侧边栏导航的钩子

		$sidenav = hook('sidenav', $_G);										// 执行钩子，获取侧边栏数据
		$addons = D('Admin/Addons')->get_installed_addons();

		$mp_list = D('Mp/Mp')->get_mp_lists();
		foreach ($mp_list as &$v) {
			$v['mp_type_name'] = '公众号';
			if ($v['mp_type'] == 2) {
				$v['mp_type_name'] = '小程序';
			}
		}

		$this->assign('mp_list', $mp_list);
		$this->assign('system_settings', $_G['system_settings']);
		$this->assign('topnav', $topnav);
		$this->assign('sidenav', $sidenav);
		$this->assign('addons', $addons);
		$this->assign('mp_info', $_G['mp_info']);
		$this->assign('user_info', $_G['user_info']);
	}

	/**
	 * 初始化
	 */
	public function _initialize()
	{
		parent::_initialize();
	}
}
