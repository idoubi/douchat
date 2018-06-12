<?php  

use WechatSdk\Wechat;
use WechatSdk\JsSdk;
use WechatSdk\Wxapp;

/**
 * 添加钩子
 * @author 艾逗笔<765532665@qq.com>
 */
function add_hook($tag,$name) {
    \Think\Hook::add($tag,$name);
}

/**
 * 执行钩子
 * @author 艾逗笔<765532665@qq.com>
 */
function hook($tag, $params=NULL) {
    return \Think\Hook::listen($tag,$params);
}

/**
 * 生成插件访问链接
 * @author 艾逗笔<765532665@qq.com>
 */
function create_addon_url($url, $param = array()){
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    $urlArr = explode('/', $url);
    switch (count($urlArr)) {
        case 1:
            if (in_array(CONTROLLER_NAME, array('Mobile', 'Web'))) {
                $act = strtolower(CONTROLLER_NAME);
                return U('/addon/'.get_addon().'/'.$act.'/'.$url.'@'.C('HTTP_HOST'), $param);
            } else {
                $param = array_merge(['addon'=>get_addon()], $param);
                return U('Mp/'.CONTROLLER_NAME.'/'.$url.'@'.C('HTTP_HOST'), $param);
            }
            break;
        case 2:
            if (in_array($urlArr[0], array('Mobile', 'Web'))) {
                $act = strtolower($urlArr[0]);
                return U('/addon/'.get_addon().'/'.$act.'/'.$urlArr[1].'@'.C('HTTP_HOST'), $param);
            } else {
				$param = array_merge(['addon'=>get_addon()], $param);
                return U('Mp/'.$urlArr[0].'/'.$urlArr[1].'@'.C('HTTP_HOST'), $param);
            }
            break;
        case 3:
            if (in_array($urlArr[1], array('Mobile', 'Web'))) {
                return U('/addon/'.$urlArr[0].'/'.strtolower($urlArr[1]).'/'.$urlArr[2].'@'.C('HTTP_HOST'), $param);
            } else {
				$param = array_merge(['addon'=>get_addon()], $param);
                return U('Mp/'.$urlArr[1].'/'.$urlArr[2].'@'.C('HTTP_HOST'), $param);
            }
            break;
        default:
            return '';
            break;
    }
}

/**
 * 生成移动端访问链接
 */
function create_mobile_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return U('/addon/'.get_addon().'/mobile/'.$url.'@'.C('HTTP_HOST'), $param);
}

/**
 * 生成插件后台访问链接
 */
function create_web_url($url, $param = array()) {
    if (!$param['mpid']) {
       $param['mpid'] = get_mpid();
    }
    return U('/addon/'.get_addon().'/web/'.$url.'@'.C('HTTP_HOST'), $param);
}

/**
 * 设置/获取当前公众号标识
 * @author 艾逗笔<765532665@qq.com>
 */
function get_mpid($mpid = '') {
    if ($mpid) {                            // 手动设置当前公众号
        session('mpid', intval($mpid));
        session('token', M('mp')->where(array('id'=>$mpid))->getField('token'));
    } elseif (I('mpid')) {                  // 如果浏览器中带有公众号标识，则设置为当前公众号
        session('mpid', intval(I('mpid')));   
        session('token', M('mp')->where(array('id'=>I('mpid')))->getField('token'));      
    }
    $mpid = session('mpid');                        // 返回当前公众号标识
    if (empty($mpid)) {                             // 如果公众号标识不存在，则返回0
        return 0;
    }
    return $mpid;
}

/**
 * 设置/获取当前公众号标识
 * @author 艾逗笔<765532665@qq.com>
 */
function get_token($token = '') {
    if ($token) {
        session('token', $token);
        session('mpid', M('mp')->where(array('token'=>$token))->getField('id'));
    } elseif (I('token')) {
        session('token', I('token'));
        session('mpid', M('mp')->where(array('token'=>I('token')))->getField('id'));
    }
    $token = session('token');
    if (empty($token)) {
        return null;
    }
    return $token;
}

/**
 * 获取公众号信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_mp_info($mpid = '') {
    if (empty($mpid)) {
        $mpid = get_mpid();
    }
    $mp_info = D('Mp/Mp')->get_mp_info($mpid);
    return $mp_info;
}

/**
 * 获取当前账号类别
 * 1：微信公众号 2：微信小程序
 */
function get_mp_type() {
	$mp_info = get_mp_info();
	if (!empty($mp_info) && in_array($mp_info['mp_type'], [1, 2])) {
		return $mp_info['mp_type'];
	}
	return 1;
}

/**
 * 设置/获取用户标识
 * @author 艾逗笔<765532665@qq.com>
 */
function get_openid($openid = '') {
    $token = get_token();                     
    if (empty($token)) {                         // 如果公众号标识不存在
        return null;
    }
    if ($openid) {                              // 设置当前用户标识
        session('openid_'.$token, $openid);
    } elseif (I('openid')) {                    // 如果浏览器带有openid参数，则缓存用户标识
        session('openid_'.$token, I('openid'));
    }
    $openid = session('openid_'.$token);                 // 获取当前用户标识
    if (empty($openid)) {
        return null;
    }
    return $openid;
}

/**
 * 获取用户借权标识
 */
function get_ext_openid($ext_openid = '') {
    $token = get_token();                     
    if (empty($token)) {                         // 如果公众号标识不存在
        return null;
    }
    if ($ext_openid) {                              // 设置当前用户标识
        session('ext_openid_'.$token, $ext_openid);
    } elseif (I('ext_openid')) {                    // 如果浏览器带有openid参数，则缓存用户标识
        session('ext_openid_'.$token, I('ext_openid'));
    }
    $ext_openid = session('ext_openid_'.$token);                 // 获取当前用户标识
    if (empty($ext_openid)) {
        return null;
    }
    return $ext_openid;
}

/**
 * 初始化粉丝信息
 */
function init_fans() {
    $mp_info = get_mp_info();
    $mpid = get_mpid();
    $openid = get_openid();
    $token = get_token();
    if (empty($openid) && is_wechat_browser() && $mp_info['appid'] && $mp_info['appsecret'] && $mp_info['type'] == 4) {     // 通过网页授权拉取用户标识
        $wechatObj = get_wechat_obj();
        if ($wechatObj->checkAuth($mp_info['appid'], $mp_info['appsecret'])) {              // 公众号有网页授权的权限
            $callback = get_current_url();                  // 当前访问地址
            $redirect_url = $wechatObj->getOauthRedirect($callback);        // 网页授权跳转地址
            if (!I('code')) {                               // 授权跳转第一步
                redirect($redirect_url);
            } elseif (I('code')) {                          // 授权跳转第二步
                $result = $wechatObj->getOauthAccessToken();
                $user_info = $wechatObj->getOauthUserinfo($result['access_token'], $result['openid']);
                if ($user_info) {
                    $fans_info = M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->find();
                    if ($fans_info) {
                        if ($fans_info['is_bind'] !== 1) {
                            $update['nickname'] = $user_info['nickname'];
                            $update['sex'] = $user_info['sex'];
                            $update['country'] = $user_info['country'];
                            $update['province'] = $user_info['province'];
                            $update['city'] = $user_info['city'];
                            $update['headimgurl'] = $user_info['headimgurl'];
                            M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$result['openid']))->save($update);
                        }
                    } else {
                        $insert['mpid'] = get_mpid();
                        $insert['openid'] = $result['openid'];
                        $insert['is_subscribe'] = 0;
                        $insert['nickname'] = $user_info['nickname'];
                        $insert['sex'] = $user_info['sex'];
                        $insert['country'] = $user_info['country'];
                        $insert['province'] = $user_info['province'];
                        $insert['city'] = $user_info['city'];
                        $insert['headimgurl'] = $user_info['headimgurl'];
                        M('mp_fans')->add($insert);
                    }
                } 
                session('openid_'.$token, $result['openid']);        // 缓存用户标识
                redirect($callback);                                 // 跳转回原来的地址
            }
        }
    }
}

/**
 * 初始化鉴权用户
 */
function init_ext_fans() {
    $openid = get_openid();
    $token = get_token();
    $ext_openid = get_ext_openid();
    $ext_appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
    $ext_appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
    if (empty($ext_openid) && is_wechat_browser() && $ext_appid && $ext_appsecret) {     // 通过网页授权拉取用户标识
            $options = array(    
                'appid'             =>  $ext_appid,               
                'appsecret'         =>  $ext_appsecret            
            );
            $wechatObj = new Wechat($options);
            if ($wechatObj->checkAuth($ext_appid, $ext_appsecret)) {              // 公众号有网页授权的权限
                $callback = get_current_url();                  // 当前访问地址
                $redirect_url = $wechatObj->getOauthRedirect($callback);        // 网页授权跳转地址
                if (!I('code')) {                               // 授权跳转第一步
                    redirect($redirect_url);
                } elseif (I('code')) {                          // 授权跳转第二步
                    $result = $wechatObj->getOauthAccessToken();
                    $user_info = $wechatObj->getOauthUserinfo($result['access_token'], $result['openid']);
                    if ($user_info) {
                        $fans_info = M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$openid))->find();
                        if ($fans_info) {
                            if ($fans_info['is_bind'] !== 1) {
                                $update['nickname'] = $user_info['nickname'];
                                $update['sex'] = $user_info['sex'];
                                $update['country'] = $user_info['country'];
                                $update['province'] = $user_info['province'];
                                $update['city'] = $user_info['city'];
                                $update['headimgurl'] = $user_info['headimgurl'];
                                M('mp_fans')->where(array('mpid'=>get_mpid(),'openid'=>$openid))->save($update);
                            }
                        }
                    } 
                    session('ext_openid_'.$token, $result['openid']);        // 缓存用户标识
                    redirect($callback);                                 // 跳转回原来的地址
                }
            }
    }
}

/**
 * 获取jssdk参数
 */
function get_jssdk_sign_package() {
    $mp_info = get_mp_info();
    $appid = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appid'))->getField('value');
    $appsecret = M('mp_setting')->where(array('mpid'=>get_mpid(),'name'=>'appsecret'))->getField('value');
    !empty($appid) || $appid = $mp_info['appid'];        // 优先使用借权的appid
    !empty($appsecret) || $appsecret = $mp_info['appsecret'];        // 优先使用借权的appsecret
    $jssdk = new JsSdk($appid, $appsecret);
    $sign_package = $jssdk->getSignPackage();        // 获取jssdk配置包
    return $sign_package;
}

/**
 * 获取微信支付参数
 * @author 艾逗笔<765532665@qq.com>
 */
function get_jsapi_parameters($data) {
    vendor('WechatPaySdk.WxPayPubHelper');
    $paySetting = M('mp_setting')->where([
    	'mpid' => $data['mpid'],
		'name' => ['in', ['appid', 'appsecret', 'mchid', 'paysignkey']]
	])->field('name, value')->select();
    $payParams = [];
    foreach ($paySetting as $v) {
    	if (!empty($v['name'])) {
    		$payParams[$v['name']] = $v['value'];
		}
	}
    $appid = isset($payParams['appid']) ? $payParams['appid'] : '';
    $appsecret = isset($payParams['appsecret']) ? $payParams['appsecret'] : '';
    $mchid = isset($payParams['mchid']) ? $payParams['mchid'] : '';
    $paysignkey = isset($payParams['paysignkey']) ? $payParams['paysignkey'] : '';
    $jsApi = new JsApi_pub($appid,$mchid,$paysignkey,$appsecret); 
    $orderid = $data['orderid'];
    $price= floatval($data['price']);
    $attach = [
		'notify' => U('Mp/MobileBase/pay_notify@'.C('HTTP_HOST'))
	];
    $unifiedOrder = new UnifiedOrder_pub($appid,$mchid,$paysignkey,$appsecret);
    if (isset($data['trade_type']) && strtoupper($data['trade_type']) == 'NATIVE') {	// 扫码支付
		$unifiedOrder->setParameter("product_id",isset($data['product_id']) ? $data['product_id'] : '');
	} else {
		$unifiedOrder->setParameter("openid",isset($data['openid']) ? $data['openid'] : '');
	}
    $unifiedOrder->setParameter("body",isset($data['body']) ? $data['body'] : $orderid);
    $unifiedOrder->setParameter("out_trade_no",$orderid);
    $unifiedOrder->setParameter("total_fee",$price*100);
    $unifiedOrder->setParameter("notify_url", SITE_URL . 'Data/notify.php');
    $unifiedOrder->setParameter("trade_type",isset($data['trade_type']) ? $data['trade_type'] : "JSAPI");
    $unifiedOrder->setParameter("attach", json_encode($attach));//附加数据
	
	$code_url = '';
	$prepay_id = '';
	if (strtoupper($data['trade_type']) == 'NATIVE') {	// 扫码支付
		$code_url = $unifiedOrder->getCodeUrl();
		if ($code_url) {
			vendor('WechatPaySdk.phpqrcode');
			$level = 'L';// 纠错级别：L、M、Q、H
			$size = 10;
			ob_start();
			QRcode::png($code_url,false, $level, $size);
			$imageString = base64_encode(ob_get_contents());
			ob_end_clean();
			$code_data = "data:image/png;base64,{$imageString}";
		} else {
			$code_data = '';
		}
		$jsApiParameters['code_data'] = $code_data;
		$jsApiParameters['code_url'] = $code_url;
	} else {
		$prepay_id = $unifiedOrder->getPrepayId();
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
	}
	if ($code_url || $prepay_id) {
		if (M('mp_payment')->where(['mpid'=>$data['mpid'],'orderid'=>$data['orderid']])->find('id')) {
		
		} else {
			M('mp_payment')->add([
				'mpid' => $data['mpid'],
				'openid' => $data['openid'],
				'orderid' => $data['orderid'],
				'create_time' => time(),
				'detail' => json_encode($data),
				'price' => $data['price'],
				'notify' => isset($data['notify']) ? $data['notify'] : '',
				'status' => 0,
				'mchid' => $mchid,
				'trade_type' => strtoupper($data['trade_type']),
				'prepay_id' => $prepay_id,
				'code_url' => $code_url
			]);
		}
	}
	
    return $jsApiParameters;
}

/**
 * 企业付款
 */
function mch_pay($params = array()) {
    vendor('WechatPaySdk.WxPayPubHelper');
    $mpid = get_mpid();
    $mp_info = get_mp_info();
    $openid = get_openid();
    $settings = D('MpSetting')->get_settings();
    $sslcert = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_cert.pem';
    $sslkey = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_key.pem';
    file_put_contents($sslcert, isset($settings['sslcert']) ? $settings['sslcert'] : '');
    file_put_contents($sslkey, isset($settings['sslkey']) ? $settings['sslkey'] : '');
    $orderid = isset($params['partner_trade_no']) ? $params['partner_trade_no'] : $mpid.time();
    $total_amount = isset($params['amount']) ? $params['amount']*100 : '';
    $mchpay = new MchPay_pub($settings['appid'], $settings['mchid'], $settings['paysignkey'], $settings['appsecret']);
    $mchpay->setParameter('partner_trade_no', $orderid);
    $mchpay->setParameter('openid', isset($params['openid']) ? $params['openid'] : $openid);
    $mchpay->setParameter('amount', $total_amount);
    $mchpay->setParameter('check_name', isset($params['check_name']) ? $params['check_name'] : 'NO_CHECK');
    $mchpay->setParameter('desc', isset($params['desc']) ? $params['desc'] : '');
    $result = $mchpay->getResult($sslcert, $sslkey);
    if (isset($result['return_code']) && isset($result['result_code']) && $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
        if (!M('mp_payment')->where(array('orderid'=>$orderid))->find()) {
            $data['mpid'] = $mpid;
            $data['openid'] = isset($params['openid']) ? $params['openid'] : $openid;
            $data['orderid'] = $orderid;
            $data['create_time'] = time();
            $result['total_fee'] = $total_amount;
            $data['detail'] = json_encode($result);
            M('mp_payment')->add($data);
        } 
    }
    unlink($sslcert);
    unlink($sslkey);
    return $result;
}

/**
 * 现金红包
 */
function redpack_pay($params = array()) {
    vendor('WechatPaySdk.WxPayPubHelper');
    $mpid = get_mpid();
    $mp_info = get_mp_info();
    $openid = get_openid();
    $settings = D('MpSetting')->get_settings();
    $sslcert = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_cert.pem';
    $sslkey = APP_PATH . '/Mp/Conf/'. $mpid . '_' . $openid . '_apiclient_key.pem';
    file_put_contents($sslcert, isset($settings['sslcert']) ? $settings['sslcert'] : '');
    file_put_contents($sslkey, isset($settings['sslkey']) ? $settings['sslkey'] : '');
    $orderid = isset($params['mch_billno']) ? $params['mch_billno'] : $mpid.time();
    $total_amount = isset($params['total_amount']) ? $params['total_amount']*100 : '';
    $mchpay = new Redpack_pub($settings['appid'], $settings['mchid'], $settings['paysignkey'], $settings['appsecret']);
    $mchpay->setParameter('mch_billno', $orderid);
    $mchpay->setParameter('send_name', isset($params['send_name']) ? $params['send_name'] : $mp_info['name']);
    $mchpay->setParameter('re_openid', isset($params['re_openid']) ? $params['re_openid'] : $openid);
    $mchpay->setParameter('total_amount', $total_amount);
    $mchpay->setParameter('total_num', isset($params['total_num']) ? $params['total_num'] : 1);
    $mchpay->setParameter('wishing', isset($params['wishing']) ? $params['wishing'] : '');
    $mchpay->setParameter('act_name', isset($params['act_name']) ? $params['act_name'] : '');
    $mchpay->setParameter('remark', isset($params['remark']) ? $params['remark'] : '');
    $result = $mchpay->getResult($sslcert, $sslkey);
    if (isset($result['return_code']) && isset($result['result_code']) && $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
        if (!M('mp_payment')->where(array('orderid'=>$orderid))->find()) {
            $data['mpid'] = $mpid;
            $data['openid'] = $result['re_openid'];
            $data['orderid'] = $orderid;
            $data['create_time'] = time();
            $result['total_fee'] = $total_amount;
            $data['detail'] = json_encode($result);
            M('mp_payment')->add($data);
        } 
    }
    unlink($sslcert);
    unlink($sslkey);
    return $result;
}

/**
 * 获取插件模型
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon_model($model) {
    return D('Addons')->get_addon_model($model);
}

/**
 * 获取插件侧边导航
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon_config($addon) {
    if (empty($addon)) {
        return false;
    }
    $addon_config = include ADDON_PATH . $addon . '/config.php';
    return $addon_config;
}

/**
 * 获取插件配置信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon_settings($addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$addon || !$mpid) {
        return false;
    }
    $addon_settings = D('AddonSetting')->get_addon_settings($addon, $mpid);
    if (!$addon_settings) {
        return false;
    }
    return $addon_settings;
}

/**
 * 获取功能入口信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon_entry($act, $addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$act || !$addon || !$mpid) {
        return false;
    }
    $addon_entry = D('AddonEntry')->get_addon_entry($act, $addon, $mpid);
    if (empty($addon_entry)) {
        $addon_config = get_addon_config($addon);
        foreach ($addon_config['entry_list'] as $k => $v) {
            if ($v['act'] == $act) {
                $addon_entry['name'] = $v['name'];
                $addon_entry['act'] = $v['act'];
                $addon_entry['url'] = U('Mobile/'.$v['act'].'@'.C('HTTP_HOST'), array('addon'=>$addon));
                break;
            }
        }
    } else {
        $addon_entry['url'] = U('Mobile/'.$addon_entry['act'].'@'.C('HTTP_HOST'), array('addon'=>$addon));
        $addon_entry['rule'] = D('MpRule')->get_entry_($addon_entry['id']);
    }
    
    if (!isset($addon_entry)) {
        return false;
    }
    return $addon_entry;
}

/**
 * 获取入口信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_entry_info($entry_id) {
    if (!$entry_id) {
        return false;
    }
    $entry_info = D('AddonEntry')->get_entry_info($entry_id);
    return $entry_info;
}

/**
 * 获取插件响应规则
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon_rule($addon = '', $mpid = '') {
    if ($addon == '') {
        $addon = get_addon();
    }
    if ($mpid == '') {
        $mpid = get_mpid();
    }
    if (!$addon || !$mpid) {
        return false;
    }
    $addon_rule = D('MpRule')->get_respond_rule();
    return $addon_rule;
}

/**
 * 获取当前访问的完整URL地址
 * @author 艾逗笔<765532665@qq.com>
 */
function get_current_url() {
    $url = 'http://';
    if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
        $url = 'https://';
    }
    if ($_SERVER ['SERVER_PORT'] != '80') {
        $url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
    } else {
        $url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos ( $url, '?' ) === false) {
        $url .= '?t=' . time ();
    }
    return $url;
}

/**
 * 根据公众号标识获取公众号基本信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_wechat_info($token = '') {
    $token || $token = session('token');                // 获取token
    $wechatInfo = M('mp')->where(array('token'=>$token))->find();
    return $wechatInfo;
}

/**
 * 获取微信api对象
 * @author 艾逗笔<765532665@qq.com>
 */
function get_wechat_obj() {
    $wechatInfo = get_mp_info();
    $options = array(
        'token'             =>  $wechatInfo['valid_token'],                 
        'encodingaeskey'    =>  $wechatInfo['encodingaeskey'],      
        'appid'             =>  $wechatInfo['appid'],               
        'appsecret'         =>  $wechatInfo['appsecret']            
    );
    $wechatObj = new Wechat($options);
    $wechatObj->getRev();
    return $wechatObj;
}

/**
 * 回复文本消息
 * @author 艾逗笔<765532665@qq.com>
 */
function reply_text($text) {
    $wechatObj = get_wechat_obj();
    if (!$text) {
        return;
    }
    return $wechatObj->text($text)->reply();
}

/**
 * 回复图文消息
 * @author 艾逗笔<765532665@qq.com>
 */
function reply_news($articles) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->news($articles)->reply();
}

/**
 * 回复音乐消息
 * @author 艾逗笔<765532665@qq.com>
 */
function reply_music($arr) {
    if (!isset($arr['title']) || !isset($arr['description']) || !$arr['musicurl']) {
        return false;
    }
    $wechatObj = get_wechat_obj();
    return $wechatObj->music($arr['title'], $arr['description'], $arr['musicurl'], $arr['hgmusicurl'], $arr['thumbmediaid'])->reply();
} 

/**
 * 发送客服消息
 * @author 艾逗笔<765532665@qq.com>
 */
function send_custom_message($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->sendCustomMessage($data);
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

function get_menu() {
    $wechatObj = get_wechat_obj();
    return $wechatObj->getMenu();
}

function create_menu($data) {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->createMenu($data);
    if (!$result) {
        $result['errcode'] = $wechatObj->errCode;
        $result['errmsg'] = $wechatObj->errMsg;
    }
    return $result;
}

function delete_menu() {
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->deleteMenu();
    if (!$result) {
        return $wechatObj->errMsg;
    }
    return $result;
}

/**
 * 创建二维码ticket
 * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
 * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
 * @param int $expire 临时二维码有效期，最大为1800秒
 * @return array('ticket'=>'qrcode字串','expire_seconds'=>1800,'url'=>'二维码图片解析后的地址')
 */
function get_qr_code($scene_id,$type=0,$expire=1800){
    $wechatObj = get_wechat_obj();
    $result = $wechatObj->getQRCode($scene_id,$type,$expire);
    if (!$result) {
        $return['errcode'] = 1001;
        $return['errmsg'] = $wechatObj->errMsg;
        return $return;
    }
    return $result;
}

/**
 * 获取二维码图片
 * @param string $ticket 传入由getQRCode方法生成的ticket参数
 * @return string url 返回http地址
 */
function get_qr_url($ticket) {
    $wechatObj = get_wechat_obj();
    return $wechatObj->getQRUrl($ticket);
}

/**
 * 长链接转短链接接口
 * @param string $long_url 传入要转换的长url
 * @return boolean|string url 成功则返回转换后的短url
 */
function get_short_url($long_url){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getShortUrl($long_url);
}

/**
 * 获取接收TICKET
 */
function get_rev_ticket(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevTicket();
}

/**
* 获取二维码的场景值
*/
function get_rev_scene_id(){
    $wechatObj = get_wechat_obj();
    return $wechatObj->getRevSceneId();
}

/**
 * 利用微信接口获取微信粉丝信息
 * @author 艾逗笔<765532665@qq.com>
 */
function get_fans_wechat_info($openid = '') {
    $openid || $openid = get_openid();
    $wechatObj = get_wechat_obj();
    return $wechatObj->getUserInfo($openid);
}

/**
 * 获取粉丝基本资料
 * @author 艾逗笔<765532665@qq.com>
 */
function get_fans_info($openid = '', $field = '') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $fans_info = D('MpFans')->get_fans_info($openid, $field);
    if (!$fans_info) {
        return false;
    }
    return $fans_info;
}

/**
 * 获取粉丝头像
 * @author 艾逗笔<765532665@qq.com>
 */
function get_fans_headimg($openid = '', $attr = 'width=50 height=50') {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $headimgurl = get_fans_info($openid, 'headimgurl');
    if (empty($headimgurl)) {
        $headimgurl = __ROOT__ . '/Public/Admin/img/noname.jpg';
    }
    return "<img src='".$headimgurl."' ".$attr." />";
}

function get_fans_nickname($openid) {
    if ($openid == '') {
        $openid = get_openid();
    }
    if (!$openid) {
        return false;
    }
    $nickname = get_fans_info($openid, 'nickname');
    if (empty($nickname)) {
        $nickname = '匿名';
    }
    return text_decode($nickname);
}

function get_nickname($openid) {
    return D('MpFans')->get_fans_info($openid, 'nickname');
}

function get_headimg($openid) {
    return D('MpFans')->get_fans_info($openid, 'headimgurl');
}

function get_message($msgid) {
    $message = D('MpMessage')->get_message($msgid);
    switch ($message['msgtype']) {
        case 'text':
            return $message['content'];
            break;
        case 'image':
            // 感谢 @  平凡<58000865@qq.com> 提供的微信图片防盗链解决方案
            return '<img src="http://www.zorhand.com/img?url='.$message['picurl'].'" width="100" height="100" />';      
            break;
        default:
            return '';
            break;
    }
}

/**
 * 将图片路径或者媒体文件转换为可访问的图片地址
 * @author 艾逗笔<765532665@qq.com>
 */
function tomedia($path) {
    if (preg_match('/(.*?)\.(jpg|jpeg|png|gif)$/', $path)) {
        if (preg_match('/^\.\/(.*)\.(jpg|png|gif|jpeg)$/', $path)) {
            return str_replace('./', SITE_URL, $path);
        } else {
            return $path;
        }
    } else {
        return SITE_URL . 'Public/Admin/img/nopic.jpg';
    }
}

/**
 * 增加积分
 * @author 艾逗笔<765532665@qq.com>
 */
function add_score($value,$remark='',$type='score',$flag='',$source='addon') {
    return D('MpScoreRecord')->add_score($value,$remark,$type,$flag,$source);
}

/**
 * 获取积分
 */
function get_score($type='', $source='', $flag='', $openid='') {
    return D('MpScoreRecord')->get_score($type, $source, $flag, $openid);
}

/**
 * 创建目录或文件
 * @author 艾逗笔<765532665@qq.com>
 */
function create_dir_or_files($files) {
    foreach ( $files as $key => $value ) {
        if (substr ( $value, - 1 ) == '/') {
            mkdir ( $value );
        } else {
            @file_put_contents ( $value, '' );
        }
    }
}
/**
 * 生成随机字符串
 * @param $length int 字符串长度
 * @return $nonce string 随机字符串
 */
function get_nonce($length=32) {
	$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$nonce = '';
	for ($i=0; $i<$length; $i++) {
		$nonce .= $str[mt_rand(0, 61)];
	}
	return $nonce;
}

/**
 * 检测用户是否登录
 * @author 艾逗笔<765532665@qq.com>
 */
function is_user_login() {
    $user_id = session('user_id');
    if (!$user_id || $user_id < 0) {
        return false;
    } else {
        return true;
    }
}

/**
 * 获取当前登录用户ID
 */
function get_user_id($user_id = null) {
	if (!empty($user_id)) {
		session(C('USER_AUTH_KEY'), $user_id);
	}
	$user_id = session(C('USER_AUTH_KEY'));
    if (!$user_id || $user_id < 0) {
        return false;
    }
    return $user_id;
}

/**
 * 获取用户资料
 * @author 艾逗笔<765532665@qq.com>
 */
function get_user_info($user_id = '') {
    if (!$user_id) {
        $user_id = get_user_id();
    }
    $user_info = M('user')->find($user_id);
    return $user_info;
}



/**
 * 判断是否处在微信浏览器内
 * @author 艾逗笔<765532665@qq.com>
 */
function is_wechat_browser() {
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}


/**
 * 执行sql文件
 * @author 艾逗笔<765532665@qq.com>
 */
function execute_sql_file($sql_path) {
    // 读取SQL文件
    $sql = file_get_contents($sql_path);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    
    // 替换表前缀
    $orginal = 'dc_';
    $prefix = C('DB_PREFIX');
    $sql = str_replace("{$orginal}", "{$prefix}", $sql);
    
    // 开始安装
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }
        $res = M()->execute($value);
    }
}

/**
 * 生成分页导航
 * @author 艾逗笔<765532665@qq.com>
 */
function pagination($count, $per = 10, $params = array()) {
    if (!$count || intval($count) < 0) {
        return '';
    }
    if (get_addon()) {
        $params['addon'] = get_addon();
    }
    $Page = new \Think\Page($count, $per, $params);
    $Page->setConfig('rollPage', 7);
    $Page->setConfig('lastSuffix', false);
    $Page->setConfig('page_begin_wrap', '<div class="page-control"><ul class="pagination pull-right">');    
    $Page->setConfig('page_end_wrap', '</ul></div>');
    $Page->setConfig('link_begin_wrap', '<li>');
    $Page->setConfig('link_end_wrap', '</li>');
    $Page->setConfig('current_begin_wrap', '<li class="active"><a>');
    $Page->setConfig('current_end_wrap', '</a></li>');
    $Page->setConfig('first', '<<');
    $Page->setConfig('last', '>>');
    $Page->setConfig('prev', '<');  
    $Page->setConfig('next', '>');  
    $pagination = $Page->show();
    return $pagination;
}

/**
 * 获取当前访问的插件名称
 * @author 艾逗笔<765532665@qq.com>
 */
function get_addon() {
    preg_match('/\/addon\/([^\/]+)/', '/'.$_SERVER['PATH_INFO'], $m);
    if (!$m[1]) {
        return false;
    }
    return $m[1];
}

// 获取插件名称
function get_addon_name($addon = '') {
	if (empty($addon)) {
		$addon = get_addon();
	}
	return M('addons')->where(['bzname'=>$addon])->getField('name');
}

function get_agent() {
    $agent = $_SERVER ['HTTP_USER_AGENT']; 
    return $agent;
}

function get_ip(){
    if (isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP'])){
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        return strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
    }
    if (isset($_SERVER['HTTP_PROXY_USER']) and !empty($_SERVER['HTTP_PROXY_USER'])){
        return $_SERVER['HTTP_PROXY_USER'];
    }
    if (isset($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['REMOTE_ADDR'])){
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return "0.0.0.0";
    }
}

// 获取所有请求头
if (!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = array();
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

// 发起curl请求
function curl($url, $method = 'get', $param = null, $headers = null) {
	$oCurl = curl_init();
	if(stripos($url,"https://")!==FALSE){
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	}
	if ($headers) {
		curl_setopt($oCurl, CURLOPT_HTTPHEADER, $headers);
	}
	if (is_string($param)) {
		$strPOST = $param;
	} else {
		$aPOST = array();
		foreach($param as $key=>$val){
			$aPOST[] = $key."=".urlencode($val);
		}
		$strPOST =  join("&", $aPOST);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	if ($method != 'get') {
		if ($method == 'post') {
			curl_setopt($oCurl, CURLOPT_POST,true);
		} else {
			curl_setopt($oCurl, CURLOPT_CUSTOMREQUEST,strtoupper($method));
		}
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
	}
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if(intval($aStatus["http_code"])==200){
		return $sContent;
	}else{
		return false;
	}
}

/**
 * 小程序相关函数
 */

if (!function_exists('get_wxa_qrcode')) {
	/**
	 * 获取小程序码
	 * 接口文档：https://developers.weixin.qq.com/miniprogram/dev/api/qrcode.html
	 * @param $path 场景值/页面路径
	 * @param $type 类型。1：A类型二维码 2：B类型二维码 3：C类型二维码
	 * @param $options 额外参数
	 * @param $filename 要保存的文件名（绝对路径，默认保存到Uploads文件夹下面）
	 */
	function get_wxa_qrcode($path, $type=1, $options=[], $filename='') {
		try {
			$mp_info = get_mp_info();
			if (empty($mp_info) || empty($mp_info['appid']) || empty($mp_info['appsecret']) || !in_array($mp_info['mp_type'], [1, 2])) {
				return false;
			}
			$appid = $mp_info['appid'];
			$appsecret = $mp_info['appsecret'];
			$join_type = $mp_info['join_type'];
			if ($join_type == 2) {		// 授权接入
				return false;
			} else {        // 手动接入
				$Wxapp = new Wxapp([
					'appid' => $appid,
					'appsecret' => $appsecret
				]);
				$res = $Wxapp->getQrcode($path, $type, $options, $filename);
				return $res;
			}
		} catch (\Exception $e) {
			return false;
		}
	}
}

if (!function_exists('text_decode')) {
	function text_decode($str){
		$text = json_encode($str);
		$text = preg_replace_callback('/\\\\\\\\/i',function($str){
			return '\\';
		},$text);
		return json_decode($text);
	}
}

if (!function_exists('text_encode')) {
	function text_encode($str){
		if(!is_string($str))return $str;
		if(!$str || $str=='undefined')return '';
		
		$text = json_encode($str);
		$text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
			return addslashes($str[0]);
		},$text);
		return json_decode($text);
	}
}