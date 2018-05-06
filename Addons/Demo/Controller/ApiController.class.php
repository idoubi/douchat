<?php

/**
 * Demo插件Api控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\Demo\Controller;
use Mp\Controller\ApiBaseController;

class ApiController extends ApiBaseController {
	
	public $model;
	
	public function __construct() {
		parent::__construct();
		$this->model = M('demo_diary');
	}
	
	// 新增日记
	public function addDiary() {
		$this->checkLogin();		// 检测用户是否登录
		$post = I('post.');
		if (empty($post['title']) || empty($post['content'])) {
			$this->response(1001, '提交数据不完整');
		}
		$post['openid'] = $this->openid;
		$post['mpid'] = $this->mpid;
		$post['created_at'] = time();
		$id = $this->model->add($post);
		if ($id) {
			$this->response(0, '提交反馈成功');
		} else {
			$this->response(1001, '提交反馈失败');
		}
	}
	
	// 获取日记列表
	public function getDiaryList() {
		$this->checkLogin();
		$data = $this->model->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid,
			'status' => 1
		])->field('id,title,content,created_at')->order('created_at desc')->select();
		foreach ($data as &$v) {
			$v['created_at_format'] = date('Y-m-d H:i:s', $v['created_at']);
		}
		$this->response(0, '获取成功', $data);
	}
	
	// 修改日记
	public function editDiary() {
		$this->checkLogin();
		$post = I('post.');
		if (empty($post['id']) || empty($post['title']) || empty($post['content'])) {
			$this->response(1001, '提交数据不完整');
		}
		$id = $post['id'];
		$data = $this->model->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid,
			'status' => 1
		])->find($id);
		if (empty($data)) {
			$this->response(1001, '要修改的数据不存在');
		}
		
		$res = $this->model->where([
			'id' => $id
		])->save([
			'title' => $post['title'],
			'content' => $post['content'],
			'updated_at' => time()
		]);
		if ($res === false) {
			$this->response(1001, '修改失败');
		}
		$this->response(0, '修改成功');
	}
	
	// 删除日记
	public function deleteDiary() {
		$this->checkLogin();
		$id = I('post.id');
		$data = $this->model->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid,
			'status' => 1
		])->find($id);
		if (empty($data)) {
			$this->response(1001, '要删除的数据不存在');
		}
		
		// 软删除
		$res = $this->model->where([
			'id' => $id
		])->save([
			'status' => -1,
			'deleted_at' => time()
		]);
		if ($res === false) {
			$this->response(1001, '删除失败');
		}
		$this->response(0, '删除成功');
	}

}