<?php 
namespace Mp\Controller;
use Think\Controller;

/**
 * 插件移动端控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MobileBaseController extends Controller {
    /**
     * 初始化
     * @author 艾逗笔<765532665@qq.com>
     */
    public function _initialize() {
        if (!is_wechat_browser() && !get_user_id() && !I('out_trade_no') && $this->wechat_only) {
            $mp_info = get_mp_info();
            if (isset($mp_info['appid'])) {
                redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$mp_info['appid'].'&redirect_uri=&wxref=mp.weixin.qq.com&from=singlemessage&isappinstalled=0&response_type=code&scope=snsapi_base&state=&connect_redirect=1#wechat_redirect');
            } else {
                redirect('https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8dcd98079e13d33f&redirect_uri=&wxref=mp.weixin.qq.com&from=singlemessage&isappinstalled=0&response_type=code&scope=snsapi_base&state=&connect_redirect=1#wechat_redirect');
            }
        }
        if (get_mpid() && !get_openid()) {
            init_fans();
        }
        if (!get_ext_openid()) {
            init_ext_fans();       // 初始化鉴权用户
        }
        global $_G;
        $_G['site_path'] = SITE_PATH . '/';
        $_G['site_url'] = str_replace('index.php', '', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        $_G['addons_path'] = str_replace('./', $_G['site_path'], ADDON_PATH);
        $_G['addons_url'] = $_G['site_url'] . str_replace('./', '', ADDON_PATH);
        $_G['addon'] = get_addon();
        $_G['addon_path'] = $_G['addons_path'] . $_G['addon'] . '/';
        $_G['addon_url'] = $_G['addons_url'] . $_G['addon'] . '/';
        $_G['addon_public_path'] = $_G['addon_path'] . 'View/Public/';
        $_G['addon_public_url'] = $_G['addon_url'] . 'View/Public/';
        $_G['current_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $_G['fans_info'] = get_fans_info();
        $_G['mp_info'] = get_mp_info();
        $_G['openid'] = get_openid();
        $_G['mpid'] = get_mpid();
        // 感谢 @苍竹先生<593485230@qq.com> 提供的处理浏览器openid问题的解决方案
        preg_match('/\/openid[\/|=]([_\-0-9A-Za-z]*+)/', $_G['current_url'], $m);		// 带上openid的参数字符串
        if (isset($m[0]) && !empty($m[0])) {
            get_openid($m[1]);                                              // 设置当前用户标识
        	$redirect_url = str_replace($m[0], '', $_G['current_url']);			// 去除openid的重定向访问链接
        	redirect($redirect_url);										// 重定向
        }
        add_hook('jssdk', 'Mp\Behavior\JssdkBehavior');                     // 注册导入jssdk的钩子
        add_hook('import_js', 'Mp\Behavior\ImportJsBehavior');              // 注册导入js的钩子
        add_hook('import_css', 'Mp\Behavior\ImportCssBehavior');              // 注册导入js的钩子
		S('Api-Token', get_nonce(32), 3600);   // 缓存api请求token
        $this->assign('_G', $_G);
    }

    /**
     * 获取微信支付参数
     * @author 艾逗笔<765532665@qq.com>
     */
    public function json_pay() {
    	$data = I('post.');
    	$mpid = get_mpid();
    	$openid = get_ext_openid();
    	if (empty($data['price']) || empty($data['orderid']) || empty($mpid) || empty($openid)) {
    		exit();
		}
		$data['mpid'] = $mpid;
    	$data['openid'] = $openid;
        $jsApiParameters = get_jsapi_parameters($data);
        $this->ajaxReturn($jsApiParameters);
    }
    
    // 接收异步通知
	public function pay_notify() {
		if (I('out_trade_no') && I('result_code') == 'SUCCESS' && I('return_code') == 'SUCCESS') {
			$payment = I('post.');
			
			if ($info = M('mp_payment')->where(array('orderid'=>$payment['out_trade_no'],'mchid'=>$payment['mch_id']))->find()) {
				/**
				签名算法：
				◆ 参数名ASCII码从小到大排序（字典序）；
				◆ 如果参数的值为空不参与签名；
				◆ 参数名区分大小写；
				◆ 验证调用返回或微信主动通知签名时，传送的sign参数不参与签名，将生成的签名与该sign值作校验。
				◆ 微信接口可能增加字段，验证签名时必须支持增加的扩展字段
				 */
				ksort($payment);
				$sArr = [];
				foreach ($payment as $k => $v) {
					if (!empty($v) && $k != 'sign') {
						if ($k == 'attach') {
							$sArr[] = "$k=".htmlspecialchars_decode($v);
						} else {
							$sArr[] = "$k=$v";
						}
					}
				}
				$stringA = implode('&', $sArr);
				
				$paysignkey = M('mp_setting')->where([
					'mpid' => $info['mpid'],
					'name' => 'paysignkey'
				])->getField('value');
				
				$stringSignTemp = $stringA . "&key=" . $paysignkey;
				$sign = strtoupper(md5($stringSignTemp));
				if ($sign == $payment['sign']) {			// 签名校验通过
					$data['detail'] = json_encode($payment);
					$data['status'] = 1;
					$data['openid'] = $payment['openid'];
					M('mp_payment')->where([
						'orderid' => $payment['out_trade_no'],
						'mchid' => $payment['mch_id']
					])->save($data);
					
					// 将通知转发到插件控制器中进行处理
					if (isset($info['notify']) && !empty($info['notify'])) {
						$notify_url = $info['notify'];
						$payment['mpid'] = $info['mpid'];
						$payment['orderid'] = $payment['out_trade_no'];
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $notify_url);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $payment);
						curl_setopt($ch, CURLOPT_TIMEOUT, 30);
						curl_exec($ch);
						curl_close($ch);
					}
					$return_code = I('return_code');
					$return_msg = I('return_msg');
					return '<xml>
                          <return_code><![CDATA['.$return_code.']]></return_code>
                          <return_msg><![CDATA['.$return_msg.']]></return_msg>
                        </xml>';
				}
			}
		}
	}

    /**
     * 粉丝绑定
     * @author 艾逗笔<765532665@qq.com>
     */
    public function fans_bind() {
        C('TOKEN_ON', false);
        if (IS_POST) {
            $data['nickname'] = I('post.nickname');
            $data['relname'] = I('post.relname');
            $data['sex'] = I('post.sex');
            $data['signature'] = I('post.signature');
            $data['mobile'] = I('post.mobile');
            $data['is_bind'] = 1;
            $res = M('mp_fans')->where(array('openid'=>get_openid()))->save($data);
            if ($res === false) {
                $return['errcode'] = 0;
                $return['errmsg'] = '保存用户信息失败';
                $return['data'] = I('post.');
            } else {
                $return['errcode'] = 1;
                $return['errmsg'] = '保存用户信息成功';
                $return['data'] = I('post.');
                $return['url'] = U('fans_bind');
            }
            $this->ajaxReturn($return);
        } else {
            $fans_info = get_fans_info();
            $this->assign('fans_info', $fans_info);
            parent::display('Fans/bind');
        }
    }

    /**
     * 图文详情
     * @author 艾逗笔<765532665@qq.com>
     */
    public function detail() {
        $detail = M('mp_material')->find(I('id'));
        $mp_info = M('mp')->find($detail['mpid']);
        $this->assign('mp', $mp_info);
        $this->assign('detail', $detail);
        parent::display('Material/detail');
    }

    /**
     * 重写模板显示方法
     * @author 艾逗笔<765532665@qq.com>
     */
    public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        global $_G;
        if (empty($templateFile)) {
            $templateFile = $_G['addon_path'] . 'View/' . CONTROLLER_NAME . '/' . ACTION_NAME . C('TMPL_TEMPLATE_SUFFIX');
        } else {
            $tempArr = explode('/', $templateFile);
            switch (count($tempArr)) {
                case 1:
                    $templateFile = $_G['addon_path'] . 'View/' . CONTROLLER_NAME . '/' . $tempArr[0] . C('TMPL_TEMPLATE_SUFFIX');
                    break;
                case 2:
                    $templateFile = $_G['addon_path'] . 'View/' . CONTROLLER_NAME . '/' . $tempArr[0] . '/' . $tempArr[1] . C('TMPL_TEMPLATE_SUFFIX');
                    break;
                default:
                    break;
            }
        }
        if (!is_file($templateFile)) {
            E('模板不存在:'.$templateFile);
        }
        parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }
}

?>