<?php 

namespace Admin\Controller;
use Common\Controller\CommonController;

/**
 * 后台公用控制器
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
		if ($this->user_access['mp']) {
			$topmenu[] = array(
				'title' => '公众号管理',
				'url' => U('Mp/Index/index'),
				'class' => ''
			);
			$topmenu[] = array(
				'title' => '插件管理',
				'url' => U('Mp/Addons/manage'),
				'class' => ''
			);
		}
		if ($this->user_access['admin']) {
			$topmenu[] = array(
				'title' => '系统管理',
				'url' => U('Admin/Index/index'),
				'class' => 'active'
			);
		}
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
						'class' => ''
					),
					array(
						'title' => '角色管理',
						'url' => U('Role/lists'),
						'class' => ''
					),
					// array(
					// 	'title' => '权限节点管理',
					// 	'url' => U('Node/lists'),
					// 	'class' => ''
					// )
				),
			),
			array(
				'title' => '公众号管理',
				'url' => 'javascript:;',
				'class' => 'icon icon-reply',
				'attr' => 'data="icon"',
				'children' => array(
					array(
						'title' => '公众号列表',
						'url' => U('Mp/lists'),
						'class' => ''
					),
					array(
						'title' => '公众号套餐',
						'url' => U('MpGroup/lists'),
						'class' => ''
					)
				)
			),
			array(
				'title' => '扩展管理',
				'url' => U('Addons/lists'),
				'class' => 'icon icon-job'
			)
		);
		$this->assign('sidenav', $sidenav);
		$product_info = file_get_contents('./Data/product.info');
		$product_info = json_decode($product_info, true);
		$this->assign('product_info', $product_info);
		$this->assign('topmenu', $topmenu);
		$this->assign('system_settings', $_G['system_settings']);
		$this->assign('user_info', get_user_info());
	}
}


?>