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
		}
		if ($this->user_access['admin']) {
			$topmenu[] = array(
				'title' => '系统管理',
				'url' => U('Admin/Index/index'),
				'class' => 'active'
			);
		}
		$product_info = file_get_contents('./Data/product.info');
		$product_info = json_decode($product_info, true);
		$this->assign('product_info', $product_info);
		$this->assign('topmenu', $topmenu);
		$this->assign('system_settings', $_G['system_settings']);
		$this->assign('user_info', get_user_info());
	}
}


?>