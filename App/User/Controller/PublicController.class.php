<?php 

namespace User\Controller;
use Think\Controller;

class PublicController extends Controller {

	/**
	 * 初始化
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function _initialize() {
		$system_settings = D('Admin/SystemSetting')->get_settings();
		$this->assign('system_settings', $system_settings);
	}

	/**
	 * 用户登录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function login() {
		$system_settings = D('Admin/SystemSetting')->get_settings();
		$userHomeUrl = isset($system_settings['user_home_url']) && !empty($system_settings['user_home_url']) ? U($system_settings['user_home_url']) : ''; // 登录注册成功后的跳转地址
		if (IS_POST) {
			$username = I('username');
			$password = I('password');
			if (empty($username)) {
				$this->error('用户名不能为空');
			}
			if (empty($password)) {
				$this->error('密码不能为空');
			}
			$User = D('User');
			if (!$User->is_user_exists($username)) {
				$this->error('用户不存在');
			}
			if (!$User->auto_login($username, $password)) {
				$this->error('登录密码错误');
			}
			$user_access = $User->get_user_access(get_user_id());
			if (!$user_access['status'] || (!$user_access['mp'] && !$user_access['admin'])) {
				$this->error('你的账号或者所属的角色被禁用，或者你没有管理权限', U('Home/Index/index'));
			}
			if ($user_access['admin']) {
				$jump_url = U('Admin/Index/index');
			} else {
				$jump_url = empty($userHomeUrl) ? U('Mp/Index/index') : $userHomeUrl;
			}
			$this->success('登录成功', $jump_url);
		} else {
			if (is_user_login()) {
			    empty($userHomeUrl) ? $this->redirect('Mp/Index/index') : redirect($userHomeUrl);
			} else {
				$this->display();
			}
		}
	}

	/**
	 * 用户注册
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function register() {
		$system_settings = D('Admin/SystemSetting')->get_settings();
		if ($system_settings['register_on'] != '1') {
			$this->error('未开放注册');
		}
		if (IS_POST) {
			if ($system_settings['register_invite_on']) {		// 开启了邀请注册的情况
				if (!I('invite_user') || !I('invite_code')) {
					$this->error('系统开启了邀请注册模式，你没有邀请码，暂时不能注册');
				}
				$invite = M('user_invite')->where(array('invite_user_id'=>I('invite_user'), 'invite_code'=>I('invite_code')))->find();
				if (!$invite || $invite['status'] == 0) {
					$this->error('邀请码不存在或被禁用，你暂时不能注册');
				}
				if ($invite['status'] == 2) {
					$this->error('邀请码已被使用，你不能再次使用此邀请码注册');
				}
			}
			$User = D('User');
			if (!$User->create()) {
				$this->error($User->getError());
			} else {
				$res = $User->add();
				if (!$res) {
					$this->error('注册失败');
				} else {
					if ($system_settings['register_invite_on']) {
						$invite['register_user_id'] = $res;
						$invite['status'] = 2;
						M('user_invite')->where(array('id'=>$invite['id']))->save($invite);
					}
					if ($system_settings['register_default_role_id']) {
						$data['role_id'] = intval($system_settings['register_default_role_id']);
						$data['user_id'] = $res;
						M('rbac_role_user')->add($data);
					}
					if ($User->auto_login(I('username'), I('password'))) {
						$this->success('用户注册成功并已自动登录', U('Mp/Index/index'));
					} else {
						$this->success('注册成功', U('login'));
					}
				}
			}
		} else {
			$this->assign('invite_user', I('invite_user'));
			$this->assign('invite_code', I('code'));
			$this->display();
		}
	}

	/**
	 * 输出极验验证码
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function geetest_verify() {
		Vendor('GeeTest.geetestlib');
		Vendor('GeeTest.config');
		$GtSdk = new \GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
		$user_id = get_user_id();
		$status = $GtSdk->pre_process($user_id);
		$_SESSION['gtserver'] = $status;
		$_SESSION['user_id'] = $user_id;
		echo $GtSdk->get_response_str();
	}

	/**
	 * 退出登录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function login_out() {
		if (D('User')->login_out()) {
			$this->success('你已退出登录', U('login'));
		} else {
			$this->redirect('login');
		}
	}

	/**
	 * 个人资料
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function profile() {
		$this->display();
	}

	/**
	 * 修改密码
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function change_password() {
		$this->display();
	}
}

?>