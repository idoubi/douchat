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
        if (I('out_trade_no')) {
            $payment = I('post.');
            if (!M('mp_payment')->where(array('orderid'=>$payment['out_trade_no']))->find()) {
                $data['mpid'] = $payment['mpid'];
                $data['openid'] = $payment['openid'];
                $data['orderid'] = $payment['out_trade_no'];
                $data['create_time'] = strtotime($payment['time_end']);
                $data['detail'] = json_encode($payment);
                M('mp_payment')->add($data);
                $return_code = I('return_code');
                $return_msg = I('return_msg');
                return '<xml>
                          <return_code><![CDATA['.$return_code.']]></return_code>
                          <return_msg><![CDATA['.$return_msg.']]></return_msg>
                        </xml>';
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
        $this->assign('_G', $_G);
    }

    /**
     * 获取微信支付参数
     * @author 艾逗笔<765532665@qq.com>
     */
    public function json_pay() {
        $jsApiParameters = get_jsapi_parameters(I('post.'));
        $this->ajaxReturn($jsApiParameters);
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