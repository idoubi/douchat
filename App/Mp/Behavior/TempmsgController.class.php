<?php

/**
 * 模板消息管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class TempmsgController extends BaseController {
	
	// 数据列表
	public function lists() {
		$this->setMetaTitle('模板消息管理')
			->common_lists();
	}
}