<?php 

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 艾逗笔<765532665@qq.com>
 */
class UserModel extends Model {

	protected $_validate = array(
		array('username', 'require', '用户名不能为空', 1, 'regex', 3),
		array('username', '', '用户已存在', 2, 'unique', 1),
		array('password', 'require', '密码不能为空', 1, 'regex', 1),
		array('confirm_password', 'password', '确认密码不正确', 0, 'confirm', 1),
		array('email', 'require', '邮箱不能为空', 1, 'regex', 3),
		array('email', 'email', '邮箱格式不正确', 2, 'regex', 3),
		array('email', '', '邮箱已被占用', 2, 'unique', 1)
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
	public function check_login($password, $username = '') {
		if (!$username) {
			$username = I('username');
		}
		$user = $this->get_user($username, $password);
		if ($user === false) {
			return false;
		}
		return true;
	}

	/**
	 * 获取用户信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_user($username, $password) {
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
}


?>