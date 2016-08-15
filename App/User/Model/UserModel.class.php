<?php 

namespace User\Model;
use Think\Model;

/**
 * 用户模型
 * @author 艾逗笔<765532665@qq.com>
 */
class UserModel extends Model {

	protected $_validate = array(
        array('username', 'require', '用户名不能为空', 1, 'regex', 3),
        array('username', '', '用户已存在', 2, 'unique', 1),
        array('username', '6,20', '用户名长度在6至20个字符之间', 2, 'length', 1),
        array('password', 'require', '密码不能为空', 1, 'regex', 1),
        array('password', '6,20', '密码长度在6至20个字符之间', 2, 'length', 1),
        array('confirm_password', 'require', '确认密码不能为空', 0, 'regex', 1),
        array('confirm_password', 'password', '确认密码不正确', 0, 'confirm', 1),
        array('email', 'require', '邮箱不能为空', 1, 'regex', 3),
        array('email', 'email', '邮箱格式不正确', 2, 'regex', 3)
    );
    protected $_auto = array(
        array('password', 'md5', 3, 'function'),
        array('nickname', 'username', 1, 'field'),
        array('register_time', 'time', 1, 'function')
    );

	/**
	 * 检测用户是否存在
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function is_user_exists($username) {
		if (empty($username)) {
			return false;
		}
		$map['username'] = $username;
		$user = M('user')->where($map)->find();
		if (empty($user)) {
			return false;
		}
		return true;
	}

	/**
	 * 验证登录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function check_login($username, $password) {
		if (empty($username) || empty($password)) {
			return false;
		}
		$map['username'] = $username;
		$map['password'] = md5($password);
		$user = M('user')->where($map)->find();
		if (empty($user)) {
			return false;
		} 
		return $user;
	}

	/**
	 * 自动登录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function auto_login($username, $password) {
		$user = $this->check_login($username, $password);
		if (!$user) {
			return false;
		}

		//写入session
		session(C('USER_AUTH_KEY'), $user['id']);
		session('user_info', $user);
		session('username', $user['username']);
		
		if($user['username'] == C('RBAC_SUPERADMIN')){
			session(C('ADMIN_AUTH_KEY'), true);
		}
		$Rbac = new \Org\Util\Rbac;
		$Rbac::saveAccessList();

		$default_mpid = $this->get_default_mp($user['id']);
		if ($default_mpid) {
			session('mpid', $default_mpid);		// 缓存当前用户默认管理公众号
		}
		return true;
	}

	/**
	 * 退出登录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function login_out() {
		if (!is_user_login()) {
			return false;
		}
		session(C('USER_AUTH_KEY'), null);
		session(C('ADMIN_AUTH_KEY'), null);
		session('user_info', null);
		session('username', null);
		session('mpid', null);
		return true;
	}

	/**
	 * 获取用户信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_user_info($user_id) {
		if (empty($user_id)) {
			return false;
		}
		$map['id'] = $user_id;
		$user_info = M('user')->where($map)->find();
		return $user_info;
	}

	/**
	 * 获取用户权限
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_user_access($user_id) {
		if (empty($user_id)) {
			return false;
		}
		$user_info = $this->get_user_info($user_id);
		if ($user_info['username'] == C('RBAC_SUPERADMIN')) {		// 超级管理员拥有全部权限
			$access['status'] = 1;
			$access['mp'] = 1;
			$access['admin'] = 1;
			$access['mp_count'] = 0;
			$access['register_invite_count'] = 0;
			$installed_addons = D('Mp/Addons')->get_installed_addons();
			foreach ($installed_addons as $k => $v) {
				$access['addons'][] = $v['bzname'];
			}
			return $access;
		}
		$map['user_id'] = $user_id;
		$roles = M('rbac_role_user')->where($map)->select();
		foreach ($roles as $k => $v) {
			$role = M('rbac_role')->where(array('id'=>$v['role_id']))->find();		// 取出角色数据
			if ($role['status'] == 1) {
				$access['status'] = 1;												// 只要有一个角色是启用的，则开启权限
			}
			if ($role['type'] == 'mp_manager') {
				$access['mp'] = 1;													// 只要有一个角色属于公众号管理员，则开启公众号管理权限
			} elseif ($role['type'] == 'admin_manager') {
				$access['admin'] = 1;												// 只要有一个角色属于后台管理员，则开启后台管理权限
			} elseif ($role['type'] == 'system_manager') {
				$access['mp'] = 1;
				$access['admin'] = 1;												// 只要有一个角色属于系统管理员，则开启公众号管理和后台管理权限
			}
			$mp_access = M('rbac_mp_access')->where(array('role_id'=>$v['role_id']))->find();
			$access['mp_count'] = intval($mp_access['mp_count']);
			$access['register_invite_count'] = intval($mp_access['register_invite_count']);
			$mp_groups = json_decode($mp_access['mp_groups'], true);
			$addonsArr = array();
			foreach ($mp_groups as $m => $n) {
				$addons = M('mp_group')->where(array('id'=>$n))->getField('addons');	
				$tmp = json_decode($addons, true);
				if (!empty($tmp)) {
					$addonsArr = array_merge($addonsArr, $tmp);
				}
			}
			$access_addons = json_decode($mp_access['addons'], true);
			if (!empty($access_addons)) {
				$addonsArr = array_merge($addonsArr, $access_addons);
			}
			$access['addons'] = array_unique($addonsArr);
		}
		return $access;
	}

	/**
	 * 修改用户密码
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function change_password($user_id, $new_pwd) {
		$user = $this->get_user_info($user_id);
		if (empty($user) || empty($new_pwd)) {
			return false;
		}
		$map['id'] = $user_id;
		M('user')->where($map)->setField('password', md5($new_pwd));
		return true;
	}

	/**
	 * 修改昵称
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function change_nickname($user_id, $nickname) {
		$user = $this->get_user_info($user_id);
		if (empty($user) || empty($nickname)) {
			return false;
		}
		$map['id'] = $user_id;
		M('user')->where($map)->setField('nickname', $nickname);
		return true;
	}

	/**
	 * 设置默认管理公众号
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function set_default_mp($mpid, $user_id = '') {
		if ($user_id == '') {
			$user_id = get_user_id();
		}
		if (!$mpid || !$user_id) {
			return false;
		}
		$map['id'] = intval($user_id);
		$data['default_mpid'] = intval($mpid);
		M('user')->where($map)->save($data);
		return true;
	}

	public function get_default_mp($user_id = '') {
		if ($user_id == '') {
			$user_id = get_user_id();
		}
		if (!$user_id) {
			return false;
		}
		$map['id'] = $user_id;
		$default_mpid = M('user')->where($map)->getField('default_mpid');
		if (!$default_mpid) {
			return false;
		}
		return $default_mpid;
	}
}

?>