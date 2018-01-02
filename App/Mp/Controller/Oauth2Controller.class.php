<?php

/**
 * Oauth2授权控制器
 */
namespace Mp\Controller;
use Think\Controller;

class Oauth2Controller extends Controller {
	
	// 设置授权token
	public function setAccessToken() {
		if (IS_POST) {
			$username = I('username');
			$password = I('password');
			$mpid = I('mpid');
			$refreshToken = I('refresh_token');
			$accessToken = get_nonce(32);
			$createTime = time();
			$expiresIn = 3600;
			if ($refreshToken) { // 刷新授权token
				$re = M('access_token')->where(['refresh_token'=>$refreshToken])
						->save(['access_token'=>$accessToken,'create_time'=>$createTime]);
				if ($re) {
					$this->ajaxReturn([
						'errcode' => 0,
						'errmsg' => 'ok',
						'items' => [
							'access_token' => $accessToken,
							'refresh_token' => $refreshToken,
							'expires_in' => $expiresIn,
							'create_time' => $createTime
						]
					]);
				}
			}
			$res = D('User/User')->auto_login($username, $password);
			if ($res) {		// 登录认证成功
				$refreshToken = get_nonce(32);
				$user_id = get_user_id();
				if (empty($mpid)) {
					$mpid = D('User/User')->get_default_mp($user_id);
				}
				get_mpid($mpid);
				$mps = D('Mp')->get_mp_lists($user_id); // 获取当前用户创建的公众号
				$addons = D('Addons')->get_access_addons();  // 有权限使用的插件
				$scope = [
					'mp' => [],
					'addon' => []
				];			// 权限
				foreach ($addons as $v) {
					if (isset($v['bzname'])) {
						$scope['addon'][] = $v['bzname'];
					}
				}
				foreach ($mps as $v) {
					if (isset($v['id'])) {
						$scope['mp'][] = $v['id'];
					}
				}
				
				$ret = M('access_token')->add([
					'access_token' => $accessToken,
					'refresh_token' => $refreshToken,
					'expires_in' => $expiresIn,
					'create_time' => $createTime,
					'scope' => json_encode($scope)
				]);
				if ($ret) {
					$this->ajaxReturn([
						'errcode' => 0,
						'errmsg' => 'ok',
						'items' => [
							'access_token' => $accessToken,
							'refresh_token' => $refreshToken,
							'expires_in' => $expiresIn,
							'create_time' => $createTime
						]
					]);
				}
			}
		}
		$this->ajaxReturn([
			'errcode' => 1001,
			'errmsg' => 'Oauth failed'
		]);
	}
}