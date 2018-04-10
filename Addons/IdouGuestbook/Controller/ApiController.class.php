<?php

/**
 * 留言板插件Api控制器
 * @author 艾逗笔<765532665@qq.com>
 */
namespace Addons\IdouGuestbook\Controller;
use Mp\Controller\ApiBaseController;

class ApiController extends ApiBaseController {
	
	public $oauth2 = true;			// 需要oauth2授权
	public $model;
	public $settings;
	
	public function __construct() {
		parent::__construct();
		$this->model = D('Addons://IdouGuestbook/IdouGuestbookList');
		$this->settings = get_addon_settings('IdouGuestbook');
	}
	
	// 获取留言列表
	public function getDataList() {
		$page = I('page', 1, 'intval');
		$per = I('per', 10, 'intval');
		$data = $this->model->get($page, $per, ['status'=>1]);
		foreach ($data as &$v) {
			$v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
			$v['fans_info'] = get_fans_info($v['openid']);
		}
		return $this->responseOk($data);
	}
	
	// 添加留言
	public function addData() {
		if (IS_POST) {
			$data = I('post.');
			if (empty($data['nickname'])) {
				$this->response(1001, '留言昵称不能为空');
			}
			if (empty($data['content'])) {
				$this->response(1001, '留言内容不能为空');
			}
			if ($this->model->add([
				'mpid' => get_mpid(),
				'openid' => get_openid(),
				'nickname' => trim($data['nickname']),
				'content' => htmlspecialchars($data['content']),
				'create_time' => time(),
				'status' => $this->settings['need_audit'] ? 0 : 1
			])) {
				$this->responseOk();
			} else {
				$this->response(1001, $this->model->getError());
			}
		}
		$this->responseFail();
	}
}