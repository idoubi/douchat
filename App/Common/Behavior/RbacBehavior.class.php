<?php

/**
 * 通用权限控制行为类
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Common\Behavior;
use Think\Behavior;

class RbacBehavior extends Behavior {

	public function run(&$params) {
		header("Content-type: text/html; charset=utf-8");
		$Rbac = new \Org\Util\Rbac;
		$user_id = session(C('USER_AUTH_KEY'));
		if (empty($user_id)) {				// 检测登录
			redirect(U('User/Public/login'));
		}
		
		if (!D('User/user')->get_user_info($user_id)) {
			die('用户信息不存在');
		}
		
		if (C('USER_AUTH_ON')) {							// 权限节点检测
			$Rbac::AccessDecision() || die('没有权限');
		}
		$user_access = D('User/User')->get_user_access($user_id);
		if (empty($user_access) || empty($user_access['status'])) {							// 检测角色权限
			die('该用户所属角色未启用');
		}
	}
}