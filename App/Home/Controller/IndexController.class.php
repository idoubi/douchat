<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;

/**
 * 豆信首页控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class IndexController extends Controller {

	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function _initialize() {
		$system_settings = D('Admin/SystemSetting')->get_settings();
		$this->assign('system_settings', $system_settings);
	}
    
    /**
     * 首页
     * @author 艾逗笔<765532665@qq.com>
     */
    public function index() {
    	$this->display();
    }
}