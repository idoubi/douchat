<?php 

namespace Common\Behavior;
use Think\Behavior;

/**
 * 通用权限控制行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class RbacBehavior extends Behavior {

	public function run(&$params) {
		global $_G;
		if ($_G['module_name'] == 'admin' || $_G['module_name'] == 'mp') {
			header("Content-type: text/html; charset=utf-8");
			$Rbac = new \Org\Util\Rbac;
			if (!session(C('USER_AUTH_KEY'))) {					// 检测登录
				redirect(U('User/Public/login'));
			}
			if (C('USER_AUTH_ON')) {							// 权限节点检测
				$Rbac::AccessDecision() || die('没有权限');
			}
			$product_info = file_get_contents('./Data/product.info');
			$product_info = json_decode($product_info, true);
			$_G['product_info'] = $product_info;
			$_G['user_id'] = session(C('USER_AUTH_KEY'));
			$_G['user_info'] = session('user_info');
			$_G['system_settings'] = D('Admin/SystemSetting')->get_settings();
			$user_access = D('User/User')->get_user_access($_G['user_id']);
			if (!$user_access['status']) {										// 检测角色权限
				die('该用户所属角色未启用');
			}
			if ($_G['module_name'] == 'mp' && !$user_access['mp']) {
				die('你没有访问公众号管理的权限');
			}
			if ($_G['module_name'] == 'admin' && !$user_access['admin']) {
				die('你没有访问系统管理的权限');
			}
		} else {
			
		}
	}

}