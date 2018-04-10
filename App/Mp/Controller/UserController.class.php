<?php

/**
 * 个人中心控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class UserController extends BaseController {

	/**
	 * 基本资料
	 */
	public function profile() {
		$this->setMetaTitle('基本资料')
			 ->addCrumb('个人中心', '', '')
			 ->addCrumb('基本资料', '', 'active')
			 ->addNav('编辑基本资料', '', 'active')
			 ->setModel('user')
			 ->addFormField('nickname', '昵称', 'text')
			 ->addFormField('headimg', '头像', 'image')
			 ->setFormData(get_user_info())
			 ->setEditMap(array('id'=>get_user_id()))
			 ->setEditSuccessUrl(U('profile'))
			 ->common_edit();
	}

	/**
	 * 修改密码
	 */
	public function change_password() {
		if (IS_POST) {
			if (!I('old_pass')) {
				$this->error('请输入原密码');
			}
			if (!I('new_pass')) {
				$this->error('请输入新密码');
			}
			if (!I('confirm_pass')) {
				$this->error('请输入确认密码');
			}
			$user_info = get_user_info(I('id'));
			if ($user_info['password'] != md5(I('old_pass'))) {
				$this->error('原密码不正确');
			}
			if (I('new_pass') != I('confirm_pass')) {
				$this->error('确认密码不正确');
			}
			if (strlen(I('new_pass')) < 6 || strlen(I('new_pass')) > 20) {
				$this->error('密码长度需在6~20之间');
			}
			$res = M('user')->where(array('id'=>I('id')))->setField('password', md5(I('new_pass')));
			if (!$res) {
				$this->error('修改密码失败');
			} else {
				$this->success('修改密码成功', U('Mp/User/profile'));
			}
		} else {
			$this->setMetaTitle('修改密码')
				 ->addCrumb('个人中心', '', '')
				 ->addCrumb('修改密码', '', 'active')
				 ->addNav('修改密码', '', 'active')
				 ->addFormField('id', '用户ID', 'hidden')
				 ->addFormField('username', '用户名', 'text', array('attr'=>'disabled="true"'))
				 ->addFormField('old_pass', '原密码', 'password')
				 ->addFormField('new_pass', '新密码', 'password')
				 ->addFormField('confirm_pass', '确认密码', 'password')
				 ->setFormData(get_user_info())
				 ->common_edit();
		}
	}
}

?>