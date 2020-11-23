<?php

/**
 * 账号管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace Mp\Controller;

use Mp\Controller\BaseController;

class MpController extends BaseController
{

	public $mp_type;					// 当前账号类型
	public $mp_type_name;				// 账号类型名称

	// 初始化
	public function __construct()
	{
		parent::__construct();
		$this->mp_type = I('mp_type', 1, 'intval');
		$mp_type_arr = [
			1 => '公众号',
			2 => '小程序'
		];
		$this->mp_type_name = $mp_type_arr[$this->mp_type];
	}

	// 账号列表
	public function lists()
	{
		$mp_count = M('mp')->where(array('user_id' => get_user_id(), 'mp_type' => $this->mp_type))->count();
		if ($this->user_access['mp_count'] === 0 || $mp_count < $this->user_access['mp_count']) {
			$btn = array(
				'title' => '添加' . $this->mp_type_name,
				'url' => U('Mp/add', ['mp_type' => $this->mp_type]),
				'class' => 'btn btn-primary'
			);
			$this->addButton($btn);
		}
		if ($this->user_access['mp_count'] !== 0) {
			$tip = '你最多只能创建  ' . $this->user_access['mp_count'] . '  个' . $this->mp_type_name;
			$this->setTip($tip);
		}
		$this->setMetaTitle($this->mp_type_name . '列表')
			->addCrumb('账号管理', '', '')
			->addCrumb($this->mp_type_name . '列表', '', 'active')
			->addNav($this->mp_type_name . '列表', '', 'active')
			->setModel('mp')
			->setListMap(array('user_id' => get_user_id(), 'mp_type' => $this->mp_type))
			->setListOrder('create_time desc')
			->addListItem('id', 'ID')
			->addListItem('name', '账号名称')
			->addListItem('appid', 'Appid')
			->addListItem('mp_type', '账号类别', 'hidden')
			->addListItem('type', '账号类型', $this->mp_type == 1 ? 'enum' : 'hidden', array('options' => array(1 => '普通订阅号', 2 => '认证订阅号', 3 => '普通服务号', 4 => '认证服务号', 5 => '测试号')))
			->addListItem('status', '状态', 'enum', array('options' => array(0 => '禁用', 1 => '正常', 2 => '审核中')))
			->addListItem('create_time', '创建时间', 'function', array('function_name' => 'date', 'params' => 'Y-m-d H:i:s,###'))
			->addListItem('id', '操作', 'custom', array('options' => array('manage' => array('管理', U('Mp/Index/index', array('mpid' => '{id}')), 'btn btn-primary btn-sm', ''), 'edit' => array('编辑', U('Mp/Mp/edit', array('id' => '{id}', 'mp_type' => '{mp_type}')), 'btn btn-success btn-sm'), 'delete' => array('删除', U('delete', array('id' => '{id}')), 'btn btn-danger btn-sm'))))
			->common_lists();
	}

	// 添加账号
	public function add()
	{
		$mp_count = M('mp')->where(array('user_id' => get_user_id(), 'mp_type' => $this->mp_type))->count();
		if ($this->user_access['mp_count'] !== 0 && $mp_count >= $this->user_access['mp_count']) {
			$this->error('你最多只能创建' . $this->user_access['mp_count'] . '个' . $this->mp_type_name);
		}
		$this->setMetaTitle('添加' . $this->mp_type_name)
			->addCrumb('账号管理', '', '')
			->addCrumb('添加' . $this->mp_type_name, '', 'active')
			->addNav('返回' . $this->mp_type_name . '列表', U('lists', ['mp_type' => $this->mp_type]), '')
			->addNav('添加' . $this->mp_type_name, '', 'active')
			->setModel('mp')
			->addFormField('name', '账号名称', 'text')
			->addFormField('type', '账号类型', $this->mp_type == 1 ? 'radio' : 'hidden', array('options' => array(1 => '普通订阅号', 2 => '认证订阅号', 3 => '普通服务号', 4 => '认证服务号', 5 => '测试号'), 'value' => 4, 'is_must' => 1))
			->addFormField('origin_id', '原始ID', 'text', array('is_must' => 1, 'placeholder' => 'gh_40c3fe0469d6'))
			//  ->addFormField('mp_number', '微信号', $this->mp_type == 1 ? 'text' : 'hidden')
			->addFormField('appid', 'APPID', 'text')
			->addFormField('appsecret', 'APPSECRET', 'text')
			->addFormField('headimg', '头像', 'image')
			->addFormField('qrcode', '二维码', 'image')
			->addFormField('mp_type', '账号类别', 'hidden', ['value' => $this->mp_type])
			->setValidate(array(
				array('name', 'require', '账号名称不能为空'),
				array('origin_id', 'require', '账号原始ID不能为空'),
				array('origin_id', '/^gh_[0-9|a-z]{12}$/', '账号原始ID格式错误'),
				array('origin_id', '', '具有相同原始ID的账号已存在', 0, 'unique', 1),
				// array('appid', 'require', 'APPID不能为空'),
				// array('appid', '', '具有相同APPID的账号已存在', 0, 'unique', 1)
			))
			->setAuto(array(
				array('status', '1'),
				array('create_time', 'time', 1, 'function'),
				array('valid_token', 'get_nonce', 1, 'function'),
				array('token', 'get_nonce', 1, 'function', '32'),
				array('encodingaeskey', 'get_nonce', 1, 'function', '43'),
				array('user_id', 'get_user_id', 1, 'function'),
				array('mp_type', $this->mp_type)
			))
			->setAddSuccessUrl(U('Mp/lists', array('mp_type' => $this->mp_type)))
			->common_add();
	}

	// 编辑账号
	public function edit()
	{
		$this->addCrumb('账号管理', '', '')
			->addCrumb('编辑' . $this->mp_type_name, '', 'active')
			->addNav('编辑' . $this->mp_type_name, '', 'active')
			->setModel('mp')
			->addFormField('name', '账号名称', 'text')
			->addFormField('type', '账号类型', $this->mp_type == 1 ? 'radio' : 'hidden', array('options' => array(1 => '普通订阅号', 2 => '认证订阅号', 3 => '普通服务号', 4 => '认证服务号', 5 => '测试号')))
			->addFormField('status', '状态', 'radio', array('options' => array(0 => '禁用', 1 => '正常', 2 => '审核中')))
			->addFormField('origin_id', '原始ID', 'text', array('is_must' => 1))
			->addFormField('mp_number', '微信号', 'text')
			->addFormField('appid', 'APPID', 'text')
			->addFormField('appsecret', 'APPSECRET', 'text')
			->addFormField('headimg', '头像', 'image')
			->addFormField('qrcode', '二维码', 'image')
			->addFormField('mp_type', '账号类别', 'hidden')
			->setFormData(M('mp')->where(['user_id' => $this->user_id])->find(I('get.id')))
			->setEditMap(array('id' => I('get.id'), 'user_id' => $this->user_id))
			->setValidate(array(
				array('name', 'require', '账号名称不能为空'),
				array('origin_id', 'require', '账号原始ID不能为空'),
				array('origin_id', '/^gh_[0-9|a-z]{12}$/', '账号原始ID格式错误'),
			))
			->setEditSuccessUrl(U('Mp/lists', ['mp_type' => $this->mp_type]))
			->common_edit();
	}

	// 删除账号
	public function delete()
	{
		$this->setModel('mp')
			->setDeleteMap(array('id' => I('get.id'), 'user_id' => $this->user_id))
			->common_delete();
	}
}
