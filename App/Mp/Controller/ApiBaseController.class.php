<?php

/**
 * 插件接口公用控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Think\Controller;
use WechatSdk\Wxapp;
use WechatSdk\WXBizDataCrypt;

class ApiBaseController extends Controller {
	
	public $user_id;				// 当前后台用户
	public $mpid;					// 当前请求的账号
	public $addon;					// 当前请求的插件
	public $version;				// 当前请求的接口版本
	public $openid;					// 当前请求的用户openid
	public $headers;				// 当前请求头
    public $controller;             // 当前控制器
    public $action;                 // 当前方法

	// 初始化
	public function __construct() {
		parent::__construct();
		C('TOKEN_ON', false);
		$this->controller = strtolower(CONTROLLER_NAME);
		$this->action = strtolower(ACTION_NAME);
		$this->headers = getallheaders();		// 获取请求头
		if (!get_user_id()) {			// 接口请求权限检测
			$ak = isset($this->headers['Ak']) ? $this->headers['Ak'] : '';
			$sk = isset($this->headers['Sk']) ? $this->headers['Sk'] : '';
			$user_id = M('access_key')->where([
				'ak' => $ak,
				'sk' => $sk,
				'status' => 1
			])->getField('user_id');
			if (empty($user_id)) {
				$this->response(1001, 'Access Denied');
			}
			get_user_id($user_id);
		}
		$this->user_id = get_user_id();
		$mpid = isset($_GET['mpid']) ? intval($_GET['mpid']) : 0;
		if (empty($mpid)) {
			$this->response(1001, 'Invalid mpid');
		}
		$this->mpid = get_mpid($mpid);            // 将当前账号进行缓存
		$this->addon = get_addon();

		if (isset($this->headers['version']) && !empty($this->headers['version'])) {
			$this->version = $headers['version'];
		}
	}
	
	/**
	 * 获取api请求token
	 */
	public function getAccessToken() {
		if (!IS_POST) {
			$this->response(1001, 'Access Denied');
		}
		$ak = I('ak');
		$sk = I('sk');
		if (empty($ak) || empty($sk)) {
			$this->response(1001, 'Invalid params');
		}
		$user_id = M('access_key')->where([
			'ak' => $ak,
			'sk' => $sk
		])->getField('user_id');
		if (!empty($user_id)) {
			$access_token = get_nonce(32);		// api访问token
			$expires = 24 * 3600;			// 过期时间
			S($access_token, [
				'user_id' => $user_id,
				'mpid' => $this->mpid
			], $expires);
			
			$this->response(0, '获取成功', [
				'access_token' => $access_token,
				'expires' => $expires
			]);
		}
		
		$this->response(1001, '获取失败');
	}
	
	/**
	 * 获取用户token
	 */
	public function getUserToken() {
		if (!IS_POST) {
			//$this->response(1001, 'Access Denied');
		}
		try {
			$post = I('post.');
			if (empty($post['code']) || empty($post['encryptedData']) || empty($post['iv'])) {
				$this->response(1001, 'Invalid params');
			}
			$mp_info = get_mp_info();
			
			if (empty($mp_info) || empty($mp_info['appid']) || empty($mp_info['appsecret']) || !in_array($mp_info['mp_type'], [1, 2])) {
				$this->response(1001, 'Invalid mpinfo');
			}
			$appid = $mp_info['appid'];
			$appsecret = $mp_info['appsecret'];
			$join_type = $mp_info['join_type'];
			if ($join_type == 1) {		// 手动接入
				$Wxapp = new Wxapp([
					'appid' => $appid,
					'appsecret' => $appsecret
				]);
				
				$sessionData = $Wxapp->getSessionKey($post['code']);
				if (empty($sessionData['session_key']) || empty($sessionData['openid'])) {
					$this->response(1001, 'Fail to get session key');
				}
				get_openid($sessionData['openid']);		// 缓存当前登录的用户openid
				$fansInfo = D('MpFans')->where([
					'mpid' => $this->mpid,
					'openid' => $sessionData['openid']
				])->find();
				$decodeData = [];
				if (empty($fansInfo)) {
					$crypt = new WXBizDataCrypt($appid, $sessionData['session_key']);
					$errCode = $crypt->decryptData($post['encryptedData'], $post['iv'], $decodeData);
					if ($errCode == 0) {
						$decodeData = json_decode($decodeData, true);
						if (is_array($decodeData) && count($decodeData) > 0) {
							$fansInfo['mpid'] = $this->mpid;
							$fansInfo['openid'] = $decodeData['openId'];
							$fansInfo['nickname'] = $decodeData['nickName'];
							$fansInfo['headimgurl'] = $decodeData['avatarUrl'];
							$fansInfo['province'] = $decodeData['province'];
							$fansInfo['city'] = $decodeData['city'];
							$fansInfo['country'] = $decodeData['country'];
							$fansInfo['sex'] = $decodeData['gender'];
							$fansInfo['language'] = $decodeData['language'];
							$fansInfo['is_subscribe'] = 1;
							$fansInfo['subscribe_time'] = time();
							D('MpFans')->add($fansInfo);       // 登录成功写到数据表
						}
					}
				} else {
					$decodeData['nickName'] = $fansInfo['nickname'];
					$decodeData['avatarUrl'] = $fansInfo['headimgurl'];
					$decodeData['gender'] = $fansInfo['sex'];
					$decodeData['language'] = $fansInfo['language'];
					$decodeData['country'] = $fansInfo['country'];
					$decodeData['city'] = $fansInfo['city'];
					$decodeData['province'] = $fansInfo['province'];
				}
				$user_token = get_nonce(32);
				$expires = 24 * 3600;
				S($user_token, [
					'openid' => $sessionData['openid'],
					'session_key' => $sessionData['session_key']
				], $expires);
				$this->response(0, '获取成功', [
					'userInfo' => $decodeData,
					'user_token' => $user_token,
					'expires' => $expires
				]);
			} elseif ($join_type == 2) {		// 授权接入的方式
			
			}
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	/**
	 * 权限检测。调用此方法的api必须要客户端有访问权限
	 */
	protected function checkAccess() {
		$access = false;
		$headers = $this->headers;
		if (isset($headers['Access-Token']) && !empty($headers['Access-Token'])) {
			$access_token = $headers['Access-Token'];
			$cache = S($access_token);
			if (!empty($cache) && !empty($cache['user_id'])) {
				$this->user_id = $cache['user_id'];
				$access = true;
			}
		}
		if (!$access) {
			$this->response(1001, 'Access Denied');
		}
	}
	
	/**
	 * 登录检测。调用此方法的api必须要微信端用户登录后才能请求
	 */
	protected function checkLogin() {
		$this->checkAccess();
		$access = false;
		$headers = $this->headers;
		if (isset($headers['User-Token']) && !empty($headers['User-Token'])) {
			$user_token = $headers['User-Token'];
			$cache = S($user_token);
			if (!empty($cache) && !empty($cache['openid'])) {
				$this->openid = $cache['openid'];
				$access = true;
			}
		}
		if (!$access) {
			$this->response(1001, 'Access Denied');
		}
	}
	
	/**
	 * api请求检测是否登录
	 */
	public function isLogin() {
		$access = false;
		$headers = $this->headers;		// 获取请求头
		if (isset($headers['User-Token']) && !empty($headers['User-Token'])) {
			$user_token = $headers['User-Token'];
			$cache = S($user_token);
			if (!empty($cache) && !empty($cache['openid'])) {
				$this->openid = $cache['openid'];
				$access = true;
				$this->response(0, '登录有效');
			}
		}
		if (!$access) {
			$this->response(1001, '登录无效');
		}
	}
	
	/**
	 * 更新小程序用户
	 */
	public function updateWxaFans() {
		if (!IS_POST) {
			$this->error(1001, 'Access Denied');
		}
	}
	
	/**
	 * 上传图片
	 */
	public function uploadPicture() {
		$this->checkLogin();
		
		import('Org.Util.UploadFile');
		$upload_time = time();
		$upload_path = './Uploads/Pictures/' . date('Ymd', $upload_time) . '/';
		if (!file_exists($upload_path)) {
			$dirs = explode('/', $upload_path);
			$dir = $dirs[0] . '/';
			for ($i=1, $j=count($dirs)-1; $i<$j; $i++) {
				$dir .= $dirs[$i] . '/';
				if (!is_dir($dir)) {
					mkdir($dir, 0777);
				}
			}
		}
		$upload = new \UploadFile();
		$upload->maxSize  = 1024*20*1000;
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
		$upload->savePath = $upload_path;
		if(!$upload->upload()) {
			$this->response(1001, $upload->getErrorMsg());
		}else{
			//上传成功,将信息存入数据库
			$info =  $upload->getUploadFileInfo();
			$data['mpid'] = get_mpid();
			$data['user_id'] = get_user_id();
			$data['file_name'] = $info[0]['name'];
			$data['file_extension'] = $info[0]['extension'];
			$data['file_size'] = $info[0]['size'];
			$data['file_path'] = $info[0]['savepath'] . $info[0]['savename'];
			$data['hash'] = $info[0]['hash'];
			$data['create_time'] = $upload_time;
			$data['item_type'] = 'image';
			$Attach = D('Attach');
			$attach_id = $Attach->add($data);
			if (!$attach_id) {
				$this->response(1001, '保存附件失败');
			} else {
				$resp['id'] = $attach_id;
				$resp['url'] = tomedia($data['file_path']);
				$this->response(0, '上传成功', $resp);
			}
		}
	}
	
	// 获取插件配置
	public function getSettings() {
		$mpid = $this->mpid;
		$addon = I('addon', get_addon());
		$type = I('type', '');
		$theme = I('theme', '');
		
		$this->response(0, '获取成功', D('Mp/AddonSetting')->get_addon_settings($addon, $mpid, $theme, $type));
	}
	
	/**
	 * 生成返回给客户端的3rdsession
	 * 此方法作为备用
	 */
	private function get3rdSession($openid) {
		$session3rd = get_nonce(168);
		S($session3rd, $openid,2592000);
		return $session3rd;
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
	
	/**
	 * 支付异步通知
	 */
	public function payNotify() {
	
	}
}

?>