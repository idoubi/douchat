<?php 
namespace Mp\Controller;
use Think\Controller;

/**
 * 插件接口控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class ApiBaseController extends Controller {
	
	public function __construct() {
		parent::__construct();
		C('TOKEN_ON', false);
		if ($this->oauth2 === true) {   // 如果有指定需要oauth2授权
			$headers = getallheaders();		// 获取请求头
			// Oauth2授权验证
			$access = false;
			if (isset($headers['Access-Token']) && !empty($headers['Access-Token'])) {
				$accessToken = $headers['Access-Token'];
				// 验证当前请求是否有权限
				$oauth = M('access_token')->where(['access_token'=>$accessToken])->find();
				if ($oauth) {
					if ($oauth['create_time'] + $oauth['expires_in'] > time()) {
						$scope = json_decode($oauth['scope'], true);
						$addon = get_addon();
						if (in_array($addon, $scope['addon'])) {
							$access = true;
						}
					}
				}
			}
			if (!$access && empty(S('Api-Token'))) {  // 对在同一套系统内的插件api请求不做鉴权验证
				$this->response(403, 'Access Denied');
			}
		}
	}
	
	/**
	 * 接口响应
	 */
	public function response($errcode, $errmsg, $items = null) {
		$res['errcode'] = intval($errcode);
		$res['errmsg'] = $errmsg;
		$items !== null && $res['items'] = $items;
		$this->ajaxReturn($res);
	}
	
	/**
	 * 成功返回
	 */
	public function responseOk($items = null) {
		$this->response(0, 'ok', $items);
	}
	
	/**
	 * 失败返回
	 */
	public function responseFail($items = null) {
		$this->response(1001, 'fail', $items);
	}
}

?>