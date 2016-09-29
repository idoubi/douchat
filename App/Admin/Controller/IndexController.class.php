<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 后台首页控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class IndexController extends BaseController {

	/**
	 * 后台首页
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function index() {
        $this->assign('meta_title', '后台首页');
		$this->display();
	}
}

?>