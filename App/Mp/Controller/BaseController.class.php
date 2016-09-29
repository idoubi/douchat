<?php 

namespace Mp\Controller;
use Common\Controller\CommonController;

/**
 * 公众号模块公用控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class BaseController extends CommonController {

	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function _initialize() {
		parent::_initialize();
		global $_G;
		$_G['mpid'] = get_mpid();
		$_G['mp_info'] = get_mp_info();
		if (!$_G['mpid'] || $_G['mpid'] < 0 || !$_G['mp_info']['origin_id']) {
			if ($_G['controller_name'] != 'mp' && $_G['controller_name'] != 'user') {
				if ($_G['controller_name'] == 'material' && ($_G['action_name'] == 'upload' || $_G['action_name'] == 'get_image_list' || $_G['action_name'] == 'delete_attach')) {

				} else {
					$this->redirect('Mp/lists');
				}
			}
		}
		if ($this->user_access['mp']) {
			$topmenu[] = array(
				'title' => '公众号管理',
				'url' => U('Mp/Index/index'),
				'class' => 'active'
			);
		}
		if ($this->user_access['admin']) {
			$topmenu[] = array(
				'title' => '系统管理',
				'url' => U('Admin/Index/index'),
				'class' => ''
			);
		}
		$this->assign('topmenu', $topmenu);
		$this->assign('system_settings', $_G['system_settings']);
		$addons = D('Admin/Addons')->get_installed_addons();
		add_hook('sidenav', 'Mp\Behavior\SidenavBehavior');				// 添加生成侧边栏导航的钩子
		add_hook('editor', 'Mp\Behavior\EditorBehavior');
		$sidenav = hook('sidenav');										// 执行钩子，获取侧边栏数据
		$this->assign('sidenav', $sidenav);
		$this->assign('addons', $addons);
		$this->assign('mp_info', $_G['mp_info']);
		$this->assign('user_info', get_user_info());
	}
	
	
}


 ?>