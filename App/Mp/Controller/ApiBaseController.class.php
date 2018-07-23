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
	public $mpid;					// 当前请求的账号id
	public $mp_info;				// 当前请求的账号信息
	public $addon;					// 当前请求的插件
	public $version;				// 当前请求的接口版本
	public $openid;					// 当前请求的用户openid
	public $sessionKey;				// 当前登录用户sessionKey
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
		$this->user_id = get_user_id();
		$this->checkMp();						// 账号检测
		$this->mpid = get_mpid();
		$this->mp_info = get_mp_info($this->mpid);
		$this->addon = get_addon();
		$this->openid = '';
		$this->sessionKey = '';
		if (isset($this->headers['version']) && !empty($this->headers['version'])) {
			$this->version = $headers['version'];
		}
	}
	
	/**
	 * 检测Api请求权限
	 * 在需要检测Api请求权限的接口方法里面调用 $this->checkAccess();
	 * 要求在发起请求的header头里面带上ak、sk秘钥参数
	 */
	protected function checkAccess() {
		if (!get_user_id()) {			// 接口请求权限检测
			$ak = isset($this->headers['Ak']) ? $this->headers['Ak'] : '';
			$sk = isset($this->headers['Sk']) ? $this->headers['Sk'] : '';
			$user_id = M('access_key')->where([
				'ak' => $ak,
				'sk' => $sk,
				'status' => 1
			])->getField('user_id');
			if (empty($user_id)) {
				$this->response(2001, 'Api请求秘钥检测失效');
			}
			get_user_id($user_id);
		}
	}
	
	/**
	 * 检测账号参数
	 * 要求在发起请求的url中带上mpid参数
	 */
	protected function checkMp() {
		$mpid = isset($_GET['mpid']) ? intval($_GET['mpid']) : 0;
		if (empty($mpid)) {
			$this->response(1001, 'mpid参数必传');
		}
		$mp_info = get_mp_info($mpid);
		if (empty($mp_info) || empty($mp_info['appid']) || empty($mp_info['appsecret'])) {
			$this->response(1001, 'mpid对应的账号信息不存在或数据不完整');
		}
		get_mpid($mpid);
	}
	
	/**
	 * 检测post请求
	 * 在需要进行post检测的接口方法里面使用 $this->checkPost();
	 * 要求发起的必须为post请求
	 */
	protected function checkPost() {
		if (!IS_POST) {
			die('Access Denied');
		}
	}
	
	/**
	 * 检测登录
	 * 在需要检测用户登录状态的接口方法里面调用 $this->checkLogin();
	 * 要求客户端必须在登录成功后才能发起请求
	 */
	protected function checkLogin() {
		$access = false;
		$headers = $this->headers;
		if (isset($headers['User-Token']) && !empty($headers['User-Token'])) {
			$user_token = $headers['User-Token'];
			$cache = S($user_token);
			if (!empty($cache) && !empty($cache['openid']) && !empty($cache['session_key'])) {
				$this->openid = $cache['openid'];
				$this->sessionKey = $cache['session_key'];
				$access = true;
			}
		}
		if (!$access) {
			$this->response(2002, '用户登录检测失效');
		}
	}
	
	/**
	 * 用户登录
	 * 提供给小程序端调用的登录方法
	 */
	public function login() {
		$this->checkPost();
		$this->checkAccess();
		try {
			$post = I('post.');
			if (empty($post['code']) || empty($post['encryptedData']) || empty($post['iv'])) {
				$this->response(1001, '参数code、encryptedData、iv必传');
			}
			$mp_info = $this->mp_info;
			$appid = $mp_info['appid'];
			$appsecret = $mp_info['appsecret'];
			$join_type = $mp_info['join_type'];
			if ($join_type == 2) {		// 授权接入
			
			} else {		// 手动接入
				$Wxapp = new Wxapp([
					'appid' => $appid,
					'appsecret' => $appsecret
				]);
				
				$sessionData = $Wxapp->getSessionKey($post['code']);
				if (empty($sessionData['session_key']) || empty($sessionData['openid'])) {
					$this->response(1001, $Wxapp->errMsg);
				}
				$this->openid = $sessionData['openid'];
				get_openid($sessionData['openid']);		// 缓存当前登录的用户openid
				
				$user_token = md5($sessionData['openid'] . get_nonce(168));		// 用户登录态标识3rdSessionKey
				S($user_token, [
					'openid' => $sessionData['openid'],
					'session_key' => $sessionData['session_key']
				]);
				
				$fansInfo = D('MpFans')->where([
					'mpid' => $this->mpid,
					'openid' => $this->openid
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
							$fansInfo['unionid'] = isset($decodeData['unionId']) ? $decodeData['unionId'] : $decodeData['openId'];
							$fansInfo['nickname'] = text_encode($decodeData['nickName']);
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
					} else {
						$this->response(1001, '解密用户信息失败');
					}
				} else {
				
				}
				
				$this->response(0, '登录成功', [
					'user_token' => $user_token,
				]);
			}
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	/**
	 * 设置手机号
	 */
	public function setPhone() {
		$this->checkPost();
		$this->checkLogin();
		try {
			$post = I('post.');
			if (empty($post['encryptedData']) || empty($post['iv'])) {
				$this->response(1001, '参数encryptedData、iv必传');
			}
			$mp_info = $this->mp_info;
			$appid = $mp_info['appid'];
			$join_type = $mp_info['join_type'];
			if ($join_type == 2) {		// 授权接入
			
			} else {		// 手动接入
				$fansInfo = D('MpFans')->where([
					'mpid' => $this->mpid,
					'openid' => $this->openid
				])->find();
				if (empty($fansInfo)) {
					$this->response(1001, '用户信息不存在，请先授权登录');
				}
				$decodeData = [];
				
				$crypt = new WXBizDataCrypt($appid, $this->sessionKey);
				$errCode = $crypt->decryptData($post['encryptedData'], $post['iv'], $decodeData);
				if ($errCode == 0) {
					$decodeData = json_decode($decodeData, true);
					if (is_array($decodeData) && count($decodeData) > 0) {
						D('MpFans')->where([
							'openid' => $this->openid
						])->save([
							'mobile' => $decodeData['purePhoneNumber']
						]);
						$this->response(0, '设置手机号成功');
					}
				}
				
				$this->response(1001, '设置手机号失败');
			}
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	// 获取用户信息
	public function getUserInfo() {
		try {
			$this->checkLogin();	// 检测登录态
			
			$fansInfo = M('mp_fans')->where([
				'mpid' => $this->mpid,
				'openid' => $this->openid
			])->find();
			$decodeData = [];
			if (!empty($fansInfo)) {
				$decodeData['nickName'] = text_decode($fansInfo['nickname']);
				$decodeData['avatarUrl'] = $fansInfo['headimgurl'];
				$decodeData['gender'] = $fansInfo['sex'];
				$decodeData['language'] = $fansInfo['language'];
				$decodeData['country'] = $fansInfo['country'];
				$decodeData['city'] = $fansInfo['city'];
				$decodeData['province'] = $fansInfo['province'];
				$decodeData['relname'] = $fansInfo['relname'];
				$decodeData['mobile'] = $fansInfo['mobile'];
				$decodeData['signature'] = $fansInfo['signature'];
			}
			$this->response(0, '获取成功', $decodeData);
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	// 更新个人资料
	public function updateProfile() {
		$this->checkLogin();
		if (!IS_POST) {
			$this->response(1001, 'Access Denied');
		}
		$post = I('post.');
		$post['mpid'] = $this->mpid;
		$post['openid'] = $this->openid;
		$res = M('mp_fans')->where([
			'mpid' => $this->mpid,
			'openid' => $this->openid
		])->save($post);
		if ($res === false) {
			$this->response(1001, '更新个人资料失败');
		}
		$this->response(0, '更新个人资料成功');
	}
	
	/**
	 * 上传图片
	 * TODO
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
	
	// 获取小程序码
	public function getQrcode() {
		try {
			$path = I('path', '');
			if (empty($path)) {
				$this->response(1001, '参数path必需');
			}
			$type = I('type', 1, 'intval');
			if (!in_array($type, [1,2,3])) {
				$type = 1;
			}
			$options = I('options', '');
			if (empty($options) || !is_array($options)) {
				$options = [];
			}
			$filename = I('filename', '');
			$res = get_wxa_qrcode($path, $type, $options, $filename);
			if (!$res || !is_file($res)) {
				$this->response(1001, '生成小程序码失败');
			}
			$this->response(0, '获取成功', [
				'path' => $res,
				'url' => str_replace('./', SITE_URL, $res)
			]);
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	// 获取插件配置
	public function getSettings() {
		try {
			$mpid = $this->mpid;
			$addon = I('addon', get_addon());
			$type = I('type', '');
			$theme = I('theme', '');
			$expand = I('expand', 0);	// 是否展开
			
			$settings = [];
			$data = D('Mp/AddonSetting')->get_addon_settings($addon, $mpid, $theme, $type);
			if ($expand) {
				foreach ($data as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $kk => $vv) {
							$settings[$k.'_'.$kk] = $vv;
						}
					} else {
						$settings[$k] = $v;
					}
				}
			} else {
				$settings = $data;
			}
			$this->response(0, '获取成功', $settings);
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	// 获取支付参数
	public function getPayParams() {
		try {
			$tradeType = I('trade_type', 'JSAPI');
			$tradeType = strtoupper($tradeType);
			if (!in_array($tradeType, ['JSAPI','NATIVE','APP'])) {
				$this->response(1001, '支付类型不正确');
			}
			$fee = I('fee', 0, 'floatval');
			if ($fee <= 0) {
				$this->response(1001, '支付金额不正确');
			}
			$orderid = I('orderid', '');
			if (empty($orderid)) {
				$this->response(1001, '订单号必须');
			}
			$notify = I('notify', '');
			if (empty($notify)) {
				$this->response(1001, '支付成功异步通知地址必须');
			}
			$openid = I('openid', '');
			$product_id = I('product_id', '');
			if ($tradeType == 'JSAPI') {		// 网页支付
				if (empty($openid)) {
					$this->checkLogin();
					$openid = $this->openid;
				}
			} elseif ($tradeType == 'NATIVE') {		// 扫码支付
				if (empty($product_id)) {
					$this->response(1001, '参数product_id必须');
				}
			}
			$body = I('body', $orderid);
			$payParams = get_jsapi_parameters([
				'mpid' => $this->mpid,
				'openid' => $openid,
				'product_id' => $product_id,
				'price' => $fee,
				'orderid' => $orderid,
				'trade_type' => $tradeType,
				'body' => $body,
				'notify' => $notify
			]);
			if (!is_array($payParams)) {
				$payParams = json_decode($payParams,true);
			}
			$this->response(0, '获取成功', $payParams);
		} catch (\Exception $e) {
			$this->response(1001, '获取支付参数失败');
		}
	}
	
	// 收集formid，用于发送模板消息
	public function setFormid() {
		if (!IS_POST) {
			die('Access Denied');
		}
		try {
			$this->checkLogin();
			$formid = I('formid');
			if (empty($formid)) {
				$this->response(1001, 'formid必需');
			}
			$id = M('mp_tempmsg')->add([
				'mpid' => $this->mpid,
				'openid' => $this->openid,
				'formid' => $formid,
				'created_at' => time(),
				'status' => 0,
				'type' => 1
			]);
			if ($id) {
				$this->response(0, '收集formid成功');
			}
			$this->response(1001, '收集formid失败');
		} catch (\Exception $e) {
			$this->response(1001, $e->getMessage());
		}
	}
	
	// 接口响应
	public function response($errcode, $errmsg, $items = null) {
		$res['errcode'] = intval($errcode);
		$res['errmsg'] = $errmsg;
		$items !== null && $res['items'] = $items;
		$this->ajaxReturn($res);
	}
	
	// 成功返回
	public function responseOk($items = null) {
		$this->response(0, 'success', $items);
	}
	
	// 失败返回
	public function responseFail($items = null) {
		$this->response(1001, 'fail', $items);
	}
}

?>