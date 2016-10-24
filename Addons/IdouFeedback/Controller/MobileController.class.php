<?php

namespace Addons\IdouFeedback\Controller;
use Mp\Controller\MobileBaseController;

/**
 * 意见反馈移动端控制器
 * @author 艾逗笔
 */
class MobileController extends MobileBaseController {

	/**
	 * 反馈首页
	 */
	public function index() {
		$config = get_addon_settings('IdouFeedback');
		$config['top_title'] || $config['top_title'] = '意见反馈';
		$config['page_title'] || $config['page_title'] = '意见反馈';
		$this->assign('config', $config);
		$this->display();
	}

	/**
	 * 处理反馈
	 */
	public function deal() {

		$data = array(
			'mpid' => get_mpid(),
			'openid' => get_openid(),
			'name' => I('name'),
			'contact_type' => I('contact_type'),
			'contact' => I('contact'),
			'content' => I('content'),
			'create_time' => time()
		);

		$result = M('idou_feedback')->add($data);
		if ($result) {
			$data['status'] = 0;
			$data['info'] = '反馈成功，感谢您的支持~';
		} else {
			$data['status'] = 1001;
			$data['info'] = '反馈失败，请重新提交反馈内容';
		}
		$this->ajaxReturn($data);
	}

}

?>